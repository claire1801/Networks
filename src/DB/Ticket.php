<?php

class DB_Ticket
{
    private $_conn;

    public function __construct()
    {
        $this->_conn = DB_Utils::getConnection();
    }

    public function createTicket($pId, $name, $desc, $url)
    {
        try {
            if (!$userid) {
                throw new Exception('User id is not valid'.$userid);
            }

          //  INSERT INTO `ticket`(`name`, `description`, `url`, `estimated`, `project_id`, `creator_id`)
        //    		VALUES ('issue23', 'test description', 'http://test.com/issue23', 0, 1, 3);
        //    		            $ret = $this->_conn->query($query);
            return $ret;
        } catch (Exception $e) {
            Utils::addDebugLog('DB Error :'.$e->getMessage());
            throw new Exception('Operation failed');
        }
    }
}
?>
