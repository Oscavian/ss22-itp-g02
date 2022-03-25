<?php

use LDAP\Result;

require_once("models/assignment.php");
require_once("models/chat.php");
require_once("models/groups.php");
require_once("models/message.php");
require_once("models/user.php");
class Database {
    
    
    private $connection;
    public function __construct(){
        include("db/database_access.php");
        
        $this->connection = new mysqli($db_servername, $db_username, $db_password, $db_name);

        if (!$this->connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function __destruct(){
        $this->connection->close();
    }
    
    function getUserData($username){
        $result = array();
        $stmt = $this->connection->prepare("SELECT * FROM user WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();        
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result->fetch_assoc();
    }
}

?>