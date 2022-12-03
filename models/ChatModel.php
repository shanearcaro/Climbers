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
     * @return int|bool
     * The **id** of the chat group if the chat exists, **false** otherwise
     */
    public function getChatId(string $area, string $time): int|bool
    {
        $query = $this->db->prepare(
            "SELECT chatid
            FROM Chats
            WHERE area = ? AND time = ?
        ");
        $query->execute([$area, $time]);

        // If no rows retrieved return false
        if ($query->rowCount() == 0)
            return false;
        // Return chatid if found
        return $query->fetch()["chatid"];
    }

    /**
     * Create a new chat group based on area and time
     * 
     * @param string $area the place where the climb will happen
     * @param string $time the time when the climb will happen
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function createChat(string $area, string $time): bool
    {
        // Check to see if chat is already created
        // Don't create duplicate chats
        $chat = $this->getChatId($area, $time);
        if ($chat)
            return false;
        $query = $this->db->prepare(
            "INSERT INTO Chats (area, time)
            VALUES (?, ?)
        ");
        return $query->execute([$area, $time]);
    }

    /**
     * Get the last created chat
     * 
     * @return array|bool
     * Returns the last created **chat**, **false** if no chats exist
     */
    public function getLatestChat(): array|bool
    {
        $query = $this->db->prepare(
            "SELECT *
            FROM Chats
            ORDER BY chatid
            DESC LIMIT 1
        ");
        $query->execute([]);
        // If no rows retrieved return false
        if ($query->rowCount() == 0)
            return false;
        return $query->fetch();
    }

    /**
     * Get the last created chat id
     * 
     * @return int|bool
     * Returns the last created chat **id**, **false** if no chats exist
     */
    public function getLatestChatId(): int|bool
    {
        return $this->getLatestChat()["chatid"];
    }

    /**
     * Insert a user into an existing chat
     * 
     * @param int $userid the id number of the user to be added
     * @param int $chatid the id number of the chat
     * 
     * @return
     * Returns **true** on success, **false** otherwise
     */
    public function insertUserIntoChat(int $userid, int $chatid): bool
    {
        $query = $this->db->prepare(
            "INSERT INTO ChatMembers (userid, chatid)
            VALUES (?, ?)
        ");
        try {
            $query->execute([$userid, $chatid]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Check whether a user belongs to a chat group or not
     * 
     * @param int $userid the id number of the user to be checked
     * @param int $chatid the id number of the chat
     * 
     * @return bool
     * Returns **true** if the user belongs to the chat group, **false** otherwise
     */
    public function isUserInChat(int $userid, int $chatid): bool
    {
        $query = $this->db->prepare(
            "SELECT *
            FROM ChatMembers
            WHERE userid = ? AND chatid = ?
        ");
        $query->execute([$userid, $chatid]);

        // Doesn't matter what the values are
        if (!$query->fetch())
            return false;
        return true;
    }

    /**
     * Insert a chat message
     * 
     * @param string $message the new text to be inserted
     * @param int $userid the id number of the user that created a message
     * @param int $chatid the id number of the chat to have the message inserted into
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function insertMessage(string $message, int $userid, int $chatid, ): bool
    {
        // Return false if user is not in chat
        if (!$this->isUserInChat($userid, $chatid))
            return false;
        $query = $this->db->prepare(
            "INSERT INTO ChatMessages (userid, chatid, `message`)
            VALUES (?, ?, ?)"
        );
        return $query->execute([$userid, $chatid, $message]);
    }

    /**
     * Get message based on its message id
     * 
     * @param int $messageid the id number of the message
     * 
     * @return array
     * Returns the **list of chat messages** sent by this user in a specific chat, **false** if the messageid isn't valid
     */
    public function getChatMessage(int $messageid): array|bool
    {
        $query = $this->db->prepare(
            "SELECT * FROM ChatMessages
            WHERE messageid = ?
        ");
        $query->execute([$messageid]);
        return $query->fetch();
    }

    /**
     * Get a users entire chat message history for a specific chat
     * 
     * @param int $userid the id number of the user
     * @param int $chatid the id number of the chat
     * 
     * @return array
     * Returns the **list of chat messages** sent by this user in a specific chat
     */
    public function getChatMessages(int $userid, $chatid): array
    {
        $query = $this->db->prepare(
            "SELECT * FROM ChatMessages
            WHERE userid = ? AND chatid = ?
        ");
        $query->execute([$userid, $chatid]);
        return $query->fetchAll();
    }

    /**
     * Get a users entire chat message history for a specific chat
     * 
     * @param int $userid the id number of the user
     * 
     * @return array
     * Returns the **list of chat messages** sent by this user
     */
    public function getAllUserChatMessages(int $userid): array
    {
        $query = $this->db->prepare(
            "SELECT * FROM ChatMessages
            WHERE userid = ?
        ");
        $query->execute([$userid]);
        return $query->fetchAll();
    }

    /**
     * Get all the chat groups that a user is in
     * 
     * @param int $userid the id number of the user to search
     * 
     * @return array
     * Returns the **list of all chats** a user is apart of
     */
    public function getAllUserChats(int $userid): array
    {
        $query = $this->db->prepare(
            "SELECT *
            FROM Chats AS c
            INNER JOIN ChatMembers AS cm on c.chatid = cm.chatid
            WHERE cm.userid = ?
        ");
        $query->execute([$userid]);
        return $query->fetchAll();
    }

    /**
     * Get all chats
     * 
     * @return array
     * Returns the **list of all chats**
     */
    public function getAllChats(): array
    {
        $query = $this->db->prepare(
            "SELECT *
            FROM Chats
        ");
        $query->execute([]);
        return $query->fetchAll();
    }

    /**
     * Delete a chat based on a chat id.
     * *NOTE* This will also delete all chat messages belonging to that chat
     * 
     * @return bool
     * Returns **true** on success, **false** on failure
     */
    public function deleteChat(int $chatid): bool
    {
        $query = $this->db->prepare(
            "DELETE FROM Chats
            WHERE chatid = ?
        ");

        // Return false if the chat messages cannot be deleted
        if (!$this->deleteAllChatMessages($chatid))
            return false;

        // Return false if the chat members cannot be deleted
        if (!$this->deleteAllChatMembers($chatid))
            return false;

        // Return whether the chat was deleted successfully or not
        return $query->execute([$chatid]);
    }

    /**
     * Delete a chat based on its message id
     * 
     * @param int $messageid The id number of the message to be deleted
     * 
     * @return bool
     * Returns **true** on success, **false** on failure
     */
    public function deleteChatMessage(int $messageid): bool
    {
        $query = $this->db->prepare(
            "DELETE FROM ChatMessages
            WHERE messageid = ?
        ");
        return $query->execute([$messageid]);
    }

    /**
     * Delete all messages belonging to a chat
     * 
     * @param int $chatid The id number of the chat where messages should be deleted
     * 
     * @return bool
     * Returns **true** on success, **false** on failure
     */
    public function deleteAllChatMessages(int $chatid): bool
    {
        $query = $this->db->prepare(
            "DELETE FROM ChatMessages
            WHERE chatid = ?
        ");
        return $query->execute([$chatid]);
    }

    /**
     * Delete all messages belonging to a user in a chat
     * 
     * @param int $userid The id number of the user to getting messages deleted
     * @param int $chatid The id number of the chat where messages should be deleted
     * 
     * @return bool
     * Returns **true** on success, **false** on failure
     */
    public function deleteUserChatMessages(int $userid, int $chatid): bool
    {
        $query = $this->db->prepare(
            "DELETE FROM ChatMessages
            WHERE userid = ? AND chatid = ?
        ");
        return $query->execute([$userid, $chatid]);
    }

    /**
     * Delete all messages belonging to a user
     * 
     * @param int $userid The id number of the user to getting messages deleted
     * 
     * @return bool
     * Returns **true** on success, **false** on failure
     */
    public function deleteAllUserChatMessages(int $userid): bool
    {
        $query = $this->db->prepare(
            "DELETE FROM ChatMessages
            WHERE userid = ?
        ");
        return $query->execute([$userid]);
    }

    /**
     * Delete a user from a chat
     * 
     * @param int $userid The id number of the user getting removed
     * @param int $chatid The id number of the chat where a member is being removed
     * 
     * @return bool
     * Returns **true** on success, **false** on failure
     */
    public function deleteChatMember(int $userid, int $chatid): bool
    {
        $query = $this->db->prepare(
            "DELETE FROM ChatMembers
            WHERE userid = ? AND chatid = ?
        ");
        return $query->execute([$userid, $chatid]);
    }

    /**
     * Delete a user from a chat
     * 
     * @param int $chatid The id number of the chat where all members are being removed
     * 
     * @return bool
     * Returns **true** on success, **false** on failure
     */
    public function deleteAllChatMembers(int $chatid): bool
    {
        $query = $this->db->prepare(
            "DELETE FROM ChatMembers
            WHERE chatid = ?
        ");
        return $query->execute([$chatid]);
    }
}