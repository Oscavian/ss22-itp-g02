<?php

require_once "db/database.php";

class Assignment {

    private $db;
    private $hub;
    private $assignment_id;
    private $creator_id;
    private $group_id;
    private $creation_time;
    private $due_time;
    private $title;
    private $text;
    private $file_path;
    private $submissions = [];

    private $isExpired;

    public function __construct(Hub $hub, $id = null) {
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
        $this->assignment_id = null;
        if ($this->db->select("SELECT * FROM assignment where pk_assignment_id=?", [$id], "i", true) == null){
            return null;
        } else {
            $this->assignment_id = $id;
        }
    }

    public function getBaseData(): array {
        $query = "SELECT pk_assignment_id as assignment_id, fk_user_id as creator_id, username as creator_name, fk_group_id as group_id, time, due_time, title, text, file_path FROM assignment JOIN  user u ON assignment.fk_user_id = u.pk_user_id where pk_assignment_id = ?";
        $result = $this->db->select($query, [$this->assignment_id], "i", true);

        $this->creator_id = $result["creator_id"];
        $this->group_id = $result["group_id"];
        $this->creation_time = $result["time"];
        $this->due_time = date("Y-m-d H:i:s", strtotime($result["due_time"]));
        $this->title = $result["title"];
        $this->text = $result["text"];
        $this->file_path = $result["file_path"];

        if (strtotime("now") > strtotime($this->due_time)){
            $this->isExpired = true;
        } else {
            $this->isExpired = false;
        }

        $result["due_time"] = $this->due_time;
        $result["isExpired"] = $this->isExpired;

        return $result;
    }

    public function getId() {
        return $this->assignment_id;
    }

    public function getCreationTime() {
        if (empty($this->creation_time)){
            return $this->creation_time = $this->db->select("SELECT time from assignment where pk_assignment_id=?", [$this->assignment_id], "i", true)["time"];
        }
        return $this->creation_time;
    }

    public function getCreatorId() {
        if (empty($this->creator_id)){
            return $this->creator_id = $this->db->select("SELECT fk_user_id from assignment where pk_assignment_id=?", [$this->assignment_id], "i", true)["user_id"];
        }
        return $this->creator_id;
    }

    public function getDueTime() {
        if (empty($this->creation_time)){
            return $this->creation_time = $this->db->select("SELECT due_time from assignment where pk_assignment_id=?", [$this->assignment_id], "i", true)["due_time"];
        }
        return $this->creation_time;
    }


    public function getFilePath() {
        if (empty($this->file_path)){
            return $this->file_path = $this->db->select("SELECT file_path from assignment where pk_assignment_id=?", [$this->assignment_id], "i", true)["file_path"];
        }
        return $this->file_path;
    }

    public function getGroupId() {
        if (empty($this->group_id)){
            return $this->group_id = $this->db->select("SELECT fk_group_id from assignment where pk_assignment_id=?", [$this->assignment_id], "i", true)["fk_group_id"];
        }
        return $this->group_id;
    }

    public function getText() {
        if (empty($this->text)){
            return $this->text = $this->db->select("SELECT text from assignment where pk_assignment_id=?", [$this->assignment_id], "i", true)["text"];
        }
        return $this->text;
    }

    public function getTitle() {
        if (empty($this->title)){
            return $this->title = $this->db->select("SELECT title from assignment where pk_assignment_id=?", [$this->assignment_id], "i", true)["title"];
        }
        return $this->title;
    }

    public function isExpired() : bool {
        if ($this->isExpired){
            return true;
        } else {
            return false;
        }
    }

    //TODO: add submission methods
    public function getSubmissions(): array {
        if (empty($this->submissions)){
            //fetch submissions by Ass Id
            //return ...
        }
        return $this->submissions;
    }
}