<?php

require_once "models/assignment.php";
require_once "models/submission.php";

class Assignments {
    private $hub;
    private $db;

    public function __construct(Hub $hub){
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
    }

    public function getById(int $id) : ?Assignment{
        return new Assignment($this->hub, $id);
    }

    public function exists(int $id) : bool {
        if ($this->db->select("SELECT * from assignment where pk_assignment_id=?", [$id], "i", true) == null){
            return false;
        } else {
            return true;
        }
    }

    /**
     * method: getAssignmentById
     * assignment_id: int $id
     * @return array|null
     */
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

    /**
     * method: createAssignment
     * group_id: int
     * due_time: string (datetime)
     * title: string
     * text*: string
     * file_path*: string
     * @return array|null
     */
    public function createAssignment(): ?array {
        if (empty($_SESSION["userId"]) ||
            empty($_POST["group_id"]) ||
            !is_numeric($_POST["group_id"]) ||
            empty($_POST["due_time"]) ||
            empty($_POST["title"])) {
            return null;
        }

        //TODO: centralise perm check
        if(!isset($_SESSION['username']) && !isset($_SESSION['userType'])){ //cecks wheter user is logged in and is teacher
            return ["success" => false, "noPermission" => true, "msg" => "Not logged in!"];
        }
        if ($_SESSION['userType'] != 1) {
            return ["success" => false, "noPermission" => true];
        }

        if (!$this->hub->getGroups()->getById($_POST["group_id"])->isMember($this->hub->getUsers()->getById($_SESSION["userId"]))){
            return ["success" => false, "userNotInGroup" => true];
        }

        $creator_id = $_SESSION["userId"];
        $group_id = $_POST["group_id"];
        $due_time = date("Y-m-d H:i:s", strtotime($_POST["due_time"]));
        $title = $_POST["title"];

        isset($_POST["text"]) ? $text = $_POST["text"] : $text = null;
        isset($_POST["file_path"]) ? $file_path = $_POST["file_path"] : $file_path = null;

        $query = "INSERT INTO assignment (fk_user_id, fk_group_id, due_time, title, text, file_path) VALUES (?,?,?,?,?,?)";
        $new_id = $this->db->insert($query, [$creator_id, $group_id, $due_time, $title, $text, $file_path], "iissss");

        if (isset($new_id)){
            return ["success" => true, "msg" => "Assignment successfully created!", "assignment_id" => $new_id];
        } else {
            return ["success" => false, "msg" => "Error creating assignment."];
        }
    }
}