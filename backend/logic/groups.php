<?php

class Groups {

    /**
     * creates new group and adds currently logged in user to group
     * 
     * method: createGroup
     * group_name: string
     * @return array|null
     */
    public function createGroup(): ?array {

        if(empty($_POST["group_name"])){
            throw new Exception("Invalid Parameters");
        }

        Permissions::checkIsTeacher();

        $group = Hub::Group();
        $group->storeNewGroup($_POST["group_name"]);
        $group->addMember(Hub::User($_SESSION["userId"]));

        return ["success" => true, "newGroupId" => $group->getId()];
    }

    /**
     * method: getGroupName
     * group_id: int
     * @return array|null
     */
    public function getGroupName(): ?array {
        if(empty($_POST["group_id"])){
            throw new Exception("Invalid Parameters");
        }

        $group = Hub::Group($_POST["group_id"]);
        Permissions::checkIsInGroup($group);

        return ["success" => true, "groupName" => $group->getName()];
    }

    /**
     * method: getGroupTeacher
     * group_id: int
     * @return array|null
     */
    public function getGroupTeacher(): ?array {
        if(empty($_POST["group_id"])){
            throw new Exception("Invalid Parameters");
        }

        $group = Hub::Group($_POST["group_id"]);
        Permissions::checkIsInGroup($group);

        $teacher = $group->getTeacher();
        return ["success" => true, "teacherFirstName" => $teacher->getFirstName(), "teacherLastName" => $teacher->getLastName()];
    }

      /**
     * method: getGroupAssignments
     * group_id: int
     * @return array|null
     */
    public function getGroupAssignments(): ?array {
        if(empty($_POST["group_id"])){
            throw new Exception("Invalid Parameters");
        }

        $group = Hub::Group($_POST["group_id"]);
        Permissions::checkIsInGroup($group);

        $groupAssignments = [];
        foreach($group->getAssignments() as $assignment){
            $res = $assignment->getBaseData();
            $res["assignmentId"] = $assignment->getId();
            $groupAssignments[] = $res;
        }
        return ["success" => true, "groupAssignments" => $groupAssignments];
    }


    /**
     * method: getGroupMembers
     * group_id: int
     * @return array|null
     */
    public function getGroupMembers(): ?array {
        if(empty($_POST["group_id"])){
            throw new Exception("Invalid Parameters");
        }

        $group = Hub::Group($_POST["group_id"]);
        Permissions::checkIsInGroup($group);

        $groupMembers = [];
        foreach($group->getMembers() as $member){
            $groupMembers[] = $member->getBaseData();
        }

        return ["success" => true, "groupMembers" => $groupMembers];
    }

    /**
     * method: getGroupChatId
     * group_id: int
     * @return array|null
     */
    public function getGroupChatId(): ?array {
        if(empty($_POST["group_id"])){
            throw new Exception("Invalid Parameters");
        }

        $group = Hub::Group($_POST["group_id"]);
        Permissions::checkIsInGroup($group);

        return ["success" => true, "groupChatId" => $group->getChat()->getChatId()];
    }

    /**
     * a user can only be assigned to a group if the user
     * is allready in another group with the teacher
     * 
     * method: assignUserToGroup
     * user_id: int
     * group_id: int
     * @return array|null
     */
    public function assignUserToGroup() {
        if(empty($_POST["group_id"]) || empty($_POST["user_id"])){
            throw new Exception("Invalid Parameters");
        }
        
        $group = Hub::Group($_POST["group_id"]);
        $user = Hub::User($_POST["user_id"]);

        Permissions::checkCanAssignUserToGroup($user, $group);
        
        $group->addMember($user);
        return ["success" => true];
    }
}


