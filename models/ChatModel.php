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

}