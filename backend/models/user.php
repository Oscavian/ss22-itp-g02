<?php

require_once("models/assignment.php");

class User {
    //private $user_id;
    //private $user_type;
    private $username;
    //private $password_hash;
    //private $first_name;
    //private $last_name;
    //private $user_type;
    private $db;
    
    public function __construct($username) {
        $this->username = $username;
        $this->db = new Database();
    }

    public function getFirstName(){
        $result = $this->db->getUserData($this->username);
        return $result["first_name"];
    }

    public function getLastName(){
        $result = $this->db->getUserData($this->username);
        return $result["last_name"];
    }

    public function getUsername(){
        return $this->username;
    }

    public function getUserId(){
        $result = $this->db->getUserData($this->username);
        return $result["pk_user_id"];
    }

    public function getUserType(){
        $result = $this->db->getUserData($this->username);
        return $result["fk_user_type"];
    }

    public function getPasswordHash(){
        $result = $this->db->getUserData($this->username);
        return $result["password"];
    }

    public function matchPassword($password){
        //hashing password
        if($password == $this->getPasswordHash()){
            return true;
        }
        return false;
    }
}

