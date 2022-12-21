<?php
function listParamUsers(){
    $tabXlsFields = array();
    $tabXlsFields["Users"] = array(
        "bdd_id" => "id",
        "bdd_table" => "users",
        "nom_menu_groupe" => "Admin",
        "nom_feuille" => "Utilisateurs",
        "bdd_table_file" => "file_moteur",
        "pagination" => array(10, 25, 50, 100, 250),

        "profilMenu" => array(
            10000 => array( // SUPERADMIN
                "lecture" => 1,
                "ecriture" => 1,
                "delete" => 1,
                "modifier" => 1,
                "index_page"=>"Principal.index",
                "myProfil" => 1,

                "filtres" => array(
                    "Profil" => array(
                        "active_view"=> 1,
                        // requete directe de selection des choix possibles avec en valeur fixe id et valeur
                        "sql"=>"SELECT id AS id,role_nom AS valeur FROM users_roles ORDER BY role_nom ASC",
                        // champ impliqué dans le filtrage
                        "champ_filtre"=>"roleId",
                    ),

                    // Exemple filtre entre deux dates 'date_min et date_max'
                    "Date min" => array(
                        "active_view"=> 0, // Activer le filtre sur la vue
                        "champ_type" =>"date_min", // Obligatoire : prend soit 'date_min' ou 'date_max'
                        "champ_filtre"=>"date_entree", // Champs à filtrer
                    ),
                    "Date max" => array(
                        "active_view"=> 0, // Activer le filtre sur la vue
                        "champ_type" =>"date_max", // Obligatoire : prend soit 'date_min' ou 'date_max'
                        "champ_filtre"=>"date_entree", // Champs à filtrer
                    ),

                ),
            ),

        ),

        "profilSql"=>array(
            10000 => array( // SUPERADMIN
                "req_sql" => 0,
                "requete" => "",
                "order" => 0,
                "orderBy" => " ORDER BY users.prenom DESC",
            ),

        ),

        "champs" => array(
            "id" => array(
                "nom" => "ID", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 2000000, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 0, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1,  //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 0, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 1, // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "", // Attribut un style css au champs dans la vue.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,),// SUPERADMIN


                ),
            ),
            "id_agent_sin3" => array(
                "nom" => "Agent sin3", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 0, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 10000000, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 0, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1,  //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "", // Attribut un style css au champs dans la vue.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN

                ),
            ),
            "actif" => array(
                "nom" => "Actif", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 0, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 1, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1,  //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 70px; text-align: center;", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                // Dans le cas ou le champ est liée à un une table(jointure).
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                "bdd_id"=>"id", // Indique le label de l'id de la table jointe à ce champ.
                "bdd_value"=>"label", // Indique le label du champ qui contient la donnée à afficher à la place.
                "bdd_table"=>"dichotomique", // Indique le nom de table de la table jointe au champ.
                "bdd_table_t"=>"actif_alias", // Indique l'alias de la table.
                "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                "bdd_ordre"=>"label ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>1, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1, "ajax" =>1, "req_ajax" => " ORDER BY label DESC",),// SUPERADMIN

                ),
            ),
            "nom" => array(
                "nom" => "Nom", // Label affiché dans la vue.
                "type_input" => "text", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 3, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 200, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 200px;", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN

                ),
            ),
            "prenom" => array(
                "nom" => "Prénom", // Label affiché dans la vue.
                "type_input" => "text", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 3, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 200, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 200px;", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN

                ),
            ),
            "login" => array(
                "nom" => "Login", // Label affiché dans la vue.
                "type_input" => "text", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 3, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 200, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1,  //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1,  // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 1,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN

                ),
            ),
            "email" => array(
                "nom" => "Mail", // Label affiché dans la vue.
                "type_input" => "email", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 6, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 255, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN

                ),
            ),
            "password" => array(
                "nom" => "Mot de passe", // Label affiché dans la vue.
                "type_input" => "password", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 5, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 12, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 0, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "", // Attribut un style css au champs dans la vue.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN

                ),
            ),
            "roleId" => array(
                "nom" => "Profil", // Label affiché dans la vue.
                "type_input" => "text", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 1, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                // Dans le cas ou le champ est liée à un une table(jointure).
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                "bdd_value"=>"role_nom", // Nom du champs dans la table qui est relié au champs principal.
                "bdd_table"=>"users_roles", // Nom de la table à joindre au champ principal.
                "bdd_table_t"=>"roleId_alias", // Alias de la table à joindre au champ principal.
                "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                "bdd_ordre"=>"role_nom ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>0, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1, "ajax" =>0, "req_ajax" => " role_nom DESC",),// SUPERADMIN

                ),
            ),
            "niveau" => array(
                "nom" => "Niveau", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 0, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 200, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 0, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1,  //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1,  // Détermine si il fait partie des champs affichés dans l'export csv.
                "liste_style" => "width:100px;", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN

                ),
            ),
            "etatId" => array(
                "nom" => "Etat", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 1, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                // Dans le cas ou le champ est liée à un une table(jointure).
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                "bdd_value"=>"etat_user_label", // Nom du champs dans la table qui est relié au champs principal.
                "bdd_table"=>"etat_user", // Nom de la table à joindre au champ principal.
                "bdd_table_t"=>"etatId_alias", // Alias de la table à joindre au champ principal.
                "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                "bdd_ordre"=>"etat_user_label ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>0, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1, "ajax" =>0, "req_ajax" => " ORDER BY etat_user_label DESC",),// SUPERADMIN

                ),
            ),
            "date_entree" => array(
                "nom" => "Date d'entrée", // Label affiché dans la vue.
                "type_input" => "date", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 14, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 20, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 155px; text-align: center;", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN


                ),
            ),
            "date_creation" => array(
                "nom" => "Ajouté le", // Label affiché dans la vue.
                "type_input" => "datetime", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 14, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 20, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 0, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1,  //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 185px;text-align: center;", // Attribut un style css au champs dans la vue.

                "date_now" => 1,

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,),// SUPERADMIN


                ),
            ),
            "backup1" => array(
                "nom" => "BackUp", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 0, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 0, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.

                "jointure_concat_list" => "CONCAT(responsable_alias.nom,' ',responsable_alias.prenom)",
                "jointure_concat" => "CONCAT(nom,' ',prenom)",

                // Dans le cas ou le champ est liée à un une table(jointure).
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                "bdd_value"=>"nom", // Nom du champs dans la table qui est relié au champs principal.
                "bdd_table"=>"users", // Nom de la table à joindre au champ principal.
                "bdd_table_t"=>"backup1_alias", // Alias de la table à joindre au champ principal.

                "bdd_condition"=> " users.actif = 1 AND users.roleId NOT IN (10000, 1)",// (isset($_GET['id'])) ? " users.actif = 1 AND responsable = (SELECT responsable FROM users WHERE id = ".$_GET['id'].") AND users.id != ".$_GET['id'] : " users.actif = 1 AND responsable = 0", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.

                "bdd_ordre"=>"backup1_alias ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>0, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.

                "profilChamps" =>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1, "ajax" =>0, "req_ajax" => " ORDER BY name DESC",),// SUPERADMIN

                ),
            ),
//            "SectoId" => array(
//                "nom" => "NRO affecté", // Label affiché dans la vue.
//                "type_input" => "checkboxNro", // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
//                "taille_min" => 0, // Nombre minimum pour le contrôle de la valeur de la clef du champ pour les formulaires edit et add.
//                "taille_max" => 9999999, // Nombre maximum d'octet pour l'upload(même multiple).
//                "liste_style" => "", // Applique un style css à ce champ.
//                // Dans le cas où le champ est liée à un une table(jointure).
//                "type" => "list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
//
//                "bdd_id" => "id", // Indique le label de l'id de la table jointe à ce champ.
//                "bdd_value" => "name", // Indique le label du champ qui contient la donnée à afficher à la place.
//                "label_value" => "name",
//                "bdd_table_multiple_input" => "nros", // Indique le nom de table de la table jointe au champ.
//                "bdd_table_t" => "SectoId_nros", // Indique l'alias de la table.
//                "bdd_condition" => "", // Permet d'indiquer une condition particulière en tapant directement une condition
//                "bdd_ordre" => " name ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocomplete.
//                "autocomplete" => 1, // Transforme le champs select par défaut en un champ de saisie libre avec autocomplétion.
//                "maxRows" => 5, // Détermine le nombre d'entrées affichées dans l'autocomplétion.
//
//                "profilChamps" =>array(
//                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1, "ajax" =>0, "req_ajax" => " ORDER BY name DESC",),// SUPERADMIN
//                    1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1, "ajax" =>0, "req_ajax" => " ORDER BY name DESC",),// ADMIN
//                    3 => array("lecture" => 1, "ecriture" => 0, "modification" => 0, "ajax" =>0, "req_ajax" => " ORDER BY name DESC",),// RR
//                    4 => array("lecture" => 1, "ecriture" => 0, "modification" => 0, "ajax" =>0, "req_ajax" => " ORDER BY name DESC",),// VPI
//                ),
//            ),
            "responsable" => array(
                "nom" => "N+1", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 0, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 0, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                "jointure_concat_list" => "CONCAT(responsable_alias.nom,' ',responsable_alias.prenom)",
                "jointure_concat" => "CONCAT(nom,' ',prenom)",

                // Dans le cas ou le champ est liée à un une table(jointure).
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                "bdd_value"=>"nom", // Nom du champs dans la table qui est relié au champs principal.
                "bdd_table"=>"users", // Nom de la table à joindre au champ principal.
                "bdd_table_t"=>"responsable_alias", // Alias de la table à joindre au champ principal.
                "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                "bdd_ordre"=>"responsable_alias ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>1, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.


                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 0, "modification" => 1, "ajax" =>2, "req_ajax" => " roleId !=10000 AND roleId != 1 AND roleId !=5 ORDER BY nom DESC",),// SUPERADMIN


                ),
            ),
            "responsable_n2" => array(
                "nom" => "N+2", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 0, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 0, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.
                "editable_list"=> 0,// Détermine si le champs est éditable depuis la vue LIST.

                "jointure_concat_list" => "CONCAT(responsable_n2_alias.nom,' ',responsable_n2_alias.prenom)",
                "jointure_concat" => "CONCAT(nom,' ',prenom)",

                // Dans le cas ou le champ est liée à un une table(jointure).
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                "bdd_value"=>"nom", // Nom du champs dans la table qui est relié au champs principal.
                "bdd_table"=>"users", // Nom de la table à joindre au champ principal.
                "bdd_table_t"=>"responsable_n2_alias", // Alias de la table à joindre au champ principal.
                "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                "bdd_ordre"=>"responsable_n2_alias ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>0, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.


                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 0, "modification" => 0, "ajax" =>2, "req_ajax" => " roleId !=10000 AND roleId != 1 AND roleId != 5  ORDER BY nom DESC",),// SUPERADMIN

                ),
            ),
            "hidden" => array(
                "bdd_id"=>"id",
                "nom" => "Delete", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 0, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 1, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 0, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 0, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 0, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 0, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "", // Attribut un style css au champs dans la vue.

                "profilChamps"=>array(
                    10000 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,), // SUPERADMIN

                ),
            ),

        ),
    );

    return $tabXlsFields;
}

?>
