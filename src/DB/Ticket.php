<?php

class DB_Ticket
{
    private $_conn;

    public function __construct()
    {
        $this->_conn = DB_Utils::getConnection();
    }

    public function createTicket($ticketName, $ticketDesc, $ticketLink, $pId, $uId) {
      //
      try {
          $sth = $this->_conn->prepare('INSERT INTO ticket(name, description, url, project_id, creator_id) VALUES (?,?,?,?,?)');
          $sth->bindParam(1, $ticketName, PDO::PARAM_STR);
          $sth->bindParam(2, $ticketDesc, PDO::PARAM_STR);
          $sth->bindParam(3, $ticketLink, PDO::PARAM_STR);
          $sth->bindParam(4, $pId, PDO::PARAM_INT);
          $sth->bindParam(5, $uId, PDO::PARAM_INT);
          $sth->execute();
          return $this->_conn->lastInsertId();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed');
      }
    }

    public function updateTicket($ticketId,$pId, $est) {
      try {
          $sth = $this->_conn->prepare('UPDATE ticket SET final_estimation=?, last_update_time=now() where project_id=? AND ticket_id = ?');
          $sth->bindParam(1, $est, PDO::PARAM_INT);
          $sth->bindParam(2, $pId, PDO::PARAM_INT);
          $sth->bindParam(3, $ticketId, PDO::PARAM_INT);
          $sth->execute();
      } catch (Exception $e) {
        echo $e->getMessage();die;
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed');
      }
    }

    public function getAllEstimatedTickets($projId) {
      try {
          $sth = $this->_conn->prepare('SELECT t.name, e.estimation,u.full_name,u.user_id, t.final_estimation '
          .'FROM ticket t, estimation e, users u WHERE t.project_id = ? AND '
          .'t.final_estimation IS NOT NULL AND t.project_id=e.project_id AND '
          .'u.user_id = e.user_id AND t.ticket_id = e.ticket_id order by t.last_update_time DESC');
          $sth->bindParam(1, $projId, PDO::PARAM_INT);
          $sth->execute();
          return $sth->fetchAll();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed');
      }
    }
}
?>
