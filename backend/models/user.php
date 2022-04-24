<?php

class User {
    private $user_id;
    private $user_type;
    private $username;
    private $password_hash;
    private $first_name;
    private $last_name;
    
    private $isValidUser = false;


    //returns false if user was not found in database
    public function initializeWithUsername($username){
        if(!isset($username) || $username == ""){
            return false;
        }

        $db = new Database();
        $result = $db->getUserData($username);
        if(!isset($result)){
            return false;
        }

        $this->user_id = $result["pk_user_id"];
        $this->user_type = $result["fk_user_type"];
        $this->username = $result["username"];
        $this->password_hash = $result["password"];
        $this->first_name = $result["first_name"];
        $this->last_name = $result["last_name"];

        $this->isValidUser = true;
        return true;
    }

    public function getFirstName(){
        if($this->isValidUser){
            return $this->first_name;
        }
        return;
    }

    public function getLastName(){
        if($this->isValidUser){
            return $this->last_name;
        }
        return;

    }

    public function getUsername(){
        if($this->isValidUser){
            return $this->username;
        }
        return;
    }

    public function getUserId(){
        if($this->isValidUser){
            return $this->user_id;
        }
        return;
    }

    public function getUserType(){
        if($this->isValidUser){
            return $this->user_type;
        }
        return;
    }

    private function getPasswordHash(){
        if($this->isValidUser){
            return $this->password_hash;
        }
        return;
    }

    public function matchPassword($password){

        if(!empty($password) && password_verify($password, $this->getPasswordHash())){
            return true;
        }
        return false;
    }
}

