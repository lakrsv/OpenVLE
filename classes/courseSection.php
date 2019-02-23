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

}
