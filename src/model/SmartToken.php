<?php

class SmartToken {

    private function accesToken() {
        //these credentials can be found inside your Project  > Code > Smart Content
        $clientId = 'smrt16153xfa820ef11c';
        $clientSecret = 'cd00692c3bfe59267d5ecfac5310286c';
        
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sitemn.gr/token/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'smart',
            'grant_type' => 'client_credentials',
        ]);
        
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        $accessToken = $data['access_token'];

        return $accessToken;
    }

    public function smartToken() {
        $accessToken = $this->accesToken();
        $groupID = '1'; //this groupID can be found when editing a smart group

        $ch = curl_init();
        $endpoint = 'https://api.sitemn.gr/smarttoken/?access_token=' . $accessToken;

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['groupID' => $groupID]);

        $response = curl_exec($ch);
        $data = json_decode($response, true);


        if ($data["status"]["type"] == "success") {
            $smartToken = $data['logintoken'];
            return $smartToken;
        } else {
            $err = $data["status"]["message"];
            throw new Exception($err);
        }
    }
}

// /swfiles/lib/protected.php*/