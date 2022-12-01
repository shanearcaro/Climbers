<?php

class ChatModel
{
    /**
     * Database to connect and interact with, set up to only work with PDO
     */
    protected $db;

    /**
     * Set the database
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get the id of a chat group based on its area and time
     * 
     * @param string $area the area of climbing for the chat group
     * @param string $time the time the climb is supposed to be taking place
     * 
     * @return int | bool
     * The id of the chat group if the chat exists, false otherwise
     */
    public function getChatId(string $area, string $time): int | bool
    {
        $query = $this->db->prepare(
            "SELECT chatid
            FROM Chats
            WHERE area = ? AND time = ?
        ");
        $query->execute([$area, $time]);
        return $query->fetch(PDO::FETCH_ASSOC)->{"chatid"};
    }

    /**
     * Create a new chat group based on area and time
     * 
     * @param string $area the place where the climb will happen
     * @param string $time the time when the climb will happen
     * 
     * @return bool
     * True on successful creation, false otherwise
     */
    public function insertChat(string $area, string $time): bool
    {
        $query = $this->db->prepare(
            "INSERT INTO Chats (area, `time`)
            WHERE area = ? AND `time` = ?
        ");
        return $query->execute([$area, $time]);
    }

    /**
     * Get the last created chat
     * 
     * @return array | bool
     * The last created chat, false if no chats exist
     */
    public function getLatestChat(): array | bool
    {
        $query = $this->db->prepare(
            "SELECT *
            FROM Chats
            ORDER BY chatid
            DESC LIMIT 1
        ");
        $query->execute([]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a user into an existing chat
     * 
     * @param int $userid the id number of the user to be added
     * @param int $chatid the id number of the chat
     * 
     * @return
     * True on successful insertion, false otherwise
     */
    public function insertUserIntoChat(int $userid, int $chatid): bool
    {
        $query = $this->db->prepare(
            "INSERT INTO ChatMembers (userid, cahtid)
            VALUES (?, ?)
        ");
        return $query->execute([$userid, $chatid]);
    }

    /**
     * Check whether a user belongs to a chat group or not
     * 
     * @param int $userid the id number of the user to be checked
     * @param int $chatid the id number of the chat
     * 
     * @return bool
     * True if the user belongs to the chat group, false otherwise
     */
    public function getUserInChat(int $userid, int $chatid): bool
    {
        $query = $this->db->prepare(
            "SELECT *
            FROM ChatMembers
            WHERE userid = ? AND chatid = ?
        ");
        $query->execute([$userid, $chatid]);

        // Doesn't matter what the values are
        if (!$query->fetch(PDO::FETCH_ASSOC))
            return false;
        return true;
    }

    /**
     * Insert a chat message
     * 
     * @param int $userid the id number of the user that created a message
     * @param int $chatid the id number of the chat to have the message inserted into
     * @param string $message the new text to be inserted
     * 
     * @return bool
     * True on successful insertion, false otherwise
     */
    public function insertMessage(int $userid, int $chatid, string $message): bool
    {
        $query = $this->db->prepare(
            "INSERT INTO ChatMessages (userid, chatid, `message`)
            VALUES (?, ?, ?)"
        );
        return $query->execute([$userid, $chatid, $message]);
    }

    /**
     * Get a users entire chat message history for a specific chat
     * 
     * @param int $userid the id number of the user
     * @param int $chatid the id number of the chat
     * 
     * @return array
     * The list of chat messages sent by this user
     */
    public function getChatMessages(int $userid, $chatid): array
    {
        $query = $this->db->prepare(
            "SELECT * FROM ChatMessages
            WHERE userid = ? AND chatid = ?
        ");
        $query->execute([$userid, $chatid]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all the chat groups that a user is in
     * 
     * @param int $userid the id number of the user to search
     * 
     * @return array
     * List of all chats a user is apart of
     */
    public function getChats(int $userid): array
    {
        $query = $this->db->prepare(
            "SELECT cm.userid, cm.chatid, c.area, c.time
            FROM ChatMembers AS cm
            INNER JOIN Chats AS c on cm.chatid = c.chatid
            WHERE cm.userid = ?
        ");
        $query->execute([$userid]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}