<?php

class UserModel
{
    /**
     * Database to connect and interact wtih, set up to only work with PDO
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
}