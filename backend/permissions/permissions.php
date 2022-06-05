<?php

class Permissions {

    public static function checkIsLoggedIn() {      
        if(empty($_SESSION['userId'])){
            throw new Exception("No Permission - Not logged in!");
        }
    }

    public static function checkIsTeacher() {
        self::checkIsLoggedIn();
        
        $user = Hub::User($_SESSION['userId']);
        if($user->getUserType() != 1){
            throw new Exception("No Permission - You need to be a teacher!");
        }
    }

    public static function checkIsStudent() {
        self::checkIsLoggedIn();
        
        $user = Hub::User($_SESSION['userId']);
        if($user->getUserType() != 2){
            throw new Exception("No Permission - You need to be a student!");
        }
    }

    public static function checkIsInGroup(Group $group) {
        self::checkIsLoggedIn();

        if(!$group->exists()){
            throw new Exception("The group with the requested ID does not exist!");
        }

        $user = Hub::User($_SESSION['userId']);
        if(!$user->isInGroup($group)){
            throw new Exception("No Permission - You're not in the group!");
        }
    }

    public static function checkCanAccessAssignment(Assignment $assignment) {
        self::checkIsLoggedIn();

        if (!$assignment->exists()){
            throw new Exception("The assignment with the requested ID does not exist!");
        }

        $group = Hub::Group($assignment->getGroupId());
        self::checkIsInGroup($group);
    }

    public static function checkCanAssignUserToGroup(User $otherUser, Group $group) {
        self::checkIsTeacher();

        if(!$group->exists()){
            throw new Exception("The group with the requested ID does not exist!");
        }
        
        if(!$otherUser->exists()){
            throw new Exception("The user with the requested ID does not exist!");
        }

        if($otherUser->isInGroup($group)){ //checks if user is allready in group
            throw new Exception("The user you're trying to add is allready in the group!");
        }

        $user = Hub::User($_SESSION['userId']);
        if(!$user->isInGroupWith($otherUser)){ //checks if teacher is in any group with the user
            throw new Exception("No Permission - You're not in any group with this user!");
        }
    }

    //checks if user is in any group with the other user
    public static function checkIsInGroupWith(User $user, User $otherUser) {

        if(!$user->exists()){
            throw new Exception("The user with the requested ID does not exist!");
        }

        if(!$otherUser->exists()){
            throw new Exception("The user with the requested ID does not exist!");
        }

        if(!$user->isInGroupWith($otherUser)){
            throw new Exception("No Permission - You're not in any group with this user!");
        }
    }
}