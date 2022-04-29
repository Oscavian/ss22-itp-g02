<?php

class Chat {
    private $hub;
    private $db;
    private $name;
    private $chat_id;

    public function __construct(Hub $hub, $id = null) {
        $this->hub = $hub;
        $this->db = $hub->getDb();

        empty($this->db->select("SELECT * FROM chat WHERE pk_chat_id = ?", [$id], "i")) ? $this->chat_id = null : $this->chat_id = $id;
    }

    public function getChatId() {
        return $this->chat_id;
    }

    public function getName() {
        //TODO
        return $this->name;
    }


}