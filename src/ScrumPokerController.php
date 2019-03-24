<?php
require_once 'LibScrumPoker.php';

class ScrumPokerController {

  private $_user;
  private $scrumMaster = 0;
  private $userId;
  private $token = NULL;

  public function __construct()
    {
        $this->_user = new DB_Users();
        $this->_proj = new DB_Project();
    }


  public function getUser($userId)
  {
    if (is_null($userId)) {
        throw new Exception('User name is not valid'.$username);
    }
    $ret1 = $this->_user->getUser($userId);
    $result = array();
    if(isset($ret1[0])) {
        $ret = $ret1[0];
        $result['user_id'] = $ret['user_id'];
        $result['user_name'] = $ret['user_name'];
        $result['full_name'] = $ret['full_name'];
        $result['email'] = $ret['email'];
        $result['scrum_master'] = $ret['scrum_master'];
    }
    return $result;
  }

  public function checkUser($username, $password) {
    if (is_null($username)) {
        throw new Exception('User name is not valid'.$username);
    }
    if (is_null($password)) {
        throw new Exception('Password is not valid'.$password);
    }
    $ret1 = $this->_user->checkUser($username, $password);
    $result = array();
    if(isset($ret1[0])) {
        $ret = $ret1[0];
        $result['user_id'] = $ret['user_id'];
        $result['user_name'] = $ret['user_name'];
        $result['full_name'] = $ret['full_name'];
        $result['email'] = $ret['email'];
        $result['scrum_master'] = $ret['scrum_master'];
    }
    return $result;
  }

  public function checkScrumMaster($userId)
  {

  }

  public function checkUserProject($projId) {
      $res = $this->_proj->checkProjectById($projId);
      if(count($res) >0) {
          $res1 = $this->_proj->checkProjUser($projId, $this->userId);
          if(count($res1) == 0) {
              $res2 = $this->_proj->createProjUser($projId, $this->userId);
              if($res2) {
                 $_SESSION['project_id'] = $projId;
                 return $projId;
              }
          } else {
            $_SESSION['project_id'] = $projId;
             return $projId;
          }
      }
      return 0;
  }

  public function createProject($projName) {
      $response = array();

      if (is_null($projName)) {
          throw new Exception('Project name is not valid'.$projName);
      }
      $res = $this->_proj->checkProject($projName);
      if(count($res) > 0){
        $response['status'] = 4;
        $response['desc']   = 'Project already exist';
        return $response;
      }
      $res = $this->_proj->createProject($projName);
       if (!$res) {
           $response['status'] = 2;
           $response['desc']   = 'Project creation failed';
       } else {
           $response['status'] = 1;
           $response['project_id']  = $res;
           $response['project_name']  = $projName;
           $response['desc']   = 'New project created';
       }
       return $response;
  }

  public function getProjectDetails($projId) {

    //return full details ... users , projects, estimations as json
  }

 public function saveTicket($projId, $ticketName, $ticketDesc, $url) {
   //only SM

 }

public function fetchTicket($projectId, $ticketId) {
  //return ticket details with estimation
}

public function updateTicket($ticketId, $finalEstimation) {
    //only SM
    //update final estimation
}

public function createEstimation($projectId, $ticketId, $userId, $estimation) {


}

public function userAuth($username, $password) {
  $res = $this->checkUser($username, $password);
  if (isset($res['user_id'])) {
      $res['token'] = $this->_getToken($res);
      return $res;
  } else {
      return FALSE;
  }
}

private function _getToken($res) {
  require_once('jwt.php');
  $userId = $res['user_id'];
  $payloadArray = array();
  $payloadArray['userId'] = $userId;
  $payloadArray['admin'] = $res['scrum_master'];
  if (isset($nbf)) {$payloadArray['nbf'] = $nbf;}
  if (isset($exp)) {$payloadArray['exp'] = $exp;}
  $token = JWT::encode($payloadArray, Settings::SECRET_TOKEN_KEY);
  return $token;
}

public function verifyUser() {
  if (!is_null($this->token)) {
      require_once('jwt.php');
      try {
          $payload = JWT::decode($this->token, Settings::SECRET_TOKEN_KEY, array('HS256'));
          $returnArray = array('userId' => $payload->userId);
          $res = $this->getUser($payload->userId);
          if(isset($res['user_id'])) {
             $this->userId = $res['user_id'];
             $this->scrumMaster = $res['scrum_master'];
             return TRUE;
          } else {
            return FALSE;
          }
      } catch(Exception $e) {
          return FALSE;
      }
   }
}

public function setToken($token) {
    $this->token = $token;
}

public function logout() {
  Utils::addDebugLog('logout');

  $_SESSION = array();
  if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
      );
  }
  session_destroy();
}


}
?>
