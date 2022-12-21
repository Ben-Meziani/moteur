<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="image/free.png"/>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/jquery-ui.min.css" rel="stylesheet">
    <link href="css/principal.css" rel="stylesheet">
    <script src="vendor/jquery/jquery.min.js"></script>
    <title><?php use Core\Config;
        require_once("../config/config.php");
        $config = Config::getInstance(ROOT . '/config/config.php');
        $arrayConfig = (array)$config;
        foreach ($arrayConfig as $arrayConfigKey => $arrayConfigVal) {
            $projet = $arrayConfigVal['name_projet'];
        }
        echo $projet; ?></title>
</head>
<body id="page-top">
<div id="wrapper">
    <?php  if (!isset($_GET["p"])) {
        $_GET["p"] = 'Principal.index';
        //die(var_dump($_GET["p"]));
    } ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php if (isset($_SESSION['nom']) && isset($_GET["p"]) && $_GET["p"] != 'Diagram.diagram' && isset($_SESSION['name_projet_user']) && $_SESSION['name_projet_user'] == $projet) { ?>

                <nav class="navbar navbar-expand-lg navbar-dark bg-gradient-dark py-3 shadow-sm static-top topbar">
                    <a class="navbar-brand text-gray-400"
                       href="index.php?p=Principal.index"><?php echo $projet; ?></a>
                    <p class="small connexionMobile mt-3" style="color:white; display: none;"><?php echo htmlspecialchars($_SESSION['nom']); ?> <?php echo htmlspecialchars($_SESSION['prenom']); ?>
                        (<?php echo htmlspecialchars($_SESSION['profil']); ?>)</p>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <?php if (isset($_SESSION['UserID']) && !in_array($_GET["p"], array("Diagram.diagram")) && isset($_SESSION['name_projet_user']) && $_SESSION['name_projet_user'] == $projet) { ?>
                            <ul id="navbarUl" class="navbar-nav mr-auto" style="display:none;">
                                <?php

                                require_once("../config/FieldsUsers.php");
                                require_once("../config/FieldsUserProfil.php");
                                require_once("../config/FieldsTeam.php");
                                $Nom_Tab = App::getInstance()->getTable("Nom_Tab");
                                $tabXlsFieldsUsers = listParamUsers();
                                $profilMenu = $tabXlsFieldsUsers['Users']['profilMenu'];
                                ?>

                                <?php if (isset($profilMenu[$_SESSION['Droit']]['lecture']) && $profilMenu[$_SESSION['Droit']]['lecture'] == 1) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.php?p=Users.listUsers&XLS=Users">
                                            <i class="fas fa-fw fa-male"></i>
                                            <span>Utilisateurs</span>
                                        </a>
                                    </li>

                                <?php } ?>

                                <?php
                                require_once("../config/Fields.php");
                                $tabXlsFields = listParamTab();
                                $tabUserMenus = array();
                                if (isset($tabXlsFields) && count($tabXlsFields) > 0) {
                                    // Parcours de la conf des menus/groupes
                                    foreach ($tabXlsFields as $k => $v) {
                                        // Si les droits sont bien déclarés, sinon ignorés
                                        if (isset($v["profilMenu"]) && isset($v["show_menu"]) && $v["show_menu"] === 1) {
                                            if (isset($v["profilMenu"][$_SESSION['Droit']])) {
                                                if (isset($v["profilMenu"][$_SESSION['Droit']]["lecture"])) {
                                                    // Droit de lecture pour le menu parcouru
                                                    if ($v["profilMenu"][$_SESSION['Droit']]["lecture"] == 1) {
                                                        // Nom de groupe par defaut
                                                        $titreGroupeMenu = "Gestion";
                                                        $titreSousGroupeMenu = "";
                                                        $titreChildSousGroupeMenu = "";
                                                        // Si on a bien un nom de groupe
                                                        if (isset($v["GroupeMenu"]) && trim($v["GroupeMenu"]) != "") $titreGroupeMenu = trim($v["GroupeMenu"]);
                                                        // Declaration, si premier element du groupe
                                                        if (!isset($tabUserMenus[$titreGroupeMenu])) $tabUserMenus[$titreGroupeMenu] = array();
                                                        // Si on a bien un nom de sous-groupe
                                                        if (isset($v["SousGroupeMenu"]) && trim($v["SousGroupeMenu"]) != "") $titreSousGroupeMenu = trim($v["SousGroupeMenu"]);
                                                        // Declaration, si premier element du groupe
                                                        if (!isset($tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu]) && trim($titreSousGroupeMenu) !== "") $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu] = array();
                                                        // Si on a des feuilles à mettre en dessous d'un sous groupe
                                                        if (isset($v["ChildSousGroupeMenu"]) && trim($v['ChildSousGroupeMenu'] !== "")) $titreChildSousGroupeMenu = trim($v["ChildSousGroupeMenu"]);
                                                        if (!isset($tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][$titreChildSousGroupeMenu]) && trim($titreChildSousGroupeMenu) !== "")
                                                            $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][$titreChildSousGroupeMenu] = array();

                                                        // Ajout du menu au tableau

                                                        if (isset($v["lienDirect"]) && $v["lienDirect"] != '') {
                                                            if (trim($titreSousGroupeMenu) != "") {
                                                                if (trim($titreChildSousGroupeMenu) != "") {
                                                                    $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][$titreChildSousGroupeMenu][trim($k)]['nom_feuille'] = $v["nom_feuille"];
                                                                    $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][$titreChildSousGroupeMenu][trim($k)]['lien'] = $v["lienDirect"];
                                                                } else {
                                                                    $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][trim($k)]['nom_feuille'] = $v["nom_feuille"];
                                                                    $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][trim($k)]['lien'] = $v["lienDirect"];
                                                                    if (isset($v["activeOnXLS"]) && !empty($v["activeOnXLS"])) {
                                                                        $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][trim($k)]['activeOnXLS'] = $v["activeOnXLS"];
                                                                    }
                                                                    if (isset($v["activeOnPages"]) && !empty($v["activeOnPages"])) {
                                                                        $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][trim($k)]['activeOnPages'] = $v["activeOnPages"];
                                                                    }
                                                                }

                                                            } else {
                                                                if($titreGroupeMenu == $v['nom_feuille']){
                                                                    $tabUserMenus[$titreGroupeMenu][trim($k)]['group_menu'] = 1;
                                                                }
                                                                $tabUserMenus[$titreGroupeMenu][trim($k)]['nom_feuille'] = $v["nom_feuille"];
                                                                $tabUserMenus[$titreGroupeMenu][trim($k)]['lien'] = $v["lienDirect"];
                                                            }


                                                            if (isset($v["ifCollapsId"])) {
                                                                if (trim($titreSousGroupeMenu) != "") {
                                                                    if (trim($titreChildSousGroupeMenu) !== "") {
                                                                        $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][$titreChildSousGroupeMenu][trim($k)]['ifCollapsId'] = $v["ifCollapsId"];
                                                                    } else {
                                                                        $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][trim($k)]['ifCollapsId'] = $v["ifCollapsId"];
                                                                    }

                                                                } else {
                                                                    $tabUserMenus[$titreGroupeMenu][trim($k)]['ifCollapsId'] = $v["ifCollapsId"];
                                                                }
                                                            }

                                                        } else {
                                                            if (trim($titreSousGroupeMenu) != "") {
                                                                if (trim($titreChildSousGroupeMenu) !== "") {
                                                                    $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][$titreChildSousGroupeMenu][trim($k)] = trim($v["nom_feuille"]);
                                                                } else {
                                                                    $tabUserMenus[$titreGroupeMenu][$titreSousGroupeMenu][trim($k)] = trim($v["nom_feuille"]);
                                                                }
                                                            } else {
//
                                                                if(isset($v["MenuName"]) && $v["MenuName"] == '[mois]'){
                                                                    $searchForReplace = array('[mois]');
                                                                    $month_number = explode('_', $k);
                                                                    $show_limit = date('d');

                                                                    if(isset($v['showLimit']['limitProfil'][$_SESSION['Droit']])){
                                                                        $show_limit = $v['showLimit']['limitProfil'][$_SESSION['Droit']];
                                                                    }
                                                                    if(date('d') > $show_limit || in_array($_SESSION['Droit'], [1, 10000])){
                                                                        $month_number = $month_number[1];
                                                                    }else{
                                                                        $month_number = $month_number[1]+1 ;
                                                                    }
                                                                    $dates = ucfirst(strftime("%B %Y", strtotime("-".($month_number)." month",strtotime(date('Y-m-01')))));
                                                                    $replaceForStrReplace = array($dates);
                                                                    $tabUserMenus[$titreGroupeMenu][ trim($k) ]=trim(str_replace($searchForReplace, $replaceForStrReplace, $v["MenuName"]));
                                                                } else {
                                                                    $tabUserMenus[$titreGroupeMenu][ trim($k) ]=trim($v["nom_feuille"]);
                                                                }

                                                            }
                                                        }

                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                // Si tableau de menus du user est rempli, HTML
                                if (count($tabUserMenus) > 0 ) {
                                    $collapseInc = 100;
                                    $collapseDiv = 1;
                                    $collapseSousDiv = 1;
                                    foreach ($tabUserMenus as $k => $v) {
                                        $collapseInc++;

                                        // Groupe
                                        print('<div class="topbar-divider d-none d-sm-block"></div>');
                                        print('<li class="nav-item dropdown">');
                                        foreach ($v as $kkk => $vvv) {
                                            if(isset($vvv['group_menu']) && $vvv['group_menu'] == 1){
                                                $activeMenu = '';
                                                if(isset($_GET["relatedXLS"]) && $_GET["relatedXLS"] == $kkk){
                                                    $activeMenu = 'active';
                                                }

                                                $menuDrop = ' <a  id="collapsePages' . $collapseInc . '"
                                                href="' . $vvv['lien'] . (isset($vvv["ifCollapsId"]) && $vvv["ifCollapsId"] == 0 ? '' : '&collapsId=collapsePages' . $collapseInc) . '"
                                                     class="nav-link '.$activeMenu.'" role="button" >' .
                                                    '<span>' . $k . '</span>' .
                                                    '</a>';
                                            }else{
                                                $menuDrop = ' <a  id="collapsePages' . $collapseInc . '"  href="#"
                                                     data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                     class="nav-link dropdown-toggle" role="button" >' .
                                                    '<span>' . $k . '</span>' .
                                                    '</a>';
                                            }
                                        }
                                        print $menuDrop;
                                        print('<ul aria-labelledby="collapsePages' . $collapseInc . '" class="dropdown-menu border-0 shadow">');

                                        // Liens du groupe
                                        foreach ($v as $kk => $vv) {
                                            if (is_array($vv)) {
                                                if (!isset($vv["lien"])) {
                                                    print('
<li class="dropdown-submenu">
<a id="collapseDiv' . ($collapseInc + $collapseDiv) . '" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
 aria-expanded="false" class="dropdown-item dropdown-toggle">' . $kk . '</a>
                                <ul aria-labelledby="collapseDiv' . ($collapseInc + $collapseDiv) . '" class="dropdown-menu border-0 shadow"> ');
                                                    foreach ($vv as $sous_key => $sous_val) {

                                                        if (is_array($sous_val)) {
                                                            if (!isset($sous_val['lien'])) {
                                                                print('<li class="dropdown-submenu">
<a id="collapseSousDiv' . ($collapseInc + $collapseSousDiv) . '" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">' . $sous_key . '</a>
                                            <ul aria-labelledby="collapseSousDiv' . ($collapseInc + $collapseSousDiv) . '" class="dropdown-menu border-0 shadow">');
                                                                foreach ($sous_val as $s_key => $s_val) {
                                                                    print('<li>');
                                                                    if (isset($s_val['lien'])) {
                                                                        print('
                                                                  
                                                                    <a style="font-size: smaller" class="dropdown-item' .
                                                                            (isset($_GET["XLS"]) && $_GET["XLS"] == $s_key ? ' active' : '') . '"
                                                href="' . $s_val['lien'] .
                                                                            (isset($s_val["ifCollapsId"]) &&
                                                                            $s_val["ifCollapsId"] == 0 ? '' : '&collapsId=collapsePages' . $collapseInc) . '">
                                                ' . $s_val['nom_feuille'] . '</a>');
                                                                    } else {
                                                                        print('<a style="font-size: smaller" class="dropdown-item' . (isset($_GET["XLS"]) && $_GET["XLS"] == $s_key ? ' active' : '') . '" href="index.php?p=Principal.listItem&collapsId=collapsePages' . $collapseInc . '&XLS=' . $s_key . '">' . $s_val . '</a>');

                                                                    }
                                                                    print('</li>');

                                                                }
                                                                print('</ul>');
                                                                $collapseSousDiv++;
                                                            } else {
                                                                $activeMenu = "";

                                                                if ((isset($_GET["XLS"]) && $_GET["XLS"] == $sous_key) ||

                                                                    (isset($_GET["XLS"]) && isset($sous_val['activeOnXLS']) && in_array($_GET["XLS"], $sous_val['activeOnXLS']))
                                                                    || (isset($_GET["p"]) && isset($sous_val['activeOnPages']) && in_array(explode('.', $_GET["p"])[1], $sous_val['activeOnPages']))
                                                                ) {
                                                                    $activeMenu = "active";
                                                                    if (isset($_GET["relatedXLS"]) && !in_array($_GET["relatedXLS"], $sous_val['activeOnXLS'])) {
                                                                        $activeMenu = "";
                                                                    }

                                                                }
                                                                print('<li><a class="dropdown-item ' . $activeMenu . '" href="' . $sous_val['lien'] . (isset($sous_val["ifCollapsId"]) && $sous_val["ifCollapsId"] == 0 ? '' : '&collapsId=collapsePages' . $collapseInc) . '">' . $sous_val['nom_feuille'] . '</a></li>');
                                                            }
                                                        } else {
                                                            print('<li><a class="dropdown-item ' . (isset($_GET["XLS"]) && $_GET["XLS"] == $sous_key ? ' active' : '') . '" href="index.php?p=Principal.listItem&collapsId=collapsePages' . $collapseInc . '&XLS=' . $sous_key . '">' . $sous_val . '</a></li>');
                                                        }
                                                    }
                                                    print('</ul></li>');
                                                    $collapseDiv++;
                                                }
                                                else {
                                                    $activeMenu = '';
                                                    if(!isset($vv['group_menu'])){
                                                        if((isset($_GET["XLS"]) && $_GET["XLS"] == $kk)
                                                            ||
                                                            (isset($_GET["relatedXLS"]) && $_GET["relatedXLS"] == $kk)
                                                        ){
                                                            $activeMenu = 'active';
                                                        }

                                                        print('<a class="dropdown-item ' . $activeMenu . '" href="' . $vv['lien'] . (isset($vv["ifCollapsId"]) && $vv["ifCollapsId"] == 0 ? '' : '&collapsId=collapsePages' . $collapseInc) . '">' . $vv['nom_feuille'] . '</a>');
                                                    }
                                                }
                                            } else {
                                                $activeMenu = '';
                                                if(isset($_GET["XLS"]) && $_GET["XLS"] == $kk && !isset($_GET["relatedXLS"])){
                                                    $activeMenu = 'active';
                                                }
                                                print('<a class="dropdown-item ' . $activeMenu . '" href="index.php?p=Principal.listItem&collapsId=collapsePages' . $collapseInc . '&XLS=' . $kk . '">' . $vv . '</a>');
                                            }
                                        }

                                        print('</ul>');
                                        print('</li>');
                                    }
                                } ?>



                            </ul>
                        <?php } ?>

                        <div class="connexionDiv" >
                            <p class="small connexionNotMobile" style="color:white; display: none;"><?php echo htmlspecialchars($_SESSION['nom']); ?> <?php echo htmlspecialchars($_SESSION['prenom']); ?>
                                (<?php echo htmlspecialchars($_SESSION['profil']); ?>)</p>
                            <a class="btn btn-outline-danger btnDisconect ml-2 btn-sm" style="color: white;
    border-color: rgba(255,255,255,.5);" href="index.php?p=Users.disconnect">Déconnexion</a>
                            <?php if (isset($_SESSION['SuperAdmin']) && isset($_SESSION['SuperAdminDroit']) && $_SESSION['SuperAdminDroit'] == 10000) { ?>
                                <a  class="btn btn-outline-warning btnDisconect btn-sm"
                                    href="index.php?p=Users.switchSession&switch=1">SuperAdmin</a>
                            <?php } ?>

                        </div>
                    </div>

                </nav>

            <?php } ?>
            <div class="container-fluid">

                <div class="modal fade show" id="wait" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalCenterTitle" aria-modal="true"
                     style="padding-right: 19px; display: none; z-index: 9999999">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="border: none">
                            <div class="col-md-3 offset-md-3"
                                 style="width: 100%;height: 100%;position: fixed;text-align: center;top: 50%;margin-top: -100px;margin-left: -150px;left: 50%;">
                                <img src="../public/image/loader.gif" style="height: 30%;"></div>
                        </div>
                    </div>
                </div>


                <?php if (isset($_GET["p"]) && $_GET["p"] != 'Diagram.diagram') { ?>
                    <br/>
                <?php } ?>
                <?php if (isset($nom_feuille)) { ?>

                    <?php if ($nom_feuille == 'Utilisateurs') { ?>
                        <h4><i class="fas fa-fw fa-male icoDash"></i> <?php echo htmlspecialchars($nom_feuille); ?></h4>
                    <?php } elseif ($nom_feuille == 'Mon Profil') { ?>
                        <h4><i class="fas fa-user-circle icoDash"></i> <?php echo htmlspecialchars($nom_feuille); ?>
                        </h4>
                    <?php } else { ?>
                        <h4><i class="fas fa-fw fa-table icoDash"></i> <?php echo htmlspecialchars($nom_feuille); ?>
                        </h4>
                    <?php } ?>
                <?php } ?>
                <?php echo $content;

                ?>

                <?php
                $finGlobal = microtime(true);
                $delaiGlobal = $finGlobal - $_SESSION['debutGlobal'];
                $tpsGlobal = substr($delaiGlobal, 0, 4);
                ?>
                <p style="font-size: 0.7em; text-align: center;background-color: #F8F9FC;margin:1px;padding: 1px;">Temps
                    d'exécution de la page : <?php echo htmlspecialchars($tpsGlobal); ?></p>
            </div>
        </div>

    </div>
</div>
<script src="js/jquery-ui.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
<script src="js/principal.js"></script>


</body>
</html>
