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

        if ($this->db->select("SELECT * FROM assignment where pk_assignment_id=?", [$id], "i") == null){
            return null;
        } else {
            $this->assignment_id = $id;
        }


        /*if (isset($id)){
            $query = "SELECT pk_assignment_id as assignment_id, fk_user_id as creator_id, username as creator_name, fk_group_id as group_id, time, due_time, title, text, file_path FROM assignment JOIN  user u ON assignment.fk_user_id = u.pk_user_id where pk_assignment_id = ?";
            $result = $this->db->select($query, [$id], "i");

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
        }*/
    }

    public function getAssignmentData(): array {
        $query = "SELECT pk_assignment_id as assignment_id, fk_user_id as creator_id, username as creator_name, fk_group_id as group_id, time, due_time, title, text, file_path FROM assignment JOIN  user u ON assignment.fk_user_id = u.pk_user_id where pk_assignment_id = ?";
        $result = $this->db->select($query, [$this->assignment_id], "i");

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

        $result["due_time"] = $this->due_time;
        $result["isExpired"] = $this->isExpired;

        return $result;
    }

    public function getId() {
        return $this->assignment_id;
    }

    public function getCreationTime() {
        if (empty($this->creation_time)){
            return $this->creation_time = $this->db->select("SELECT creation_time from assignment where pk_assignment_id=?", [$this->assignment_id], "i")["creation_time"];
        }
        return $this->creation_time;
    }

    public function getCreatorId() {
        if (empty($this->creator_id)){
            return $this->creator_id = $this->db->select("SELECT fk_user_id from assignment where pk_assignment_id=?", [$this->assignment_id], "i")["fk_user_id"];
        }
        return $this->creator_id;
    }

    public function getDueTime() {
        if (empty($this->creation_time)){
            return $this->creation_time = $this->db->select("SELECT creation_time from assignment where pk_assignment_id=?", [$this->assignment_id], "i")["creation_time"];
        }
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

    public function getSubmissions(): array {
        if (empty($this->submissions)){
            //fetch submissions by Ass Id
            //return ...
        }
        return $this->submissions;
    }
}