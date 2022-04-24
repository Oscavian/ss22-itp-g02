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


    public function getById(int $id) : ?Assignment{
        return new Assignment($this->hub, $id);
    }

    public function exists(int $id) : bool {
        if ($this->db->select("SELECT * from assignment where pk_assignment_id=?", [$id], "i") == null){
            return false;
        } else {
            return true;
        }
    }

    public function getAssignmentById() : ?array {
        if (empty($_POST["assignment_id"])){
            return null;
        } else {
            $id = $_POST["assignment_id"];
        }

        //TODO: perm check

        if ($this->exists($id)){
            $assignment = $this->getById($id);
        } else {
            return ["success" => false, "msg" => "Assignment with ID $id does not exist!", "inputInvalid" => true];
        }

        return $assignment->getBaseData();
    }

    public function createAssignment(): ?array {
        if (/*empty($_SESSION["user_id"]) ||*/
            empty($_POST["group_id"]) ||
            !is_numeric($_POST["group_id"]) ||
            empty($_POST["due_time"]) ||
            empty($_POST["title"])) {
            return null;
        }

        //TODO: perm check
        //check if user in group

        //TODO: change to session value
        //$creator_id = $_SESSION["user_id"];
        $creator_id = 1;

        $group_id = $_POST["group_id"];
        $due_time = date("Y-m-d H:i:s", strtotime($_POST["due_time"]));
        $title = $_POST["title"];

        isset($_POST["text"]) ? $text = $_POST["text"] : $text = null;
        isset($_POST["file_path"]) ? $file_path = $_POST["file_path"] : $file_path = null;

        $query = "INSERT INTO assignment (fk_user_id, fk_group_id, due_time, title, text, file_path) VALUES (?,?,?,?,?,?)";
        $new_id = $this->db->insert($query, [$creator_id, $group_id, $due_time, $title, $text, $file_path], "iissss");

        if ($new_assignment = new Assignment($this->hub, $new_id)){
            return ["success" => true, "msg" => "Assignment successfully created!", "assignment_id" => $new_assignment->getId()];
        } else {
            return ["success" => false, "msg" => "Error creating assignment."];
        }
    }
}