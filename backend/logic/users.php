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

    public function exists(int $id) : bool {
        $user = $this->getById($id);

        if ($user->exists()){
            return true;
        }
        return false;
    }

    /**
     * method: login
     * user: string
     * password: string
     * @return array|null
     */
    public function login(): ?array {
        if (empty($_POST["user"]) || empty($_POST["password"])) {
            return null;
        }

        $user = new User($this->hub);
        if ($user->login($_POST["user"], $_POST["password"])){
            $_SESSION['userId'] = $user->getUserId();
            return ["success" => true];
        }
        return ["success" => false];
    }

    /**
     * method: logout
     * @return array
     */
    public function logout(): array {
        session_destroy();
        return ["success" => true];
    }

    /**
     * method: getUserGroups
     * @return array
     */
    public function getUserGroups(): array {

        //TODO new Permissions system
        if(empty($_SESSION['userId'])){ //cecks whether user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        $res = [];
        foreach ($this->getById($_SESSION["userId"])->getGroups() as $group){
            $item = new stdClass();
            $item->groupName = $group->getName();
            $item->groupId = $group->getId();
            $res[] = $item;
        }

        if(empty($res)){
            return ["success" => true, "noGroups" => true];
        }

        $res["success"] = true;
        $res["noGroups"] = false;
        return $res;
    }

    /**
     * method: registerTeacher
     * first_name: string 
     * last_name: string 
     * username: string 
     * password: string
     * @return array
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

        
        if (!$this->checkUserNameAvailable($username)["userNameAvailable"]) {
            return ["success" => false, "userNameUnavailable" => true];
        }

        // --- End of form validation ---
        
        $user = new User($this->hub);
        $user_type = 1; // = teacher
        
        $user->registerUser($username, $password, $first_name, $last_name, $user_type);
        $_SESSION['userId'] = $user->getUserId();

        return ["success" => true, "msg" => "User successfully created!"];
    }

    /**
     * method: getLoginStatus
     * @return array
     */
    public function getLoginStatus(): array {
        
        if(empty($_SESSION['userId'])){
            return ["isLoggedIn" => false];
        }

        $user = $this->getById($_SESSION['userId']);

        $res["isLoggedIn"] = true;
        $res["username"] = $user->getUsername();
        $res["userId"] = $user->getUserId();
        $res["userType"] = $user->getUserType();
        return $res;
    }

    /**
     * method: checkUserNameAvailable
     * user: string
     * @return array|null
     */
    public function checkUserNameAvailable() : ?array {
        if (empty($_POST["user"])) {
            return null;
        }

        if((new User($this->hub))->initializeByUserName($_POST["user"])){
            return ["userNameAvailable" => false];
        }

        return ["userNameAvailable" => true];
    }
}