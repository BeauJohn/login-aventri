<?php
require_once __DIR__ . '/../model/User.php';

$rowID = '8';
$token = 'b28b1050ba54f33d8be34f182c931c81a1f8af8b.93022.67455292';

try  {
    $user = new User();
    $user->setRowId($rowID);
    $user->setToken($token);
    $valid = $user->validateToken();

    echo json_encode($valid);

} catch (Exception $e) {
    $errorMsg = $e->getMessage();
    echo json_encode('Error: ' . $errorMsg);
}    