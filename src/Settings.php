<?php
abstract class Settings
{
    const SRC_DIR = '';
    const SECRET_TOKEN_KEY = 'sfasf987sdafhsadf90k98';
    const PROJECT_URL = "http://ec2-34-214-229-187.us-west-2.compute.amazonaws.com/scrumpoker?";

    const SOCKET_ADDRESS = "localhost";
    const SOCKET_PORT = 9000;
    const SOCKET_SECURE_KEY = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
    const SOCKET_LOCATION = "Scrum2/socket/server.php";
    const SOCKET_URL = "ws://ec2-34-214-229-187.us-west-2.compute.amazonaws.com:9000/scrumpoker/socket/server.php";

    //const SOCKET_SECURE_KEY = '345DFG45-G244-DFG4-DR45-DFG45DFG25D';


    public static $DATABASE =
      array(
        'dns'      => 'mysql:host=localhost;
                        port=3306;
                        dbname=scrum_poker;',
        'user'     => 'scrumpoker',
        'password' => 'Test@123456'
      );


    const DEBUG_LOG_FILE
        = '/var/www/html/scrumpoker/log/';
}
?>
