<?php

class User {
    private $user_id;
    private $user_type;
    private $username;
    private $first_name;
    private $last_name;
    private $groups = [];

    public function __construct($id = null){
        empty(Database::select("SELECT * FROM user WHERE pk_user_id = ?", [$id], "i")) ? $this->user_id = null : $this->user_id = $id;
    }

    /**
     * initializes user with  username
     * returns true if user with username exists
     * @return bool
     */
    public function initializeByUserName($username): bool {
        
        $user = Database::select("SELECT * from user where username=?", [$username], "s", true);
            
        if(empty($user)){
            return false;
        }

        $this->user_id = $user["pk_user_id"];
        return true;
    }

    public function exists(){   
        if(empty($this->user_id)){
            return false;
        }
        return true;
    }
    
    public function getId(){
        return $this->user_id;
    }

    public function getBaseData(): array {

        $query = "SELECT pk_user_id as user_id, fk_user_type as user_type, first_name, last_name, username, password from user where pk_user_id = ?"; 
        $result = Database::select($query, [$this->user_id], "i", true);
        
        $this->user_id = $result["user_id"];
        $this->user_type = $result["user_type"];
        $this->username = $result["username"];
        $this->first_name =  $result["first_name"];
        $this->last_name = $result["last_name"];

        return $result;
    }


    /**
     * fetches and returns all group objects the user belongs to
     * @return array
     */
    public function getGroups(): array {
        if (empty($this->groups)){
            $result = Database::select("SELECT fk_group_id as group_id FROM user_group WHERE fk_user_id = ?", [$this->user_id], "i");
            foreach ($result as $item){
                $this->groups[] = Hub::Group($item["group_id"]);
            }
        }
        return $this->groups;
    }

    /**
     * checks whether the user is part of a group
     * @param Group $group
     * @return bool
     */
    public function isInGroup(Group $group) : bool {
        if(empty(Database::select("SELECT * FROM user_group WHERE fk_user_id = ? AND fk_group_id = ?", [$this->user_id, $group->getId()], "ii"))){
            return false;
        }
        return true;
    }

    /**
     * checks whether the user is in (any) group with another user
     * @param User $otherUser
     * @return bool
     */
    public function isInGroupWith(User $otherUser): bool {
        $query = "SELECT u1.fk_user_id, u2.fk_user_id FROM user_group u1 INNER JOIN user_group u2 ON u1.fk_group_id = u2.fk_group_id AND u1.fk_user_id != u2.fk_user_id WHERE u2.fk_user_id = ? AND u1.fk_user_id = ?";
        if(empty(Database::select($query, [$this->user_id, $otherUser->getId()], "ii"))){
            return false;
        }
        return true;
    }

    public function getFirstName(){
        if(empty($this->first_name)){
            $this->first_name = Database::select("SELECT first_name FROM user where pk_user_id = ?", [$this->user_id], "i", true)["first_name"];
        }
        return $this->first_name;
    }

    public function getLastName(){
        if(empty($this->last_name)){
            $this->last_name = Database::select("SELECT last_name FROM user where pk_user_id = ?", [$this->user_id], "i", true)["last_name"];
        }
        return $this->last_name;

    }

    public function getUsername(){
        if(empty($this->username)){
            $this->username = Database::select("SELECT username FROM user where pk_user_id = ?", [$this->user_id], "i", true)["username"];
        }
        return $this->username;
    }


    public function getUserType(){
        if(empty($this->user_type)){
            $this->user_type = Database::select("SELECT fk_user_type FROM user where pk_user_id = ?", [$this->user_id], "i", true)["fk_user_type"];
        }
        return $this->user_type;
    }

    private function getPasswordHash(){
        return Database::select("SELECT password FROM user where pk_user_id = ?", [$this->user_id], "i", true)["password"];
    }

    /**
     * used for logging in user
     * returns true if login was successful
     * also initializes user
     * @return bool
     */
    public function verifyLogin($username, $password): bool{
            
        if(!$this->initializeByUserName($username)){
            return false;
        }
        
        if(password_verify($password, $this->getPasswordHash())){
            return true;
        }

        $this->user_id = NULL;
        return false;
    }

    public function storeNewUser($username, $password, $first_name, $last_name, $user_type){
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $this->user_id = Database::insert("INSERT INTO user (fk_user_type, first_name, last_name, username, password) VALUES (?, ?, ?, ?, ?)", [$user_type, $first_name, $last_name, $username, $password_hash], "issss");
    }

    public function storeUpdateUserData($type, $data){
        if($type == "username"){
            Database::update("UPDATE user SET username = ?  WHERE pk_user_id = ?", [$data, $this->user_id], "si");
            return;
        }
        if($type == "firstName"){
            Database::update("UPDATE user SET first_name = ?  WHERE pk_user_id = ?", [$data, $this->user_id], "si");
            return;
        }
        if($type == "lastName"){
            Database::update("UPDATE user SET last_name = ?  WHERE pk_user_id = ?", [$data, $this->user_id], "si");
            return;
        }
    }

    public function changePassword($newPassword){
        $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
        Database::update("UPDATE user SET password = ?  WHERE pk_user_id = ?", [$password_hash, $this->user_id], "si");
    }
}

