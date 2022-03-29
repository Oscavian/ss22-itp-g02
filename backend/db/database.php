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
}

?>