<?php
abstract class Settings
{
    const SRC_DIR = '';
    const SECRET_TOKEN_KEY = 'sfasf987sdafhsadf90k98';

    public static $DATABASE =
      array(
        'dns'      => 'mysql:host=localhost;
                        port=3306;
                        dbname=scrum_poker;',
        'user'     => 'scrumuser',
        'password' => 'Test@123456'
      );


    const DEBUG_LOG_FILE
        = '/var/www/html/Scrum2/log/';
}
?>
