<?php
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Europe/Brussels');

class DB
{
    private static $connString = "mysql:host=ID123087_appcnctr.db.webhosting.be;dbname=ID123087_appcnctr;charset=utf8";
    private static $username = "ID123087_appcnctr";
    private static $password = "appcnctr@sitemngr123";
    private static $instance = null;

    public static function connect()
    {
        if (self::$instance === null) {
            try {
                self::$instance = new pdo(self::$connString, self::$username, self::$password);
            } catch (PDOException $e) {
                echo 'failed:' . $e->getMessage();
            }
        }
        return self::$instance;
    }
}