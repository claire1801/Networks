<?php
class DB_Users
{
    private $_conn;

    public function __construct()
    {
        $this->_conn = DB_Utils::getConnection();
    }

    public function getUser($userid)
    {
      try {
          $sth = $this->_conn->prepare('SELECT * FROM users WHERE user_id = ? limit 1');
          $sth->bindParam(1, $userid, PDO::PARAM_INT);
          $sth->execute();
          return $sth->fetchAll();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed');
      }
    }

    public function checkUser($username, $password)
    {
        try {
            $sth = $this->_conn->prepare('SELECT * FROM users WHERE user_name = ? AND password = ? limit 1');
            $sth->bindParam(1, $username, PDO::PARAM_STR);
            $sth->bindParam(2, $password, PDO::PARAM_STR);
            $sth->execute();
            return $sth->fetchAll();
        } catch (Exception $e) {
            Utils::addDebugLog('DB Error :'.$e->getMessage());
            throw new Exception('Operation failed');
        }
    }
}
?>
