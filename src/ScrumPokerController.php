<?php
require_once 'LibScrumPoker.php';

class ScrumPokerController {

  private $_user;
  private $_userId;
  private $token = NULL;

  public function __construct()
    {
        $this->_user = new DB_Users();
        $this->_proj = new DB_Project();
        $this->_ticket = new DB_Ticket();
        $this->_estimation = new DB_Estimation();

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
        $_SESSION['scrum_master'] = $ret['scrum_master'];
        $_SESSION['user_id'] = $ret['user_id'];
        $_SESSION['full_name'] = $ret['full_name'];

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
        $_SESSION['scrum_master'] = $ret['scrum_master'];
        $_SESSION['user_id'] = $ret['user_id'];
    }
    return $result;
  }

  public function createTicket($ticketName, $ticketDesc, $ticketLink){
      $response = array();
      $response['status'] = 2;
      if($this->isScrumMaster()  && isset($_SESSION['project_id'])) {
        $ticketId = $this->_ticket->createTicket(
                $ticketName,
                $ticketDesc,
                $ticketLink,
                $_SESSION['project_id'],
                $_SESSION['user_id']);
      } else {
          $ticketId = 0;
      }
      if($ticketId) {
        $response['status'] = 1;
        $bc = array();
        $bc['ticket_id'] = $ticketId;
        $bc['ticket_name'] = $ticketName;
        $bc['ticket_desc'] = $ticketDesc;
        $bc['ticket_url'] = $ticketLink;
        $bc['project_id'] = $_SESSION['project_id'];
        $bc['action'] = 'new_ticket_created';
        $response['broadcast'] =$bc;
      }
      return $response;
  }

  public function isScrumMaster() {
    if ($_SESSION['scrum_master'] == 1) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function checkUserProject($projId) {
      $res = $this->_proj->checkProjectById($projId);
      $response = array();
      if(count($res) >0) {
          $response['project_id']=$res[0]['project_id'];
          $response['project_name'] = $res[0]['name'];
          $res1 = $this->_proj->checkProjUser($projId, $this->userId);
          if(count($res1) == 0) {
              $res2 = $this->_proj->createProjUser($projId, $this->userId);
              if($res2) {
                 $_SESSION['project_id'] = $projId;
                 $_SESSION['project_name'] = $res[0]['name'];
                 return $response;
              }
          } else {
            $_SESSION['project_id'] = $projId;
            $_SESSION['project_name'] = $res[0]['name'];
             return $response;
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
           $_SESSION['project_id'] = $res;
           $_SESSION['project_name'] = $projName;
       }
       return $response;
  }

  public function getProjectDetails($projId) {

    //return full details ... users , projects, estimations as json
  }


public function fetchTicket($projectId, $ticketId) {
  //return ticket details with estimation
}

public function createEstimation( $ticketId, $estimation, $projId) {
   if(!isset($_SESSION['project_id'])) {
     $this->checkUserProject($projId);
   }
    $check = $this->_estimation->checkEstimation($ticketId, $_SESSION['project_id'], $_SESSION['user_id']);
    $result =array();
    $broadcast = array();
    if(count($check)>0) {
      $this->_estimation->updateEstimation($ticketId, $_SESSION['project_id'], $_SESSION['user_id'], $estimation);
    } else {
      $this->_estimation->createEstimation($ticketId, $_SESSION['project_id'], $_SESSION['user_id'], $estimation);
    }
    $result['status'] = 1;
    $result['desc'] = 'updated';
    $broadcast['ticket_id'] = $ticketId;
    $broadcast['user_id'] = $_SESSION['user_id'];
    $broadcast['estimation'] =$estimation;
    $broadcast['user_name'] = $_SESSION['full_name'];
    $broadcast['action'] = 'update_estimation';
    $result['broadcast'] = $broadcast;
    return $result;
}

public function finalEstimation( $ticketId, $estimation, $projId) {
   if(!isset($_SESSION['project_id'])) {
     $this->checkUserProject($projId);
   }
    $this->_ticket->updateTicket($ticketId, $_SESSION['project_id'], $estimation);

    $allEstimations = $this->getAllEstimatedTickets($_SESSION['project_id']);
    $result =array();
    $broadcast = array();
    $result['status'] = 1;
    $result['desc'] = 'updated';
    $broadcast['action'] = 'estimation_done';
    $broadcast['estimations'] = $allEstimations;
    $result['broadcast'] =  $broadcast;
    return $result;
}

public function getAllEstimatedTickets($projId) {
    $allTickets = $this->_ticket->getAllEstimatedTickets($projId);
    $results = array();
    //$ticketName ="";
    $users = [];
    foreach ($allTickets as $key => $value) {
      //if($ticketName != $value['name'] && count($users)) {
      //  $results[$ticketName]['users'] = $users;
      //  $users = [];
      //}
      //$ticketName = $value['name'];
      $results[$key] = ['name'=> $value['name'],
                              'final_estimation'=>$value['final_estimation']
                            ];
      //$users[$value['user_id']] = ['user_name'=>$value['full_name'] , 'estimation' =>$value['estimation']];
    }
    //$results[$ticketName]['users'] = $users;
    return $results;
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
  $payloadArray = array();
  $payloadArray['userId'] = $res['user_id'];
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
