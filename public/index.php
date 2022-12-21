<?php
	
use Core\Config;

define('ROOT', dirname(__DIR__));
require ROOT . '/app/App.php';
App::load();

$debutGlobal = microtime(true);
$_SESSION['debutGlobal'] = $debutGlobal;

require_once("../config/config.php");

$_SESSION['delaiGlobal'] = "";


$config = Config::getInstance(ROOT . '/config/config.php');
$arrayConfig = (array)$config;
foreach ($arrayConfig as $arrayConfigKey => $arrayConfigVal) {
    $projet = $arrayConfigVal['name_projet'];
}

if (isset($_SESSION['UserID']) && !empty($_SESSION['UserID']) && isset($_SESSION['name_projet_user']) && $_SESSION['name_projet_user'] == $projet) {
	
    if (isset($_GET['p']) && $_GET['p'] != null) {
        $page = explode('.', $_GET['p']);
        if (array_key_exists(0, $page) && array_key_exists(1, $page)) {
            if (!preg_match("#[^A-Za-z0-9_\.]#", $page[0]) && !preg_match("#[^A-Za-z0-9_\.]#", $page[1])) {
                $controller = '\App\Controller\\' . ucfirst($page[0]) . 'Controller';
                $verifController = '../app/Controller/' . ucfirst($page[0]) . 'Controller';
                if (file_exists($verifController . '.php')) {
                    $action = $page[1];
                    $class_methods = get_class_methods(new $controller());
                    foreach ($class_methods as $method_name) {
                        if ($method_name == $action) {
                            $list = $method_name;
                        }
                    }
                }
            }
        }
		
        if (isset($list) && $list != null && $controller != null) {
			
            $controller = new $controller();

            $finGlobal = microtime(true);
            $delaiGlobal = $finGlobal - $debutGlobal;
            $_SESSION['delaiGlobal'] = substr($delaiGlobal, 0, 4);

            $controller->$action();
			
        } else {

            $controller = '\App\Controller\PrincipalController';
            $action = 'notFound';
            $controller = new $controller();

            $finGlobal = microtime(true);
            $delaiGlobal = $finGlobal - $debutGlobal;
            $_SESSION['delaiGlobal'] = substr($delaiGlobal, 0, 4);

            $controller->$action();
        }
    } else {
        $controller = '\App\Controller\\' . ucfirst('Principal') . 'Controller';
        $action = 'index';
        $controller = new $controller();

        $finGlobal = microtime(true);
        $delaiGlobal = $finGlobal - $debutGlobal;
        $_SESSION['delaiGlobal'] = substr($delaiGlobal, 0, 4);

        $controller->$action();
    }
} else {
    if (isset($_GET['p']) && $_GET['p'] != null) {

        $page = explode('.', $_GET['p']);

        if (array_key_exists(0, $page) && array_key_exists(1, $page)) {

            if (!preg_match("#[^A-Za-z0-9_\.]#", $page[0]) && !preg_match("#[^A-Za-z0-9_\.]#", $page[1])) {
                if ($page[0] == 'WebService') {
                    $controller = '\App\Controller\\' . ucfirst('WebService') . 'Controller';
                    $action = $page[1];
                    $class_methods = get_class_methods(new $controller());
                    foreach ($class_methods as $method_name) {
                        if ($method_name == $action) {
                            $listWebService = $method_name;
                        }
                    }
                } else if ($page[0] == 'CasParticulier' && ($page[1] == 'debriefingTicket' || $page[1] == 'updateTicketStateDebrief' || $page[1]== 'deleteTokenAfterDebrief')) {
                    $controller = '\App\Controller\\' . ucfirst('CasParticulier') . 'Controller';
                    $action = $page[1];
                    $class_methods = get_class_methods(new $controller());
                    foreach ($class_methods as $method_name) {
                        if ($method_name == $action) {
                            $listWebService = $method_name;
                        }
                    }
                }
            }
            if (isset($listWebService) && $listWebService != null && $controller != null) {
                $controller = new $controller();

                $finGlobal = microtime(true);
                $delaiGlobal = $finGlobal - $debutGlobal;
                $_SESSION['delaiGlobal'] = substr($delaiGlobal, 0, 4);

                $controller->$action();
            } else {
                $controller = '\App\Controller\\' . ucfirst('Users') . 'Controller';
                $action = 'login';
                $controller = new $controller();

                $finGlobal = microtime(true);
                $delaiGlobal = $finGlobal - $debutGlobal;
                $_SESSION['delaiGlobal'] = substr($delaiGlobal, 0, 4);

                $controller->$action();
            }
        } else {
            $controller = '\App\Controller\\' . ucfirst('Users') . 'Controller';
            $action = 'login';
            $controller = new $controller();

            $finGlobal = microtime(true);
            $delaiGlobal = $finGlobal - $debutGlobal;
            $_SESSION['delaiGlobal'] = substr($delaiGlobal, 0, 4);

            $controller->$action();
        }
    } else {
        $controller = '\App\Controller\\' . ucfirst('Users') . 'Controller';
        $action = 'login';
        $controller = new $controller();

        $finGlobal = microtime(true);
        $delaiGlobal = $finGlobal - $debutGlobal;
        $_SESSION['delaiGlobal'] = substr($delaiGlobal, 0, 4);

        $controller->$action();
    }
}


