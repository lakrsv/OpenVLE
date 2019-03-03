<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of courseSectionContent
 *
 * @author lakrs
 */
class CourseSectionContent {

    const Text = 1;
    const PDF = 2;
    const Quiz = 3;

    private $id;
    private $sectionId;
    private $name;
    private $type;
    private $data;

    public function GetId() {
        return $this->id;
    }

    public function GetSectionId() {
        return $this->sectionId;
    }

    public function GetName() {
        return $this->name;
    }

    public function GetType() {
        return $this->type;
    }

    public function GetData() {
        switch ($this->type) {
            case CourseSectionContent::Text:
                return $this->data;
            case CourseSectionContent::PDF;
                break;
            case CourseSectionContent::Quiz;
                break;
        }
        return $this->data;
    }

    public function __construct($id, $sectionId, $name, $type, $data) {
        $this->id = $id;
        $this->sectionId = $sectionId;
        $this->name = $name;
        $this->type = $type;
        $this->data = $data;
    }

    public static function ContentWithIdExists($contentId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM CourseSectionContent WHERE id = :contentid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("contentid", $contentId);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }

    public static function DeleteContentWithId($contentId) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM CourseSectionContent WHERE id = :contentid;";
        $statement = $connection->prepare($sql);
        $statement->bindValue("contentid", $contentId, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function AddContentToSection($sectionId, $contentTitle, $content) {
        $connection = MysqlConfig::Connect();
        // 1 as only one type is currently supported.
        $sql = "INSERT INTO CourseSectionContent (sectionId, name, type, data) VALUES (:sectionid, :name, 1, :data)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("sectionid", $sectionId, PDO::PARAM_STR);
        $statement->bindValue("name", $contentTitle, PDO::PARAM_STR);
        $statement->bindValue("data", $content, PDO::PARAM_STR);
        $statement->execute();
    }

}
