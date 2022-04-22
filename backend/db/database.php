<?php

require_once "models/assignment.php";
require_once "models/chat.php";
require_once "models/group.php";
require_once "models/message.php";
require_once "models/user.php";

class Database {


    private $connection;

    public function __construct() {
        require_once "dbaccess.php";

        $this->connection = new mysqli(DB_SERVERNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

        if (isset($this->connection->connect_error)) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    /**
     * @param string $query
     * @param array|null $params
     * @param string|null $param_types
     * @return array|bool
     */
    public function select(string $query, array $params = null, string $param_types = null){

        /* -- not really necessary --
        //count & check param amount
        $param_count = substr_count($query, '?');
        $param_letter_count = 0;
        foreach (count_chars($param_types, 1) as $i){
            $param_letter_count++;
        }

        if ($param_count != $param_letter_count){
            //remove later for security reasons
            return ["invalidQuery" => true, "msg" => "Param count doesn't match param types in prepared stmt.", "query" => $query];
        } */

        $stmt = $this->connection->prepare($query);
        if (isset($params)) {
            $stmt->bind_param($param_types, ...$params);
        }

        if (!$stmt->execute()){
            return false;
        }

        $result = $stmt->get_result();
        $rows = [];
        $stmt->close();

        if ($result->num_rows == 1){
            return $result->fetch_assoc();
        } else if ($result->num_rows == 0){
            return null;
        } else if ($result->num_rows > 1){
            foreach ($result->fetch_assoc() as $row){
                $rows[] = $row;
            }
            return $rows;
        } else {
            return null;
        }
    }

    /**
     * @param string $query
     * @param array|null $params
     * @param string|null $param_types
     * @return bool
     */
    public function insert(string $query, array $params, string $param_types): bool {

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param($param_types, ...$params);

        if ($stmt->execute()){
            $stmt->close();
            return $this->connection->insert_id;
        } else {
            $stmt->close();
            return false;
        }
    }

    /**
     * @param string $query
     * @param array|null $params
     * @param string|null $param_types
     * @return bool
     */
    public function update(string $query, array $params = null, string $param_types = null): bool {

        $stmt = $this->connection->prepare($query);

        if (isset($params)) {
            $stmt->bind_param($param_types, ...$params);
        }

        if ($stmt->execute()){
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }


    //TODO: move to user class
    public function checkUserNameAvailable($username) {
        if (!isset($username) || $username == "") {
            return false;
        }

        $result = $this->getUserData($username);
        if (isset($result)) {
            return false;
        }

        return true;
    }


    //TODO: move to user class
    function getUserData($username) {
        $stmt = $this->connection->prepare("SELECT * FROM user WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result->fetch_assoc();
    }

    //TODO: move to user class
    function registerUser($username, $password, $first_name, $last_name, $user_type) {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->connection->prepare("INSERT INTO user (fk_user_type, first_name, last_name, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_type, $first_name, $last_name, $username, $password_hash);
        $stmt->execute();
        $stmt->close();

        return;
    }

    //TODO: move to groups class
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

    //TODO: move to groups class
    function assignUserGroup($groupId, $userId){

        $stmt = $this->connection->prepare("INSERT INTO user_group (fk_group_id, fk_user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $groupId, $userId);
        $stmt->execute();        
        $stmt->close();
        
        return;
    }

    //TODO: move to groups class
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

    //TODO: move to groups class
    function userIsInGroup($userId, $groupId){

        foreach($this->getUserGroups($userId) as $user_group){
            if($user_group["groupId"] == $groupId){
                return true;
            }
        };

        return false;
    }

    //TODO: move to groups class
    function getGroupName($groupId){
        
        $stmt = $this->connection->prepare("SELECT * FROM groups WHERE pk_group_id=?");
        $stmt->bind_param("i", $groupId);
        $stmt->execute();        
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result->fetch_assoc()["name"];
    }

    //TODO: move to groups class
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