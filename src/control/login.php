<?php 
require_once __DIR__ . '/../model/aventri.php';
require_once __DIR__ . '/../model/User.php';

$mail = $_POST['login'];
$userID = $_POST['ID'];

try {
    if(validateUser($mail, $userID) === true) {
        $statusUpdate = updateStatus($userID);
        if ($statusUpdate === false) {
            throw new Exception('Attendee does not have a valid status');
        }

        $user = new User();
        $user->setMail($mail);
        $user->setUserId($userID);
        $user->setUser();

        $loginData = $user->loginUser();
        echo json_encode($loginData);
    } else {
        throw new Exception('User does not exist');
    }
} catch(Exception $e) {
    $errorMsg = $e->getMessage();
    echo json_encode(['status' => 'Error: ' . $errorMsg]);
}