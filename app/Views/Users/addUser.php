        <div id="content-wrapper">
                <div class="container-fluid">

                    <br/>

                    <!-- Verification dans la conf que le profil user à accès au formulaire ------------------------------------------------->
                    <?php if(isset($_SESSION['Droit']) && $tabXlsFields[$_GET['XLS']]['profilMenu'][$_SESSION['Droit']]['ecriture'] == 1) { ?>
                        <?php if(!empty($errors) && $errors != null): ?>
                            <div class="alert alert-danger">
                                <p>Vous n'avez pas rempli le formulaire correctement :</p>
                            </div>
                        <?php endif; ?>
                        <form method="post" enctype="multipart/form-data">
                            <?php
                            foreach ($champs as $key => $v){



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



                                // Verification dans la conf que le champ est en ecriture pour le user  ----
                                if($v['profilChamps'][$_SESSION['Droit']]['ecriture'] == 1 && !isset($v['bdd_table_multiple_input']) && $v['type_input'] != 'categorie' && $v['type_input'] != 'sous_categorie'){
                                    if(isset($v['type']) && $v['type'] == "list_sql" && $v['autocomplete'] == 1){
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- Autocomplétion avec parcours de l'objet errors  ------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                        if(!empty($errors) && $errors != null){
                                            foreach($errors->errors as $error => $e):
                                                if($error != null && $key == $error){?>
                                                    <?php echo $form->inputWithIdAutocomp('inputItem'.$v['bdd_table_t'], $v['bdd_value'], $v['nom'].' '. $e, $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);?>
                                                    <?php if (isset($_POST[$v['bdd_value']])){ ?>
                                                        <fieldset disabled>
                                                            <div class="form-group champHidAutoComp">
                                                                <label>Label :</label>
                                                                <input disabled="true" class="form-control" type="text" id="labelDisable<?php echo $v['bdd_table_t'] ;?>" value="<?php echo htmlspecialchars($_POST[$v['bdd_value']]); ?>">
                                                            </div>
                                                        </fieldset>
                                                        <hr>
                                                        <input type="hidden" name="item<?php echo htmlspecialchars($key) ;?>" id="labelhidden<?php echo $v['bdd_table_t'] ;?>" value="<?php echo htmlspecialchars($_POST['item'.$key]); ?>">
                                                    <?php } else { ?>
                                                        <fieldset disabled>
                                                            <div class="form-group champHidAutoComp">
                                                                <?php echo $form->inputWithId('labelDisable'.$v['bdd_table_t'], 'disable', 'Label :', $v['taille_min'], $v['taille_max']);?>
                                                            </div>
                                                        </fieldset>
                                                        <hr>
                                                        <input type="hidden" name="item<?php echo htmlspecialchars($key) ;?>" id="labelhidden<?php echo $v['bdd_table_t'] ;?>" />
                                                    <?php }
                                                }
                                            endforeach;
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- Autocomplétion sans l'objet errors  ------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                        }else { ?>
                                            <?php echo $form->inputWithIdAutocomp('inputItem'.$v['bdd_table_t'], $v['bdd_value'], $v['nom'].' :', $v['bdd_table'], $v['bdd_value'], $_GET['XLS'], $v['bdd_table_t'], $v['taille_min'], $v['taille_max'], $key);?>
                                            <?php if (isset($_POST[$v['bdd_value']])){ ?>
                                                <fieldset disabled>
                                                    <div class="form-group champHidAutoComp">
                                                        <label>Label :</label>
                                                        <input disabled="true" class="form-control" type="text" id="labelDisable<?php echo $v['bdd_table_t'] ;?>" value="<?php echo htmlspecialchars($_POST[$v['bdd_value']]); ?>">
                                                    </div>
                                                </fieldset>
                                                <input type="hidden" name="item<?php echo htmlspecialchars($key) ;?>" id="labelhidden<?php echo $v['bdd_table_t'] ;?>" value="<?php echo htmlspecialchars($_POST['item'.$key]); ?>">
                                            <?php } else { ?>
                                                <fieldset disabled>
                                                    <div class="form-group champHidAutoComp">
                                                        <?php echo $form->inputWithId('labelDisable'.$v['bdd_table_t'], 'disable', 'Label :', $v['taille_min'], $v['taille_max']);?>
                                                    </div>
                                                </fieldset>
                                                <hr>
                                                <input type="hidden" name="item<?php echo htmlspecialchars($key) ;?>" id="labelhidden<?php echo $v['bdd_table_t'] ;?>" />
                                            <?php } ?>

                                            <?php
                                        }?>
                                    <?php }


//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- Select avec parcours de l'objet errors  -----------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                    elseif(isset($v['type']) && $v['type'] == "list_sql"){ ?>

                                        <?php  if(!empty($errors) && $errors != null){
                                            foreach($errors->errors as $error => $e):
                                                if($error != null && $key == $error){ ?>
                                                    <div class="form-group row">  <label class="col-sm-2 col-form-label" for="<?php echo $key; ?>"><?php echo $v['nom'].$e; ?></label> <div class="col-sm-10"> <select id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="form-control">
                                                                <?php if(isset($v['obligatoire']) && $v['obligatoire'] == 1){ }else{ ?>
                                                                    <option value="0"></option>
                                                                <?php } ?>
                                                                <?php
                                                                foreach($options as $o => $oo){
                                                                    if($key == $o) {
                                                                        foreach ($oo as $o_key => $o_value) { ?>
                                                                            <option value="<?php echo $o_value->{$v['bdd_id']}; ?>" <?php if (isset($_POST[$key]) && $_POST[$key] == $o_value->{$v['bdd_id']}) {
                                                                                echo 'selected="selected"';
                                                                            } ?>><?php echo $o_value->{$v['bdd_table_t']} ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                }
                                                                ?></select></div></div><hr><?php
                                                }
                                            endforeach;
                                        }
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-- Select sans l'objet errors  --------------------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                        else { ?>
                                            <div class="form-group row">  <label class="col-sm-2 col-form-label"  for="<?php echo $key; ?>"><?php echo $v['nom']; ?> :</label><div class="col-sm-10">   <select id="<?php echo $key; ?>" name="<?php echo $key; ?>" class="form-control">
                                                        <?php if(isset($v['obligatoire']) && $v['obligatoire'] == 1){ }else{ ?>
                                                            <option value="0"></option>
                                                        <?php } ?>
                                                        <?php
                                                        foreach($options as $o => $oo){
                                                            if($key == $o) {
                                                                foreach ($oo as $o_key => $o_value) { ?>
                                                                    <option value="<?php echo $o_value->{$v['bdd_id']}; ?>" <?php if (isset($_POST[$key]) && $_POST[$key] == $o_value->{$v['bdd_id']}) {
                                                                        echo 'selected="selected"';
                                                                    } ?>><?php echo $o_value->{$v['bdd_table_t']} ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                        } ?>
                                                    </select></div></div><hr><?php
                                        }?>
                                    <?php }else{
                                        if(!empty($errors) && $errors != null){
                                            foreach($errors->errors as $error => $e):
                                                if($error != null && $key == $error){
//-- input Number avec l'objet errors -----------------------------------------------------------------------------------------------------------------------------------------------------------
                                                    if($v['type_input'] == "number"){
                                                        echo $form->inputNumber(htmlspecialchars($key), htmlspecialchars($v['nom']).' '. $e, $v['taille_min'], $v['taille_max'], ['type' => $v['type_input']]);
//-- input DateTime avec l'objet errors ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                                    }elseif ($v['type_input'] == "datetime"){?>
                                                        <div>
                                                            <?php if(isset($_POST[$key])){
                                                                $date = explode(" ", $_POST[$key]);
                                                                $heur = explode(":", $date[1]);
                                                                $heurFormate = $heur[0].":".$heur[1]; ?>
                                                                <div class="form-group row">       <label class="col-sm-2 col-form-label"  for="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($v['nom']);?> <?php echo ' '. $e; ?></label><div class="col-sm-10">  <input id="<?php echo htmlspecialchars($key);?>" type="date" name="<?php echo htmlspecialchars($key);?>_date1" value="<?php echo htmlspecialchars($date[0]);?>" class="input_date"/> <label> à </label> <input id="<?php echo htmlspecialchars($key);?>" type="time" name="<?php echo htmlspecialchars($key);?>_date2" value="<?php echo htmlspecialchars($heurFormate);?>"  class="input_number"/></div></div>
                                                            <?php } else{ ?>
                                                                <div class="form-group row">      <label class="col-sm-2 col-form-label"  for="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($v['nom']);?> <?php echo ' '. $e; ?></label><div class="col-sm-10">  <input id="<?php echo htmlspecialchars($key);?>" type="date" name="<?php echo htmlspecialchars($key);?>_date1" class="input_date"/> <label> à </label> <input id="<?php echo htmlspecialchars($key);?>" type="time" name="<?php echo htmlspecialchars($key);?>_date2"   class="input_number"/></div></div>
                                                            <?php }?>
                                                        </div>
                                                        <?php
//-- input File avec l'objet errors ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                                    }elseif($v['type_input'] == "file"){?>
                                                        <div class="wrapper">
                                                            <div class="sections file_<?php echo htmlspecialchars($key); ?>">
                                                                <label><?php echo htmlspecialchars($v['nom']) ?> <em class="notificationMax"></em></label>
                                                                <section class="active">
                                                                    <div id="images" class="images">
                                                                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo htmlspecialchars($v['taille_max']); ?>">
                                                                        <input id="fileUpload" data-section="0"  data-nameInput="<?php echo htmlspecialchars($key); ?><?php if($v['nb_file'] > 0){echo '[]';}?>" data-nameExt="<?php echo htmlspecialchars($key); ?>" class="fileUpload" type="file" name="<?php echo htmlspecialchars($key); ?><?php if($v['nb_file'] > 0){echo '[]';}?>" data-nbFile="<?php echo htmlspecialchars($v['nb_file']); ?>" <?php if($v['nb_file'] > 0){echo 'multiple';}?>/>
                                                                        <input class="data_section" type="hidden" name="<?php echo htmlspecialchars($key); ?>_section" value="<?php if(isset($_POST[$key.'_section'])){echo htmlspecialchars($_POST[$key.'_section']);}else{ echo '0';};?>"/>
                                                                        <?php if(isset($_POST[$key])){
                                                                            foreach ($_POST[$key] as $file => $f){?>
                                                                                <?php foreach ($e as $eKey => $eVal){ ?>
                                                                                    <?php if($eKey == $_POST[$key.'_Name'][$file].'.'.$_POST[$key.'_ext'][$file] && $eVal != null){ ?>
                                                                                        <div class="img imgdelete" style="background-image: url('<?php if($_POST[$key.'_ext'][$file] != 'jpg'){echo '../public/image/forfile/' .$_POST[$key.'_ext'][$file]. '.png';}else{echo htmlspecialchars($f);} ?>'); margin-right: 10px;" rel="<?php echo htmlspecialchars($_POST[$key.'_Name'][$file]);?>"><span class="span_delete span_delete_add">Supprimer</span>
                                                                                            <input class="file_hidden" type="hidden" name="<?php echo htmlspecialchars($key); ?><?php if($v['nb_file'] > 0){echo '[]';}?>" value="<?php echo htmlspecialchars($_POST[$key.'_Name'][$file]);?>"/>
                                                                                            <input type="hidden" name="<?php echo htmlspecialchars($key.'_ext');  if($v['nb_file'] > 0){echo '[]';}?>" value="<?php echo htmlspecialchars($_POST[$key.'_ext'][$file]); ?>"/>
                                                                                            <input type="hidden" name="<?php echo htmlspecialchars($key.'_Name');  if($v['nb_file'] > 0){echo '[]';}?>" value="<?php echo htmlspecialchars($_POST[$key.'_Name'][$file]); ?>"/>
                                                                                            <?php if($eKey == $_POST[$key.'_Name'][$file].'.'.$_POST[$key.'_ext'][$file]){
                                                                                                $chaineCoupee = substr($_POST[$key.'_Name'][$file],0,10);?>
                                                                                                <span class="nom_file"><?php echo $eVal .'<br/>'.$chaineCoupee.'.'.$_POST[$key.'_ext'][$file]; ?></span>
                                                                                            <?php }?>
                                                                                        </div>
                                                                                    <?php }elseif($eKey == $_POST[$key.'_Name'][$file].'.'.$_POST[$key.'_ext'][$file] && $eVal == null){ ?>
                                                                                        <div class="img imgdelete" style="background-image: url('<?php if($_POST[$key.'_ext'][$file] != 'jpg'){echo '../public/image/forfile/' .$_POST[$key.'_ext'][$file]. '.png';}else{echo htmlspecialchars($f);} ?>'); margin-right: 10px;" rel="<?php echo htmlspecialchars($_POST[$key.'_Name'][$file]);?>"><span class="span_delete span_delete_add">Supprimer</span>
                                                                                            <input class="file_hidden" type="hidden" name="<?php echo htmlspecialchars($key); ?><?php if($v['nb_file'] > 0){echo '[]';}?>" value="<?php echo htmlspecialchars($f);?>"/>
                                                                                            <input type="hidden" name="<?php echo htmlspecialchars($key.'_ext');  if($v['nb_file'] > 0){echo '[]';}?>" value="<?php echo htmlspecialchars($_POST[$key.'_ext'][$file]); ?>"/>
                                                                                            <input type="hidden" name="<?php echo htmlspecialchars($key.'_Name');  if($v['nb_file'] > 0){echo '[]';}?>" value="<?php echo htmlspecialchars($_POST[$key.'_Name'][$file]); ?>"/>
                                                                                            <?php if($eKey == $_POST[$key.'_Name'][$file].'.'.$_POST[$key.'_ext'][$file]){
                                                                                                $chaineCoupee = substr($_POST[$key.'_Name'][$file],0,10);?>
                                                                                                <span class="nom_file"><em class="file_ok">Fichier OK.</em><br/><?php echo $chaineCoupee.'.'.$_POST[$key.'_ext'][$file]; ?></span>
                                                                                            <?php }?>
                                                                                        </div>
                                                                                    <?php    }?>
                                                                                    <?php
                                                                                } ?>
                                                                            <?php }
                                                                            if(isset($_POST[$key.'_section']) && $_POST[$key.'_section'] < $v['nb_file']){ ?>
                                                                                <div class="pic">Ajouter<br/>un fichier</div>
                                                                            <?php }else{ ?>
                                                                                <div style="display: none;" class="pic">Ajouter<br/>un fichier</div>
                                                                            <?php  } ?>
                                                                        <?php  }else{ ?>
                                                                            <div class="pic">Ajouter<br/>un fichier</div>
                                                                        <?php  } ?>
                                                                    </div>
                                                                </section>
                                                            </div>
                                                            <footer>
                                                                <ul>
                                                                    <li><span id="reset" class="reset"><i class="far fa-fw fa-trash-alt"></i></span></li>
                                                                </ul>
                                                            </footer>
                                                        </div>

                                                    <?php  }
//-- Autre input avec l'objet errors ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                                    else{
                                                        if(isset($v['type_input']) && $v['type_input'] != 'checkboxDep' && $v['type_input'] != 'inputMultiple') {
                                                            echo $form->input(htmlspecialchars($key), htmlspecialchars($v['nom']) . ' ' . $e, ['type' => $v['type_input'],'min' => $v['taille_min'], 'max' => $v['taille_max']]);

                                                        }
                                                    }
                                                    ?> <hr><?php
                                                }
                                            endforeach;
                                        }else {
//-- input Number sans l'objet errors -----------------------------------------------------------------------------------------------------------------------------------------------------------
                                            if($v['type_input'] == "number"){
                                                echo $form->inputNumber(htmlspecialchars($key), htmlspecialchars($v['nom']).' :', $v['taille_min'], $v['taille_max'], ['type' => $v['type_input']]);
//-- input DateTime sans l'objet errors  ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                            }elseif ($v['type_input'] == "datetime"){?>
                                                <div>
                                                    <?php if(isset($_POST[$key])){
                                                        $date = explode(" ", $_POST[$key]);
                                                        $heur = explode(":", $date[1]);
                                                        $heurFormate = $heur[0].":".$heur[1]; ?>
                                                        <div class="form-group row">   <label class="col-sm-2 col-form-label" for="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($v['nom']);?> :</label><div class="col-sm-10"><input id="<?php echo htmlspecialchars($key);?>" type="date" name="<?php echo htmlspecialchars($key);?>_date1" value="<?php echo htmlspecialchars($date[0]);?>" class="input_date"/> <label> à </label> <input id="<?php echo htmlspecialchars($key);?>" type="time" name="<?php echo htmlspecialchars($key);?>_date2" value="<?php echo htmlspecialchars($heurFormate);?>"  class="input_number"/></div></div>
                                                    <?php } else{ ?>
                                                        <div class="form-group row">   <label class="col-sm-2 col-form-label" for="<?php echo htmlspecialchars($key);?>"><?php echo htmlspecialchars($v['nom']);?> :</label><div class="col-sm-10"><input id="<?php echo htmlspecialchars($key);?>" type="date" name="<?php echo htmlspecialchars($key);?>_date1" class="input_date"/> <label> à </label> <input id="<?php echo htmlspecialchars($key);?>" type="time" name="<?php echo htmlspecialchars($key);?>_date2"   class="input_number"/></div></div>
                                                    <?php }?>
                                                </div>
                                            <?php }
//-- input File sans l'objet errors  ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                            elseif($v['type_input'] == "file"){?>
                                                <div class="wrapper">
                                                    <div class="sections file_<?php echo htmlspecialchars($key); ?>">
                                                        <label><?php echo htmlspecialchars($v['nom']).' :'; ?> <em class="notificationMax"></em></label>
                                                        <section class="active">
                                                            <div id="images" class="images">
                                                                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo htmlspecialchars($v['taille_max']); ?>">
                                                                <input id="fileUpload" class="fileUpload"  data-nameInput="<?php echo htmlspecialchars($key); ?><?php if($v['nb_file'] > 0){echo '[]';}?>" data-nameExt="<?php echo htmlspecialchars($key); ?>" type="file" name="<?php echo htmlspecialchars($key); ?><?php if($v['nb_file'] > 0){echo '[]';}?>" data-nbFile="<?php echo htmlspecialchars($v['nb_file']); ?>" data-section="0" <?php if($v['nb_file'] > 0){echo 'multiple';}?>/>

                                                                <input class="data_section" type="hidden" name="<?php echo htmlspecialchars($key); ?>_section" value="0"/>

                                                                <div class="pic">
                                                                    Ajouter<br/>un fichier
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </div>
                                                    <footer>
                                                        <ul>
                                                            <li><span id="reset" class="reset"><i class="far fa-fw fa-trash-alt"></i></span></li>
                                                        </ul>
                                                    </footer>
                                                </div>
                                            <?php  } else{
//-- Autre input sans l'objet errors ----------------------------------------------------------------------------------------------------------------------------------------------------------
                                                echo $form->input(htmlspecialchars($key), htmlspecialchars($v['nom']).' :', ['type' => $v['type_input'],'min' => $v['taille_min'], 'max' => $v['taille_max']]);
                                            }
                                            ?> <hr><?php
                                        }
                                    }
                                }


                                if(isset($v['type_input']) && $v['type_input'] == "inputMultiple"){ ?>
                                    <label for="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($v['nom']); ?> :</label>
                                    <div id="div_champ_secto_site" class="div_champ_secto_site">
                                        <?php
                                        if(isset($_POST[$key])){
                                            foreach ($_POST[$key] as $pKey => $pVal){?>
                                                <span class="span_site" data-item="<?php echo htmlspecialchars($_POST[$key.'_label'][$pKey]);?>"><?php echo htmlspecialchars($_POST[$key.'_label'][$pKey]);?><em class="delete_site">x</em><input type="hidden" id="inputHidden<?php echo htmlspecialchars($v['bdd_table_t']);?>" class="inputHiddenSite" value="<?php echo htmlspecialchars($pVal);?>" name="<?php echo htmlspecialchars($key);?>[]"/><input type="hidden" id="inputHidden<?php echo htmlspecialchars($v['bdd_table_t']);?>" class="inputHiddenSite" value="<?php echo htmlspecialchars($_POST[$key.'_label'][$pKey]);?>" name="<?php echo htmlspecialchars($key);?>_label[]"/></span>
                                                <?php
                                            } ?>
                                            <?php
                                        }elseif(!isset($_POST[$key])){
                                            if(isset($ListSecto) && !empty($ListSecto)) {
                                                foreach ($ListSecto as $ListSectoKey => $ListSectoVal) {?>
                                                    <span class="span_site" data-item="<?php echo htmlspecialchars($ListSectoVal[0]->code_site);?>"><?php echo htmlspecialchars($ListSectoVal[0]->code_site);?><em class="delete_site">x</em><input type="hidden" id="inputHidden<?php echo htmlspecialchars($v['bdd_table_t']);?>" class="inputHiddenSite" value="<?php echo htmlspecialchars($ListSectoVal[0]->id);?>" name="<?php echo htmlspecialchars($key);?>[]"/><input type="hidden" id="inputHidden<?php echo htmlspecialchars($v['bdd_table_t']);?>" class="inputHiddenSite" value="<?php echo htmlspecialchars($ListSectoVal[0]->code_site);?>" name="<?php echo htmlspecialchars($key);?>_label[]"/></span>
                                                <?php }
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php  echo $form->inputWithIdAutocompPrincipalInputMultiple('inputItem' . $v['bdd_table_multiple_input'], 'inputItem', $v['bdd_table_multiple_input'], $v['bdd_value'], $v['bdd_table_t'], $key); ?>

                                <?php }

                                if(isset($v['type_input']) && $v['type_input'] == "checkboxDep"){ ?>
                                    <hr>
                                    <label><?php echo htmlspecialchars($v['nom']); ?> :</label><br/>
                                    <div class="div_champ_secto_site">
                                        <div class="div_checkbox_dep">
                                            <?php foreach ($ListDep as $ListDepKey => $ListDepVal){?>
                                                <div <?php if(isset ($ListDepAttribarray[$ListDepVal->{$v['bdd_id']}]) && $ListDepAttribarray[$ListDepVal->{$v['bdd_id']}] == $ListDepVal->{$v['bdd_id']}){echo 'style="background-color: #81BAE0; color: #ffffff;"';}?>><input id="<?php echo htmlspecialchars($ListDepVal->{$v['bdd_id']});?>" type="checkbox" name="<?php echo htmlspecialchars($key); ?>[]" value="<?php echo htmlspecialchars($ListDepVal->{$v['bdd_id']});?>" <?php if(isset ($ListDepAttribarray[$ListDepVal->{$v['bdd_id']}]) && $ListDepAttribarray[$ListDepVal->{$v['bdd_id']}] == $ListDepVal->{$v['bdd_id']}){echo 'checked';}?>/> <label for="<?php echo htmlspecialchars($ListDepVal->{$v['bdd_id']});?>"><?php echo htmlspecialchars($ListDepVal->{$v['label_value']});?></label></div>
                                            <?php  } ?>
                                        </div>

                                    </div>
                                    <hr>
                                <?php }


                            }
                            ?>
                            <br/>
                            <div class="modal-footer">
                                <button id="send" style="margin:auto;" class="btn btn-danger">Ajouter</button>
                            </div>
                        </form>
                        <br/>
                    <?php } else{ ?>
                        <p>Accès refusé.</a></p>
                    <?php }?>
                </div>
            </div>
        <?php /*
        <!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!-- Debug Mode ------------------------------------------------------------------------------------------------------------------------------------------------------------->
        <!--------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
        */?>

        <?php if(isset($_SESSION['Droit'])  && $debugMode == 1){ ?>
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

                        <?php if(isset($reqOptionsP) && !empty($reqOptionsP)){
                            foreach ($reqOptionsP as $reqOptionKey => $reqOptionVal){ ?>
                                <li class="list-group-item"><strong>Requête Options :</strong> <?php echo htmlspecialchars($reqOptionVal); ?> </li>
                            <?php  }
                        } ?>
                    </ul>
                </div>
            </footer>
        <?php } ?>