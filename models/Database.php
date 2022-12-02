<?php

require_once('UserModel.php');
require_once('ChatModel.php');

class Database
{
    /**
     * db is private so that the data stays encaspulated. No one should be able to interact
     * with the database in any way besides by using the defined queries below
     */
    private $db;

    /**
     * UserModel object that contains all queries related to a User.
     */
    public $user;

    /**
     * ChatModel object that contains all queries related to a Chat.
     */
    public readonly object $chat;

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
        if (!$options)
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
        
       // Try connection
        try {
            $this->db = new PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        } 

        // Create user and chat objects
        $this->user = new UserModel($this->db);
        $this->chat = new ChatModel($this->db);
    }
}