<?php
require_once('../djmagic/rabbitMQLib.inc');
require_once('../logging/logPublish.php');

require_once('../models/Database.php');


// Get variables and request code
// $data = json_decode(file_get_contents('php://input'));
// $request_code = $data->{'request'};

$db = new Database("127.0.0.1", "IT490", "it490user", "it490pass");