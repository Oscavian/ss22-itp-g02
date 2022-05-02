<?php

class Hub {

    private $db;
    private $permissions;

    private $assignments;
    private $chats;
    private $groups;
    private $users;


    public function getDb(): Database {
        if ($this->db == null) {
            require_once "db/database.php";
            return $this->db = new Database();
        }
        return $this->db;
    }

    public function getPermissions(): Permissions {
        if ($this->permissions == null) {
            require_once "permissions.php";
            return $this->permissions = new Permissions($this);
        }
        return $this->permissions;
    }

    public function getChats(): Chats {
        if ($this->chats == null) {
            require_once "chats.php";
            return $this->chats = new Chats($this);
        }
        return $this->chats;
    }

    public function getAssignments(): Assignments
    {
        if ($this->assignments == null) {
            require_once "assignments.php";
            return $this->assignments = new Assignments($this);
        }
        return $this->assignments;
    }

    public function getUsers(): Users {
        if ($this->users == null) {
            require_once "users.php";
            return $this->users = new Users($this);
        }
        return $this->users;
    }

    public function getGroups(): Groups {
        if ($this->groups == null) {
            require_once "groups.php";
            return $this->groups = new Groups($this);
        }
        return $this->groups;
    }
}