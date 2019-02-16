<?php

require_once __DIR__ . '/../auth/mysql_config.php';

class Course {

    private $id;
    private $name;
    private $description;

    private function __construct($id, $name, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    public function GetId() {
        return $this->id;
    }

    public function GetName() {
        return $this->name;
    }

    public function GetDescription() {
        return $this->description;
    }

    public static function GetAll() {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT * FROM Courses";
        $statement = $connection->prepare($sql);
        $statement->execute();

        $courses = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $name = $row['name'];
            $description = $row['description'];

            $newCourse = new Course($id, $name, $description);
            array_push($courses, $newCourse);
        }
        return $courses;
    }

    public static function CourseWithNameExists($name) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM Courses WHERE name = :name LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("name", $name);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }
    
    public static function CourseWithIdExists($courseId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM Courses WHERE id = :courseid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("courseid", $courseId);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }
    
     public static function DeleteCourseWithId($courseId) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM Courses WHERE id = :courseid;";
        $statement = $connection->prepare($sql);
        $statement->bindValue("courseid", $courseId, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function AddCourseWithNameAndDescription($name, $description) {
        $connection = MysqlConfig::Connect();
        $sql = "INSERT INTO Courses (name, description) VALUES (:name, :description)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("name", $name);
        $statement->bindValue("description", $description);
        $statement->execute();
    }

    public static function GetCourseWithName($name) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT * FROM Courses WHERE name = :name LIMIT 1;";
        $statement = $connection->prepare($sql);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $description = $row['description'];

        $course = new Course($id, $name, $description);

        return $course;
    }
}
