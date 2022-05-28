<?php

class Chats {

    /**
     * fetches the messages for a given chat
     *
     * method: getMessages
     * chat_id: int
     * @return array
     * @throws Exception
     */
    public function getMessages(): array {
        if (empty($_POST["chat_id"])){
            throw new Exception("Invalid Parameters!");
        }

        $chat = Hub::Chat($_POST["chat_id"]);
        //TODO: add permission checks

        //TODO: add limitation for loading messages
        $messages = [];
        foreach ($chat->getMessages() as $message){
            $msgData = $message->getData();
            $messages[] = $msgData;
        }

        return $messages;
    }

    /**
     * receives and stores a new new message to a given chat
     *
     * method: sendMessage
     * text: string
     * @return array
     * @throws Exception
     */
    public function sendMessage() {
        if (empty($_POST["chat_id"]) ||
            empty($_POST["text"])) {
            throw new Exception("Invalid Parameters!");
        }

        Permissions::checkIsLoggedIn();
        //TODO: check for permission

        $message = Hub::Message();
        if ($message->storeNewMessage($_SESSION["userId"], $_POST["chat_id"], $_POST["text"])) {
            return ["success" => true, "msg" => "Nachricht gesendet.", "message_id" => $message->getMessageId()];
        } else {
            throw new Exception("Message not sent!");
        }
    }

    public function deleteMessage() {
        if (empty($_POST["message_id"])) {
            throw new Exception("Invalid Parameters!");
        }

        //TODO: check for permissions
        Permissions::checkIsTeacher();

        $message = Hub::Message($_POST["message_id"]);
        $message->removeMessage();
        return ["success" => true, "msg" => "Message deleted."];
    }
}