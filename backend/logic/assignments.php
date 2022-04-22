<?php

require_once "models/assignment.php";
require_once "models/submission.php";

class Assignments {
    private $hub;
    private $db;
    private $users;

    public function __construct(Hub $hub){
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
        $this->users = $this->hub->getUsers();
    }


    public function getBaseDataById() : ?array {
        if (empty($_POST["assignment_id"] ||
            !is_numeric($_POST["assignment_id"]))){
            return null;
        } else {
            $id = $_POST["assignment_id"];
        }

        $assignment = new Assignment($this->hub, $id);
        if ($assignment->getId() != null){
            return $assignment->getAssignmentData();
        } else {
            return ["success" => false, "inputInvalid" => true];
        }
    }

    public function createAssignment() {
        if (empty($_POST["user_id"]) ||
            empty($_POST["group_id"]) ||
            empty($_POST["due_time"]) ||
            empty($_POST["title"])) {
            return null;
        }
        $creator_id = $_POST["user_id"];
        $group_id = $_POST["group_id"];
        $due_time = date("Y-m-d H:i:s", strtotime($_POST["due_time"]));
        $title = $_POST["title"];

        isset($_POST["text"]) ? $text = $_POST["text"] : $text = null;
        isset($_POST["file_path"]) ? $file_path = $_POST["file_path"] : $file_path = null;

        $new_assignment = new Assignment($this->hub);

        if ($this->storeNewAssignment($creator_id, $group_id, $due_time, $title, $text, $file_path)){
            return ["success" => true, "assignment_id" => $new_assignment->getId()];
        } else {
            return ["success" => false];
        }
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

}