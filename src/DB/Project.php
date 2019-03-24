<?php
class DB_Project
{
    private $_conn;

    public function __construct()
    {
        $this->_conn = DB_Utils::getConnection();
    }

    public function getProjectDetails($projectId)
    {
        try {
            if (!$userid) {
                throw new Exception('User id is not valid'.$userid);
            }
            //$query  = 'select * from users where user_id= '.$userid;
            //$ret = $this->_conn->query($query);
            //return $ret;
        } catch (Exception $e) {
            Utils::addDebugLog('DB Error :'.$e->getMessage());
            throw new Exception('Operation failed');
        }
    }

    public function createProject($projectName) {
      //
      try {
          $sth = $this->_conn->prepare('INSERT INTO project(name, start_time) VALUES (?,now())');
          $sth->bindParam(1, $projectName, PDO::PARAM_STR);
          $sth->execute();
          return $this->_conn->lastInsertId();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed');
      }
    }

    public function checkProject($projectName) {
      try {
          $sth = $this->_conn->prepare('SELECT * FROM project WHERE name = ?');
          $sth->bindParam(1, $projectName, PDO::PARAM_STR);
          $sth->execute();
          return $sth->fetchAll();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed');
      }
    }

    public function checkProjectById($projectId) {
      try {
          $sth = $this->_conn->prepare('SELECT * FROM project WHERE project_id = ?');
          $sth->bindParam(1, $projectId, PDO::PARAM_INT);
          $sth->execute();
          return $sth->fetchAll();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed');
      }
    }

    public function checkProjUser($projId, $userId) {
      try {
          $sth = $this->_conn->prepare('SELECT * FROM project_to_user pu, project p WHERE pu.project_id = ? AND pu.user_id=? AND p.project_id = ?' );
          $sth->bindParam(1, $projId, PDO::PARAM_INT);
          $sth->bindParam(2, $userId, PDO::PARAM_INT);
          $sth->bindParam(3, $projId, PDO::PARAM_INT);
          $sth->execute();
          return $sth->fetchAll();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed');
      }
    }

    public function createProjUser($projId, $userId) {
      try {
          $sth = $this->_conn->prepare('INSERT INTO project_to_user(project_id, user_id) VALUES (?,?)');
          $sth->bindParam(1, $projId, PDO::PARAM_INT);
          $sth->bindParam(2, $userId, PDO::PARAM_INT);
          return $sth->execute();
      } catch (Exception $e) {
          Utils::addDebugLog('DB Error :'.$e->getMessage());
          throw new Exception('Operation failed');
      }
    }





    public function endProject($projectId) {
      //UPDATE `project` SET `end_time` = now() WHERE `project_id` = 1
    }


}
?>
