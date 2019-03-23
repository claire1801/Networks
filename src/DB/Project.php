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
      //INSERT INTO `project`(`name`, `start_time`) VALUES ('test',now())
    }

    public function endProject($projectId) {
      //UPDATE `project` SET `end_time` = now() WHERE `project_id` = 1
    }


}
?>
