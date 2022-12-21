<?php
function listParamUserProfil(){
    $tabXlsFields = array();
    $tabXlsFields["Profil"] = array(
        "bdd_id" => "id",
        "bdd_table" => "users_moteur",
        "nom_feuille" => "Mon Profil",
        "bdd_table_file" => "file_moteur",

        "profilMenu" => array(
            10000 => array( // SUPERADMIN
                "lecture" => 1,
                "ecriture" => 1,
                "delete" => 1,
                "modifier" => 1,
            ),
            1 => array( // ADMIN
                "lecture" => 1,
                "ecriture" => 1,
                "delete" => 1,
                "modifier" => 1,
            ),
            2 => array( // BE
                "lecture" => 0,
                "ecriture" => 0,
                "delete" => 0,
                "modifier" => 1,
            ),
        ),

        "profilSql"=>array(
            10000 => array( // SUPERADMIN
                "req_sql" => 0,
                "requete" => " AND users.id = 3 ",
                "order" => 0,
                "orderBy" => " ORDER BY users.prenom DESC",
            ),
            1 => array( // ADMIN
                "req_sql" => 1,
                "requete" => " AND users.roleId != 10000",
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
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                ),
            ),
            "nom" => array(
                "nom" => "Nom", // Label affiché dans la vue.
                "type_input" => "text", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 3, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 200, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.

                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1,  //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 200px;", // Attribut un style css au champs dans la vue.

                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
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

                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                ),
            ),
            "email" => array(
                "nom" => "Mail", // Label affiché dans la vue.
                "type_input" => "text", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 6, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 255, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 0, // Pour l'instant n'est pas exploité.
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1,  // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "", // Attribut un style css au champs dans la vue.

                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
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
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
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
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.

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
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => " ORDER BY etat_user_label DESC",
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => "ORDER BY etat_user_label DESC ",
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                ),
            ),
            "backup1" => array(
                "nom" => "Backup 1", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 1, // Pour l'instant n'est pas exploité.
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.

                // Dans le cas ou le champ est liée à un une table(jointure).
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.


                "reqPart" => "SELECT users.id, users.nom, users.prenom FROM users WHERE users.id != ".$_SESSION['UserID']." AND users.roleId = ".$_SESSION['Droit']." AND users.etatId = 1 AND users.actif = 1",

                "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                "bdd_value"=>"nom", // Nom du champs dans la table qui est relié au champs principal.
                "bdd_table"=>"users", // Nom de la table à joindre au champ principal.
                "bdd_table_t"=>"backup1_alias", // Alias de la table à joindre au champ principal.
                "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                "bdd_ordre"=>"nom ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>0, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.

                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => " ORDER BY nom DESC",
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => "ORDER BY nom DESC ",
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                ),
            ),
            "backup2" => array(
                "nom" => "Backup 2", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 1, // Pour l'instant n'est pas exploité.
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.


                "reqPart" => "SELECT users.id, users.nom, users.prenom FROM users WHERE users.id != ".$_SESSION['UserID']." AND users.roleId = ".$_SESSION['Droit']." AND users.etatId = 1 AND users.actif = 1",

                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                    "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                    "bdd_value"=>"nom", // Nom du champs dans la table qui est relié au champs principal.
                    "bdd_table"=>"users", // Nom de la table à joindre au champ principal.
                    "bdd_table_t"=>"backup2_alias", // Alias de la table à joindre au champ principal.
                    "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                    "bdd_ordre"=>"nom ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                    "autocomplete"=>0, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                    "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.

                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => " ORDER BY nom DESC",
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => "ORDER BY nom DESC ",
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                ),
            ),
            "backup3" => array(
                "nom" => "Backup 3", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 1, // Pour l'instant n'est pas exploité.
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.

                "reqPart" => "SELECT users.id, users.nom, users.prenom FROM users WHERE users.id != ".$_SESSION['UserID']." AND users.roleId = ".$_SESSION['Droit']." AND users.etatId = 1 AND users.actif = 1",
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                "bdd_value"=>"nom", // Nom du champs dans la table qui est relié au champs principal.
                "bdd_table"=>"users", // Nom de la table à joindre au champ principal.
                "bdd_table_t"=>"backu3_alias", // Alias de la table à joindre au champ principal.
                "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                "bdd_ordre"=>"nom ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>0, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.


                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => " ORDER BY nom DESC",
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => "ORDER BY nom DESC ",
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                ),
            ),
            "backup4" => array(
                "nom" => "Backup 4", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 1, // Pour l'instant n'est pas exploité.
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.


                "reqPart" => "SELECT users.id, users.nom, users.prenom FROM users WHERE users.id != ".$_SESSION['UserID']." AND users.roleId = ".$_SESSION['Droit']." AND users.etatId = 1 AND users.actif = 1",
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                "bdd_value"=>"nom", // Nom du champs dans la table qui est relié au champs principal.
                "bdd_table"=>"users", // Nom de la table à joindre au champ principal.
                "bdd_table_t"=>"backup4_alias", // Alias de la table à joindre au champ principal.
                "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                "bdd_ordre"=>"nom ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>0, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.


                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => " ORDER BY nom DESC",
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => "ORDER BY nom DESC ",
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                ),
            ),
            "backup5" => array(
                "nom" => "Backup 5", // Label affiché dans la vue.
                "type_input" => "number", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.(En rapport avec le nombre de tuple dans la table jointe)
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "controle_balise" => 1, //(Facultatif) Détermine si le champs peut contenir des chevron et balise.
                "filtre" => 1, // Pour l'instant n'est pas exploité.
                "recherche" => 1, // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
                "unique" => 0,  // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
                "liste_style" => "width: 150px;", // Attribut un style css au champs dans la vue.

                "reqPart" => "SELECT users.id, users.nom, users.prenom FROM users WHERE users.id != ".$_SESSION['UserID']." AND users.roleId = ".$_SESSION['Droit']." AND users.etatId = 1 AND users.actif = 1",
                "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
                "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
                "bdd_value"=>"nom", // Nom du champs dans la table qui est relié au champs principal.
                "bdd_table"=>"users", // Nom de la table à joindre au champ principal.
                "bdd_table_t"=>"backup5_alias", // Alias de la table à joindre au champ principal.
                "bdd_condition"=>"", // Permet d'indiquer une condition particulière en tapant directement une condition SQL.
                "bdd_ordre"=>"nom ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
                "autocomplete"=>0, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
                "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.
                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => " ORDER BY nom DESC",
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                        "ajax" =>0,
                        "req_ajax" => "ORDER BY nom DESC ",
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 1,
                        "modification" => 1,
                    ),
                ),
            ),

            "SectoId" => array(
                "nom" => "Site affecté", // Label affiché dans la vue.
                "type_input" => "inputMultiple", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                ),
            ),

            "dept" => array(
                "nom" => "Département affecté", // Label affiché dans la vue.
                "type_input" => "checkboxDep", // Détermine le type d'input et le type de controle qui est réservé au champ.
                "profilChamps"=>array(
                    10000 => array( // SUPERADMIN
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                    1 => array( // ADMIN
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                    2 => array( // BE
                        "lecture" => 1,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
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
                    10000 => array( // SUPERADMIN
                        "lecture" => 0,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                    1 => array( // ADMINs
                        "lecture" => 0,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                    2 => array( // BE
                        "lecture" => 0,
                        "ecriture" => 0,
                        "modification" => 0,
                    ),
                ),
            ),

        ),
    );
    return $tabXlsFields;
}
?>