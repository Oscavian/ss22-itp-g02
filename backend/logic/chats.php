<?php

class Chats {

    /**
     * fetches the messages for a given chat
     *
     * method: getMessages
     * chat_id: int
     * group_id: int //either chat_id or group_id must be provided
     * offset: int //offsets loaded messages by 20, set offset to 0 to get latest messages
     * @return array
     * @throws Exception
     */
    public function getMessages(): array {        
        if ((empty($_POST["chat_id"]) && empty($_POST["group_id"])) || !isset($_POST["offset"])){
            throw new Exception("Invalid Parameters!");
        }

        if(empty($_POST["chat_id"])){
            $group = Hub::Group($_POST["group_id"]);
            if(!$group->exists()){
                throw new Exception("The group with the requested id could not be found!");
            }
            $chat = $group->getChat();
        } else {
            $chat = Hub::Chat($_POST["chat_id"]);
        }
        
        if(!$chat->exists()){
            throw new Exception("The chat with the requested id could not be found!");
        }

        Permissions::checkIsInGroup($chat->getGroup());

        return $chat->getMessages($_POST["offset"]);
    }

    /**
     * receives and stores a new new message to a given chat
     *
     * method: sendMessage
     * chat_id: int
     * group_id: int //either chat_id or group_id must be provided
     * text: string
     * @return array
     * @throws Exception
     */
    public function sendMessage() {
        if ((empty($_POST["chat_id"]) && empty($_POST["group_id"])) ||
            empty($_POST["text"])) {
            throw new Exception("Invalid Parameters!");
        }

        if(empty($_POST["chat_id"])){
            $group = Hub::Group($_POST["group_id"]);
            if(!$group->exists()){
                throw new Exception("The group with the requested id could not be found!");
            }
            $chat = $group->getChat();
        } else {
            $chat = Hub::Chat($_POST["chat_id"]);
        }

        if(!$chat->exists()){
            throw new Exception("The chat with the requested id could not be found!");
        }

        Permissions::checkIsLoggedIn();
        Permissions::checkIsInGroup($chat->getGroup());

        $message = Hub::Message();
        if ($message->storeNewMessage($_SESSION["userId"], $chat->getChatId(), $_POST["text"])) {
            return ["success" => true, "msg" => "Nachricht gesendet.", "message_id" => $message->getMessageId()];
        } else {
            throw new Exception("Message not sent!");
        }
    }

    /**
     * deletes a message
     *
     * method: deleteMessage
     * message_id: int
     * @return array
     * @throws Exception
     */
    public function deleteMessage() {
        if (empty($_POST["message_id"])) {
            throw new Exception("Invalid Parameters!");
        }
        
        Permissions::checkIsTeacher();
        
        $message = Hub::Message($_POST["message_id"]);
        
        if(!$message->exists()){
            throw new Exception("The message with the requested id could not be found!");
        }
        
        $chat = Hub::Chat($message->getChatId());       
        Permissions::checkIsInGroup($chat->getGroup());

        $message->removeMessage();
        return ["success" => true, "msg" => "Message deleted."];
    }
}