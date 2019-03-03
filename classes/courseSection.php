<?php

require_once __DIR__ . '/../auth/mysql_config.php';
require_once 'courseSectionContent.php';

class CourseSection {

    private $id;
    private $courseId;
    private $name;

    public function __construct($id, $courseId, $name) {
        $this->id = $id;
        $this->courseId = $courseId;
        $this->name = $name;
    }

    public function GetId() {
        return $this->id;
    }

    public function GetCourseId() {
        return $this->courseId;
    }

    public function GetName() {
        return $this->name;
    }

    public function GetContents() {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT * FROM CourseSectionContent WHERE sectionId = :id";
        $statement = $connection->prepare($sql);
        $statement->bindValue("id", $this->id, PDO::PARAM_STR);
        $statement->execute();

        $contents = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $name = $row['name'];
            $type = $row['type'];
            $data = $row['data'];

            $content = new CourseSectionContent($id, $this->id, $name, $type, $data);
            array_push($contents, $content);
        }
        return $contents;
    }

    public static function SectionWithIdExists($sectionId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM CourseSections WHERE id = :sectionid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("sectionid", $sectionId);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }

    public static function DeleteSectionWithId($sectionId) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM CourseSections WHERE id = :sectionid;";
        $statement = $connection->prepare($sql);
        $statement->bindValue("sectionid", $sectionId, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function AddSectionToCourse($courseId, $sectionTitle) {
        $connection = MysqlConfig::Connect();
        $sql = "INSERT INTO CourseSections (courseId, name) VALUES (:courseid, :name)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("courseid", $courseId, PDO::PARAM_STR);
        $statement->bindValue("name", $sectionTitle, PDO::PARAM_STR);
        $statement->execute();
    }
}
