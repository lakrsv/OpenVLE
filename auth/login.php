<?php

require_once 'mysql_config.php';
require_once __DIR__ . '/../classes/passwordConstants.php';

class User {

    private $id;
    private $email;
    private $name;
    private $password;

    public function __construct($data = array()) {
        if (isset($data['email'], $data['password'])) {
            $this->email = stripslashes(strip_tags($data['email']));
            $this->password = stripslashes(strip_tags($data['password']));
        }
    }

    public function GetId() {
        return $this->id;
    }

    public function GetEmail() {
        return $this->email;
    }

    public function GetName() {
        return $this->name;
    }

    public function GetPassword() {
        return $this->password;
    }

    public function Login() {
        $success = false;
        try {
            $connection = MysqlConfig::Connect();
            $statement = $this->CreateLoginStatement($connection);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);

            // TODO - Cleanup
            if ($result) {
                if (password_verify($this->password, $result['password'])) {
                    $success = true;
                    session_start();
                    session_regenerate_id();

                    $_SESSION['username'] = $this->name;
                    $_SESSION['useremail'] = $this->email;
                    $_SESSION['userid'] = $result['id'];

                    session_write_close();
                    echo 'Success';
                    exit();
                }
            }
            return $success;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return $success;
        }
    }

    private function CreateLoginStatement($connection) {
        $sql = "SELECT * FROM Users WHERE email = :email LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("email", $this->email, PDO::PARAM_STR);
        return $statement;
    }

    public static function GetAll() {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT * FROM Users";
        $statement = $connection->prepare($sql);
        $statement->execute();

        $users = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $newUser = new User($row);
            $newUser->id = $row['id'];
            $newUser->name = $row['name'];
            array_push($users, $newUser);
        }

        return $users;
    }

    public static function UserWithIdExists($userId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM Users WHERE id = :userid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }

    public static function UserWithEmailExists($email) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM Users WHERE email = :email LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("email", $email);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }

    public static function AddUserWithEmailAndNameAndPassword($email, $name, $password) {
        $connection = MysqlConfig::Connect();
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO Users (email, name, password) VALUES (:email, :name, :password)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("email", $email);
        $statement->bindValue("name", $name);
        $statement->bindValue("password", $password);
        $statement->execute();
    }

    public static function DeleteUserWithId($userId) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM Users WHERE id = :userid";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function ChangeUserRole($userId, $roleId) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM UserRoles WHERE userId = :userid;INSERT INTO UserRoles (userId, roleId) VALUES (:userid, :roleid)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->bindValue("roleid", $roleId, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function UnassignUserCourse($userId, $courseId) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM CourseUsers WHERE userId = :userid AND courseId = :courseid";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->bindValue("courseid", $courseId, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function AssignUserCourse($userId, $courseId) {
        $connection = MysqlConfig::Connect();
        $sql = "INSERT INTO CourseUsers (userId, courseId) VALUES (:userid, :courseid)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->bindValue("courseid", $courseId, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function HasCourse($userId, $courseId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM CourseUsers WHERE userId = :userid AND courseId = :courseid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->bindValue("courseid", $courseId, PDO::PARAM_STR);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }

    public static function ChangeUserName($userId, $newName) {
        $connection = MysqlConfig::Connect();
        $sql = "UPDATE Users SET name=:name WHERE id=:userid";
        $statement = $connection->prepare($sql);
        $statement->bindValue("name", $newName, PDO::PARAM_STR);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function ChangePasswordForUser($userId, $newPassword) {
        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $connection = MysqlConfig::Connect();
        $sql = "UPDATE Users SET password=:password WHERE id=:userid";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->bindValue("password", $newPassword, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function ChangeUserProfilePicture($userId, $imageData) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM UserProfilePicture WHERE userId=:userid; INSERT INTO UserProfilePicture (userId, img) VALUES (:userid, :img)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->bindValue("img", $imageData, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function GetUserProfilePicture($userId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT img FROM UserProfilePicture WHERE userId=:userid LIMIT 1;";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public static function GetUserIdFromEmail($email) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM Users WHERE email = :email LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("email", $email, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public static function GetEmailFromId($userId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT email FROM Users WHERE id = :id LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("id", $userId, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public static function GetUsernameFromId($userId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT name FROM Users WHERE id = :id LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("id", $userId, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

}

if (isset($_POST['email'], $_POST['password'])) {
    $user = new User(["email" => $_POST['email'], "password" => $_POST['password']]);
    $user->Login();
}