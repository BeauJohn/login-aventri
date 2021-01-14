<?php
//Credentials
$masterKey = 'cf3bc560a01f8fd9e2b28b21f182abf419809166';
$eventID = '540755';
$accountID = '4648';

$body = array('accountid' => $accountID, 'key' => $masterKey);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://api-na.eventscloud.com/api/v2/global/authorize.json");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($body));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

$accesstoken = $data['accesstoken'];


//Helper Functions
function getResponseMail($dataFields) {
    foreach ($dataFields as $key => $value) {
        if ($value['fieldname'] === 'email') {
            return $value['choicekey'];
        }
    }
}

//Functions
function validateUser($mail, $userID) {
    global $eventID, $accesstoken;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://api-na.eventscloud.com/api/v2/ereg/getAttendee.json?accesstoken=$accesstoken&eventid=$eventID&attendeeid=$userID");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);
    
    $dataFields = $data['responses'];
    $responseMail = getResponseMail($dataFields);

    if ($mail === $responseMail) {
        return true;
    }

    return false;
}


function updateStatus($userID) {
    global $eventID, $accesstoken;

    $status = 'Confirmed';
    $body = Array('accesstoken' => $accesstoken, 'attendeeid' => $userID, 'eventid' => $eventID, 'status' => $status);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://api-na.eventscloud.com/api/v2/ereg/updateAttendeeStatus.json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($body));
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);
    
    if ($data['attendeeid'] === $userID) {
        return true;
    }

    return false;
}
