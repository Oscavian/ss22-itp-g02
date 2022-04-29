<?php
require_once "models/chat.php";
require_once "models/message.php";

class Chats {

    private $db;
    private $hub;

    public function __construct(Hub $hub){
        $this->hub = $hub;
        $this->db = $this->hub->getDb();
    }

    public function getById(int $id): Chat {
        return new Chat($this->hub, $id);
    }
}