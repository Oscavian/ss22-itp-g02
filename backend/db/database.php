<?php

require_once("models/assignment.php");
require_once("models/chat.php");
require_once("models/groups.php");
require_once("models/message.php");
require_once("models/user.php");
class Database {
    
    
    private $connection;
    public function __construct(){
        include("db/dbaccess.php");
        
        $this->connection = new mysqli($db_servername, $db_username, $db_password, $db_name);

        if (!$this->connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function __destruct(){
        $this->connection->close();
    }
    
    public function checkUserNameAvailable($username){
        if(!isset($username) || $username == ""){
            return false;
        }

        $result = $this->getUserData($username);
        if(isset($result)){
            return false;
        }

        return true;
    }

    function getUserData($username){
        $stmt = $this->connection->prepare("SELECT * FROM user WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();        
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result->fetch_assoc();
    }

    function registerUser($username, $password, $first_name, $last_name, $user_type){

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->connection->prepare("INSERT INTO user (fk_user_type, first_name, last_name, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_type, $first_name, $last_name, $username, $password_hash);
        $stmt->execute();        
        $stmt->close();
        
        return;
    }

    function createGroup($groupName){

        
        $stmt = $this->connection->prepare("INSERT INTO chat (name) VALUES (?)");
        $stmt->bind_param("s", $groupName);
        $stmt->execute();        
        $stmt->close();

        $newChatId = $this->connection->insert_id;
        
        $stmt = $this->connection->prepare("INSERT INTO groups (name, fk_chat_id) VALUES (?, ?)");
        $stmt->bind_param("si", $groupName, $newChatId);
        $stmt->execute();        
        $stmt->close();
        
        return $newGroupId = $this->connection->insert_id;
    }

    function assignUserGroup($groupId, $userId){

        $stmt = $this->connection->prepare("INSERT INTO user_group (fk_group_id, fk_user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $groupId, $userId);
        $stmt->execute();        
        $stmt->close();
        
        return;
    }

    function getUserGroups($userId){

        $stmt = $this->connection->prepare("SELECT * FROM user_group WHERE fk_user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();        
        $result = $stmt->get_result();
        $stmt->close();
        
        $groups = [];
        while($nextResult = $result->fetch_assoc()){
            $groups[] = $nextResult;
        }

        $res = [];
        foreach($groups as $group){
            $stmt = $this->connection->prepare("SELECT name FROM groups WHERE pk_group_id = ?");
            $stmt->bind_param("i", $group["fk_group_id"]);
            $stmt->execute();        
            $result = $stmt->get_result();
            $stmt->close();

            $groupName = $result->fetch_assoc()["name"];

            $res[] = array("groupName" => $groupName, "groupId" => $group["fk_group_id"]);
        }

        return $res; 
    }


    function userIsInGroup($userId, $groupId){

        foreach($this->getUserGroups($userId) as $user_group){
            if($user_group["groupId"] == $groupId){
                return true;
            }
        };

        return false;
    }

    function getGroupName($groupId){
        
        $stmt = $this->connection->prepare("SELECT * FROM groups WHERE pk_group_id=?");
        $stmt->bind_param("i", $groupId);
        $stmt->execute();        
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result->fetch_assoc()["name"];
    }

    function getGroupChatId($groupId){
        
        $stmt = $this->connection->prepare("SELECT * FROM groups WHERE pk_group_id=?");
        $stmt->bind_param("i", $groupId);
        $stmt->execute();        
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result->fetch_assoc()["fk_chat_id"];
    }
}

?>