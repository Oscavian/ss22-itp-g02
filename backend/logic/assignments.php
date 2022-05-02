<?php

require_once "models/assignment.php";
require_once "models/submission.php";

class Assignments {
    private $hub;

    public function __construct(Hub $hub){
        $this->hub = $hub;
    }

    public function getById(int $id) : ?Assignment{
        return new Assignment($this->hub, $id);
    }

    /**
     * method: getAssignmentById
     * assignment_id: int $id
     * @return array|null
     */
    public function getAssignmentById() : ?array {
        if (empty($_POST["assignment_id"])){
            return null;
        }

        //TODO: perm check

        $assignment = $this->getById($_POST["assignment_id"]);

        if ($assignment->exists()){
            return $assignment->getBaseData();
        }

        return ["success" => false, "msg" => "Assignment with ID" . $_POST["assignment_id"] . " does not exist!", "inputInvalid" => true];
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
        if (empty($_POST["group_id"]) ||
            !is_numeric($_POST["group_id"]) ||
            empty($_POST["due_time"]) ||
            empty($_POST["title"])) {
            return null;
        }

        //TODO: centralise perm check
        if(!isset($_SESSION['userId'])){ //cecks wheter user is logged in and is teacher
            return ["success" => false, "noPermission" => true, "msg" => "Not logged in!"];
        }
        // if ($_SESSION['userId'] != 1) {
        //     return ["success" => false, "noPermission" => true];
        // }

        if (!$this->hub->getGroups()->getById($_POST["group_id"])->isMember($this->hub->getUsers()->getById($_SESSION["userId"]))){
            return ["success" => false, "userNotInGroup" => true];
        }

        $creator_id = $_SESSION["userId"];
        $group_id = $_POST["group_id"];
        $due_time = date("Y-m-d H:i:s", strtotime($_POST["due_time"]));
        $title = $_POST["title"];

        isset($_POST["text"]) ? $text = $_POST["text"] : $text = null;
        isset($_POST["file_path"]) ? $file_path = $_POST["file_path"] : $file_path = null;

        $assignment = new Assignment($this->hub);
        $assignment->createAssignment($creator_id, $group_id, $due_time, $title, $text, $file_path);

        if (!$assignment->exists()){
            return ["success" => false, "msg" => "Error creating assignment."];
        }

        return ["success" => true, "msg" => "Assignment successfully created!", "assignment_id" => $assignment->getId()];
    }
}