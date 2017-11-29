<?php

/**
 * Classe de conexão com o banco de dados.
 *
 * @author     Kaio Teixeira
 */

namespace Configs;
require_once 'database.php';

class Connection
{
    private static $instance;

    /**
     * Método cria a intância do PDO para acesso ao banco
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
     * Método que prepara a instância do PDO com um comando SQL
     * @param $command
     * @return \PDOStatement
     */
    public static function prepare($command)
    {
        return self::getInstance()->prepare($command);
    }

    /**
     * Método de abstração do método lastInsertId do PDO
     * @return string
     */
    public static function lastInsertId()
    {
        return self::getInstance()->lastInsertId();
    }
}