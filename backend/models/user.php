<?php

class User {
    private $hub;
    private $db;
    private $user_id;
    private $user_type;
    private $username;
    private $first_name;
    private $last_name;
    private $groups = [];

    public function __construct(Hub $hub, $id = null){
        $this->db = $hub->getDb();
        $this->hub = $hub;

        empty($this->db->select("SELECT * FROM user WHERE pk_user_id = ?", [$id], "i")) ? $this->user_id = null : $this->user_id = $id;
    }

    public function getBaseData(): array {

        $query = "SELECT pk_user_id as user_id, fk_user_type as user_type, first_name, last_name, username, password where pk_user_id = ?"; 
        $result = $this->db->select($query, [$this->user_id], "i", true);
        
        $this->user_id = $result["user_id"];
        $this->user_type = $result["user_type"];
        $this->username = $result["username"];
        $this->first_name =  $result["first_name"];
        $this->last_name = $result["last_name"];

        return $result;
    }

    /**
     * initializes user with  username
     * returns true if user with username exists
     * @return bool
     */
    public function initializeByUserName($username){
        
        $user = $this->db->select("SELECT * from user where username=?", [$username], "s", true);
            
        if(empty($user)){
            return false;
        }

        $this->user_id = $user["pk_user_id"];
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
        if(empty($this->first_name)){
            $this->first_name = $this->db->select("SELECT first_name FROM user where pk_user_id = ?", [$this->user_id], "i", true)["first_name"];
        }
        return $this->first_name;
    }

    public function getLastName(){
        if(empty($this->last_name)){
            $this->last_name = $this->db->select("SELECT last_name FROM user where pk_user_id = ?", [$this->user_id], "i", true)["last_name"];
        }
        return $this->last_name;

    }

    public function getUsername(){
        if(empty($this->username)){
            $this->username = $this->db->select("SELECT username FROM user where pk_user_id = ?", [$this->user_id], "i", true)["username"];
        }
        return $this->username;
    }

    public function getUserId(){
            return $this->user_id;
    }

    public function getUserType(){
        if(empty($this->user_type)){
            $this->user_type = $this->db->select("SELECT fk_user_type FROM user where pk_user_id = ?", [$this->user_id], "i", true)["fk_user_type"];
        }
        return $this->user_type;
    }

    private function getPasswordHash(){
        return $this->db->select("SELECT password FROM user where pk_user_id = ?", [$this->user_id], "i", true)["password"];
    }

    /**
     * used for logging in user
     * returns true if login was successful
     * also initializes user
     * @return bool
     */
    public function login($username, $password): bool{
            
        if(!$this->initializeByUserName($username)){
            return false;
        }
        
        if(password_verify($password, $this->getPasswordHash())){
            return true;
        }

        $this->user_id = NULL;
        return false;
    }

    public function registerUser($username, $password, $first_name, $last_name, $user_type){
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $this->user_id = $this->db->insert("INSERT INTO user (fk_user_type, first_name, last_name, username, password) VALUES (?, ?, ?, ?, ?)", [$user_type, $first_name, $last_name, $username, $password_hash], "issss");
        return;
    }
}

