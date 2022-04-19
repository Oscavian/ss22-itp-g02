<?php

require_once "db/database.php";

class Assignment {

    private $db;
    private $assignment_id;
    private $creator_id;
    private $group_id;
    private $creation_time;
    private $due_time;
    private $title;
    private $text;
    private $file_path;

    private $isExpired;

    public function __construct() {
        $this->db = new Database();
    }

    public function storeNewAssignment($creator_id, $group_id, $due_time, $title, $text = null, $file_path = null): bool {
        $this->creator_id = $creator_id;
        $this->group_id = $group_id;
        $this->due_time = date("Y-m-d H:i:s", strtotime($due_time));
        $this->title = $title;
        $this->file_path = $file_path;
        $this->text = $text;

        if (strtotime($this->creation_time) > strtotime($this->due_time)){
            $this->isExpired = true;
        } else {
            $this->isExpired = false;
        }

        $query = "INSERT INTO assignment (fk_user_id, fk_group_id, due_time, title, text, file_path) VALUES (?,?,?,?,?,?)";
        if ($this->db->insert($query, [$creator_id, $group_id, $due_time, $title, $text, $file_path], "iissss")){
            $this->assignment_id = $this->db->select("SELECT pk_assignment_id from assignment order by pk_assignment_id desc limit 1")["pk_assignment_id"];
            return true;
        } else {
            return false;
        }
    }

    public function initById($id){
        $query = "SELECT pk_assignment_id as assignment_id, fk_user_id as creator_id, username as creator_name, fk_group_id as group_id, time, due_time, title, text, file_path FROM assignment JOIN  user u ON assignment.fk_user_id = u.pk_user_id where pk_assignment_id = ?";
        $result = $this->db->select($query, [$id], "i");

        if ($result == null) {
            return false;
        }

        $this->assignment_id = $result["assignment_id"];
        $this->creator_id = $result["creator_id"];
        $this->group_id = $result["group_id"];
        $this->creation_time = $result["time"];
        $this->due_time = date("Y-m-d H:i:s", strtotime($result["due_time"]));
        $this->title = $result["title"];
        $this->text = $result["text"];
        $this->file_path = $result["file_path"];

        if (strtotime($this->creation_time) > strtotime($this->due_time)){
            $this->isExpired = true;
        } else {
            $this->isExpired = false;
        }

        return true;
    }

    public function getAssignmentData(): array {
        return ["assignment_id" => $this->assignment_id, "title" => $this->title, "creator_id" => $this->creator_id, "group_id" => $this->group_id, "creation_time" => $this->creation_time, "due_time" => $this->due_time, "text" => $this->text, "file_path" => $this->file_path, "isExpired" => $this->isExpired];
    }

    public function getAssignmentId() {
        return $this->assignment_id;
    }


    public function getCreationTime() {
        return $this->creation_time;
    }


    public function getCreatorId() {
        return $this->creator_id;
    }


    public function getDueTime() {
        return $this->due_time;
    }


    public function getFilePath() {
        return $this->file_path;
    }


    public function getGroupId() {
        return $this->group_id;
    }


    public function getText() {
        return $this->text;
    }

    public function getTitle() {
        return $this->title;
    }
}