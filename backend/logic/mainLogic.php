<?php


require_once "db/database.php";
require_once "hub.php";

class MainLogic {

    private $db;
    private $hub;

    public function __construct() {
        $this->db = new Database();
        $this->hub = new Hub();
    }

    public function handleRequest($method): ?array {

        $this->sanitizePostArray();

        switch ($method) {
            /* USERS */
            case "login":
                return $this->hub->getUsers()->login();
            case "logout":
                return $this->hub->getUsers()->logout();
            case "getLoginStatus":
                return $this->hub->getUsers()->getLoginStatus();
            case "checkUserNameAvailable":
                return $this->hub->getUsers()->checkUserNameAvailable();
            case "registerTeacher":
                return $this->hub->getUsers()->registerTeacher();

            /* GROUPS */
            case "createGroup":
                return $this->hub->getGroups()->createGroup();
            case "getUserGroups":
                return $this->hub->getGroups()->getUserGroups();
            case "getGroupName":
                return $this->hub->getGroups()->getGroupName();
            case "getGroupChatId":
                return $this->hub->getGroups()->getGroupChatId();

            /* ASSIGNMENTS */
            case "getAssignmentById":
                return $this->hub->getAssignments()->getAssignmentById();
            case "createAssignment":
                return $this->hub->getAssignments()->createAssignment();
            case "uploadAssignments":
                break;

            /* CHATS */
            case "getMessages":
                break;
            case "sendMessage":
                break;
            default:
                return null;
        }
        return null;
    }

    private function sanitizePostArray(){
        foreach ($_POST as $key => $value){
            $_POST[$key] = $this->test_input($value);
        }
    }

    public function test_input($data): string {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}