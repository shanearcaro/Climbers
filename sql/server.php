<?php
require_once('../djmagic/rabbitMQLib.inc');
require_once('../logging/logPublish.php');

require_once('../models/UserModel.php');
require_once('../models/ChatModel.php');

// Create connection string
$dsn = "mysql:host=127.0.0.1;dbname=IT490;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Try connection
try {
     $pdo = new PDO($dsn, "it490user", "it490pass", $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Get variables and request code
$data = json_decode(file_get_contents('php://input'));
$request_code = $data->{'request'};

// Create database model objects
$user = new UserModel($pdo);
$chat = new ChatModel($pdo);

print_r($user->getAllUsers());