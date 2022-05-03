<?php

class Group {
    private $chat;
    private $group_id;
    private $name;
    private $members = [];
    private $assignments = [];

    public function __construct($id = null) {
        
        empty(Database::select("SELECT * FROM `groups` WHERE pk_group_id = ?", [$id], "i", true)) ? $this->group_id = null : $this->group_id = $id;
    }

    public function getBaseData(): array {

        $query = "SELECT pk_group_id as group_id, name, fk_chat_id as chat_id where pk_assignment_id = ?"; 
        $result = Database::select($query, [$this->group_id], "i", true);
        
        $this->chat = $result["chat_id"];
        $this->group_id = $result["group_id"];
        $this->name = $result["name"];

        return $result;
    }

    public function getId() : int{
        return $this->group_id;
    }

    public function exists(){   
        if(empty($this->group_id)){
            return false;
        };
        return true;
    }

    public function getChat(): Chat {
        if (empty($this->chat)){
            $result = Database::select("SELECT fk_chat_id as chat_id FROM `groups` where pk_group_id = ?", [$this->group_id], "i", true);
            $this->chat = Hub::Chat($result["chat_id"]);
        }
        return $this->chat;
    }

    public function getName() {
        if (empty($this->name)){
            return $this->name =Database::select("SELECT * FROM `groups` WHERE pk_group_id=?", [$this->group_id], "i", true)["name"];
        }
        return $this->name;
    }

    /**
     * fetches all user-objects that are member of the group and returns them
     * @return array
     */
    public function getMembers(): array {

        $result = Database::select("SELECT fk_user_id as user_id FROM user_group WHERE fk_group_id = ?", [$this->group_id], "i");
        foreach ($result as $item) {
            $this->members[] = Hub::User($item["user_id"]);
        }
        return $this->members;
    }

    /**
     * @param User $user
     * @return void
     */
    public function addMember(User $user){
        
        Database::insert("INSERT INTO user_group (fk_group_id, fk_user_id) VALUES (?, ?)", [$this->group_id, $user->getId()], "ii");
        $this->members[] = $user;
    }

    /**
     * adds Group to database
     * sets $this->group_id to new group id
     * also creates Group chat and links it to group
     * @return void
     */
    public function storeNewGroup($groupName){
        
        //TODO: create chat as part of chat class
        $newChatId = Database::insert("INSERT INTO chat (name) VALUES (?)", [$groupName], "s");
        $this->group_id = Database::insert("INSERT INTO `groups` (name, fk_chat_id) VALUES (?, ?)", [$groupName, $newChatId], "si");
    }

    /**
     * fetches all Assignments associated with the group and returns them
     * @return array
     */
    public function getAssignments(): array {
        
        if (!empty($this->assignments)){
            return $this->assignments;
        }

        $result = Database::select("SELECT pk_assignment_id as assignment_id FROM assignment WHERE fk_group_id = ?", [$this->group_id], "i");
        foreach ($result as $item) {
            $this->assignments[] = Hub::Assignment($item["assignment_id"]);
        }
        return $this->assignments;
    }

}