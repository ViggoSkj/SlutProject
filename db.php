<?php
include "env.php";

class Database
{
    static protected $m_instance;

    public PDO $PDO;

    protected function __construct()
    {
        global $database_host;
        global $database_name;
        global $database_username;
        global $database_userpassword;
        $this->PDO = new PDO("mysql:host=$database_host;dbname=$database_name;charset=utf8", $database_username, $database_userpassword);
        $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function GetInstance(): Database
    {
        if (!self::$m_instance)
        {
            self::$m_instance = new Database();
        }
        return self::$m_instance;
    }
}

class DatabaseObject
{
    protected $m_database;

    protected function __construct()
    {
        $this->m_database = Database::GetInstance();
    }
}