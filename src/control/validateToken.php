<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../model/User.php';

$rowID = $_POST['userID'];
$token = $_POST['token'];

try {
    $user = new User();
    $user->setRowId($rowID);
    $user->setToken($token);
    $valid = $user->validateToken();

    echo json_encode($valid);
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
    echo json_encode('Error: ' . $errorMsg);
}