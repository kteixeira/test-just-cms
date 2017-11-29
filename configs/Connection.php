<?php

namespace Configs;
require_once 'database.php';

class Connection
{
    private static $instance;

    /**
     * @return \PDO
     */
    public static function getInstance()
    {
        if(!isset(self::$instance))
        {
            try
            {
                self::$instance = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
                self::$instance->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            }
            catch (\PDOException $e)
            {
                echo $e->getMessage();
            }
        }

        return self::$instance;
    }

    /**
     * @param $command
     * @return \PDOStatement
     */
    public static function prepare($command)
    {
        return self::getInstance()->prepare($command);
    }

    /**
     * @return string
     */
    public static function lastInsertId()
    {
        return self::getInstance()->lastInsertId();
    }
}