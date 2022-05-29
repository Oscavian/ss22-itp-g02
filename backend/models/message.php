<?php

class Message {
    private $message_id;
    private $user_id;
    private $chat_id;
    private $text;
    private $time;
    //private $file_path;
    //private $image_path;


    public function __construct($id = null) {
        empty(Database::select("SELECT * FROM message WHERE pk_message_id = ?", [$id], "i")) ? $this->message_id = null : $this->message_id = $id;

    }

    public function exists(): bool {
        if (empty($this->message_id)){
            return false;
        } else {
            return true;
        }
    }

    public function getData(): array {
        $query = "SELECT fk_user_id as user_id, fk_chat_id as chat_id, time, text FROM message WHERE pk_message_id = ?";
        $result = Database::select($query, [$this->message_id], "i", true);

        $this->user_id = $result["user_id"];
        $this->text = $result["text"];
        $this->chat_id = $result["chat_id"];
        $this->time = $result["time"];

        return get_object_vars($this);
    }

    public function storeNewMessage($user_id, $chat_id, $text): bool {

        $new_message_id = Database::insert("INSERT INTO message (fk_user_id, fk_chat_id, text) values (?, ?, ?)", [$user_id, $chat_id, $text], "iis");
        if (isset($this->message_id)) {
            $this->message_id = $new_message_id;
            $this->getData();
        } else {
            $this->message_id = $new_message_id;
        }

        if (isset($new_message_id)) {
            return true;
        } else {
            return false;
        }
    }

    public function removeMessage(): bool {
        return Database::delete("DELETE FROM message where pk_message_id = ?", [$this->message_id], "i");
    }

    public function getMessageId() {
        return $this->message_id;
    }

    public function getUserId() {
        if (empty($this->user_id)) {
            return $this->user_id = Database::select("SELECT fk_user_id AS user_id FROM message WHERE pk_message_id = ?", [$this->message_id], "i", true)["user_id"];
        }
        return $this->user_id;
    }

    public function getText() {
        if (empty($this->text)) {
            return $this->text = Database::select("SELECT text FROM message WHERE pk_message_id = ?", [$this->message_id], "i", true)["text"];
        }
        return $this->text;
    }

    public function getChatId() {
        if (empty($this->chat_id)) {
            return $this->chat_id = Database::select("SELECT fk_chat_id AS chat_id FROM message WHERE pk_message_id = ?", [$this->message_id], "i", true)["chat_id"];
        }
        return $this->chat_id;
    }


    public function getTime() {
        if (empty($this->time)) {
            return $this->time = Database::select("SELECT time AS user_id FROM message WHERE pk_message_id = ?", [$this->message_id], "i", true)["time"];
        }
        return $this->time;
    }
}