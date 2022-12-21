<?php
$tabXlsFields["test"] = array(
    "bdd_id" => "id",
    "bdd_table" => "test",
    "bdd_table_file" => "file_moteur",
    "nom_feuille" => "Test",
    "GroupeMenu" => "EVALUATION",
    "SousGroupeMenu" => "EVALUATION",
    "show_menu" => 1,
    "pagination" => array(10, 25, 50, 100, 250),

    "profilMenu" => array(
        10000 => array(
            "lecture" => 1,
            "ecriture" => 1,
            "delete" => 1,
            "modifier" => 1,
            "filtres" => array(
                // Exemple filtre entre deux dates 'date_min et date_max'
                "Date min" => array(
                    "active_view"=> 1, // Activer le filtre sur la vue
                    "champ_type" =>"date_min", // Obligatoire : prend soit 'date_min' ou 'date_max'
                    "champ_filtre"=>"date", // Champs à filtrer
                ),
                "Date max" => array(
                    "active_view"=> 1, // Activer le filtre sur la vue
                    "champ_type" =>"date_max", // Obligatoire : prend soit 'date_min' ou 'date_max'
                    "champ_filtre"=>"date", // Champs à filtrer
                ),

                // Exemple filtre une seule date = 'date_min'
                "DateTime Filtre" => array(
                    "active_view"=> 1, // Activer le filtre sur la vue
                    "champ_type" =>"date_min", // Obligatoire : prend 'date_min'
                    "champ_filtre"=>"datetime", // Champs à filtrer
                ),

                // Filtre SELECT
                "Profil" => array(
                    "active_view"=> 1,
                    // requete directe de selection des choix possibles avec en valeur fixe id et valeur
                    "sql"=>"SELECT id AS id,name AS valeur FROM test2",
                    // champ impliqué dans le filtrage
                    "champ_filtre"=>"id_test2",
                ),

            ),
            ),// SUPERADMIN
        1 => array("lecture" => 1, "ecriture" => 1, "delete" => 1, "modifier" => 1,),// ADMIN

    ),

    "profilSql" => array(
        10000 => array("req_sql" => 0, "requete" => "", "order" => 1, "orderBy" => "",),// SUPERADMIN
        1 => array("req_sql" => 0, "requete" => "", "order" => 1, "orderBy" => "",),// ADMIN
    ),

    "champs" => array(
        "id" => array(
            "nom" => "ID", // Label affiché dans la vue.
            "type_input" => "number",  // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
            "taille_min" => 1, // Nombre minimum pour le contrôle de la valeur de la clef du champ pour les formulaires edit et add.
            "taille_max" => 999999999, // Nombre maximum pour le contrôle de la valeur de la clef du champ pour les formulaires edit et add.
            "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
            "unique" => 1, // Détermine si le champs est unique en base de données et refuse l'insertion si la valeur du champ existe déjà dans un tuple.
            "liste" => 0, // Détermine si le champ est affiché dans le tableau(vue).
            "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
            "liste_style" => "width: 120px;", // Applique un style css à ce champ.
            "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).

            "profilChamps" => array(
                10000 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,),// SUPERADMIN
                1 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,),// ADMIN

            ),
        ),
        "id_test2" => array(
            "nom" => "id_test2", // Label affiché dans la vue.
            "type_input" => "number",  // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
            "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "taille_max" => 99999, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
            "controle_balise" => 1, // Détermine si le champs a le droit de comporté des chevrons ou balise.
            "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
            "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
            "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
            "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
            "liste_style" => "width :500px;", // Applique un style css à ce champ.
            "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
            "editable_list"=> 1, // Détermine si le champs est éditable depuis la vue LIST.


            // Dans le cas ou le champ est liée à un une table(jointure).
            "type"=>"list_sql", // Indique si c'est un champ qui dépend d'une jointure de table.
            "bdd_id"=>"id", // Nom de l'id à indiquer permettant de ne pas à avoir l'id de selectionné dans la requete(ainsi que dans l'affichage).
            "bdd_value"=>"name", // Nom du champs dans la table qui est relié au champs principal.
            "bdd_table"=>"test2", // Nom de la table à joindre au champ principal.
            "bdd_table_t"=>"test2_alias", // Alias de la table à joindre au champ principal.
            "bdd_ordre"=>"test2_alias ASC", // Permet d'indiquer le Order By pour l'affichage des select ou des champs autocompléte.
            "autocomplete"=>1, // Transforme le champs select par default en un champ de saisie libre avec autocomplétion.
            "maxRows" => 10, // Détermine le nombre d'entrées affichées dans l'autocomplétion.

            "profilChamps" =>array(
                10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1, "ajax" =>0, "req_ajax" => " ORDER BY name DESC",),// SUPERADMIN
                1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1, "ajax" =>0, "req_ajax" => " ORDER BY name DESC",),// ADMIN
            ),
        ),
        "name" => array(
            "nom" => "name", // Label affiché dans la vue.
            "type_input" => "text",  // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
            "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "taille_max" => 190, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
            "controle_balise" => 1, // Détermine si le champs a le droit de comporté des chevrons ou balise.
            "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
            "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
            "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
            "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
            "liste_style" => "width :auto;", // Applique un style css à ce champ.
            "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
            "unique"=> 0,
            "autocomplete"=>1,
            "editable_list"=> 1, // Détermine si le champs est éditable depuis la vue LIST.
            "profilChamps" => array(
                10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN
                1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// ADMIN

            ),
        ),

        "testNumber" => array(
            "nom" => "testNumber", // Label affiché dans la vue.
            "type_input" => "number",  // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
            "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "taille_max" => 20, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
            "controle_balise" => 1, // Détermine si le champs a le droit de comporté des chevrons ou balise.
            "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
            "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
            "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
            "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
            "liste_style" => "width :auto;", // Applique un style css à ce champ.
            "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
            "editable_list"=> 1, // Détermine si le champs est éditable depuis la vue LIST.
            "unique"=> 0,
            "profilChamps" => array(
                10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN
                1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// ADMIN

            ),
        ),
        "email" => array(
            "nom" => "email", // Label affiché dans la vue.
            "type_input" => "email",  // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
            "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "taille_max" => 190, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
            "controle_balise" => 1, // Détermine si le champs a le droit de comporté des chevrons ou balise.
            "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
            "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
            "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
            "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
            "liste_style" => "width :auto;", // Applique un style css à ce champ.
            "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
            "editable_list"=> 1, // Détermine si le champs est éditable depuis la vue LIST.
            "unique"=> 0,
            "profilChamps" => array(
                10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN
                1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// ADMIN

            ),
        ),
        "phone" => array(
            "nom" => "phone", // Label affiché dans la vue.
            "type_input" => "phone",  // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
            "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "taille_max" => 20, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
            "controle_balise" => 1, // Détermine si le champs a le droit de comporté des chevrons ou balise.
            "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
            "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
            "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
            "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
            "liste_style" => "width :auto;", // Applique un style css à ce champ.
            "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
            "editable_list"=> 1, // Détermine si le champs est éditable depuis la vue LIST.            "unique"=> 0,
            "profilChamps" => array(
                10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN
                1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// ADMIN

            ),
        ),
        "textarea" => array(
            "nom" => "textarea", // Label affiché dans la vue.
            "type_input" => "textarea",  // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
            "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "taille_max" => 190, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
            "controle_balise" => 1, // Détermine si le champs a le droit de comporté des chevrons ou balise.
            "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
            "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
            "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
            "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
            "liste_style" => "width :auto;", // Applique un style css à ce champ.
            "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
            "editable_list"=> 1, // Détermine si le champs est éditable depuis la vue LIST.
            "unique"=> 0,
            "profilChamps" => array(
                10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN
                1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// ADMIN

            ),
        ),
        "date" => array(
            "nom" => "date", // Label affiché dans la vue.
            "type_input" => "date",  // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
            "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "taille_max" => 190, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
            "controle_balise" => 1, // Détermine si le champs a le droit de comporté des chevrons ou balise.
            "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
            "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
            "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
            "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
            "liste_style" => "width :auto;", // Applique un style css à ce champ.
            "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
            "editable_list"=> 1, // Détermine si le champs est éditable depuis la vue LIST.            "unique"=> 0,
            "profilChamps" => array(
                10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN
                1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// ADMIN

            ),
        ),
        "datetime" => array(
            "nom" => "datetime", // Label affiché dans la vue.
            "type_input" => "datetime",  // Détermine le type d'input affiché dans les formulaires et le type de contrôle du champs.
            "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "taille_max" => 190, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
            "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
            "controle_balise" => 1, // Détermine si le champs a le droit de comporté des chevrons ou balise.
            "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
            "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
            "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
            "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
            "liste_style" => "width :auto;", // Applique un style css à ce champ.
            "csv" => 1, // Détermine si il fait partie des champs affichés dans l'export csv.
            "editable_list"=> 1, // Détermine si le champs est éditable depuis la vue LIST.
            "unique"=> 0,
            "profilChamps" => array(
                10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// SUPERADMIN
                1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// ADMIN

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
            "liste_style" => "width :auto;", // Applique un style css à ce champ.

            "profilChamps"=>array(
                10000 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,), // SUPERADMIN
                1 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,),// ADMIN
                3 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,), // RR
                4 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,),// VPI
            ),
        ),
    ),

);
