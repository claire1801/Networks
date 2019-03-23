<?php
abstract class DB_Utils
{
    private static $_connection = null;

    public static function getConnection()
    {
        try {
            if (!self::$_connection) {
                self::$_connection = new PDO(Settings::$DATABASE['dns'],
                Settings::$DATABASE['user'],
                Settings::$DATABASE['password']);
              self::$_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                self::$_connection->query("set names 'UTF8'");
            }
        } catch (Exception $e){
            Utils::addDebugLog('DB Error :'.$e->getMessage());
            self::$_connection = null;
            throw new Exception('Operation failed');
        }
        return self::$_connection;
    }
}
?>
