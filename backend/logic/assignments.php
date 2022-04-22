<?php

include "models/assignment.php";
include "models/submission.php";

class Assignments {
    private $hub;

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