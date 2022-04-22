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

    public function getChats(): Chats {
        if ($this->db == null) {
            require_once "chats.php";
            return $this->chats = new Chats();
        }
        return $this->chats;
    }

    public function getAssignments(): Assignments
    {
        if ($this->db == null) {
            require_once "assignments.php";
            return $this->assignments = new Assignments();
        }
        return $this->assignments;
    }

    public function getUsers(): Users {
        if ($this->users == null) {
            require_once "users.php";
            return $this->users = new Users();
        }
        return $this->users;
    }

    public function getGroups(): Groups {
        if ($this->groups == null) {
            require_once "groups.php";
            return $this->groups = new Groups();
        }
        return $this->groups;
    }
}