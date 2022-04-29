<?php

class User {
    private $hub;
    private $db;
    private $user_id;
    private $user_type;
    private $username;
    private $password_hash;
    private $first_name;
    private $last_name;
    private $groups = [];
    
    private $isValidUser = false;

    public function __construct(Hub $hub, $id = null){
        $this->db = $hub->getDb();
        $this->hub = $hub;

        empty($this->db->select("SELECT * FROM user WHERE pk_user_id = ?", [$id], "i")) ? $this->user_id = null : $this->user_id = $id;
    }

    //TODO: refactor
    //returns false if user was not found in database
    public function initializeWithUsername($username){
        if(!isset($username) || $username == ""){
            return false;
        }

        $result = $this->db->getUserData($username);
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

    /**
     * fetches and returns all group objects the user belongs to
     * @return array
     */
    public function getGroups(): array {
        if (empty($this->groups)){
            $result = $this->db->select("SELECT fk_group_id as group_id FROM user_group WHERE fk_user_id = ?", [$this->user_id], "i");
            foreach ($result as $item){
                $this->groups[] = $this->hub->getGroups()->getById($item["group_id"]);
            }
        }
        return $this->groups;
    }

    public function getFirstName(){
        if($this->isValidUser){
            return $this->first_name;
        }
        return null;
    }

    public function getLastName(){
        if($this->isValidUser){
            return $this->last_name;
        }
        return null;

    }

    public function getUsername(){
        if($this->isValidUser){
            return $this->username;
        }
        return null;
    }

    public function getUserId(){
        if($this->isValidUser){
            return $this->user_id;
        }
        return null;
    }

    public function getUserType(){
        if($this->isValidUser){
            return $this->user_type;
        }
        return null;
    }

    private function getPasswordHash(){
        if($this->isValidUser){
            return $this->password_hash;
        }
        return null;
    }

    public function matchPassword($password){

        if(!empty($password) && password_verify($password, $this->getPasswordHash())){
            return true;
        }
        return false;
    }
}

