<?php setlocale(LC_TIME, $LocalTime);
$currentDay = intval(date("d"));
$currentMonth = intval(date("m"));
$currentYear = intval(date("Y"));
?>
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
            L'item a été modifié avec succès.
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
          action="?p=Principal.editItem&id=<?php echo htmlspecialchars($_GET['id']); ?>&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&collapsId=<?php echo htmlspecialchars($_GET['collapsId']); ?>">
        <hr>
        <?php
        //-- Parcours de la conf  ---------------------------------------------------------------------------------------------------------------------------------------------------
        foreach ($champs as $key => $v) {
            //Désactiver les champs si on a des conditions
            $disableChamps = '';

            if (isset($disable_on_conditions) && isset($disable_on_conditions[$key]) && in_array(true, $disable_on_conditions[$key])) {
                $disableChamps = 'disabled';
            }

            if ($v['type_input'] == 'categorie') {
                if ($v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                    ?>
                    <div class=""><h5 class="title"><span><?php echo htmlspecialchars($v['nom']); ?></span></h5></div>
                <?php }
            }


            if ($v['type_input'] == 'sous_categorie') {
                if ($v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                    ?>
                    <div class=""><h5 class="title2"><span><?php echo htmlspecialchars($v['nom']); ?></span></h5></div>
                <?php }
            }


            if (isset($v['liste_detail']) && $v['liste_detail'] == 1) {
                if ($v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                    if (isset($v['type']) && $v['type'] == "list_sql") {

                        if (isset($v['autocomplete'])) {
                            if ($v['autocomplete'] == 1) {
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- Autocomplétion avec parcours de l'objet errors  ------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                if (!empty($errors) && $errors != null && !empty(array_keys($errors->errors, $key))) {
                                    foreach ($errors->errors as $error => $e):
                                        if ($error != null && $key == $error) {
                                            if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {
                                                //-- Mode Disabled activé ---------------------------------------------
                                                if (isset($_POST[$v['bdd_value']])) {
                                                    echo $form->inputWithIdAutocompEditPostDPrincipal('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $_POST[$v['bdd_table_t']], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                                } else {
                                                    echo $form->inputWithIdAutocompDPrincipal('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                                }
                                            } //-- Mode Disabled désactivé -------------------------------------------
                                            else {
                                                if (isset($_POST[$v['bdd_value']])) {
                                                    echo $form->inputWithIdAutocompEditPostPrincipal('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $_POST[$v['bdd_table_t']], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                                } else {
                                                    echo $form->inputWithIdAutocompPrincipal('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
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
                                            echo $form->inputWithIdAutocompEditPostDPrincipal('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $_POST[$v['bdd_value']], $v['nom'] . ' :', $v['bdd_table_t'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                            ?>
                                            <hr><?php
                                        } else {
                                            echo $form->inputWithIdAutocompDPrincipal('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $v['nom'] . ' :', $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
                                            ?>
                                            <hr><?php
                                        }
                                    } else {

                                        if (isset($_POST[$v['bdd_value']])) {
                                            echo $form->inputWithIdAutocompEditPostPrincipal('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $_POST[$v['bdd_value']], $v['nom'] . ' :', $v['bdd_table_t'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);

                                        } else {
                                            echo $form->inputWithIdAutocompPrincipal('inputItem' . $v['bdd_table_t'], $v['bdd_table_t'], $v['nom'] . ' :', $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);
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
                                                <?php echo $form->inputWithId('labelDisable' . $v['bdd_table_t'], $v['bdd_table_t'], 'Label :', $v['taille_min'], $v['taille_max']); ?>
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
                            elseif ($v['autocomplete'] == 0) { ?>
                                <?php if (!empty($errors) && $errors != null && !empty(array_keys($errors->errors, $key))) {
                                    foreach ($errors->errors as $error => $e):
                                        if ($error != null && $key == $error) {
                                            ?>
                                            <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) { ?>
                                                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                                       value="<?php echo htmlspecialchars($donnees->{$key}); ?>"/>
                                            <?php } ?>
                                            <div class="form-group row"><label
                                                        for="<?php echo htmlspecialchars($key); ?>"
                                                        class="col-sm-2 col-form-label"><?php echo htmlspecialchars($v['nom']); ?><?php echo ' ' . $e; ?></label>
                                                <div class="col-sm-10"><select
                                                            id="<?php echo htmlspecialchars($key); ?>"
                                                            name="<?php echo htmlspecialchars($key); ?>"
                                                            class="form-control" <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1 || $disableChamps == 'disabled') {
                                                        echo 'disabled="disabled"';
                                                    } ?>>

                                                        <?php if (isset($v['obligatoire']) && $v['obligatoire'] == 1) {
                                                        } else { ?>
                                                            <option value="0"></option>
                                                        <?php } ?>
                                                        <?php
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
                                else {
                                    if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1 || $disableChamps == 'disabled') { ?>
                                        <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                               value="<?php echo htmlspecialchars($donnees->{$key}); ?>"/>
                                    <?php } ?>
                                    <div class="form-group row">
                                        <label for="<?php echo htmlspecialchars($key); ?>"
                                               class="col-sm-2 col-form-label"><?php echo htmlspecialchars($v['nom']); ?>
                                            :</label>
                                        <div class="col-sm-10">
                                            <select style="<?php if(isset($donnees->old_value)
                                                && isset($donnees->old_value->{'old_'.$key}) && $donnees->old_value->{'old_'.$key} != null
                                                && ($donnees->{$key} ==null || $donnees->{$key} == '0')){
                                                echo 'color : #4e73df;';
                                            }?>"
                                                    id="<?php echo htmlspecialchars($key); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    data-input = "<?php echo htmlspecialchars($v['bdd_table_t']) ?>"
                                                    class="form-control <?php echo isset($donnees->old_value->{'old_'.$key}) ? 'changeSelected' : '' ?>"
                                                <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1 || $disableChamps == 'disabled') {echo 'disabled="disabled"';
                                            } ?>>

                                                <?php if (isset($v['obligatoire']) && $v['obligatoire'] == 1) {
                                                } else {
                                                    if(isset($v['placeholder']) && $v['placeholder'] ==1 && ($donnees->{$key} == NULL || $donnees->{$key} == '0') &&
                                                        isset($donnees->old_value) && !empty($donnees->old_value)
                                                        && isset($donnees->old_value->{'old_'.$key}) && $donnees->old_value->{'old_'.$key} != NULL
                                                    ) {?>
                                                        <option class="oldNoteValue" selected disabled hidden><?php echo htmlspecialchars($donnees->old_value->{'old_'.$key}) ?></option>
                                                    <?php } else { ?>
                                                        <option value="0"></option>

                                                    <?php } ?>
                                                <?php } ?>
                                                <?php
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
                                                ?>
                                            </select>
                                            <input type="hidden" name="<?php echo htmlspecialchars($v['bdd_table_t']) ?>" id="<?php echo htmlspecialchars($v['bdd_table_t']) ?>">
                                        </div>
                                    </div>
                                    <hr>
                                <?php } ?>
                            <?php } ?>
                        <?php } else {
                            if ($v['type_input'] == 'textarea_jointure') {
//TextAreaJointure avec parcours de l'objet errors
                                if (!empty($errors) && $errors != null) {
                                    foreach ($errors->errors as $error => $e):
                                        if ($error != null && $key == $error) { ?>
                                            <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) { ?>
                                                <input type="hidden"
                                                       name="<?php echo htmlspecialchars($tabXlsFields[$_GET['XLS']]['champs'][$key]['bdd_table_t']); ?>"
                                                       value="<?php echo htmlspecialchars($donnees->{$tabXlsFields[$_GET['XLS']]['champs'][$key]['bdd_table_t']}); ?>"/>
                                            <?php } ?>
                                            <div class="form-group row"><label
                                                        for="<?php echo htmlspecialchars($key); ?>"
                                                        class="col-sm-2 col-form-label"><?php echo htmlspecialchars($v['nom']); ?><?php echo ' ' . $e; ?></label>
                                                <div class="col-sm-10">
                                            <textarea
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnees->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1 || $disableChamps == 'disabled') {
                                                        echo 'disabled="disabled"';
                                                    } ?>
                                            ><?php echo htmlspecialchars($donnees->{$tabXlsFields[$_GET['XLS']]['champs'][$key]['bdd_table_t']}); ?></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                        <?php }
                                    endforeach;
                                }
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- TextAreaJointure sans l'objet errors  --------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                else {
                                     if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {?>
                                        <input type="hidden"
                                               name="<?php echo htmlspecialchars($tabXlsFields[$_GET['XLS']]['champs'][$key]['bdd_table_t']); ?>"
                                               value="<?php echo htmlspecialchars($donnees->{$tabXlsFields[$_GET['XLS']]['champs'][$key]['bdd_table_t']}); ?>"/>
                                    <?php echo $disableChamps; } ?>
                                    <div class="form-group row"><label for="<?php echo htmlspecialchars($key); ?>"
                                                                       class="col-sm-2 col-form-label"><?php echo htmlspecialchars($v['nom']); ?>
                                            :</label>
                                        <div class="col-sm-10">

                                            <textarea
                                                    class="form-control"
                                                    minlength="<?php echo $v['taille_min'] ?>"
                                                    maxlength="<?php echo $v['taille_max'] ?>"
                                                    id="<?php echo htmlspecialchars($_GET['XLS']); ?>_<?php echo htmlspecialchars($key) ?>_<?php echo htmlspecialchars($donnees->{$tabXlsFields[$_GET['XLS']]['bdd_id']}); ?>"
                                                    name="<?php echo htmlspecialchars($key); ?>"
                                                    <?php if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1 || $disableChamps == 'disabled') {
                                                        echo 'disabled="disabled"';
                                                    } ?>
                                            ><?php echo htmlspecialchars($donnees->{$tabXlsFields[$_GET['XLS']]['champs'][$key]['bdd_table_t']}); ?></textarea>
                                            <input type="hidden"
                                                   name="<?php echo htmlspecialchars($key) . "_old_val"; ?>"
                                                   value="<?php echo htmlspecialchars($donnees->{$tabXlsFields[$_GET['XLS']]['champs'][$key]['bdd_table_t']}); ?>"/>
                                            <input type="hidden"
                                                   name="<?php echo htmlspecialchars($key) . "_bdd_val"; ?>"
                                                   value="<?php echo($donnees->{$key}); ?>"/>
                                        </div>
                                    </div>
                                    <hr>
                                <?php } ?>

                                <?php
                            }
                        }
                    } else {
                        if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {
//-- input password sans l'objet errors  ---------------------------------------------------------------------------------------------------------------------------------------------------------
                            if ($key == 'password') {
                                ?>
                                <div class="form-group row"><label class="col-sm-2 col-form-label" for="password">Nouveau <?php echo htmlspecialchars($v['nom']); ?>
                                        :</label>
                                    <div class="col-sm-10"><input id="password" type="text" name="password"
                                                                  class="form-control" disabled="disabled"
                                                                  minlength="<?php echo htmlspecialchars($v['taille_min']); ?>"
                                                                  maxlength="<?php echo htmlspecialchars($v['taille_max']); ?>"

                                        /></div>
                                </div>
                                <hr>
                            <?php } else {
//-- input DateTime sans l'objet errors ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                if ($v['type_input'] == "datetime") { ?>
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
                                     if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1 || $disableChamps == 'disabled') { ?>
                                        <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                               value="<?php echo htmlspecialchars($donnees->{$key}); ?>"/>
                                    <?php }
                                    if ($v['type_input'] == "date") {
                                        echo $form->inputD(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' :', ["type" => "date"]);
                                    } else {
                                            $class='onCalcul ';
                                            $placeholder = isset($donnees->{'calcul_'.$key}) ? $donnees->{'calcul_'.$key} : '';
                                        if (isset($v['compareWith']) && !empty($v['compareWith']) && isset($v['compareWith']['champ'])
                                            && isset($v['compareWith']['condition']) && $v['compareWith']['condition'] == true
                                            && $donnees->{'calcul_' . $key} == $donnees->{$v['compareWith']['champ']}
                                            && ($donnees->{$key} == null || $donnees->{$key} == '0')) {
                                            $class .= 'calculated';
                                        }
                                            if($donnees->{$key} == -1) {
                                                $disableChamps = 'disabled';
                                                $class.=' noFillValue';
                                                $donnees->{$key} = null;
                                            }
                                            //-- Autre input Disabled sans l'objet errors  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                            echo $form->inputD(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' :', ['type' => $v['type_input'], 'class'=>$class, 'placeholder'=>$placeholder]);

                                        }
                                }
                                ?>
                                <hr><?php
                            }
                        } else {
                            $placeholder = "";
                            if (!empty($donnees->old_value)) {
                                if (isset($donnees->old_value->{$key}) && $donnees->{$key} != -1) {
                                    $placeholder = $donnees->old_value->{$key};
                                }
                            }
                            if (!empty($errors) && $errors != null) {
                                foreach ($errors->errors as $error => $e):
                                    if ($error != null && $key == $error) {
//-- input password avec l'objet errors  ---------------------------------------------------------------------------------------------------------------------------------------------------------
                                        if ($v['type_input'] == 'password') {
                                            ?>
                                            <div class="form-group row"><label for="password"
                                                                               class="col-sm-2 col-form-label">Nouveau <?php echo htmlspecialchars($v['nom']); ?><?php echo ' ' . $e; ?></label>
                                                <div class="col-sm-10"><input id="password" type="text" name="password"
                                                                              minlength="<?php echo htmlspecialchars($v['taille_min']); ?>"
                                                                              maxlength="<?php echo htmlspecialchars($v['taille_max']); ?>"
                                                                              class="form-control"/></div>
                                            </div>
                                            <hr>
                                        <?php } //-- input number avec l'objet errors  ---------------------------------------------------------------------------------------------------------------------------------------------------------
                                        elseif ($v['type_input'] == "number") {
                                            $class = 'onCalcul ';
                                            $placeholder = isset($donnees->{'calcul_' . $key}) ? $donnees->{'calcul_' . $key} : '';
                                            if (isset($v['compareWith']) && !empty($v['compareWith']) && isset($v['compareWith']['champ'])
                                                && isset($v['compareWith']['condition']) && $v['compareWith']['condition'] == true
                                                && $donnees->{'calcul_' . $key} == $donnees->{$v['compareWith']['champ']}
                                                && ($donnees->{$key} == null || $donnees->{$key} == '0')) {
                                                $class .= 'calculated';
                                            }
                                            if($donnees->{$key} == -1) {
                                                $disableChamps = 'disabled';
                                                $class.=' noFillValue';
                                                $donnees->{$key} = null;
                                            }
                                                echo $form->inputNumber(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' ' . $e, $v['taille_min'], $v['taille_max'], ['type' => $v['type_input'], 'disable' => $disableChamps, 'placeholder' => $placeholder, 'class' => $class]);
                                            ?>
                                            <hr><?php
                                        } //-- input DateTime avec l'objet errors ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                        elseif ($v['type_input'] == "datetime") {
                                            ?>
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
                                                                    id="<?php echo htmlspecialchars($key); ?>"
                                                                    type="date"
                                                                    name="<?php echo htmlspecialchars($key); ?>_date1"
                                                                    value="<?php echo htmlspecialchars($dateFormate); ?>"
                                                                    class="input_date"/> à <input
                                                                    id="<?php echo htmlspecialchars($key); ?>"
                                                                    type="time"
                                                                    name="<?php echo htmlspecialchars($key); ?>_date2"
                                                                    value="<?php echo htmlspecialchars($heurFormate); ?>"
                                                                    class="input_number"/></div>
                                                    </div> <?php
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
                                            echo $form->input(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' ' . $e, ['type' => $v['type_input'], 'disable' => $disableChamps, 'min' => $v['taille_min'], 'max' => $v['taille_max']]);
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
                                                                      minlength="<?php echo htmlspecialchars($v['taille_min']); ?>"
                                                                      maxlength="<?php echo htmlspecialchars($v['taille_max']); ?>"
                                                                      class="form-control"/></div>
                                    </div>
                                    <hr>
                                <?php }
                                elseif ($v['type_input'] == "number") {
                                        $class = 'onCalcul ';
                                    if (isset($donnees->{'calcul_' . $key})) {
                                            $placeholder = isset($donnees->{'calcul_' . $key}) ? $donnees->{'calcul_' . $key} : '';
                                        if (isset($v['compareWith']) && !empty($v['compareWith']) && isset($v['compareWith']['champ'])
                                            && isset($v['compareWith']['condition']) && $v['compareWith']['condition'] == true
                                            && $donnees->{'calcul_' . $key} == $donnees->{$v['compareWith']['champ']}
                                            && ($donnees->{$key} == null || $donnees->{$key} == '0')) {
                                            $class .= 'calculated';
                                        }
                                        }
                                    if($donnees->{$key} == -1) {
                                        $disableChamps = 'disabled';
                                        $class.=' noFillValue';
                                        $donnees->{$key} = null;
                                    }
                                        echo $form->inputNumber(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' :', $v['taille_min'], $v['taille_max'], ['type' => $v['type_input'], 'disable' => $disableChamps, 'placeholder' => $placeholder, 'class' => $class]);
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
                                    if ($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1 || $disableChamps == 'disabled') { ?>
                                        <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                               value="<?php echo htmlspecialchars($donnees->{$key}); ?>"/>
                                    <?php }
                                    echo $form->input(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' :', ['type' => $v['type_input'], 'disable' => $disableChamps, 'min' => $v['taille_min'], 'max' => $v['taille_max']]);
                                    ?>
                                    <hr><?php
                                }
                            }
                        }
                    }
                }
            }

//-- Début de Input file --------------------------------------------------------------------------------------------------------------------------------------------------------

            if (isset($v['type_input']) && $v['type_input'] == "file" && isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {

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
                                                             rel=""><?php if (isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) { ?>
                                                                <a class="a_dl_file"
                                                                   href="<?php echo $v['cheminDossier'] . htmlspecialchars($val_f->path); ?>"
                                                                   download><span style="" class="span_delete dl">Télécharger</span>
                                                                </a> <?php } ?>  <?php if (isset($v['profilChamps'][$_SESSION['Droit']]['suppression']) && $v['profilChamps'][$_SESSION['Droit']]['suppression'] == 1) { ?>
                                                            <span data-id_item="<?php echo htmlspecialchars($val_f->id); ?>"
                                                                  data-confFile="<?php echo htmlspecialchars($val_f->champ); ?>"
                                                                  data-champFile="<?php echo htmlspecialchars($val_f->path); ?>"
                                                                  class="ico_poubelle" data-toggle="modal"
                                                                  data-target="#modalDeleteItem"><i
                                                                            class="far fa-fw fa-trash-alt"></i>
                                                                </span><?php } ?></div>
                                                    <?php } else {
                                                        $path = "../public/image/forfile/" . $extEx[1] . ".png"; ?>
                                                        <div class="img imgdl"
                                                             style="background-image: url('<?php echo htmlspecialchars($path); ?>'); margin-right: 10px;"
                                                             rel=""><?php if (isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) { ?>
                                                                <a class="a_dl_file"
                                                                   href="<?php echo $v['cheminDossier'] . htmlspecialchars($val_f->path); ?>"
                                                                   download><span style="" class="span_delete dl">Télécharger</span>
                                                                </a> <?php } ?><span
                                                                    class="nom_file"><?php echo htmlspecialchars($nomFile); ?></span> <?php if (isset($v['profilChamps'][$_SESSION['Droit']]['suppression']) && $v['profilChamps'][$_SESSION['Droit']]['suppression'] == 1) { ?>
                                                                <span data-toggle="modal" data-target="#modalDeleteItem"
                                                                      data-id_item="<?php echo htmlspecialchars($val_f->id); ?>"
                                                                      data-confFile="<?php echo htmlspecialchars($val_f->champ); ?>"
                                                                      data-champFile="<?php echo htmlspecialchars($val_f->path); ?>"
                                                                      class="ico_poubelle"><i
                                                                            class="far fa-fw fa-trash-alt"></i></span> <?php } ?>
                                                        </div>
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
                                    if ($v['nb_file'] > $nbFile[$key . '_nbFile']) {
                                        ?>

                                        <?php if (isset($v['profilChamps'][$_SESSION['Droit']]['ecriture']) && $v['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1) { ?>
                                            <div class="pic picEdit">Ajouter<br/>un fichier</div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php if (isset($v['profilChamps'][$_SESSION['Droit']]['ecriture']) && $v['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1) { ?>
                                            <div class="pic picEdit" style="display: none">Ajouter<br/>un fichier</div>
                                        <?php } ?>
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

// Début du bloc Extra Attribute -------------------------------------------------------------------------------------------------------------------------------------------------

            if (isset($donneesExtraAttributes) && $donneesExtraAttributes != null) {
                if (isset($v['type_input']) && $v['type_input'] == "bloc_attribute" && isset($v['profilChamps'][$_SESSION['Droit']]['modification']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) { ?>
                    <p style="margin-top: 10px;"><?php echo htmlspecialchars($v['nom']); ?> :</p>
                    <?php
                    foreach ($donneesExtraAttributes as $donneesExtraAttributesKey => $donneesExtraAttributesVal) { ?>
                        <div style="border: 1px solid #dfe0e2;margin-top: 20px;margin-bottom: 20px;padding-top: 20px;padding-bottom: 20px;background-color: #ffffff;">

                            <input type="hidden" name="id_extra_attribute[]"
                                   value="<?php echo htmlspecialchars($donneesExtraAttributesVal->id); ?>"/>

                            <?php
                            if (!empty($errors) && $errors != null) {
                                foreach ($errors->errors as $error => $e):
                                    foreach ($v as $vKey => $vVal) { ?>
                                        <?php
                                        if ($error != null && $vKey . '_extra_attribute' == $error) {
                                            if (isset($vVal['profilChamps'][$_SESSION['Droit']]['modification']) && $vVal['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                                                if ($vKey != 'id') { ?>
                                                    <div class="form-group row"><label style="padding-left: 25px;"
                                                                                       class="col-sm-2 col-form-label"
                                                                                       for="<?php echo htmlspecialchars($vKey); ?>"> <?php echo htmlspecialchars($vVal['nom']); ?><?php echo $e[$donneesExtraAttributesKey]; ?> </label>
                                                        <div class="col-sm-10"><input
                                                                    id="<?php echo htmlspecialchars($vKey); ?>"
                                                                    type="text"
                                                                    name="<?php echo htmlspecialchars($vKey); ?>_extra_attribute[]"
                                                                    value="<?php echo htmlspecialchars($donneesExtraAttributesVal->{$vKey}); ?>"
                                                                    class="form-control"/></div>
                                                    </div>
                                                <?php } ?>
                                                <?php
                                            }
                                        }
                                    }
                                endforeach;
                            } else {
                                foreach ($v as $vKey => $vVal) {
                                    if (isset($vVal['profilChamps'][$_SESSION['Droit']]['modification']) && $vVal['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                                        if ($vKey != 'id') { ?>
                                            <div class="form-group row"><label style="padding-left: 25px;"
                                                                               class="col-sm-2 col-form-label"
                                                                               for="<?php echo htmlspecialchars($vKey); ?>"> <?php echo htmlspecialchars($vVal['nom']); ?>
                                                    : </label>
                                                <div class="col-sm-10"><input
                                                            id="<?php echo htmlspecialchars($vKey); ?>" type="text"
                                                            name="<?php echo htmlspecialchars($vKey); ?>_extra_attribute[]"
                                                            value="<?php echo htmlspecialchars($donneesExtraAttributesVal->{$vKey}); ?>"
                                                            class="form-control"/></div>
                                            </div>
                                        <?php } ?>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    <?php }
                }
            }

        }
        if(isset($other_champs) && !empty($other_champs)){
            foreach($other_champs AS $key=>$val){
                if(isset($val['liste_detail_hidden']) && $val['liste_detail_hidden'] == 1 && isset($donnees->{$key})) { ?>
                    <input type="hidden" name="<?php echo 'old_'.$key ?>" value="<?php echo $donnees->{$key} ?>">
                <?php }
                 }
        } ?>
        <?php /*
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- Bouton de soumission des modifications ou de suppressions -------------------------------------------------------------------------------------------------------------->
<!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
 */ ?>
        <div class="div_edit_button">
            <?php if (isset($_SESSION['Droit']) && isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['modifier']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['modifier'] == 1) { ?>
                <button class="btn btn-danger">Modifier</button>
            <?php } ?>

            <?php if (isset($_SESSION['Droit']) && isset($tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['delete']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['delete'] == 1) { ?>
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalDelete">Supprimer
                </button>
            <?php } ?>
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
                   href="index.php?p=Principal.deleteItem&XLS=<?php echo htmlspecialchars($_GET['XLS']); ?>&id=<?php echo htmlspecialchars($_GET['id']); ?>">Confirmer
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
