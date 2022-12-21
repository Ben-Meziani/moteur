<?php setlocale(LC_TIME, $LocalTime);
$confXLS = 'Users.listUsers';
$confXLSEdit = 'Users.editUser';
$confXLSSwitch = 'Users.switchSession';
?>
<?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Titre de la page, lien d'ajout d'item, alertes et messages de confirmation --------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
 */ ?>
<?php if (isset($_SESSION['succes']) && $_SESSION['succes'] == 1) { ?>
    <div id="bloc_info" class="alert alert-success" role="alert">
        L'item a été ajouté avec succès.
    </div>
    <?php
    unset($_SESSION['succes']);
} ?>
<?php if (isset($_SESSION['succes']) && $_SESSION['succes'] == 2) { ?>
    <div id="bloc_info" class="alert alert-success" role="alert">
        L'item a été modifié avec succès.
    </div>
    <?php
    unset($_SESSION['succes']);
} ?>
<?php if (isset($_SESSION['delete']) && $_SESSION['delete'] == 1) { ?>
    <div id="bloc_info" class="alert alert-dark" role="alert">
        L'item a été supprimé avec succès.
    </div>
    <?php
    unset($_SESSION['delete']);
} ?>
<?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Concerne la partie filtre , barre de recherche et csv ------------------------------------------------------------------------------------------------------------------>
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
 */ ?>
<div class="div_filtre">
    <form method="get" action="index.php?">
        <input type="hidden" name="p" value="<?php echo htmlspecialchars($confXLS); ?>"/>
        <input type="hidden" name="XLS" value="<?php echo htmlspecialchars($_SESSION[$_GET['XLS']]['XLS']); ?>"/>
        <input type="hidden" name="filtre" value="1"/>
        <?php if (isset($_SESSION[$_GET['XLS']]['collapsId'])) { ?>
            <input type="hidden" name="collapsId"
                   value="<?php echo htmlspecialchars($_SESSION[$_GET['XLS']]['collapsId']); ?>"/>
        <?php } ?>
        <?php if (isset($_SESSION[$_GET['XLS']]['order']) && isset($_SESSION[$_GET['XLS']]['champ'])) { ?>
            <input type="hidden" name="order"
                   value="<?php echo htmlspecialchars($_SESSION[$_GET['XLS']]['order']); ?>"/>
            <input type="hidden" name="champ"
                   value="<?php echo htmlspecialchars($_SESSION[$_GET['XLS']]['champ']); ?>"/>
        <?php } ?>
        <?php if (isset($_SESSION['Droit']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['ecriture'] == 1) { ?>
            <a id="lienModal" class="lien_add_user btnAdd"
               href="index.php?p=Users.addUser&XLS=<?php if (isset($_SESSION[$_GET['XLS']]['XLS'])) {
                   echo htmlspecialchars($_SESSION[$_GET['XLS']]['XLS']);
               } else {
                   echo htmlspecialchars($_GET['XLS']);
               }; ?>">Ajouter </a>
        <?php } ?>
        <a href="index.php?p=<?php echo htmlspecialchars($confXLS); ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
            echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
        } ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&champ=<?php if (isset($_SESSION[$_GET['XLS']]['champ'])) {
            echo htmlspecialchars($_SESSION[$_GET['XLS']]['champ']);
        } ?>&order=<?php if (isset($_SESSION[$_GET['XLS']]['order'])) {
            echo htmlspecialchars($_SESSION[$_GET['XLS']]['order']);
        } ?>&exportCsv=1&filtre=1&search=<?php if (isset($_SESSION[$_GET['XLS']]['search'])) {
            echo htmlspecialchars($_SESSION[$_GET['XLS']]['search']);
        }; ?>" class="btnExport">Exporter (CSV)</a>

        <a href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&delFilter=1"><i class="fas fa-backspace icoDash"
                                                                                       style="margin-left: 10px;"></i></a>

        <div class="div_pagination">
            <label class="pagination" for="ParPage">Pagination :
                <select id="ParPage" name="ParPage">
                    <?php
                    foreach ($pagination['pagination'] as $p => $p_v) {
                        ?>
                        <option value="<?php echo htmlspecialchars($p_v); ?>" <?php if (isset($_SESSION[$_GET['XLS']]['ParPage']) && $_SESSION[$_GET['XLS']]['ParPage'] == $p_v) {
                            echo 'selected="selected"';
                        } elseif ($ParPage == $p_v && !isset($_SESSION[$_GET['XLS']]['ParPage'])) {
                            echo 'selected="selected"';
                        } ?>><?php echo htmlspecialchars($p_v); ?></option>
                    <?php }
                    ?>
                </select>
            </label>
        </div>
        <?php
        if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['filtres'])) {
            $filtre = $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['filtres'];
        }
        if (isset($filtre_array_key) && isset($filtre_array_val) && $filtre_array_key != null && $filtre_array_val != null) {
            foreach ($filtre_array_val as $filtre_key => $filtre_val) {

                if ($active_view_array[$filtre_key] == 1) {

                    if (isset($filtre_array_val) && !empty($filtre_array_val)) {

                        if (isset($_GET[$filtre_array_key[$filtre_key]])) {
                            $_SESSION[$_GET['XLS']][$filtre_array_key[$filtre_key]] = $_GET[$filtre_array_key[$filtre_key]];

                        }
                        // Création du filtre de type Date si champs_type = date_min
                        if ($filtre_val == "date_min") {

                            ?>
                            <div class="div_groupe">
                                <label class="input_date2"
                                       for="input_date2"><?php if (isset($filtre[$filtre_array_key[$filtre_key]]['nom_filtre']) && $filtre[$filtre_array_key[$filtre_key]]['nom_filtre'] != null) {
                                        echo htmlspecialchars($filtre[$filtre_array_key[$filtre_key]]['nom_filtre']);
                                    } else {
                                        echo ucfirst(htmlspecialchars($filtre_array_key[$filtre_key]));
                                    } ?> :
                                    <input
                                            class="dateFiltre min"
                                            type="date"
                                            id="<?php echo $filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_' . $filtre_val ?>"
                                            name="<?php echo $filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_' . $filtre_val ?>"
                                            value="<?php if (isset($_GET[$filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_date_min']) && !empty($_GET[$filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_date_min'])) {
                                                echo $_GET[$filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_date_min'];
                                            } ?>"
                                    >
                                </label>
                            </div>
                            <?php

                        } // Création du filtre de type Date si champs_type = date_max
                        elseif ($filtre_val == "date_max") {
                            ?>
                            <div class="div_groupe">
                                <label class="input_date2"
                                       for="input_date2"><?php if (isset($filtre[$filtre_array_key[$filtre_key]]['nom_filtre']) && $filtre[$filtre_array_key[$filtre_key]]['nom_filtre'] != null) {
                                        echo htmlspecialchars($filtre[$filtre_array_key[$filtre_key]]['nom_filtre']);
                                    } else {
                                        echo ucfirst(htmlspecialchars($filtre_array_key[$filtre_key]));
                                    } ?> :
                                    <input class="dateFiltre max input_date2_val"
                                           type="date"
                                           id="<?php echo $filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_' . $filtre_val ?>"
                                           name="<?php echo $filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_' . $filtre_val ?>"
                                           value="<?php if (isset($_GET[$filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_date_max']) && !empty($_GET[$filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_date_max'])) {
                                               echo $_GET[$filtre[$filtre_array_key[$filtre_key]]['champ_filtre'] . '_date_max'];
                                           } ?>"
                                </label>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="div_groupe">
                                <label class="groupe"
                                       for="groupe"><?php if (isset($filtre[$filtre_array_key[$filtre_key]]['nom_filtre']) && $filtre[$filtre_array_key[$filtre_key]]['nom_filtre'] != null) {
                                        echo htmlspecialchars($filtre[$filtre_array_key[$filtre_key]]['nom_filtre']);
                                    } else {
                                        echo ucfirst(htmlspecialchars($filtre_array_key[$filtre_key]));
                                    } ?> :
                                    <select id="groupe"
                                            name="<?php echo htmlspecialchars($filtre_array_key[$filtre_key]); ?>">
                                        <option value="">Tous</option>
                                        <?php
                                        foreach ($filtre_val as $groupe_key => $groupe_val) {
                                            ?>
                                            <option value="<?php echo htmlspecialchars($groupe_val->id); ?>" <?php if (isset($_SESSION[$_GET['XLS']][$filtre_array_key[$filtre_key]]) && $_SESSION[$_GET['XLS']][$filtre_array_key[$filtre_key]] == $groupe_val->id) {
                                                echo 'selected="selected"';
                                            } ?>><?php echo htmlspecialchars($groupe_val->valeur); ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                </label>
                            </div>
                            <?php
                        }


                    }

                    ?>

                <?php } ?>
            <?php }
        } ?>
        <div class="div_search">
            <label for="search"><input id="search" class="inp_search" maxlength="100" type="search" name="search"
                                       placeholder="Rechercher"
                                       value="<?php if (isset($_SESSION[$_GET['XLS']]['search'])) {
                                           echo htmlspecialchars($_SESSION[$_GET['XLS']]['search']);
                                       } ?>"/></label>
            <input type="submit" value="Go" class="btnGo"/>
            <?php if (isset($_SESSION[$_GET['XLS']]['search'])) $search = $_SESSION[$_GET['XLS']]['search']; ?>
        </div>
    </form>
</div>
<div class="div_pagination_haut">
    <?php if ($nbTotalPage[0]->total != 0) { ?>
        <form method="post" name="f_follow">
            <ul class="pagination">
                <?php $disabled = 'disabled'; ?>
                <li class="page-item <?php if ($pageCurrent == '1') {
                    echo $disabled;
                } ?>"><a class="page-link"
                         href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                             echo htmlspecialchars($_GET['collapsId']);
                         } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                             echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                         } ?>&filtre=1&search=<?php if (isset($search)) {
                             echo htmlspecialchars($search);
                         }; ?>&ParPage=<?php if (isset($ParPage)) {
                             echo htmlspecialchars($ParPage);
                         }; ?>&page=<?php if ($pageCurrent != '1') {
                             echo $pageCurrent - 1;
                         } else {
                             echo htmlspecialchars($pageCurrent);
                         } ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&order=<?php if (isset($_GET['order'])) {
                             echo htmlspecialchars($_GET['order']);
                         } ?>&champ=<?php if (isset($_GET['champ'])) {
                             echo htmlspecialchars($_GET['champ']);
                         } ?>">&lt;</a></li>
                <?php for ($i = 1; $i <= $nbPage; $i++) {
                    if ($i == 1 || (($pageCurrent - 3) < $i && $i < ($pageCurrent + 3)) || $i == $nbPage) {
                        if ($i == $pageCurrent) { ?>
                            <li class="page-item active"><a class="page-link"
                                                            href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                                                                echo htmlspecialchars($_GET['collapsId']);
                                                            } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                                                                echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                                                            } ?>&filtre=1&search=<?php if (isset($search)) {
                                                                echo htmlspecialchars($search);
                                                            }; ?>&ParPage=<?php if (isset($ParPage)) {
                                                                echo htmlspecialchars($ParPage);
                                                            }; ?>&page=<?php echo htmlspecialchars($i); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&order=<?php if (isset($_GET['order'])) {
                                                                echo htmlspecialchars($_GET['order']);
                                                            } ?>&champ=<?php if (isset($_GET['champ'])) {
                                                                echo htmlspecialchars($_GET['champ']);
                                                            } ?>"><?php echo htmlspecialchars($i); ?></a></li>
                        <?php } else { ?>
                            <li class="page-item"><a class="page-link"
                                                     href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                                                         echo htmlspecialchars($_GET['collapsId']);
                                                     } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                                                         echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                                                     } ?>&filtre=1&search=<?php if (isset($search)) {
                                                         echo htmlspecialchars($search);
                                                     }; ?>&ParPage=<?php if (isset($ParPage)) {
                                                         echo htmlspecialchars($ParPage);
                                                     }; ?>&page=<?php echo htmlspecialchars($i); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&order=<?php if (isset($_GET['order'])) {
                                                         echo htmlspecialchars($_GET['order']);
                                                     } ?>&champ=<?php if (isset($_GET['champ'])) {
                                                         echo htmlspecialchars($_GET['champ']);
                                                     } ?>"><?php echo htmlspecialchars($i); ?></a></li>
                        <?php }
                    }
                } ?>
                <li class="page-item <?php if ($pageCurrent == $nbPage) {
                    echo $disabled;
                } ?>"><a class="page-link"
                         href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                             echo htmlspecialchars($_GET['collapsId']);
                         } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                             echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                         } ?>&filtre=1&search=<?php if (isset($search)) {
                             echo htmlspecialchars($search);
                         }; ?>&ParPage=<?php if (isset($ParPage)) {
                             echo htmlspecialchars($ParPage);
                         }; ?>&page=<?php if ($pageCurrent != $nbPage) {
                             echo $pageCurrent + 1;
                         } else {
                             echo htmlspecialchars($pageCurrent);
                         } ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&order=<?php if (isset($_GET['order'])) {
                             echo htmlspecialchars($_GET['order']);
                         } ?>&champ=<?php if (isset($_GET['champ'])) {
                             echo htmlspecialchars($_GET['champ']);
                         } ?>">&gt;</a></li>
            </ul>
        </form>
    <?php } ?>
    <p class="span_total_result">Résultats : <b><?php echo htmlspecialchars($nbTotalPage[0]->total); ?></b><br>
        Page : <b><?php echo htmlspecialchars($pageCurrent); ?> / <?php echo htmlspecialchars($nbPage); ?></b></p>
</div>
<?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Table qui liste toutes les données ------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
<div class="table-responsive">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Affichage des noms de colonnes ----------------------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
        <thead>
        <tr>


            <?php if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'] == 1) { ?>
                <th class="th_img_show"></th>
            <?php } ?>



            <?php
            $nbCol = '';
            $col = array();
            $label = array();
            $type = array();
            foreach ($champs as $key => $v) {
                if (isset($v['liste']) && $v['liste'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                    array_push($col, $key);
                    if (isset($v['type'])) {
                        array_push($type, $v['type']);
                    }
                    $nbCol++;
                    ?>
                    <th style="color: #222222;">
                        <div style="display: flex;">
                            <div style="width: max-content;flex:1; margin-right: 10px;">
                                <?php if (isset($v['liste_tri']) && $v['liste_tri'] == 1) { ?>
                                    <a class="" style="color: #222222;
                                <?php if (isset($v['bdd_table_t']) && isset($_SESSION[$_GET['XLS']]['champ'])
                                        && $_SESSION[$_GET['XLS']]['champ'] == $v['bdd_table_t']) {
                                        echo 'text-decoration: underline;';
                                    } elseif (isset($_SESSION[$_GET['XLS']]['champ']) && $_SESSION[$_GET['XLS']]['champ'] == $key) {
                                        echo 'text-decoration: underline;';
                                    } ?>"
                                       href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_SESSION[$_GET['XLS']]['collapsId'])) {
                                           echo htmlspecialchars($_SESSION[$_GET['XLS']]['collapsId']);
                                       } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                                           echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                                       } ?>&order=<?php if (isset($_SESSION[$_GET['XLS']]['order']) && $_SESSION[$_GET['XLS']]['order'] == 'DESC') {
                                           echo 'ASC';
                                       } elseif (isset($_SESSION[$_GET['XLS']]['order']) && $_SESSION[$_GET['XLS']]['order'] == 'ASC') {
                                           echo 'DESC';
                                       } else {
                                           echo 'ASC';
                                       } ?>&champ=<?php if (isset($v['type']) && $v['type'] == 'list_sql') {
                                           echo htmlspecialchars($v['bdd_table_t']);
                                       } else {
                                           echo htmlspecialchars($key);
                                       } ?>&filtre=1&search=<?php if (isset($_SESSION[$_GET['XLS']]['search'])) {
                                           echo htmlspecialchars($_SESSION[$_GET['XLS']]['search']);
                                       }; ?>&ParPage=<?php if (isset($_SESSION[$_GET['XLS']]['ParPage'])) {
                                           echo htmlspecialchars($_SESSION[$_GET['XLS']]['ParPage']);
                                       } elseif (isset($_GET['ParPage'])) {
                                           echo htmlspecialchars($_GET['ParPage']);
                                       } else {
                                           echo '25';
                                       }; ?>&page=<?php echo htmlspecialchars($pageCurrent); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&filtre=1"><?php echo htmlspecialchars($v['nom']); ?>
                                        <?php if (isset($v['text_info_bulle'])) { ?><a href="javascript:void(0)" data-toggle="popover"   data-html="true"  data-placement="right" data-content="<?php echo $v['text_info_bulle']; ?>" >
                                                <i class="fas fa-info-circle"></i></a><?php } ?>

                                    </a>
                                <?php } else { ?>
                                    <?php echo htmlspecialchars($v['nom']); ?>
                                    <?php if (isset($v['text_info_bulle'])) { ?><a href="javascript:void(0)" data-toggle="popover"  data-html="true"
                                                                                   data-placement="right"
                                                                                   data-content="<?php echo $v['text_info_bulle']; ?>" >
                                            <i class="fas fa-info-circle"></i></a><?php } ?>
                                <?php } ?>
                            </div>
                            <?php if (isset($v['liste_tri']) && $v['liste_tri'] == 1) { ?>
                                <a class="lien_order"
                                   style="<?php if (isset($v['bdd_table_t']) && isset($_SESSION[$_GET['XLS']]['champ']) && $_SESSION[$_GET['XLS']]['champ'] == $v['bdd_table_t'] && isset($_SESSION[$_GET['XLS']]['order']) && $_SESSION[$_GET['XLS']]['order'] == 'DESC') {
                                       echo 'color: red;';
                                   } elseif (isset($_SESSION[$_GET['XLS']]['champ']) && $_SESSION[$_GET['XLS']]['champ'] == $key && isset($_SESSION[$_GET['XLS']]['order']) && $_SESSION[$_GET['XLS']]['order'] == 'DESC') {
                                       echo 'color: red;';
                                   } ?>"
                                   href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_SESSION[$_GET['XLS']]['collapsId'])) {
                                       echo htmlspecialchars($_SESSION[$_GET['XLS']]['collapsId']);
                                   } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                                       echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                                   } ?>&order=DESC&champ=<?php if (isset($v['type']) && $v['type'] == 'list_sql') {
                                       echo htmlspecialchars($v['bdd_table_t']);
                                   } else {
                                       echo htmlspecialchars($key);
                                   } ?>&filtre=1&search=<?php if (isset($_SESSION[$_GET['XLS']]['search'])) {
                                       echo htmlspecialchars($_SESSION[$_GET['XLS']]['search']);
                                   }; ?>&ParPage=<?php if (isset($_SESSION[$_GET['XLS']]['ParPage'])) {
                                       echo htmlspecialchars($_SESSION[$_GET['XLS']]['ParPage']);
                                   } elseif (isset($_GET['ParPage'])) {
                                       echo htmlspecialchars($_GET['ParPage']);
                                   } else {
                                       echo '25';
                                   }; ?>&page=<?php echo htmlspecialchars($pageCurrent); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&filtre=1">&darr;</a>
                                <a class="lien_order"
                                   style="<?php if (isset($v['bdd_table_t']) && isset($_SESSION[$_GET['XLS']]['champ']) && $_SESSION[$_GET['XLS']]['champ'] == $v['bdd_table_t'] && isset($_SESSION[$_GET['XLS']]['order']) && $_SESSION[$_GET['XLS']]['order'] == 'ASC') {
                                       echo 'color: red;';
                                   } elseif (isset($_SESSION[$_GET['XLS']]['champ']) && $_SESSION[$_GET['XLS']]['champ'] == $key && isset($_SESSION[$_GET['XLS']]['order']) && $_SESSION[$_GET['XLS']]['order'] == 'ASC') {
                                       echo 'color: red;';
                                   } ?>"
                                   href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_SESSION[$_GET['XLS']]['collapsId'])) {
                                       echo htmlspecialchars($_SESSION[$_GET['XLS']]['collapsId']);
                                   } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                                       echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                                   } ?>&order=ASC&champ=<?php if (isset($v['type']) && $v['type'] == 'list_sql') {
                                       echo htmlspecialchars($v['bdd_table_t']);
                                   } else {
                                       echo htmlspecialchars($key);
                                   } ?>&filtre=1&search=<?php if (isset($_SESSION[$_GET['XLS']]['search'])) {
                                       echo htmlspecialchars($_SESSION[$_GET['XLS']]['search']);
                                   }; ?>&ParPage=<?php if (isset($_SESSION[$_GET['XLS']]['ParPage'])) {
                                       echo htmlspecialchars($_SESSION[$_GET['XLS']]['ParPage']);
                                   } elseif (isset($_GET['ParPage'])) {
                                       echo htmlspecialchars($_GET['ParPage']);
                                   } else {
                                       echo '25';
                                   }; ?>&page=<?php echo htmlspecialchars($pageCurrent); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&filtre=1">&uarr;</a>
                                <?php
                                $nbCol++;
                            } ?>
                        </div>
                    </th>
                    <?php
                }
                if (isset($v['bdd_value'])) {
                    array_push($label, $v['bdd_value']);
                    $nbCol++;
                }
            }
            ?>

            <?php if (isset($custom_list_edit) && $custom_list_edit != null) {
                foreach ($custom_list_edit as $custom_list_editKey => $custom_list_editVal) {
                    ?>
                    <th class="th_img_show"></th>
                <?php } ?>
            <?php } ?>
            <?php if ($_SESSION['Droit'] == 10000) { ?>
                <th class="th_img_show"></th>
            <?php } ?>


        </tr>
        </thead>
        <?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Affichage des entrées -------------------------------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
        <tbody>
        <?php
        if (!empty($donnees)) {
            foreach ($donnees as $donnee):
                ?>
                <tr>
                    <?php if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'] == 1) { ?>
                        <td><a class="collapse-item"
                               href="index.php?p=<?php echo htmlspecialchars($confXLSEdit); ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                                   echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                               } ?>&id=<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                                   echo htmlspecialchars($_GET['collapsId']);
                               } ?>"><img class="imgshow" src="image/show.png" alt="modifier"/></a></td>
                    <?php } ?>
                    <!--    Création des INPUTS si editable      -->
                    <?php
                    foreach ($champs as $key => $v):
                        if (isset($v['type']) && $v['type'] == "list_sql" && isset($v['liste']) && $v['liste'] == 1 && isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                            if (isset($v['profilChamps'][$_SESSION['Droit']]['modification']) && $v['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && isset($v['autocomplete']) && $v['autocomplete'] == 0 && isset($v['editable_list']) && $v['editable_list'] == 1) { ?>
                                <td style="<?php if (isset($v['liste_style'])) {
                                    echo htmlspecialchars($v['liste_style']);
                                } ?>">
                                    <!--   INPUT select si autocomplete à 0    -->
                                    <select
                                            style="<?php if (isset($v['liste_style'])) {
                                                echo htmlspecialchars($v['liste_style']);
                                            } ?>"
                                            class="form-control selectAjax "
                                            id="<?php echo htmlspecialchars($v['bdd_table_t']); ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                            name="<?php echo htmlspecialchars($key); ?>"
                                            onmouseenter="selectFromListUser('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($v['bdd_table_t']); ?>', '<?php echo htmlspecialchars($key); ?>', '<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>', '<?php echo htmlspecialchars($donnee->{$key}); ?>')"
                                            onfocus="selectFromListUser('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($v['bdd_table_t']); ?>', '<?php echo htmlspecialchars($key); ?>', '<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>', '<?php echo htmlspecialchars($donnee->{$key}); ?>')"
                                            onchange="updateSelectFromListUser('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($v['bdd_table_t']); ?>', '<?php echo htmlspecialchars($key); ?>', '<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>',this.value)">
                                        <option value="<?php echo htmlspecialchars($donnee->{$key}); ?>"
                                                id="<?php echo htmlspecialchars($v['bdd_table_t']); ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>_span"
                                                class="valueFromAjax"
                                                selected><?php echo htmlspecialchars($donnee->{$v['bdd_table_t']}) ?></option>

                                    </select>
                                </td>
                            <?php } else if (isset($v['profilChamps'][$_SESSION['Droit']]['modification']) && $v['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && isset($v['autocomplete']) && $v['autocomplete'] == 1 && isset($v['editable_list']) && $v['editable_list'] == 1) { ?>
                                <td style="<?php if (isset($v['liste_style'])) {
                                    echo htmlspecialchars($v['liste_style']);
                                } ?>">
                                    <!--   INPUT autocomplete si autocomplete à 1    -->
                                    <input type="text"
                                           id="inputItem<?php echo $v['bdd_table_t'] ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                           name="<?php echo $v['bdd_table_t'] ?>"
                                           value="<?php echo htmlspecialchars($donnee->{$v['bdd_table_t']}) ?>"
                                           minlength="<?php echo $v['taille_min'] ?>"
                                           maxlength="<?php echo $v['taille_max'] ?>"
                                           class="form-control autocomp ui-autocomplete-input"
                                           data-conf="<?php echo $v['bdd_table'] ?>"
                                           data-champs="<?php echo $v['bdd_value'] ?>"
                                           data-confname="<?php echo htmlspecialchars($_GET['XLS']); ?>"
                                           data-alias="<?php echo $v['bdd_table_t'] ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                           data-key="<?php echo htmlspecialchars($key); ?>"
                                           autocomplete="off"
                                           onchange="updateSelectFromListUser('<?php echo htmlspecialchars($_GET['XLS']); ?>',
                                                   '<?php echo htmlspecialchars($v['bdd_table_t']); ?>',
                                                   '<?php echo htmlspecialchars($key); ?>',
                                                   '<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>',
                                                   $('#labelhidden<?php echo $v['bdd_table_t'] ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>').val())"
                                    >
                                    <input type="hidden" name="item<?php echo $v['bdd_table_t'] ?>"
                                           id="labelhidden<?php echo $v['bdd_table_t'] ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                           value="">
                                </td>
                            <?php } else { ?>
                                <td>
                                    <div style="<?php if (isset($v['liste_style'])) {
                                        echo htmlspecialchars($v['liste_style']);
                                    } ?>">
                                        <?php echo htmlspecialchars($donnee->{$v['bdd_table_t']}) ?>
                                    </div>

                                </td>
                            <?php } ?>
                        <?php } elseif (isset($v['liste']) && $v['liste'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                            if (isset($v['editable_list']) && $v['editable_list'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['modification'] == 1) {
                                switch ($v['type_input']) :
                                    case "text":
                                        ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>">
                                            <input
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    type="<?php echo $v['type_input'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    onchange="updateListUsersItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"

                                                    value="<?php echo htmlspecialchars($donnee->{$key}) ?>">


                                        </td> <?php
                                        break;
                                    case "textarea":
                                        ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>">
                                            <textarea
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    onchange="updateListUsersItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                            ><?php echo htmlspecialchars($donnee->{$key}) ?></textarea>


                                        </td> <?php
                                        break;
                                    case "date":
                                        ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>">
                                            <input
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    type="<?php echo $v['type_input'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    value="<?php echo htmlspecialchars($donnee->{$key}) ?>"
                                                    onchange="updateListUsersItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                            >


                                        </td> <?php
                                        break;
                                    case "datetime":
                                        $fulldate = $donnee->{$key};
                                        $date = explode(' ', $fulldate)[0];
                                        $time = explode(' ', $fulldate)[1];
                                        $heur = explode(":", $time);
                                        $heurFormate = $heur[0] . ":" . $heur[1];
                                        ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>">
                                            <input
                                                    class="form-control dateTime<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    type="date"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>_1_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    value="<?php echo $date ?>"
                                                    onchange="updateListUsersItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value,'datetime_2_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>')"
                                            > à
                                            <input
                                                    class="form-control dateTime<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    type="time"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>_2_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    value="<?php echo $heurFormate ?>"
                                                    onchange="updateListUsersItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value,'datetime_1_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>')"
                                            >


                                        </td> <?php


                                        break;
                                    case "number":
                                        ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>">
                                            <input
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    type="<?php echo $v['type_input'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    value="<?php echo htmlspecialchars($donnee->{$key}) ?>"
                                                    onchange="updateListUsersItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                            >


                                        </td> <?php
                                        break;
                                    case "phone":
                                        ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>">
                                            <input
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    type="<?php echo $v['type_input'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    value="<?php echo htmlspecialchars($donnee->{$key}) ?>"
                                                    onchange="updateListUsersItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                            >


                                        </td> <?php
                                        break;
                                    case "email":
                                        ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>">
                                            <input
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    type="<?php echo $v['type_input'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    value="<?php echo htmlspecialchars($donnee->{$key}) ?>"
                                                    onchange="updateListUsersItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                            >


                                        </td> <?php
                                        break;
                                    case "url":
                                        ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>">
                                            <input
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    type="<?php echo $v['type_input'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    value="<?php echo htmlspecialchars($donnee->{$key}) ?>"
                                                    onchange="updateListUsersItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                            >

                                        </td> <?php
                                        break;
                                    default:
                                endswitch;
                            } else {
                                ?>
                                <?php if (isset($v['type_input']) && in_array($v['type_input'], ['datetime', 'date'])) {
                                    if ($donnee->{$key} == null) {
                                        $date = '';
                                    } else {
                                        if ($v['type_input'] == "date") {
                                            $date = strftime("%d/%m/%Y", strtotime($donnee->{$key}));
                                        } elseif ($v['type_input'] == "datetime") {
                                            $date = strftime("%d/%m/%Y à %Hh%M", strtotime($donnee->{$key}));
                                        }
                                    }
                                    ?>
                                    <td style="<?php if (isset($v['liste_style']) && $v['liste_style'] != "") {
                                        echo htmlspecialchars($v['liste_style']);
                                    } else {
                                        echo 'width:110px;text-align:center;';
                                    } ?>"><?php echo htmlspecialchars($date) ?></td>
                                <?php } else { ?>
                                    <td>
                                        <div style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>"></div>
                                        <?php echo nl2br(htmlspecialchars($donnee->{$key})) ?></td>
                                <?php }
                            }
                        }
                    endforeach; ?>

                    <?php if (isset($custom_list_edit) && $custom_list_edit != null) {
                        foreach ($custom_list_edit as $custom_list_editKey => $custom_list_editVal) {
                            if (!is_array($custom_list_editVal)) { ?>
                                <td><a class="collapse-item"
                                       href="<?php echo htmlspecialchars($custom_list_editVal); ?>"><?php echo htmlspecialchars($custom_list_editKey); ?></a>
                                </td> <?php }
                            if (is_array($custom_list_editVal)) {

                                $customString = str_replace('[id]', $donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}, $custom_list_editVal['lien']);
                                if (isset($custom_list_editVal['ChampsLien'])) {
                                    $customString = str_replace('[' . $custom_list_editVal['ChampsLien'] . ']', $donnee->{$custom_list_editVal['ChampsLien']}, $customString);
                                } ?>

                                <?php if (isset($custom_list_editVal['exeption']) && $custom_list_editVal['exeption'] == 1) { ?>

                                    <?php if (isset($planId) && $planId != null && isset($planId[$donnee->id])) { ?>
                                        <td>
                                            <a class="collapse-item btn btn-success custom_lien" <?php if (isset($custom_list_editVal['target'])) {
                                                echo 'target="' . $custom_list_editVal['target'] . '"';
                                            } ?> href="<?php if (isset($customString)) {
                                                echo htmlspecialchars($customString);
                                            } ?>"><?php echo htmlspecialchars($custom_list_editKey); ?></a></td>
                                    <?php } else { ?>
                                        <td></td>
                                    <?php } ?>

                                <?php } elseif (!isset($custom_list_editVal['exeption']) || $custom_list_editVal['exeption'] != 1) { ?>
                                    <td>
                                        <a class="collapse-item btn btn-success custom_lien" <?php if (isset($custom_list_editVal['target'])) {
                                            echo 'target="' . $custom_list_editVal['target'] . '"';
                                        } ?> href="<?php if (isset($customString)) {
                                            echo htmlspecialchars($customString);
                                        } ?>"><?php echo htmlspecialchars($custom_list_editKey); ?></a></td>
                                <?php } ?>
                                <?php
                            }
                        } ?>
                    <?php } ?>
                    <?php if ($_SESSION['Droit'] == 10000) { ?>
                        <td><a class="collapse-item"
                               href="index.php?p=<?php echo htmlspecialchars($confXLSSwitch); ?>&id=<?php echo htmlspecialchars($donnee->{$tabXlsFields['Users']['bdd_id']}); ?>"><img
                                        class="imgshow" src="image/SpAdmin.png" alt="modifier"/></a></td>
                    <?php } ?>
                </tr> <?php
            endforeach;
        } else {
            ?>
            <td colspan="<?php echo htmlspecialchars($nbCol); ?>">Aucun Résultat trouvé.</td>
        <?php }
        ?>
        </tbody>
    </table>
</div>
<?php /*
<!-- Fin de Table ----------------------------------------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->

<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Pagination  ------------------------------------------------------------------------------------------------------------------------------------------------------------>
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
<div class="div_pagination_bas">
    <?php if ($nbTotalPage[0]->total != 0) { ?>
        <form method="post" name="f_follow">
            <ul class="pagination">
                <?php $disabled = 'disabled'; ?>
                <li class="page-item <?php if ($pageCurrent == '1') {
                    echo $disabled;
                } ?>"><a class="page-link"
                         href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                             echo htmlspecialchars($_GET['collapsId']);
                         } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                             echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                         } ?>&filtre=1&search=<?php if (isset($search)) {
                             echo htmlspecialchars($search);
                         }; ?>&ParPage=<?php if (isset($ParPage)) {
                             echo htmlspecialchars($ParPage);
                         }; ?>&page=<?php if ($pageCurrent != '1') {
                             echo $pageCurrent - 1;
                         } else {
                             echo htmlspecialchars($pageCurrent);
                         } ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&order=<?php if (isset($_GET['order'])) {
                             echo htmlspecialchars($_GET['order']);
                         } ?>&champ=<?php if (isset($_GET['champ'])) {
                             echo htmlspecialchars($_GET['champ']);
                         } ?>">&lt;</a></li>
                <?php for ($i = 1; $i <= $nbPage; $i++) {
                    if ($i == 1 || (($pageCurrent - 3) < $i && $i < ($pageCurrent + 3)) || $i == $nbPage) {
                        if ($i == $pageCurrent) { ?>
                            <li class="page-item active"><a class="page-link"
                                                            href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                                                                echo htmlspecialchars($_GET['collapsId']);
                                                            } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                                                                echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                                                            } ?>&filtre=1&search=<?php if (isset($search)) {
                                                                echo htmlspecialchars($search);
                                                            }; ?>&ParPage=<?php if (isset($ParPage)) {
                                                                echo htmlspecialchars($ParPage);
                                                            }; ?>&page=<?php echo htmlspecialchars($i); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&order=<?php if (isset($_GET['order'])) {
                                                                echo htmlspecialchars($_GET['order']);
                                                            } ?>&champ=<?php if (isset($_GET['champ'])) {
                                                                echo htmlspecialchars($_GET['champ']);
                                                            } ?>"><?php echo htmlspecialchars($i); ?></a></li>
                        <?php } else { ?>
                            <li class="page-item"><a class="page-link"
                                                     href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                                                         echo htmlspecialchars($_GET['collapsId']);
                                                     } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                                                         echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                                                     } ?>&filtre=1&search=<?php if (isset($search)) {
                                                         echo htmlspecialchars($search);
                                                     }; ?>&ParPage=<?php if (isset($ParPage)) {
                                                         echo htmlspecialchars($ParPage);
                                                     }; ?>&page=<?php echo htmlspecialchars($i); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&order=<?php if (isset($_GET['order'])) {
                                                         echo htmlspecialchars($_GET['order']);
                                                     } ?>&champ=<?php if (isset($_GET['champ'])) {
                                                         echo htmlspecialchars($_GET['champ']);
                                                     } ?>"><?php echo htmlspecialchars($i); ?></a></li>
                        <?php }
                    }
                } ?>
                <li class="page-item <?php if ($pageCurrent == $nbPage) {
                    echo $disabled;
                } ?>"><a class="page-link"
                         href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                             echo htmlspecialchars($_GET['collapsId']);
                         } ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                             echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                         } ?>&filtre=1&search=<?php if (isset($search)) {
                             echo htmlspecialchars($search);
                         }; ?>&ParPage=<?php if (isset($ParPage)) {
                             echo htmlspecialchars($ParPage);
                         }; ?>&page=<?php if ($pageCurrent != $nbPage) {
                             echo $pageCurrent + 1;
                         } else {
                             echo htmlspecialchars($pageCurrent);
                         } ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&order=<?php if (isset($_GET['order'])) {
                             echo htmlspecialchars($_GET['order']);
                         } ?>&champ=<?php if (isset($_GET['champ'])) {
                             echo htmlspecialchars($_GET['champ']);
                         } ?>">&gt;</a></li>
            </ul>
        </form>
    <?php } ?>
    <p class="span_total_result">Résultats : <b><?php echo htmlspecialchars($nbTotalPage[0]->total); ?></b><br>
        Page : <b><?php echo htmlspecialchars($pageCurrent); ?> / <?php echo htmlspecialchars($nbPage); ?></b></p>
    <p style="font-size: 0.6em; float: right; margin-top: 10px;" class="version">Version
        : <?php echo htmlspecialchars($Version); ?></p>
</div>


<br/>
<?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Debug Mode ------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>

<?php if (isset($_SESSION['Droit']) && $debugMode == 1) { ?>
    <footer class="sticky-footer bg-white">
        <div class="text-center my-auto">
            <span class="lien_debug" onClick="AfficherMasquer()"><a id="debug" class="debug"
                                                                    href="#"">Infos SQL</a></span>
        </div>
        <div id="menu_debug" class="menu_debug">
            <ul class="list-group">
                <li class="list-group-item"><strong>Temps de la requête
                        :</strong> <?php echo htmlspecialchars($delai) ?> secondes.
                </li>
                <?php if (isset($_SESSION['debug']['Update']) && $_SESSION['debug']['Update'] != null) { ?>
                    <li class="list-group-item"><strong>Requête
                            Update:</strong> <?php echo htmlspecialchars($_SESSION['debug']['Update']); ?> </li>
                    <?php
                    $_SESSION['debug']['Update'] = null;
                } ?>
                <?php if (isset($_SESSION['debug']['Insert']) && $_SESSION['debug']['Insert'] != null) { ?>
                    <li class="list-group-item"><strong>Requête
                            Insert:</strong> <?php echo htmlspecialchars($_SESSION['debug']['Insert']); ?> </li>
                    <?php
                    $_SESSION['debug']['Insert'] = null;
                } ?>
                <li class="list-group-item"><strong>Requête AllItem :</strong> <?php echo htmlspecialchars($req); ?>
                </li>
                <li class="list-group-item"><strong>Requête COUNT :</strong> <?php echo htmlspecialchars($reqCount); ?>
                </li>
                <?php if (isset($reqOptionsP) && !empty($reqOptionsP)) {
                    foreach ($reqOptionsP as $reqOptionKey => $reqOptionVal) { ?>
                        <li class="list-group-item"><strong>Requête Options
                                :</strong> <?php echo htmlspecialchars($reqOptionVal); ?> </li>
                    <?php }
                } ?>
            </ul>
        </div>
    </footer>
<?php } ?>
