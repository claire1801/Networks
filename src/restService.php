<?php
require_once 'LibScrumPoker.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : null;
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : null;
$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;

try {
    $response = array();

//check User logged in or not
$scrumCont = new ScrumPokerController();
$scrumCont->setToken($token);


switch ($action) {
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
case 'createProject' :
    $result = $scrumCont->verifyUser($token);
    break;
case 'test2' :
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
