<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../model/dbConfig.php';
require_once __DIR__ . '/../model/SmartToken.php';

class User {
  private $mail;
  private $userID;
  private $rowID;
  private $token;
  private $smartCredentials;

  
  //Setters
  public function setMail($mail) {
    if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $this->mail = $mail;
    } else {
      throw new Exception('Email does not have a valid format');
    }
  }

  public function setSmartCredentials($client, $secret) {
    if(isset($client) && isset($secret)) {
      $smartCredentials = ['clientKey' => $client, 'secretKey' => $secret];
      $this->smartCredentials = $smartCredentials;
    } else {
      throw new Exception('No smart content keys are set');
    }
  }

  public function setRowId($rowID) {
    if (is_numeric($rowID)) {
      $this->rowID = $rowID;
    } else {
      throw new Exception('Row ID is not numeric');
    }
  }
  
  public function setUserID($userID) {
    if (is_numeric($userID)) {
      $this->userID = $userID;
    } else {
      throw new Exception('Reference number is not numeric');
    }
  }

  public function setToken($token) {
    if (preg_match('/^[a-z0-9 .\-]+$/i', $token)) {
      $this->token = $token;
    } else {
      throw new Exception('Token does not have a valid format');
    }
  }


//private  
  private function createToken() {
      $header = md5(time());
      $middle = rand(10000, 99999);  
      $id = rand(10000000, 99999999);

      return $header . '.' . $middle . '.' . $id; 
  }
  
  
  private function getToken() {
    $userID = $this->userID;
    
    $sql = "SELECT token FROM offcores_user WHERE referenceNumber = :userID";
    $inst = DB::connect()->prepare($sql);
    $inst->execute(array(":userID" => $userID));
    $data = $inst->fetch(PDO::FETCH_ASSOC);
    $inst = null;
    
    return $data['token'];
  }
  
  private function getRowId() {
    $userID = $this->userID;
    
    $sql = "SELECT ID FROM offcores_user WHERE referenceNumber = :userID";
    $inst = DB::connect()->prepare($sql);
    $inst->execute(array(":userID" => $userID));
    $data = $inst->fetch(PDO::FETCH_ASSOC);
    $inst = null;
    
    return $data['ID'];
  }
  

//Public
  public function validateToken() {
    $token = $this->token;
    $rowID = $this->rowID;

    $sql = "SELECT token FROM offcores_user WHERE ID = :ID";
    $inst = DB::connect()->prepare($sql);
    $inst->execute(array(":ID" => $rowID));
    $data = $inst->fetch(PDO::FETCH_ASSOC);
    $inst = null;

    if ($token === $data['token']) {
      return ['status' => 'success']; 
    }

    return ['status' => 'invalid'];
  }

  public function loginUser($groupID = 1) {
    $userToken = $this->getToken();
    $userID = $this->getRowId();
    $smrt = new SmartToken($this->smartCredentials);
    $smartToken = $smrt->smartToken($groupID);
    
    return ['status' => 'success', 'userID' => $userID, 'loginToken' => $userToken, 'smartToken' => $smartToken ];
  }

  public function setUser() {
    $userID = $this->userID;
    $mail = $this->mail;

    $sql = "SELECT referenceNumber FROM offcores_user WHERE referenceNumber = :userID";
    $inst = DB::connect()->prepare($sql);
    $inst->execute(array(":userID" => $userID));
    $data = $inst->fetch(PDO::FETCH_ASSOC);
    $inst = null;
    
    if(empty($data) === true ) {
      $token = $this->createToken();
      $sql = "INSERT INTO offcores_user (Email, referenceNumber, token) VALUES (:Email, :userID, :token)";
      $inst = DB::connect()->prepare($sql);
      $response = $inst->execute(array(":Email" => $mail,":userID" => $userID, ':token' => $token));
      $inst = null;
      
      if($response === false) {
        throw new Exception('Updating new user failed');
      }

    } else if (empty($data) === false) {
      $token = $this->createToken();
      $sql = "UPDATE offcores_user SET token = :token WHERE referenceNumber = :userID";
      $inst = DB::connect()->prepare($sql);
      $response = $inst->execute(array(":userID" => $userID, ':token' => $token));
      $inst = null;

      if($response === false) {
        throw new Exception('Token not updated');
      }
    }
  }
}