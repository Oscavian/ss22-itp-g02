<?php

require "hub.php";
require "db/database.php";
require "permissions/permissions.php";

class MainLogic {

    public static function handleRequest($method): ?array {

        self::sanitizePostArray();

        switch ($method) {
            /* USERS */
            case "login":            
                return Hub::Users()->login();
            case "logout":
                return Hub::Users()->logout();
            case "getLoginStatus":
                return Hub::Users()->getLoginStatus();
            case "checkUserNameAvailable":
                return Hub::Users()->isUserNameAvailable();
            case "registerTeacher":
                return Hub::Users()->registerTeacher();
            case "registerStudents":
                return Hub::Users()->registerStudents();
            case "getUserGroups":
                return Hub::Users()->getUserGroups();
            case "updateUserData":
                return Hub::Users()->updateUserData();
            case "updateUserPassword":
                return Hub::Users()->updateUserPassword();

            /* GROUPS */
            case "createGroup":
                return Hub::Groups()->createGroup();
            case "getGroupName":
                return Hub::Groups()->getGroupName();
            case "getGroupChatId":
                return Hub::Groups()->getGroupChatId();
            case "getStudentsOfGroup":
                return Hub::Groups()->getGroupMembers();

            /* ASSIGNMENTS */
            case "getAssignmentById":
                return Hub::Assignments()->getAssignmentById();
            case "createAssignment":
                return Hub::Assignments()->createAssignment();
            case "uploadAssignments":
                break;

            /* CHATS */
            case "getMessages":
                break;
            case "sendMessage":
                break;
            default:
                return null;
        }
        return null;
    }

    private static function sanitizePostArray(){
        foreach ($_POST as $key => $value){
            // we dont need htmlspecialchars for an api
            // furthermore, it prevents us from posting json strings as payload
            $_POST[$key] = trim(stripslashes($value));
        }
    }
}