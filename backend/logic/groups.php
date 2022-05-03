<?php

class Groups {

    /**
     * creates new group and adds currently logged in user to group
     * 
     * method: create group
     * groupName: string
     * @return array|null
     */
    public function createGroup(): ?array {

        if(empty($_POST["groupName"])){
            throw new Exception("Invalid Parameters");
        }

        Permissions::checkIsTeacher();

        $group = Hub::Group();
        $group->storeNewGroup($_POST["groupName"]);
        $group->addMember(Hub::User($_SESSION["userId"]));

        return ["success" => true, "newGroupId" => $group->getId()];
    }

    /**
     * method: getGroupName
     * groupId: int
     * @return array|null
     */
    public function getGroupName(): ?array {
        if(empty($_POST["groupId"])){
            throw new Exception("Invalid Parameters");
        }

        $group = Hub::Group($_POST["groupId"]);
        Permissions::checkIsInGroup($group);

        return ["success" => true, "groupName" => $group->getName()];
    }

    /**
     * method: getGroupChatId
     * groupId: int
     * @return array|null
     */
    public function getGroupChatId(): ?array {
        if(empty($_POST["groupId"])){
            throw new Exception("Invalid Parameters");
        }

        $group = Hub::Group($_POST["groupId"]);
        Permissions::checkIsInGroup($group);

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
            throw new Exception("Invalid Parameters");
        }
        
        $group = Hub::Group($_POST["groupId"]);
        $user = Hub::User($_POST["userId"]);

        Permissions::checkCanAssignUserToGroup($user, $group);
        
        $group->addMember($user);
        return ["success" => true];
    }
}


