<?php
require_once "models/user.php";

class Users {
    private $db;
    private $hub;

    public function __construct(Hub $hub){
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
    }

    public function getById($user_id){

    }

    public function login(): ?array {
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

    public function logout(): array {
        session_destroy();
        $res["success"] = true;
        return $res;
    }

    public function registerTeacher() {

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

    public function getLoginStatus(): array {
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

    public function checkUserNameAvailable() : ?array {
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
}