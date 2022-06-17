<?php

class Users {

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

        $user = Hub::User();
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

        $user = Hub::User($_SESSION["userId"]);

        if(!$user->exists()){
            Hub::Users()->logout();
            throw new Exception("The currently logged in user with Id " . $_SESSION["userId"] . " does not exist in the database!");
        }

        $res["isLoggedIn"] = true;
        $res["username"] = $user->getUsername();
        $res["firstName"] = $user->getFirstName();
        $res["lastName"] = $user->getLastName();
        $res["userId"] = $user->getId();
        $res["userType"] = $user->getUserType();
        return $res;
    }
    
    /**
     * method: getUserGroups
     * @return array
     */
    public function getUserGroups(): array {

        Permissions::checkIsLoggedIn();
        
        $resultGroups = [];
        foreach ((Hub::User($_SESSION["userId"]))->getGroups() as $group){
            $item["groupName"] = $group->getName();
            $item["groupId"] = $group->getId();
            $item["groupChatId"] = $group->getChat();

            $teacher = $group->getTeacher();
            $item["teacherFirstName"] = $teacher->getFirstName();
            $item["teacherLastName"] = $teacher->getLastName();

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
        // --- Backend form-validation ---
        if (!preg_match("/^[äöüÄÖÜßa-zA-Z-' ]*$/", $first_name) || strlen($first_name) > 50) {
            throw new Exception("Invalid fist_name");
        }

        if (!preg_match("/^[äöüÄÖÜßa-zA-Z-' ]*$/", $last_name) || strlen($last_name) > 50) {
            throw new Exception("Invalid last_name");
        }

        if (strlen($username) < 6 || strlen($username) > 50) {
            throw new Exception("Invalid username");
        }

        if (strlen($password) < 6) {
            throw new Exception("Invalid password");
        }

        if ($this->isUsernameNameTaken($username)) {
            throw new Exception("Username is unavailable");
        }

        // --- End of form validation ---
        
        $user = Hub::User();
        $user_type = 1; // = teacher
        
        $user->storeNewUser($username, $password, $first_name, $last_name, $user_type);
        $_SESSION['userId'] = $user->getId();

        return ["success" => true, "msg" => "User successfully created!"];
    }


    /**
     * method: registerStudents
     * students: array: [
     *                      {
     *                          "first_name": "...",
     *                          "last_name": "...",
     *                      }
     *                  ]
     * group_id: int
     * @return array
     * @throws Exception
     */
    public function registerStudents(): array {
        if (empty($_POST["students"]) || empty($_POST["group_id"])){
            throw new Exception("Invalid Parameters!");
        }
     
        $group = Hub::Group($_POST["group_id"]);
        
        Permissions::checkIsTeacher();
        Permissions::checkIsInGroup($group);

        $students = json_decode($_POST["students"]);

        //Payload validation
        foreach ($students as $student) {
            if (empty($student->first_name) ||
                empty($student->last_name)) {
                throw new Exception("Invalid Payload!");
            }

            if (!preg_match("/^[äöüÄÖÜßa-zA-Z-' ]*$/", $student->first_name) || strlen($student->first_name) > 50) {
                throw new Exception("Invalid payload: " . $student->first_name);
            }

            if (!preg_match("/^[äöüÄÖÜßa-zA-Z-' ]*$/", $student->last_name) || strlen($student->last_name) > 50) {
                throw new Exception("Invalid payload: " . $student->last_name);
            }
        }

        $new_students_data = [];
        //create student account data
        foreach ($students as $student) {
            $student_data = new stdClass();
            if (strlen($student->last_name) > 12){
                $username = strtolower($student->last_name . "." .  substr($student->first_name, 0, 1));
            } else {
                $username = strtolower($student->last_name . "." .  $student->first_name);
            }

            $length = 10;
            $password = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1, $length);

            $student_data->username = $username;
            $student_data->password = $password;
            $student_data->first_name = $student->first_name;
            $student_data->last_name = $student->last_name;

            $new_students_data[] = $student_data;
        }

        //create accounts
        foreach ($new_students_data as $key => $student_data){
            
            do{
                $student_data->username .= random_int(100, 999);
            } while($this->isUsernameNameTaken($student_data->username));
            
            $user = Hub::User();
            $user->storeNewUser($student_data->username, $student_data->password, $student_data->first_name, $student_data->last_name, 2);
            $group->addMember($user);
            $student_data->user_id = $user->getId();
        }

        return $new_students_data;
    }

    /**
     * method: updateUserData
     * type: "username" | "firstName" | "lastName"
     * data: string
     * @return array|null
     */
    public function updateUserData() : ?array {
        if (empty($_POST["type"]) || empty($_POST["data"])) {
            throw new Exception("Invalid Parameters");
        }

        Permissions::checkIsLoggedIn();

        if($_POST["type"] == "username"){
            $username = $_POST["data"];
            if(Hub::User()->initializeByUserName($username)){
                throw new Exception("Username unavailable!");
            }

            if (strlen($username) < 6 || strlen($username) > 50) {
                throw new Exception("Invalid username");
            }

            Hub::User($_SESSION["userId"])->storeUpdateUserData("username", $username);
            return ["success" => true];
        }

        if($_POST["type"] == "firstName"){
            $first_name = $_POST["data"];

            if (!preg_match("/^[äöüÄÖÜßa-zA-Z-' ]*$/", $first_name) || strlen($first_name) > 50) {
                throw new Exception("Invalid fist_name");
            }

            Hub::User($_SESSION["userId"])->storeUpdateUserData("firstName", $_POST["data"]);
            return ["success" => true];
        }

        if($_POST["type"] == "lastName"){
            $last_name = $_POST["data"];

            if (!preg_match("/^[äöüÄÖÜßa-zA-Z-' ]*$/", $last_name) || strlen($last_name) > 50) {
                throw new Exception("Invalid last_name");
            }

            Hub::User($_SESSION["userId"])->storeUpdateUserData("lastName", $_POST["data"]);
            return ["success" => true];
        }

        throw new Exception("Invalid Type");
    }

    /**
     * method: updateUserPassword
     * old_password: string
     * new_password: string
     * @return array|null
     */
    public function updateUserPassword() : ?array {
        if (empty($_POST["old_password"]) || empty($_POST["new_password"])) {
            throw new Exception("Invalid Parameters");
        }

        Permissions::checkIsLoggedIn();
       
        $newPassword = $_POST["new_password"];

        if (strlen($newPassword) < 6) {
            throw new Exception("New password is invalid");
        }

        $oldPassword = $_POST["old_password"];
        $username = Hub::User($_SESSION["userId"])->getUsername();

        $user = Hub::User();
        if ($user->verifyLogin($username, $oldPassword)){
            $user->changePassword($newPassword);
            return ["success" => true];
        }

        return ["success" => false];
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

        if(Hub::User()->initializeByUserName($_POST["user"])){
            return ["success" => true, "userNameAvailable" => false];
        }

        return ["success" => true, "userNameAvailable" => true];
    }

    /**
     * helper method for boolean question if a username is already taken
     * @param string $username
     * @return bool
     */
    public function isUsernameNameTaken(string $username) : bool {
        if (Hub::User()->initializeByUserName($username)){
            return true;
        }
        return false;
    }

    /**
     * generates new password for resetting student password
     * 
     * method: generateNewStudentPassword
     * @return array|null
     */
    public function generateNewStudentPassword(){     
        $length = 10;
        $generatedPassword = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1, $length);

        return ["success" => true, "generatedPassword" => $generatedPassword];
    }

    /**
     * method: setNewStudentPassword
     * new_password: string
     * user_id: int //student user id
     * @return array|null
     */
    public function setNewStudentPassword(){
        if (empty($_POST["user_id"]) || empty($_POST["new_password"])) {
            throw new Exception("Invalid Parameters");
        }

        Permissions::checkIsTeacher();
        
        $teacher = Hub::User($_SESSION['userId']);
        $student = Hub::User($_POST["user_id"]);
        
        Permissions::checkIsInGroupWith($teacher, $student);

        if (strlen($_POST["new_password"]) < 6) {
            throw new Exception("Invalid password");
        }

        $student->changePassword($_POST["new_password"]);

        return ["success" => true];
    }
}