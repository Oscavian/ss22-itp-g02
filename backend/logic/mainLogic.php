<?php

require "hub.php";
require "db/database.php";
require "permissions/permissions.php";

class MainLogic {

    /**
     * Handles POST requests
     * @param $method
     * @return array|null
     * @throws Exception
     */
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
            case "getGroupTeacher":
                return Hub::Groups()->getGroupTeacher();
            case "getGroupChatId":
                return Hub::Groups()->getGroupChatId();
            case "getGroupMembers":
                return Hub::Groups()->getGroupMembers();
            case "getGroupAssignments":
                return Hub::Groups()->getGroupAssignments();

            /* ASSIGNMENTS */
            case "getAssignmentById":
                return Hub::Assignments()->getAssignmentById();
            case "downloadAssignmentFile":
                Hub::Assignments()->downloadAssignmentFile();
                break;
            case "createAssignment":
                return Hub::Assignments()->createAssignment();
            case "getSubmissions":
                return Hub::Assignments()->getSubmissions();
            case "downloadSubmissionFile":
                Hub::Assignments()->downloadSubmissionFile();
                break;
            case "addSubmission":
                return Hub::Assignments()->addSubmission();

            /* CHATS */
            case "getMessages":
                return Hub::Chats()->getMessages();
            case "sendMessage":
                return Hub::Chats()->sendMessage();
            case "deleteMessage":
                return Hub::Chats()->deleteMessage();
            default:
                return null;
        }
        return null;
    }

    private static function sanitizePostArray() {
        foreach ($_POST as $key => $value) {
            // we dont need htmlspecialchars for an api
            // furthermore, it prevents us from posting json strings as payload
            $_POST[$key] = trim(stripslashes($value));
        }
    }
}