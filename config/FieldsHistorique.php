<?php
function listParamHistorique(){
    $tabXlsFields = array();
    $tabXlsFields["Historique"] = array(
        "bdd_id" => "id",
        "bdd_table" => "historique",
        "nom_menu_groupe" => "Admin",
        "nom_feuille" => "Historique",
        "pagination" => array(1, 25, 50, 100, 250),


        12 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,),// cdt
        41 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// rgp
        9 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,),// cdp
        56 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,),// tech
        50 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,),// SOA
        45 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,),// rrh
        46 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,),// rrha
        50 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,),// SOA


        "profilMenu" => array(
            10000 => array( // SUPERADMIN
                "lecture" => 1,
            ),
            1 => array( // admin
                "lecture" => 1,
            ),
            12 => array( // cdt
                "lecture" => 1,
            ),
            41 => array( // rgp
                "lecture" => 1,
            ),
			9 => array( // cdp
                "lecture" => 1,
            ),
            45 => array( // rrh
                "lecture" => 1,
            ),
            46 => array( // rrha
                "lecture" => 1,
            ),
		),
		
        "profilSql"=>array(
            10000 => array( // SUPERADMIN
                "req_sql" => 0,
                "requete" => "",
                "order" => 0,
                "orderBy" => "",
            ),
            1 => array( // admin
                "req_sql" => 0,
                "requete" => "",
                "order" => 0,
                "orderBy" => "",
            ),
            12 => array( // cdt
                "req_sql" => 0,
                "requete" => "",
                "order" => 0,
                "orderBy" => "",
            ),
			41 => array( // rgp
                "req_sql" => 0,
                "requete" => "",
                "order" => 0,
                "orderBy" => "",
            ),
			9 => array( // cdp
                "req_sql" => 0,
                "requete" => "",
                "order" => 0,
                "orderBy" => "",
            ),
            45 => array( // rh
                "req_sql" => 0,
                "requete" => "",
                "order" => 0,
                "orderBy" => "",
            ),
            46 => array( // rrha
                "req_sql" => 0,
                "requete" => "",
                "order" => 0,
                "orderBy" => "",
            ),
        ),

        "champs" => array(
            "id" => array(
                "nom" => "ID", // Label affiché dans la vue.
                "type_controle" => "int", // Verification du type de champs dans ControleObjetConfig (controle la conformité du champs avant insertion en bdd)
                "type"=>"int", // Indique le type de champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 11, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "filtre" => 0,
                "recherche" => 0,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0, // // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "liste_style" => "", // Applique un style css à ce champ.

                "profilChamps"=>array(
                    10000 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,), // SUPERADMIN
                    1 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,),// admin
                    12 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,),// cdt
                    41 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,), // rgp
                    9 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,), // cdp
                    45 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,), // rh
                    46 => array("lecture" => 0, "ecriture" => 0, "modification" => 0,), // rrha
                ),
            ),
            "id_user" => array(
                "nom" => "Id de l'utilisateur", // Label affiché dans la vue.
                "type_controle" => "int", // Verification du type de champs dans ControleObjetConfig (controle la conformité du champs avant insertion en bdd)
                "type"=>"int", // Indique le type de champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 11, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "filtre" => 0,
                "recherche" => 0,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0, // // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 0, // Détermine si le champs est affiché dans le détail(vue).
                "liste_style" => "", // Applique un style css à ce champ.

                "profilChamps"=>array(

                    10000 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // SUPERADMIN
                    1 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,),// admin
                    12 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,),// cdt
                    41 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // rgp
                    9 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // cdp
                    45 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // rh
                    46 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // rrha
                ),
            ),
            "ip_user" => array(
                "nom" => "Adresse IP", // Label affiché dans la vue.
                "type_controle" => "varchar", // Verification du type de champs dans ControleObjetConfig (controle la conformité du champs avant insertion en bdd)
                "type" => "varchar", // Indique le type de champ.
                "taille_min" => 8, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 100, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "filtre" => 0,
                "recherche" => 0,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 0, // Détermine si le champs est affiché dans le détail(vue).
                "liste_style" => "", // Applique un style css à ce champ.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // SUPERADMIN
                    1 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,),// admin
                    12 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,),// cdt
                    41 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // rgp
                    9 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // cdp
                    45 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // rh
                    46 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // rrha
                ),
            ),
            "id_item" => array(
                "nom" => "Id de l'item", // Label affiché dans la vue.
                "type_controle" => "int", // Verification du type de champs dans ControleObjetConfig (controle la conformité du champs avant insertion en bdd)
                "type"=>"int", // Indique le type de champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 11, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "filtre" => 0,
                "recherche" => 0,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 0, // // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 0, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 0, // Détermine si le champs est affiché dans le détail(vue).
                "liste_style" => "", // Applique un style css à ce champ.

                "profilChamps"=>array(
                    10000 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // SUPERADMIN
                    1 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,),// admin
                    12 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,),// cdt
                    41 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // rgp
                    9 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // cdp
                    45 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // rh
                    46 => array("lecture" => 0, "ecriture" => 1, "modification" => 1,), // rrha
                ),
            ),
            "date_modification" => array(
                "nom" => "Modifié le", // Label affiché dans la vue.
                "type_controle" => "date", // Verification du type de champs dans ControleObjetConfig (controle la conformité du champs avant insertion en bdd)
                "type" => "date", // Indique le type de champ.
                "taille_min" => 14, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 20, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "filtre" => 0,
                "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "liste_style" => "", // Applique un style css à ce champ.

                "date_now" => 1,

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,), // SUPERADMIN
                    1 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,),// admin
                    12 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,),// cdt
                    41 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,), // rgp
                    9 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,), // cdp
                    45 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,), // rh
                    46 => array("lecture" => 1, "ecriture" => 0, "modification" => 0,), // rrha
                ),
            ),
            "cle_conf" => array(
                "nom" => "Clé Conf", // Label affiché dans la vue.
                "type_controle" => "varchar", // Verification du type de champs dans ControleObjetConfig (controle la conformité du champs avant insertion en bdd)
                "type" => "varchar", // Indique le type de champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 100, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "filtre" => 0,
                "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "liste_style" => "", // Applique un style css à ce champ.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // SUPERADMIN
                    1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// admin
                    12 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// cdt
                    41 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // rgp
                    9 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // cdp
                    45 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // rh
                    46 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // rrha
                ),
            ),
            "value_modif" => array(
                "nom" => "Valeur modifiée(s)", // Label affiché dans la vue.
                "type_controle" => "varchar", // Verification du type de champs dans ControleObjetConfig (controle la conformité du champs avant insertion en bdd)
                "type" => "text", // Indique le type de champ.
                "taille_min" => 1, // Nombre minimum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "taille_max" => 65000, // Nombre maximum pour le controle de la valeur de la clef du champ pour les formulaires edit et add.
                "obligatoire" => 1, // Détermine si le champ est obligatoire lors d'une nouvel insertion de tuple en base de données.
                "filtre" => 0,
                "recherche" => 1,  // Détermine si le champ fait partie du concat (prise en compte dans la barre de recherche).
                "liste" => 1, // Détermine si le champ est affiché dans le tableau(vue).
                "liste_tri" => 1, // Permet que le champ soit prit en compte pour un tri par ordre ASC ou DESC.
                "liste_detail" => 1, // Détermine si le champs est affiché dans le détail(vue).
                "liste_style" => "", // Applique un style css à ce champ.

                "profilChamps"=>array(
                    10000 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // SUPERADMIN
                    1 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// admin
                    12 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,),// cdt
                    41 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // rgp
                    9 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // cdp
                    45 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // rh
                    46 => array("lecture" => 1, "ecriture" => 1, "modification" => 1,), // rrha
                ),
            ),
        ),
    );
    return $tabXlsFields;
}
?>
