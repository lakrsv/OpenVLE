<?php

require_once 'config/mysql_config.php';

class User {

    public $username = null;
    public $password = null;

    public function __construct($data = array()) {
        if (isset($data['username'], $data['password'])) {
            $this->username = stripslashes(strip_tags($data['username']));
            $this->password = stripslashes(strip_tags($data['password']));
        }
    }

    public function Login() {
        $success = false;
        try {
            $connection = MysqlConfig::Connect();
            $statement = $this->CreateLoginStatement($connection);
            $statement->execute();

            if ($statement->fetchColumn()) {
                $success = true;
                session_start();
                session_regenerate_id();
                $_SESSION['user'] = $this->username;
                session_write_close();
                echo 'Success';
                exit();
            }
            return $success;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return $success;
        }
    }

    private function CreateLoginStatement($connection) {
        $sql = "SELECT * FROM Users WHERE username = :username AND password = :password LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("username", $this->username, PDO::PARAM_STR);
        $statement->bindValue("password", $this->password, PDO::PARAM_STR);
        return $statement;
    }

}

$user = new User(array("username" => $_POST['username'], "password" => $_POST['password']));
$user->Login();
