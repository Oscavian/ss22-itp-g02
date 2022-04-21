<?php

require_once "assignments.php";
require_once "users.php";
require_once "groups.php";


class Hub {

    private $db;

    private $assignments;
    private $chats;
    private $groups;
    private $users;


    public function getDb(): Database {
        if ($this->db == null) {
            require_once "../db/database.php";
            return $this->db = new Database();
        }
        return $this->db;
    }

    public function getChats() {
        if ($this->db == null) {
            require_once "../db/database.php";
            return $this->chats = new Chats();
        }
        return $this->chats;
    }

    public function getAssignments() {
        if ($this->db == null) {
            require_once "../models/assignment.php";
            return $this->assignments = new Assignment();
        }
        return $this->assignments;
    }

    public function getUsers() {
        return $this->users;
    }
}