<?php

use Core\Config;
use Core\Database\MysqlDatabase;



class App
{

    public $title = '';
    private $db_instance;
    private static $_instance;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new App();
        }
        return self::$_instance;
    }

    public static function load()
    {
		
		
		//define("SESSION_PROJET", base64_encode(__DIR__));
		//session_name(SESSION_PROJET);
		
        session_start();
        require ROOT . '/app/Autoloader.php';
        App\Autoloader::register();

        require ROOT . '/core/Autoloader.php';
        Core\Autoloader::register();
    }

    public function getTable($name)
    {
        $class_name = '\\App\\Table\\' . ucfirst($name) . 'Table';
        return new $class_name($this->getDb());
    }

    public function getDb()
    {
        $config = Config::getInstance(ROOT . '/config/config.php');
        if (is_null($this->db_instance)) {
            $this->db_instance = new MysqlDatabase($config->get('db_name'), $config->get('db_user'), $config->get('db_pass'), $config->get('db_host'), $config->get('db_charset'), $config->get('db_DebugMode'), $config->get('db_LocalTime'));
        }
        return $this->db_instance;
    }

    public function showtable()
    {

        $bdd = $this->getDb();
        $result = $bdd->query("show tables");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $nomTables = $row[0];
        }
        return $nomTables;
    }

}

