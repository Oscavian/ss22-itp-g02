<?php


include "db/database.php";

class MainLogic {

    private $db;

    function __construct() {
        $this->db = new Database();
    }

    function handleRequest($method) {
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
            case "getAssignmentById":
                return $this->getAssignmentById();
            case "createAssignment":
                return $this->createAssignment();
            case "getMessages":
                break;
            case "sendMessage":
                break;
            case "uploadAssignments":
                break;
            default:
                return null;
        }
        return null;
    }

    private function getAssignmentById(){
        if (empty($_POST["assignment_id"])){
            return null;
        } else {
            $id = $this->test_input($_POST["assignment_id"]);
        }

        $assignment = new Assignment();

        if ($assignment->initById($id)){
            return $assignment->getAssignmentData();
        } else {
            return ["success" => false, "inputInvalid" => true];
        }
    }


    public function createAssignment() {
        if (empty($_POST["user_id"]) ||
            empty($_POST["group_id"]) ||
            empty($_POST["due_time"]) ||
            empty($_POST["title"])) {
            return null;
        }
        $creator_id = $this->test_input($_POST["user_id"]);
        $group_id = $this->test_input($_POST["group_id"]);
        $due_time = date("Y-m-d H:i:s", strtotime($this->test_input($_POST["due_time"])));
        $title = $this->test_input($_POST["title"]);

        isset($_POST["text"]) ? $text = $this->test_input($_POST["text"]) : $text = null;
        isset($_POST["file_path"]) ? $file_path = $this->test_input($_POST["file_path"]): $file_path = null;

        $new_assignment = new Assignment();

        if ($new_assignment->storeNewAssignment($creator_id, $group_id, $due_time, $title, $text, $file_path)){
            return ["success" => true, "assignment_id" => $new_assignment->getAssignmentId()];
        } else {
            return ["success" => false];
        }


    }

    public function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    private function login() {
        if (empty($_POST["user"]) || empty($_POST["password"])) {
            return null;
        }

        $user = new User();
        if ($user->initializeWithUsername($_POST["user"])) {
            if ($user->matchPassword($_POST["password"])) {
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['userId'] = $user->getUserId();
                $_SESSION['userType'] = $user->getUserType();
                $res["success"] = true;
                return $res;
            }
        }
        $res["success"] = false;
        return $res;
    }

    private function logout() {
        session_destroy();
        $res["success"] = true;
        return $res;
    }

    public function getLoginStatus() {
        if (!empty($_SESSION['username']) && !empty($_SESSION['userId']) && !empty($_SESSION['userType'])) {
            $res["isLoggedIn"] = true;
            $res["username"] = $_SESSION['username'];
            $res["userId"] = $_SESSION['userId'];
            $res["userType"] = $_SESSION['userType'];
            return $res;
        }

        $res["isLoggedIn"] = false;
        return $res;
    }

    private function checkUserNameAvailable() {
        if (!isset($_POST["user"]) || $_POST["user"] == "") {
            return null;
        }

        if ($this->db->checkUserNameAvailable(($_POST["user"]))) {
            $res["userNameAvailable"] = true;
            return $res;
        }

        $res["userNameAvailable"] = false;
        return $res;

    }

    private function registerTeacher() {

        if (empty($_POST["user"]) || empty($_POST["password"]) || empty($_POST["first_name"]) || empty($_POST["last_name"])) {
            return null;
        }

        //sanitises input
        $username = $this->test_input(($_POST["user"]));
        $password = $this->test_input(($_POST["password"]));
        $first_name = $this->test_input(($_POST["first_name"]));
        $last_name = $this->test_input(($_POST["last_name"]));


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

        if (strlen($username) < 6 || strlen($username) > 50) {
            $res["success"] = false;
            $res["formDataInvalid"] = true;
            return $res;
        }

        if (strlen($password) < 6) {
            $res["success"] = false;
            $res["formDataInvalid"] = true;
            return $res;
        }

        if (!$this->db->checkUserNameAvailable($username)) {
            $res["success"] = false;
            $res["userNameUnavailable"] = true;
            return $res;
        }
        // --- End of form validation ---

        $user_type = 1; // = teacher
        $this->db->registerUser($username, $password, $first_name, $last_name, $user_type);

        $user = new User();
        if ($user->initializeWithUsername("$username")) {
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['userId'] = $user->getUserId();
            $_SESSION['userType'] = $user->getUserType();
            $res["success"] = true;
            return $res;
        }

        $res["success"] = false;
        $res["unknownError"] = true;
        return $res;
    }


}