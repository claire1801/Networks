<?php


class DB_Estimation
{
    private $_conn;

    public function __construct()
    {
        $this->_conn        = DB_Utils::getConnection();
    }

    public function createEstimation($ticketId, $pId, $uId, $estimation) {
      try {
          $sth = $this->_conn->prepare('INSERT INTO estimation(project_id,ticket_id,user_id, estimation) VALUES (?,?,?,?)');
          $sth->bindParam(1, $pId, PDO::PARAM_INT);
          $sth->bindParam(2, $ticketId, PDO::PARAM_INT);
          $sth->bindParam(3, $uId, PDO::PARAM_INT);
          $sth->bindParam(4, $estimation, PDO::PARAM_INT);
          $sth->execute();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed3'.$e->getMessage());
      }
    }

    public function updateEstimation($ticketId, $pId, $uId, $estimation) {
      try {
          $sth = $this->_conn->prepare('UPDATE estimation SET estimation=? where project_id=? AND ticket_id = ? AND user_id =?');
          $sth->bindParam(1, $estimation, PDO::PARAM_INT);
          $sth->bindParam(2, $pId, PDO::PARAM_INT);
          $sth->bindParam(3, $ticketId, PDO::PARAM_INT);
          $sth->bindParam(4, $uId, PDO::PARAM_INT);
          return $sth->execute();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed2');
      }
    }

    public function checkEstimation($ticketId, $pId, $uId) {
      try {
        $sth = $this->_conn->prepare('SELECT * FROM estimation WHERE project_id = ? AND ticket_id = ? AND user_id =?');
        $sth->bindParam(1, $pId, PDO::PARAM_INT);
        $sth->bindParam(2, $ticketId, PDO::PARAM_INT);
        $sth->bindParam(3, $uId, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed1');
      }
    }




}
?>
