<?php


class DB_Estimation
{
    private $_conn;

    public function __construct()
    {
        $this->_conn        = DB_Utils::getConnection();
    }

}
?>
