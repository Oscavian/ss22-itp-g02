<?php

class Chat {
    private $name;
    private $chat_id;
    private $messages = [];

    public function __construct(int $id = null) {
        empty(Database::select("SELECT * FROM chat WHERE pk_chat_id = ?", [$id], "i")) ? $this->chat_id = null : $this->chat_id = $id;
    }

    public function exists(): bool {
        if (empty($this->chat_id)){
            return false;
        } else {
            return true;
        }
    }

    public function getChatId(): ?int {
        return $this->chat_id;
    }

    public function getName(): string {
        if (empty($this->name)){
            return $this->name = Database::select("SELECT name FROM chat WHERE pk_chat_id =?", [$this->chat_id], "i", true)["name"];
        }
        return $this->name;
    }

    public function storeNewChat(string $name) {
        $new_chat_id = Database::insert("INSERT INTO chat (name) VALUES (?)", [$name], "s");
        if (isset($this->chat_id)){
            $this->chat_id = $new_chat_id;
            $this->getName();
            $this->getMessages();
        } else {
            $this->chat_id = $new_chat_id;
        }
    }

    public function getMessages(): array {
        if (empty($this->messages)) {
            $result = Database::select("SELECT pk_message_id FROM message where fk_chat_id = ?", [$this->chat_id], "i");
            foreach ($result as $item) {
                $this->messages[] = Hub::Message($item["pk_message_id"]);
            }
        }
        return $this->messages;
    }
}