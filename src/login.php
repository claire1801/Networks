<?php

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch($requestMethod) {

    case 'POST':
        $username = '';
        $password = '';

        if (isset($_POST['username'])) {$username = $_POST['username'];}
        if (isset($_POST['password'])) {$password = $_POST['password'];}

        //TOdo check the username , password from database

        if (($username == 'scrum_master') && ($password == 'test')) {

            require_once('jwt.php');
            $userId = 'sm_1';

            $serverKey = 'sfasf987sdafhsadf90k98';

            $payloadArray = array();
            $payloadArray['userId'] = $userId;
            if (isset($nbf)) {$payloadArray['nbf'] = $nbf;}
            if (isset($exp)) {$payloadArray['exp'] = $exp;}
            $token = JWT::encode($payloadArray, $serverKey);

            // return to caller
            $returnArray = array('token' => $token);
            $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
            echo $jsonEncodedReturnArray;

        } else {
            $returnArray = array('error' => 'Invalid user ID or password.');
            $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
            echo $jsonEncodedReturnArray;
        }

        break;

    case 'GET':
        $token = null;
        if (isset($_GET['token'])) {$token = $_GET['token'];}
        if (!is_null($token)) {
            require_once('jwt.php');
            // Get our server-side secret key from a secure location.
            $serverKey = 'sfasf987sdafhsadf90k98';

            try {
                $payload = JWT::decode($token, $serverKey, array('HS256'));
                $returnArray = array('userId' => $payload->userId);
                if (isset($payload->exp)) {
                    $returnArray['exp'] = date(DateTime::ISO8601, $payload->exp);;
                }
            }
            catch(Exception $e) {
                $returnArray = array('error' => $e->getMessage());
            }
        }
        else {
            $returnArray = array('error' => 'You are not logged in with a valid token.');
        }
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
        echo $jsonEncodedReturnArray;
        break;

    default:
        $returnArray = array('error' => 'You have requested an invalid method.');
        $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
        echo $jsonEncodedReturnArray;
}