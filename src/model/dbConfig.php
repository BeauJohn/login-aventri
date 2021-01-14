<?php
date_default_timezone_set('Europe/Brussels');

    class DB {
        private static $connString = "mysql:host=localhost;dbname=aventri_token;charset=utf8";
        private static $username = "root2";
        private static $password = "";
        private static $instance = null;

          public static function connect() {
           if(self::$instance === null) {
               try {
                   self::$instance = new pdo(self::$connString,self::$username,self::$password);
               }
               catch (PDOException $e ) {
                   echo 'failed:'. $e->getMessage();
               }
           }
           return self::$instance;
          }
    }