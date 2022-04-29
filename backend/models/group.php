<?php

class Group {
    private $hub;
    private $db;
    private $chat;
    private $group_id;
    private $name;
    private $members = [];
    private $assignments = [];

    public function __construct(Hub $hub, $id = null) {
        $this->hub = $hub;
        $this->db = $this->hub->getDb();

        empty($this->db->select("SELECT * FROM `groups` WHERE pk_group_id = ?", [$id], "i", true)) ? $this->group_id = null : $this->group_id = $id;
    }

    public function getGroupId() {
        return $this->group_id;
    }


    public function getChat(): Chat {
        if (empty($this->chat)){
            $result = $this->db->select("SELECT fk_chat_id as chat_id FROM `groups` where pk_group_id = ?", [$this->group_id], "i", true);
            $this->chat = $this->hub->getChats()->getById($result["chat_id"]);
        }
        return $this->chat;
    }

    public function getName() {
        if (empty($this->name)){
            return $this->name = $this->db->select("SELECT * FROM `groups` WHERE pk_group_id=?", [$this->group_id], "i", true)["name"];
        }
        return $this->name;
    }

    /**
     * fetches all user-objects that are member of the group and returns them
     * @return array
     */
    public function getMembers(): array {

        $result = $this->db->select("SELECT fk_user_id as user_id FROM user_group WHERE fk_group_id = ?", [$this->group_id], "i");
        foreach ($result as $item) {
            $this->members[] = $this->hub->getUsers()->getById($item["user_id"]);
        }
        return $this->members;
    }

    /**
     * checks, whether a user is part of the group or not
     * @param User $user
     * @return bool
     */
    public function isMember(User $user) :bool {
        if (empty($this->members)){
            $this->getMembers();
        }

        foreach ($this->members as $member){
            if ($member->getUserId() == $user->getUserId()){
                return true;
            }
        }
        return false;
    }

    /**
     * @param User $user
     * @return void
     */
    public function addMember(User $user){
        $this->db->insert("INSERT INTO user_group (fk_group_id, fk_user_id) VALUES (?, ?)", [$this->group_id, $user->getUserId()], "ii");
        $this->members[] = $user;
    }

    /**
     * TODO: get Assignments associated with the group
     * @return mixed
     */
    public function getAssignments() {
        return $this->assignments;
    }
}