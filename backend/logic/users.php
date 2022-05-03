<?php
require_once "models/user.php";

class Users {
    private $hub;

    public function __construct(Hub $hub){
        $this->hub = $hub;
    }

    public function getById($user_id): User {
        return new User($this->hub, $user_id);
    }

    public function getLoggedInUser(): User {
        return new User($this->hub, $_SESSION["userId"]);
    }

    /**
     * method: login
     * user: string
     * password: string
     * @return array|null
     */
    public function login(): ?array {
        session_unset();
        if (empty($_POST["user"]) || empty($_POST["password"])) {
            throw new Exception("Invalid Parameters");
        }

        $user = new User($this->hub);
        if ($user->verifyLogin($_POST["user"], $_POST["password"])){
            $_SESSION['userId'] = $user->getId();
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
     * method: getLoginStatus
     * @return array
     */
    public function getLoginStatus(): array {
        
        if(empty($_SESSION["userId"])){
            return ["isLoggedIn" => false];
        }

        $user = $this->getLoggedInUser();

        if(!$user->exists()){
            throw new Exception("The currently logged in user with Id " . $_SESSION["userId"] . " does not exist in the database!");
        }

        $res["isLoggedIn"] = true;
        $res["username"] = $user->getUsername();
        $res["userId"] = $user->getId();
        $res["userType"] = $user->getUserType();
        return $res;
    }
    
    /**
     * method: getUserGroups
     * @return array
     */
    public function getUserGroups(): array {

        $this->hub->getPermissions()->checkIsLoggedIn();
        
        $resultGroups = [];
        foreach ($this->getLoggedInUser()->getGroups() as $group){
            $item["groupName"] = $group->getName();
            $item["groupId"] = $group->getId();
            $resultGroups[] = $item;
        }
        
        if(empty($resultGroups)){
            return ["success" => true, "noGroups" => true];
        }

        $res["groups"] = $resultGroups;
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
            throw new Exception("Invalid Parameters");
        }

        $username = $_POST["user"];
        $password = $_POST["password"];
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];


        $dataOk = true;
        $res = ["msg" => ""];
        // --- Backend form-validation ---
        if (!preg_match("/^[a-zA-Z-' ]*$/", $first_name) || strlen($first_name) > 50) {
            throw new Exception("Invalid fist_name");
        }

        if (!preg_match("/^[a-zA-Z-' ]*$/", $last_name) || strlen($last_name) > 50) {
            throw new Exception("Invalid last_name");
        }

        if (strlen($username) < 6 || strlen($username) > 50) {
            throw new Exception("Invalid username");
        }

        if (strlen($password) < 6) {
            throw new Exception("Invalid password");
        }

        if (!$this->isUserNameAvailable($username)["userNameAvailable"]) {
            throw new Exception("Username is unavailable");
        }

        // --- End of form validation ---
        
        $user = new User($this->hub);
        $user_type = 1; // = teacher
        
        $user->storeNewUser($username, $password, $first_name, $last_name, $user_type);
        $_SESSION['userId'] = $user->getId();

        return ["success" => true, "msg" => "User successfully created!"];
    }

    /**
     * method: checkUserNameAvailable
     * user: string
     * @return array|null
     */
    public function isUserNameAvailable() : ?array {
        if (empty($_POST["user"])) {
            throw new Exception("Invalid Parameters");
        }

        if((new User($this->hub))->initializeByUserName($_POST["user"])){
            return ["success" => true, "userNameAvailable" => false];
        }

        return ["success" => true, "userNameAvailable" => true];
    }
}