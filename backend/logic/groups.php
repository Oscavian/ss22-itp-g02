<?php

class Groups {
    private $db;
    private $hub;
    private $users;

    public function __construct(Hub $hub){
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
        $this->users = $this->hub->getUsers();
    }

    public function createGroup(): ?array {

        if(empty($_POST["groupName"])){
            return null;
        }

        if(empty($_SESSION['username']) && $_SESSION['userType'] == 1){ //cecks wheter user is logged in and is teacher
            $res["success"] = false;
            $res["noPermission"] = true;
            return $res;
        }

        $newGroupId = $this->db->createGroup($_POST["groupName"]);

        $this->db->assignUserGroup($newGroupId, $_SESSION['userId']);

        $res["success"] = true;
        $res["newGroupId"] = $newGroupId;
        return $res;
    }

    public function getUserGroups(): array {

        if(empty($_SESSION['username'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        $res = $this->db->getUserGroups($_SESSION['userId']);

        if(empty($res)){
            $res["success"] = true;
            $res["noGroups"] = true;
            return $res;
        }
        return $res;
    }

    public function getGroupName(): ?array {
        if(empty($_POST["groupId"])){
            return null;
        }

        if(empty($_SESSION['username'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        if($this->db->userIsInGroup($_SESSION['userId'], $_POST["groupId"])){
            $res["success"] = true;
            $res["groupName"] = $this->db->getGroupName($_POST["groupId"]);
            return $res;
        }

        $res["success"] = false;
        $res["userNotInGroup"] = true;
        return $res;
    }

    public function getGroupChatId(): ?array {
        if(empty($_POST["groupId"])){
            return null;
        }

        if(empty($_SESSION['username'])){ //cecks wheter user is logged in
            $res["success"] = false;
            $res["notLoggedIn"] = true;
            return $res;
        }

        if($this->db->userIsInGroup($_SESSION['userId'], $_POST["groupId"])){
            $res["success"] = true;
            $res["groupChatId"] = $this->db->getGroupChatId($_POST["groupId"]);
            return $res;
        }

        $res["success"] = false;
        $res["userNotInGroup"] = true;
        return $res;
    }
}