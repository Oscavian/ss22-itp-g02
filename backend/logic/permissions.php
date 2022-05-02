<?php

class Permissions {
    private $db;
    private $hub;

    public function __construct(Hub $hub){
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
    }


    public function isLoggedIn(): bool {      
        if(empty($_SESSION['userId'])){
            return false;
        }
        return true;
    }

    public function isTeacher(): bool {
        if(!$this->isLoggedIn()){
            return false;
        }
        
        $user = $this->hub->getUsers()->getLoggedInUser();
        if($user->getUserType() == 1){
            return true;
        }
        return false;
    }

    public function isStudent(): bool {
        if(!$this->isLoggedIn()){
            return false;
        }
        
        $user = $this->hub->getUsers()->getLoggedInUser();
        if($user->getUserType() == 1){
            return true;
        }
        return false;
    }

    public function isInGroup(Group $group): bool {
        if(!$this->isLoggedIn()){
            return false;
        }

        $user = $this->hub->getUsers()->getLoggedInUser();
        return $user->isInGroup($group);
    }

    public function canAccessAssignment(Assignment $assignment): bool {
        if(!$this->isLoggedIn()){
            return false;
        }

        $group = $this->hub->getGroups()->getById($assignment->getGroupId());
        $user = $this->hub->getUsers()->getLoggedInUser();
        return $user->isInGroup($group);
    }


    public function canAssignUserToGroup(User $otherUser, Group $group): bool {
        if(!$this->isTeacher()){
            return false;
        }

        if($otherUser->isInGroup($group)){ //checks if user is allready in group
            return false;
        }

        $user = $this->hub->getUsers()->getLoggedInUser();
        return $user->isInGroupWith($otherUser); //checks if teacher is in a group with user
    }



}