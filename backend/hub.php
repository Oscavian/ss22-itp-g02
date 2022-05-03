<?php

class Hub {
    
    //-------------------Logic Classes--------------------

    private static $assignments;
    private static $chats;
    private static $groups;
    private static $users;

    public static function Chats(): Chats {
        if (self::$chats == null) {
            require_once "logic/chats.php";
            self::$chats = new Chats();
        }
        return self::$chats;
    }
    
    public static function Assignments(): Assignments {
        if (self::$assignments == null) {
            require_once "logic/assignments.php";
            self::$assignments = new Assignments();
        }
        return self::$assignments;
    }
    
    public static function Users(): Users {
        if (self::$users == null) {
            require_once "logic/users.php";
            self::$users = new Users();
        }
        return self::$users;
    }
    
    public static function Groups(): Groups {
        if (self::$groups == null) {
            require_once "logic/groups.php";
            self::$groups = new Groups();
        }
        return self::$groups;
    }


    //------------------Model Classes---------------------

    public static function Chat($id = null): Chat {
        require_once "models/chat.php";
        return new Chat($id);
    }
    
    public static function Assignment($id = null): Assignment {
        require_once "models/assignment.php";
        return new Assignment($id);
    }
    
    public static function User($id = null): User {
        require_once "models/user.php";
        return new User($id);
    }
    
    public static function Group($id = null): Group {
        require_once "models/group.php";
        return new Group($id);
    }
}