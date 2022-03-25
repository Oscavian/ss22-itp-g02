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
                $res = $this->login();
                break;
            case "logout":
                session_destroy();
                $res = "success";
                break;
            case "getLoginStatus":
                $res = "Username: " . $_SESSION['user'] . ", Usertype: " . $_SESSION['userType'];
                break;
            case "register":
            
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
                $res = null;
                break;
        }
        return $res;
    }

    private function login(){
        $user = new User($_POST["user"]);
                if($user->matchPassword($_POST["password"])){
                    $res = "success";
                    $_SESSION['user']= $user->getUsername();
                    $_SESSION['userType']= $user->getUserType();
                }else{
                    $res = "fail";
                }
        return $res;
    }
}