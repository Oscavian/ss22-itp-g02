<?php
require_once "models/group.php";

class Groups {
    private $db;
    private $hub;

    public function __construct(Hub $hub){
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
    }

    public function getById(int $id): Group {
        return new Group($this->hub, $id);
    }

    public function exists(int $id) : bool {
        if ($this->db->select("SELECT * from group where pk_group_id=?", [$id], "i", true) == null){
            return false;
        } else {
            return true;
        }
    }

    /**
     * method: create group
     * groupName: string
     * @return array|null
     */
    public function createGroup(): ?array {

        if(empty($_POST["groupName"])){
            return null;
        }

        //TODO: centralise perm check
        if(!isset($_SESSION['userId'])){ //cecks wheter user is logged in and is teacher
            return ["success" => false, "noPermission" => true, "msg" => "Not logged in!"];
        }
        if ($_SESSION['userType'] != 1){
            return ["success" => false, "noPermission" => true];
        }

        //create chat for group
        //TODO: create chat with method of chat class
        $newChatId = $this->db->insert("INSERT INTO chat (name) VALUES (?)", [$_POST["groupName"]], "s");

        //create new group
        $newGroupId = $this->db->insert("INSERT INTO `groups` (name, fk_chat_id) VALUES (?, ?)", [$_POST["groupName"], $newChatId], "si");
        $this->db->insert("INSERT INTO user_group (fk_group_id, fk_user_id) VALUES (?, ?)", [$newGroupId, $_SESSION["userId"]], "ii");

        return ["success" => true, "newGroupId" => $newGroupId];
    }

    /**
     * method: getGroupName
     * groupId: int
     * @return array|null
     */
    public function getGroupName(): ?array {
        if(empty($_POST["groupId"])){
            return null;
        }

        //TODO: centralise perm check
        if(empty($_SESSION['userId'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        $group = $this->getById($_POST["groupId"]);

        //TODO: centralise perm check
        if($group->isMember($this->hub->getUsers()->getById($_SESSION["userId"]))){
            $res["success"] = true;
            $res["groupName"] = $group->getName();
            return $res;
        }

        $res["success"] = false;
        $res["userNotInGroup"] = true;
        return $res;
    }

    /**
     * method: getGroupChatId
     * groupId: int
     * @return array|null
     */
    public function getGroupChatId(): ?array {
        if(empty($_POST["groupId"])){
            return null;
        }

        //TODO: centralise perm check
        if(empty($_SESSION['userId'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        $group = $this->getById($_POST["groupId"]);

        //TODO: centralise perm check
        if($group->isMember($this->hub->getUsers()->getById($_SESSION["userId"]))){
            $res["success"] = true;
            $res["groupChatId"] = $group->getChat()->getChatId();
            return $res;
        }

        $res["success"] = false;
        $res["userNotInGroup"] = true;
        return $res;
    }

    /**
     * method: assignUserToGroup
     * userId
     * groupId
     * @return array|null
     */
    public function assignUserToGroup() {
        if(empty($_POST["groupId"]) || empty($_POST["userId"])){
            return null;
        }
        
        //TODO: Permission check - who can add who to a group?

        $groupId = $_POST["groupId"];
        $userId = $_POST["userId"];

        if(!$this->exists($groupId)){
            return ["success" => false, "msg" => "Group with ID $groupId does not exist!", "inputInvalid" => true];
        }

        if(!$this->hub->getUsers()->exists($userId)){
            return ["success" => false, "msg" => "User with ID $userId does not exist!", "inputInvalid" => true];
        }
        
        $group = $this->getById($groupId);
        $user = $this->hub->getUsers()->getById($userId);
        $group->addMember($user);

        return ["success" => true];
    }
}