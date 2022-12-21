<?php setlocale(LC_TIME, $LocalTime);
$confXLS = 'Principal.listItem';
$confXLSEdit = 'Principal.editItem';

$currentDay = intval(date("d"));
$currentMonth = intval(date("m"));
$currentYear = intval(date("Y"));
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
<?php
if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['customListHtml'])) {
    print('<div class="alert alert-info" style="padding:0">');
    print($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['customListHtml']);
    print('</div>');
}
?>
<?php if (isset($tabXlsFields[$_GET['XLS']]['show_date']) && $tabXlsFields[$_GET['XLS']]['show_date'] == 1) {
    if(isset($_GET['XLS']) && (strpos($_GET['XLS'], 'mois_') !== false)){
        $month_number = explode('_', $_GET['XLS']);
        $dates = ucfirst(strftime("%B %Y", strtotime("-".($month_number[1] +1)." month",strtotime(date('Y-m-01')))));
    } else {
        $dates = ucfirst(strftime("%B %Y", strtotime('-1 month',strtotime(date('Y-m-01')))));
    }
  if(isset($_GET['XLS']) && (strpos($_GET['XLS'], 'mois_') !== false)){
        $month_number = explode('_', $_GET['XLS']);
        $show_limit = date('d');
        if(isset($tabXlsFields[$_GET['XLS']]['showLimit']['limitProfil'][$_SESSION['Droit']])){
            $show_limit = $tabXlsFields[$_GET['XLS']]['showLimit']['limitProfil'][$_SESSION['Droit']];
        }
        if(date('d') > $show_limit || in_array($_SESSION['Droit'], [1, 10000])){
            $month_number = $month_number[1];
        }else{
            $month_number = $month_number[1]+1 ;
        }
        $dates = ucfirst(strftime("%B %Y", strtotime("-".($month_number)." month",strtotime(date('Y-m-01')))));
    } else {
      $dates = ucfirst(strftime("%B %Y", strtotime('-1 month',strtotime(date('Y-m-01')))));

  }

    ?>
    <h4 class="text-center"><?php echo $dates; ?></h4>
<?php } ?>
<div class="div_filtre">

    <form method="get" action="index.php?">
        <input type="hidden" name="p" value="<?php echo htmlspecialchars($confXLS); ?>"/>
        <input type="hidden" name="XLS" value="<?php if (isset($_SESSION[$_GET['XLS']]['XLS'])) {
            echo htmlspecialchars($_SESSION[$_GET['XLS']]['XLS']);
        } else {
            echo htmlspecialchars($_GET['XLS']);
        }; ?>"/>
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
               href="index.php?p=Principal.addItem&XLS=<?php if (isset($_SESSION[$_GET['XLS']]['XLS'])) {
                   echo htmlspecialchars($_SESSION[$_GET['XLS']]['XLS']);
               } else {
                   echo htmlspecialchars($_GET['XLS']);
               }; ?>&collapsId=<?php echo htmlspecialchars($_GET['collapsId']); ?>">Ajouter </a>
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
        <a href="index.php?p=<?php echo htmlspecialchars($confXLS); ?>&delFilter=1&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&collapsId=<?php echo htmlspecialchars($_GET['collapsId']); ?>"><i
                    class="fas fa-backspace icoDash" style="margin-left: 10px;"></i></a>

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

                        }
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
                        }
                        else {
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
                                            if(isset($groupe_val->id)){ ?>
                                                <option value="<?php echo htmlspecialchars($groupe_val->id); ?>" <?php if (isset($_SESSION[$_GET['XLS']][$filtre_array_key[$filtre_key]]) && $_SESSION[$_GET['XLS']][$filtre_array_key[$filtre_key]] == $groupe_val->id) {
                                                    echo 'selected="selected"';
                                                } ?>><?php echo htmlspecialchars($groupe_val->valeur); ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo htmlspecialchars($groupe_key); ?>" <?php if (isset($_SESSION[$_GET['XLS']][$filtre_array_key[$filtre_key]]) && $_SESSION[$_GET['XLS']][$filtre_array_key[$filtre_key]] == $groupe_key) {
                                                    echo 'selected="selected"';
                                                } ?>><?php echo htmlspecialchars($groupe_val); ?></option>
                                            <?php }
                                        }
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
<?php if (isset($tabXlsFields[$_GET['XLS']]['show_bareme']) && $tabXlsFields[$_GET['XLS']]['show_bareme'] == 1) {
    ?>
    <div class="tableBareme mt-2">
        <table class="table table-bordered text-center">
            <tr>
                <th>Note</th>
                <th colspan="3">Barème de notation</th>
            </tr>

            <tr>
                <td >1</td>
                <td >1 skill maîtrisé = 1</td>
                <td >0 Env. maîtrisé - lacunes faibles à combler = 1</td>
                <td >insuffisant = 1 </td>
            </tr>
            <tr>
                <td >2</td>
                <td >2 skills maîtrisés = 2</td>
                <td >1 Env. maîtrisé = 2</td>
                <td >très moyen = 2 </td>
            </tr>
            <tr>
                <td >3</td>
                <td >3 skills maîtrisés = 3</td>
                <td >2 Env. maîtrisé = 3</td>
                <td >moyen = 3 </td>
            </tr>
            <tr>
                <td >4</td>
                <td >4 skills maîtrisés = 4</td>
                <td >3 Env. maîtrisés = 4</td>
                <td >bon/autonome = 4 </td>
            </tr>
            <tr>
                <td >5</td>
                <td >5 skills maîtrisés = 5</td>
                <td >4 Env. maîtrisés = 5</td>
                <td >excellent/expert = 5</td>
            </tr>
        </table>
    </div>
<?php }
if(isset($tabXlsFields[$_GET['XLS']]['showLimit']) && !empty($tabXlsFields[$_GET['XLS']]['showLimit'])){
    if(isset($tabXlsFields[$_GET['XLS']]['showLimit']['message'])
        && isset($tabXlsFields[$_GET['XLS']]['showLimit']['limitProfil'])
        && !empty($tabXlsFields[$_GET['XLS']]['showLimit']['limitProfil']) && array_key_exists($_SESSION['Droit'], $tabXlsFields[$_GET['XLS']]['showLimit']['limitProfil']) == true){ ?>
        <div class="mb-2 text-center" style="color: #E74A3B;">
            <?php
        if(isset($_GET['XLS']) && (strpos($_GET['XLS'], 'mois_') !== false)){
            $message = $tabXlsFields[$_GET['XLS']]['showLimit']['message'];

        } else {
            if(isset($tabXlsFields[$_GET['XLS']]['showLimit']['limitProfil'][$_SESSION['Droit']])){
                $show_limit = $tabXlsFields[$_GET['XLS']]['showLimit']['limitProfil'][$_SESSION['Droit']];
                $message = $tabXlsFields[$_GET['XLS']]['showLimit']['message'].' '.$show_limit.' '.ucfirst(strftime("%B %Y"));
            }
            }

            ?>
            <b><i><?php echo $message; ?></i></b>
        </div>
    <?php }
} ?>
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
<?php
// Add scroll TOP to datatable
$wrapper ='';
$fixed = '';
if(isset($tabXlsFields[$_GET['XLS']]['scrollOnTop']) && $tabXlsFields[$_GET['XLS']]['scrollOnTop'] == 1) {
    $fixed = "fixed"; ?>
<?php } ?>
<div class="wrapper1 <?php echo $fixed?>">
    <div class="div1"></div>
</div>
<div class="table-responsive wrapper2">
    <div class="div2" style="<?php echo (isset($tabXlsFields[$_GET['XLS']]['custom_list_style']) && $nbTotalPage[0]->total >= 5 ) ?$tabXlsFields[$_GET['XLS']]['custom_list_style']:'';?>">

        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Affichage des noms de colonnes ----------------------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
            <thead <?php if(isset($tabXlsFields[$_GET['XLS']]['stickyHeader'])&& $tabXlsFields[$_GET['XLS']]['stickyHeader'] == 1)
            { echo 'style="position: sticky;top: 0;position: -webkit-sticky;"';}?>>
            <tr>
                <?php if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'] == 1) { ?>
                    <th class="th_img_show"></th>
                <?php } ?>

                <?php
                $nbCol = '';
                $col = array();
                $label = array();
                $type = array();
                $champsTable = array();
                foreach ($champs as $key => $v) {
                    if (isset($v['liste']) && $v['liste'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                        array_push($col, $key);
                        if (isset($v['type'])) {
                            array_push($type, $v['type']);
                        }
                        $champsTable[$key] = $v;
                        $nbCol++;

                    }
                    if (isset($v['bdd_value'])) {
                        array_push($label, $v['bdd_value']);
                        $nbCol++;
                    }
                }


                if (isset($other_champ) && $other_champ != NULL) {

                    foreach ($other_champ as $key => $v) {
                        if (isset($v['liste']) && $v['liste'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                            if (isset($v['type'])) {
                                array_push($type, $v['type']);
                            }
                            $champsTable[$key] = $v;
                            if (isset($v['after_champs'])) {
                                $position = array_search($v['after_champs'], $col);
                                $old_position = array_search($key, array_keys($champsTable));
                                array_splice($col, $position + 1, 0, $key);
                                $p1 = array_splice($champsTable, $old_position, 1);
                                $p2 = array_splice($champsTable, 0, $position + 1);
                                $champsTable = array_merge($p2, $p1, $champsTable);
                            } else {
                                array_push($col, $key);
                            }

                            $nbCol++;
                        }
                        if (isset($v['bdd_value'])) {
                            array_push($label, $v['bdd_value']);
                            $nbCol++;
                        }
                    }
                }

                if (isset($champsTable) && !empty($champsTable)) {
                    foreach ($champsTable AS $key => $v) { ?>
                        <th style="color: #222222;"  <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-col"' : ''?>>
                            <div style="display: flex;">
                                <div style="width: max-content;flex:1; margin-right: 10px;">

                                    <?php if (isset($v['liste_tri']) && $v['liste_tri'] == 1) { ?>
                                        <a class=""
                                           style="color: #222222;<?php if (isset($v['bdd_table_t']) && isset($_SESSION[$_GET['XLS']]['champ']) && $_SESSION[$_GET['XLS']]['champ'] == $v['bdd_table_t']) {
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
                                           }; ?>&page=<?php echo htmlspecialchars($pageCurrent); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&filtre=1"><?php echo htmlspecialchars($v['nom']); ?></a>
                                        <?php if (isset($v['text_info_bulle'])) { ?><a href="javascript:void(0)"
                                                                                       data-toggle="popover"
                                                                                       data-html="true"
                                                                                       data-placement="right"
                                                                                       data-content="<?php echo $v['text_info_bulle']; ?>" >
                                                <i class="fas fa-info-circle"></i></a><?php } ?>
                                    <?php } else { ?>
                                        <?php echo htmlspecialchars($v['nom']); ?>
                                        <?php if (isset($v['text_info_bulle'])) { ?><a href="javascript:void(0)"
                                                                                       data-toggle="popover"
                                                                                       data-html="true"
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
                    <?php }
                }
                ?>

                <?php if (isset($custom_list_edit) && $custom_list_edit != null) {
                    foreach ($custom_list_edit as $custom_list_editKey => $custom_list_editVal) {
                        ?>
                        <th class="th_img_show"></th>
                    <?php } ?>
                <?php } ?>

            </tr>
            </thead>
            <?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Affichage des entrées -------------------------------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/

            ?>
            <tbody>

            <?php

            if (!empty($donnees)) {
                foreach ($donnees as $donnee): ?>
                    <tr>
                        <?php if (isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['lecture'] == 1) { ?>
                            <td><a class="collapse-item"
                                   href="index.php?p=<?php echo htmlspecialchars($confXLSEdit); ?><?php foreach ($get_filtre as $get_filtre_key => $get_filtre_val) {
                                       echo '&' . htmlspecialchars($get_filtre_key) . '=' . htmlspecialchars($get_filtre_val);
                                   } ?>&id=<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&collapsId=<?php if (isset($_GET['collapsId'])) {
                                       echo htmlspecialchars($_GET['collapsId']);
                                   } ?>"><img class="imgshow" src="image/show.png" alt="modifier"/></a></td>
                        <?php } ?>


                        <?php
                        $enabled = 0;
                        if (isset($champsTable) && !empty($champsTable)) {
                            foreach ($champsTable as $key => $v):
                                //Désactiver les champs si on a des conditions
                                $disableChamps = '';
                                if (isset($disable_on_conditions) && isset($disable_on_conditions[$donnee->id.'_'.$key]) && in_array(true, $disable_on_conditions[$donnee->id.'_'.$key])) {
                                    $disableChamps = 'disabled';
                                }
                                if (isset($v['float_delim']) && !empty($v['float_delim'])) {
                                    $donnee->{$key} = str_replace('.', $v['float_delim'], $donnee->{$key});
                                }

                                if (isset($v['type']) && $v['type'] == "list_sql" && isset($v['liste']) && $v['liste'] == 1 && isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                                    if (isset($v['profilChamps'][$_SESSION['Droit']]['modification']) && $v['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && isset($v['autocomplete']) && $v['autocomplete'] == 0 && isset($v['editable_list']) && $v['editable_list'] == 1) { ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        }
                                        if(isset($donnee->old_value) && isset($donnee->old_value->{'old_'.$key}) && $donnee->old_value->{'old_'.$key} != null
                                            && ($donnee->{$key} ==null || $donnee->{$key} == '0')){
                                            echo 'background-color : #d0daf7d9;';
                                        }
                                        ?>" <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-column"' : ''?>>
                                            <select style="<?php if (isset($v['liste_style'])) {
                                                        echo htmlspecialchars($v['liste_style']);
                                                    } ?>" class="form-control selectAjax "
                                                    id="<?php echo htmlspecialchars($v['bdd_table_t']); ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    onfocus="selectFromListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($v['bdd_table_t']); ?>', '<?php echo htmlspecialchars($key); ?>', '<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>', '<?php echo htmlspecialchars($donnee->{$key}); ?>')"
                                                    onchange="updateSelectFromListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($v['bdd_table_t']); ?>', '<?php echo htmlspecialchars($key); ?>', '<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>',this.value, '<?php echo htmlspecialchars(json_encode($updateFieldsOnchange), ENT_QUOTES, 'UTF-8');?>')"
                                                <?php if (isset($disableChamps) && $disableChamps != '') {
                                                    echo $disableChamps;
                                                } ?>>
                                                <?php if(isset($v['placeholder']) && $v['placeholder'] ==1 && ($donnee->{$key} == NULL || $donnee->{$key} == '0') &&
                                                    isset($donnee->old_value) && !empty($donnee->old_value)
                                                    && isset($donnee->old_value->{'old_'.$key}) && $donnee->old_value->{'old_'.$key} != NULL
                                                ) {?>
                                                    <option class="oldNoteValue" selected disabled hidden>
                                                        <?php $donneeVal = str_replace('.',',',$donnee->old_value->{'old_'.$key});
                                                        echo htmlspecialchars($donneeVal) ?>
                                                    </option>
                                                <?php } else if(isset($donnee->{$v['bdd_table_t']}) && $donnee->{$v['bdd_table_t']} != NULL) { ?>
                                                    <option value="<?php echo htmlspecialchars($donnee->{$key}); ?>"
                                                            id="<?php echo htmlspecialchars($v['bdd_table_t']); ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>_span"
                                                            class="valueFromAjax" hidden>
                                                        <?php
                                                        $donneeVal = str_replace('.',',',$donnee->{$v['bdd_table_t']});
                                                        echo htmlspecialchars($donneeVal) ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    <?php } else if (isset($v['profilChamps'][$_SESSION['Droit']]['modification']) && $v['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && isset($v['autocomplete']) && $v['autocomplete'] == 1 && isset($v['editable_list']) && $v['editable_list'] == 1) { ?>

                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>" <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-column"' : ''?>>
                                            <input type="text"
                                                   style="<?php if (isset($v['liste_style'])) {
                                                       echo htmlspecialchars($v['liste_style']);
                                                   } ?>"
                                                   id="inputItem<?php echo $v['bdd_table_t'] ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                   name="<?php echo $v['bdd_table_t'] ?>"
                                                   value="<?php echo htmlspecialchars($donnee->{$v['bdd_table_t']}) ?>"
                                                   minlength="<?php echo $v['taille_min'] ?>"
                                                   maxlength="<?php echo $v['taille_max'] ?>"
                                                   class="form-control autocompPrincipal ui-autocomplete-input"
                                                   data-conf="<?php echo $v['bdd_table'] ?>"
                                                   data-champs="<?php echo $v['bdd_value'] ?>"
                                                   data-confname="<?php echo htmlspecialchars($tabXlsFields[$_GET['XLS']]['bdd_table']); ?>"
                                                   data-alias="<?php echo $v['bdd_table_t'] ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                   data-key="<?php echo htmlspecialchars($key); ?>"
                                                   autocomplete="off"
                                                   onchange="updateSelectFromListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>',
                                                           '<?php echo htmlspecialchars($v['bdd_table_t']); ?>',
                                                           '<?php echo htmlspecialchars($key); ?>',
                                                           '<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>',
                                                           $('#labelhidden<?php echo $v['bdd_table_t'] ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>').val())"
                                                <?php if (isset($disableChamps) && $disableChamps != '') {
                                                    echo $disableChamps;
                                                } ?>>
                                            <input type="hidden" name="item<?php echo $v['bdd_table_t'] ?>"
                                                   id="labelhidden<?php echo $v['bdd_table_t'] ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                   value="">
                                        </td>
                                    <?php } elseif (isset($v['profilChamps'][$_SESSION['Droit']]['modification']) && $v['profilChamps'][$_SESSION['Droit']]['modification'] == 1 && !isset($v['autocomplete'])  && isset($v['editable_list']) && $v['editable_list'] == 1 && $v['type_input'] == "textarea_jointure") { ?>
                                        <td style="<?php if (isset($v['liste_style'])) {
                                            echo htmlspecialchars($v['liste_style']);
                                        } ?>" <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-column"' : ''?>>
                                            <textarea
                                                    style="<?php if (isset($v['liste_style'])) {
                                                        echo htmlspecialchars($v['liste_style']);
                                                    } ?>"
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    onchange="updateListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                            <?php if (isset($disableChamps) && $disableChamps != '') {
                                                echo $disableChamps;
                                            } ?>><?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['champs'][$key]['bdd_table_t']}); ?></textarea>
                                        </td>

                                    <?php } else {
                                        $value = $donnee->{$v['bdd_table_t']};
                                        $style = "";
                                        if(isset($donnee->old_value) && isset($donnee->old_value->{'old_'.$key}) && $donnee->old_value->{'old_'.$key} != null
                                            && ($donnee->{$key} ==null || $donnee->{$key} == '0')){
                                            $value = $donnee->old_value->{'old_'.$key};
                                            $style = 'background-color : #d0daf7d9;';
                                        }
                                        ?>
                                        <td <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ?
                                            'class= "sticky-column"' : ''?>
                                        style="<?php echo $style ?>">
                                            <div style="<?php if (isset($v['liste_style'])) {
                                                echo htmlspecialchars($v['liste_style']);
                                            } ?>"><?php echo htmlspecialchars($value) ?></div>
                                        </td>
                                    <?php } ?>


                                <?php }

                                elseif (isset($v['liste']) && $v['liste'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
//                                    var_dump(isset($v['editable_list']) && $v['editable_list']);
                                    if (isset($v['editable_list']) && $v['editable_list'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['modification'] == 1) {

                                        $placeholder = "";
                                        if (!empty($donnee->old_value)) {
                                            if(isset($donnee->old_value->{$key})){
                                                $placeholder = $donnee->old_value->{$key};
                                            }
                                        }
                                        switch ($v['type_input']) :
                                            case "text":
                                                ?>
                                                <td style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']); } ?>" <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-column"' : ''?>>

                                                    <input
                                                            style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']);} ?>"
                                                            class="form-control"
                                                            minlength="<?php echo $v['taille_min'] ?>"
                                                            maxlength="<?php echo $v['taille_max'] ?>"
                                                            type="<?php echo $v['type_input'] ?>"
                                                            id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            name="<?php echo htmlspecialchars($key); ?>"
                                                            onchange="updateListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                                            value="<?php echo htmlspecialchars($donnee->{$key}) ?>"
                                                        <?php if (isset($disableChamps) && $disableChamps != '') { echo $disableChamps;} ?>>
                                                </td> <?php
                                                break;
                                            case "textarea":
                                                ?>
                                                <td style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']); } ?>" <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-column"' : ''?>>
                                            <textarea
                                                    style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']); } ?>"
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    onchange="updateListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                                    <?php if (isset($disableChamps) && $disableChamps != '') { echo $disableChamps; } ?>><?php echo htmlspecialchars($donnee->{$key}) ?></textarea>
                                                </td> <?php
                                                break;
                                            case "date":
                                                ?>
                                                <td style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']); } ?>" <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-column"' : ''?>>
                                                    <input
                                                            style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']); } ?>"
                                                            class="form-control"
                                                            minlength="<?php echo $v['taille_min'] ?>"
                                                            maxlength="<?php echo $v['taille_max'] ?>"
                                                            type="<?php echo $v['type_input'] ?>"
                                                            id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            name="<?php echo htmlspecialchars($key); ?>"
                                                            value="<?php echo htmlspecialchars($donnee->{$key}) ?>"
                                                            onchange="updateListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                                        <?php if (isset($disableChamps) && $disableChamps != '') { echo $disableChamps; } ?> >
                                                </td> <?php
                                                break;
                                            case "datetime":
                                                $fulldate = $donnee->{$key};
                                                $date = explode(' ', $fulldate)[0];
                                                $time = explode(' ', $fulldate)[1];
                                                $heur = explode(":", $time);
                                                $heurFormate = $heur[0] . ":" . $heur[1];
                                                ?>
                                                <td style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']); } ?>" <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-column"' : ''?>>
                                                    <input
                                                            style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']); } ?>"
                                                            class="form-control dateTime<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            minlength="<?php echo $v['taille_min'] ?>"
                                                            maxlength="<?php echo $v['taille_max'] ?>"
                                                            type="date"
                                                            id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            name="<?php echo htmlspecialchars($key); ?>_1_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            value="<?php echo $date ?>"
                                                            onchange="updateListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value,'datetime_2_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>')"
                                                        <?php if (isset($disableChamps) && $disableChamps != '') { echo $disableChamps;} ?>>
                                                    à
                                                    <input
                                                            style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']); } ?>"
                                                            class="form-control dateTime<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            minlength="<?php echo $v['taille_min'] ?>"
                                                            maxlength="<?php echo $v['taille_max'] ?>"
                                                            type="time"
                                                            id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            name="<?php echo htmlspecialchars($key); ?>_2_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            value="<?php echo $heurFormate ?>"
                                                            onchange="updateListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value,'datetime_1_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>')"
                                                        <?php if (isset($disableChamps) && $disableChamps != '') { echo $disableChamps;} ?>>
                                                </td> <?php
                                                break;
                                            case "number":?>
                                                <td style="<?php if (isset($v['liste_style'])) { echo htmlspecialchars($v['liste_style']); } ?>"  class="<?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'sticky-column' : '' ; ?><?php echo isset($donnee->{$key}) && $donnee->{$key} == -1 ? ' noFillValue' : ''?>">
                                                    <?php
                                                    $class='onCalcul ';
                                                    if(isset($v['compareWith']) && !empty($v['compareWith']) && isset($v['compareWith']['champ']) &&
                                                        isset($v['compareWith']['condition']) && $v['compareWith']['condition'] == true
                                                        && isset($donnee->{'calcul_'.$key}) && $donnee->{'calcul_' . $key} == $donnee->{$v['compareWith']['champ']}
                                                        && ($donnee->{$key} == null || $donnee->{$key} == '0')){
                                                        $class .= 'calculated';
                                                        $placeholder = $donnee->{'calcul_'.$key};
                                                }
                                                     if($donnee->{$key} != -1){ ?>
                                                    <input
                                                            placeholder="<?php echo $placeholder ?>"
                                                            style="<?php if (isset($v['liste_style'])) {echo htmlspecialchars($v['liste_style']);} ?>"
                                                            class="form-control <?php echo $class ?>"
                                                            min="<?php echo $v['taille_min'] ?>"
                                                            max="<?php echo $v['taille_max'] ?>"
                                                            type="<?php echo $v['type_input'] ?>"
                                                            id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            name="<?php echo htmlspecialchars($key); ?>"
                                                            value="<?php echo !isset($donnee->{'calcul_'.$key}) ? htmlspecialchars($donnee->{$key}) : '' ?>"
                                                            data-onchange ="<?php echo (isset($v['onChangeUpdate']) && !empty($v['onChangeUpdate'])) ? $v['onChangeUpdate'] :''; ?>"
                                                            onchange="updateListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                                        <?php if (isset($disableChamps) && $disableChamps != '') { echo $disableChamps; } ?>
                                                        <?php if(isset($v['profilChamps'][$_SESSION['Droit']]['enableOnRoles']) && !in_array(intval($donnee->posteId), $v['profilChamps'][$_SESSION['Droit']]['enableOnRoles'])){ echo "disabled"; }?>>
                                                         <?php } ?>
                                                </td> <?php

                                                    break;
                                            case "phone":
                                            case "email":
                                            case "url":
                                                ?>
                                                <td style="<?php if (isset($v['liste_style'])) {echo htmlspecialchars($v['liste_style']);} ?>" <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-column"' : ''?>>
                                                    <input
                                                            style="<?php if (isset($v['liste_style'])) {echo htmlspecialchars($v['liste_style']);} ?>"
                                                            class="form-control"
                                                            minlength="<?php echo $v['taille_min'] ?>"
                                                            maxlength="<?php echo $v['taille_max'] ?>"
                                                            type="<?php echo $v['type_input'] ?>"
                                                            id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                            name="<?php echo htmlspecialchars($key); ?>"
                                                            value="<?php echo htmlspecialchars($donnee->{$key}) ?>"
                                                            onchange="updateListItem('<?php echo htmlspecialchars($_GET['XLS']); ?>','<?php echo htmlspecialchars($key); ?>','<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>','<?php echo $v['type_input'] ?>',this.value)"
                                                        <?php if (isset($disableChamps) && $disableChamps != '') {echo $disableChamps;} ?>>
                                                </td> <?php
                                                break;
                                            default:
                                                break;
                                        endswitch;
                                    }
                                    else {?>
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
                                            } ?>" <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'class= "sticky-column"' : ''?>><?php echo htmlspecialchars($date) ?></td>
                                        <?php }
                                        elseif (isset($v['type_input']) && $v['type_input'] == "number" && isset($v['calcul']['type_calcul']) && $v['calcul']['type_calcul'] == "compare" && isset($v['liste']) && $v['liste'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {?>
                                            <td class="<?php echo htmlspecialchars($_GET['XLS']).'_'.htmlspecialchars($key).'_'. htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?> <?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'sticky-column' : ''?>"
                                                style="<?php if (isset($v['liste_style']) && $v['liste_style'] != "") {
                                                    echo htmlspecialchars($v['liste_style']);
                                                } else {
                                                    echo 'width:110px;text-align:center;';
                                                } ?>">
                                                <div id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>" style="<?php if (isset($v['liste_style'])) {echo htmlspecialchars($v['liste_style']);} ?>">
                                                    <?php echo $donnee->{$key} ?>
                                                </div>
                                            </td>
                                        <?php } else {
                                            ?>
                                            <td class="<?php echo htmlspecialchars($_GET['XLS']).'_'.htmlspecialchars($key).'_'.htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?><?php echo isset($donnee->{$key}) && $donnee->{$key} == -1 ? ' noFillValue' : ''?><?php echo (isset($v['fixed_on_scroll']) && $v['fixed_on_scroll'] == 1) ? 'sticky-column' : ''?>">
                                                <?php
                                                $class='onCalcul ';
                                                if(isset($v['compareWith']) && !empty($v['compareWith']) && isset($v['compareWith']['champ']) &&
                                                    isset($v['compareWith']['condition']) && $v['compareWith']['condition'] == true
                                                    && isset($donnee->{'calcul_'.$key})){
                                                    $class .= 'calculated';
                                                }
                                                if(isset($donnee->{'exist_'.$key})) {
                                                    $class .= ' last_value';
                                                }
                                                ?>
                                                <?php if(isset($donnee->{$key}) && $donnee->{$key} != -1){?>
                                                <div  id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnee->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                      class= "<?php echo $class; ?>"style="<?php if (isset($v['liste_style'])) {
                                                    echo htmlspecialchars($v['liste_style']);
                                                } ?>">
                                                    <?php echo  nl2br(htmlspecialchars($donnee->{$key})) ?></div>
                                                <?php } ?>
                                            </td>
                                        <?php }
                                    }
                                }
                            endforeach;
                        }

                        if (isset($custom_list_edit) && $custom_list_edit != null) {

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
                    </tr> <?php
                endforeach;
            } else {
                ?>
                <td colspan="<?php echo htmlspecialchars($nbCol); ?>">Aucun Résultat trouvé.</td>
            <?php }
            ?>
            </tbody>
        </table></div>
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

                <?php if (0) { ?>
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
                    <li class="list-group-item"><strong>Requête COUNT
                            :</strong> <?php echo htmlspecialchars($reqCount); ?> </li>
                    <?php if (isset($reqOptionsP) && !empty($reqOptionsP)) {
                        foreach ($reqOptionsP as $reqOptionKey => $reqOptionVal) { ?>
                            <li class="list-group-item"><strong>Requête Options
                                    :</strong> <?php echo htmlspecialchars($reqOptionVal); ?> </li>
                        <?php }
                    } ?>
                <?php } ?>
                <?php
                if (isset($_SESSION['debug']['sqlAll']) && is_array($_SESSION['debug']['sqlAll'])) {
                    foreach ($_SESSION['debug']['sqlAll'] as $kads => $vads) {
                        print('<li class="list-group-item"><strong>' . htmlspecialchars($vads["what"]) . ' [ ' . (isset($vads["time"]) ? number_format($vads["time"], 3, ".", " ") : "-") . ' ] </strong> ' . htmlspecialchars($vads["query"]) . '</li>');
                    }
                    $_SESSION['debug']['sqlAll'] = array();
                }
                ?>
            </ul>
        </div>
    </footer>
<?php } ?>
