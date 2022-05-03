<?php

class Chat {
    private $name;
    private $chat_id;

    public function __construct($id = null) {
        empty(Database::select("SELECT * FROM chat WHERE pk_chat_id = ?", [$id], "i")) ? $this->chat_id = null : $this->chat_id = $id;
    }

    public function getChatId() {
        return $this->chat_id;
    }

    public function getName() {
        //TODO
        return $this->name;
    }

}