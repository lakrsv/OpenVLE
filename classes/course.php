<?php

require_once __DIR__ . '/../auth/mysql_config.php';
require_once 'courseSection.php';

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
    
    public function GetSections(){
         $connection = MysqlConfig::Connect();
        $sql = "SELECT * FROM CourseSections WHERE courseId = :id";
        $statement = $connection->prepare($sql);
        $statement->bindValue("id", $this->id);
        $statement->execute();

        $sections = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $courseId = $row['courseId'];
            $name = $row['name'];

            $section = new CourseSection($id, $courseId, $name);
            array_push($sections, $section);
        }
        return $sections;
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
        $statement->bindValue("name", $name, PDO::PARAM_STR);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $description = $row['description'];

        $course = new Course($id, $name, $description);

        return $course;
    }
    
      public static function GetCourseWithId($id) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT * FROM Courses WHERE id = :id LIMIT 1;";
        $statement = $connection->prepare($sql);
        $statement->bindValue("id", $id, PDO::PARAM_STR);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        $name = $row['name'];
        $description = $row['description'];

        $course = new Course($id, $name, $description);

        return $course;
    }
    
    public static function GetCoursesForUser($userId){
        $connection = MysqlConfig::Connect();
        $sql = "SELECT c.* FROM CourseUsers cu INNER JOIN Courses c ON cu.courseId = c.id WHERE cu.userId = :userid";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->execute();
        
        $coursesForUser = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($coursesForUser, new Course($row['id'], $row['name'], $row['description']));
        }

        return $coursesForUser;
    }
}
