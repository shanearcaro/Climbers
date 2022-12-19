<?php

class Database
{
    /**
     * db is private so that the data stays encaspulated. No one should be able to interact
     * with the database in any way besides by using the defined queries below
     */
    private $db;

    /**
     * Create a new database object to interact with a PDO database
     * 
     * @param string $host The host server
     * @param string $databaseName The name of the database to connect
     * @param string $username The name of the account to connect to the database with
     * @param string $password The password of the account to connect to the database with
     */
    public function __construct(string $host, string $databaseName, string $username, string $password, array $options = [])
    {
        // Create the SQL connect string
        $dsn = "mysql:host=" . $host . ";dbname=" . $databaseName . ";charset=utf8mb4";

        // Set default options if none are specified
        if (!$options) {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
        }
        // Try connection
        try {
            $this->db = new PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * Creates and inserts a new user
     * 
     * @param string $username the username of the new account, this must be unique
     * @param string $email the email to contact for the new account
     * @param string $password the password for the new account
     * @param array $options list of all supplied password hash options
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function insertUser(string $username, string $email, string $password, array $options = []): bool
    {
        // Generate password
        $hash = password_hash($password, PASSWORD_DEFAULT, $options);

        $query = $this->db->prepare(
            "INSERT INTO Users (userid, username, email, `hash`, temp)
            VALUES (?, ?, ?, ?, ?)
        ");
        try {
            $query->execute([NULL, $username, $email, $hash, NULL]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Get a user's information based on their userid number
     * 
     * @param int $userid the id number of the user
     * 
     * @return array|bool
     * Returns a **user** if the id exists, **false** otherwise
     */
    public function getUser(int $userid): array|bool
    {
        $query = $this->db->prepare(
            "SELECT username, email
            FROM Users
            WHERE userid = ?   
        ");
        $query->execute([$userid]);
        return $query->fetch();
    }

    /**
     * Fetch the timestamp from a userimage.png
     * @param int $userid the id of the user to be searched
     * @return array|bool
     * Returns the **timestamp** of a user, **false** if the user doesn't exist
     */
    public function getTimestamp(int $userid): array|bool
    {
        $query = $this->db->prepare(
            "SELECT `timestamp`
            FROM Users
            WHERE userid = ?   
        ");
        $query->execute([$userid]);
        return $query->fetch();
    }

    /**
     * Get the id number of a user from their username
     * 
     * @param string $username the username of the user
     * 
     * @return int|bool
     * Returns the **id** of the user if it exists, **false** otherwise
     */
    public function getUserId(string $username): int|bool
    {
        $query = $this->db->prepare(
            "SELECT userid
            FROM Users
            WHERE username = ?
        ");
        $query->execute([$username]);
        $row = $query->fetch();

        if (!$row)
            return false;
        return $row["userid"];
    }

    /**
     * Get the id number of a user from their email
     * 
     * @param string $email the email of the user
     * 
     * @return int|bool
     * Returns the **id** of the user if it exists, **false** otherwise
     */
    public function getUserIdFromEmail(string $email): int|bool
    {
        $query = $this->db->prepare(
            "SELECT userid
            FROM Users
            WHERE email = ?
        ");
        $query->execute([$email]);
        $row = $query->fetch();

        if (!$row)
            return false;
        return $row["userid"];
    }

    /**
     * Get all available users
     * Returns **array of all users**
     * 
     * @return array
     * Entire users array
     */
    public function getAllUsers(): array
    {
        $query = $this->db->prepare(
            "SELECT * 
            FROM Users
        ");
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Updates the password of a user based on the userid of a user
     * 
     * The temp password will always be sent to the users actual password hash.
     * The password hash itself is secure so by default the temp hash is secure. The
     * only time the temp is different from the actual password is when the user is
     * trying to reset their password.
     * 
     * @param int $userid the id number of the user
     * @param string $password the new password to be set
     * @param array $options list of all supplied password hash options
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function updatePassword(int $userid, string $password, array $options = []): bool
    {
        // If user doesn't exist return false;
        if (!$this->getUser($userid))
            return false;
        // Generate password
        $hash = password_hash($password, PASSWORD_DEFAULT, $options);

        $query = $this->db->prepare(
            "UPDATE Users
            SET hash = ?, temp = ?
            WHERE userid = ?
        ");
        return $query->execute([$hash, NULL, $userid]);
    }

    /**
     * Sets the temporary password of a user based on the userid of a user
     * 
     * @param int $userid the id number of the user
     * @param string $password the new password to be set
     * @param array $options list of all supplied password hash options
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function setTempPassword(int $userid, string $password, array $options = []): bool
    {
        // Generate password
        $hash = password_hash($password, PASSWORD_DEFAULT, $options);

        $query = $this->db->prepare(
            "UPDATE Users
            SET temp = ?, timestamp = ?
            WHERE userid = ?
        ");

        // Get current date
        $date = new DateTime();
        $date = $date->format('Y-m-d H:i:s');

        return $query->execute([$hash, $date, $userid]);
    }

    /**
     * Resets the temporary password of a user based on the userid of a user
     * 
     * @param int $userid the id number of the user
     * @param array $options list of all supplied password hash options
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function resetTempPassword(int $userid, array $options = []): bool
    {

        $query = $this->db->prepare(
            "UPDATE Users
            SET temp = ?
            WHERE userid = ?
        ");
        return $query->execute([NULL, $userid]);
    }

    /**
     * Updates the email address of a user based on the userid of a user
     * 
     * @param int $userid the id number of the user
     * @param string $email the new email address of the user
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function updateEmail(int $userid, string $email): bool
    {
        $query = $this->db->prepare(
            "UPDATE Users
            SET email = ?
            WHERE userid = ?
        ");
        return $query->execute([$email, $userid]);
    }

    /**
     * Authenticate a user in the database based on their username and password
     * 
     * @param string $username the username of the user
     * @param string $password the password of the user
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function authenticateUser(string $username, string $password): bool
    {
        $query = $this->db->prepare(
            "SELECT hash
            FROM Users
            WHERE username = ?
        ");
        $query->execute([$username]);
        
        // Get query results as associative array
        $row = $query->fetch();

        // If username not found return false
        if ($query->rowCount() == 0)
            return false;
        $hash = $row["hash"];

        // Return verified password hash
        return password_verify($password, $hash);
    }

    /**
     * Authenticate a user in the database based on their username and temp password
     * 
     * @param string $username the username of the user
     * @param string $password the password of the user
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function authenticateTempUser(string $username, string $password): bool
    {
        $query = $this->db->prepare(
            "SELECT temp
            FROM Users
            WHERE username = ?
        ");
        $query->execute([$username]);
        
        // Get query results as associative array
        $row = $query->fetch();

        // If username not found return false
        if ($query->rowCount() == 0)
            return false;
        $hash = $row["temp"];

        // Return verified password hash
        return password_verify($password, $hash);
    }

    /**
     * Create a friendship between two users
     * 
     * @param int $userid The id number of the user that is creating a friend request
     * @param int $userid The id number of the user that is receiving the friend request
     * 
     * @return bool
     * Return **true** on success, **false** on failure
     */
    public function createFriendship(int $userid, int $friendid): bool
    {
        // If the friendship between these two users already exists return false
        if ($this->getConnectionId($userid, $friendid))
            return false;

        // if the users are blocked do not create a friendship
        if ($this->isUserBlocked($userid, $friendid))
            return false;
        $query = $this->db->prepare(
            "INSERT INTO UserFriends (userid, friendid)
            VALUES (?, ?)
        ");
        // Return false if user doesn't exist
        try {
            $query->execute([$userid, $friendid]);
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Accept a friend request between two users
     * 
     * @param int $userid The id number of the user that sent the request
     * @param int $friendid The id number of the user that received the request
     * 
     * @return bool
     * Return **true** on success, **false** on failure
     */
    public function acceptFriendRequest(int $userid, int $friendid): bool
    {
        $query = $this->db->prepare(
            "UPDATE UserFriends
            SET accepted = b'1'
            WHERE userid = ? AND friendid = ?
        ");
        return $query->execute([$userid, $friendid]);
    }

    /**
     * Accept a friend request between two users with the connection id
     * 
     * @param int $connectionid The id number of the connection request
     * 
     * @return bool
     * Return **true** on success, **false** on failure
     */
    public function acceptFriendRequestWithId(int $connectionid): bool
    {
        $query = $this->db->prepare(
            "UPDATE UserFriends
            SET accepted = b'1'
            WHERE connectionid = ?  
        ");
        return $query->execute([$connectionid]);
    }

    /**
     * Get all friends of a user
     * 
     * @param int $userid the id number of a user
     * 
     * @return array
     * Returns the **list of friends** a user has
     */
    public function getFriends(int $userid): array
    {
        $query = $this->db->prepare(
            "SELECT * FROM UserFriends
            WHERE userid = ? OR friendid = ?
        ");
        $query->execute([$userid, $userid]);
        return $query->fetchAll();
    }

    /**
     * Get the connection id between two friends
     * 
     * @param int $userid The id of the first friend
     * @param int $friendid The id of the second friend
     * 
     * @return int|bool
     * Returns **connectionid** if exists, **false** otherwise
     */
    public function getConnectionId(int $userid, int $friendid): int|bool
    {
        $query = $this->db->prepare(
            "SELECT *
            FROM UserFriends
            WHERE (userid = ? AND friendid = ?) OR (userid = ? AND friendid = ?)
        ");
        $query->execute([$userid, $friendid, $friendid, $userid]);

        // Return false if no connection exists
        if ($query->rowCount() == 0)
            return false;
        return $query->fetch()["connectionid"];
    }

    /**
     * Block a user
     * 
     * *NOTE* Blocking a user will also delete the friend connection between
     * these users, if it exists
     * 
     * @param int $userid The id number of the user creating the block
     * @param int $blockid The id number of the user being blocked
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function blockUser(int $userid, int $blockid): bool
    {
        // Delete a friend connection with a user if the user is now being blocked
        $connectionid = $this->getConnectionId($userid, $blockid);
        if ($connectionid)
            $this->deleteFriend($connectionid);

        // If user is already blocked return true
        if ($this->isUserBlocked($userid, $blockid))
            return true;

        $query = $this->db->prepare(
            "INSERT INTO UserBlocks (userid, blockid)
            VALUES (?, ?)
        "); 
        // Catch exception if user doesn't exist
        try {
            $query->execute([$userid, $blockid]);
        } catch (PDOException $e) {
            return false;
        }    
        return true;
    }

    /**
     * Get all blocked user ids
     * 
     * @param int $userid the id number of the user to be searched
     * 
     * @return array
     * Returns the **list of blocked user ids**
     */
    public function getBlockedUsers(int $userid): array
    {
        $query = $this->db->prepare(
            "SELECT * FROM UserBlocks
            WHERE userid = ? OR blockid = ?
        ");
        $query->execute([$userid, $userid]);
        return $query->fetchAll();
    }

    /**
     * Determine whether a user is blocked or not
     * 
     * @param int $userid The id number of the first user
     * @param int $blockid The id number of the second user
     * 
     * @return bool
     * Returns **true** if the user is blocked, **false** otherwise
     */
    public function isUserBlocked(int $userid, int $blockid): bool
    {
        $query = $this->db->prepare(
            "SELECT *
            FROM UserBlocks
            WHERE (userid = ? AND blockid = ?) OR (userid = ? AND blockid = ?)
        ");
        $query->execute([$userid, $blockid, $blockid, $userid]);

        // Return whether user is blocked
        return !$query->rowCount() == 0;
    }

    /**
     * Delete a friendship using a connection id
     * 
     * @param int $connectionid The id number of the connection between two users
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function deleteFriend(int $connectionid): bool
    {
        $query = $this->db->prepare(
            "DELETE
            FROM UserFriends
            WHERE connectionid = ?
        ");
        return $query->execute([$connectionid]);
    }

    /**
     * Deletes a blocked user
     * 
     * @param int $userid The id of the first user
     * @param int $blockid The id of the second user
     * 
     * @return bool
     * Returns **true** on success, **false** otherwise
     */
    public function deleteBlockedUser(int $userid, int $blockid): bool
    {
        $query = $this->db->prepare(
            "DELETE FROM UserBlocks
            WHERE (userid = ? AND blockid = ?) OR (userid = ? AND blockid = ?)
        ");
        return $query->execute([$userid, $blockid, $blockid, $userid]);
    }

    /**
     * Get the id of a chat room based on its area and time
     * 
     * @param string $area the area of climbing for the chat room
     * @param string $time the time the climb is supposed to be taking place
     * 
     * @return int|bool
     * The **id** of the chat room if the chat exists, **false** otherwise
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
     * Get the area and time of a chat room based on its chatid
     * 
     * @param int $chatid the chat id of the chat room being searched
     * 
     * @return array|bool
     * The **area** and **time** of the chat room if the chat exists, **false** otherwise
     */
    public function getChatInfo(int $chatid): array|bool
    {
        $query = $this->db->prepare(
            "SELECT area, time
            FROM Chats
            WHERE chatid = ?
        ");
        $query->execute([$chatid]);

        // If no rows retrieved return false
        if ($query->rowCount() == 0)
            return false;

        // Return area and time if chat exists
        return $query->fetch();
    }

    /**
     * Create a new chat room based on area and time
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
     * Check whether a user belongs to a chat room or not
     * 
     * @param int $userid the id number of the user to be checked
     * @param int $chatid the id number of the chat
     * 
     * @return bool
     * Returns **true** if the user belongs to the chat room, **false** otherwise
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
     * Get a chats entire chat message history
     * 
     * @param int $chatid the id number of the chat
     * 
     * @return array
     * Returns the **list of chat messages** in this chat
     */
    public function getAllChatMessages(int $chatid): array
    {
        $query = $this->db->prepare(
            "SELECT * FROM ChatMessages
            WHERE chatid = ?
        ");
        $query->execute([$chatid]);
        return $query->fetchAll();
    }

    /**
     * Get all the chat rooms that a user is in
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