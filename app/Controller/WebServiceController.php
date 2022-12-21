<?php

namespace App\Controller;

use Core\Controller\Controller;
use App\config\ControleObjetConfig;

class WebServiceController extends AppController
{

    protected $tableName;
    protected $Nom_Tab;

    public function __construct()
    {
        parent::__construct();
        $this->loadModel('Nom_Tab');// Chargement du modèle qui fait office de moule pour créer les items et les renvoyer en format objet. (Spécifique au webservice)
    }




}

