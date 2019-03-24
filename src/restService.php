<?php
session_start();
require_once 'LibScrumPoker.php';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : null;
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : null;
$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;
$projName = isset($_REQUEST['proj_name']) ? $_REQUEST['proj_name'] : null;
$projId = isset($_REQUEST['proj_id']) ? $_REQUEST['proj_id'] : null;

try {
    $response = array();

//check User logged in or not
$scrumCont = new ScrumPokerController();
$scrumCont->setToken($token);
if($action!= 'login' && $action!= 'logout' && !$scrumCont->verifyUser()) {
  $response['status'] = 3;
  $response['desc']   = 'Invalid User. Login again';
  echo json_encode($response, JSON_PRETTY_PRINT);
  die;
}

switch ($action) {

case 'projUserCheck':
    $response['project_id'] = 0;
    //print_r ($_SESSION['project_id']);die;
    if(!isset($_SESSION['project_id']) && $projId) {
      $response['status'] = 1;
      $response['project_id'] = $scrumCont->checkUserProject($projId);
    } else if(isset($_SESSION['project_id']) && $_SESSION['project_id'] == $projId) {
      $response['status'] = 1;
      $response['project_id'] = $_SESSION['project_id'];
    } else {
      $response['status'] = 2;
      $response['project_id'] = 0;
    }
    break;

case 'login' :
    $result = $scrumCont->userAuth($username, $password);
    if (!$result) {
        $response['status'] = 2;
        $response['desc']   = 'Wrong username / password';
    } else {
        $response['status'] = 1;
        $response['user_details']  = $result;
        $response['desc']   = 'Login Success';
    }
    break;
case 'create_proj' :
    $response = $scrumCont->createProject($projName);
    break;
case 'logout' :
    Utils::addDebugLog('logout');
    $scrumCont->logout();
    $response['status'] = 1;
    $response['desc']   = 'Log out success';
    break;
default:
        $response['status'] = 2;
        $response['desc']   = 'Action not exist';
}

  echo json_encode($response, JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    if ($e->getMessage()) {
        $response['status'] = 2;
        $response['desc']   = $e->getMessage();
    } else {
        $response['status'] = 2;
        $response['desc']   = 'Request Failed';
    }
    echo json_encode($response, JSON_PRETTY_PRINT);
}
?>
