<?php

namespace App\Controller;

use App;
use App\config\FpdfConfig;
use App\config\PDF;
use Core\Auth\DBAuth;
use Core\Config;
use Core\Controller\Controller;
use Core\HTML\BootstrapForm;
use App\config\ControleObjetConfig;

require_once("../config/Fields.php");
require_once("../config/FieldsHistorique.php");
require_once("../config/TypeMIME.php");
require_once("../config/config.php");

$config = Config::getInstance(ROOT . '/config/config.php');
$arrayConfig = (array)$config;
foreach ($arrayConfig as $arrayConfigKey => $arrayConfigVal) {
    $_SESSION['db_DebugMode'] = $arrayConfigVal['db_DebugMode'];
    $_SESSION['db_LocalTime'] = $arrayConfigVal['db_LocalTime'];
    $_SESSION['url_projet'] = $arrayConfigVal['url_projet'];
    $_SESSION['mail_from'] = $arrayConfigVal['mail_from'];
}

class CasParticulierController extends AppController
{

    protected $tableName;
    protected $Nom_Tab;
    CONST TIME_TOKEN_LIFE = 86400;

    public function __construct()
    {
        parent::__construct();
        $this->loadModel('Nom_Tab'); // Chargement du modèle qui fait office de moule pour créer les items et les renvoyer en format objet.
    }

    public function treatment()
    {
        $form = new BootstrapForm($_POST);
        $this->render('CasParticulier.TraitementData', compact(
            'form'
        ));
        exit();
    }



}
