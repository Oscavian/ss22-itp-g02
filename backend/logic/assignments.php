<?php

class Assignments {

    /**
     * method: getAssignmentById
     * assignment_id: int $id
     * @return array|null
     */
    public function getAssignmentById() : ?array {
        if (empty($_POST["assignment_id"])){
            throw new Exception("Invalid Parameters");
        }

        $assignment = Hub::Assignment($_POST["assignment_id"]);
        Permissions::checkCanAccessAssignment($assignment);
        
        return $assignment->getBaseData();    
    }

    /**
     * @throws Exception
     */
    public function getAssignmentList() : ?array {
        if (empty($_POST["group_id"])){
            throw new Exception("Invalid Parameters");
        }

        $group = Hub::Group($_POST["group_id"]);

        if (!$group->exists()){
            throw new Exception("Group does not exist!");
        }

        Permissions::checkIsInGroup($group);

        return $group->getAssignments();
    }

    /**
     * method: createAssignment
     * group_id: int
     * due_time: string (datetime)
     * title: string
     * text*: string
     * @return array|null
     * @throws Exception
     */
    public function createAssignment(): ?array {
        if (empty($_POST["group_id"]) ||
            !is_numeric($_POST["group_id"]) ||
            empty($_POST["due_time"]) ||
            empty($_POST["title"])) {
            throw new Exception("Invalid Parameters");
        }

        $group = Hub::Group($_POST["group_id"]);
        
        Permissions::checkIsTeacher();
        Permissions::checkIsInGroup($group);

        $creator_id = $_SESSION["userId"];
        $group_id = $_POST["group_id"];
        $due_time = date("Y-m-d H:i:s", strtotime($_POST["due_time"]));
        $title = $_POST["title"];

        isset($_POST["text"]) ? $text = $_POST["text"] : $text = null;

        //TODO: make file-upload optional
        try {
            $file_path = Hub::FileHandler()->uploadFile("attachment", "assignments/attachments/", ["pdf", "png"]);
        } catch (ErrorException $ex) {
            return ["success" => false, "error" => $ex->getMessage()];
        }

        $assignment = Hub::Assignment();
        $assignment->storeNewAssignment($creator_id, $group_id, $due_time, $title, $text, $file_path);

        if (!$assignment->exists()){
            throw new Exception("Error creating Assignment!");
        }

        return ["success" => true, "msg" => "Assignment erfolgreich erstellt!", "assignment_id" => $assignment->getId()];
    }
}