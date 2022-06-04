<?php

class Chat {
    private $name;
    private $chat_id;
    private $group;

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

    
    public function getGroup(): Group {
        if (empty($this->group)){

            $result = Database::select("SELECT pk_group_id FROM groups WHERE fk_chat_id = ?", [$this->chat_id], "i", true);
            
            if(!$result){
                throw new Exception("Chat has no group id!");
            }

            return $this->group = Hub::Group($result["pk_group_id"]);
        }
        return $this->group;
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
        } else {
            $this->chat_id = $new_chat_id;
        }
    }

    public function getMessages(int $offset): array {
        $offsetMessages = $offset * 20;
        $result = Database::select("SELECT * FROM message where fk_chat_id = ? ORDER BY time DESC LIMIT 20 OFFSET ?", [$this->chat_id, $offsetMessages], "ii");

        $messages = [];
        foreach ($result as $message) {
            $message["first_name"] = Hub::User($message["fk_user_id"])->getFirstName();
            $message["last_name"] = Hub::User($message["fk_user_id"])->getLastName();
            $message["fk_user_id"] == $_SESSION["userId"] ? $message["isOwnMessage"] = true : $message["isOwnMessage"] = false;
            $messages[] = $message;
        }
        return $messages;
    }
}