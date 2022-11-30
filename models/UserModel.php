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
     * Get a user's information based on their userid number
     * 
     * @param int $userid the id number of the user
     * 
     * @return 
     * Returns a single user's information if the id exists, false otherwise
     */
    public function getUser(int $userid): mixed
    {
        $query = $this->db->prepare(
            "SELECT *
            FROM Users
            WHERE userid = ?   
        ");
        $query->execute([$userid]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get the id number of a user from their username
     * 
     * @param string $username the username of the user
     * 
     * @return int | bool
     * The id of the user if it exists, false otherwise
     */
    public function getUserId(string $username): int | bool
    {
        $query = $this->db->prepare(
            "SELECT userid
            FROM Users
            WHERE username = ?
        ");
        $query->execute([$username]);
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if (!$row)
            return false;
        return $row->{"userid"};
    }

    /**
     * Get all available users
     * Returns array of all users within the database
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
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Updates the password of a user based on the userid of a user
     * 
     * @param int $userid the id number of the user
     * @param string $password the new password to be set
     * @param array $options list of all supplied password hash options
     * 
     * @return bool
     * True on successful update, false otherwise
     */
    public function updatePassword(int $userid, string $password, array $options = []): bool
    {
        // Generate password
        $hash = password_hash($password, PASSWORD_DEFAULT, $options);

        $query = $this->db->prepare(
            "UPDATE Users
            SET hash = ?
            WHERE userid = ?
        ");
        return $query->execute([$hash, $userid]);
    }

    /**
     * Updates the email address of a user based on the userid of a user
     * 
     * @param int $userid the id number of the user
     * @param string $email the new email address of the user
     * 
     * @return bool
     * True on successful update, false otherwise
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
     * true on successful authentication, false otherwise
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
        $row = $query->fetch(PDO::FETCH_ASSOC);

        // If username not found return false
        if (count($row) == 0)
            return false;
        $hash = $row->{"hash"};

        // Return verified password hash
        return password_verify($password, $hash);
    }
}