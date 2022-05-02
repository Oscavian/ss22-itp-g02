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

        if(!$this->hub->getPermissions()->isTeacher()){ //cecks wheter user is logged in and is teacher
            return ["success" => false, "noPermission" => true];
        }

        $group = new Group($this->hub);
        $group->storeNewGroup($_POST["groupName"]);
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

        $group = $this->getById($_POST["groupId"]);

        if(!$group->exists()){
            return ["success" => false, "msg" => "Group with ID" .  $_POST["groupId"] . "does not exist!"];
        }

        if(!$this->hub->getPermissions()->isInGroup($group)){
            return ["success" => false, "userNotInGroup" => true];
        }

        return ["success" => true, "groupName" => $group->getName()];
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

        $group = $this->getById($_POST["groupId"]);

        if(!$group->exists()){
            return ["success" => false, "msg" => "Group with ID" .  $_POST["groupId"] . "does not exist!"];
        }

        if(!$this->hub->getPermissions()->isInGroup($group)){
            return ["success" => false, "userNotInGroup" => true];
        }

        return ["success" => true, "groupChatId" => $group->getChat()->getChatId()];
    }

    /**
     * a user can only be assigned to a group if the user
     * is allready in another group with the teacher
     * 
     * method: assignUserToGroup
     * userId
     * groupId
     * @return array|null
     */
    public function assignUserToGroup() {
        if(empty($_POST["groupId"]) || empty($_POST["userId"])){
            return null;
        }
        
        $group = $this->getById($_POST["groupId"]);
        $user = $this->hub->getUsers()->getById($_POST["userId"]);

        if(!$group->exists()){
            return ["success" => false, "msg" => "Group with ID" .  $_POST["groupId"] . "does not exist!", "inputInvalid" => true];
        }

        if(!$user->exists()){
            return ["success" => false, "msg" => "User with ID" . $_POST["userId"] . "does not exist!", "inputInvalid" => true];
        }

        if(!$this->hub->getPermissions()->canAssignUserToGroup($user, $group)){
            return ["success" => false, "noPermission" => true];
        }
        
        $group->addMember($user);
        return ["success" => true];
    }
}


