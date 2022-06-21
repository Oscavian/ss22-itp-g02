<?php

class Submission {
    private $submission_id;
    private $user_id;
    private $assignment_id;
    private $file_path;
    private $creation_time;

    public function __construct($id = null) {
        empty(Database::select("SELECT * FROM student_upload where pk_upload_id=?", [$id], "i", true)) ? $this->submission_id = null : $this->submission_id = $id;
    }

    public function getData(): array {
        $result = Database::select("SELECT * FROM student_upload where pk_upload_id = ?", [$this->submission_id], "i", true);

        $this->user_id = $result["fk_user_id"];
        $this->assignment_id = $result["fk_assignment_id"];
        $this->file_path = $result["file_path"];
        $this->creation_time = $result["time"];

        return get_object_vars($this);
    }

    public function getSubmissionId() {
        return $this->submission_id;
    }

    public function getUserId() {
        if (empty($this->user_id)) {
            return $this->user_id = Database::select("SELECT fk_user_id from student_upload where pk_upload_id = ?", [$this->submission_id], "i", true)["fk_user_id"];
        }
        return $this->user_id;
    }

    public function getFilePath() {
        if (empty($this->file_path)) {
            return $this->file_path = Database::select("SELECT file_path from student_upload where pk_upload_id = ?", [$this->submission_id], "i", true)["file_path"];
        }
        return $this->file_path;
    }

    public function getCreationTime() {
        if (empty($this->creation_time)) {
            return $this->creation_time = Database::select("SELECT time from student_upload where pk_upload_id = ?", [$this->submission_id], "i", true)["time"];
        }
        return $this->creation_time;
    }

    public function getAssignmentId() {
        if (empty($this->user_id)) {
            return $this->assignment_id = Database::select("SELECT fk_assignment_id from student_upload where pk_upload_id = ?", [$this->submission_id], "i", true)["fk_assignment_id"];
        }
        return $this->assignment_id;
    }

    public function storeNewSubmission($user_id, $assignment_id, $file_path): bool {
        $this->submission_id = Database::insert("INSERT INTO student_upload (fk_user_id, fk_assignment_id, file_path) VALUES (?, ?, ?)", [$user_id, $assignment_id, $file_path], "iis");

        if (isset($this->submission_id)) {
            return true;
        } else {
            return false;
        }
    }
}