<?php

namespace App\Controller;

use App;
use App\config\ControleObjetConfig;
use Core\Config;
use Core\HTML\BootstrapForm;

require_once("../config/Fields.php");
require_once("../config/FieldsHistorique.php");
require_once("../config/TypeMIME.php");
require_once("../config/config.php");

$config = Config::getInstance(ROOT . '/config/config.php');
$arrayConfig = (array)$config;
foreach ($arrayConfig as $arrayConfigKey => $arrayConfigVal) {
    $_SESSION['db_DebugMode'] = $arrayConfigVal['db_DebugMode'];
    $_SESSION['db_LocalTime'] = $arrayConfigVal['db_LocalTime'];
}

class PrincipalController extends AppController
{ // hérite des méthodes protégées de la class parent AppController.(Dossier *racine*/app/App.php)

    protected $tableName;
    protected $Nom_Tab;

    CONST PER_PAGE = 25; // Nombre de résultats affichés par default.(pagination)
    CONST MAX_PER_PAGE = 350; // Evite que un utilisateur affiche + de 350 résultats en modifiant la valeur du limit dans l'url.(pagination)
    CONST HISTORY_DAYS = 30;  // Nombre de jours pour le delete des tuples de l'historique.
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

    public function index()
    {
        $Version = self::VERSION;
        $debugMode = $_SESSION['db_DebugMode'];
        $form = new BootstrapForm($_POST);
        $this->render('Principal.index', compact(
            'form',
            'debugMode',
            'Version'
        ));
        exit();
    }

    public function listItem()
    {

        $debut = microtime(true);


        if (isset($_GET['XLS'])) {
            $_SESSION[$_GET['XLS']]['XLS'] = $_GET['XLS'];
        }

        if (isset($_GET['delFilter']) && $_GET['delFilter'] == 1) {
            unset($_SESSION[$_GET['XLS']]);
            unset($_GET['delFilter']);
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

        // Récup du fichier conf (dossier *racine*/conf/FieldsUsers.php).
        $tabXlsFields = listParamTab();
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


        $erreur = array();
        // Verifie si les clés obligatoire sont bien présentes.
        if (!isset($tabXlsFields[$_GET['XLS']]['profilSql']) || empty($tabXlsFields[$_GET['XLS']]['profilSql'])) {
            $erreur['profilSql'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['bdd_table']) || empty($tabXlsFields[$_GET['XLS']]['bdd_table'])) {
            $erreur['bdd_table'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['champs']) || empty($tabXlsFields[$_GET['XLS']]['champs'])) {
            $erreur['champs'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['nom_feuille']) || empty($tabXlsFields[$_GET['XLS']]['nom_feuille'])) {
            $erreur['nom_feuille'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['pagination']) || empty($tabXlsFields[$_GET['XLS']]['pagination'])) {
            $erreur['pagination'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['bdd_table_file']) || empty($tabXlsFields[$_GET['XLS']]['bdd_table_file'])) {
            $erreur['bdd_table_file'] = "manquant ou vide";
        }
        if (isset($tabXlsFields[$_GET['XLS']]['profilSql'])) {
            if (!isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']])) {
                $erreur['Le profil utilisateurs'] = "manquant ou n'est pas autorisé à voir la page";
            }
        }

        if ($erreur != null) {
            $erreur = serialize($erreur);
            header('Location: index.php?p=Principal.notFound&erreurConf=' . $erreur . '');
            exit();
        } else {
            // Si les clés sont présent.
            $profilSql = $tabXlsFields[$_GET['XLS']]['profilSql'];
            $table = $tabXlsFields[$_GET['XLS']]['bdd_table'];
            $champs = $tabXlsFields[$_GET['XLS']]['champs'];
            $nom_feuille = $tabXlsFields[$_GET['XLS']]['nom_feuille'];

            if (isset($tabXlsFields[$_GET['XLS']]['champs_groupe'])) {
                $champ_for_edit = $tabXlsFields[$_GET['XLS']]['champs_groupe'];
            } else {
                $champ_for_edit = 0;
            }

            if (isset($tabXlsFields[$_GET['XLS']]['updateFieldsOnchange']) && !empty($tabXlsFields[$_GET['XLS']]['updateFieldsOnchange'])) {
                $updateFieldsOnchange = $tabXlsFields[$_GET['XLS']]['updateFieldsOnchange'];
            } else {
                $updateFieldsOnchange = [];
            }

            $label_id = $tabXlsFields[$_GET['XLS']]['bdd_id'];


            if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['custom_list_edit'])) {
                $custom_list_edit = $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['custom_list_edit'];
            } else {
                $custom_list_edit = null;
            }

            if (isset($tabXlsFields[$_GET['XLS']]['groupe'])) {
                $groupeActive = $tabXlsFields[$_GET['XLS']]['groupe'];
            } else {
                $groupeActive = null;
            }
            if (isset($tabXlsFields[$_GET['XLS']]['table_groupe'])) {
                $table_groupe = $tabXlsFields[$_GET['XLS']]['table_groupe'];
            } else {
                $table_groupe = null;
            }
            if (isset($tabXlsFields[$_GET['XLS']]['champs_groupe'])) {
                $champs_groupe = $tabXlsFields[$_GET['XLS']]['champs_groupe'];
            } else {
                $champs_groupe = null;
            }


            // Vérification si les clés des champs obligatoire sont présent.
            foreach ($champs as $souschamps => $sc) {

                if (isset($sc['type_input']) && $sc['type_input'] != 'categorie' && $sc['type_input'] != 'sous_categorie' && $sc['type_input'] != 'tendance') {
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
        /*------------------------------------------------------------------------------------------------------------*/


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


        $filtre_array_key = array();
        $filtre_array_val = array();
        $champs_array_val = array();
        $champs_array_type = array();
        $active_view_array = array();
        $get_filtre = array();


        if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['filtres'])) {

            $filtres_conf = $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['filtres'];


            foreach ($filtres_conf as $filtres_conf_key => $filtres_conf_val) {

                if (isset($filtres_conf_val['sql']) && !empty($filtres_conf_val['sql'])) {
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
                } else {
                    if (isset($filtres_conf_val['champ_type']) && !empty($filtres_conf_val['champ_type'])) {
                        $active_view = $filtres_conf_val['active_view'];
                        $champs_array_val[$filtres_conf_val['champ_filtre'] . '_' . $filtres_conf_val["champ_type"]] = $filtres_conf_val['champ_filtre'];
                        $champs_array_type[$filtres_conf_key] = $filtres_conf_val['champ_type'];


                        array_push($active_view_array, $active_view);
                        array_push($filtre_array_key, $filtres_conf_key);
                        array_push($filtre_array_val, $champs_array_type[$filtres_conf_key]);

                        if (isset($_GET[$filtres_conf_val['champ_filtre'] . '_' . $filtres_conf_val["champ_type"]])) {

                            $get_filtre[$filtres_conf_val['champ_filtre'] . '_' . $filtres_conf_val["champ_type"]] = ($_GET[$filtres_conf_val['champ_filtre'] . '_' . $filtres_conf_val["champ_type"]]);
                        } elseif (!isset($_GET[$filtres_conf_val['champ_filtre'] . '_' . $filtres_conf_val["champ_type"]]) && isset($_SESSION[$_GET['XLS']][$filtres_conf_val['champ_filtre'] . '_' . $filtres_conf_val["champ_type"]])) {
                            $get_filtre[$filtres_conf_val['champ_filtre'] . '_' . $filtres_conf_val["champ_type"]] = ($_SESSION[$_GET['XLS']][$filtres_conf_val['champ_filtre'] . '_' . $filtres_conf_val["champ_type"]]);
                        }
                    } else if(isset($filtres_conf_val['static_filtre']) && !empty($filtres_conf_val['static_filtre'])){
                        $active_view = $filtres_conf_val['active_view'];
                        $champs_array_val[$filtres_conf_key] = $filtres_conf_val['champ_filtre'];

                        array_push($active_view_array, $active_view);
                        array_push($filtre_array_key, $filtres_conf_key);
                        array_push($filtre_array_val, $filtres_conf_val['static_filtre']);

                        if (isset($_GET[$filtres_conf_key])) {
                            $get_filtre[$filtres_conf_key] = ($_GET[$filtres_conf_key]);
                        } elseif (!isset($_GET[$filtres_conf_key]) && isset($_SESSION[$_GET['XLS']][$filtres_conf_key])) {
                            $get_filtre[$filtres_conf_key] = ($_SESSION[$_GET['XLS']][$filtres_conf_key]);
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
        /*------------------------------------------------------------------------------------------------------------*/


        $reqCount = $this->Nom_Tab->countItem($profilSql, $table, $champs, $filtre, $label_id, $get_filtre, $champs_array_val); // Récup le nombre de résultat (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).


        //print($reqCount);exit;
        $nbTotalPage = $this->Nom_Tab->countAllItems($reqCount); // Récup le nombre de résultat (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).


        $nbPage = ceil(intval($nbTotalPage[0]->total) / $ParPage); // Calcule le nombre de page en fonction du nombre de résultat issue du count.
        $pageCurrent = $this->Nom_Tab->getCurrent($page, $nbPage); // Détermine la page courante (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).


        if (intval($nbTotalPage[0]->total) > 0) {

            $req = $this->Nom_Tab->getItemQuery($profilSql, $table, $champs, $pageCurrent, $ParPage, $filtre, $label_id, $get_filtre, $champs_array_val); // Récuperation de la requete pour l'afficher dans Infos SQL (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).
            $donnees = $this->Nom_Tab->getAllItems($req);

            if (isset($tabXlsFields[$_GET['XLS']]['old_values']) && !empty($tabXlsFields[$_GET['XLS']]['old_values'])
                && isset($tabXlsFields[$_GET['XLS']]['old_values']['champs_related'])
                && !empty($tabXlsFields[$_GET['XLS']]['old_values']['champs_related'])) {

                if (isset($donnees) && !empty($donnees)) {
                    $requeteWhere = $tabXlsFields[$_GET['XLS']]['old_values']['requete_condition'];
                    $relatedChamps = $tabXlsFields[$_GET['XLS']]['old_values']['champs_related'];
                    $old_donnees = $this->Nom_Tab->getAllOldItem($table, $champs, $relatedChamps, $requeteWhere);

                    foreach ($donnees as $k => $v) {
                        for ($i = 0; $i < count($old_donnees); $i++) {
                            if ($v->{$relatedChamps} == $old_donnees[$i]->{$relatedChamps}) {
                                $v->old_value = $old_donnees[$i];
                            } elseif (!isset($v->old_value)) {
                                $v->old_value = null;
                            }
                        }
                    }
                }
            }
            foreach ($champs AS $champK => $champV) {
                foreach ($donnees AS $donnee) {
                    if($_SESSION['Droit'] == 9){
                        // CDP
                        if($donnee->posteId == 41){
                            if($champK == 'note_valide_rgp'){
                                $donnee->{$champK} = $donnee->{"note_valide_cdp"};
                            }
                        } else {
                            if($champK == 'note_valide_cdt'){
                                $donnee->{$champK} = $donnee->{"note_valide_cdp"};
                            }
                        }
                    } else if($_SESSION["Droit"] == 41) {
                        //RGP
                        if($champK == "note_valide_cdt"){
                            $donnee->{$champK} =  $donnee->{"note_valide_rgp"};
                        }
                    }
                    if (isset($champV['defaultValue']) && !empty($champV['defaultValue']) && ($donnee->{$champK} == NULL || $donnee->{$champK} == 0)) {
                        $searchChamp = array('[champs_related]');
                        $replaceChamp = array($donnee->{$champV['defaultValue']['champs_related']});
                        $conditionWhere = str_replace($searchChamp, $replaceChamp, $champV['defaultValue']['requete_condition']);
                        $where = " WHERE " . $conditionWhere;
                        $result = $this->Nom_Tab->findItem("SELECT collaborateurId, " . $champK . " FROM " . $tabXlsFields[$_GET['XLS']]['bdd_table'] . $where);
                        if (!empty($result)) {
                            $donnee->{'exist_' . $champK} = $result[0]->{$champK};
                            $donnee->{$champK} = $result[0]->{$champK};
                        }
                    }
                }
            }
        } else {
            $donnees = null;
        }


        if (isset($tabXlsFields[$_GET['XLS']]['other_champs']) && $tabXlsFields[$_GET['XLS']]['other_champs'] != NULL) {
            $other_champ = array_merge($tabXlsFields[$_GET['XLS']]['other_champs'], $champs);

            $disable_on_conditions = array();
            foreach ($other_champ AS $key => $val) {
                if ($donnees != NULL && $val['liste'] == 1) {
                    foreach ($donnees AS $donnee) {
                        if (isset($val['type_input']) && $val['type_input'] == "number" && isset($val['calcul']) && !empty($val['calcul'])) {
                            if (isset($val['calcul']['type_calcul']) && $val['calcul']['type_calcul'] == "not_compare") {

                                if(!isset($donnee->{$key}) || (isset($donnee->{$key}) && $donnee->{$key} == NULL)) {
                                    $arrayChamps = [];
                                    if (isset($val['calcul']['condition_on_champs']) && !empty($val['calcul']['condition_on_champs'])) {
                                        $champCondition = $val['calcul']['condition_on_champs'];
                                        if (isset($val['calcul']['calcul_on_condition']) && !empty($val['calcul']['calcul_on_condition'])
                                            && isset($val['calcul']['calcul_on_condition'][$donnee->{$champCondition}])
                                            && !empty($val['calcul']['calcul_on_condition'][$donnee->{$champCondition}])
                                        ) {
                                            if (is_array($val['calcul']['calcul_on_condition'][$donnee->{$champCondition}])) {
                                                for ($i = 0; $i < count($val['calcul']['calcul_on_condition'][$donnee->{$champCondition}]); $i++) {
                                                    $valueChamps = isset($donnee->{$val['calcul']['calcul_on_condition'][$donnee->{$champCondition}][$i]}) ? $donnee->{$val['calcul']['calcul_on_condition'][$donnee->{$champCondition}][$i]} : '0';
                                                    array_push($arrayChamps, $valueChamps);
                                                }
                                            }
                                        } else {
                                            $noCalculProfil = false;
                                            if(isset($val['calcul']['no_calcul_profil']) && !empty($val['calcul']['no_calcul_profil'])){
                                                $searchNoCacul = array('[condition_on_champs]');
                                                $replaceNoCalcul = $donnee->{$champCondition};
                                                $noCalculProfil = str_replace($searchNoCacul, $replaceNoCalcul, $val['calcul']['no_calcul_profil']);
                                                eval('$noCalculProfil = ' . $noCalculProfil . ';');
                                                if($noCalculProfil) {
                                                    $donnee->{$key} = -1;
                                                }
                                            }
                                            if ($noCalculProfil == false && is_array($val['calcul']['champs_calcul'])) {
                                                for ($i = 0; $i < count($val['calcul']['champs_calcul']); $i++) {
                                                    $valueChamps = isset($donnee->{$val['calcul']['champs_calcul'][$i]}) ? $donnee->{$val['calcul']['champs_calcul'][$i]} : '0';
                                                    array_push($arrayChamps, $valueChamps);
                                                }
                                            }
                                        }
                                    }
                                    else {
                                        if (is_array($val['calcul']['champs_calcul'])) {
                                            for ($i = 0; $i < count($val['calcul']['champs_calcul']); $i++) {
                                                $valueChamps = isset($donnee->{$val['calcul']['champs_calcul'][$i]}) ? $donnee->{$val['calcul']['champs_calcul'][$i]} : '0';
                                                array_push($arrayChamps, $valueChamps);
                                            }
                                        }
                                    }
                                    if(!empty($arrayChamps)){
                                        $searchForReplace = array('[array]');
                                        $replaceForStrReplace = array(implode(',', $arrayChamps));
                                        $donnee->{$key} = str_replace($searchForReplace, $replaceForStrReplace, $val['calcul']['calcul_func']);

                                        if(isset($val['calcul']['value_on_prioirity']) && !empty($val['calcul']['value_on_prioirity'])){
                                            $firstPrio = $val['calcul']['value_on_prioirity'][1];
                                            $secondPrio = $val['calcul']['value_on_prioirity'][2];
                                            $thirdPrio = $val['calcul']['value_on_prioirity'][3];
                                            if(isset($donnee->{$firstPrio}) && ($donnee->{$firstPrio} != NULL || $donnee->{$firstPrio} !='0')) {
                                                $donnee->{$key} = $donnee->{$firstPrio};
                                            } else if(isset($donnee->{$secondPrio}) && ($donnee->{$secondPrio} != NULL || $donnee->{$secondPrio} !='0')) {
                                                $donnee->{$key} = $donnee->{$secondPrio};
                                            } else if(isset($donnee->{$thirdPrio}) && ($donnee->{$thirdPrio} != NULL || $donnee->{$thirdPrio} !='0')) {
                                                $donnee->{$key} = $donnee->{$thirdPrio};
                                            }
                                        }
                                        else {
                                            $donnee->{'calcul_'.$key} = str_replace($searchForReplace, $replaceForStrReplace, $val['calcul']['calcul_func']);
                                            eval('$donnee->{"calcul_".$key} = ' . $donnee->{'calcul_'.$key} . ';');
                                        }
                                        eval('$donnee->{$key} = ' . $donnee->{$key} . ';');
                                    }



                                }

                            }
                            else if(isset($val['calcul']['type_calcul']) && $val['calcul']['type_calcul'] == "compare") {
                                $searchForReplace = array('[champs1]', '[champs2]');
                                $champs1 = isset($donnee->{$val['calcul']['champs_calcul']['champs1']}) && $donnee->{$val['calcul']['champs_calcul']['champs1']} !== null ? (float)$donnee->{$val['calcul']['champs_calcul']['champs1']} : 0;
                                $champs2 = isset($donnee->{$val['calcul']['champs_calcul']['champs2']}) && $donnee->{$val['calcul']['champs_calcul']['champs2']} !== null ? (float)$donnee->{$val['calcul']['champs_calcul']['champs2']} : 0;
                                $replaceForStrReplace = array($champs1, $champs2);
                                $functionReplace = str_replace($searchForReplace, $replaceForStrReplace, $val['calcul']['calcul_func']);
                                eval('$functionReplace = ' . $functionReplace . ';');
                                if (isset($val['calcul']['returnValues']) && !empty($val['calcul']['returnValues'])) {
                                    foreach ($val['calcul']['returnValues'] AS $valK => $valValue) {
                                        $valueCalcul = str_replace('[val]', $functionReplace, $valK);
                                        eval('$valueCalcul = ' . $valueCalcul . ';');
                                        if ($valueCalcul == true) {
                                            $donnee->{$key} = $valValue;
                                        }
                                    }
                                }

                            }
                        }
                        if(isset($val['profilChamps'][$_SESSION['Droit']]['modification']) && $val['profilChamps'][$_SESSION['Droit']]['modification'] == 1){
                            if(isset($val['disable_on_conditions']) && !empty($val['disable_on_conditions'])){
                                foreach($val['disable_on_conditions'] as $conditionKey => $conditionVal){
                                    $enableIf = 0;
                                    if(isset($conditionVal['profilEnable']) && !empty($conditionVal['profilEnable'])){
                                        $enableIf= isset($_SESSION['Droit']) && in_array($_SESSION['Droit'], $conditionVal['profilEnable']) ? 1 : 0;
                                    }

                                    if (isset($conditionVal['condition']) && !empty($conditionVal['condition']) && isset($conditionVal['champs_condition']) && !empty($conditionVal['champs_condition'])) {
                                        if($enableIf != 1) {

                                            $searchForReplace = array();
                                            $replaceForStrReplace = array();

                                            foreach ($conditionVal['champs_condition'] AS $champsConditionK => $champsConditionV) {
                                                if($champsConditionK != "sql_condition"){
                                                    array_push($searchForReplace, '[' . $champsConditionK . ']');
                                                    if(is_array($champsConditionV) && !empty($champsConditionV) && array_key_exists($_SESSION['Droit'], $champsConditionV) == true){
                                                        $replace = $champsConditionV[$_SESSION['Droit']];
                                                    } else {
                                                        $replace = isset($donnee->{$champsConditionV}) ? $donnee->{$champsConditionV} : $champsConditionV;
                                                    }
                                                    array_push($replaceForStrReplace, $replace);
                                                } else {
                                                    if(isset($conditionVal['type_condition']) && $conditionVal['type_condition'] == "sql_condition" && isset($conditionVal['condition_on_request']) && !empty($conditionVal['condition_on_request'])){
                                                        if(!empty($conditionVal['condition_on_request']['select_condition']) && isset($conditionVal['condition_on_request']['select_condition_on'])){
                                                            $select_condition_on = $conditionVal['condition_on_request']['select_condition_on'];
                                                            if(isset($conditionVal['condition_on_request']['select_condition'][$donnee->{$select_condition_on}])){
                                                                array_push($searchForReplace, '[' . $champsConditionK . ']');
                                                                $requestCondition = "SELECT ".$conditionVal['condition_on_request']['select_condition'][$donnee->{$select_condition_on}]." FROM ".$conditionVal['condition_on_request']['table_condition'];
                                                                if(isset($conditionVal['condition_on_request']['where_condition']) && !empty($conditionVal['condition_on_request']['where_condition'])){
                                                                    $requestCondition .= " WHERE ";
                                                                    $searchCondReplace = array();
                                                                    $replaceCondReplace = array();
                                                                    foreach($conditionVal['condition_on_request']['condition_params'] AS $condParamKey => $condParamVal){
                                                                        array_push($searchCondReplace, '[' . $condParamKey . ']');
                                                                        $replace = isset($donnee->{$condParamVal}) ? $donnee->{$condParamVal} : $condParamVal;
                                                                        array_push($replaceCondReplace, $replace);
                                                                    }
                                                                    $requestCondition .= str_replace($searchCondReplace, $replaceCondReplace, $conditionVal['condition_on_request']['where_condition']);
                                                                }

                                                                $conditionRequest = $this->Nom_Tab->findItem($requestCondition);

                                                                array_push($replaceForStrReplace, $conditionRequest[0]->result != NULL ? $conditionRequest[0]->result : 0 );
                                                            }
                                                        }

                                                    }
                                                }
                                            }
                                            if(!empty($searchForReplace) && !empty($replaceForStrReplace)){
                                                $condition = str_replace($searchForReplace, $replaceForStrReplace, $conditionVal['condition']);
                                                eval('$condition = ' . $condition . ';');
                                                if(!isset($disable_on_conditions[$donnee->id.'_'.$key])){
                                                    $disable_on_conditions[$donnee->id.'_'.$key] = array();
                                                    array_push($disable_on_conditions[$donnee->id.'_'.$key], $condition);
                                                } else {
                                                    array_push($disable_on_conditions[$donnee->id.'_'.$key], $condition);
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

        } else {
            $other_champ = NULL;
        }


        /* Récupération du résultat renvoyé par le moteur dans l'objet $donnees. -------------------------------------*/
        /*------------------------------------------------------------------------------------------------------------*/


        // Si l'Export CSV est réclamé.
        if (isset($_GET['exportCsv']) && !empty($_GET['exportCsv']) && intval($_GET['exportCsv']) == 1) {
            header("Content-Type: text/csv;");
            header("Content-disposition: filename=" . $_GET['XLS'] . "_" . date("Y-m-d_H\hi") . ".csv");
            $champsCsv = $champs;
            if (isset($tabXlsFields[$_GET['XLS']]["csv_memory_limit"]) && intval($tabXlsFields[$_GET['XLS']]["csv_memory_limit"])) {
                ini_set('memory_limit', intval($tabXlsFields[$_GET['XLS']]["csv_memory_limit"]) . 'M');
            }
            // Récup des données qui ont dans la config CSV = 1.

            $donneesCsv = $this->Nom_Tab->getAllICSVItems($profilSql, $table, $champs, $filtre, $label_id, $get_filtre, $champs_array_val); // Récuperation de tous les item (Fonction dans le dossier *racine*/App/Table fichier Nom_TabTable.php).

            // Création du tableau contenant les colonnes qui ont dans la config csv = 1.
            $col = array();
            if (isset($other_champ) && !empty($other_champ)) {
                $champsCsv = array_merge($champs, $other_champ);

                foreach ($other_champ AS $key => $val) {
                    if (isset($val['after_champs'])) {
                        $arrKeyChamps = array_keys($champsCsv);
                        $position = array_search($val['after_champs'], $arrKeyChamps);

                        $old_position = array_search($key, $arrKeyChamps);

                        array_splice($arrKeyChamps, $position + 1, 0, $key);
                        $p1 = array_splice($champsCsv, $old_position, 1);
                        $p2 = array_splice($champsCsv, 0, $position + 1);
                        $champsCsv = array_merge($p2, $p1, $champsCsv);
                    }


                    if ($donneesCsv != NULL) {
                        foreach ($donneesCsv AS $donnee) {
                            if (isset($val['type_input']) && $val['type_input'] == "number" && isset($val['calcul']) && !empty($val['calcul'])) {
                                if (isset($val['calcul']['type_calcul']) && $val['calcul']['type_calcul'] == "not_compare") {
                                    $arrayChamps = [];
                                    if(isset($val['calcul']['calcul_on_condition']) && !empty($val['calcul']['calcul_on_condition'])
                                        && in_array($donnee->posteId , $val['calcul']['calcul_on_condition']['profil'])){
                                        if (is_array($val['calcul']['calcul_on_condition'][$donnee->posteId])) {
                                            for ($i = 0; $i < count($val['calcul']['calcul_on_condition'][$donnee->posteId]); $i++) {
                                                $valueChamps = isset($donnee->{$val['calcul']['calcul_on_condition'][$donnee->posteId][$i]}) ? $donnee->{$val['calcul']['calcul_on_condition'][$donnee->posteId][$i]} : '0';
                                                array_push($arrayChamps, $valueChamps);
                                            }
                                        }
                                    }else{
                                        if (is_array($val['calcul']['champs_calcul'])) {

                                            for ($i = 0; $i < count($val['calcul']['champs_calcul']); $i++) {
                                                $valueChamps = isset($donnee->{$val['calcul']['champs_calcul'][$i]}) ? $donnee->{$val['calcul']['champs_calcul'][$i]} : '0';
                                                array_push($arrayChamps, $valueChamps);
                                            }
                                        }
                                    }
                                    $searchForReplace = array('[array]');
                                    $replaceForStrReplace = array(implode(',', $arrayChamps));
                                    $donnee->{$key} = str_replace($searchForReplace, $replaceForStrReplace, $val['calcul']['calcul_func']);
                                    eval('$donnee->{$key} = ' . $donnee->{$key} . ';');
                                }
                                else if(isset($val['calcul']['type_calcul']) && $val['calcul']['type_calcul'] == "compare") {
                                    $searchForReplace = array('[champs1]', '[champs2]');
                                    $champs1 = isset($donnee->{$val['calcul']['champs_calcul']['champs1']}) && $donnee->{$val['calcul']['champs_calcul']['champs1']} !== null ? $donnee->{$val['calcul']['champs_calcul']['champs1']} : 0;
                                    $champs2 = isset($donnee->{$val['calcul']['champs_calcul']['champs2']}) && $donnee->{$val['calcul']['champs_calcul']['champs2']} !== null ? $donnee->{$val['calcul']['champs_calcul']['champs2']} : 0;
                                    $replaceForStrReplace = array($champs1, $champs2);
                                    $functionReplace = str_replace($searchForReplace, $replaceForStrReplace, $val['calcul']['calcul_func']);
                                    eval('$functionReplace = ' . $functionReplace . ';');
                                    if (isset($val['calcul']['returnValuesCSV']) && !empty($val['calcul']['returnValuesCSV'])) {
                                        foreach ($val['calcul']['returnValuesCSV'] AS $valK => $valValue) {
                                            $valueCalcul = str_replace('[val]', $functionReplace, $valK);
                                            eval('$valueCalcul = ' . $valueCalcul . ';');
                                            if ($valueCalcul == true) {
                                                $donnee->{$key} = $valValue;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            foreach ($champsCsv as $key => $v) {
                if (isset($v['csv']) && $v['csv'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1 && !isset($v['bdd_value'])) {
                    //$v['nom']=utf8_decode($v['nom']);
                    array_push($col, $v['nom']);
                } elseif (isset($v['csv']) && $v['csv'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1 && isset($v['bdd_value'])) {
                    //$v['nom']=utf8_decode($v['nom']);
                    array_push($col, $v['nom']);
                }
            }


            $nbCol = count($col); // Récuperation du nombre de colonne à afficher.

            $separateur = ";";
            // Affichage de la ligne terminée par un retour chariot
            echo utf8_decode(implode($separateur, $col)) . "\r\n";

            // Création du contenu du tableau
            $liCsv = array();

            // Parcours de l'array contenant l'objet
            foreach ($donneesCsv as $ligne => $value) {
                // Transforme un objet en array.
                $value = (array)$value;
                // Parcours de la conf.
                foreach ($champsCsv as $key => $v):
                    // Condition si la clé du tableau conf est identique à la clé du tableau de données.
                    if (isset($v['type']) && $v['type'] == "list_sql" && isset($v['csv']) && $v['csv'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                        $val = $value[$v['bdd_table_t']];

                        if (isset($val)) {
                            $val = str_replace(array('"', "\t", "\n", "\r", ";"), array("", " ", " ", " ", ","), $val);
                        }
                        $val = utf8_decode($val);
                        array_push($liCsv, $val);
                    } elseif (isset($v['csv']) && $v['csv'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                        $val = $value[$key];

                        if (isset($val)) {
                            $val = str_replace(array('"', "\t", "\n", "\r", ";"), array("", " ", " ", " ", ","), $val);
                        }
                        $val = utf8_decode($val);
                        array_push($liCsv, $val);
                    }
                endforeach;
            }
            // Découpe du tableau en fonction du nombre de colonne.
            $liCsv = array_chunk($liCsv, $nbCol);

            // Affichage de chaque sous tableaux issue du découpage.
            foreach ($liCsv as $liCVal) {
                echo utf8_decode(implode($separateur, $liCVal)) . "\r\n";
            }

            exit();
        } else {
            if (!isset($errors) || empty($errors)) {
                $errors = null;
            }
            if (!isset($champ) || empty($champ)) {
                $champ = null;
            }
            if (!isset($order) || empty($order)) {
                $order = null;
            }
            if (!isset($option) || empty($option)) {
                $option = null;
            }
            if (!isset($getGroupe) || empty($getGroupe)) {
                $getGroupe = null;
            }
            if (!isset($optionGroupe) || empty($optionGroupe)) {
                $optionGroupe = null;
            }
            if (!isset($reqOptionsP) || empty($reqOptionsP)) {
                $reqOptionsP = null;
            }
            if (!isset($option) || empty($options)) {
                $options = null;
            }

            if (!isset($req) || empty($req)) {
                $req = null;
            }

            $Version = self::VERSION;

            $fin = microtime(true);
            $delai = $fin - $debut;
            $delai = substr($delai, 0, 4);
            $debugMode = $_SESSION['db_DebugMode'];
            $LocalTime = $_SESSION['db_LocalTime'];
            $form = new BootstrapForm($_POST);


            $this->render('Principal.listItem', compact('donnees', 'Version', 'active_view_array',
                'champ_for_edit', 'get_filtre', 'filtre_array_key', 'filtre_array_val', 'LocalTime',
                'reqOptionsP', 'custom_list_edit', 'order', 'groupeActive', 'optionGroupe', 'debugMode',
                'errors', 'options', 'champ', 'tabXlsFields', 'pagination', 'champs', 'form', 'page', 'nbPage',
                'pageCurrent', 'nbTotalPage', 'delai', 'req', 'reqCount', 'filtre', 'search', 'ParPage', 'nom_feuille',
                'other_champ', 'updateFieldsOnchange', 'disable_on_conditions'
            ));


            exit();
        }


    }

    public function addItem()
    {

        $debut = microtime(true);


        if (isset($_GET['XLS'])) {
            $_SESSION[$_GET['XLS']]['XLS'] = $_GET['XLS'];
        }

        // Récup du fichier conf (dossier *racine*/conf/FieldsUsers.php).
        $tabXlsFields = listParamTab();
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


        $erreur = array();
        // Verifie si les clés obligatoire sont bien présentes.
        if (!isset($tabXlsFields[$_GET['XLS']]['profilSql']) || empty($tabXlsFields[$_GET['XLS']]['profilSql'])) {
            $erreur['profilSql'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['bdd_table']) || empty($tabXlsFields[$_GET['XLS']]['bdd_table'])) {
            $erreur['bdd_table'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['champs']) || empty($tabXlsFields[$_GET['XLS']]['champs'])) {
            $erreur['champs'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['nom_feuille']) || empty($tabXlsFields[$_GET['XLS']]['nom_feuille'])) {
            $erreur['nom_feuille'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['pagination']) || empty($tabXlsFields[$_GET['XLS']]['pagination'])) {
            $erreur['pagination'] = "manquant ou vide";
        }
        if (!isset($tabXlsFields[$_GET['XLS']]['bdd_table_file']) || empty($tabXlsFields[$_GET['XLS']]['bdd_table_file'])) {
            $erreur['bdd_table_file'] = "manquant ou vide";
        }
        if (isset($tabXlsFields[$_GET['XLS']]['profilSql'])) {
            if (!isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']])) {
                $erreur['Le profil utilisateurs'] = "manquant ou n'est pas autorisé à voir la page";
            }
        }

        if ($erreur != null) {
            $erreur = serialize($erreur);
            header('Location: index.php?p=Principal.notFound&erreurConf=' . $erreur . '');
            exit();
        } else {
            // Si les clés sont présent.
            $profilSql = $tabXlsFields[$_GET['XLS']]['profilSql'];
            $table = $tabXlsFields[$_GET['XLS']]['bdd_table'];
            $champs = $tabXlsFields[$_GET['XLS']]['champs'];
            $nom_feuille = $tabXlsFields[$_GET['XLS']]['nom_feuille'];

            if (isset($tabXlsFields[$_GET['XLS']]['champs_groupe'])) {
                $champ_for_edit = $tabXlsFields[$_GET['XLS']]['champs_groupe'];
            } else {
                $champ_for_edit = 0;
            }

            $TabListMIME = listParamTypeMIME();
            $ListeTypeMIME = $TabListMIME['TypeMIME']['listeTypeMIME'];
            $tableFile = $tabXlsFields[$_GET['XLS']]['bdd_table_file'];
            $label_id = $tabXlsFields[$_GET['XLS']]['bdd_id'];


            if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['custom_list_edit'])) {
                $custom_list_edit = $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['custom_list_edit'];
            } else {
                $custom_list_edit = null;
            }

            if (isset($tabXlsFields[$_GET['XLS']]['groupe'])) {
                $groupeActive = $tabXlsFields[$_GET['XLS']]['groupe'];
            } else {
                $groupeActive = null;
            }
            if (isset($tabXlsFields[$_GET['XLS']]['table_groupe'])) {
                $table_groupe = $tabXlsFields[$_GET['XLS']]['table_groupe'];
            } else {
                $table_groupe = null;
            }
            if (isset($tabXlsFields[$_GET['XLS']]['champs_groupe'])) {
                $champs_groupe = $tabXlsFields[$_GET['XLS']]['champs_groupe'];
            } else {
                $champs_groupe = null;
            }

            // Vérification si les clés des champs obligatoire sont présent.
            foreach ($champs as $souschamps => $sc) {


                if (isset($sc['type_input']) && $sc['type_input'] != 'categorie' && $sc['type_input'] != 'sous_categorie' && $sc['type_input'] != 'tendance') {

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

            $arrayError = array();
            $edit = 0; // Cette variable permet à la fonction de vérification de différencier si c'est une mise à jour ou une nouvel insertion.
            $id = 0; // Variable qui vaut null dans le cas d'un insertion(auto-incrémentation).

            // Parcours du fichier conf et contrôle des champs.
            foreach ($champs as $c => $cc) {

                if (isset($cc['profilChamps'][$_SESSION['Droit']]['ecriture']) && $cc['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1) {

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
                        case "textarea_jointure":
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
                                $_POST[$c] = null;
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
                                                    //$valueParamFile = array(0 => $_SESSION['UserID'], 1 => $_POST[$c . '_Name'][$post_f] . $name, 2 => $file, 3 => $_GET['XLS'], 4 => $c, 5 => $TypeMimeFile);
                                                    $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $_POST[$c . '_Name'][$post_f] . "." . $_POST[$c . '_ext'][$post_f], 2 => $file, 3 => $_GET['XLS'], 4 => $c, 5 => $TypeMimeFile);

                                                    //Insertion du fichier dans la base de données temporaire.
                                                    $result = $this->Nom_Tab->insertItem('file_tmp', $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                                    //$_POST[$c][$post_f] = $_POST[$c . '_Name'][$post_f] . $name;
                                                    $_POST[$c][$post_f] = $_POST[$c . '_Name'][$post_f] . "." . $_POST[$c . '_ext'][$post_f];

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

                    if (isset($cc['profilChamps'][$_SESSION['Droit']]['ecriture']) && $cc['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1 && $cc['type_input'] != 'file' && !isset($cc['date_now'])) {
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
                $result = $this->Nom_Tab->insertItem($table, $colInsert, $valueInsert, $valueParam); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                /* Fin de l'insertion de l'item ----------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/
                // Si l'insertion est OK.
                if ($result) {
                    $lastIdItem = $this->Nom_Tab->lastID($table);// La fonction lastID est dans le dossier *racine*/app/Table fichier Nom_Table.php.

                    $colInsertFile = array(0 => "id_user", 1 => "id_item", 2 => "nom", 3 => "path", 4 => "conf", 5 => "champ", 6 => 'datequote');
                    $valueInsertFile = array(0 => ":id_user", 1 => ":id_item", 2 => ":nom", 3 => ":path", 4 => ":conf", 5 => ":champ", 6 => 'NOW()');
                    /*----------------------------------------------------------------------------------------*/
                    /* Concerne l'upload de fichier -------------- -------------------------------------------*/
                    /*----------------------------------------------------------------------------------------*/

                    // Parcours de la conf.
                    foreach ($champs as $c => $cc) {
                        if (isset($cc['profilChamps'][$_SESSION['Droit']]['ecriture']) && $cc['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1 && $cc['type_input'] == 'file' && isset($cc['cheminDossier'])) {

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
                                    $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $lastIdItem[0]->id, 2 => $name, 3 => $file, 4 => $_GET['XLS'], 5 => $c);
                                    // Insertion du fichier dans la table définitif.
                                    $result = $this->Nom_Tab->insertItem($tableFile, $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                }
                            }
                        }
                    }
                    /* Fin d'upload de fichier ---------------------------------------------------------------*/
                    /*----------------------------------------------------------------------------------------*/
                    $_SESSION['succes'] = 1;
                    header('Location: index.php?p=Principal.listItem&XLS=' . $_GET['XLS'] . '&collapsId=' . $_GET['collapsId']);
                    exit();
                }
            }
        }


        $options = array();
        $reqOptionsP = array();
        // Parcours du fichier conf (dossier *racine*/conf/FieldsUsers.php).
        foreach ($champs as $auto_c => $a_c) {

            if (isset($a_c['profilChamps'][$_SESSION['Droit']]['ecriture']) && $a_c['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1) {

                if (isset($a_c['type']) && $a_c['type'] == "list_sql" && isset($a_c['autocomplete']) && $a_c['autocomplete'] == 0) {
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
            if (isset($champs[$_GET['key']])) {
                $optionC = json_encode($this->Nom_Tab->autoCompletion($champs[$_GET['key']], $_GET['champs'], $_GET['conf']));
                echo $optionC;
                exit();
            }
        }


        if (!isset($errors) || empty($errors)) {
            $errors = null;
        }
        $Version = self::VERSION;
        $Version = self::VERSION;
        $fin = microtime(true);
        $delai = $fin - $debut;
        $delai = substr($delai, 0, 4);
        $debugMode = $_SESSION['db_DebugMode'];
        $LocalTime = $_SESSION['db_LocalTime'];
        $form = new BootstrapForm($_POST);

        $this->render('Principal.addItem', compact('Version', 'champs', 'LocalTime', 'reqOptionsP', 'debugMode', 'errors', 'options', 'tabXlsFields', 'form', 'delai', 'nom_feuille'));
        exit();


    }

    public function editItem()
    {

        $debut = microtime(true);
        // Récup du fichier conf (dossier *racine*/conf/FieldsUsers.php).
        $tabXlsFields = listParamTab();
        $search_array = $tabXlsFields;

        if (isset($_GET['historique']) && $_GET['historique'] == '1') {
            if (!isset($_GET['XLS'])) {
                foreach ($search_array as $key => $val) {
                    if ($val['nom_feuille'] === $_GET['nom_feuille']) {
                        $_GET['XLS'] = $key;
                    }
                }
            }
        }

        // Verifie si la conf correspondant à la page demmandé existe.
        if (!isset($_GET['XLS']) || empty($_GET['XLS']) || !array_key_exists($_GET['XLS'], $search_array)) {
            header('Location: index.php?p=Principal.notFound');
            exit();
        }
        if (!isset($_GET['historique'])) {
            // Verifie si la conf correspondant à la page demmandé existe.
            if (!isset($_GET['id']) || empty($_GET['id']) && !is_int($_GET['id'])) {
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

            // Verification droit de lecture DE l'item :
            $itemOkToEdit = 1;
            if (isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']])) {
                if (isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["req_sql"])) {
                    if ($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["req_sql"] == 1) {
                        if (isset($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["requete"])) {
                            if (trim($tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["requete"]) != "") {

                                $itemOkToEdit = 0;
                                // Requete : Verification que l'item a modifier fait bien parti de la liste visible par le user:
                                $query = "SELECT COUNT(*) AS total " .
                                    "FROM " . $tabXlsFields[$_GET['XLS']]['bdd_table'] . " " .
                                    "WHERE " . $tabXlsFields[$_GET['XLS']]['bdd_id'] . "=" . intval($_GET['id']);
                                $query .= " " . $tabXlsFields[$_GET['XLS']]['profilSql'][$_SESSION['Droit']]["requete"];


                                // Verif si nb item = 1 avec droits de lecture du user
                                $result = $this->Nom_Tab->findItem($query);
                                if ($result) {
                                    if (isset($result[0])) {
                                        if (isset($result[0]->total)) {
                                            if ($result[0]->total == 1) {
                                                unset($result);
                                                $itemOkToEdit = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ($itemOkToEdit == 0) {
                header('Location: index.php?p=Principal.notFound');
                exit();
            }
        }


        $id = $tabXlsFields[$_GET['XLS']]['bdd_id'];
        $table = $tabXlsFields[$_GET['XLS']]['bdd_table'];
        $champs = $tabXlsFields[$_GET['XLS']]['champs'];
        $other_champs = array();
        if(isset($tabXlsFields[$_GET['XLS']]['other_champs'])) {
            $other_champs = $tabXlsFields[$_GET['XLS']]['other_champs'];
        }
        $nom_feuille = $tabXlsFields[$_GET['XLS']]['nom_feuille'];

        // Récup du fichier conf Historique (dossier *racine*/conf/FieldsHistorique.php).
        $tabXlsFieldsHistorique = listParamHistorique();
        $idHistorique = $tabXlsFieldsHistorique['Historique']['bdd_id'];
        $tableHistorique = $tabXlsFieldsHistorique['Historique']['bdd_table'];
        $champsHistorique = $tabXlsFieldsHistorique['Historique']['champs'];
        $nom_feuilleHistorique = $tabXlsFieldsHistorique['Historique']['nom_feuille'];

        $TabListMIME = listParamTypeMIME();
        $ListeTypeMIME = $TabListMIME['TypeMIME']['listeTypeMIME'];
        $tableFile = $tabXlsFields[$_GET['XLS']]['bdd_table_file'];

        // Récupération des données de l'historique en ajax.
        if (isset($_GET['historique']) && $_GET['historique'] == 1 && isset($_GET['id_item'])) {

            $historique = $this->Nom_Tab->getHistorique($_GET['id_item'], $_GET['nom_feuille']);// La fonction getHistorique est dans le dossier *racine*/core/Table fichier Table.php.

            if ($historique) {
                foreach ($historique as $ht => $t) {
                    setlocale(LC_TIME, "fr_FR");
                    $date = strftime("%d/%m/%Y à %Hh%M", strtotime($t->date_modification));
                    echo '<div class="card mb-2">';
                    echo '<h6 class="card-header"><b>' . htmlspecialchars($t->nom) . ' ' . htmlspecialchars($t->prenom) . '</b> <span class="far fa-clock float-right"> ' . htmlspecialchars($date) . '</span></h6>';
                    echo '<div class="card-body">';
                    $valueModif = json_decode($t->value_modif);

                    foreach ($valueModif as $vm) {
                        foreach ($vm as $vvm => $vv) {
                            echo ' <p class="card-text"> ' . htmlspecialchars($vvm) . ' <b>' . htmlspecialchars($vv) . '</b></p>';
                        }
                    }
                    echo '</div>';
                    echo '</div>';


                }
            } else {
                echo 'Aucune modification n\'a été enregistrée.';
            }
            exit();
        }

        // Si le formulaire de la page a été soumis.
        if (!empty($_POST)) {

            /*-------------------------------------------------------------------------------------------------------*/
            /* Vérification des champs issue du formulaire ----------------------------------------------------------*/
            /*-------------------------------------------------------------------------------------------------------*/
            $errors = new ControleObjetConfig(); // L'objet controleForm est dans le dossier *racine*/App/config.
            $errors->existSession($_SESSION['UserID']); // Verifie avant d'éxécuter une méthode si User est connecté.(sa évite qu'un formulaire non issue de l'application soit soumis à la place)

            $edit = 1; // Cette variable permet à la fonction de vérification du mail de différencier si c'est une mise à jour ou une nouvel insertion.

            // Parcours du fichier conf et contrôle des champs.
            foreach ($champs as $c => $cc) {

                if (isset($cc['profilChamps'][$_SESSION['Droit']]['modification']) && $cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1) {

                    if (isset($cc['type_input']) && $cc['type_input'] == 'bloc_attribute') {
                        foreach ($cc as $ccKey => $ccVal) {
                            if (isset($_POST[$ccKey . '_extra_attribute'])) {

                                switch ($ccVal['type_input']) :
                                    case "text":
                                        foreach ($_POST[$ccKey . '_extra_attribute'] as $ccKeyPostKey => $ccKeyPostVal) {
                                            $errors->validVarcharExtraAtrib($ccKeyPostVal, $ccKey . '_extra_attribute', $ccVal['obligatoire'], $ccVal['controle_balise'], $ccVal['taille_min'], $ccVal['taille_max'], $ccVal['unique'], $table, $edit, $_GET['id']);
                                        }
                                        break;
                                endswitch;
                            }
                        }
                    }


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
                            $errors->validVarchar($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "textarea":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validVarchar($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "textarea_jointure":
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
                                $errors->validDateTime($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);

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
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validPhone($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "password":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validPassword($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "email":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validMail($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "url":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validUrl($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "radio":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
                            // Contrôle des champs dans l'objet ControleObjetConfig dans le dossier *racine*/app/config/ControleObjetConfig.php.
                            $errors->validRadio($_POST[$c], $c, $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $table, $edit, $_GET['id']);
                            break;
                        case "checkbox":
                            if (!isset($_POST[$c]) || empty($_POST[$c])) {
                                $_POST[$c] = null;
                            }
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
                                                    $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $_POST[$c . '_Name'][$post_f], 2 => $file, 3 => $_GET['XLS'], 4 => $c, 5 => $TypeMimeFile);

                                                    //Insertion du fichier dans la base de données temporaire.
                                                    $result = $this->Nom_Tab->insertItem('file_tmp', $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.
                                                    $_POST[$c][$post_f] = $_POST[$c . '_Name'][$post_f];
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
                            if (isset($_POST[$p . "_old_val"])) {
                                if ($pp == $donnees[0]->{$champs[$p]['bdd_table_t']}) {
                                    $dd = "noChange";
                                } else {
                                    $dd = $_POST[$p . "_old_val"];
                                }
                            }

                            foreach ($champs as $key => $val) {
                                if ($key == $d) {
                                    if ($dd != "noChange") {
                                        if ($dd !== '' && $dd != null) {
                                            $lastValue = $val["nom"] . ' : ' . $dd . ' par ';
                                        } else {
                                            $lastValue = $val["nom"] . ' : ';
                                        }
                                        $value = $pp;
                                        // Attribution des valeurs dans un array.
                                        array_push($jsonLastValue, $lastValue); // array contenant les anciennes valeurs.
                                        array_push($jsonValue, $value); // array contenant les nouvelles valeurs.
                                    }

                                }

                            }
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
                    if (isset($cc['liste_detail']) && $cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1) {
                        // Condition qui permet d'isoler le champs mot de passe pour lui attribuer un cryptage.
                        if ($c == 'password') {
                            $_POST[$c] = password_hash($_POST[$c], PASSWORD_BCRYPT);
                            $colIn = $c;
                            $colvalue = ":" . $c;
                            $value = $_POST[$c];
                        } else {
                            $colIn = $c;
                            $colvalue = ":" . $c;
                            if (isset($cc['onInsert_champs']) && !empty($cc['onInsert_champs'])) {
                                $deleteWhere = "";
                                $bdd_champs_insert = "";
                                $val_bdd_insert_champs = "";
                                foreach ($cc['onInsert_champs'] AS $keyChamps => $valChamps) {
                                    $valChamps = str_replace('[id_champs]', htmlspecialchars($_GET['id']), $valChamps);
                                    $deleteWhere .= $deleteWhere !== "" ? " AND " . $keyChamps . "=" . htmlspecialchars($valChamps) : $keyChamps . "=" . htmlspecialchars($valChamps);
                                    $bdd_champs_insert .= $bdd_champs_insert !== "" ? " , " . $keyChamps : $keyChamps;
                                    $val_bdd_insert_champs .= $val_bdd_insert_champs !== "" ? " , '" . htmlspecialchars($valChamps) . "'" : "'" . htmlspecialchars($valChamps) . "'";
                                }
                                // Suppression de l'ancienne valeur
                                $this->Nom_Tab->findItem("DELETE FROM " . $cc['bdd_table'] . " WHERE " . $deleteWhere);
                                // Création de la nouvelle ligne dans la table de jointure
                                $this->Nom_Tab->findItem("INSERT INTO " . $cc['bdd_table'] . "(" . $cc['bdd_value'] . ", " . $bdd_champs_insert . ") VALUES ('" . htmlspecialchars($_POST[$c]) . "', " . $val_bdd_insert_champs . ")");
                                $value = $_POST[$c . '_bdd_val'];
                            } else {
                                $value = $_POST[$c];
                            }

                        }
                        array_push($colInsert, $colIn); // array contenant les colonnes pour l'insertion.
                        array_push($valueInsert, $colvalue); // array contenant le nom des paramètres pour la requete préparé.
                        array_push($valueParam, $value); // array contenant les valeurs à attribuer au paramètres.
                    }
                    // Partie qui met à jour les données complémentaire dans une autre table(extra attribute).
                    if (isset($cc['type_input']) && isset($cc['profilChamps'][$_SESSION['Droit']]['modification']) && $cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && $cc['type_input'] == 'bloc_attribute') {
                        foreach ($cc as $ccKey => $ccVal) {
                            if (isset($_POST[$ccKey . '_extra_attribute'])) {
                                foreach ($_POST[$ccKey . '_extra_attribute'] as $testKey => $testVal) {
                                    $tabDataExtraAttKey[$testKey][] = $ccKey;
                                    $tabDataExtraAttKeyParam[$testKey][] = ':' . $ccKey;
                                    $tabDataExtraAtt[$testKey][] = $testVal;

                                }
                            }
                        }
                    }
                }
                //
                if(!empty($other_champs)){
                    foreach ($other_champs as $other_champK=>$val) {
                        if (isset($val['calcul']['type_calcul']) && $val['calcul']['type_calcul'] == "not_compare") {
                            $arrayChamps = [];
                            if (isset($val['calcul']['condition_on_champs']) && !empty($val['calcul']['condition_on_champs'])) {
                                $champCondition = $val['calcul']['condition_on_champs'];
                                if (isset($val['calcul']['calcul_on_condition']) && !empty($val['calcul']['calcul_on_condition'])
                                    && isset($val['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}])
                                    && !empty($val['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}])
                                ) {
                                    if (is_array($val['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}])) {
                                        for ($i = 0; $i < count($val['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}]); $i++) {
                                            $champName = $val['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}][$i];
                                            $valueChamps = isset($_POST[$champName]) && !empty($_POST[$champName]) ? $_POST[$champName] : '0';
                                            array_push($arrayChamps, $valueChamps);
                                        }
                                    }
                                } else {
                                    if (is_array($val['calcul']['champs_calcul'])) {
                                        for ($i = 0; $i < count($val['calcul']['champs_calcul']); $i++) {
                                            $champName = $val['calcul']['champs_calcul'][$i];
                                            $valueChamps = isset($_POST[$champName]) && !empty($_POST[$champName]) ? $_POST[$champName] : '0';
                                            array_push($arrayChamps, $valueChamps);
                                        }
                                    }
                                }
                            }
                            if(!empty($arrayChamps)){
                                $newValue = null;
                                $searchForReplace = array('[array]');
                                $replaceForStrReplace = array(implode(',', $arrayChamps));
                                $resultFunc = str_replace($searchForReplace, $replaceForStrReplace, $val['calcul']['calcul_func']);
                                eval('$resultFunc = ' . $resultFunc . ';');
                                $newValue = $resultFunc;

                                if(isset($val['calcul']['value_on_prioirity']) && !empty($val['calcul']['value_on_prioirity'])){
                                    $firstPrio = $val['calcul']['value_on_prioirity'][1];
                                    $secondPrio = $val['calcul']['value_on_prioirity'][2];
                                    $thirdPrio = $val['calcul']['value_on_prioirity'][3];
                                    if(isset($_POST[$firstPrio]) && $_POST[$firstPrio] != NULL) {
                                        $newValue = $_POST[$firstPrio];
                                    } else if(isset($donnees[0]->{$firstPrio}) && $donnees[0]->{$firstPrio} != NULL ) {
                                        $newValue = $donnees[0]->{$firstPrio};
                                    } else if(isset($_POST[$secondPrio]) && $_POST[$secondPrio] != NULL ) {
                                        $newValue = $_POST[$secondPrio];
                                    } else if(isset($donnees[0]->{$secondPrio}) && $donnees[0]->{$secondPrio} != NULL ) {
                                        $newValue = $donnees[0]->{$secondPrio};
                                    }  else if(isset($_POST[$thirdPrio]) && $_POST[$thirdPrio] != NULL) {
                                        $newValue = $_POST[$thirdPrio];
                                    } else if(isset($donnees[0]->{$thirdPrio}) && $donnees[0]->{$thirdPrio} != NULL) {
                                        $newValue = $donnees[0]->{$thirdPrio};
                                    }
                                }
                            }
                            if(isset($val['update_champs_edit']) && !empty($val['update_champs_edit']) && isset($champs[$val['update_champs_edit']])) {
                                $champs_to_update = $champs[$val['update_champs_edit']];
                                if(isset($champs_to_update['calcul']) && !empty($champs_to_update['calcul']) && isset($champs_to_update['calcul']['type_calcul']) && $champs_to_update['calcul']['type_calcul'] == 'update_value'){
                                    $searchForReplace = array('[champ1]', '[champ2]', '[champ3]');
                                    $champ1 = isset($donnees[0]->{$champs_to_update['calcul']['champs_calcul']['champ1']}) && $donnees[0]->{$champs_to_update['calcul']['champs_calcul']['champ1']} !== null ? $donnees[0]->{$champs_to_update['calcul']['champs_calcul']['champ1']} : 0;
                                    $champ2 = isset($_POST[$champs_to_update['calcul']['champs_calcul']['champ2']]) && $_POST[$champs_to_update['calcul']['champs_calcul']['champ2']] !== null ? $_POST[$champs_to_update['calcul']['champs_calcul']['champ2']] : 0;
                                    $champ3 = $newValue;
                                    $replaceForStrReplace = array($champ1, $champ2, $champ3);

                                    $_POST[$val['update_champs_edit']] = str_replace($searchForReplace, $replaceForStrReplace, $champs_to_update['calcul']['calcul_func']);
                                    eval('$_POST[$val["update_champs_edit"]] = ' . $_POST[$val["update_champs_edit"]] . ';');
                                    array_push($colInsert, $val['update_champs_edit']);
                                    array_push($valueInsert, ':'.$val['update_champs_edit']);
                                    array_push($valueParam, $_POST[$val["update_champs_edit"]]);
                                }
                            }
                        }
                    }
                }
                $result = $this->Nom_Tab->updateitem($id, $table, $colInsert, $valueInsert, $valueParam, $_GET['id']); // La fonction updateitem est dans le dossier *racine*/Core/Table fichier Table.php.

                if (isset($tabDataExtraAttKey) && isset($tabDataExtraAtt)) {
                    foreach ($tabDataExtraAttKey as $tabDataExtraAttKeyKey => $tabDataExtraAttKeyVal) {
                        $resultextra = $this->Nom_Tab->updateitem($tabXlsFields[$_GET['XLS']]['label_id_table_attribute'], $tabXlsFields[$_GET['XLS']]['table_attribute'], $tabDataExtraAttKeyVal, $tabDataExtraAttKeyParam[$tabDataExtraAttKeyKey], $tabDataExtraAtt[$tabDataExtraAttKeyKey], $tabDataExtraAtt[$tabDataExtraAttKeyKey][0]); // La fonction updateitem est dans le dossier *racine*/Core/Table fichier Table.php.
                    }
                }


                /* Fin de la mise à jour de l'item -------------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------------------*/


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
                                $resultReq = $this->Nom_Tab->findItemData($reqVal, $p_f); // pf est le nom du ficher.


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


                                $valueParamFile = array(0 => $_SESSION['UserID'], 1 => $_GET['id'], 2 => $name, 3 => $file, 4 => $_GET['XLS'], 5 => $c);
                                // Insertion du fichier dans la table définitif.

                                $result = $this->Nom_Tab->insertItem($tableFile, $colInsertFile, $valueInsertFile, $valueParamFile); // La fonction insertUserItem est dans le dossier *racine*/Core/Table fichier Table.php.


                            }
                        }
                    }

                }
                /* Fin d'upload de fichier ---------------------------------------------------------------*/
                /*----------------------------------------------------------------------------------------*/

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
                        if ($cch['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && !isset($cc['date_now'])) {

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
                    $_SESSION['succes'] = 2;
                    header('Location: index.php?p=Principal.listItem&XLS=' . $_GET['XLS'] . '&collapsId=' . $_GET['collapsId']);
                    exit();
                } else {
                    $_SESSION['fail'] = 1;
                    header('Location: index.php?p=Principal.editItem&id=' . $_GET['id'] . '&XLS=' . $_GET['XLS'] . '&collapsId=' . $_GET['collapsId']);
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
        }
        else {
            if ($_GET['groupe'] && $_GET['groupe'] != 'null') {
                $getGroupe = abs($_GET['groupe']);
            } else {
                $getGroupe = null;
            }
        }

        $ifFile = 0;
        $options = array();
        $reqOptionsP = array();
        // Parcours du fichier conf Utilisateur.
        foreach ($champs as $auto_c => $a_c) {
            if (isset($a_c['autocomplete'])) {

                if (isset($a_c['type']) && $a_c['type'] == "list_sql" && $a_c['autocomplete'] != 1) {
                    $options[$auto_c] = array();
                    // Récup les valeurs à afficher dans le select(html).
                    $reqOptions = $this->Nom_Tab->allItemsForSelectQuery($a_c);
                    $option = $this->Nom_Tab->allItemsForSelect($reqOptions);
                    $options[$auto_c] = $option;
                    array_push($reqOptionsP, $reqOptions);
                }
            }

            if (isset($cc['type_input']) && $cc['type_input'] == 'file') {
                $ifFile = 1;
            }
        }


        // Si l'autocomplétion a été déclanchée.
        if (isset($_GET['champs']) && !empty($_GET['champs']) && isset($_GET['key']) && !empty($_GET['key']) && isset($_GET['conf']) && !empty($_GET['conf']) && isset($_GET['autocomp']) && $_GET['autocomp'] == 1) {
            if (isset($champs[$_GET['key']])) {
                $optionC = json_encode($this->Nom_Tab->autoCompletion($champs[$_GET['key']], $_GET['champs'], $_GET['conf']));
                echo $optionC;
                exit();
            }
        }


        // Condition qui verifie si on a bien un id de l'item à afficher.
        if (isset($_GET['id']) || !empty($_GET['id'])) {
            $_GET['id'] = intval($_GET['id']);
        }

        if (!isset($_GET['id']) || empty($_GET['id']) && !is_int($_GET['id'])) {
            header('index.php?p=Principal.notFound');
            exit();
        }
        else {
            if (isset($_GET['id']) || !empty($_GET['id']) && is_int($_GET['id'])) {
                $debut = microtime(true);
                $req = $this->Nom_Tab->findItemQuery($id, $table, $champs, $_GET['id']);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                $donnees = $this->Nom_Tab->findItem($req);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.

                if ($donnees) {
                    $donnees = $donnees[0];
                    if (isset($tabXlsFields[$_GET['XLS']]['old_values']) && !empty($tabXlsFields[$_GET['XLS']]['old_values'])
                        && isset($tabXlsFields[$_GET['XLS']]['old_values']['champs_related']) && !empty($tabXlsFields[$_GET['XLS']]['old_values']['champs_related'])) {

                        if (isset($donnees) && !empty($donnees)) {
                            $relatedChamps = $tabXlsFields[$_GET['XLS']]['old_values']['champs_related'];
                            $requeteWhere = $tabXlsFields[$_GET['XLS']]['old_values']['requete_condition']. ' AND '.$table.'.'.$relatedChamps.'='.$donnees->{$relatedChamps};
                            $old_donnees = $this->Nom_Tab->getAllOldItem($table, $champs, $relatedChamps, $requeteWhere);
                            if(!empty($old_donnees)){
                                $donnees->old_value = $old_donnees[0];
                            } else {
                                $donnees->old_value = null;
                            }
                        }
                    }
                    $disable_on_conditions = array();
                    foreach ($champs as $c => $cc) {
                        if (isset($cc['profilChamps'][$_SESSION['Droit']]['lecture']) && $cc['profilChamps'][$_SESSION['Droit']]['lecture'] == 1 &&
                            isset($cc['type_input']) && $cc['type_input'] == "number" && isset($cc['calcul']) && !empty($cc['calcul'])) {
                            if (isset($cc['calcul']['type_calcul']) && $cc['calcul']['type_calcul'] == "not_compare" ) {
                                $arrayChamps = [];
                                $noCalculProfil = false;

                                if (isset($cc['calcul']['condition_on_champs']) && !empty($cc['calcul']['condition_on_champs'])) {
                                    $champCondition = $cc['calcul']['condition_on_champs'];
                                    if(isset($cc['calcul']['calcul_on_condition']) && !empty($cc['calcul']['calcul_on_condition'])
                                        && isset($cc['calcul']['calcul_on_condition'][$donnees->{$champCondition}])
                                        && !empty($cc['calcul']['calcul_on_condition'][$donnees->{$champCondition}] ))
                                    {
                                        if (is_array($cc['calcul']['calcul_on_condition'][$donnees->{$champCondition}])) {
                                            for ($i = 0; $i < count($cc['calcul']['calcul_on_condition'][$donnees->{$champCondition}]); $i++) {
                                                $valueChamps = isset($donnees->{$cc['calcul']['calcul_on_condition'][$donnees->{$champCondition}][$i]}) ?
                                                    $donnees->{$cc['calcul']['calcul_on_condition'][$donnees->{$champCondition}][$i]} : '0';
                                                array_push($arrayChamps, $valueChamps);
                                            }
                                        }

                                    } else {
                                        if(isset($cc['calcul']['no_calcul_profil']) && !empty($cc['calcul']['no_calcul_profil'])){
                                            $searchNoCacul = array('[condition_on_champs]');
                                            $replaceNoCalcul = $donnees->{$champCondition};
                                            $noCalculProfil = str_replace($searchNoCacul, $replaceNoCalcul, $cc['calcul']['no_calcul_profil']);
                                            eval('$noCalculProfil = ' . $noCalculProfil . ';');
                                            if($noCalculProfil){
                                                $donnees->{$c} = -1;
                                            }
                                        }
                                        if ($noCalculProfil == false && is_array($cc['calcul']['champs_calcul'])) {
                                            for ($i = 0; $i < count($cc['calcul']['champs_calcul']); $i++) {
                                                $valueChamps = isset($donnees->{$cc['calcul']['champs_calcul'][$i]}) ? $donnees->{$cc['calcul']['champs_calcul'][$i]} : '0';
                                                array_push($arrayChamps, $valueChamps);
                                            }
                                        }
                                    }
                                } else {
                                    if (is_array($cc['calcul']['champs_calcul'])) {
                                        for ($i = 0; $i < count($cc['calcul']['champs_calcul']); $i++) {
                                            $valueChamps = isset($donnees->{$cc['calcul']['champs_calcul'][$i]}) ? $donnees->{$cc['calcul']['champs_calcul'][$i]} : '0';
                                            array_push($arrayChamps, $valueChamps);
                                        }
                                    }
                                }

                                if(!empty($arrayChamps)){
                                    $searchForReplace = array('[array]');
                                    $replaceForStrReplace = array(implode(',', $arrayChamps));
                                    $donnees->{'calcul_'.$c} = str_replace($searchForReplace, $replaceForStrReplace, $cc['calcul']['calcul_func']);
                                    eval('$donnees->{"calcul_".$c} = ' . $donnees->{'calcul_'.$c} . ';');

                                    $donnees->{$cc['compareWith']['champ']} = str_replace($searchForReplace, $replaceForStrReplace, $cc['calcul']['calcul_func']);
                                    eval('$donnees->{$cc["compareWith"]["champ"]} = ' . $donnees->{$cc["compareWith"]["champ"]} . ';');

                                    if(isset($cc['compareWith']) && !empty($cc['compareWith']) && isset($cc['compareWith']['champ']) && !empty($cc['compareWith']['champ'])){
                                        $champOther = $other_champs[$cc['compareWith']['champ']];
                                        if(isset($champOther) && !empty($champOther) && isset($champOther['calcul']['value_on_prioirity']) && !empty($champOther['calcul']['value_on_prioirity'])){
                                            $firstPrio = $champOther['calcul']['value_on_prioirity'][1];
                                            $secondPrio = $champOther['calcul']['value_on_prioirity'][2];
                                            $thirdPrio = $champOther['calcul']['value_on_prioirity'][3];
                                            if(isset($donnees->{$firstPrio}) && $donnees->{$firstPrio} != NULL && $donnees->{$firstPrio} !='-1') {
                                                $donnees->{$cc['compareWith']['champ']} = $donnees->{$firstPrio};
                                            } else if(isset($donnees->{$secondPrio}) && $donnees->{$secondPrio} != NULL && $donnees->{$secondPrio} !='-1') {
                                                $donnees->{$cc['compareWith']['champ']} = $donnees->{$secondPrio};
                                            } else if(isset($donnees->{$thirdPrio}) && $donnees->{$thirdPrio} != NULL && $donnees->{$thirdPrio} !='-1') {
                                                $donnees->{$cc['compareWith']['champ']} = $donnees->{$thirdPrio};
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if(isset($cc['profilChamps'][$_SESSION['Droit']]['modification']) && $cc['profilChamps'][$_SESSION['Droit']]['modification'] == 1){
                            if(isset($cc['disable_on_conditions']) && !empty($cc['disable_on_conditions'])){
                                foreach($cc['disable_on_conditions'] as $conditionKey => $conditionVal){
                                    $enableIf = 0;
                                    if(isset($conditionVal['profilEnable']) && !empty($conditionVal['profilEnable'])){
                                        $enableIf= isset($_SESSION['Droit']) && in_array($_SESSION['Droit'], $conditionVal['profilEnable']) ? 1 : 0;
                                    }

                                    if (isset($conditionVal['condition']) && !empty($conditionVal['condition']) && isset($conditionVal['champs_condition']) && !empty($conditionVal['champs_condition'])) {
                                        if($enableIf != 1) {

                                            $searchForReplace = array();
                                            $replaceForStrReplace = array();

                                            foreach ($conditionVal['champs_condition'] AS $champsConditionK => $champsConditionV) {
                                                if($champsConditionK != "sql_condition"){
                                                    array_push($searchForReplace, '[' . $champsConditionK . ']');
                                                    if(is_array($champsConditionV) && !empty($champsConditionV) && array_key_exists($_SESSION['Droit'], $champsConditionV) == true){
                                                        $replace = $champsConditionV[$_SESSION['Droit']];
                                                    } else {
                                                        $replace = isset($donnees->{$champsConditionV}) ? $donnees->{$champsConditionV} : $champsConditionV;
                                                    }
                                                    array_push($replaceForStrReplace, $replace);
                                                } else {
                                                    if(isset($conditionVal['type_condition']) && $conditionVal['type_condition'] == "sql_condition" && isset($conditionVal['condition_on_request']) && !empty($conditionVal['condition_on_request'])){
                                                        if(!empty($conditionVal['condition_on_request']['select_condition']) && isset($conditionVal['condition_on_request']['select_condition_on'])){
                                                            $select_condition_on = $conditionVal['condition_on_request']['select_condition_on'];
                                                            if(isset($conditionVal['condition_on_request']['select_condition'][$donnees->{$select_condition_on}])){
                                                                array_push($searchForReplace, '[' . $champsConditionK . ']');
                                                                $requestCondition = "SELECT ".$conditionVal['condition_on_request']['select_condition'][$donnees->{$select_condition_on}]." FROM ".$conditionVal['condition_on_request']['table_condition'];
                                                                if(isset($conditionVal['condition_on_request']['where_condition']) && !empty($conditionVal['condition_on_request']['where_condition'])){
                                                                    $requestCondition .= " WHERE ";
                                                                    $searchCondReplace = array();
                                                                    $replaceCondReplace = array();
                                                                    foreach($conditionVal['condition_on_request']['condition_params'] AS $condParamKey => $condParamVal){
                                                                        array_push($searchCondReplace, '[' . $condParamKey . ']');
                                                                        $replace = isset($donnees->{$condParamVal}) ? $donnees->{$condParamVal} : $condParamVal;
                                                                        array_push($replaceCondReplace, $replace);
                                                                    }
                                                                    $requestCondition .= str_replace($searchCondReplace, $replaceCondReplace, $conditionVal['condition_on_request']['where_condition']);
                                                                }
                                                                $conditionRequest = $this->Nom_Tab->findItem($requestCondition);
                                                                array_push($replaceForStrReplace, $conditionRequest[0]->result != NULL ? $conditionRequest[0]->result : 0);
                                                            }
                                                        }



                                                    }
                                                }
                                            }
                                            if(!empty($searchForReplace) && !empty($replaceForStrReplace)){
                                                $condition = str_replace($searchForReplace, $replaceForStrReplace, $conditionVal['condition']);
                                                eval('$condition = ' . $condition . ';');
                                                if(!isset($disable_on_conditions[$c])){
                                                    $disable_on_conditions[$c] = array();
                                                    array_push($disable_on_conditions[$c], $condition);
                                                } else {
                                                    array_push($disable_on_conditions[$c], $condition);
                                                }
                                            }

                                        }
                                    }
                                }
                            }
                        }

                        if (isset($cc['type_input']) && $cc['type_input'] == 'file')
                            $ifFile = 1;
                    }

                    if (isset($ifFile) && $ifFile == 1) {
                        $fileDonneesREQ = $this->Nom_Tab->findFileQuery('id_item', 'file_moteur', 'file_moteur.nom ,file_moteur.path, file_moteur.conf, file_moteur.champ, file_moteur.datequote', $donnees->id, $_GET['XLS']);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                        $fileDonnees = $this->Nom_Tab->findItem($fileDonneesREQ);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                    } else {
                        $fileDonneesREQ = null;
                        $fileDonnees = null;
                    }
                    // Partie qui charge les données complémentaire issue d'une autre table.
                    $colExtraAttribute = array();
                    if (isset($tabXlsFields[$_GET['XLS']]['extention_attribute']) && $tabXlsFields[$_GET['XLS']]['extention_attribute'] == 1) {
                        foreach ($champs as $champsExtraKey => $champsExtraVal) {
                            if (isset($champsExtraVal['type_input']) && $champsExtraVal['type_input'] == "bloc_attribute" && isset($champsExtraVal['profilChamps'][$_SESSION['Droit']]['modification']) && $champsExtraVal['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                                foreach ($champsExtraVal as $champsExtraValKey => $champsExtraValVal) {
                                    if (isset($champsExtraValVal['liste_detail']) && $champsExtraValVal['liste_detail'] == 1 && isset($champsExtraValVal['profilChamps']) && $champsExtraValVal['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                                        array_push($colExtraAttribute, $champsExtraValKey);
                                    }
                                }
                            }
                        }
                        if (isset($colExtraAttribute) && $colExtraAttribute != null) {
                            $colExtraA = implode(',', $colExtraAttribute);

                            $ExtraSQL = 'SELECT  ' . $tabXlsFields[$_GET['XLS']]['label_id_table_attribute'] . ',' . $colExtraA . ' FROM ' . $tabXlsFields[$_GET['XLS']]['table_attribute'] . ' WHERE ' . $tabXlsFields[$_GET['XLS']]['col_liee'] . ' = ' . abs($_GET['id']);
                            if (isset($tabXlsFields[$_GET['XLS']]['extention_attribute']) && $tabXlsFields[$_GET['XLS']]['extention_attribute'] != null) {
                                $ExtraSQL .= ' AND ' . $tabXlsFields[$_GET['XLS']]['requeteExtraAttribute'];
                            }
                            $donneesExtraAttributes = $this->Nom_Tab->findItem($ExtraSQL);// La fonction find est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.

                        }
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
            if (!isset($donneesExtraAttributes) || empty($donneesExtraAttributes)) {
                $donneesExtraAttributes = null;
            }

            $Version = self::VERSION;
            $fin = microtime(true);
            $delai = $fin - $debut;
            $delai = substr($delai, 0, 4);
            // Affichage en vue.
            $debugMode = $_SESSION['db_DebugMode'];
            $LocalTime = $_SESSION['db_LocalTime'];

            $form = new BootstrapForm($donnees);
            $this->render('Principal.editItem', compact('donnees', 'donneesExtraAttributes', 'Version', 'LocalTime', 'reqOptionsP', 'optionGroupe', 'fileDonnees', 'debugMode', 'historique', 'delai', 'req', 'fileDonneesREQ', 'requpdate', 'errors', 'options', 'tabXlsFields', 'champs', 'form', 'delai', 'req', 'nom_feuille', 'nom_feuilleHistorique', 'disable_on_conditions', 'other_champs'));
            exit();
        }
        else {
            $this->render('Principal.notFound');
            exit();
        }
    }

    public function deleteItem()
    {

        if (isset($_GET['id']) && isset($_SESSION['Droit'])) {

            // Récup du fichier conf Utilisateurs (dossier *racine*/conf/FieldsUsers.php).
            $tabXlsFields = listParamTab();
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


            $id = $tabXlsFields[$_GET['XLS']]['bdd_id'];
            $table = $tabXlsFields[$_GET['XLS']]['bdd_table'];

            // Nom de la colonne à modifier.
            $value = "hidden";

            // Fait passer la valeur de la colonne hidden à 1.
            $result = $this->Nom_Tab->desactiveitem($id, $table, $value, $_GET['id']); // Fonction dans le dossier *racine*/app/Table fichier Nom_TabTable.php.

            // Si la désactivation de l'item est OK.
            if ($result) {
                $_SESSION['delete'] = 1;
                header('Location: index.php?p=Principal.listItem&XLS=' . htmlspecialchars($_GET['XLS']) . '&collapsId=' . $_SESSION[htmlspecialchars($_GET['XLS'])]['collapsId']);
                exit();
            }
        }
    }

    public function deleteFile()
    {

        if (isset($_POST['id_item']) && isset($_POST['conf_file']) && isset($_POST['champ_file']) && isset($_POST['champ_xls'])) {

            $tabXlsFields = listParamTab();
            $champs = $tabXlsFields[$_POST['champ_xls']]['champs'];

            foreach ($champs as $key => $val) {
                if (isset($val['type_input']) && $val['type_input'] == 'file') {

                    if ($key == $_POST['conf_file']) {
                        if ($val['modeSup'] == 'hidden') {
                            $req = 'UPDATE file_moteur SET hidden = 1 WHERE id = ' . intval($_POST['id_item']);
                            $result = $this->Nom_Tab->findItem($req); // Fonction dans le dossier *racine*/app/Table fichier Nom_TabTable.php.

                        } elseif ($val['modeSup'] == 'delete') {
                            unlink($val['cheminDossier'] . $_POST['champ_file']);
                            $req = 'DELETE FROM file_moteur WHERE id = ' . intval($_POST['id_item']);
                            $result = $this->Nom_Tab->findItem($req); // Fonction dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                        }

                    }
                }
            }
        }

    }

    public function callAjaxForSelectFromListItem()
    {

        // verif user connecte
        if (isset($_SESSION['Droit'])) {

            // Verif params envoyés
            if (isset($_GET['idItem']) && isset($_GET['key']) && isset($_GET['alias']) && isset($_GET['XLS']) && isset($_GET['idOptiondefaut'])) {
                $tabXlsFields = listParamTab();

                // verif droit modification
                if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']])) {
                    if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"])) {
                        if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"][$_SESSION['Droit']])) {
                            if (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"][$_SESSION['Droit']]["modification"])) {
                                if ($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]["profilChamps"][$_SESSION['Droit']]["modification"] == 1) {

                                    $arrayChamp = $tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']];
                                    $result = $this->Nom_Tab->findItem('SELECT ' . $arrayChamp['bdd_id'] . ', ' . $arrayChamp['bdd_value'] . ' as ' . $arrayChamp['bdd_table_t'] . ' ' .
                                        'FROM ' . $arrayChamp['bdd_table'] . ' ' .
                                        (isset($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]['bdd_condition']) && trim($tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]['bdd_condition']) != "" ? " WHERE  " . $tabXlsFields[$_GET['XLS']]['champs'][$_GET['key']]['bdd_condition'] : "") .
                                        'ORDER BY ' . $arrayChamp['bdd_value'] . ' ASC');

                                    if ($result) {
                                        echo '<option value=""></option>';
                                        foreach ($result as $key => $val) {
                                            echo '<option style="color:#6e707e;" value="' . $val->id . '">' . $val->{$_GET['alias']} . '</option>';
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

    public function updateSelectFromListItem()
    {
        $return = 0;
        // verif user connecte
        if (isset($_SESSION['Droit'])) {

            // Verif params envoyés
            if (isset($_GET['idItem']) && isset($_GET['key']) && isset($_GET['alias']) && isset($_GET['XLS']) && isset($_GET['idOption'])) {
                $tabXlsFields = listParamTab();

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
        print(json_encode(["return" => $return, "error" => $return == 0 ? '<em>' . $_GET['key'] . ' Veuillez choisir un élément existant parmi la liste.</em>' : null]));
    }

    public function updateColumnsOnChange(){
        if(!empty($_POST) && isset($_POST['idItem']) && isset($_POST['XLS'])){
            $arrayJson = [];
            $tabXlsFields = listParamTab();
            $table = $tabXlsFields[$_POST['XLS']]['bdd_table'];
            $champs = $tabXlsFields[$_POST['XLS']]['champs'];
            $id = $tabXlsFields[$_POST['XLS']]['bdd_id'];
            $updateFieldsOnchange = $tabXlsFields[$_POST['XLS']]['updateFieldsOnchange'];
            if(isset($_POST['fieldsUpdate']) && !empty($_POST['fieldsUpdate'])){
                $updateFieldsOnchange = $_POST['fieldsUpdate'];
            }
            if(isset($tabXlsFields[$_POST['XLS']]['updateFieldsOnchange']) && !empty($tabXlsFields[$_POST['XLS']]['updateFieldsOnchange'])){

                $req = $this->Nom_Tab->findItemQuery($id, $table, $champs, $_POST['idItem']);// La fonction find est dans le dossier *racine*/App/Table fichier Nom_TabTable.php.
                $donnees = $this->Nom_Tab->findItem($req);// La fonction find est dans le dossier *racine*/App/Table fichier Nom_TabTable.php.

                if(!empty($donnees) && !empty($updateFieldsOnchange)){

                    if (isset($tabXlsFields[$_POST['XLS']]['other_champs']) && $tabXlsFields[$_POST['XLS']]['other_champs'] != NULL) {
                        $other_champ = $tabXlsFields[$_POST['XLS']]['other_champs'];
                        $champs = array_merge($champs, $other_champ);
                        $calculNoteCurrent = 0;
                        $addToJson = true;
                        $noCalculProfil = false;
                        foreach ($updateFieldsOnchange AS $key => $val) {
                            $val = trim($val);
                            if ($champs[$val]['liste'] == 1) {
                                if (isset($champs[$val]['type_input']) && $champs[$val]['type_input'] == "number" && isset($champs[$val]['calcul']) && !empty($champs[$val]['calcul'])) {
                                    if (isset($champs[$val]['calcul']['type_calcul']) && $champs[$val]['calcul']['type_calcul'] == "not_compare") {

                                        if(!isset($donnees[0]->{$val}) || (isset($donnees[0]->{$val}) && $donnees[0]->{$val} == NULL)) {
                                            $arrayChamps = [];
                                            if (isset($champs[$val]['calcul']['condition_on_champs']) && !empty($champs[$val]['calcul']['condition_on_champs'])) {
                                                $champCondition = $champs[$val]['calcul']['condition_on_champs'];
                                                if(isset($champs[$val]['calcul']['calcul_on_condition']) && !empty($champs[$val]['calcul']['calcul_on_condition']) && isset($champs[$val]['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}]) && !empty($champs[$val]['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}] )){
                                                    if (is_array($champs[$val]['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}])) {
                                                        for ($i = 0; $i < count($champs[$val]['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}]); $i++) {
                                                            $valueChamps = isset($donnees[0]->{$champs[$val]['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}][$i]}) ? $donnees[0]->{$champs[$val]['calcul']['calcul_on_condition'][$donnees[0]->{$champCondition}][$i]} : '0';
                                                            array_push($arrayChamps, $valueChamps);
                                                        }
                                                    }
                                                }
                                                else {
                                                    if(isset($champs[$val]['calcul']['no_calcul_profil']) && !empty($champs[$val]['calcul']['no_calcul_profil'])){
                                                        $searchNoCacul = array('[condition_on_champs]');
                                                        $replaceNoCalcul = $donnees[0]->{$champCondition};
                                                        $noCalculProfil = str_replace($searchNoCacul, $replaceNoCalcul, $champs[$val]['calcul']['no_calcul_profil']);
                                                        eval('$noCalculProfil = ' . $noCalculProfil . ';');
                                                        if($noCalculProfil){
                                                            $donnees[0]->{$val} = -1;

                                                        }
                                                    }
                                                    if ($noCalculProfil == false && is_array($champs[$val]['calcul']['champs_calcul'])) {
                                                        for ($i = 0; $i < count($champs[$val]['calcul']['champs_calcul']); $i++) {
                                                            $valueChamps = isset($donnees[0]->{$champs[$val]['calcul']['champs_calcul'][$i]}) ? $donnees[0]->{$champs[$val]['calcul']['champs_calcul'][$i]} : '0';
                                                            array_push($arrayChamps, $valueChamps);
                                                        }
                                                    }
                                                }
                                                $searchForReplace = array('[array]');
                                                $replaceForStrReplace = array(implode(',', $arrayChamps));
                                                $donnees[0]->{$val} = str_replace($searchForReplace, $replaceForStrReplace, $champs[$val]['calcul']['calcul_func']);
                                                if(isset($champs[$val]['calcul']['value_on_prioirity']) && !empty($champs[$val]['calcul']['value_on_prioirity'])){
                                                    $firstPrio = $champs[$val]['calcul']['value_on_prioirity'][1];
                                                    $secondPrio = $champs[$val]['calcul']['value_on_prioirity'][2];
                                                    $thirdPrio = $champs[$val]['calcul']['value_on_prioirity'][3];
                                                    if (!empty ($donnees[0]->{$firstPrio}) || !empty ($donnees[0]->{$secondPrio}) || !empty ($donnees[0]->{$thirdPrio})) {
                                                        $addToJson = false;
                                                        if (isset($donnees[0]->{$firstPrio}) && $donnees[0]->{$firstPrio} != NULL) {
                                                            $donnees[0]->{$val} = $donnees[0]->{$firstPrio};
                                                        } else if (isset($donnees[0]->{$secondPrio}) && $donnees[0]->{$secondPrio} != NULL) {
                                                            $donnees[0]->{$val} = $donnees[0]->{$secondPrio};
                                                        } else if (isset($donnees[0]->{$thirdPrio}) && $donnees[0]->{$thirdPrio} != NULL) {
                                                            $donnees[0]->{$val} = $donnees[0]->{$thirdPrio};
                                                        }
                                                        if (isset($_POST['currentField']) && !empty($_POST['currentField'])) {
                                                            $currentField = $_POST['currentField'];
                                                            if(($_POST['currentField'] == "note_valide_rgp" || $_POST['currentField'] == "note_valide_cdt" ) && $_SESSION["Droit"] == 9){
                                                                $currentField = $firstPrio;
                                                            }
                                                            switch ($currentField) {
                                                                case $firstPrio :
                                                                    $addToJson = true;
                                                                    break;
                                                                case $secondPrio :
                                                                    $addToJson = ($donnees[0]->{$firstPrio} == NULL) ? true : false;
                                                                    break;
                                                                case $thirdPrio :
                                                                    $addToJson = ($donnees[0]->{$firstPrio} == NULL) && ($donnees[0]->{$secondPrio} == NULL) ? true : false;
                                                                    break;
                                                            }
                                                        }
                                                    }
                                                }
                                                if( $noCalculProfil == false) {
                                                    eval('$donnees[0]->{$val} = ' . $donnees[0]->{$val} . ';');
                                                    if($addToJson) {
                                                        $arrayJson[$val] = $donnees[0]->{$val};
                                                        if(isset($champs[$val]['float_delim']) && !empty($champs[$val]['float_delim'])){
                                                            $arrayJson[$val] = str_replace('.',$champs[$val]['float_delim'],$donnees[0]->{$val});
                                                        }
                                                    }
                                                    $calculNoteCurrent = $donnees[0]->{$val};
                                                }
                                            }
                                        }
                                    }
                                    else if(isset($champs[$val]['calcul']['type_calcul']) && $champs[$val]['calcul']['type_calcul'] == "update_value" && isset($_POST['oldChampValue']) && $addToJson) {
                                        $searchForReplace = array('[champ1]', '[champ2]', '[champ3]');

                                        $champ1 = 1;
                                        if (isset($donnees[0]->{$champs[$val]['calcul']['champs_calcul']['champ1']})
                                            && $donnees[0]->{$champs[$val]['calcul']['champs_calcul']['champ1']} !== null ){
                                            $champ1 =  $donnees[0]->{$champs[$val]['calcul']['champs_calcul']['champ1']};
                                        }
                                        else {
                                            if (isset($champs[$val]['defaultValue']) && !empty($champs[$val]['defaultValue'])) {
                                                $searchChamp = array('[champs_related]');
                                                $replaceChamp = array($donnees[0]->{$champs[$val]['defaultValue']['champs_related']});
                                                $whereChamp = str_replace($searchChamp, $replaceChamp, $champs[$val]['defaultValue']['requete_condition']);
                                                $where = " WHERE " . $whereChamp;
                                                $result = $this->Nom_Tab->findItem("SELECT " . $val . " FROM " . $table . $where);
                                                if (!empty($result)) {
                                                    $champ1 = $result[0]->{$val};
                                                }
                                            }
                                        }
                                        $champ2 = $_POST['oldChampValue'];
                                        $champ3 = $calculNoteCurrent;

                                        $replaceForStrReplace = array($champ1, $champ2, $champ3);
                                        if(isset($champs[$val]['float_delim']) && !empty($champs[$val]['float_delim'])){
                                            foreach($replaceForStrReplace AS $replaceK=>$replaceV){
                                                $replaceForStrReplace[$replaceK] = str_replace($champs[$val]['float_delim'],'.', $replaceV);
                                            }
                                        }
                                        $donnees[0]->{$val} = str_replace($searchForReplace, $replaceForStrReplace, $champs[$val]['calcul']['calcul_func']);
                                        eval('$donnees[0]->{$val} = ' . $donnees[0]->{$val} . ';');
                                        $this->Nom_Tab->findItem('UPDATE ' . $tabXlsFields[$_POST['XLS']]['bdd_table'] . ' SET ' . $val . ' = ' . $donnees[0]->{$val} . ' WHERE ' . $tabXlsFields[$_POST['XLS']]['bdd_id'] . ' = ' . intval($_POST['idItem']));
                                        $arrayJson[$val] = $donnees[0]->{$val};
                                        if(isset($champs[$val]['float_delim']) && !empty($champs[$val]['float_delim'])){
                                            $arrayJson[$val] = str_replace('.',$champs[$val]['float_delim'],$donnees[0]->{$val});
                                        }
                                    }
                                    else if (isset($champs[$val]['calcul']['type_calcul']) && $champs[$val]['calcul']['type_calcul'] == "compare") {
                                        $searchForReplace = array('[champs1]', '[champs2]');
                                        $champs1 = isset($donnees[0]->{$champs[$val]['calcul']['champs_calcul']['champs1']}) && $donnees[0]->{$champs[$val]['calcul']['champs_calcul']['champs1']} !== null ? $donnees[0]->{$champs[$val]['calcul']['champs_calcul']['champs1']} : 0;
                                        $champs2 = isset($donnees[0]->{$champs[$val]['calcul']['champs_calcul']['champs2']}) && $donnees[0]->{$champs[$val]['calcul']['champs_calcul']['champs2']} !== null ? $donnees[0]->{$champs[$val]['calcul']['champs_calcul']['champs2']} : 0;
                                        $replaceForStrReplace = array($champs1, $champs2);
                                        $functionReplace = str_replace($searchForReplace, $replaceForStrReplace, $champs[$val]['calcul']['calcul_func']);
                                        eval('$functionReplace = ' . $functionReplace . ';');
                                        if (isset($champs[$val]['calcul']['returnValues']) && !empty($champs[$val]['calcul']['returnValues'])) {
                                            foreach ($champs[$val]['calcul']['returnValues'] AS $valK => $valValue) {
                                                $valueCalcul = str_replace('[val]', $functionReplace, $valK);
                                                eval('$valueCalcul = ' . $valueCalcul . ';');
                                                if ($valueCalcul == true) {
                                                    $donnees[0]->{$val} = $valValue;
                                                    $arrayJson[$val] = $donnees[0]->{$val};
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
            echo json_encode($arrayJson);
        }
    }


    public function updateListItem()
    {
        // Récup du fichier conf Historique (dossier *racine*/conf/FieldsHistorique.php).
        $tabXlsFieldsHistorique = listParamHistorique();
        $tableHistorique = $tabXlsFieldsHistorique['Historique']['bdd_table'];
        $champsHistorique = $tabXlsFieldsHistorique['Historique']['champs'];

        $return = 0;
        $error = NULL;
        $calculFunc = '';

        // verif user connecte
        if (isset($_SESSION['Droit'])) {
            // Verif params envoyés
            if (isset($_POST['idItem']) && isset($_POST['key']) && isset($_POST['XLS']) && isset($_POST['value'])) {
                $tabXlsFields = listParamTab();
                // verif conf ok
                if (isset($tabXlsFields[$_POST['XLS']]['champs'][$_POST['key']]) && isset($tabXlsFields[$_POST['XLS']]['bdd_table'])) {

                    // verif droit modification
                    if (isset($tabXlsFields[$_POST['XLS']]['champs'][$_POST['key']]["profilChamps"])) {
                        if (isset($tabXlsFields[$_POST['XLS']]['champs'][$_POST['key']]["profilChamps"][$_SESSION['Droit']])) {
                            if (isset($tabXlsFields[$_POST['XLS']]['champs'][$_POST['key']]["profilChamps"][$_SESSION['Droit']]["modification"])) {
                                if ($tabXlsFields[$_POST['XLS']]['champs'][$_POST['key']]["profilChamps"][$_SESSION['Droit']]["modification"] == 1) {

                                    $errors = new ControleObjetConfig(); // L'objet controleForm est dans le dossier racine/App/config.
                                    $errors->existSession($_SESSION['UserID']); // Verifie avant d'éxécuter une méthode si User est connecté.(sa évite qu'un formulaire non issue de l'application soit soumis à la place)
                                    $edit = 1;
                                    $cc = $tabXlsFields[$_POST['XLS']]['champs'][$_POST['key']];

                                    // Requete : Verification que l'item a modifier fait bien parti de la liste visible par le user:
                                    $query = "SELECT COUNT(*) AS total " .
                                        "FROM " . $tabXlsFields[$_POST['XLS']]['bdd_table'] . " " .
                                        "WHERE " . $tabXlsFields[$_POST['XLS']]['bdd_id'] . "=" . intval($_POST['idItem']);

                                    if (isset($tabXlsFields[$_POST['XLS']]['profilSql'][$_SESSION['Droit']])) {
                                        if (isset($tabXlsFields[$_POST['XLS']]['profilSql'][$_SESSION['Droit']]["req_sql"])) {
                                            if ($tabXlsFields[$_POST['XLS']]['profilSql'][$_SESSION['Droit']]["req_sql"] == 1) {

                                                if (isset($tabXlsFields[$_POST['XLS']]['profilSql'][$_SESSION['Droit']]["requete"])) {
                                                    if (trim($tabXlsFields[$_POST['XLS']]['profilSql'][$_SESSION['Droit']]["requete"]) != "") {
                                                        $query .= " " . $tabXlsFields[$_POST['XLS']]['profilSql'][$_SESSION['Droit']]["requete"];
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
                                                    if (!isset($cc['unique'])) {
                                                        $cc['unique'] = 0;
                                                    }
                                                    switch ($cc['type_input']) :
                                                        case "text":
                                                            $errors->validVarchar($_POST['value'], $_POST['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_POST['XLS'], $edit, $_POST['idItem']);
                                                            break;
                                                        case "textarea":
                                                            $errors->validVarchar($_POST['value'], $_POST['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_POST['XLS'], $edit, $_POST['idItem']);
                                                            break;
                                                        case "textarea_jointure":
                                                            $errors->validVarchar($_POST['value'], $_POST['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_POST['XLS'], $edit, $_POST['idItem']);
                                                            break;
                                                        case "date":
                                                            $errors->validDate($_POST['value'], $_POST['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_POST['XLS'], $edit, $_POST['idItem']);
                                                            break;
                                                        case "datetime":
                                                            $errors->validDateTime($_POST['value'], $_POST['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_POST['XLS'], $edit, $_POST['idItem']);
                                                            break;
                                                        case "number":
                                                            if ($_POST['value'] == '') {
                                                                $value = null;
                                                            } else {
                                                                $value = intval($_POST['value']);
                                                            }
                                                            $errors->validInt($value, $_POST['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_POST['XLS'], $edit, $_POST['idItem']);
                                                            break;
                                                        case "phone":
                                                            $errors->validPhone($_POST['value'], $_POST['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_POST['XLS'], $edit, $_POST['idItem']);
                                                            break;
                                                        case "email":
                                                            $errors->validMail($_POST['value'], $_POST['key'], $cc['obligatoire'], $cc['controle_balise'], $cc['taille_min'], $cc['taille_max'], $cc['unique'], $_POST['XLS'], $edit, $_POST['idItem']);
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
                                    if (isset($errors->errors[$_POST['key']]) && !empty($errors->errors[$_POST['key']])) {
                                        $return = 0;
                                        $error = '<em>' . $_POST['key'] . '</em> ' . $errors->errors[$_POST['key']];
                                        if ($_POST['key'] == "email") {
                                            $error = "<em>" . $_POST['key'] . "</em> " . $errors->errors[$_POST['key']] . "<br><em>Exemple : 'email@email.fr'</em>";
                                        }

                                    } else {
                                        $return = 1;
                                        $error = NULL;
                                    }

                                    if ($return == 1) {

                                        // Update BDD
                                        if ($cc['obligatoire'] == 0 && $_POST['value'] == '') {
                                            $set = 'null';
                                        } else {
                                            $set = '"' . htmlspecialchars($_POST['value']) . '"';
                                        }

                                        if ($cc['type_input'] == "textarea_jointure" && isset($cc['bdd_table']) && isset($cc['bdd_value']) && isset($cc['bdd_id']) && isset($cc['onInsert_champs']) && !empty($cc['onInsert_champs'])) {
                                            $whereCondition = "";
                                            $bdd_champs_insert = "";
                                            $val_bdd_insert_champs = "";
                                            foreach ($cc['onInsert_champs'] AS $keyChamps => $valChamps) {
                                                $valChamps = str_replace('[id_champs]', htmlspecialchars($_POST['idItem']), $valChamps);
                                                $whereCondition .= $whereCondition !== "" ? " AND " . $keyChamps . "=" . htmlspecialchars($valChamps) : $keyChamps . "=" . htmlspecialchars($valChamps);
                                                $bdd_champs_insert .= $bdd_champs_insert !== "" ? " , " . $keyChamps : $keyChamps;
                                                $val_bdd_insert_champs .= $val_bdd_insert_champs !== "" ? " , '" . htmlspecialchars($valChamps) . "'" : "'" . htmlspecialchars($valChamps) . "'";
                                            }
                                            // Récupération de l'ancienne valeur
                                            $oldVal = $this->Nom_Tab->findItem("SELECT " . $cc['bdd_value'] . " FROM " . $cc['bdd_table'] . " WHERE " . $whereCondition);
                                            $lastV = isset($oldVal[0]->{$cc['bdd_value']}) ? $oldVal[0]->{$cc['bdd_value']} : "";

                                            // Suppression de l'ancienne valeur
                                            $this->Nom_Tab->findItem("DELETE FROM " . $cc['bdd_table'] . " WHERE " . $whereCondition);
                                            // Création de la nouvelle ligne dans la table de jointure
                                            $this->Nom_Tab->findItem("INSERT INTO " . $cc['bdd_table'] . "(" . $cc['bdd_value'] . ", " . $bdd_champs_insert . ") VALUES ('" . htmlspecialchars($_POST['value']) . "', " . $val_bdd_insert_champs . ")");
                                            $set = htmlspecialchars($_POST['idItem']);

                                        } else {
                                            $oldVal = $this->Nom_Tab->findItem('SELECT ' . $_POST['key'] . ' FROM ' . $tabXlsFields[$_POST['XLS']]['bdd_table'] . ' WHERE ' . $tabXlsFields[$_POST['XLS']]['bdd_id'] . ' = ' . intval($_POST['idItem']));
                                            $lastV = $oldVal[0]->{$_POST["key"]};
                                        }

                                        switch ($_SESSION["Droit"]) {
                                            //cdp
                                            case 9:
                                                $note_valid = "note_valide_cdp";
                                                break;
                                            //rgp
                                            case 41:
                                                $note_valid = "note_valide_rgp";
                                                break;
//                                            //cdt
                                            case 12:
                                                $note_valid = "note_valide_cdt";
                                                break;
                                            default:
                                                $note_valid =  $_POST['key'];

                                        }

                                        // Mise à jour du champs
                                        $this->Nom_Tab->findItem('UPDATE ' . $tabXlsFields[$_POST['XLS']]['bdd_table'] . ' SET ' . $note_valid . ' = ' . $set . ' WHERE ' . $tabXlsFields[$_POST['XLS']]['bdd_id'] . ' = ' . intval($_POST['idItem']));

                                        if (isset($cc['type_input']) && $cc['type_input'] == "number" && isset($cc['calcul']) && !empty($cc['calcul'])) {
                                            if (isset($cc['calcul']['type_calcul']) && $cc['calcul']['type_calcul'] == "not_compare") {
                                                $arrayChamps = [];
                                                $getPoste = $this->Nom_Tab->findItem('SELECT posteId FROM ' . $tabXlsFields[$_POST['XLS']]['bdd_table'] . ' WHERE ' . $tabXlsFields[$_POST['XLS']]['bdd_id'] . ' = ' . intval($_POST['idItem']));
                                                if (!empty($getPoste[0]) && $set == 'null') {
                                                    $leftjoins = '';
                                                    $select = [];
                                                    if(isset($cc['calcul']['calcul_on_condition']) && !empty($cc['calcul']['calcul_on_condition'])
                                                        && isset($cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId]) && !empty($cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId] )
                                                    ) {
                                                        if (is_array($cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId])) {
                                                            for ($i = 0; $i < count($cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId]); $i++) {
                                                                array_push($select, $cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId][$i] . '.label AS '.$cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId][$i]);
                                                                $leftjoins .= ' LEFT JOIN bareme AS ' . $cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId][$i] . ' ON ' . $cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId][$i] . '.id=' . $tabXlsFields[$_POST['XLS']]['bdd_table'] . '.note' . ($i + 1);
                                                            }
                                                            $getNotes = 'SELECT ' . implode(',', $select) . ' FROM ' . $tabXlsFields[$_POST['XLS']]['bdd_table'] . $leftjoins . ' WHERE ' . $tabXlsFields[$_POST['XLS']]['bdd_table'] . '.' . $tabXlsFields[$_POST['XLS']]['bdd_id'] . ' = ' . intval($_POST['idItem']);
                                                            $donneeVal = $this->Nom_Tab->findItem($getNotes);
                                                            if(!empty($donneeVal)){
                                                                for ($i = 0; $i < count($cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId]); $i++) {
                                                                    $valueChamps = isset($donneeVal[0]->{$cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId][$i]}) ? $donneeVal[0]->{$cc['calcul']['calcul_on_condition'][$getPoste[0]->posteId][$i]} : '0';
                                                                    array_push($arrayChamps, $valueChamps);
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if(!empty($arrayChamps)){
                                                        $searchForReplace = array('[array]');
                                                        $replaceForStrReplace = array(implode(',', $arrayChamps));
                                                        $calculFunc = str_replace($searchForReplace, $replaceForStrReplace, $cc['calcul']['calcul_func']);
                                                        eval('$calculFunc = ' . $calculFunc . ';');
                                                    }

                                                }
                                            }
                                        }
                                        // Json des valeurs modifiés
                                        if ($lastV !== '' && $lastV != null) {
                                            $lastValue = $tabXlsFields[$_POST['XLS']]['champs'][$_POST['key']]["nom"] . ' : ' . $lastV . ' par ';
                                        } else {
                                            $lastValue = $tabXlsFields[$_POST['XLS']]['champs'][$_POST['key']]["nom"] . ' : ';
                                        }
                                        $jsonV = array(array($lastValue => $_POST['value']));

                                        // Récupération de l'adresse IP.
                                        $ip = $this->Nom_Tab->getIpAdress(); // La fonction get_ip_address est dans le dossier *racine*/app/Table fichier Nom_TabTable.php.
                                        $nom_feuille = $tabXlsFields[$_POST['XLS']]['nom_feuille'];
                                        // Attribution des valeurs à inserer dans la table Historique.
                                        $arrayValueHistorique = array('id_user' => $_SESSION['UserID'], 'ip_user' => $ip, 'id_item' => intval($_POST['idItem']), 'cle_conf' => $nom_feuille, 'value_modif' => json_encode($jsonV));
                                        $colInsertHistorique = array();
                                        $valueInsertHistorique = array();
                                        $valueParamHistorique = array();

                                        // Parcours du fichier conf Historique.
                                        foreach ($champsHistorique as $ch => $cch) {
                                            if ($cch['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && !isset($cc['date_now'])) {
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
                                        $this->Nom_Tab->insertItem($tableHistorique, $colInsertHistorique, $valueInsertHistorique, $valueParamHistorique);// La fonction insertItem est dans le dossier *racine*/core/Table fichier Table.php.
                                        $this->Nom_Tab->deleteHistorique(self::HISTORY_DAYS);

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($calculFunc != '') {
            $returnValue = ["return" => $return, "error" => $error, "calculVal" => $calculFunc];
        } else {
            $returnValue = ["return" => $return, "error" => $error];
        }
        print(json_encode($returnValue));
    }
}

