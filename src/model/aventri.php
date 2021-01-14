<?php
header('Access-Control-Allow-Origin: *');

class Aventri
{

    function __construct($masterKey, $eventID, $accountID, $mail, $userID)
    {
        $this->masterkey = $masterKey;
        $this->eventID = $eventID;
        $this->accountID = $accountID;
        $this->mail = $mail;
        $this->userID = $userID;

        $body = ['accountid' => $accountID, 'key' => $masterKey];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api-na.eventscloud.com/api/v2/global/authorize.json");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        curl_close($ch);

        $this->accesstoken = $data['accesstoken'];
    }

    private function getResponseMail($dataFields)
    {
        foreach ($dataFields as $key => $value) {
            if ($value['fieldname'] === 'email') {
                return $value['choicekey'];
            }
        }
    }

    public function validateUser()
    {
        $userID = $this->userID;
        $eventID = $this->eventID;
        $accesstoken = $this->accesstoken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api-na.eventscloud.com/api/v2/ereg/getAttendee.json?accesstoken=$accesstoken&eventid=$eventID&attendeeid=$userID");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        curl_close($ch);

        $dataFields = $data['responses'];
        $responseMail = $this->getResponseMail($dataFields);

        if ($responseMail === $this->mail) {
            $this->data = $data;
            return true;
        }

        return false;
    }


    public function updateStatus()
    {
        $accesstoken = $this->accesstoken;
        $userID = $this->userID;
        $eventID = $this->eventID;
        $status = 'Attended';

        $body = array('accesstoken' => $accesstoken, 'attendeeid' => $userID, 'eventid' => $eventID, 'status' => $status);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api-na.eventscloud.com/api/v2/ereg/updateAttendeeStatus.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        curl_close($ch);

        if ($data['attendeeid'] === $this->userID) {
            return true;
        }

        return false;
    }
}