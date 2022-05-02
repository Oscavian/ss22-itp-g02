<?php
require_once "models/group.php";

class Groups {
    private $hub;

    public function __construct(Hub $hub){
        $this->hub = $hub;
    }

    public function getById(int $id): Group {
        return new Group($this->hub, $id);
    }

    /**
     * creates new group and adds currently logged in user to group
     * 
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

        //TODO: centralize perm check
        // if ($_SESSION['userType'] != 1){
        //     return ["success" => false, "noPermission" => true];
        // }

        $group = new Group($this->hub);
        $group->createGroup($_POST["groupName"]);
        $group->addMember($this->hub->getUsers()->getLoggedInUser());

        return ["success" => true, "newGroupId" => $group->getId()];
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

        $group = $this->getById($_POST["groupId"]);
        $user = $this->hub->getUsers()->getById($_POST["userId"]);

        if(!$group->exists()){
            return ["success" => false, "msg" => "Group with ID" .  $_POST["groupId"] . "does not exist!", "inputInvalid" => true];
        }

        if(!$user->exists()){
            return ["success" => false, "msg" => "User with ID" . $_POST["userId"] . "does not exist!", "inputInvalid" => true];
        }
        
        $group->addMember($user);
        return ["success" => true];
    }
}


