<?php

class Permissions {
    private $db;
    private $hub;

    public function __construct(Hub $hub){
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
    }

    public function checkIsLoggedIn() {      
        if(empty($_SESSION['userId'])){
            throw new Exception("No Permission - Not logged in!");
        }
    }

    public function checkIsTeacher() {
        $this->checkIsLoggedIn();
        
        $user = $this->hub->getUsers()->getLoggedInUser();
        if($user->getUserType() != 1){
            throw new Exception("No Permission - You need to be a teacher!");
        }
    }

    public function checkIsStudent() {
        $this->checkIsLoggedIn();
        
        $user = $this->hub->getUsers()->getLoggedInUser();
        if($user->getUserType() != 2){
            throw new Exception("No Permission - You need to be a student!");
        }
    }

    public function checkIsInGroup(Group $group) {
        $this->checkIsLoggedIn();

        if(!$group->exists()){
            throw new Exception("The group with the requested ID does not exist!");
        }

        $user = $this->hub->getUsers()->getLoggedInUser();
        if(!$user->isInGroup($group)){
            throw new Exception("No Permission - You're not in the group!");
        }
    }

    public function checkCanAccessAssignment(Assignment $assignment) {
        $this->checkIsLoggedIn();

        if (!$assignment->exists()){
            throw new Exception("The assignment with the requested ID does not exist!");
        }

        $group = $this->hub->getGroups()->getById($assignment->getGroupId());
        $this->checkIsInGroup($group);
    }

    public function checkCanAssignUserToGroup(User $otherUser, Group $group) {
        $this->checkIsTeacher();

        if(!$group->exists()){
            throw new Exception("The group with the requested ID does not exist!");
        }
        
        if(!$otherUser->exists()){
            throw new Exception("The user with the requested ID does not exist!");
        }

        if($otherUser->isInGroup($group)){ //checks if user is allready in group
            throw new Exception("The user you're trying to add is allready in the group!");
        }

        $user = $this->hub->getUsers()->getLoggedInUser();
        if(!$user->isInGroupWith($otherUser)){ //checks if teacher is in any group with the user
            throw new Exception("No Permission - You're not in any group with this user!");
        }
    }
}