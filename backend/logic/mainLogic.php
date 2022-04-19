<?php


include "db/database.php";

class MainLogic {

    private $db;
    function __construct()
    {
        $this->db = new Database();
    }
    
    function handleRequest($method)
    {
        switch ($method) {
            case "login":
                return $this->login();
            case "logout":
                return $this->logout();
            case "getLoginStatus":
                return $this->getLoginStatus();
            case "checkUserNameAvailable":
                return $this->checkUserNameAvailable();
            case "registerTeacher":
                return $this->registerTeacher();
            case "createGroup":
                return $this->createGroup();
                break;
            case "getUserGroups":
                return $this->getUserGroups();
                break;
            case "getGroupName":
                return $this->getGroupName();
                break;
            case "getGroupChatId":
                return $this->getGroupChatId();
                break;
            case "getMessages":
            
                break;
            case "sendMessage":
                
                break;
            case "getAssignments":
            
                break;
            case "createAssignments":
                
                break;
            case "uploadAssignments":
            
                break;
            default:
                return null;
                break;
        }
        return null;
    }

    private function login(){
        if(empty($_POST["user"]) || empty($_POST["password"])){
            return null;
        }

        $user = new User();
        if($user->initializeWithUsername($_POST["user"])){
            if($user->matchPassword($_POST["password"])){
                $_SESSION['username']= $user->getUsername();
                $_SESSION['userId']= $user->getUserId();
                $_SESSION['userType']= $user->getUserType();
                $res["success"] = true;
                return $res;
            }
        }
        $res["success"] = false;
        return $res;
    }

    private function logout(){
        session_destroy();
        $res["success"] = true;
        return $res;
    }

    public function getLoginStatus(){       
        if(!empty($_SESSION['username']) && !empty($_SESSION['userId']) && !empty($_SESSION['userType'])){
            $res["isLoggedIn"] = true;
            $res["username"] = $_SESSION['username'];
            $res["userId"] = $_SESSION['userId'];
            $res["userType"] = $_SESSION['userType'];
            return $res;
        }

        $res["isLoggedIn"] = false; 
        return $res;
    }

    private function checkUserNameAvailable(){
        if(!isset($_POST["user"]) || $_POST["user"] == ""){
            return null;
        }

        if($this->db->checkUserNameAvailable(($_POST["user"]))){
            $res["userNameAvailable"] = true;
            return $res;
        }

        $res["userNameAvailable"] = false;
        return $res;

    }

    private function registerTeacher(){

        if(empty($_POST["user"]) || empty($_POST["password"]) || empty($_POST["first_name"]) || empty($_POST["last_name"])){
            return null;
        }

        //sanitises input
        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $username = test_input(($_POST["user"]));
        $password = test_input(($_POST["password"]));
        $first_name = test_input(($_POST["first_name"]));
        $last_name = test_input(($_POST["last_name"]));


        // --- Backend form-validation ---
        if (!preg_match("/^[a-zA-Z-' ]*$/", $first_name) || strlen($first_name) > 50) {
            $res["success"] = false;
            $res["formDataInvalid"] = true;
            return $res;
        }

        if (!preg_match("/^[a-zA-Z-' ]*$/", $last_name) || strlen($last_name) > 50) {
            $res["success"] = false;
            $res["formDataInvalid"] = true;
            return $res;
        }

        if (strlen($username) < 6 || strlen($username) > 50){
            $res["success"] = false;
            $res["formDataInvalid"] = true;
            return $res;
        }

        if (strlen($password) < 6){
            $res["success"] = false;
            $res["formDataInvalid"] = true;
            return $res;
        }

        if(!$this->db->checkUserNameAvailable($username)){
            $res["success"] = false;
            $res["userNameUnavailable"] = true;
            return $res;
        }
        // --- End of form validation ---

        $user_type = 1; // = teacher
        $this->db->registerUser($username, $password, $first_name, $last_name, $user_type);
        
        $user = new User();
        if($user->initializeWithUsername("$username")){
            $_SESSION['username']= $user->getUsername();
            $_SESSION['userId']= $user->getUserId();
            $_SESSION['userType']= $user->getUserType();
            $res["success"] = true;
            return $res;
        }

        $res["success"] = false;
        $res["unknownError"] = true;
        return $res;
    }

    function createGroup(){
        
        if(empty($_POST["groupName"])){
            return null;
        }
        
        if(empty($_SESSION['username']) && $_SESSION['userType'] == 1){ //cecks wheter user is logged in and is teacher
            $res["success"] = false;
            $res["noPermission"] = true;
            return $res;
        }

        $newGroupId = $this->db->createGroup($_POST["groupName"]);

        $this->db->assignUserGroup($newGroupId, $_SESSION['userId']);

        $res["success"] = true;
        $res["newGroupId"] = $newGroupId;
        return $res;
    }

    function getUserGroups(){
        
        if(empty($_SESSION['username'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        $res = $this->db->getUserGroups($_SESSION['userId']);
        
        if(empty($res)){
            $res["success"] = true;
            $res["noGroups"] = true;
            return $res;
        }
            return $res;
    }

    function getGroupName(){
        if(empty($_POST["groupId"])){
            return null;
        }

        if(empty($_SESSION['username'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }
        
        if($this->db->userIsInGroup($_SESSION['userId'], $_POST["groupId"])){
            $res["success"] = true;
            $res["groupName"] = $this->db->getGroupName($_POST["groupId"]);
            return $res;
        }

        $res["success"] = false;
        $res["userNotInGroup"] = true;
        return $res;
    }

    function getGroupChatId(){
        if(empty($_POST["groupId"])){
            return null;
        }

        if(empty($_SESSION['username'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }
        
        if($this->db->userIsInGroup($_SESSION['userId'], $_POST["groupId"])){
            $res["success"] = true;
            $res["groupChatId"] = $this->db->getGroupChatId($_POST["groupId"]);
            return $res;
        }

        $res["success"] = false;
        $res["userNotInGroup"] = true;
        return $res;
    }
}