<?php

namespace App\Controller;

use App;
use Core\Auth\DBAuth;
use Core\Config;
use Core\Controller\Controller;
use Core\HTML\BootstrapForm;
use App\config\ControleObjetConfig;

require_once("../config/FieldsUsers.php");
require_once("../config/FieldsUserProfil.php");
require_once("../config/FieldsHistorique.php");
require_once("../config/TypeMIME.php");
require_once("../config/Fields.php");

$config = Config::getInstance(ROOT . '/config/config.php');
$arrayConfig = (array)$config;
foreach ($arrayConfig as $arrayConfigKey => $arrayConfigVal) {
    $_SESSION['db_DebugMode'] = $arrayConfigVal['db_DebugMode'];
    $_SESSION['db_LocalTime'] = $arrayConfigVal['db_LocalTime'];
    $_SESSION['name_projet'] = $arrayConfigVal['name_projet'];
}

class UsersController extends AppController
{ // UsersController hérite des méthodes protégées de la class parent AppController.(Dossier *racine*/app/App.php)

    protected $tableName;
    protected $Nom_Tab;

    CONST PER_PAGE = 25; // Nombre de résultats affichés par default.(pagination)
    CONST MAX_PER_PAGE = 350; // Evite que un utilisateur affiche + de 350 résultats en modifiant la valeur du limit dans l'url.(pagination)
    CONST HISTORY_DAYS = 30; // Nombre de jours pour le delete des tuples de l'historique.
    CONST TEMP_FILE_MINUTES = 30; // Nombre de minute ou les fichiers mit en table temporaire et en dossier temporaire doivent être supprimés.
    CONST MAX_SITE = 100;
    CONST VERSION = 'O7-04-2020 V 1.1';

    public function __construct()
    {
        parent::__construct();
        $this->loadModel('Nom_Tab'); // Chargement du modèle qui fait office de moule pour créer les items et les renvoyer en format objet.
    }

    public function notFound()
    {
        $this->render('Principal.notFound');
        exit();
    }

    public function switchSession()
    {
        if ($_SESSION['Droit'] == 10000 && !isset($_GET['switch'])) {
            $_SESSION['SuperAdmin'] = $_SESSION['UserID'];
            $_SESSION['SuperAdminDroit'] = $_SESSION['Droit'];
            $users = $this->Nom_Tab->findItem('SELECT 
                users.id,
                users.nom,
                users.prenom,
                users.email,
                users.login,
                users.roleId,
                users_roles.role_nom as profil
                FROM users
                LEFT JOIN users_roles on users.roleId = users_roles.id
                WHERE users.id = ' . intval($_GET['id']), true);


            $_SESSION["user_depts"] = array();

            $dept = $this->Nom_Tab->query('SELECT t1.id,t1.code_vc FROM (user_dept AS t0,secto_departements AS t1) WHERE t0.id_dept=t1.id AND t0.id_user=' . $users[0]->id);

            if ($dept) {
                foreach ($dept as $deptKey => $deptVal) {
                    $_SESSION["user_depts"][$deptVal->id] = $deptVal->code_vc;
                }

            }
			
			$_SESSION["user_backups_to"] = array();
				$backups = $this->Nom_Tab->query('SELECT t0.id FROM (users AS t0) WHERE t0.actif=1 AND (t0.backup1='.$users[0]->id.' OR t0.backup2='.$users[0]->id.' OR t0.backup3='.$users[0]->id.' OR t0.backup4='.$users[0]->id.')');
				if ($backups) {
                    foreach ($backups as $backupsK => $backupsV) {
                        $_SESSION["user_backups_to"][$backupsV->id] = $backupsV->id;
                    }
                }
            $_SESSION['pole']  = '';
            if(!in_array($users[0]->roleId, [1,10000]) ){
                $get_hie = $this->Nom_Tab->query('SELECT hie_poste_id,hie_poste,hie_sous_service_id FROM cov_hierarchie WHERE id ='.$users[0]->id);
                if(isset($get_hie)&& !empty($get_hie)){
                    $users[0]->roleId = $get_hie[0]->hie_poste_id;
                    $users[0]->profil = $get_hie[0]->hie_poste;
                    $_SESSION['pole']  = $get_hie[0]->hie_sous_service_id;

                }
            }

            $_SESSION['debug'] = array();
            $_SESSION['debug']['Update'] = null;
            $_SESSION['debug']['Insert'] = null;

            $_SESSION['UserID'] = $users[0]->id;
            $_SESSION['login'] = $users[0]->login;
            $_SESSION['email'] = $users[0]->email;
            $_SESSION['Droit'] = $users[0]->roleId;
            $_SESSION['nom'] = $users[0]->nom;
            $_SESSION['prenom'] = $users[0]->prenom;
            $_SESSION['profil'] = $users[0]->profil;
            $_SESSION['name_projet_user'] = $_SESSION['name_projet'];


            header('Location: index.php?p=Principal.index');

            exit();
        } else {

            if (isset($_GET['switch']) && isset($_SESSION['SuperAdminDroit']) && $_GET['switch'] == 1 && $_SESSION['SuperAdminDroit'] == 10000) {
                $users = $this->Nom_Tab->findItem('SELECT 
                users.id,
                users.nom,
                users.prenom,
                users.email,
                users.login,
                users.roleId,
                users_roles.role_nom as profil
                FROM users
                LEFT JOIN users_roles on users.roleId = users_roles.id
                WHERE users.id = ' . intval($_SESSION['SuperAdmin']), true);

                $_SESSION["user_depts"] = array();
                $dept = $this->Nom_Tab->query('SELECT t1.id,t1.code_vc FROM (user_dept AS t0,secto_departements AS t1) WHERE t0.id_dept=t1.id AND t0.id_user=' . $users[0]->id);

                if ($dept) {
                    foreach ($dept as $deptKey => $deptVal) {
                        $_SESSION["user_depts"][$deptVal->id] = $deptVal->code_vc;
                    }
                }
                $_SESSION['debug'] = array();
                $_SESSION['debug']['Update'] = null;
                $_SESSION['debug']['Insert'] = null;

                $_SESSION['UserID'] = $users[0]->id;
                $_SESSION['login'] = $users[0]->login;
                $_SESSION['email'] = $users[0]->email;
                $_SESSION['Droit'] = $users[0]->roleId;
                $_SESSION['nom'] = $users[0]->nom;
                $_SESSION['prenom'] = $users[0]->prenom;
                $_SESSION['profil'] = $users[0]->profil;
                $_SESSION['name_projet_user'] = $_SESSION['name_projet'];

				
                if ($users) {
                    unset($_SESSION['SuperAdmin']);
                    unset($_SESSION['SuperAdminDroit']);
                    header('Location: index.php?p=Principal.index');
                    exit();
                } else {
                    header('Location: index.php?p=Principal.notFound');
                    exit();
                }
            } else {
                header('Location: index.php?p=Principal.notFound');
                exit();
            }

        }
    }

    public function login()
    {

        if (!empty($_POST) && isset($_POST['identifiant']) && isset($_POST['password'])) {
            /*-------------------------------------------------------------------------------------------------------*/
            /* Vérification du login --------------------------------------------------------------------------------*/
            /*-------------------------------------------------------------------------------------------------------*/
            $errors = new ControleObjetConfig(); // L'objet ControleObjetConfig est dans le dossier App/config.
            $errors->validIdentifiant($_POST['identifiant'], $_POST['password']);

            /* Parcours de l'objet errors ---------------------------------------------------------------------------*/
            $NbErrors = 0;
            foreach ($errors->errors as $error => $value) {
                if ($value != null) {
                    $NbErrors++;
                }
            }
            /*-------------------------------------------------------------------------------------------------------*/
            /* En cas de non erreur dans la vérification de l'objet errors ------------------------------------------*/
            /*-------------------------------------------------------------------------------------------------------*/
            if ($NbErrors === 0) {
                $auth = new DBAuth(App::getInstance()->getDb()); // L'objet DBAuth est dans le dossier core/Auth fichier DBAuth.php (permet de récupérer l'instance en cour ou de créer une seule instance si elle existe pas).

                $valid = $auth->loginAuth($_POST['identifiant'], $_POST['password']);
                // Si User exist et que les identifiants sont OK.
                if ($valid == 0) {
                    header('Location: index.php?p=Principal.index');
                    exit();

                } else {
                    $errors = new ControleObjetConfig();
                    $errors->getErrorLogin();
                }
            }
        }
        if (!isset($errors) || empty($errors)) {
            $errors = null;
        }
        $form = new BootstrapForm($_POST);
        $this->render('Principal.login', compact('form', 'errors'));
        exit();
    }

    public function myProfil()
    {
        $debut = microtime(true);

        // Récup du fichier conf Utilisateurs (dossier *racine*/conf/FieldsUsers.php).
        $tabXlsFields = listParamUserProfil();
        $id = $tabXlsFields['Profil']['bdd_id'];
        $table = $tabXlsFields['Profil']['bdd_table'];
        $champs = $tabXlsFields['Profil']['champs'];
        $nom_feuille = $tabXlsFields['Profil']['nom_feuille'];

        // Récup du fichier conf Historique (dossier *racine*/conf/FieldsHistorique.php).
        $tabXlsFieldsHistorique = listParamHistorique();
        $idHistorique = $tabXlsFieldsHistorique['Historique']['bdd_id'];
        $tableHistorique = $tabXlsFieldsHistorique['Historique']['bdd_table'];
        $champsHistorique = $tabXlsFieldsHistorique['Historique']['champs'];
        $nom_feuilleHistorique = $tabXlsFieldsHistorique['Historique']['nom_feuille'];

        if (!empty($_POST)) {

            /*-------------------------------------------------------------------------------------------------------*/
            /* Vérification des champs issue du formulaire ----------------------------------------------------------*/
            /*-------------------------------------------------------------------------------------------------------*/
            $errors = new ControleObjetConfig(); // L'objet controleForm est dans le dossier *racine*/App/config.
            $errors->existSession($_SESSION['UserID']); // Verifie avant d'éxécuter une méthode si User est connecté.(sa évite qu'un formulaire non issue de l'application soit soumis à la place)

            $edit = 1; // Cette variable permet à la fonction de vérification du mail de différencier si c'est une mise à jour ou une nouvel insertion.

            // Parcours du fichier conf.
            foreach ($champs as $c => $cc) {

                if ($cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1) {

                    if (isset($_POST["item" . $c])) {
                        $_POST[$c] = $_POST["item" . $c];
                    }
                    if (isset($_POST[$c . "_date1"]) && isset($_POST[$c . "_date2"]) && !empty($_POST[$c . "_date1"]) && !empty($_POST[$c . "_date2"])) {
                        $_POST[$c] = $_POST[$c . "_date1"] . " " . $_POST[$c . "_date2"] . ":00";
                    }
                    if (!isset($cc['controle_balise']) || empty($cc['controle_balise'])) {
                        $cc['controle_balise'] = 0;
                    }
                    if ($_POST[$c] == '') {
                        $_POST[$c] = null;
                    }

                    switch ($cc['type_input']) :
                        case "text":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validVarchar($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "textarea":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validVarchar($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "date":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validDate($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "datetime":
                            if ($_POST[$c . "_date1"] != '' && $_POST[$c . "_date2"] == '' && !isset($_POST[$c])) {
                                $errors->invalidDateTime($c);

                            } elseif ($_POST[$c . "_date1"] == '' && $_POST[$c . "_date2"] != '' && !isset($_POST[$c])) {

                                $errors->invalidDateTime($c);
                            } else {
                                if (!isset($_POST[$c])) {
                                    $_POST[$c] = null;
                                }
                                // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                                $errors->validDateTime($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);

                            }
                            break;
                        case "number":
                            if (!isset($_POST[$c]) || $_POST[$c] == '') {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validInt($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "phone":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validPhone($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "password":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validPassword($_POST[$c], $c, 0, $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "email":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validMail($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "url":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validUrl($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "radio":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validRadio($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "checkbox":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validCheckbox($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        case "file":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validFile($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_SESSION['UserID']);
                            break;
                        default:

                    endswitch;


                }
            }

            /* Parcours de l'objet errors ----------------------------------------------------------------------------*/
            $NbErrors = 0;
            foreach ($errors->errors as $error => $value) {
                if ($value != null) {
                    $NbErrors++;
                }
            }
            /*--------------------------------------------------------------------------------------------------------*/
            /* En cas de non erreur dans la vérification des champs issue du formulaire ------------------------------*/
            /*--------------------------------------------------------------------------------------------------------*/
            if ($NbErrors === 0) {

                /*----------------------------------------------------------------------------------------------------*/
                /* Préparation des données pour la mise à jour de l'item ---------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/

                // Préparation des colonnes et des valeurs à envoyer à la fonction update.
                $colInsert = array();
                $valueInsert = array();
                $valueParam = array();

                // Parcours du fichier conf (dossier *racine*/conf/FieldsUsers.php).
                foreach ($champs as $c => $cc) {
                    if ($cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && $_POST[$c] != null) {
                        // Condition qui permet d'isoler le champs mot de passe pour lui attribuer un cryptage.
                        if ($c == 'password') {
                            if (isset($_POST[$c]) || !empty($_POST[$c])) {
                                $_POST[$c] = password_hash($_POST[$c], PASSWORD_BCRYPT);
                                $colIn = $c;
                                $colvalue = ":" . $c;
                                $value = $_POST[$c];
                            }
                        } else {
                            $colIn = $c;
                            $colvalue = ":" . $c;
                            $value = $_POST[$c];
                        }

                        if (isset($colIn) && $colIn != null) {
                            array_push($colInsert, $colIn); // array contenant les colonnes pour l'insertion.
                            array_push($valueInsert, $colvalue); // array contenant le nom des paramètres pour la requete préparé.
                            array_push($valueParam, $value); // array contenant les valeurs à attribuer au paramètres.
                        }


                    }
                }
                // Mise à jour du user dans la base de données.
                $result = $this->Nom_Tab->updateitem($id, $table, $colInsert, $valueInsert, $valueParam, $_SESSION['UserID']); // La fonction updateitem est dans le dossier *racine*/Core/Table fichier Table.php.

                /* Fin de la mise à jour de l'item -------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/


                // Redirection si tout est OK.
                if ($result) {
                    $succes = 1; // Variable envoyée pour afficher le succès dans la vue.
                    // Récup des nouvelles données de l'item.
                    $req = $this->Nom_Tab->findItemQuery($id, $table, $champs, $_SESSION['UserID']);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                    $donnees = $this->Nom_Tab->findItem($req);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                    $donnees = $donnees[0];

                    // Si l'item existe en base de données.
                    if ($donnees != false) {
                        $options = array();
                        $reqOptionsP = array();
                        // Parcours du fichier conf Utilisateur.
                        foreach ($champs as $auto_c => $a_c) {
                            // Condition qui identifie si les champs possède un type , si le type est de type sql (jointure) et qui sont en autocomplétion.
                            if (isset($a_c['type']) && $a_c['type'] == "list_sql" && $a_c['autocomplete'] == 1 && isset($_GET['conf']) && $_GET['conf'] == $a_c['bdd_table']) {

                                // Récup les valeurs à afficher dans l'autocomplétion.
                                $option = json_encode($this->Nom_Tab->autoCompletion($_GET['champs'], $_GET['conf'])); // La fonction autoCompletion est dans le dossier *racine*/core/Table fichier Table.php.

                                // Condition si le type est sql (jointure) sans autocomplétion.
                            } elseif (isset($a_c['type']) && $a_c['type'] == "list_sql") {
                                $options[$auto_c] = array();
                                // Récup les valeurs à afficher dans le select(html).

                                $reqOptions = $this->Nom_Tab->allItemsForSelectQuery($a_c);
                                $option = $this->Nom_Tab->allItemsForSelect($reqOptions);

                                $options[$auto_c] = $option;
                                array_push($reqOptionsP, $reqOptions);
                            }
                        }


                        // Si l'autocomplétion a été déclanchée.
                        if (isset($_GET['conf']) && !empty($_GET['conf'])) {
                            echo $option;
                        } else {
                            if (!isset($errors) || empty($errors)) {
                                $errors = null;
                            }
                            if (!isset($champ) || empty($champ)) {
                                $champ = null;
                            }
                            if (!isset($requpdate) || empty($requpdate)) {
                                $requpdate = null;
                            }
                            if (!isset($historique) || empty($historique)) {
                                $historique = null;
                            }
                            if (!isset($option) || empty($option)) {
                                $option = null;
                            }
                            if (!isset($reqOptionsP) || empty($reqOptionsP)) {
                                $reqOptionsP = null;
                            }
                            $fin = microtime(true);
                            $delai = $fin - $debut;
                            $delai = substr($delai, 0, 4);
                            $form = new BootstrapForm($donnees);
                            $ListDepAff = $this->Nom_Tab->findItem('SELECT user_dept.id, user_dept.id_user, user_dept.id_dept FROM user_dept WHERE user_dept.id_user=' . $_SESSION['UserID']);
                            $ListDep = array();
                            foreach ($ListDepAff as $ListDepAffKey => $ListDepAffVal) {
                                $ListDepRes = $this->Nom_Tab->findItem('SELECT secto_departements.id, secto_departements.code_vc FROM secto_departements WHERE secto_departements.id=' . $ListDepAffVal->id_dept);
                                array_push($ListDep, $ListDepRes);
                            }

                            $ListSectoAff = $this->Nom_Tab->findItem('SELECT user_site.id_user, user_site.id_site FROM user_site WHERE user_site.id_user =' . $_SESSION['UserID']);
                            $ListSecto = array();
                            foreach ($ListSectoAff as $ListSectoAffKey => $ListSectoAffVal) {
                                $ListSectoRes = $this->Nom_Tab->findItem('SELECT nros.id, nros.name FROM nros WHERE nros.id =' . $ListSectoAffVal->id_site);
                                array_push($ListSecto, $ListSectoRes);
                            }
							
							


                            $debugMode = $_SESSION['db_DebugMode'];
                            $LocalTime = $_SESSION['db_LocalTime'];


                            $_SESSION['succes'] = 1;
                            header('Location: index.php?p=Users.myProfil');
                            //  $this->render('Users.myProfil', compact('donnees', 'ListSecto','ListDep', 'LocalTime','reqOptionsP','debugMode', 'historique','delai', 'req', 'requpdate', 'options', 'tabXlsFields', 'champs', 'form', 'delai', 'req', 'nom_feuille', 'nom_feuilleHistorique', 'succes'));
                            exit();
                        }
                    } else {
                        $this->render('Principal.notFound');
                        exit();
                    }
                }
            }
            /*--------------------------------------------------------------------------------------------------------*/
            /* Fin de mise à jour de l'item, d'insertion des modification dans l'historique + redirection terminée ---*/
            /* Pour rappel (la partie ci-dessus traite et concerne uniquement le cas ou le formulaire à été soumis). -*/
            /*--------------------------------------------------------------------------------------------------------*/
        }


        /*------------------------------------------------------------------------------------------------------------*/
        /* Préparation des données pour l'affichage ------------------------------------------------------------------*/
        /*------------------------------------------------------------------------------------------------------------*/
        $options = array();
        $reqOptionsP = array();
        // Parcours du fichier conf Utilisateur.
        foreach ($champs as $auto_c => $a_c) {
            // Condition qui identifie si les champs possède un type , si le type est de type sql (jointure) et qui sont en autocomplétion.
            if (isset($a_c['type']) && $a_c['type'] == "list_sql" && $a_c['autocomplete'] == 1 && isset($_POST["inputItem"])) {
                // Récup les valeurs à afficher dans l'autocomplétion.
                $option = json_encode($this->Nom_Tab->autoCompletion($_GET['champs'], $_GET['conf'])); // La fonction autoCompletion est dans le dossier *racine*/core/Table fichier Table.php.
            } elseif (isset($a_c['type']) && $a_c['type'] == "list_sql") {
                $options[$auto_c] = array();
                // Récup les valeurs à afficher dans le select(html).

                $reqOptions = $this->Nom_Tab->allItemsForSelectQuery($a_c);
                $option = $this->Nom_Tab->allItemsForSelect($reqOptions);

                $options[$auto_c] = $option;
                array_push($reqOptionsP, $reqOptions);
            }
        }

        if (isset($_SESSION['UserID']) || !empty($_SESSION['UserID'])) {
            $req = $this->Nom_Tab->findItemQuery($id, $table, $champs, $_SESSION['UserID']);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
            $donnees = $this->Nom_Tab->findItem($req);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
            if ($donnees) {
                $donnees = $donnees[0];
            }
        }

        // Condition si l'item existe bien en base de données.
        if ($donnees != false) {
            if (!isset($errors) || empty($errors)) {
                $errors = null;
            }
            if (!isset($champ) || empty($champ)) {
                $champ = null;
            }
            if (!isset($requpdate) || empty($requpdate)) {
                $requpdate = null;
            }
            if (!isset($historique) || empty($historique)) {
                $historique = null;
            }
            if (!isset($option) || empty($option)) {
                $option = null;
            }
            if (!isset($reqOptionsP) || empty($reqOptionsP)) {
                $reqOptionsP = null;
            }
            $fin = microtime(true);
            $delai = $fin - $debut;
            $delai = substr($delai, 0, 4);
            // Affichage en vue.
            $form = new BootstrapForm($donnees);
            $ListDepAff = $this->Nom_Tab->findItem('SELECT user_dept.id, user_dept.id_user, user_dept.id_dept FROM user_dept WHERE user_dept.id_user=' . $_SESSION['UserID']);
            $ListDep = array();
            foreach ($ListDepAff as $ListDepAffKey => $ListDepAffVal) {
                $ListDepRes = $this->Nom_Tab->findItem('SELECT secto_departements.id, secto_departements.code_vc FROM secto_departements WHERE secto_departements.id=' . $ListDepAffVal->id_dept);
                array_push($ListDep, $ListDepRes);
            }

            $ListSectoAff = $this->Nom_Tab->findItem('SELECT user_site.id_user, user_site.id_site FROM user_site WHERE user_site.id_user =' . $_SESSION['UserID']);
            $ListSecto = array();
            foreach ($ListSectoAff as $ListSectoAffKey => $ListSectoAffVal) {
                $ListSectoRes = $this->Nom_Tab->findItem('SELECT nros.id, nros.name FROM nros WHERE nros.id =' . $ListSectoAffVal->id_site);
                array_push($ListSecto, $ListSectoRes);
            }
            $debugMode = $_SESSION['db_DebugMode'];
            $LocalTime = $_SESSION['db_LocalTime'];
            $this->render('Users.myProfil', compact('donnees', 'ListSecto', 'ListDep', 'LocalTime', 'reqOptionsP', 'debugMode', 'historique', 'delai', 'req', 'requpdate', 'errors', 'options', 'tabXlsFields', 'champs', 'form', 'delai', 'req', 'nom_feuille', 'nom_feuilleHistorique'));
            exit();
        } else {
            $this->render('Principal.notFound');
            exit();
        }
    }

    public function disconnect()
    {
		//session_name(SESSION_PROJET);
        session_start();
		
		
        if (isset($_SESSION['UserID'])) {
            // Suppression des variables de session et de la session
            $_SESSION = array();
            session_destroy();
            header('Location: index.php');
            exit();
        } else {
            header('Location: index.php');
            exit();
        }
    }

    public function listUsers()
    {

        $debut = microtime(true);

        if (!isset($_GET['XLS'])) {
            $_GET['XLS'] = 'Users';
        }

        // Récup du fichier conf (dossier *racine*/conf/FieldsUsers.php).
        $tabXlsFields = listParamUsers();
        $search_array = $tabXlsFields;

        // Verifie si la conf correspondant à la page demmandé existe.
        if (!isset($_GET['XLS']) || empty($_GET['XLS']) || !array_key_exists($_GET['XLS'], $search_array)) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        }

        if (!isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'])) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        } elseif (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'] != 1) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        }


        if (isset($_GET['delFilter']) && $_GET['delFilter'] == 1) {
            unset($_SESSION[$_GET['XLS']]);
            unset($_GET['delFilter']);
        }


        if (isset($_GET['XLS'])) {
            $_SESSION[$_GET['XLS']]['XLS'] = $_GET['XLS'];
        }
        if (isset($_GET['collapsId'])) {
            $_SESSION[$_GET['XLS']]['collapsId'] = $_GET['collapsId'];
        }
        if (isset($_GET['filtre'])) {
            $_SESSION[$_GET['XLS']]['filtre'] = $_GET['filtre'];
        }
        if (isset($_GET['order'])) {
            $_SESSION[$_GET['XLS']]['order'] = $_GET['order'];
        }
        if (isset($_GET['champ'])) {
            $_SESSION[$_GET['XLS']]['champ'] = $_GET['champ'];
        }

        if (isset($_GET['search'])) {
            $_GET['search'] = trim($_GET['search']);
        }
        if (isset($_GET['search'])) {
            $_SESSION[$_GET['XLS']]['search'] = $_GET['search'];
        }

        if (isset($_GET['ParPage'])) {
            $_SESSION[$_GET['XLS']]['ParPage'] = $_GET['ParPage'];
        }
        if (isset($_GET['page'])) {
            $_SESSION[$_GET['XLS']]['page'] = $_GET['page'];
        }


        // Récup du fichier conf Utilisateurs (dossier *racine*/conf/FieldsUsers.php).
        $tabXlsFields = listParamUsers();

        // Verifie si la conf correspondant à la page demmandé existe.
        if (!isset($tabXlsFields['Users']) || empty($tabXlsFields['Users'])) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        }

        $erreur = array();
        // Verifie si les clés obligatoire sont bien présentes.
        if (!isset($tabXlsFields['Users']['profilSql']) || empty($tabXlsFields['Users']['profilSql'])) {
            $erreur['profilSql'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields['Users']['bdd_table']) || empty($tabXlsFields['Users']['bdd_table'])) {
            $erreur['bdd_table'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields['Users']['champs']) || empty($tabXlsFields['Users']['champs'])) {
            $erreur['champs'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields['Users']['nom_feuille']) || empty($tabXlsFields['Users']['nom_feuille'])) {
            $erreur['nom_feuille'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields['Users']['pagination']) || empty($tabXlsFields['Users']['pagination'])) {
            $erreur['pagination'] = "manquant ou vide";
        }


        if (isset($tabXlsFields['Users']['profilSql'])) {

            if (!isset($tabXlsFields['Users']['profilSql'][$_SESSION['Droit']])) {
                $erreur['Le profil utilisateurs'] = "manquant ou n'est pas autorisé à voir la page";
            }

        }
        $TabListMIME = listParamTypeMIME();
        $ListeTypeMIME = $TabListMIME['TypeMIME']['listeTypeMIME'];
        $tableFile = $tabXlsFields['Users']['bdd_table_file'];

        if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['custom_list_edit'])) {
            $custom_list_edit = $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['custom_list_edit'];
        } else {
            $custom_list_edit = null;
        }

        if ($erreur != null) {
            $erreur = serialize($erreur);
            header('Location: index.php?p=Principal.notFound&erreurConf=' . $erreur . '');
            exit();
        } else {
            // Si les clés sont présent.
            $profilSql = $tabXlsFields['Users']['profilSql'];
            $table = $tabXlsFields['Users']['bdd_table'];
            $champs = $tabXlsFields['Users']['champs'];
            $nom_feuille = $tabXlsFields['Users']['nom_feuille'];
            $label_id = $tabXlsFields['Users']['bdd_id'];

            foreach ($champs as $souschamps => $sc) {

                if(isset($sc['type_input']) && $sc['type_input'] != 'categorie' && $sc['type_input'] != 'sous_categorie') {

                    if (!isset($sc['nom']) || empty($sc['nom'])) {
                        $erreur['nom'] = " manquant ou vide";
                    } elseif (!isset($sc['type_input']) || empty($sc['type_input'])) {
                        $erreur['type_input'] = " manquant ou vide";
                    } elseif (!isset($sc['taille_max']) || empty($sc['taille_max'])) {
                        $erreur['taille_max'] = " manquant ou vide";
                    } elseif (!isset($sc['taille_min']) || empty($sc['taille_min'])) {
                        if ($sc['taille_min'] == 0) {
                        } else {
                            $erreur['taille_min'] = " manquant ou vide";
                        }
                    }

                }
            }

            if ($erreur != null) {
                $erreur = serialize($erreur);
                header('Location: index.php?p=Principal.notFound&erreurConf=' . $erreur . '');
                exit();
            }
        }



        /*------------------------------------------------------------------------------------------------------------*/
        /* Champs concérnant les filtres de recherche. ---------------------------------------------------------------*/
        // Récup des paramètres de la pagination dans le fichier conf (dossier *racine*/conf/FieldsUsers.php).
        $pagination = $tabXlsFields[$_GET['XLS']];
        // Page courante de la pagination.
        if (!isset($_GET['page']) && !isset($_SESSION[$_GET['XLS']]['page'])) {
            $page = 1;
        } else {
            if (isset($_GET['page'])) {
                $page = abs($_GET['page']);
            } elseif (isset($_SESSION[$_GET['XLS']]['page']) && !isset($_GET['page'])) {
                $page = abs($_SESSION[$_GET['XLS']]['page']);
            } else {
                $page = 1;
            }
        }

        // Nombre de données affiché par page.
        if (!isset($_GET['ParPage']) && !isset($_SESSION[$_GET['XLS']]['ParPage'])) {
            $ParPage = self::PER_PAGE;;
        } else {

            if (isset($_SESSION[$_GET['XLS']]['ParPage']) && !isset($_GET['ParPage'])) {
                $_SESSION[$_GET['XLS']]['ParPage'] = intval($_SESSION[$_GET['XLS']]['ParPage']);
                if (is_int($_SESSION[$_GET['XLS']]['ParPage']) && $_SESSION[$_GET['XLS']]['ParPage'] < self::MAX_PER_PAGE && $_SESSION[$_GET['XLS']]['ParPage'] > 0) {
                    $ParPage = $_SESSION[$_GET['XLS']]['ParPage'];
                } else {
                    $ParPage = self::PER_PAGE;
                }
            } else {
                $_GET['ParPage'] = intval($_GET['ParPage']);
                if (is_int($_GET['ParPage']) && $_GET['ParPage'] < self::MAX_PER_PAGE && $_GET['ParPage'] > 0) {
                    $ParPage = $_GET['ParPage'];
                } else {
                    $ParPage = self::PER_PAGE;
                }
            }
        }

        // Indique si un filtre à été soumis.
        if (isset($_GET['filtre']) && !isset($_SESSION[$_GET['XLS']]['filtre'])) {
            $filtre = $_GET['filtre'];
        } elseif (!isset($_GET['filtre']) && isset($_SESSION[$_GET['XLS']]['filtre'])) {
            $filtre = $_SESSION[$_GET['XLS']]['filtre'];
        } elseif (isset($_GET['filtre']) && isset($_SESSION[$_GET['XLS']]['filtre'])) {
            $filtre = "1";
        } else {
            $filtre = "0";
        }

        // Champs de la barre de recherche.
        if (isset($_GET['search']) && !isset($_SESSION[$_GET['XLS']]['search'])) {
            $search = $_GET['search'];
        } elseif (!isset($_GET['search']) && isset($_SESSION[$_GET['XLS']]['search'])) {
            $search = $_SESSION[$_GET['XLS']]['search'];
        } else {
            $search = "";
        }
        // Récup le type de orderBy et la colonne sur le quel est effectué le orderby.
        if (isset($_GET['order']) && isset($_GET['champ']) && !isset($_SESSION[$_GET['XLS']]['order']) && !isset($_SESSION[$_GET['XLS']]['champ'])) {
            $order = $_GET['order'];
            $champ = $_GET['champ'];
        } elseif (!isset($_GET['order']) && !isset($_GET['champ']) && isset($_SESSION[$_GET['XLS']]['order']) && isset($_SESSION[$_GET['XLS']]['champ'])) {
            $order = $_SESSION[$_GET['XLS']]['order'];
            $champ = $_SESSION[$_GET['XLS']]['champ'];
        }


        if (isset($_GET['actif'])) {
            if (!is_int($_GET['actif'])) {
                $actif = 2;
            } else {
                $actif = intval($_GET['actif']);
            }
        } else {
            $actif = 2;
        }


        $filtre_array_key = array();
        $filtre_array_val = array();
        $champs_array_val = array();
        $active_view_array = array();
        $get_filtre = array();

        if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['filtres'])) {
            $filtres_conf = $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['filtres'];


            foreach ($filtres_conf as $filtres_conf_key => $filtres_conf_val) {

                if(isset($filtres_conf_val['sql']) && !empty($filtres_conf_val['sql'])){
                    $active_view = $filtres_conf_val['active_view'];
                    $req_conf_val = $this->Nom_Tab->listeOptionConf($filtres_conf_val['sql']);
                    $champs_array_val[$filtres_conf_key] = $filtres_conf_val['champ_filtre'];

                    array_push($active_view_array, $active_view);
                    array_push($filtre_array_key, $filtres_conf_key);
                    array_push($filtre_array_val, $req_conf_val);

                    if (isset($_GET[$filtres_conf_key])) {
                        $get_filtre[$filtres_conf_key] = ($_GET[$filtres_conf_key]);
                    } elseif (!isset($_GET[$filtres_conf_key]) && isset($_SESSION[$_GET['XLS']][$filtres_conf_key])) {
                        $get_filtre[$filtres_conf_key] = ($_SESSION[$_GET['XLS']][$filtres_conf_key]);
                    }
                }else{
                    if(isset($filtres_conf_val['champ_type']) && !empty($filtres_conf_val['champ_type'])){
                        $active_view = $filtres_conf_val['active_view'];
                        $champs_array_val[$filtres_conf_val['champ_filtre'].'_'.$filtres_conf_val["champ_type"]] = $filtres_conf_val['champ_filtre'];
                        $champs_array_type[$filtres_conf_key] = $filtres_conf_val['champ_type'];


                        array_push($active_view_array, $active_view);
                        array_push($filtre_array_key, $filtres_conf_key);
                        array_push($filtre_array_val, $champs_array_type[$filtres_conf_key]);

                        if (isset($_GET[$filtres_conf_val['champ_filtre'].'_'.$filtres_conf_val["champ_type"]])) {

                            $get_filtre[$filtres_conf_val['champ_filtre'].'_'.$filtres_conf_val["champ_type"]] = ($_GET[$filtres_conf_val['champ_filtre'].'_'.$filtres_conf_val["champ_type"]]);
                        } elseif (!isset($_GET[$filtres_conf_val['champ_filtre'].'_'.$filtres_conf_val["champ_type"]]) && isset($_SESSION[$_GET['XLS']][$filtres_conf_val['champ_filtre'].'_'.$filtres_conf_val["champ_type"]])) {
                            $get_filtre[$filtres_conf_val['champ_filtre'].'_'.$filtres_conf_val["champ_type"]] = ($_SESSION[$_GET['XLS']][$filtres_conf_val['champ_filtre'].'_'.$filtres_conf_val["champ_type"]]);
                        }
                    }
                }


            }
        }

        if (!isset($filtre_array_key)) {
            $filtre_array_key = null;
        }
        if (!isset($filtre_array_val)) {
            $filtre_array_val = null;
        }
        if (!isset($champs_array_val)) {
            $champs_array_val = null;
        }
        if (!isset($get_filtre)) {
            $get_filtre = null;
        }


        /* Fin concérnant les filtres de recherche. ------------------------------------------------------------------*/
        /*------------------------------------------------------------------------------------------------------------*/

        /*------------------------------------------------------------------------------------------------------------*/
        /* Envoie des informations au moteur -------------------------------------------------------------------------*/

        $reqCount = $this->Nom_Tab->countItem($profilSql, $table, $champs, $filtre, $label_id, $get_filtre, $champs_array_val); // Récup le nombre de résultat (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).
        $nbTotalPage = $this->Nom_Tab->countAllItems($reqCount); // Récup le nombre de résultat (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).

        $nbPage = ceil(intval($nbTotalPage[0]->total) / $ParPage); // Calcule le nombre de page en fonction du nombre de résultat issue du count.
        $pageCurrent = $this->Nom_Tab->getCurrent($page, $nbPage); // Détermine la page courante (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).

        $req = $this->Nom_Tab->getItemQuery($profilSql, $table, $champs, $pageCurrent, $ParPage, $filtre, $label_id, $get_filtre, $champs_array_val); // Récuperation de la requete pour l'afficher dans Infos SQL (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).
        $donnees = $this->Nom_Tab->getAllItems($req); // Récuperation de tous les Users (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).


        /* Récupération du résultat renvoyé par le moteur dans l'objet $donnees. -------------------------------------*/
        /*------------------------------------------------------------------------------------------------------------*/




            // Si l'Export CSV est réclamé.
            if (isset($_GET['exportCsv']) && !empty($_GET['exportCsv']) && intval($_GET['exportCsv']) == 1) {

                header("Content-Type: text/csv; charset=UTF-8");
                header("Content-disposition: filename=" . $_GET['XLS'] . ".csv");

                if (isset($tabXlsFields[$_GET['XLS']]["csv_memory_limit"]) && intval($tabXlsFields[$_GET['XLS']]["csv_memory_limit"])) {
                    ini_set('memory_limit', intval($tabXlsFields[$_GET['XLS']]["csv_memory_limit"]) . 'M');
                }
                // Récup des données qui ont dans la config CSV = 1.
                $donneesCsv = $this->Nom_Tab->getAllICSVItems($profilSql, $table, $champs, $filtre, $label_id, $get_filtre, $champs_array_val); // Récuperation de tous les item (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).

                // Création du tableau contenant les colonnes qui ont dans la config csv = 1.
                $col = array();
                foreach ($champs as $key => $v) {
                    if (isset($v['csv']) && $v['csv'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1 && !isset($v['bdd_value'])) {
                        array_push($col, $v['nom']);
                    } elseif (isset($v['csv']) && $v['csv'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1 && isset($v['bdd_value'])) {
                        array_push($col, $v['nom']);
                    }
                }
                $nbCol = count($col); // Récuperation du nombre de colonne à afficher.

                $separateur = ";";
                // Affichage de la ligne terminée par un retour chariot
                echo implode($separateur, $col) . "\r\n";

                // Création du contenu du tableau
                $liCsv = array();
                // Parcours de l'array contenant l'objet
                foreach ($donneesCsv as $ligne => $value) {
                    // Transforme un objet en array.
                    $value = (array)$value;
                    // Parcours de la conf.
                    foreach ($champs as $key => $v):
                        // Condition si la clé du tableau conf est identique à la clé du tableau de données.
                        if (isset($v['type']) && $v['type'] == "list_sql" && isset($v['csv']) && $v['csv'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                            $val = $value[$v['bdd_table_t']];

                            if (isset($val)) {
                                $val = str_replace(array('"', "\t", "\n", "\r"), array("", " ", " ", " "), $val);
                            }
                            array_push($liCsv, $val);
                        } elseif (isset($v['csv']) && $v['csv'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                            $val = $value[$key];

                            if (isset($val)) {
                                $val = str_replace(array('"', "\t", "\n", "\r"), array("", " ", " ", " "), $val);
                            }

                            array_push($liCsv, $val);
                        }
                    endforeach;
                }
                // Découpe du tableau en fonction du nombre de colonne.
                $liCsv = array_chunk($liCsv, $nbCol);

                // Affichage de chaque sous tableaux issue du découpage.
                foreach ($liCsv as $liCVal) {
                    echo implode($separateur, $liCVal) . "\r\n\t";
                }
                exit();
            } else {
                if (!isset($errors) || empty($errors)) {
                    $errors = null;
                }
                if (!isset($champ) || empty($champ)) {
                    $champ = null;
                }
                if (!isset($option) || empty($option)) {
                    $option = null;
                }
                if (!isset($reqOptionsP) || empty($reqOptionsP)) {
                    $reqOptionsP = null;
                }
                $Version = self::VERSION;
                $ListDep = $this->Nom_Tab->listeOption('secto_departements', 'code_vc');
                $fin = microtime(true);
                $delai = $fin - $debut;
                $delai = substr($delai, 0, 4);
                $form = new BootstrapForm($_POST);
                $debugMode = $_SESSION['db_DebugMode'];
                $LocalTime = $_SESSION['db_LocalTime'];
                $this->render('Users.listUsers', compact('donnees', 'Version', 'ListDep', 'active_view_array', 'get_filtre', 'filtre_array_key', 'filtre_array_val', 'LocalTime', 'reqOptionsP', 'debugMode', 'errors', 'champ', 'tabXlsFields', 'pagination', 'champs', 'form', 'page', 'nbPage', 'pageCurrent', 'nbTotalPage', 'delai', 'req', 'reqCount', 'actif', 'filtre', 'search', 'ParPage', 'nom_feuille'));
                exit();
            }

    }

    public function addUser(){
        $debut = microtime(true);

        if (!isset($_GET['XLS'])) {
            $_GET['XLS'] = 'Users';
        }

        // Récup du fichier conf (dossier *racine*/conf/FieldsUsers.php).
        $tabXlsFields = listParamUsers();
        $search_array = $tabXlsFields;

        // Verifie si la conf correspondant à la page demmandé existe.
        if (!isset($_GET['XLS']) || empty($_GET['XLS']) || !array_key_exists($_GET['XLS'], $search_array)) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        }

        if (!isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'])) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        } elseif (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'] != 1) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        }


        if (isset($_GET['XLS'])) {
            $_SESSION[$_GET['XLS']]['XLS'] = $_GET['XLS'];
        }
        if (isset($_GET['collapsId'])) {
            $_SESSION[$_GET['XLS']]['collapsId'] = $_GET['collapsId'];
        }


        // Récup du fichier conf Utilisateurs (dossier *racine*/conf/FieldsUsers.php).
        $tabXlsFields = listParamUsers();

        // Verifie si la conf correspondant à la page demmandé existe.
        if (!isset($tabXlsFields['Users']) || empty($tabXlsFields['Users'])) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        }

        $erreur = array();
        // Verifie si les clés obligatoire sont bien présentes.
        if (!isset($tabXlsFields['Users']['profilSql']) || empty($tabXlsFields['Users']['profilSql'])) {
            $erreur['profilSql'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields['Users']['bdd_table']) || empty($tabXlsFields['Users']['bdd_table'])) {
            $erreur['bdd_table'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields['Users']['champs']) || empty($tabXlsFields['Users']['champs'])) {
            $erreur['champs'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields['Users']['nom_feuille']) || empty($tabXlsFields['Users']['nom_feuille'])) {
            $erreur['nom_feuille'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields['Users']['pagination']) || empty($tabXlsFields['Users']['pagination'])) {
            $erreur['pagination'] = "manquant ou vide";
        }


        if (isset($tabXlsFields['Users']['profilSql'])) {

            if (!isset($tabXlsFields['Users']['profilSql'][$_SESSION['Droit']])) {
                $erreur['Le profil utilisateurs'] = "manquant ou n'est pas autorisé à voir la page";
            }

        }
        $TabListMIME = listParamTypeMIME();
        $ListeTypeMIME = $TabListMIME['TypeMIME']['listeTypeMIME'];
        $tableFile = $tabXlsFields['Users']['bdd_table_file'];

        if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['custom_list_edit'])) {
            $custom_list_edit = $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['custom_list_edit'];
        } else {
            $custom_list_edit = null;
        }

        if ($erreur != null) {
            $erreur = serialize($erreur);
            header('Location: index.php?p=Principal.notFound&erreurConf=' . $erreur . '');
            exit();
        } else {
            // Si les clés sont présent.
            $profilSql = $tabXlsFields['Users']['profilSql'];
            $table = $tabXlsFields['Users']['bdd_table'];
            $champs = $tabXlsFields['Users']['champs'];
            $nom_feuille = $tabXlsFields['Users']['nom_feuille'];
            $label_id = $tabXlsFields['Users']['bdd_id'];

            foreach ($champs as $souschamps => $sc) {

                if (isset($sc['type_input']) && $sc['type_input'] != 'categorie' && $sc['type_input'] != 'sous_categorie') {

                    if (!isset($sc['nom']) || empty($sc['nom'])) {
                        $erreur['nom'] = " manquant ou vide";
                    } elseif (!isset($sc['type_input']) || empty($sc['type_input'])) {
                        $erreur['type_input'] = " manquant ou vide";
                    } elseif (!isset($sc['taille_max']) || empty($sc['taille_max'])) {
                        $erreur['taille_max'] = " manquant ou vide";
                    } elseif (!isset($sc['taille_min']) || empty($sc['taille_min'])) {
                        if ($sc['taille_min'] == 0) {
                        } else {
                            $erreur['taille_min'] = " manquant ou vide";
                        }
                    }

            }
            }

            if ($erreur != null) {
                $erreur = serialize($erreur);
                header('Location: index.php?p=Principal.notFound&erreurConf=' . $erreur . '');
                exit();
            }
        }

        // Si le formulaire de la page a été soumis.
        if (!empty($_POST) && empty($_GET['conf'])) {


            /*-------------------------------------------------------------------------------------------------------*/
            /* Vérification des champs issue du formulaire ----------------------------------------------------------*/
            /*-------------------------------------------------------------------------------------------------------*/
            $errors = new ControleObjetConfig(); // L'objet controleForm est dans le dossier *racine*/App/config.
            $errors->existSession($_SESSION['UserID']); // Verifie avant d'éxécuter une méthode si User est connecté.(sa évite qu'un formulaire non issue de l'application soit soumis à la place)

            $edit = 0; // Cette variable permet à la fonction de vérification du mail de différencier si c'est une mise à jour ou une nouvel insertion.
            $id = 0; // Variable qui vaut null dans le cas d'un insertion(auto-incrémentation) ou l'id du user dans le cas d'une mise à jour.

            // Parcours du fichier conf et contrôle des champs.
            foreach ($champs as $c => $cc) {

                if ($cc['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1) {

                    if (isset($_POST["item" . $c])) {
                        $_POST[$c] = $_POST["item" . $c];
                    }
                    if (isset($_POST[$c . "_date1"]) && isset($_POST[$c . "_date2"]) && !empty($_POST[$c . "_date1"]) && !empty($_POST[$c . "_date2"])) {
                        $_POST[$c] = $_POST[$c . "_date1"] . " " . $_POST[$c . "_date2"] . ":00";
                    }
                    if (!isset($cc['controle_balise']) || empty($cc['controle_balise'])) {
                        $cc['controle_balise'] = 0;
                    }
                    if (!isset($cc['obligatoire']) || empty($cc['obligatoire'])) {
                        $cc['obligatoire'] = 0;
                    }
                    if (!isset($cc['unique']) || empty($cc['unique'])) {
                        $cc['unique'] = 0;
                    }


                    switch ($cc['type_input']) :
                        case "text":
                            if (!isset($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validVarchar($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "textarea":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validVarchar($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "date":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validDate($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "datetime":

                            if ($_POST[$c . "_date1"] != '' && $_POST[$c . "_date2"] == '' && !isset($_POST[$c])) {
                                $errors->invalidDateTime($c);

                            } elseif ($_POST[$c . "_date1"] == '' && $_POST[$c . "_date2"] != '' && !isset($_POST[$c])) {

                                $errors->invalidDateTime($c);
                            } else {
                                if (!isset($_POST[$c])) {
                                    $_POST[$c] = null;
                                }
                                // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                                $errors->validDateTime($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            }

                            break;
                        case "number":
                            if (!isset($_POST[$c]) || $_POST[$c] == '') {
                                $_POST[$c] = 0;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validInt($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "phone":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validPhone($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "password":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validPassword($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "email":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validMail($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "url":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validUrl($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "radio":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validRadio($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "checkbox":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validCheckbox($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            break;
                        case "file":
                            if (isset($_POST[$c]) && !empty($_POST[$c]) && isset($_POST[$c . '_ext']) && !empty($_POST[$c . '_ext']) && isset($_POST[$c . '_Name']) && !empty($_POST[$c . '_Name'])) {
                                // Suppression de tous les fichiers temporaire qui date de + de 30 min. ---------------------------------------------------------------------------------------------
                                $reqVal = 'SELECT file_tmp.id, file_tmp.id_user, file_tmp.nom, file_tmp.path, file_tmp.conf, file_tmp.champ, file_tmp.typeMime, file_tmp.datequote FROM file_tmp';
                                $reqVal .= ' WHERE file_tmp.datequote < DATE_SUB(NOW(), INTERVAL ? MINUTE)';
                                $resultReq = $this->Nom_Tab->findItemData($reqVal, self::TEMP_FILE_MINUTES);

                                foreach ($resultReq as $resKey => $resVal) {
                                    unlink('./fileTmp/' . $resVal->path);
                                }
                                $this->Nom_Tab->deleteFileTempo(self::TEMP_FILE_MINUTES);
                                // ------------------------------------------------------------------------------------------------------------------------------------------------------------------

                                foreach ($_POST[$c] as $post_f => $p_f) {

                                    // Récupère les 5 premiers caractères du champ file.
                                    $n = substr($p_f, 0, 5);

                                    // ICI sa concerne le cas ou les fichiers sont plus des data, on été verifié avant et mit en table tempo.
                                    if ($n != 'data:') {

                                        // Permet de renvoyer dans la vue les informations du fichier mit temporairement en bdd.
                                        $reqVal = 'SELECT file_tmp.id, file_tmp.id_user, file_tmp.nom, file_tmp.path, file_tmp.conf, file_tmp.champ, file_tmp.typeMime FROM file_tmp';
                                        $reqVal .= ' WHERE file_tmp.nom = ?';
                                        $resultReq = $this->Nom_Tab->findItemData($reqVal, $p_f);

                                        if ($resultReq) {
                                            $_POST[$c][$post_f] = $resultReq[0]->nom;
                                            $errors->errors{$c}[$_POST[$c . '_Name'][$post_f] . '.' . $_POST[$c . '_ext'][$post_f]] = null;
                                        } else {
                                            $errors->errors{$c}[$_POST[$c . '_Name'][$post_f] . '.' . $_POST[$c . '_ext'][$post_f]] = '<em>Fichier non autorisée.</em>';
                                        }
                                    } else {
                                        // ICI sa concerne le cas ou les fichiers sont des data url(donc pas encor mit en bdd temporaire).

                                        // Vérification des fichiers.
                                        $errors->validFile($p_f, $_POST[$c . '_ext'][$post_f], $ListeTypeMIME, $c, $champs[$c], $_POST[$c . '_Name'][$post_f]);

                                        // Si le fichier est conforme.
                                        if ($errors->errors{$c}[$_POST[$c . '_Name'][$post_f] . '.' . $_POST[$c . '_ext'][$post_f]] == null && $_POST[$c . '_ext'][$post_f] != 'jpeg' && $_POST[$c . '_ext'][$post_f] != 'jpg') {

                                            if ($n == 'data:') {


                                                $name = uniqid();
                                                $file = $p_f;

                                                // Récup le Type MIME du fichier. ------------------------------------------------------
                                                $mime = substr($file, strpos($file, "data:"), strpos($file, ",") - strlen($file));
                                                $mimeFilter = explode(':', $mime);

                                                $TypeMimeFile = explode(';', $mimeFilter[1]);
                                                $TypeMimeFile = $TypeMimeFile[0];

                                                // Récup les données du fichier sans le type mime. -------------------------------------
                                                $file = str_replace($mime . ',', '', $file);
                                                $file = str_replace(' ', '+', $file);
                                                $data = base64_decode($file);

                                                // Mise en dossier temporaire.
                                                $file = $name . '.' . $_POST[$c . '_ext'][$post_f];
                                                $fileContent = "./fileTmp/" . $name . '.' . $_POST[$c . '_ext'][$post_f];

                                                $success = file_put_contents($fileContent, $data);

                                                if ($success) {
                                                    // Affectation des colonnes et valeurs pour l'insertion en bdd du fichier ---------------
                                                    $colInsertFile = array(0 => "id_user", 1 => "nom", 2 => "path", 3 => "conf", 4 => "champ", 5 => 'typeMime', 6 => 'datequote');
                                                    $valueInsertFile = array(0 => ":id_user", 1 => ":nom", 2 => ":path", 3 => ":conf", 4 => ":champ", 5 => ':typeMime', 6 => 'NOW()');
                                                    $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $_POST[$c . '_Name'][$post_f] . $name, 2 => $file, 3 => 'Users', 4 => $c, 5 => $TypeMimeFile);

                                                    //Insertion du fichier dans la base de données temporaire.
                                                    $result = $this->Nom_Tab->insertItem('file_tmp', $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                                    $_POST[$c][$post_f] = $_POST[$c . '_Name'][$post_f] . $name;

                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                // Dans le cas ou aucun fichier n'a été uploadé.
                                $file = null;
                                $ext = null;
                                $nameFile = null;
                                // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                                $errors->validFile($file, $ext, $ListeTypeMIME, $c, $champs[$c], $nameFile);
                            }
                            break;
                        default:
                    endswitch;
                }
            }

            /* Parcours de l'objet errors ---------------------------------------------------------------------------*/
            $NbErrors = 0;
            foreach ($errors->errors as $error => $value) {
                if (!is_array($value) && $value != null) {
                    $NbErrors++;
                } elseif (is_array($value)) {
                    foreach ($value as $vKey => $vVal) {
                        if ($vVal != null) {
                            $NbErrors++;
                        }
                    }
                }
            }
            /*-------------------------------------------------------------------------------------------------------*/
            /* En cas de non erreur dans la vérification de l'objet errors ------------------------------------------*/
            /*-------------------------------------------------------------------------------------------------------*/
            if ($NbErrors === 0) {

                /*----------------------------------------------------------------------------------------------------*/
                /* Préparation des données à inserer -----------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/
                $colInsert = array();
                $valueInsert = array();
                $valueParam = array();

                // Parcours du fichier conf (dossier *racine*/conf/FieldsUsers.php).
                foreach ($champs as $c => $cc) {

                    if ($cc['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1 && $cc['type_input'] != 'file' && !isset($cc['date_now']) && $cc['type_input'] != 'inputMultiple' && $cc['type_input'] != 'checkboxDep' && $cc['type_input'] !=  'checkboxNro') {
                        // Condition qui permet d'isoler le champs mot de passe pour lui attribuer un cryptage.
                        if ($c == 'password') {
                            $_POST[$c] = password_hash($_POST[$c], PASSWORD_BCRYPT);
                            $colIn = $c;
                            $colvalue = ":" . $c;
                            $value = $_POST[$c];
                            array_push($colInsert, $colIn); // array contenant les colonnes pour l'insertion.
                            array_push($valueInsert, $colvalue); // array contenant le nom des paramètres pour la requete préparé.
                            array_push($valueParam, $value); // array contenant les valeurs à attribuer au paramètres.
                        } else {
                            $colIn = $c;
                            $colvalue = ":" . $c;
                            $value = $_POST[$c];
                            array_push($colInsert, $colIn); // array contenant les colonnes pour l'insertion.
                            array_push($valueInsert, $colvalue); // array contenant le nom des paramètres pour la requete préparé.
                            array_push($valueParam, $value); // array contenant les valeurs à attribuer au paramètres.
                        }

                    }

                }

                // Parcours du fichier conf (dossier *racine*/conf/FieldsUsers.php).
                foreach ($champs as $c => $cc) {
                    if (isset($cc['date_now']) && $cc['date_now'] == 1) {
                        $colIn = $c;
                        $value = 'NOW()';
                        array_push($colInsert, $colIn); // array contenant les colonnes pour l'insertion.
                        array_push($valueInsert, $value); // array contenant le nom des paramètres pour la requete préparé.
                    }
                }


                // Insertion de l'item dans la base de données.
                $result = $this->Nom_Tab->insertUserItem($table, $colInsert, $valueInsert, $valueParam); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                /* Fin de l'insertion de l'item ----------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/

                // Si l'insertion est OK.
                if ($result) {

                    $lastIdItem = $this->Nom_Tab->lastID($table);// La fonction lastID est dans le dossier *racine*/app/Table fichier Nom_Table.php.

                    foreach ($champs as $c => $cc) {

                        if (isset($cc['type_input']) && $cc['type_input'] == 'checkboxDep') {

                            if (!isset($_POST[$c])) {
                                $delete = $this->Nom_Tab->findItem('DELETE FROM user_dept WHERE user_dept.id_user =' . $lastIdItem[0]->id); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                            }
                            if (isset($_POST[$c])) {

                                $nbValTab = array_count_values($_POST[$c]);

                                $valReqDelete = implode($_POST[$c], ',');

                                if ($nbValTab < self::MAX_SITE) {
                                    $delete = $this->Nom_Tab->findItem('DELETE FROM user_dept WHERE NOT IN (' . $valReqDelete . ') AND user_dept.id_user =' . $lastIdItem[0]->id); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.

                                } else {
                                    $delete = $this->Nom_Tab->findItem('DELETE FROM user_dept WHERE user_dept.id_user =' . $lastIdItem[0]->id); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                }

                                foreach ($_POST[$c] as $ChekKey => $ChekVal) {
                                    $isExist = $this->Nom_Tab->findItem('SELECT user_dept.id_user, user_dept.id_dept FROM user_dept WHERE user_dept.id_user =' . $lastIdItem[0]->id . ' AND user_dept.id_dept =' . $ChekVal); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                    if (!$isExist) {
                                        $colCheck = array(0 => "id_user", 1 => "id_dept");
                                        $valueParamCheck = array(0 => ":id_user", 1 => ":id_dept");
                                        $valueCheck = array(0 => $lastIdItem[0]->id, 1 => $ChekVal);
                                        $resultCheck = $this->Nom_Tab->insertItem('user_dept', $colCheck, $valueParamCheck, $valueCheck); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                    }
                                }
                            }
                        }
                    }

                    foreach ($champs as $c => $cc) {

                        if (isset($cc['type_input']) && $cc['type_input'] == 'inputMultiple') {

                            if (!isset($_POST[$c])) {
                                $delete = $this->Nom_Tab->findItem('DELETE FROM user_site WHERE user_site.id_user =' . $lastIdItem[0]->id); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                            }
                            if (isset($_POST[$c])) {
                                $nbValTab = array_count_values($_POST[$c]);
                                $valReqDelete = implode($_POST[$c], ',');
                                if ($nbValTab < self::MAX_SITE) {
                                    $delete = $this->Nom_Tab->findItem('DELETE FROM user_site WHERE NOT IN (' . $valReqDelete . ') AND user_site.id_user =' . $lastIdItem[0]->id); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.

                                } else {
                                    $delete = $this->Nom_Tab->findItem('DELETE FROM user_site WHERE user_site.id_user =' . $lastIdItem[0]->id); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                }

                                foreach ($_POST[$c] as $MultKey => $MultVal) {
                                    $isExist = $this->Nom_Tab->findItem('SELECT user_site.id_user, user_site.id_site FROM user_site WHERE user_site.id_user =' . $lastIdItem[0]->id . ' AND user_site.id_site =' . $MultVal); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                    if (!$isExist) {
                                        $colCheck = array(0 => "id_user", 1 => "id_site");
                                        $valueParamCheck = array(0 => ":id_user", 1 => ":id_site");
                                        $valueCheck = array(0 => $lastIdItem[0]->id, 1 => $MultVal);
                                        $resultCheck = $this->Nom_Tab->insertItem('user_site', $colCheck, $valueParamCheck, $valueCheck); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                    }
                                }
                            }
                        }
                    }


                    $colInsertFile = array(0 => "id_user", 1 => "id_item", 2 => "nom", 3 => "path", 4 => "conf", 5 => "champ", 6 => 'datequote');
                    $valueInsertFile = array(0 => ":id_user", 1 => ":id_item", 2 => ":nom", 3 => ":path", 4 => ":conf", 5 => ":champ", 6 => 'NOW()');
                    /*----------------------------------------------------------------------------------------*/
                    /* Concerne l'upload de fichier -------------- -------------------------------------------*/
                    /*----------------------------------------------------------------------------------------*/

                    $ifFile = 0;

                    // Parcours de la conf.
                    foreach ($champs as $c => $cc) {

                        if (isset($_POST[$c]) && $cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && $cc['type_input'] == 'file' && isset($cc['cheminDossier'])) {


                            // Parcours de l'array file.
                            foreach ($_POST[$c] as $post_file => $p_f) {

                                $file = $p_f;
                                // Récupère les 5 premiers caractères du champ file.
                                $n = substr($p_f, 0, 5);

                                // Si les fichier n'est pas un data url (fichier déjà mit en table temporaire).
                                if ($n != 'data:') {

                                    // Récupération des informations du fichier en table temporaire.
                                    $reqVal = 'SELECT file_tmp.id, file_tmp.id_user, file_tmp.nom, file_tmp.path, file_tmp.conf, file_tmp.champ, file_tmp.typeMime FROM file_tmp';
                                    $reqVal .= ' WHERE file_tmp.nom = ?';
                                    $resultReq = $this->Nom_Tab->findItemData($reqVal, $p_f);

                                    if ($resultReq) {
                                        // Assigniation des valeurs.
                                        $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $lastIdItem[0]->id, 2 => $resultReq[0]->nom, 3 => $resultReq[0]->path, 4 => $resultReq[0]->conf, 5 => $resultReq[0]->champ);
                                        // Insertion du fichier dans la table définitif.
                                        $result = $this->Nom_Tab->insertItem($tableFile, $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                        // Si le fichier existe bien en table temporaire.
                                        if ($result) {
                                            // Copie du fichier dans le dossier temporaire dans le dossier définitif.
                                            copy('./fileTmp/' . $resultReq[0]->path, $cc['cheminDossier'] . $resultReq[0]->path);
                                            // Si le copie est OK.
                                            if (copy('./fileTmp/' . $resultReq[0]->path, $cc['cheminDossier'] . $resultReq[0]->path)) {
                                                // Suppression du fichier dans le dossier temporaire.
                                                unlink('./fileTmp/' . $resultReq[0]->path);
                                                // Suppression du fichier dans la table temporaire.
                                                $this->Nom_Tab->deleteItem($resultReq[0]->id, 'file_tmp');

                                            }
                                        }
                                    }
                                } else {


                                    // Dans le cas ou le fichier n'est pas en table temporaire (soit un fichier jpg ou alors aucune erreur a eu lieu lors de la première soumission du formulaire).

                                    // Récup le Type MIME du fichier. ------------------------------------------------------
                                    $mime = substr($file, strpos($file, "data:"), strpos($file, ",") - strlen($file));

                                    // Récup les données du fichier sans le type mime. -------------------------------------
                                    $file = str_replace($mime . ',', '', $file);
                                    $file = str_replace(' ', '+', $file);
                                    $data = base64_decode($file);
                                    // -------------------------------------------------------------------------------------

                                    // Affectation des colonnes et valeurs pour l'insertion en bdd du fichier ---------------
                                    $name = uniqid();

                                    $file = $name . '.' . $_POST[$c . '_ext'][$post_file];
                                    $fileContent = $cc['cheminDossier'] . $name . '.' . $_POST[$c . '_ext'][$post_file];

                                    $success = file_put_contents($fileContent, $data);
                                    $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $lastIdItem[0]->id, 2 => $name, 3 => $file, 4 => 'Users', 5 => $c);
                                    // Insertion du fichier dans la table définitif.
                                    $result = $this->Nom_Tab->insertItem($tableFile, $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                }
                            }
                        }

                    }
                    /* Fin d'upload de fichier ---------------------------------------------------------------*/
                    /*----------------------------------------------------------------------------------------*/


                    $_SESSION['succes'] = 1;
                    header('Location: index.php?p=Users.listUsers&XLS=' . $_GET['XLS']);
                    exit();
                }
                // Si l'insertion est OK redirection.
            }
        }


        $options = array();
        $reqOptionsP = array();

        // Parcours du fichier conf (dossier *racine*/conf/FieldsUsers.php).
        foreach ($champs as $auto_c => $a_c) {

            if(isset($a_c['profilChamps'][$_SESSION['Droit']]['ecriture']) && $a_c['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1) {

                if (isset($a_c['type']) && $a_c['type'] == "list_sql" && $a_c['autocomplete'] == 0) {
                    $options[$auto_c] = array();
                    // Récup les valeurs à afficher dans le select(html).
                    $reqOptions = $this->Nom_Tab->allItemsForSelectQuery($a_c);
                    $option = $this->Nom_Tab->allItemsForSelect($reqOptions);
                    $options[$auto_c] = $option;
                    array_push($reqOptionsP, $reqOptions);
                }
            }
        }

        // Si l'autocomplétion a été déclanchée.
        if (isset($_GET['champs']) && !empty($_GET['champs']) && isset($_GET['key']) && !empty($_GET['key']) && isset($_GET['conf']) && !empty($_GET['conf']) && isset($_GET['autocomp']) && $_GET['autocomp'] == 1) {
            if(isset($champs[$_GET['key']])){
                if($_GET['key'] == 'responsable' && isset($_GET['id'])){
                    $optionC = json_encode($this->Nom_Tab->autoCompletionWithConcatForResp($champs[$_GET['key']], $_GET['champs'], $_GET['conf']));
                }else{
                    $optionC = json_encode($this->Nom_Tab->autoCompletion($champs[$_GET['key']], $_GET['champs'], $_GET['conf']));
                }
                echo $optionC;
                exit();
            }
        }


        if (isset($options['roleId'])) {
            foreach ($options['roleId'] as $optKey => $optVal) {
                if ($optVal->id == 10000 && $_SESSION['Droit'] != 10000) {
                    unset($options['roleId'][$optKey]);
                }
            }
        }

        if (!isset($errors) || empty($errors)) {
            $errors = null;
        }

        $Version = self::VERSION;
        $ListDep = $this->Nom_Tab->listeOption('secto_departements', 'code_vc');
        $fin = microtime(true);
        $delai = $fin - $debut;
        $delai = substr($delai, 0, 4);
        $form = new BootstrapForm($_POST);
        $debugMode = $_SESSION['db_DebugMode'];
        $LocalTime = $_SESSION['db_LocalTime'];
        $this->render('Users.addUser', compact('Version', 'ListDep', 'LocalTime', 'reqOptionsP', 'debugMode', 'errors', 'champs', 'options', 'tabXlsFields', 'form', 'delai', 'nom_feuille'));
        exit();

    }

    public function editUser()
    {
        $debut = microtime(true);

        if (!isset($_GET['XLS'])) {
            $_GET['XLS'] = 'Users';
        }

        // Récup du fichier conf (dossier *racine*/conf/FieldsUsers.php).
        $tabXlsFields = listParamUsers();
        $search_array = $tabXlsFields;

        // Verifie si la conf correspondant à la page demmandé existe.
        if (!isset($_GET['XLS']) || empty($_GET['XLS']) || !array_key_exists($_GET['XLS'], $search_array)) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        }

        if (!isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'])) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        } elseif (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture']) &&
            $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'] != 1) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        }

            $id = $tabXlsFields[$_GET['XLS']]['bdd_id'];
            $table = $tabXlsFields[$_GET['XLS']]['bdd_table'];
            $champs = $tabXlsFields[$_GET['XLS']]['champs'];
            $nom_feuille = $tabXlsFields[$_GET['XLS']]['nom_feuille'];
            $tableFile = $tabXlsFields[$_GET['XLS']]['bdd_table_file'];


        // Récup du fichier conf Historique (dossier *racine*/conf/FieldsHistorique.php).
        $tabXlsFieldsHistorique = listParamHistorique();
        $idHistorique = $tabXlsFieldsHistorique['Historique']['bdd_id'];
        $tableHistorique = $tabXlsFieldsHistorique['Historique']['bdd_table'];
        $champsHistorique = $tabXlsFieldsHistorique['Historique']['champs'];
        $nom_feuilleHistorique = $tabXlsFieldsHistorique['Historique']['nom_feuille'];

        $TabListMIME = listParamTypeMIME();
        $ListeTypeMIME = $TabListMIME['TypeMIME']['listeTypeMIME'];
//        $tableFile = $tabXlsFields['Users']['bdd_table_file'];

        // Récupération des données de l'historique en ajax.
        if (isset($_GET['historique']) && $_GET['historique'] == 1 && isset($_GET['id_item'])) {

                    $historique = $this->Nom_Tab->getHistorique($_GET['id_item'], $_GET['nom_feuille']);// La fonction getHistorique est dans le dossier *racine*/core/Table fichier Table.php.

            if ($historique) {
                foreach ($historique as $ht => $t) {
                    echo '<hr>';
                    echo '<ul class="list-group">';
                    echo '<li class="list-group-item"> Modifié par : ' . htmlspecialchars($t->nom) . ' ' . htmlspecialchars($t->prenom) . '</li>';
                    setlocale(LC_TIME, "fr_FR");
                    $date = strftime("%d/%m/%Y à %Hh%M", strtotime($t->date_modification));
                    echo '<li class="list-group-item">Date de modification : ' . htmlspecialchars($date) . '</li>';
                    $valueModif = json_decode($t->value_modif);

                    foreach ($valueModif as $vm) {
                        foreach ($vm as $vvm => $vv) {
                            echo ' <li class="list-group-item"> Valeur modifiée : ' . htmlspecialchars($vvm) . ' par ' . htmlspecialchars($vv) . '</li>';
                        }
                    }
                    echo '</ul>';
                }
            } else {
                echo 'Aucune modification n\'a été enregistrée.';
            }
            exit();
        }


        // Si le formulaire de la page a été soumis.
        if (!empty($_POST) && empty($_GET['conf'])) {


            /*-------------------------------------------------------------------------------------------------------*/
            /* Vérification des champs issue du formulaire ----------------------------------------------------------*/
            /*-------------------------------------------------------------------------------------------------------*/
            $errors = new ControleObjetConfig(); // L'objet controleForm est dans le dossier *racine*/App/config.
            $errors->existSession($_SESSION['UserID']); // Verifie avant d'éxécuter une méthode si User est connecté.(sa évite qu'un formulaire non issue de l'application soit soumis à la place)

            $edit = 1; // Cette variable permet à la fonction de vérification du mail de différencier si c'est une mise à jour ou une nouvel insertion.

            // Parcours du fichier conf.
            foreach ($champs as $c => $cc) {

                if ($cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1) {

                    if (isset($_POST["item" . $c])) {
                        $_POST[$c] = $_POST["item" . $c];
                    }
                    if (isset($_POST[$c . "_date1"]) && isset($_POST[$c . "_date2"]) && !empty($_POST[$c . "_date1"]) && !empty($_POST[$c . "_date2"])) {
                        $_POST[$c] = $_POST[$c . "_date1"] . " " . $_POST[$c . "_date2"] . ":00";
                    }
                    if (!isset($cc['controle_balise']) || empty($cc['controle_balise'])) {
                        $cc['controle_balise'] = 0;
                    }

                    if (!isset($cc['unique']) || empty($cc['unique'])) {
                        $cc['unique'] = 0;
                    }

                    switch ($cc['type_input']) :
                        case "text":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validVarchar($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "textarea":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validVarchar($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "date":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validDate($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "datetime":

                            if ($_POST[$c . "_date1"] != '' && $_POST[$c . "_date2"] == '' && !isset($_POST[$c])) {
                                $errors->invalidDateTime($c);

                            } elseif ($_POST[$c . "_date1"] == '' && $_POST[$c . "_date2"] != '' && !isset($_POST[$c])) {

                                $errors->invalidDateTime($c);
                            } else {
                                if (!isset($_POST[$c])) {
                                    $_POST[$c] = null;
                                }
                                // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                                $errors->validDateTime($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $id);
                            }
                            break;
                        case "number":
                            if (!isset($_POST[$c]) || $_POST[$c] == '') {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validInt($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "phone":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validPhone($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "password":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validPassword($_POST[$c], $c, 0, $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "email":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validMail($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "url":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validUrl($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "radio":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validRadio($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "checkbox":
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validCheckbox($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "file":
                            if (isset($_POST[$c]) && !empty($_POST[$c]) && isset($_POST[$c . '_ext']) && !empty($_POST[$c . '_ext']) && isset($_POST[$c . '_Name']) && !empty($_POST[$c . '_Name'])) {
                                // Suppression de tous les fichiers temporaire qui date de + de 30 min. ---------------------------------------------------------------------------------------------
                                $reqVal = 'SELECT file_tmp.id, file_tmp.id_user, file_tmp.nom, file_tmp.path, file_tmp.conf, file_tmp.champ, file_tmp.typeMime, file_tmp.datequote FROM file_tmp';
                                $reqVal .= ' WHERE file_tmp.datequote < DATE_SUB(NOW(), INTERVAL ? MINUTE)';
                                $resultReq = $this->Nom_Tab->findItemData($reqVal, self::TEMP_FILE_MINUTES);

                                foreach ($resultReq as $resKey => $resVal) {
                                    unlink('./fileTmp/' . $resVal->path);
                                }
                                $this->Nom_Tab->deleteFileTempo(self::TEMP_FILE_MINUTES);
                                // ------------------------------------------------------------------------------------------------------------------------------------------------------------------

                                foreach ($_POST[$c] as $post_f => $p_f) {

                                    // Récupère les 5 premiers caractères du champ file.
                                    $n = substr($p_f, 0, 5);

                                    // ICI sa concerne le cas ou les fichiers sont plus des data, on été verifié avant et mit en table tempo.
                                    if ($n != 'data:') {

                                        // Permet de renvoyer dans la vue les informations du fichier mit temporairement en bdd.
                                        $reqVal = 'SELECT file_tmp.id, file_tmp.id_user, file_tmp.nom, file_tmp.path, file_tmp.conf, file_tmp.champ, file_tmp.typeMime FROM file_tmp';
                                        $reqVal .= ' WHERE file_tmp.nom = ?';
                                        $resultReq = $this->Nom_Tab->findItemData($reqVal, $p_f);

                                        if ($resultReq) {
                                            $_POST[$c][$post_f] = $resultReq[0]->nom;
                                            $errors->errors{$c}[$_POST[$c . '_Name'][$post_f] . '.' . $_POST[$c . '_ext'][$post_f]] = null;
                                        } else {
                                            $errors->errors{$c}[$_POST[$c . '_Name'][$post_f] . '.' . $_POST[$c . '_ext'][$post_f]] = '<em>Fichier non autorisée.</em>';
                                        }
                                    } else {
                                        // ICI sa concerne le cas ou les fichiers sont des data url(donc pas encor mit en bdd temporaire).

                                        // Vérification des fichiers.
                                        $errors->validFile($p_f, $_POST[$c . '_ext'][$post_f], $ListeTypeMIME, $c, $champs[$c], $_POST[$c . '_Name'][$post_f]);

                                        // Si le fichier est conforme.
                                        if ($errors->errors{$c}[$_POST[$c . '_Name'][$post_f] . '.' . $_POST[$c . '_ext'][$post_f]] == null && $_POST[$c . '_ext'][$post_f] != 'jpeg' && $_POST[$c . '_ext'][$post_f] != 'jpg') {

                                            if ($n == 'data:') {
                                                $name = uniqid();
                                                $file = $p_f;

                                                // Récup le Type MIME du fichier. ------------------------------------------------------
                                                $mime = substr($file, strpos($file, "data:"), strpos($file, ",") - strlen($file));
                                                $mimeFilter = explode(':', $mime);

                                                $TypeMimeFile = explode(';', $mimeFilter[1]);
                                                $TypeMimeFile = $TypeMimeFile[0];

                                                // Récup les données du fichier sans le type mime. -------------------------------------
                                                $file = str_replace($mime . ',', '', $file);
                                                $file = str_replace(' ', '+', $file);
                                                $data = base64_decode($file);

                                                // Mise en dossier temporaire.
                                                $file = $name . '.' . $_POST[$c . '_ext'][$post_f];
                                                $fileContent = "./fileTmp/" . $name . '.' . $_POST[$c . '_ext'][$post_f];

                                                $success = file_put_contents($fileContent, $data);

                                                if ($success) {
                                                    // Affectation des colonnes et valeurs pour l'insertion en bdd du fichier ---------------
                                                    $colInsertFile = array(0 => "id_user", 1 => "nom", 2 => "path", 3 => "conf", 4 => "champ", 5 => 'typeMime', 6 => 'datequote');
                                                    $valueInsertFile = array(0 => ":id_user", 1 => ":nom", 2 => ":path", 3 => ":conf", 4 => ":champ", 5 => ':typeMime', 6 => 'NOW()');
                                                    //$valueParamFile = array(0 => $_SESSION['UserID'], 1 => $_POST[$c . '_Name'][$post_f] . $name, 2 => $file, 3 => $_GET['XLS'], 4 => $c, 5 => $TypeMimeFile);
                                                    $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $_POST[$c . '_Name'][$post_f] .".". $_POST[$c . '_ext'][$post_f], 2 => $file, 3 => $_GET['XLS'], 4 => $c, 5 => $TypeMimeFile);

                                                    //Insertion du fichier dans la base de données temporaire.
                                                    $result = $this->Nom_Tab->insertItem('file_tmp', $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                                    //$_POST[$c][$post_f] = $_POST[$c . '_Name'][$post_f] . $name;
                                                    $_POST[$c][$post_f] = $_POST[$c . '_Name'][$post_f]  .".". $_POST[$c . '_ext'][$post_f];

                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                // Dans le cas ou aucun fichier n'a été uploadé.
                                $file = null;
                                $ext = null;
                                $nameFile = null;
                                // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                                $errors->validFile($file, $ext, $ListeTypeMIME, $c, $champs[$c], $nameFile);
                            }
                            break;
                    endswitch;

                }
            }
            /* Parcours de l'objet errors ---------------------------------------------------------------------------*/
            $NbErrors = 0;
            foreach ($errors->errors as $error => $value) {
                if (!is_array($value) && $value != null) {
                    $NbErrors++;
                } elseif (is_array($value)) {
                    foreach ($value as $vKey => $vVal) {
                        if ($vVal != null) {
                            $NbErrors++;
                        }
                    }
                }
            }
            /*--------------------------------------------------------------------------------------------------------*/
            /* En cas de non erreur dans la vérification des champs issue du formulaire ------------------------------*/
            /*--------------------------------------------------------------------------------------------------------*/
            if ($NbErrors === 0) {

                /*----------------------------------------------------------------------------------------------------*/
                /* Préparation des actions dans la table historique --------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/

                // Récup les anciennes valeurs de l'item qui va être modifié.
                $req = $this->Nom_Tab->findItemQuery($id, $table, $champs, $_GET['id']);// La fonction find est dans le dossier *racine*/App/Table fichier Nom_TabTable.php.
                $donnees = $this->Nom_Tab->findItem($req);// La fonction find est dans le dossier *racine*/App/Table fichier Nom_TabTable.php.
                $jsonLastValue = array();
                $jsonValue = array();

                // Parcours des données reçue depuis la fonction finditem ci-dessus.
                foreach ($donnees[0] as $d => $dd) {
                    // Parcours des données reçue depuis le formulaire POST.
                    foreach ($_POST as $p => $pp) {
                        // str_replace enlève le préfixe item des champs hidden de l'autocomplétion.
                        $p = str_replace("item", "", $p);
                        // Condition qui permet de reperer les clés identique entre le tableau POST et le tableau d'objet données.(et écarte le champs password qui n'est pas enregistré dans l'historique.)
                        if (!empty($p) && $p == $d && $donnees[0]->$p != $pp && $p != 'password') {
                            $lastValue = $d . ' -> ' . $dd;
                            $value = $pp;

                            // Attribution des valeurs dans un array.
                            array_push($jsonLastValue, $lastValue); // array contenant les anciennes valeurs.
                            array_push($jsonValue, $value); // array contenant les nouvelles valeurs.

                        }
                    }
                }

                // Parcours des deux array (1er anciennes valeurs, 2eme nouvelles valeurs) contenant les valeurs à ajouter dans la table historique.
                $jsonV = array();
                foreach ($jsonLastValue as $jLV => $LV) {
                    foreach ($jsonValue as $jL => $V) {
                        if ($jLV == $jL) {
                            $ValueJson = array($LV => $V);
                            // Ajout de l'ancienne valeur en tant que clé et nouvel valeur en tant que valeur. ('ancienne' => 'nouvelle')
                            array_push($jsonV, $ValueJson);
                        }
                    }
                }

                /* Fin de préparation des actions pour la table historique -------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/

                /*----------------------------------------------------------------------------------------------------*/
                /* Préparation des données pour la mise à jour de l'item ---------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/

                // Préparation des colonnes et des valeurs à envoyer à la fonction update.
                $colInsert = array();
                $valueInsert = array();
                $valueParam = array();


                // Parcours du fichier conf (dossier *racine*/conf/Fields.php).
                foreach ($champs as $c => $cc) {
                    if (isset($cc['liste_detail']) && $cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && $_POST[$c] != null) {
                        // Condition qui permet d'isoler le champs mot de passe pour lui attribuer un cryptage.
                        if ($c == 'password') {
                            $_POST[$c] = password_hash($_POST[$c], PASSWORD_BCRYPT);
                            $colIn = $c;
                            $colvalue = ":" . $c;
                            $value = $_POST[$c];
                        } else {
                            $colIn = $c;
                            $colvalue = ":" . $c;
                            $value = $_POST[$c];
                        }
                        array_push($colInsert, $colIn); // array contenant les colonnes pour l'insertion.
                        array_push($valueInsert, $colvalue); // array contenant le nom des paramètres pour la requete préparé.
                        array_push($valueParam, $value); // array contenant les valeurs à attribuer au paramètres.
                    }


                    if (isset($cc['type_input']) && $cc['type_input'] == 'checkboxDep') {

                        if (!isset($_POST[$c])) {
                            $delete = $this->Nom_Tab->findItem('DELETE FROM user_dept WHERE user_dept.id_user =' . abs($_GET['id'])); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                        }
                        if (isset($_POST[$c])) {

                            $nbValTab = array_count_values($_POST[$c]);

                            $valReqDelete = implode($_POST[$c], ',');

                            if ($nbValTab < self::MAX_SITE) {
                                $delete = $this->Nom_Tab->findItem('DELETE FROM user_dept WHERE NOT IN (' . $valReqDelete . ') AND user_dept.id_user =' . abs($_GET['id'])); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.

                            } else {
                                $delete = $this->Nom_Tab->findItem('DELETE FROM user_dept WHERE user_dept.id_user =' . abs($_GET['id'])); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                            }

                            foreach ($_POST[$c] as $ChekKey => $ChekVal) {
                                $isExist = $this->Nom_Tab->findItem('SELECT user_dept.id_user, user_dept.id_dept FROM user_dept WHERE user_dept.id_user =' . abs($_GET['id']) . ' AND user_dept.id_dept =' . abs($ChekVal)); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                if (!$isExist) {
                                    $colCheck = array(0 => "id_user", 1 => "id_dept");
                                    $valueParamCheck = array(0 => ":id_user", 1 => ":id_dept");
                                    $valueCheck = array(0 => $_GET['id'], 1 => $ChekVal);
                                    $resultCheck = $this->Nom_Tab->insertItem('user_dept', $colCheck, $valueParamCheck, $valueCheck); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                }
                            }
                        }
                    }

                    if (isset($cc['type_input']) && $cc['type_input'] == 'checkboxNro') {

                        if (!isset($_POST[$c])) {
                            $delete = $this->Nom_Tab->findItem('DELETE FROM user_site WHERE user_site.id_user =' . abs($_GET['id'])); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                        }
                        if (isset($_POST[$c])) {

                            $nbValTab = array_count_values($_POST[$c]);

                            $valReqDelete = implode($_POST[$c], ',');

                            if ($nbValTab < self::MAX_SITE) {
                                $delete = $this->Nom_Tab->findItem('DELETE FROM user_site WHERE NOT IN (' . $valReqDelete . ') AND user_site.id_user =' . abs($_GET['id'])); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.

                            } else {
                                $delete = $this->Nom_Tab->findItem('DELETE FROM user_site WHERE user_site.id_user =' . abs($_GET['id'])); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                            }

                            foreach ($_POST[$c] as $ChekKey => $ChekVal) {
                                $isExist = $this->Nom_Tab->findItem('SELECT user_site.id_user, user_site.id_site FROM user_site WHERE user_site.id_user =' . abs($_GET['id']) . ' AND user_site.id_site =' . abs($ChekVal)); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                if (!$isExist) {
                                    $colCheck = array(0 => "id_user", 1 => "id_site");
                                    $valueParamCheck = array(0 => ":id_user", 1 => ":id_site");
                                    $valueCheck = array(0 => $_GET['id'], 1 => $ChekVal);
                                    $resultCheck = $this->Nom_Tab->insertItem('user_site', $colCheck, $valueParamCheck, $valueCheck); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                }
                            }
                        }
                    }


                    if (isset($cc['type_input']) && $cc['type_input'] == 'inputMultiple') {

                        if (!isset($_POST[$c])) {
                            $delete = $this->Nom_Tab->findItem('DELETE FROM user_site WHERE user_site.id_user =' . abs($_GET['id'])); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                        }
                        if (isset($_POST[$c])) {
                            $nbValTab = array_count_values($_POST[$c]);
                            $valReqDelete = implode($_POST[$c], ',');
                            if ($nbValTab < self::MAX_SITE) {
                                $delete = $this->Nom_Tab->findItem('DELETE FROM user_site WHERE NOT IN (' . $valReqDelete . ') AND user_site.id_user =' . abs($_GET['id'])); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.

                            } else {
                                $delete = $this->Nom_Tab->findItem('DELETE FROM user_site WHERE user_site.id_user =' . abs($_GET['id'])); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                            }

                            foreach ($_POST[$c] as $MultKey => $MultVal) {
                                $isExist = $this->Nom_Tab->findItem('SELECT user_site.id_user, user_site.id_site FROM user_site WHERE user_site.id_user =' . abs($_GET['id']) . ' AND user_site.id_site =' . abs($MultVal)); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                if (!$isExist) {
                                    $colCheck = array(0 => "id_user", 1 => "id_site");
                                    $valueParamCheck = array(0 => ":id_user", 1 => ":id_site");
                                    $valueCheck = array(0 => $_GET['id'], 1 => $MultVal);

                                    $resultCheck = $this->Nom_Tab->insertItem('user_site', $colCheck, $valueParamCheck, $valueCheck); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                }
                            }
                        }
                    }
                }

                // Mise à jour du user dans la base de données.
                $result = $this->Nom_Tab->updateitem($id, $table, $colInsert, $valueInsert, $valueParam, $_GET['id']); // La fonction updateitem est dans le dossier *racine*/Core/Table fichier Table.php.

                $DataUser = $this->Nom_Tab->finditem('SELECT users.roleId, users.responsable FROM users WHERE  users.id = '.abs($_GET['id']));
                if(isset($DataUser[0]->responsable) && isset($DataUser[0]->roleId) && in_array($DataUser[0]->roleId, array(5,7, 8,11))){
                    $VpiDuUser = $this->Nom_Tab->finditem('SELECT users.id, users.responsable FROM users WHERE users.id ='.abs($DataUser[0]->responsable));
                    if(isset($VpiDuUser[0]->responsable)){
                        $RRDuUser = $this->Nom_Tab->finditem('SELECT users.id FROM users WHERE users.id ='.abs($VpiDuUser[0]->responsable));
                        if(isset($RRDuUser[0]->id)){
                            $UpdateRRDuUser = $this->Nom_Tab->finditem("UPDATE users SET responsable_n2 = ".abs($RRDuUser[0]->id)." WHERE id = ".abs($_GET['id']));
                        }
                    }
                }

                $colInsertFile = array(0 => "id_user", 1 => "id_item", 2 => "nom", 3 => "path", 4 => "conf", 5 => "champ", 6 => 'datequote');
                $valueInsertFile = array(0 => ":id_user", 1 => ":id_item", 2 => ":nom", 3 => ":path", 4 => ":conf", 5 => ":champ", 6 => 'NOW()');
                /*----------------------------------------------------------------------------------------*/
                /* Concerne l'upload de fichier -------------- -------------------------------------------*/
                /*----------------------------------------------------------------------------------------*/

                $ifFile = 0;

                // Parcours de la conf.
                foreach ($champs as $c => $cc) {

                    if (isset($_POST[$c]) && $cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && $cc['type_input'] == 'file' && isset($cc['cheminDossier'])) {


                        // Parcours de l'array file.
                        foreach ($_POST[$c] as $post_file => $p_f) {

                            $file = $p_f;
                            // Récupère les 5 premiers caractères du champ file.
                            $n = substr($p_f, 0, 5);

                            // Si les fichier n'est pas un data url (fichier déjà mit en table temporaire).
                            if ($n != 'data:') {

                                // Récupération des informations du fichier en table temporaire.
                                $reqVal = 'SELECT file_tmp.id, file_tmp.id_user, file_tmp.nom, file_tmp.path, file_tmp.conf, file_tmp.champ, file_tmp.typeMime FROM file_tmp';
                                $reqVal .= ' WHERE file_tmp.nom = ?';
                                $resultReq = $this->Nom_Tab->findItemData($reqVal, $p_f);

                                if ($resultReq) {
                                    // Assigniation des valeurs.
                                    $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $_GET['id'], 2 => $resultReq[0]->nom, 3 => $resultReq[0]->path, 4 => $resultReq[0]->conf, 5 => $resultReq[0]->champ);
                                    // Insertion du fichier dans la table définitif.
                                    $result = $this->Nom_Tab->insertItem($tableFile, $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                    // Si le fichier existe bien en table temporaire.
                                    if ($result) {
                                        // Copie du fichier dans le dossier temporaire dans le dossier définitif.
                                        copy('./fileTmp/' . $resultReq[0]->path, $cc['cheminDossier'] . $resultReq[0]->path);
                                        // Si le copie est OK.
                                        if (copy('./fileTmp/' . $resultReq[0]->path, $cc['cheminDossier'] . $resultReq[0]->path)) {
                                            // Suppression du fichier dans le dossier temporaire.
                                            unlink('./fileTmp/' . $resultReq[0]->path);
                                            // Suppression du fichier dans la table temporaire.
                                            $this->Nom_Tab->deleteItem($resultReq[0]->id, 'file_tmp');

                                        }
                                    }
                                }
                            } else {


                                // Dans le cas ou le fichier n'est pas en table temporaire (soit un fichier jpg ou alors aucune erreur a eu lieu lors de la première soumission du formulaire).

                                // Récup le Type MIME du fichier. ------------------------------------------------------
                                $mime = substr($file, strpos($file, "data:"), strpos($file, ",") - strlen($file));

                                // Récup les données du fichier sans le type mime. -------------------------------------
                                $file = str_replace($mime . ',', '', $file);
                                $file = str_replace(' ', '+', $file);
                                $data = base64_decode($file);
                                // -------------------------------------------------------------------------------------

                                // Affectation des colonnes et valeurs pour l'insertion en bdd du fichier ---------------
                                $name = uniqid();

                                $file = $name . '.' . $_POST[$c . '_ext'][$post_file];
                                $fileContent = $cc['cheminDossier'] . $name . '.' . $_POST[$c . '_ext'][$post_file];

                                $success = file_put_contents($fileContent, $data);
                                $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $_GET['id'], 2 => $name, 3 => $file, 4 => 'Users', 5 => $c);
                                // Insertion du fichier dans la table définitif.
                                $result = $this->Nom_Tab->insertItem($tableFile, $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                            }
                        }
                    }

                }
                /* Fin d'upload de fichier ---------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------*/


                /* Fin de la mise à jour de l'item -------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/


                // Si le User existe et que la mise à jour est OK.
                if ($result && !empty($jsonV)) {

                    // Récupération de l'adresse IP.
                    $ip = $this->Nom_Tab->getIpAdress(); // La fonction get_ip_address est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.

                    // Attribution des valeurs à inserer dans la table Historique.
                    $arrayValueHistorique = array('id_user' => $_SESSION['UserID'], 'ip_user' => $ip, 'id_item' => $_GET['id'], 'cle_conf' => $nom_feuille, 'value_modif' => json_encode($jsonV));
                    $colInsertHistorique = array();
                    $valueInsertHistorique = array();
                    $valueParamHistorique = array();

                    // Parcours du fichier conf Historique.
                    foreach ($champsHistorique as $ch => $cch) {
                        if ($cch['profilChamps'][$_SESSION['Droit']]['modification'] == 1) {

                            $colInHistorique = $ch;
                            $colvalueHistorique = ":" . $ch;
                            $valueHistorique = $arrayValueHistorique[$ch];
                            array_push($colInsertHistorique, $colInHistorique); // array contenant les colonnes pour l'insertion.
                            array_push($valueInsertHistorique, $colvalueHistorique); // array contenant le nom des paramètres de la requete préparé.
                            array_push($valueParamHistorique, $valueHistorique); // array contenant les valeurs à envoyer aux paramètres de la requete.
                        }
                    }


                    // Parcours du fichier conf (dossier *racine*/conf/FieldsUsers.php).
                    foreach ($champsHistorique as $ch => $cch) {
                        if (isset($cch['date_now']) && $cch['date_now'] == 1) {
                            $colInDateNow = $ch;
                            $valueDateNow = 'NOW()';
                            array_push($colInsertHistorique, $colInDateNow); // array contenant les colonnes pour l'insertion.
                            array_push($valueInsertHistorique, $valueDateNow); // array contenant le nom des paramètres pour la requete préparé.
                        }
                    }


                    // Insertion dans la table historique des modifications de l'item.
                    $resultHistorique = $this->Nom_Tab->insertItem($tableHistorique, $colInsertHistorique, $valueInsertHistorique, $valueParamHistorique);// La fonction insertItem est dans le dossier *racine*/core/Table fichier Table.php.
                    $resultHistoriqueDelete = $this->Nom_Tab->deleteHistorique(self::HISTORY_DAYS);

                }

                // Redirection si tout est OK.
                if ($result) {

                    if (isset($_GET['id']) && isset($_SESSION['UserID']) && isset($_POST['itemresponsable'])) {

                        if (isset($_POST['roleId']) && $_POST['roleId'] == 5) {
                            $n2 = $this->Nom_Tab->findItem("SELECT responsable FROM users WHERE id = " . abs($_POST['itemresponsable']));
                            $attribTech = $this->Nom_Tab->findItem("UPDATE users SET  responsable_n2 = '" . abs($n2[0]->responsable) . "' WHERE id =" . abs($_GET['id']));
                        }
                    }

                    // Si l'item existe en base de données.

                    if (isset($_SESSION['UserID']) && isset($_GET['id']) && $_SESSION['UserID'] == $_GET['id']) {
                        $usersForUpdate = $this->Nom_Tab->findItem('SELECT 
                                    users.id,
                                    users.nom,
                                    users.prenom,
                                    users.email,
                                    users.login,
                                    users.roleId,
                                    users_roles.role_nom as profil
                                    FROM users
                                    LEFT JOIN users_roles on users.roleId = users_roles.id
                                    WHERE users.id = ' . intval($_SESSION['UserID']), true);
                        $_SESSION['UserID'] = $usersForUpdate[0]->id;
                        $_SESSION['login'] = $usersForUpdate[0]->login;
                        $_SESSION['email'] = $usersForUpdate[0]->email;
                        $_SESSION['Droit'] = $usersForUpdate[0]->roleId;
                        $_SESSION['nom'] = $usersForUpdate[0]->nom;
                        $_SESSION['prenom'] = $usersForUpdate[0]->prenom;
                        $_SESSION['profil'] = $usersForUpdate[0]->profil;
						
						
                    }

                    $_SESSION['succes'] = 2;
                    header('Location: index.php?p=Users.listUsers&&XLS=Users&collapsId=' . abs($_GET['collapsId']));
                    exit();

                } else {
                    $_SESSION['fail'] = 1;
                    header('Location: index.php?p=Users.editUser&id=' . abs($_GET['id']) . '&XLS=Users&collapsId=' . abs($_GET['collapsId']));
                    exit();
                }

            }
            /*--------------------------------------------------------------------------------------------------------*/
            /* Fin de mise à jour de l'item, d'insertion des modification dans l'historique + redirection terminée ---*/
            /* Pour rappel (la partie ci-dessus traite et concerne uniquement le cas ou le formulaire à été soumis). -*/
            /*--------------------------------------------------------------------------------------------------------*/
        }

        /*------------------------------------------------------------------------------------------------------------*/
        /* Préparation des données pour l'affichage ------------------------------------------------------------------*/
        /*------------------------------------------------------------------------------------------------------------*/

        if (!isset($_GET['groupe'])) {
            $getGroupe = null;
        } else {
            if ($_GET['groupe'] && $_GET['groupe'] != 'null') {
                $getGroupe = abs($_GET['groupe']);
            } else {
                $getGroupe = null;
            }
        }

        $ifFile = 0;
        $options = array();
        $reqOptionsP = array();

        // Parcours du fichier conf (dossier *racine*/conf/FieldsUsers.php).
        foreach ($champs as $auto_c => $a_c) {

            if (isset($a_c['type']) && $a_c['type'] == "list_sql" && $a_c['autocomplete'] == 0) {

                    if ($auto_c == 'responsable') {

                        // Récup les valeurs à afficher dans le select(html).
                        $option = $this->Nom_Tab->findItem('SELECT id, CONCAT(nom," ",prenom) as responsable_alias FROM users WHERE roleId = ' . abs($_SESSION['Droit']) . ' ORDER BY responsable_alias ASC');
                        $options[$auto_c] = $option;
                        array_push($reqOptionsP, $option);


                    } else {

                        $options[$auto_c] = array();
                        // Récup les valeurs à afficher dans le select(html).
                        $reqOptions = $this->Nom_Tab->allItemsForSelectQuery($a_c);
                        $option = $this->Nom_Tab->allItemsForSelect($reqOptions);
                        $options[$auto_c] = $option;
                        array_push($reqOptionsP, $reqOptions);
                    }

                }

        }

        // Si l'autocomplétion a été déclanchée.
        if (isset($_GET['champs']) && !empty($_GET['champs']) && isset($_GET['key']) && !empty($_GET['key']) && isset($_GET['conf']) && !empty($_GET['conf']) && isset($_GET['autocomp']) && $_GET['autocomp'] == 1) {
            if(isset($champs[$_GET['key']])){
                if($_GET['key'] == 'responsable' && isset($_GET['id'])){
                    $optionC = json_encode($this->Nom_Tab->autoCompletionWithConcatForResp($champs[$_GET['key']], $_GET['champs'], $_GET['conf']));
                }else{
                    $optionC = json_encode($this->Nom_Tab->autoCompletion($champs[$_GET['key']], $_GET['champs'], $_GET['conf']));
                }
                echo $optionC;
                exit();
            }
        }




        if (isset($options['roleId'])) {
            foreach ($options['roleId'] as $optKey => $optVal) {
                if ($optVal->id == 10000 && $_SESSION['Droit'] != 10000) {
                    unset($options['roleId'][$optKey]);
                }
            }
        }



        // Condition qui verifie si on a bien un id de l'item à afficher.
        if (isset($_GET['id']) || !empty($_GET['id'])) {
            $_GET['id'] = intval($_GET['id']);
        }

        if (!isset($_GET['id']) || empty($_GET['id']) && !is_int($_GET['id'])) {
            header('index.php?p=Principal.notFound');
            exit();
        } else {
            if (isset($_GET['id']) || !empty($_GET['id']) && is_int($_GET['id'])) {
                $debut = microtime(true);
                $req = $this->Nom_Tab->findItemQuery($id, $table, $champs, $_GET['id']);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                $donnees = $this->Nom_Tab->findItem($req);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.

                if ($donnees) {
                    $donnees = $donnees[0];

                    if ($ifFile == 1) {
                        $fileDonneesREQ = $this->Nom_Tab->findFileQuery('id_item', 'file_moteur', 'file_moteur.nom ,file_moteur.path, file_moteur.conf, file_moteur.champ, file_moteur.datequote', $donnees->id, 'Users');// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                        $fileDonnees = $this->Nom_Tab->findItem($fileDonneesREQ);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.

                    } else {
                        $fileDonneesREQ = null;
                        $fileDonnees = null;
                    }

                }
            }
        }
        // Condition si l'item existe bien en base de données.
        if ($donnees != false) {
            if (!isset($errors) || empty($errors)) {
                $errors = null;
            }
            if (!isset($champ) || empty($champ)) {
                $champ = null;
            }
            if (!isset($requpdate) || empty($requpdate)) {
                $requpdate = null;
            }
            if (!isset($historique) || empty($historique)) {
                $historique = null;
            }
            if (!isset($option) || empty($option)) {
                $option = null;
            }
            if (!isset($optionGroupe) || empty($optionGroupe)) {
                $optionGroupe = null;
            }
            if (!isset($reqOptionsP) || empty($reqOptionsP)) {
                $reqOptionsP = null;
            }

            $fin = microtime(true);
            $delai = $fin - $debut;
            $delai = substr($delai, 0, 4);
            // Affichage en vue.
            $debugMode = $_SESSION['db_DebugMode'];
            $LocalTime = $_SESSION['db_LocalTime'];

            $form = new BootstrapForm($donnees);
            $ListDep = $this->Nom_Tab->listeOption('secto_departements', 'code_vc');
            $ListSec = null;
            // $ListSec = $this->Nom_Tab->listeOption('nros', 'name');
            // $ListSec = $this->Nom_Tab->listeOptionLimit('nros', 'name', 20);

            $ListDepAttrib = $this->Nom_Tab->findItem('SELECT user_dept.id, user_dept.id_user, user_dept.id_dept FROM user_dept WHERE user_dept.id_user =' . abs($_GET['id']));
            $ListDepAttribarray = array();
            if (!isset($ListDepAttrib) && $ListDepAttrib == null) {
                $ListDepAttribarray = null;
            } else {
                foreach ($ListDepAttrib as $ListDepAttribKey => $ListDepAttribVal) {
                    $ListDepAttribarray[$ListDepAttribVal->id_dept] = $ListDepAttribVal->id_dept;
                }
            }

            $ListSectoAttrib = $this->Nom_Tab->findItem('SELECT user_site.id, user_site.id_user, user_site.id_site  FROM user_site WHERE user_site.id_user =' . abs($_GET['id']));

            $ListSectoAttribarray = array();
            if (!isset($ListSectoAttrib) && $ListSectoAttrib == null) {
                $ListSectoAttribarray = null;
            } else {
                foreach ($ListSectoAttrib as $ListSectoAttribKey => $ListSectoAttribVal) {
                    $ListSectoAttribarray[$ListSectoAttribVal->id_site] = $ListSectoAttribVal->id_site;
                }
            }

            $ListSecto = array();
            foreach ($ListSectoAttribarray as $ListSectoAttribarrayKey => $ListSectoAttribarrayVal) {
                $ListSectoF = $this->Nom_Tab->findItem('SELECT nros.id, nros.name FROM nros WHERE nros.id =' . $ListSectoAttribarrayVal);
                array_push($ListSecto, $ListSectoF);
            }

            if (!isset($ListSecto) || empty($ListSecto)) {
                $ListSecto = null;
            }

            if (!isset($ListSec) || empty($ListSec)) {
                $ListSec = null;
            }

            // N+1 des VPI
            if ($donnees->roleId == 4) {
                $listResponsable = $this->Nom_Tab->findItem('SELECT id, CONCAT(nom, " ", prenom) as nom FROM users WHERE roleId = 3');
            } // N+1 des PCI
            elseif ( isset($donnees->roleId) && in_array($donnees->roleId, array(5,6,7,8,11))) {
                $listResponsable = $this->Nom_Tab->findItem('SELECT id, CONCAT(nom, " ", prenom) as nom FROM users WHERE roleId = 4');
            } else {
                $listResponsable = null;
            }


            $this->render('Users.editUser', compact('donnees', 'listResponsable', 'ListSec', 'ListSecto', 'LocalTime', 'ListSectoAttribarray', 'ListDepAttribarray', 'ListDep', 'reqOptionsP', 'optionGroupe', 'fileDonnees', 'debugMode', 'historique', 'delai', 'req', 'fileDonneesREQ', 'requpdate', 'errors', 'options', 'tabXlsFields', 'champs', 'form', 'delai', 'req', 'nom_feuille', 'nom_feuilleHistorique'));
            exit();
        } else {
            $this->render('Principal.notFound');
            exit();
        }
    }

    public function deleteUser()
    {

        if (isset($_GET['id']) && isset($_SESSION['Droit'])) {

            if (!isset($_GET['XLS'])) {
                $_GET['XLS'] = 'Users';
            }

            // Récup du fichier conf Utilisateurs (dossier *racine*/conf/FieldsUsers.php).
            $tabXlsFields = listParamUsers();
            $search_array = $tabXlsFields;

            // Verifie si la conf correspondant à la page demmandé existe.
            if (!isset($_GET['XLS']) || empty($_GET['XLS']) || !array_key_exists($_GET['XLS'], $search_array)) {
                header('Location: index.php?p=Principal.notFound');
                exit();
            }

            if (!isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['delete'])) {
                header('Location: index.php?p=Principal.notFound');
                exit();
            } elseif (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['delete']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['delete'] != 1) {
                header('Location: index.php?p=Principal.notFound');
                exit();
            }


            $id = $tabXlsFields['Users']['bdd_id'];
            $table = $tabXlsFields['Users']['bdd_table'];

            // Nom de la colonne à modifier.
            $value = "hidden";

            // Fait passer la valeur de la colonne hidden à 1.
            $result = $this->Nom_Tab->desactiveitem($id, $table, $value, $_GET['id']); // Fonction dans le dossier *racine*/app/Table fichier Nom_TabTable.php.

            // Si la désactivation de l'item est OK.
            if ($result) {
                $_SESSION['delete'] = 1;
                header('Location: index.php?p=Users.listUsers&XLS=Users');
                exit();
            }


        }
    }

    public function deleteFile()
    {

        if (isset($_POST['id_item']) && isset($_POST['conf_file']) && isset($_POST['champ_file'])) {

            $tabXlsFields = listParamUsers();
            $champs = $tabXlsFields['Users']['champs'];

            foreach ($champs as $key => $val) {
                if (isset($val['type_input']) && $val['type_input'] == 'file') {

                    if ($key == $_POST['conf_file']) {
                        if ($val['modeSup'] == 'hidden') {
                            $req = 'UPDATE file_moteur SET hidden = 1 WHERE id = ' . intval($_POST['id_item']);
                            $result = $this->Nom_Tab->findItem($req); // Fonction dans le dossier *racine*/app/Table fichier Nom_TabTable.php.

                        } elseif ($val['modeSup'] == 'delete') {
                            unlink('./file/' . $_POST['champ_file']);
                            $req = 'DELETE FROM file_moteur WHERE id = ' . intval($_POST['id_item']);
                            $result = $this->Nom_Tab->findItem($req); // Fonction dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                        }

                    }
                }
            }
        }

    }

    public function callAjaxForSelectFromListItem(){

        if(isset($_GET['idItem']) && isset($_GET['key']) && isset($_GET['alias']) && isset($_GET['XLS']) && isset($_GET['idOptiondefaut'])){

            $tabXlsFields = listParamUsers();

            if(isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']])){

                $arrayChamp = $tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']];
                $result = $this->Nom_Tab->findItem('SELECT '.$arrayChamp['bdd_id'].', '.$arrayChamp['bdd_value'].' as '.$arrayChamp['bdd_table_t'].' FROM '.$arrayChamp['bdd_table'].' WHERE '.$arrayChamp['bdd_id'].' != '.intval($_GET['idOptiondefaut']).' ORDER BY '.$arrayChamp['bdd_value'].' ASC');

                if($result){

                    foreach ($result as $key =>$val){
                        echo '<option value="'.$val->id.'">'.$val->{$_GET['alias']}.'</option>';
                    }
                }
            }

        }
    }


    // Fonction pour mettre à jour un champs editable depuis LIST de types (select ou autocomplete)
    public function updateSelectFromListItem()
    {
        $return = 0;
        // verif user connecte
        if (isset($_SESSION['Droit'])) {
            // Verif params envoyés
            if (isset($_GET['idItem']) && isset($_GET['key']) && isset($_GET['alias']) && isset($_GET['XLS']) && isset($_GET['idOption'])) {
                $tabXlsFields = listParamUsers();
                // verif conf ok
                if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]) && isset($tabXlsFields[$_GET['XLS']]['bdd_table']) && $_GET['idOption'] != null) {
                    // verif droit modification
                    if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"])) {
                        if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"][$_SESSION['Droit']])) {
                            if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"][$_SESSION['Droit']]["modification"])) {
                                if ($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"][$_SESSION['Droit']]["modification"] == 1) {
                                    // Requete : Verification que l'item a modifier fait bien parti de la liste visible par le user:
                                    $query = "SELECT COUNT(*) AS total " .
                                        "FROM " . $tabXlsFields[$_GET['XLS']]['bdd_table'] . " " .
                                        "WHERE " . $tabXlsFields[$_GET['XLS']]['bdd_id'] . "=" . intval($_GET['idItem']);

                                    if (isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']])) {
                                        if (isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["req_sql"])) {
                                            if ($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["req_sql"] == 1) {

                                                if (isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["requete"])) {
                                                    if (trim($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["requete"]) != "") {
                                                        $query .= " " . $tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["requete"];
                                                    }
                                                }

                                            }
                                        }
                                    }
                                    // Verif si nb item = 1 avec droits de lecture du user
                                    $result = $this->Nom_Tab->findItem($query);
                                    if ($result) {
                                        if (1 || isset($result[0])) {
                                            if (1 || isset($result[0]->total)) {
                                                if (1 || $result[0]->total == 1) {
                                                    unset($result);
                                                    // Verif que la valeur envoyée est bien dans la liste
                                                    //intval($_GET['idOption'])
                                                    $query = "SELECT COUNT(*) AS total " .
                                                        "FROM (" . $tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]['bdd_table'] . " ";
                                                    if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["bdd_table_t"])) {
                                                        if (trim($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["bdd_table_t"]) != "") {
                                                            $query .= " AS " . $tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["bdd_table_t"];
                                                        }
                                                    }
                                                    $query .= ") " .
                                                        "WHERE " . $tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]['bdd_id'] . "=" . intval($_GET['idOption']);
                                                    if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["bdd_condition"])) {
                                                        if (trim($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["bdd_condition"]) != "") {
                                                            $query .= " AND " . $tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["bdd_condition"];
                                                        }
                                                    }

                                                    $result = $this->Nom_Tab->findItem($query);
                                                    if ($result) {
                                                        if (isset($result[0])) {
                                                            if (isset($result[0]->total)) {
                                                                if ($result[0]->total == 1) {
                                                                    unset($result);

                                                                    // Update BDD
                                                                    $update = $this->Nom_Tab->findItem('UPDATE ' . $tabXlsFields[$_GET['XLS']]['bdd_table'] . ' SET ' . $_GET['key'] . ' = "' . intval($_GET['idOption']) . '" WHERE ' . $tabXlsFields[$_GET['XLS']]['bdd_id'] . ' = ' . intval($_GET['idItem']));

                                                                    $return = 1;

                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }
        print(json_encode(["return"=>$return, "error"=>$return == 0 ? '<em>'.$_GET['key'].' Veuillez choisir un élément existant parmi la liste.</em>' : null]));
    }

//    public function updateSelectFromListItem(){
//
//        if(isset($_GET['idItem']) && isset($_GET['key']) && isset($_GET['alias']) && isset($_GET['XLS']) && isset($_GET['idOption'])){
//            $tabXlsFields = listParamUsers();
//
//
//            if(isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]) && isset($tabXlsFields[$_GET['XLS']]['bdd_table']) && $_GET['idOption'] != null){
//                $update = $this->Nom_Tab->findItem('UPDATE '.$tabXlsFields[$_GET['XLS']]['bdd_table'] .' SET '.$_GET['key'].' = "' .intval($_GET['idOption']).'" WHERE '.$tabXlsFields[$_GET['XLS']]['bdd_id'].' = '.intval($_GET['idItem']));
//
//                //  $result = $this->Nom_Tab->findItem('UPDATE '.$tabXlsFields[$_GET['XLS']]['bdd_table'] .' SET '.$_GET['key'].' = 1');
//
//
//            }
//        }
//    }

    // Fonction pour mettre à jour un champs editable depuis LIST du reste des types (text, number ...)
    public function updateListItem()
    {
        $return = 0;
        // verif user connecte
        if (isset($_SESSION['Droit'])) {
            // Verif params envoyés
            if (isset($_GET['idItem']) && isset($_GET['key']) && isset($_GET['XLS']) && isset($_GET['value'])) {
                $tabXlsFields = listParamUsers();
                // verif conf ok
                if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]) && isset($tabXlsFields[$_GET['XLS']]['bdd_table'])) {

                    // verif droit modification
                    if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"])) {
                        if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"][$_SESSION['Droit']])) {
                            if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"][$_SESSION['Droit']]["modification"])) {
                                if ($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"][$_SESSION['Droit']]["modification"] == 1) {

                                    $errors = new ControleObjetConfig(); // L'objet controleForm est dans le dossier *racine*/App/config.
                                    $errors->existSession($_SESSION['UserID']); // Verifie avant d'éxécuter une méthode si User est connecté.(sa évite qu'un formulaire non issue de l'application soit soumis à la place)
                                    $edit = 1;
                                    $cc = $tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']];

                                    // Requete : Verification que l'item a modifier fait bien parti de la liste visible par le user:
                                    $query = "SELECT COUNT(*) AS total " .
                                        "FROM " . $tabXlsFields[$_GET['XLS']]['bdd_table'] . " " .
                                        "WHERE " . $tabXlsFields[$_GET['XLS']]['bdd_id'] . "=" . intval($_GET['idItem']);

                                    if (isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']])) {
                                        if (isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["req_sql"])) {
                                            if ($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["req_sql"] == 1) {

                                                if (isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["requete"])) {
                                                    if (trim($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["requete"]) != "") {
                                                        $query .= " " . $tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["requete"];
                                                    }
                                                }

                                            }
                                        }
                                    }
                                    // Verif si nb item = 1 avec droits de lecture du user
                                    $result = $this->Nom_Tab->findItem($query);

                                    if ($result) {
                                        if (1 || isset($result[0])) {
                                            if (1 || isset($result[0]->total)) {
                                                if (1 || $result[0]->total == 1) {
                                                    if(!isset($cc['unique'])){
                                                        $cc['unique'] = 0;
                                                    }
                                                    switch ($cc['type_input']) :
                                                        case "text":
                                                            $errors->validVarchar($_GET['value'], $_GET['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_GET['XLS'], $edit, $_GET['idItem']);
                                                            break;
                                                        case "textarea":
                                                            $errors->validVarchar($_GET['value'], $_GET['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_GET['XLS'], $edit, $_GET['idItem']);
                                                            break;
                                                        case "date":
                                                            $errors->validDate($_GET['value'], $_GET['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_GET['XLS'], $edit, $_GET['idItem']);
                                                            break;
                                                        case "datetime":
                                                            $errors->validDateTime($_GET['value'], $_GET['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_GET['XLS'], $edit, $_GET['idItem']);
                                                            break;
                                                        case "number":
                                                            $errors->validInt(intval($_GET['value']), $_GET['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_GET['XLS'], $edit, $_GET['idItem']);
                                                            break;
                                                        case "phone":
                                                            $errors->validPhone($_GET['value'], $_GET['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_GET['XLS'], $edit, $_GET['idItem']);
                                                            break;
                                                        case "email":
                                                            $errors->validMail($_GET['value'], $_GET['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_GET['XLS'], $edit, $_GET['idItem']);
                                                            break;
//                                        case "url":
//                                            $errors->validUrl($_GET['value'], $_GET['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_GET['XLS'], $edit, $_GET['idItem']);
//                                            break;
                                                        default:
                                                    endswitch;
                                                }
                                            }
                                        }
                                    }
                                    if (isset($errors->errors[$_GET['key']]) && !empty($errors->errors[$_GET['key']])) {
                                        $return = 0;
                                        $error = '<em>'.$_GET['key'].'</em> '.$errors->errors[$_GET['key']];
                                        if($_GET['key'] == "email"){
                                            $error = "<em>".$_GET['key']."</em> ".$errors->errors[$_GET['key']]."<br><em>Exemple : 'email@email.fr'</em>";
                                        }

                                    } else {
                                        $return = 1;
                                        $error = NULL;
                                    }

                                    if ($return == 1) {
                                        // Update BDD
                                        $update = $this->Nom_Tab->findItem('UPDATE ' . $tabXlsFields[$_GET['XLS']]['bdd_table'] . ' SET ' . $_GET['key'] . ' = "' . htmlspecialchars($_GET['value']) . '" WHERE ' . $tabXlsFields[$_GET['XLS']]['bdd_id'] . ' = ' . intval($_GET['idItem']));

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        print(json_encode(["return"=>$return, "error"=>$error]));
    }

}

