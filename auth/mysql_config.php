<?php

class MysqlConfig {

    private $db_url = "lsvenoey01.lampt.eeecs.qub.ac.uk";
    private $db_name = "lsvenoey01";
    private $db_username = "lsvenoey01";
    private $db_password = "MJHrXsxjsTkCTY91";
    
    public function Connect() {
        $connection = new PDO("mysql:host=".$this->db_url.";dbname=".$this->db_name, $this->db_username, $this->db_password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

}
