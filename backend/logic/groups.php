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
        if(!isset($_SESSION['username']) && !isset($_SESSION['userType'])){ //cecks wheter user is logged in and is teacher
            return ["success" => false, "noPermission" => true, "msg" => "Not logged in!"];
        }
        if ($_SESSION['userType'] != 1){
            return ["success" => false, "noPermission" => true];
        }

        //create chat for group
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

        if(empty($_SESSION['username'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        $group = $this->getById($_POST["groupId"]);

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

        if(empty($_SESSION['username'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        $group = $this->getById($_POST["groupId"]);

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
     * @return void
     */
    public function assignUserToGroup() {
        //TODO
    }
}