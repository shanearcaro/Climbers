<?php

namespace IT490\models\ModelController;

class UserModel extends ModelController
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
     * Get a user's information based on their userid number
     * Returns a single user's information
     */
    public function getUser(int $userid): array
    {
        $query = $db->prepare(
            "SELECT *
            FROM Users
            WHERE userid = ?   
        ");
        $query->execute([$userid]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserId(string $username): array
    {
        $query = $db->prepare(
            "SELECT userid
            FROM Users
            WHERE username = ?
        ");
        $query->execute([$username]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all available users
     * Returns array of all users within the database
     */
    public function getAllUsers(): array
    {
        $query = $db->prepare(
            "SELECT * 
            FROM Users
        ");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Updates the password of a user based on the userid of a user
     * Returns true on success, false on failure
     */
    public function updatePassword(int $userid, string $password, array $options = []): bool
    {
        // Generate password
        $hash = password_hash($password, PASSWORD_DEFAULT, $options);

        $query = $db->prepare(
            "UPDATE Users
            SET hash = ?
            WHERE userid = ?
        ");
        return $query->execute([$hash, $userid]);
    }

    /**
     * Updates the email address of a user based on the userid of a user
     * Returns true on success, false on failure
     */
    public function updateEmail(int $userid, string $email): bool
    {
        $query = $db->prepare(
            "UPDATE Users
            SET email = ?
            WHERE userid = ?
        ");
        return $query->execute([$email, $userid]);
    }

    /**
     * Authenticate a user in the database based on their username and password
     * Return true on successful authentication, false otherwise
     */
    public function authenticateUser(string $username, string $password): bool
    {
        $query = $dp->prepare(
            "SELECT hash
            FROM Users
            WHERE username = ?
        ");
        $query->execute([$username]);
        
        // Get query results as associative array
        $row = $query->fetch(PDO::FETCH_ASSOC);

        // If username not found return false
        if (count($row) == 0)
            return false;
        $hash = $row->{"hash"};

        // Return verified password hash
        return password_verify($password, $hash);
    }
}