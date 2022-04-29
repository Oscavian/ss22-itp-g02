<?php
require_once "models/user.php";

class Users {
    private $db;
    private $hub;

    public function __construct(Hub $hub){
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
    }

    public function getById($user_id): User {
        return new User($this->hub, $user_id);
    }

    /**
     * method: login
     * @return array|null
     */
    public function login(): ?array {
        if (empty($_POST["user"]) || empty($_POST["password"])) {
            return null;
        }

        $user = new User($this->hub);
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

    /**
     * method: logout
     * @return array
     */
    public function logout(): array {
        session_destroy();
        $res["success"] = true;
        return $res;
    }

    /**
     * method: getUserGroups
     * @return array
     */
    public function getUserGroups(): array {

        if(empty($_SESSION['username'])){ //cecks whether user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        $res = [];
        foreach ($this->getById($_SESSION["userId"])->getGroups() as $group){
            $item = new stdClass();
            $item->groupName = $group->getName();
            $item->groupId = $group->getGroupId();
            $res[] = $item;
        }

        if(empty($res)){
            $res["success"] = true;
            $res["noGroups"] = true;
            return $res;
        }
        return $res;
    }

    /**
     * @return string[]|null
     */
    public function registerTeacher() {

        if (empty($_POST["user"]) || empty($_POST["password"]) || empty($_POST["first_name"]) || empty($_POST["last_name"])) {
            return null;
        }

        $username = $_POST["user"];
        $password = $_POST["password"];
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];


        $dataOk = true;
        $res = ["msg" => ""];
        // --- Backend form-validation ---
        if (!preg_match("/^[a-zA-Z-' ]*$/", $first_name) || strlen($first_name) > 50) {
            $res["msg"] .= "Invalid fist_name\n";
            $dataOk = false;
        }

        if (!preg_match("/^[a-zA-Z-' ]*$/", $last_name) || strlen($last_name) > 50) {
            $res["msg"] .= "Invalid last_name\n";
            $dataOk = false;
        }

        if (strlen($username) < 6 || strlen($username) > 50) {
            $res["msg"] .= "Invalid username\n";
            $dataOk = false;
        }

        if (strlen($password) < 6) {
            $res["msg"] .= "Invalid password\n";
            $dataOk = false;
        }

        if (!$dataOk) {
            $res["success"] = false;
            return $res;
        }

        //TODO: refactor
        if (!$this->db->checkUserNameAvailable($username)) {
            $res["success"] = false;
            $res["userNameUnavailable"] = true;
            return $res;
        }
        // --- End of form validation ---

        $user_type = 1; // = teacher
        //TODO: refactor
        $this->db->registerUser($username, $password, $first_name, $last_name, $user_type);

        $user = new User($this->hub);
        if ($user->initializeWithUsername("$username")) {
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['userId'] = $user->getUserId();
            $_SESSION['userType'] = $user->getUserType();
            $res["success"] = true;
            $res["msg"] = "User successfully created!";
            return $res;
        }

        $res["success"] = false;
        $res["unknownError"] = true;
        return $res;
    }

    /**
     * method: getLoginStatus
     * @return array
     */
    public function getLoginStatus(): array {
        if (!empty($_SESSION['username']) && !empty($_SESSION['userId']) && !empty($_SESSION['userType'])) {
            $res["isLoggedIn"] = true;
            $res["username"] = $_SESSION['username'];
            $res["userId"] = $_SESSION['userId'];
            $res["userType"] = $_SESSION['userType'];
            print_r($_SESSION);
            return $res;
        }
        $res["isLoggedIn"] = false;
        return $res;
    }

    /**
     * method: checkUserNameAvailable
     * @return array|null
     */
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