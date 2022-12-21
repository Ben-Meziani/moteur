<?php setlocale(LC_TIME, $LocalTime);
$confXLS = 'Users.listUsers';
$confXLSEdit = 'Users.editUser';
$confXLSSwitch = 'Users.switchSession';
?>

<script type="text/javascript">

    var id_user = <?php echo $_GET['id'];?>;

</script>

<br/>
<?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Titre de la page, lien de l'historique, alertes et messages de confirmation -------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
<?php if (isset($_SESSION['Droit'])) { ?>
    <a id="lienModal" class="lien_add_user" href="#" data-ajax="<?php echo htmlspecialchars($_GET['id']); ?>"
       data-feuille="<?php echo htmlspecialchars($nom_feuille); ?>" data-toggle="modal"
       data-target=".bd-example-modal-lg">Détail & historique</a>
<?php } ?>
<?php if (isset($_SESSION['UserID']) && $_SESSION['Droit']) { ?>

    <?php if (!empty($errors) && $errors != null): ?>
        <div class="alert alert-danger">
            <p>Vous n'avez pas rempli le formulaire correctement :</p>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['succes']) && $_SESSION['succes'] == 1) { ?>
        <div id="bloc_info" class="alert alert-success" role="alert">
            L'utilisateur a été modifié avec succès.
        </div>
        <?php
        unset($_SESSION['succes']);
    } ?>
    <?php if (isset($_SESSION['fail']) && $_SESSION['fail'] == 1) { ?>
        <div id="bloc_info" class="alert alert-dark" role="alert">
            Aucune modification faite.
        </div>
        <?php
        unset($_SESSION['fail']);
    } ?>
    <?php if (isset($_SESSION['succes']) && $_SESSION['succes'] == 10) { ?>
        <div id="bloc_info" class="alert alert-success" role="alert">
            Affectation du résponsable reussie.
        </div>
        <?php
        unset($_SESSION['succes']);
    } ?>
    <?php if (isset($_SESSION['succes']) && $_SESSION['succes'] == 11) { ?>
        <div id="bloc_info" class="alert alert-warning" role="alert">
            Le profil est déjà affecté à un autre résponsable.
        </div>
        <?php
        unset($_SESSION['succes']);
    } ?>
    <?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Début du Formulaire ---------------------------------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
    <form method="post" enctype="multipart/form-data"
          action="?p=<?php echo htmlspecialchars($confXLSEdit); ?>&id=<?php echo htmlspecialchars($_GET['id']); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&collapsId=<?php echo htmlspecialchars($_GET['collapsId']); ?>">
        <hr>
        <?php
        //-- Parcours de la conf  ---------------------------------------------------------------------------------------------------------------------------------------------------
        foreach ($champs as $key => $v) {


            if($v['type_input'] == 'categorie'){
                if($v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1){?>
                    <div class=""><h5 class="title"><span><?php echo htmlspecialchars($v['nom']); ?></span></h5></div>
                <?php }
            }



            if($v['type_input'] == 'sous_categorie'){
                if($v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1){?>
                    <div class=""><h5 class="title2"><span><?php echo htmlspecialchars($v['nom']); ?></span></h5></div>
                <?php }
            }


            if (isset($v['liste_detail']) && $v['liste_detail'] == 1) {
                if ($v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                    if (isset($v['type']) && $v['type'] == "list_sql") {
                        if ($v['autocomplete'] == 1) {
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- Autocomplétion avec parcours de l'objet errors  ------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                            ?>
                            <?php
                            if (!empty($errors) && $errors != null) {
                                foreach ($errors->errors as $error => $e):
                                    if ($error != null && $key == $error) {
                                        if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {
                                            //-- Mode Disabled activé ---------------------------------------------
                                            if (isset($_POST[$v['bdd_value']])) {
                                                echo $form->inputWithIdAutocompEditPostD('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $_POST[$v['bdd_table_t']], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                            } else {
                                                echo $form->inputWithIdAutocompD('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                            }


                                        } //-- Mode Disabled désactivé -------------------------------------------
                                        else {
                                            if (isset($_POST[$v['bdd_value']])) {
                                                echo $form->inputWithIdAutocompEditPost('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $_POST[$v['bdd_table_t']], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                            } else {
                                                echo $form->inputWithIdAutocomp('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                            }
                                            ?>
                                            <hr><?php

                                        }

                                    }
                                endforeach;
                            } else {
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- Autocomplétion sans l'objet errors  ------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {
                                    if (isset($_POST[$v['bdd_value']])) {
                                        echo $form->inputwithIdAutocompEditPostD('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $_POST[$v['bdd_value']], $v['nom'] . ' :', $v['bdd_table_t'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                        ?><hr><?php
                                    } else {
                                        echo $form->inputwithIdAutocompD('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $v['nom'] . ' :', $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                        ?><hr><?php
                                    }


                                } else {
                                    if (isset($_POST[$v['bdd_value']])) {
                                        echo $form->inputWithIdAutocompEditPost('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $_POST[$v['bdd_value']], $v['nom'] . ' :', $v['bdd_table_t'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                    } else {
                                        echo $form->inputWithIdAutocomp('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $v['nom'] . ' :', $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                    }
                                    ?>
                                    <hr><?php


                                }
                            }
                            ?>
                            <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {
                            } else {
                                if (isset($_POST[$v['bdd_value']])) { ?>
                                    <fieldset disabled>
                                        <div class="form-group champHidAutoComp">
                                            <label>Label :</label>
                                            <input disabled="true" class="form-control" type="text"
                                                   id="labelDisable<?php echo $v['bdd_table_t']; ?>"
                                                   value="<?php echo htmlspecialchars($_POST[$key]); ?>">
                                        </div>
                                    </fieldset>
                                    <input type="hidden" name="item<?php echo htmlspecialchars($key); ?>"
                                           id="labelhidden<?php echo $v['bdd_table_t']; ?>"
                                           value="<?php echo htmlspecialchars($_POST['item' . $key]); ?>">
                                    <hr>
                                <?php } else { ?>
                                    <fieldset disabled>
                                        <div class="form-group champHidAutoComp">
                                            <?php echo $form->inputWithId('labelDisable'.$v['bdd_table_t'], $v['bdd_table_t'], 'Label :', $v['taille_min'], $v['taille_max']);?>
                                        </div>
                                    </fieldset>
                                    <input type="hidden" name="item<?php echo htmlspecialchars($key); ?>"
                                           id="labelhidden<?php echo $v['bdd_table_t']; ?>"
                                           value="<?php echo htmlspecialchars($donnees->{$key}); ?>"/>
                                <?php }
                            } ?>
                        <?php }
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- Select avec parcours de l'objet errors  --------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        else { ?>
                            <?php if (!empty($errors) && $errors != null) {
                                foreach ($errors->errors as $error => $e):
                                    if ($error != null && $key == $error) {
                                        ?>
                                        <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) { ?>
                                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                                   value="<?php echo htmlspecialchars($donnees->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"/>
                                        <?php } ?>
                                        <div class="form-group row"><label for="<?php echo htmlspecialchars($key); ?>"
                                                                           class="col-sm-2 col-form-label"><?php echo htmlspecialchars($v['nom']); ?><?php echo ' ' . $e; ?></label>
                                            <div class="col-sm-10"><select id="<?php echo htmlspecialchars($key); ?>"
                                                                           name="<?php echo htmlspecialchars($key); ?>"
                                                                           class="form-control" <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {
                                                    echo 'disabled="disabled"';
                                                } ?>>

                                                    <?php if (isset($v['obligatoire']) && $v['obligatoire'] == 1) {
                                                    } else { ?>
                                                        <option value="0"></option>
                                                    <?php } ?>

                                                    <?php

                                                    if ($key == 'responsable') {

                                                        foreach ($listResponsable as $o_key => $o_value) { ?>
                                                            <option value="<?php echo $o_value->{$v['bdd_id']}; ?>" <?php if ($o_value->{$v['bdd_id']} == $donnees->{$key}) {
                                                                echo 'selected="selected"';
                                                            } ?>><?php echo htmlspecialchars($o_value->{$v['bdd_value']}); ?></option>
                                                            <?php
                                                        }

                                                    } else {
                                                        foreach ($options as $o => $oo) {
                                                            if ($key == $o) {
                                                                foreach ($oo as $o_key => $o_value) { ?>
                                                                    <option value="<?php echo $o_value->{$v['bdd_id']}; ?>" <?php if ($o_value->{$v['bdd_id']} == $donnees->{$key}) {
                                                                        echo 'selected="selected"';
                                                                    } ?>><?php echo htmlspecialchars($o_value->{$v['bdd_table_t']}); ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                </select></div>
                                        </div>
                                        <hr>
                                    <?php }
                                endforeach;
                            }
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- Select sans l'objet errors  --------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                            else { ?>
                                <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) { ?>
                                    <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                           value="<?php echo htmlspecialchars($donnees->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"/>
                                <?php } ?>
                                <div class="form-group row"><label for="<?php echo htmlspecialchars($key); ?>"
                                                                   class="col-sm-2 col-form-label"><?php echo htmlspecialchars($v['nom']); ?>
                                        :</label>
                                    <div class="col-sm-10"><select id="<?php echo htmlspecialchars($key); ?>"
                                                                   name="<?php echo htmlspecialchars($key); ?>"
                                                                   class="form-control" <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {
                                            echo 'disabled="disabled"';
                                        } ?>>

                                            <?php if (isset($v['obligatoire']) && $v['obligatoire'] == 1) {
                                            } else { ?>
                                                <option value="0"></option>
                                            <?php } ?>
                                            <?php

                                            if ($key == 'responsable') {

                                                foreach ($listResponsable as $o_key => $o_value) { ?>
                                                    <option value="<?php echo $o_value->{$v['bdd_id']}; ?>" <?php if ($o_value->{$v['bdd_id']} == $donnees->{$key}) {
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo htmlspecialchars($o_value->{$v['bdd_value']}); ?></option>
                                                    <?php
                                                }

                                            } else {

                                                foreach ($options as $o => $oo) {
                                                    if ($key == $o) {
                                                        foreach ($oo as $o_key => $o_value) { ?>
                                                            <option value="<?php echo $o_value->{$v['bdd_id']}; ?>" <?php if ($o_value->{$v['bdd_id']} == $donnees->{$key}) {
                                                                echo 'selected="selected"';
                                                            } ?>><?php echo htmlspecialchars($o_value->{$v['bdd_table_t']}); ?></option>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            }


                                            ?>
                                        </select></div>
                                </div>
                                <hr>
                            <?php } ?>
                        <?php } ?>
                    <?php } else {
                        if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {
//-- input password sans l'objet errors  ---------------------------------------------------------------------------------------------------------------------------------------------------------
                            if ($key == 'password') {
                                ?>
                                <div class="form-group row"><label class="col-sm-2 col-form-label" for="password">Nouveau <?php echo htmlspecialchars($v['nom']); ?>
                                        :</label>
                                    <div class="col-sm-10"><input id="password" type="text" name="password"
                                                                  minlength="<?php echo htmlspecialchars($v['taille_min']); ?>" maxlength="<?php echo htmlspecialchars($v['taille_max']); ?>"
                                                                  class="form-control" disabled="disabled"/></div>
                                </div>
                                <hr>
                            <?php } else {
//-- input DateTime sans l'objet errors ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                if ($v['type_input'] == "datetime") {
                                    ?>
                                    <div>
                                        <?php
                                        if (isset($donnees->{$key}) && !empty($donnees->{$key}) && $donnees->{$key} != null) {
                                            $resultat_date = explode('-', $donnees->{$key});
                                            $resultat_heur = explode(':', $resultat_date[2]);
                                            $sep = explode(' ', $resultat_heur[0]);
                                            $dateFormate = $resultat_date[0] . "-" . $resultat_date[1] . "-" . $sep[0];
                                            $heurFormate = $sep[1] . ":" . $resultat_heur[2];
                                            ?>
                                            <div class="form-group row"><label class="col-sm-2 col-form-label"
                                                                               for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?>
                                                : </label>
                                            <div class="col-sm-10"><input id="<?php echo htmlspecialchars($key); ?>"
                                                                          type="date"
                                                                          name="<?php echo htmlspecialchars($key); ?>_date1"
                                                                          value="<?php echo htmlspecialchars($dateFormate); ?>"
                                                                          class="input_date input_date_disabled"
                                                                          disabled="disabled"/> à <input
                                                        id="<?php echo htmlspecialchars($key); ?>" type="time"
                                                        name="<?php echo htmlspecialchars($key); ?>_date2"
                                                        value="<?php echo htmlspecialchars($heurFormate); ?>"
                                                        class="input_number input_date_disabled" disabled="disabled"/>
                                            </div></div><?php
                                        } else {

//-- Input Date sans l'objet errors Disabled ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                            ?>
                                            <div class="form-group row">   <label class="col-sm-2 col-form-label"
                                                                                  for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?>
                                                :</label>
                                            <div class="col-sm-10"><input id="<?php echo htmlspecialchars($key); ?>"
                                                                          type="date"
                                                                          name="<?php echo htmlspecialchars($key); ?>_date1"
                                                                          class="input_date"/> <label> à </label> <input
                                                        id="<?php echo htmlspecialchars($key); ?>" type="time"
                                                        name="<?php echo htmlspecialchars($key); ?>_date2"
                                                        class="input_number input_date_disabled" disabled="disabled"/>
                                            </div></div><?php
                                            ?>
                                            <hr><?php
                                        } ?>
                                    </div>
                                <?php } else {
                                    if ($v['type_input'] == "date") {
                                        echo $form->inputD(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' :', ["type" => "date"]);
                                    } else {
//-- Autre input Disabled sans l'objet errors  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                        echo $form->inputD(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' :', ['type' => $v['type_input']]);
                                    }
                                }
                                ?>
                                <hr><?php
                            }
                        } else {
                            if (!empty($errors) && $errors != null) {
                                foreach ($errors->errors as $error => $e):
                                    if ($error != null && $key == $error) {
//-- input password avec l'objet errors  ---------------------------------------------------------------------------------------------------------------------------------------------------------
                                        if ($v['type_input'] == 'password') {
                                            ?>
                                            <div class="form-group row"><label for="password"
                                                                               class="col-sm-2 col-form-label">Nouveau <?php echo htmlspecialchars($v['nom']); ?><?php echo ' ' . $e; ?></label>
                                                <div class="col-sm-10"><input id="password" type="text" name="password"
                                                                              minlength="<?php echo htmlspecialchars($v['taille_min']); ?>" maxlength="<?php echo htmlspecialchars($v['taille_max']); ?>"
                                                                              class="form-control"/></div>
                                            </div>
                                            <hr>
                                        <?php }
//-- input number avec l'objet errors  ---------------------------------------------------------------------------------------------------------------------------------------------------------
                                        elseif($v['type_input'] == "number"){
                                            echo $form->inputnumber(htmlspecialchars($key), htmlspecialchars($v['nom']).' '. $e, $v['taille_min'], $v['taille_max'], ['type' => $v['type_input']]);
                                            ?><hr><?php
                                        }
//-- input DateTime avec l'objet errors ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                        elseif ($v['type_input'] == "datetime"){?>
                                            <div>
                                                <?php
                                                if (isset($donnees->{$key}) && !empty($donnees->{$key})) {
                                                    $resultat_date = explode('-', $donnees->{$key});
                                                    $resultat_heur = explode(':', $resultat_date[2]);
                                                    $sep = explode(' ', $resultat_heur[0]);
                                                    $dateFormate = $resultat_date[0] . "-" . $resultat_date[1] . "-" . $sep[0];
                                                    $heurFormate = $sep[1] . ":" . $resultat_heur[2];
                                                    ?>
                                                    <div class="form-group row"><label class="col-sm-2 col-form-label"
                                                                                       for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?>
                                                            : <?php echo ' ' . $e; ?></label>
                                                        <div class="col-sm-10"><input
                                                                    id="<?php echo htmlspecialchars($key); ?>" type="date"
                                                                    name="<?php echo htmlspecialchars($key); ?>_date1"
                                                                    value="<?php echo htmlspecialchars($dateFormate); ?>"
                                                                    class="input_date"/> à <input
                                                                    id="<?php echo htmlspecialchars($key); ?>" type="time"
                                                                    name="<?php echo htmlspecialchars($key); ?>_date2"
                                                                    value="<?php echo htmlspecialchars($heurFormate); ?>"
                                                                    class="input_number"/></div></div> <?php
                                                    ?>
                                                    <hr><?php
                                                } else {
                                                    ?>
                                                    <div class="form-group row"><label class="col-sm-2 col-form-label"
                                                                                       for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?>
                                                        : <?php echo ' ' . $e; ?></label>
                                                    <div class="col-sm-10"><input
                                                                id="<?php echo htmlspecialchars($key); ?>" type="date"
                                                                name="<?php echo htmlspecialchars($key); ?>_date1"
                                                                class="input_date"/> à <input
                                                                id="<?php echo htmlspecialchars($key); ?>" type="time"
                                                                name="<?php echo htmlspecialchars($key); ?>_date2"
                                                                class="input_number"/></div></div><?php
                                                    ?>
                                                    <hr><?php
                                                } ?>
                                            </div>
                                        <?php } else {
//-- Autre input avec l'objet errors ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                            echo $form->input(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' ' . $e, ['type' => $v['type_input'],'min' => $v['taille_min'], 'max' => $v['taille_max']]);
                                            ?>
                                            <hr><?php
                                        }
                                    }
                                endforeach;
                            } else {
                                if ($v['type_input'] == 'password') {
                                    ?>
                                    <div class="form-group row"><label for="password" class="col-sm-2 col-form-label">Nouveau <?php echo htmlspecialchars($v['nom']); ?>
                                            :</label>
                                        <div class="col-sm-10"><input id="password" type="text" name="password"
                                                                      minlength="<?php echo htmlspecialchars($v['taille_min']); ?>" maxlength="<?php echo htmlspecialchars($v['taille_max']); ?>"
                                                                      class="form-control"/></div>
                                    </div>
                                    <hr>
                                <?php } elseif ($v['type_input'] == "number") {
                                    echo $form->inputNumber(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' :', $v['taille_min'], $v['taille_max'], ['type' => $v['type_input']]);
                                    ?>
                                    <hr><?php
                                } elseif ($v['type_input'] == "datetime") {
                                    ?>
                                    <div>
                                        <?php
                                        if (isset($donnees->{$key}) && !empty($donnees->{$key})) {
                                            $resultat_date = explode('-', $donnees->{$key});
                                            $resultat_heur = explode(':', $resultat_date[2]);
                                            $sep = explode(' ', $resultat_heur[0]);
                                            $dateFormate = $resultat_date[0] . "-" . $resultat_date[1] . "-" . $sep[0];
                                            $heurFormate = $sep[1] . ":" . $resultat_heur[1];
                                            ?>
                                            <div class="form-group row"><label class="col-sm-2 col-form-label"
                                                                               for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?>
                                                : </label>
                                            <div class="col-sm-10"><input id="<?php echo htmlspecialchars($key); ?>"
                                                                          type="date"
                                                                          name="<?php echo htmlspecialchars($key); ?>_date1"
                                                                          value="<?php echo htmlspecialchars($dateFormate); ?>"
                                                                          class="input_date"/> à <input
                                                        id="<?php echo htmlspecialchars($key); ?>" type="time"
                                                        name="<?php echo htmlspecialchars($key); ?>_date2"
                                                        value="<?php echo htmlspecialchars($heurFormate); ?>"
                                                        class="input_number"/></div></div><?php
                                            ?>
                                            <hr><?php
                                        } else {
                                            ?>
                                            <div class="form-group row"><label class="col-sm-2 col-form-label"
                                                                               for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?>
                                                : </label>
                                            <div class="col-sm-10"><input id="<?php echo htmlspecialchars($key); ?>"
                                                                          type="date"
                                                                          name="<?php echo htmlspecialchars($key); ?>_date1"
                                                                          value="" class="input_date"/> à <input
                                                        id="<?php echo htmlspecialchars($key); ?>" type="time"
                                                        name="<?php echo htmlspecialchars($key); ?>_date2" value=""
                                                        class="input_number"/></div></div><?php
                                            ?>
                                            <hr><?php
                                        } ?>
                                    </div>
                                <?php } else {
                                    echo $form->input(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' :', ['type' => $v['type_input'],'min' => $v['taille_min'], 'max' => $v['taille_max']]);
                                    ?>
                                    <hr><?php
                                }
                            }
                        }
                    }
                }
            }

//-- Début de Input file --------------------------------------------------------------------------------------------------------------------------------------------------------

            if (isset($v['type_input']) && $v['type_input'] == "file" && isset($v['profilChamps'][$_SESSION['Droit']]['modification']) && $v['profilChamps'][$_SESSION['Droit']]['modification'] == 1) {

                // Permet de mettre à zero le nombre de fichier max pour le champ.
                if (!isset($nbFile[$key . '_nbFile']) || empty($nbFile[$key . '_nbFile'])) {
                    $nbFile[$key . '_nbFile'] = 0;
                } ?>

                <div id="content-wrapper">
                    <div class="wrapper">
                        <div class="sections file_<?php echo htmlspecialchars($key); ?>">
                            <label><?php echo htmlspecialchars($v['nom']) . ' :'; ?> <em
                                        class="notificationMax"></em></label>
                            <section class="active">
                                <div id="images" class="images">
                                    <input type="hidden" name="MAX_FILE_SIZE"
                                           value="<?php echo htmlspecialchars($v['taille_max']); ?>">

                                    <?php
                                    // File issue de la bdd ------------------------------------------------------------------------------------------------------------------------------------------------------
                                    ?>
                                    <?php if (isset($fileDonnees)) { ?>

                                        <div class="div_old_img">
                                            <?php foreach ($fileDonnees as $key_f => $val_f) {

                                                if ($val_f->champ == $key) {
                                                    $extEx = explode(".", $val_f->path);
                                                    $nomFile = substr($val_f->nom, 0, 8) . '.' . $extEx[1];
                                                    if ($extEx[1] == 'jpg') {
                                                        ?>
                                                        <div class="img imgdl"
                                                             style="background-image: url('<?php echo $v['cheminDossier'] . htmlspecialchars($val_f->path); ?>'); margin-right: 10px;"
                                                             rel=""><?php if (isset($v['profilChamps'][$_SESSION['Droit']]['suppression']) && $v['profilChamps'][$_SESSION['Droit']]['suppression'] == 1) { ?>
                                                                <a class="a_dl_file"
                                                                   href="<?php echo $v['cheminDossier'] . htmlspecialchars($val_f->path); ?>"
                                                                   download><span style="" class="span_delete dl">Télécharger</span>
                                                                </a> <?php } ?> <span
                                                                    data-id_item="<?php echo htmlspecialchars($val_f->id); ?>"
                                                                    data-confFile="<?php echo htmlspecialchars($val_f->champ); ?>"
                                                                    data-champFile="<?php echo htmlspecialchars($val_f->path); ?>"
                                                                    class="ico_poubelle" data-toggle="modal"
                                                                    data-target="#modalDeleteItem"><i
                                                                        class="far fa-fw fa-trash-alt"></i></span></div>
                                                    <?php } else {
                                                        $path = "../public/image/forfile/" . $extEx[1] . ".png"; ?>
                                                        <div class="img imgdl"
                                                             style="background-image: url('<?php echo htmlspecialchars($path); ?>'); margin-right: 10px;"
                                                             rel=""><?php if (isset($v['profilChamps'][$_SESSION['Droit']]['suppression']) && $v['profilChamps'][$_SESSION['Droit']]['suppression'] == 1) { ?>
                                                                <a class="a_dl_file"
                                                                   href="<?php echo $v['cheminDossier'] . htmlspecialchars($val_f->path); ?>"
                                                                   download><span style="" class="span_delete dl">Télécharger</span>
                                                                </a> <?php } ?><span
                                                                    class="nom_file"><?php echo htmlspecialchars($nomFile); ?></span>
                                                            <span data-toggle="modal" data-target="#modalDeleteItem"
                                                                  data-id_item="<?php echo htmlspecialchars($val_f->id); ?>"
                                                                  data-confFile="<?php echo htmlspecialchars($val_f->champ); ?>"
                                                                  data-champFile="<?php echo htmlspecialchars($val_f->path); ?>"
                                                                  class="ico_poubelle"><i
                                                                        class="far fa-fw fa-trash-alt"></i></span></div>
                                                    <?php }
                                                    $nbFile[$key . '_nbFile']++;
                                                }
                                            } ?>

                                        </div>
                                    <?php } ?>
                                    <?php
                                    // File avec l'objet error si il ya une erreur -----------------------------------------------------------------------------------------------------------------------------------
                                    ?>
                                    <?php if (!empty($errors) && $errors != null) { ?>
                                        <div class="div_new_img_errors">
                                            <?php foreach ($errors->errors as $error => $e):
                                                if ($error != null && $key == $error) {
                                                    if (isset($_POST[$key])) {
                                                        foreach ($_POST[$key] as $file => $f) {
                                                            foreach ($e as $eKey => $eVal) { ?>
                                                                <?php if ($eKey == $_POST[$key . '_Name'][$file] . '.' . $_POST[$key . '_ext'][$file] && $eVal != null) { ?>
                                                                    <div class="img imgdelete"
                                                                         style="background-image: url('<?php if ($_POST[$key . '_ext'][$file] != 'jpg') {
                                                                             echo '../public/image/forfile/' . $_POST[$key . '_ext'][$file] . '.png';
                                                                         } else {
                                                                             echo htmlspecialchars($f);
                                                                         } ?>'); margin-right: 10px;"
                                                                         rel="<?php echo htmlspecialchars($_POST[$key . '_Name'][$file]); ?>">
                                                                        <span class="span_delete span_delete_add">Supprimer</span>
                                                                        <input class="file_hidden" type="hidden"
                                                                               name="<?php echo htmlspecialchars($key); ?><?php if ($v['nb_file'] > 0) {
                                                                                   echo '[]';
                                                                               } ?>"
                                                                               value="<?php echo htmlspecialchars($_POST[$key . '_Name'][$file]); ?>"/>
                                                                        <input type="hidden"
                                                                               name="<?php echo htmlspecialchars($key . '_ext');
                                                                               if ($v['nb_file'] > 0) {
                                                                                   echo '[]';
                                                                               } ?>"
                                                                               value="<?php echo htmlspecialchars($_POST[$key . '_ext'][$file]); ?>"/>
                                                                        <input type="hidden"
                                                                               name="<?php echo htmlspecialchars($key . '_Name');
                                                                               if ($v['nb_file'] > 0) {
                                                                                   echo '[]';
                                                                               } ?>"
                                                                               value="<?php echo htmlspecialchars($_POST[$key . '_Name'][$file]); ?>"/>
                                                                        <?php if ($eKey == $_POST[$key . '_Name'][$file] . '.' . $_POST[$key . '_ext'][$file]) {
                                                                            $chaineCoupee = substr($_POST[$key . '_Name'][$file], 0, 10); ?>
                                                                            <span class="nom_file"><?php echo $eVal . '<br/>' . $chaineCoupee . '.' . $_POST[$key . '_ext'][$file]; ?></span>
                                                                            <?php
                                                                            $nbFile[$key . '_nbFile']++;
                                                                        } ?>
                                                                        <span data-toggle="modal"
                                                                              data-target="#modalDeleteItem"
                                                                              class="ico_poubelle"><i
                                                                                    class="far fa-fw fa-trash-alt"></i></span>
                                                                    </div>
                                                                <?php } elseif ($eKey == $_POST[$key . '_Name'][$file] . '.' . $_POST[$key . '_ext'][$file] && $eVal == null) { ?>
                                                                    <div class="img imgdelete"
                                                                         style="background-image: url('<?php if ($_POST[$key . '_ext'][$file] != 'jpg') {
                                                                             echo '../public/image/forfile/' . $_POST[$key . '_ext'][$file] . '.png';
                                                                         } else {
                                                                             echo htmlspecialchars($f);
                                                                         } ?>'); margin-right: 10px;"
                                                                         rel="<?php echo htmlspecialchars($_POST[$key . '_Name'][$file]); ?>">
                                                                        <span class="span_delete span_delete_add">Supprimer</span>
                                                                        <input class="file_hidden" type="hidden"
                                                                               name="<?php echo htmlspecialchars($key); ?><?php if ($v['nb_file'] > 0) {
                                                                                   echo '[]';
                                                                               } ?>"
                                                                               value="<?php echo htmlspecialchars($f); ?>"/>
                                                                        <input type="hidden"
                                                                               name="<?php echo htmlspecialchars($key . '_ext');
                                                                               if ($v['nb_file'] > 0) {
                                                                                   echo '[]';
                                                                               } ?>"
                                                                               value="<?php echo htmlspecialchars($_POST[$key . '_ext'][$file]); ?>"/>
                                                                        <input type="hidden"
                                                                               name="<?php echo htmlspecialchars($key . '_Name');
                                                                               if ($v['nb_file'] > 0) {
                                                                                   echo '[]';
                                                                               } ?>"
                                                                               value="<?php echo htmlspecialchars($_POST[$key . '_Name'][$file]); ?>"/>
                                                                        <?php if ($eKey == $_POST[$key . '_Name'][$file] . '.' . $_POST[$key . '_ext'][$file]) {
                                                                            $chaineCoupee = substr($_POST[$key . '_Name'][$file], 0, 10); ?>
                                                                            <span class="nom_file"><em class="file_ok">Fichier OK.</em><br/><?php echo $chaineCoupee . '.' . $_POST[$key . '_ext'][$file]; ?></span>
                                                                            <?php
                                                                            $nbFile[$key . '_nbFile']++;
                                                                        } ?>
                                                                        <span data-toggle="modal"
                                                                              data-target="#modalDeleteItem"
                                                                              class="ico_poubelle"><i
                                                                                    class="far fa-fw fa-trash-alt"></i></span>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php }
                                                        }
                                                    }
                                                }
                                            endforeach; ?>
                                        </div>
                                    <?php } ?>

                                    <?php
                                    // File par default -----------------------------------------------------------------------------------------------------------------------------------------------------------
                                    ?>

                                    <input id="fileUpload" class="fileUpload"
                                           data-nameInput="<?php echo htmlspecialchars($key); ?><?php if ($v['nb_file'] > 0) {
                                               echo '[]';
                                           } ?>" data-nameExt="<?php echo htmlspecialchars($key); ?>" type="file"
                                           name="<?php echo htmlspecialchars($key); ?><?php if ($v['nb_file'] > 0) {
                                               echo '[]';
                                           } ?>" data-nbFile="<?php echo htmlspecialchars($v['nb_file']); ?>"
                                           data-section="0" <?php if ($v['nb_file'] > 0) {
                                        echo 'multiple';
                                    } ?>/>
                                    <input class="data_section" type="hidden"
                                           name="<?php echo htmlspecialchars($key); ?>_section"
                                           value="<?php echo htmlspecialchars($nbFile[$key . '_nbFile']); ?>"/>

                                    <div class="div_new_img"></div>


                                    <?php
                                    if ($v['nb_file'] > $nbFile[$key . '_nbFile']) { ?>
                                        <div class="pic picEdit">Ajouter<br/>un fichier</div>
                                    <?php } else { ?>
                                        <div class="pic picEdit" style="display: none">Ajouter<br/>un fichier</div>
                                    <?php } ?>
                                </div>
                            </section>
                        </div>
                        <hr>
                        <br/>
                    </div>
                </div>
            <?php }

// Fin de l'unput File -----------------------------------------------------------------------------------------------------------------------------------------------------------

// input Multiple ( Secto ) -------------------------------------------------------------------------------------------------------------------------------------------------

            if (isset($v['type_input']) && $v['type_input'] == "inputMultiple") { ?>
                <label for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?> :</label>
                <div id="div_champ_secto_site" class="div_champ_secto_site">
                    <?php
                    if (isset($_POST[$key])) {
                        foreach ($_POST[$key] as $pKey => $pVal) {
                            ?>
                            <span class="span_site"
                                  data-item="<?php echo htmlspecialchars($_POST[$key . '_label'][$pKey]); ?>"><?php echo htmlspecialchars($_POST[$key . '_label'][$pKey]); ?>
                                <em class="delete_site">x</em><input type="hidden"
                                                                     id="inputHidden<?php echo htmlspecialchars($v['bdd_table_t']); ?>"
                                                                     class="inputHiddenSite"
                                                                     value="<?php echo htmlspecialchars($pVal); ?>"
                                                                     name="<?php echo htmlspecialchars($key); ?>[]"/><input
                                        type="hidden" id="inputHidden<?php echo htmlspecialchars($v['bdd_table_t']); ?>"
                                        class="inputHiddenSite"
                                        value="<?php echo htmlspecialchars($_POST[$key . '_label'][$pKey]); ?>"
                                        name="<?php echo htmlspecialchars($key); ?>_label[]"/></span>
                            <?php
                        } ?>
                        <?php
                    } elseif (!isset($_POST[$key])) {
                        if (isset($ListSecto) && !empty($ListSecto)) {
                            foreach ($ListSecto as $ListSectoKey => $ListSectoVal) {
                                if ($ListSectoVal != null) { ?>
                                    <span class="span_site"
                                          data-item="<?php echo htmlspecialchars($ListSectoVal[0]->name); ?>"><?php echo htmlspecialchars($ListSectoVal[0]->name); ?>
                                        <em class="delete_site">x</em><input type="hidden"
                                                                             id="inputHidden<?php echo htmlspecialchars($v['bdd_table_t']); ?>"
                                                                             class="inputHiddenSite"
                                                                             value="<?php echo htmlspecialchars($ListSectoVal[0]->id); ?>"
                                                                             name="<?php echo htmlspecialchars($key); ?>[]"/><input
                                                type="hidden"
                                                id="inputHidden<?php echo htmlspecialchars($v['bdd_table_t']); ?>"
                                                class="inputHiddenSite"
                                                value="<?php echo htmlspecialchars($ListSectoVal[0]->name); ?>"
                                                name="<?php echo htmlspecialchars($key); ?>_label[]"/></span>
                                <?php }
                                ?>
                            <?php }
                        }
                    }
                    ?>
                </div>
                <?php echo $form->inputWithIdAutocompPrincipalInputMultiple('inputItem' . $v['bdd_table_multiple_input'], 'inputItem', $v['bdd_table_multiple_input'], $v['bdd_value'], $v['bdd_table_t'], $key); ?>
                <hr>
            <?php }

// input Multiple ( Dept ) -------------------------------------------------------------------------------------------------------------------------------------------------

            if (isset($v['type_input']) && $v['type_input'] == "checkboxDep") { ?>
                <label><?php echo htmlspecialchars($v['nom']); ?> :</label><br/>
                <div class="div_champ_secto_site">
                    <div class="div_checkbox_dep">
                        <?php foreach ($ListDep as $ListDepKey => $ListDepVal) { ?>
                            <div <?php if (isset ($ListDepAttribarray[$ListDepVal->{$v['bdd_id']}]) && $ListDepAttribarray[$ListDepVal->{$v['bdd_id']}] == $ListDepVal->{$v['bdd_id']}) {
                                echo 'style="background-color: #81BAE0; color: #ffffff;"';
                            } ?>><input id="<?php echo htmlspecialchars($ListDepVal->{$v['bdd_id']}); ?>"
                                        type="checkbox" name="<?php echo htmlspecialchars($key); ?>[]"
                                        value="<?php echo htmlspecialchars($ListDepVal->{$v['bdd_id']}); ?>" <?php if (isset ($ListDepAttribarray[$ListDepVal->{$v['bdd_id']}]) && $ListDepAttribarray[$ListDepVal->{$v['bdd_id']}] == $ListDepVal->{$v['bdd_id']}) {
                                    echo 'checked';
                                } ?>/> <label
                                        for="<?php echo htmlspecialchars($ListDepVal->{$v['bdd_id']}); ?>"><?php echo htmlspecialchars($ListDepVal->{$v['label_value']}); ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <hr>
            <?php }

            if (isset($v['type_input']) && $v['type_input'] == "checkboxNro" && isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) { ?>

                <div id="affectedNros" class="row" style="margin-left: 5px;" >

                    <label style="margin-right: 10px;"><?php echo htmlspecialchars($v['nom']); ?> : </label>
                    <?php
                    if (isset($ListSecto) && !empty($ListSecto)){

                        foreach ($ListSecto as $ListSectokey => $ListSectoVal) { ?>

                            <div style="margin-right: 5px;">
                                <span class="badge" style="background-color: #81BAE0; color: #ffffff;font-size: 15px;"><?php echo htmlspecialchars($ListSectoVal[0]->name); ?>
                                    <a href="javascript:void(null);" onclick="showDeleteNroAffected(<?= $ListSectoVal[0]->id;?>)">
                                        <i class="fas fa-times" style="color: #e74a3b"></i>
                                    </a>
                                </span>
                            </div>
                        <?php }
                    }?>
                </div> </br>
                <div class="row">
                    <div class="col-md-12">
                        <input class="form-control" id="nroFilter" type="text" name="nroFilter" placeholder="Affecter un nouveau NRO " autocomplete="off"> <br/>
                    </div>
                </div>
                <div class="div_champ_secto_site_nro" style="display: none">
                    <div class="div_checkbox_dep_nro" >

                    </div>

                </div>
                <hr>
            <?php }
        }
        ?>
        <?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Bouton de soumission des modifications ou de suppressions -------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
 */ ?>
        <script>
            selectedInput =  `<?= json_encode(array_values($ListSectoAttribarray))?>`
        </script>

        <div class="div_edit_button">
            <?php if (isset($_SESSION['Droit']) && isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['modifier']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['modifier'] == 1) { ?>
                <button class="btn btn-danger" onclick="EditDemo()">Modifier</button>

            <?php } ?>


            <?php if (isset($_SESSION['Droit']) && isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['delete']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['delete'] == 1 && isset($_GET['id']) && $_GET['id'] != $_SESSION['UserID']) { ?>
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalDelete">Supprimer
                </button>
            <?php }  ?>


        </div>
    </form>
    <br/>
<?php } else { ?>
    <p>Accès refusé.</a></p>
<?php } ?>

<br/>
<?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Modal Historique ------------------------------------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
<div id="modalAddUser" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div id="content-wrapper">
                <div class="container-fluid">
                    <br/>
                    <h5><?php echo htmlspecialchars($nom_feuilleHistorique); ?></h5>
                    <br/>
                    <?php if (isset($_SESSION['UserID']) && isset($_SESSION['Droit'])) { ?>
                        <div id="div_data_historique" class="div_data_historique">
                        </div>
                        <br/>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        </div>
                        <br/>
                    <?php } else { ?>
                        <p>Accès refusé.</a></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Modal de confirmation de suppression des items ------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Attention</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <br/>
                <p>Etes vous sûr de vouloir supprimer cet item ?</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger"
                   href="index.php?p=Users.deleteUser&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&id=<?php echo htmlspecialchars($_GET['id']); ?>">Confirmer
                    suppression</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>
<?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Modal de confirmation de suppression des fichiers ---------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
<div class="modal fade" id="modalDeleteItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Attention</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <br/>
                <p>Etes vous sûr de vouloir supprimer ce fichier ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger confirmDelItem"
                        data-xls="<?php echo htmlspecialchars($_GET['XLS']) ?>" data-dismiss="modal">Confirmer
                    suppression
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>
<?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Debug Mode ------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
*/ ?>
<?php if (isset($_SESSION['Droit']) && $debugMode == 1) { ?>
    <!-- Footer -->
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

                <li class="list-group-item"><strong>Requête :</strong> <?php echo htmlspecialchars($req); ?> </li>
                <?php if (isset($fileDonneesREQ) && $fileDonneesREQ != null && $fileDonnees != null) { ?>
                    <li class="list-group-item"><strong>Requête File
                            :</strong> <?php echo htmlspecialchars($fileDonneesREQ); ?> </li>
                <?php } ?>
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
<div class="modal fade" id="modalDelAffectNro" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Attention</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div id="affectAlertSucces" class="alert alert-success" role="alert" style="display: none">
                    L'affectation est supprimée avec succés.
                </div>
                <div id="affectAlertFail" class="alert alert-danger" role="alert" style="display: none">
                    L'affectation n'a pas pu être supprimée.
                </div>
                <br/>
                <input type="hidden" id="idNroTodelete" value="">
                <p style="margin-top: 20px;">Etes-vous sur de vouloir supprimer cette affectation ?</p>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(null);" onclick="deleteNroAffected(<?= $_GET['id'] ?>)" type="button"  class="btn btn-danger" >Confirmer annulation</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>