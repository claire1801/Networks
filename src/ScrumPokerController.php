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
        //$this->
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

  public function createProject() {
     //if already exist return error
     //only SM
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
          $payload = JWT::decode($token, Settings::SECRET_TOKEN_KEY, array('HS256'));
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



}
?>
