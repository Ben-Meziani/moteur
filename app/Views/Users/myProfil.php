<?php  setlocale(LC_TIME, $LocalTime); ?>
<br/>
<?php if(isset($_SESSION['UserID']) && $_SESSION['Droit']) { ?>

    <?php if(!empty($errors) && $errors != null): ?>
        <div class="alert alert-danger">
            <p>Vous n'avez pas rempli le formulaire correctement :</p>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['succes']) && $_SESSION['succes'] == 1){ ?>
        <div id="bloc_info" class="alert alert-success" role="alert">
            Le Profil a été modifié avec succès.
        </div>
    <?php
    unset($_SESSION['succes']);
    } ?>
    <form method="post" action="?p=Users.myProfil">
        <hr>
        <?php
        foreach ($champs as $key => $v){
            if(isset($v['liste_detail']) && $v['liste_detail'] == 1){
                if($v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1){ ?>

                    <?php   if(isset($v['type']) && $v['type'] == "list_sql"){ ?>
                        <?php    if($v['autocomplete'] == 1){?>

                            <?php
                            if(!empty($errors) && $errors != null){
                                foreach($errors->errors as $error => $e):
                                    if($error != null && $key == $error){
                                        if($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {

                                            if (isset($_POST[$v['bdd_value']])) {
                                                echo $form->inputWithIdAutocompEditPostD('inputItem' . $v['bdd_table'], $v['bdd_table_t'], $_POST[$v['bdd_value']], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value']);
                                            } else {
                                                echo $form->inputWithIdAutocompD('inputItem' . $v['bdd_table'], $v['bdd_table_t'], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value']);
                                            }

                                        }else{
                                            if (isset($_POST[$v['bdd_value']])) {
                                                echo $form->inputWithIdAutocompEditPost('inputItem' . $v['bdd_table'], $v['bdd_table_t'], $_POST[$v['bdd_table_t']], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value']);
                                            } else {
                                                echo $form->inputWithIdAutocomp('inputItem' . $v['bdd_table'], $v['bdd_table_t'], $v['nom'] . ' ' . $e, $v['bdd_table'], $v['bdd_value']);
                                            }
                                        }
                                    }
                                endforeach;
                            }else {

                                if($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1) {
                                    if (isset($_POST[$v['bdd_value']])) {
                                        echo $form->inputWithIdAutocompEditPostD('inputItem' . $v['bdd_table'], $v['bdd_table_t'], $_POST[$v['bdd_table_t']], $v['nom'] . ' :', $v['bdd_table'], $v['bdd_value']);
                                        ?><hr><?php
                                    } else {
                                        echo $form->inputWithIdAutocompD('inputItem' . $v['bdd_table'], $v['bdd_table_t'], $v['nom'] . ' :', $v['bdd_table'], $v['bdd_value']);
                                        ?><hr><?php
                                    }
                                }else{

                                    if (isset($_POST[$v['bdd_value']])) {
                                        echo $form->inputWithIdAutocompEditPost('inputItem' . $v['bdd_table'], $v['bdd_table_t'], $_POST[$v['bdd_table_t']], $v['nom'] . ' :', $v['bdd_table'], $v['bdd_value']);

                                    } else {
                                        echo $form->inputWithIdAutocomp('inputItem' . $v['bdd_table'], $v['bdd_table_t'], $v['nom'] . ' :', $v['bdd_table'], $v['bdd_value']);
                                    }
                                }
                            }
                            ?>
                        <?php } else{ ?>
                            <?php if(!empty($errors) && $errors != null){
                                foreach($errors->errors as $error => $e):


                                    if($error != null && $key == $error){?>
        <div class="form-group row">   <label class="col-sm-2 col-form-label" for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']) .$e; ?></label> <div class="col-sm-10"> <select id="<?php echo htmlspecialchars($key); ?>" name="<?php echo htmlspecialchars($key); ?>" class="form-control"  <?php  if($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1){ echo 'disabled="disabled"';}?>>


                                            <?php
                                            if(isset($v['reqPart'])){?>
                                                <option value="0">Aucun</option>
                                            <?php } ?>



                                            <?php foreach($options as $o => $oo){
                                                if($key == $o) {
                                                    foreach ($oo as $o_key => $o_value) {


                                                        if(isset($v['reqPart']) ){
                                                            ?>
                                                            <option value="<?php echo $o_value->id; ?>" <?php if($o_value->id == $donnees->{$key}){echo 'selected="selected"';}?>><?php echo htmlspecialchars($o_value->nom); ?> <?php echo htmlspecialchars($o_value->prenom); ?></option>
                                                            <?php
                                                        }
                                                        elseif ($o_value->{$v['bdd_id']} != 10000 && $o_value->{$v['bdd_table_t']} != 'roleId_alias') {
                                                            ?>
                                                            <option value="<?php echo $o_value->{$v['bdd_id']}; ?>" <?php if ($o_value->{$v['bdd_id']} == $donnees->{$key}) {
                                                                echo 'selected="selected"';
                                                            } ?>><?php echo htmlspecialchars($o_value->{$v['bdd_table_t']}); ?></option>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            }?></select></div></div>
                                        <hr>
                                    <?php }
                                endforeach;
                            }else {?>
        <div class="form-group row">     <label class="col-sm-2 col-form-label" for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?> :</label><div class="col-sm-10">  <select id="<?php echo htmlspecialchars($key); ?>" name="<?php echo htmlspecialchars($key); ?>" class="form-control"  <?php  if($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1){ echo '  disabled="disabled"';}?>>

                                            <?php
                                        if(isset($v['reqPart'])){?>
                                            <option value="0">Aucun</option>
                                        <?php } ?>

                                    <?php foreach($options as $o => $oo){
                                        if($key == $o) {
                                            foreach ($oo as $o_key => $o_value) {

                                                if(isset($v['reqPart']) ){
                                                    ?>
                                                    <option value="<?php echo $o_value->id; ?>" <?php if($o_value->id == $donnees->{$key}){echo 'selected="selected"';}?>><?php echo htmlspecialchars($o_value->nom); ?> <?php echo htmlspecialchars($o_value->prenom); ?></option>
                                                    <?php
                                                }
                                                elseif ($o_value->{$v['bdd_id']} != 10000 && $o_value->{$v['bdd_table_t']} != 'roleId_alias') {
                                                    ?>
                                                    <option value="<?php echo $o_value->{$v['bdd_id']}; ?>" <?php if ($o_value->{$v['bdd_id']} == $donnees->{$key}) {
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo htmlspecialchars($o_value->{$v['bdd_table_t']}); ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                    }?></select></div></div>
                                <hr>
                            <?php }?>
                        <?php   } ?>

                    <?php }else{
                        if($v['profilChamps'][$_SESSION['Droit']]['modification'] != 1){
                            if($key == 'password'){?>
                                <div class="form-group row">      <label class="col-sm-2 col-form-label"  for="password">Nouveau <?php echo htmlspecialchars($v['nom']);?> :</label><div class="col-sm-10"> <input id="password" type="text" name="password" class="form-control" disabled="disabled"/></div></div>
                                <hr>
                            <?php }else{


                                if ($v['type_input'] == "datetime"){?>

                                    <div>
                                        <?php
                                        if(isset($donnees->{$key})&& !empty($donnees->{$key})) {
                                            $resultat_date = explode('-', $donnees->{$key});
                                            $resultat_heur = explode(':', $resultat_date[2]);
                                            $sep = explode(' ', $resultat_heur[0]);

                                            $dateFormate = $resultat_date[0]."-".$resultat_date[1]."-".$sep[0] ;
                                            $heurFormate = $sep[1].":".$resultat_heur[2];

                                            ?>    <div class="form-group row"> <label class="col-sm-2 col-form-label"  for="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($v['nom']);?> : </label><div class="col-sm-10">  <input id="<?php echo htmlspecialchars($key);?>" type="date" name="<?php echo htmlspecialchars($key);?>_date1" value="<?php echo htmlspecialchars($dateFormate);?>" class="input_date input_date_disabled" disabled="disabled"/> à <input id="<?php echo htmlspecialchars($key);?>" type="time" name="<?php echo htmlspecialchars($key);?>_date2" value="<?php echo htmlspecialchars($heurFormate);?>" class="input_number input_date_disabled" disabled="disabled"/></div></div><?php

                                        }?>
                                    </div>
                                <?php }else{
                                    echo $form->inputD(htmlspecialchars($key), htmlspecialchars($v['nom']).' :', ['type' => $v['type_input']]);
                                }

                                ?><hr><?php
                            }
                        }else{
                            if(!empty($errors) && $errors != null){
                                foreach($errors->errors as $error => $e):
                                    if($error != null && $key == $error){

                                        if($v['type_input']  == 'password'){?>
                                            <div class="form-group row">   <label class="col-sm-2 col-form-label"  for="password">Nouveau <?php echo htmlspecialchars($v['nom']);?><?php echo ' '. $e;?></label><div class="col-sm-10"> <input id="password" type="text" name="password" class="form-control"/></div></div>
                                            <hr>
                                        <?php }
                                        elseif($v['type_input'] == "number"){
                                            echo $form->inputNumber(htmlspecialchars($key), htmlspecialchars($v['nom']).' '. $e, $v['taille_min'], $v['taille_max'], ['type' => $v['type_input']]);
                                            ?><hr><?php
                                        }elseif ($v['type_input'] == "datetime"){?>

                                            <div>
                                                <?php
                                                if(isset($donnees->{$key})&& !empty($donnees->{$key})) {
                                                    $resultat_date = explode('-', $donnees->{$key});
                                                    $resultat_heur = explode(':', $resultat_date[2]);
                                                    $sep = explode(' ', $resultat_heur[0]);

                                                    $dateFormate = $resultat_date[0]."-".$resultat_date[1]."-".$sep[0] ;
                                                    $heurFormate = $sep[1].":".$resultat_heur[2];

                                                    ?> <div class="form-group row"><label class="col-sm-2 col-form-label"  for="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($v['nom']);?> : </label><div class="col-sm-10"> <input id="<?php echo htmlspecialchars($key);?>" type="date" name="<?php echo htmlspecialchars($key);?>_date1" value="<?php echo htmlspecialchars($dateFormate);?>" class="input_date"/> <input id="<?php echo htmlspecialchars($key);?>" type="time" name="<?php echo htmlspecialchars($key);?>_date2" value="<?php echo htmlspecialchars($heurFormate);?>" class="input_number"/> </div></div><?php echo ' '. $e ;?><?php

                                                }?>
                                            </div>
                                        <?php }else{
                                            echo $form->input(htmlspecialchars($key), htmlspecialchars($v['nom']).' '. $e, ['type' => $v['type_input']]);
                                            ?><hr><?php
                                        }
                                    }
                                endforeach;
                            }else {
                                if($v['type_input']  == 'password'){?>
                                    <div class="form-group row"> <label class="col-sm-2 col-form-label"  for="password">Nouveau <?php echo htmlspecialchars($v['nom']);?> :</label><div class="col-sm-10"><input id="password" type="text" name="password" class="form-control"/></div></div>
                                    <hr>
                                <?php }
                                elseif($v['type_input'] == "number"){
                                    echo $form->inputNumber(htmlspecialchars($key), htmlspecialchars($v['nom']).' :', $v['taille_min'], $v['taille_max'], ['type' => $v['type_input']]);
                                }elseif ($v['type_input'] == "datetime"){?>

                                    <div>
                                        <?php
                                        if(isset($donnees->{$key})&& !empty($donnees->{$key})) {
                                            $resultat_date = explode('-', $donnees->{$key});
                                            $resultat_heur = explode(':', $resultat_date[2]);
                                            $sep = explode(' ', $resultat_heur[0]);

                                            $dateFormate = $resultat_date[0]."-".$resultat_date[1]."-".$sep[0] ;
                                            $heurFormate = $sep[1].":".$resultat_heur[1];

                                            ?> <div class="form-group row"><label class="col-sm-2 col-form-label" for="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($v['nom']);?> : </label><div class="col-sm-10"> <input id="<?php echo htmlspecialchars($key);?>" type="date" name="<?php echo htmlspecialchars($key);?>_date1" value="<?php echo htmlspecialchars($dateFormate);?>" class="input_date"/> <input id="<?php echo htmlspecialchars($key);?>" type="time" name="<?php echo htmlspecialchars($key);?>_date2" value="<?php echo htmlspecialchars($heurFormate);?>" class="input_number"/></div></div><?php
                                            ?><hr><?php
                                        }?>

                                    </div>
                                <?php } else{
                                    echo $form->input(htmlspecialchars($key), htmlspecialchars($v['nom']).' :', ['type' => $v['type_input']]);
                                    ?><hr><?php
                                }

                            }
                        }

                    }
                }
            }


            if(isset($ListSecto) && !empty($ListSecto)) {
                if (isset($v['type_input']) && $v['type_input'] == "inputMultiple") { ?>
                    <label for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?>
                        :</label>
                    <div id="div_champ_secto_site" class="div_champ_secto_site">
                        <?php
                        if (isset($ListSecto) && !empty($ListSecto)) {
                            foreach ($ListSecto as $ListSectoKey => $ListSectoVal) {

                                ?>
                                <span class="span_site"><?php echo htmlspecialchars($ListSectoVal[0]->code_site); ?></span>
                            <?php }
                        }
                        ?>
                    </div>
                    <br/>
                    <hr>
                <?php }
            }
            if(isset($ListDep) && !empty($ListDep)){
                if(isset($v['type_input']) && $v['type_input'] == "checkboxDep"){ ?>
                    <label><?php echo htmlspecialchars($v['nom']);?> :</label>

                    <div class="div_champ_secto_site">
                        <?php
                        foreach ($ListDep as $ListDepKey => $ListDepVal){?>
                            <div class="depMyProfil"><?php echo htmlspecialchars($ListDepVal[0]->code_vc); ?></div>
                        <?php  } ?>
                    </div>
                    <br/>
                <?php }
            }
        }
        ?>

        <div class="div_edit_button">

            <?php if(isset($_SESSION['Droit']) && isset($tabXlsFields['Profil']['profilMenu'][$_SESSION['Droit']]['modifier']) &&  $tabXlsFields['Profil']['profilMenu'][$_SESSION['Droit']]['modifier'] == 1){ ?>
                <button class="btn btn-danger">Modifier</button>
            <?php }else{ ?>
                <button class="btn btn-danger" disabled ="disabled">Modifier</button>
            <?php } ?>

        </div>

    </form>
    <br/>
<?php } else{ ?>
    <p>Accès refusé.</a></p>
<?php }?>

<br/>


<?php if(isset($_SESSION['Droit']) && $debugMode == 1){ ?>
    <!-- Footer -->
    <footer class="sticky-footer bg-white">
        <div class="text-center my-auto">
            <span class="lien_debug" onClick="AfficherMasquer()"><a id="debug" class="debug" href="#"">Infos SQL</a></span>
        </div>
        <div id="menu_debug" class="menu_debug">
            <ul class="list-group">
                <li class="list-group-item"><strong>Temps de la requête :</strong> <?php echo htmlspecialchars($delai) ?> secondes.</li>
                <?php if(isset($_SESSION['debug']['Update']) && $_SESSION['debug']['Update'] != null){ ?>
                    <li class="list-group-item"><strong>Requête Update:</strong> <?php echo htmlspecialchars($_SESSION['debug']['Update']); ?> </li>
                    <?php

                    $_SESSION['debug']['Update'] = null;

                } ?>

                <?php if(isset($_SESSION['debug']['Insert']) && $_SESSION['debug']['Insert'] != null){ ?>
                    <li class="list-group-item"><strong>Requête Insert:</strong> <?php echo htmlspecialchars($_SESSION['debug']['Insert']); ?> </li>
                    <?php

                    $_SESSION['debug']['Insert']=null;
                } ?>
                <li class="list-group-item"><strong>Requête :</strong> <?php echo htmlspecialchars($req); ?> </li>
                <?php if(isset($requpdate) && $requpdate != null){?>
                    <li class="list-group-item"><strong>Requête Update :</strong> <?php echo htmlspecialchars($requpdate); ?> </li>
                <?php  } ?>
                <?php if(isset($fileDonneesREQ) && $fileDonneesREQ != null && $fileDonnees != null){?>
                    <li class="list-group-item"><strong>Requête File :</strong> <?php echo htmlspecialchars($fileDonneesREQ); ?> </li>
                <?php } ?>
                <?php if(isset($reqOptionsP) && !empty($reqOptionsP)){
                    foreach ($reqOptionsP as $reqOptionKey => $reqOptionVal){ ?>
                        <li class="list-group-item"><strong>Requête Options :</strong> <?php echo htmlspecialchars($reqOptionVal); ?> </li>
                    <?php  }
                } ?>
            </ul>

        </div>
    </footer>
<?php } ?>
