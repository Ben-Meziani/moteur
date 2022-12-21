<?php

namespace App\Table;

use Core\Table\Table;

class Nom_TabTable extends Table
{

    // Recup l'adresse ip basique ou derrière un proxy.
    function getIpAdress()
    {
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return (string)self::is_ip_address(trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']))));
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return '';
    }

    public function lastID($table)
    {
        return $this->query("
			SELECT id FROM " . $table . " WHERE id = LAST_INSERT_ID();
		");
    }

    // Recup la requete nb d'item.
    public function countItem($profilSql, $table, $champs, $filtre, $label_id, $get_filtre = null, $champs_array_val = null)
    {

        // Récup des champs quand type = sql(jointure).
        $leftjoin = array();
        $alias = array();
        foreach ($champs as $key => $v) {
            if (isset($v['bdd_table']) && $v['bdd_table'] != null) {
                if ($v['type'] == "list_sql") {
                    if(isset($v['left_join_brut']) && $v['left_join_brut'] != '' ){
                        $leftjoin[] = $v['left_join_brut'];
                    }else{
                        $leftjoin[] = " LEFT JOIN " . $v['bdd_table'] . " as " . $v['bdd_table_t'] . "  ON " . $table . "." . $key . " = " . $v['bdd_table_t'] . "." . $v['bdd_id'];
                    }
                     if (isset($v['jointure_concat_list'])) {
                        $alias[] = $v['jointure_concat_list'] . " as " . $v['bdd_table_t'];
                    } else {
                        $alias[] = $v['bdd_table_t'] . "." . $v['bdd_value'] . " as " . $v['bdd_table_t'];
                    }
                }
            }
        }

        $reqSql = "SELECT COUNT(*) AS total FROM " . $table;

        if ($leftjoin) {
            $reqSql = $reqSql . implode(" ", $leftjoin);
        }

        // Récup des champs ou recherche = 1.
        $colConcat = array();
        foreach ($champs as $key => $v) {
            if (isset($v['recherche']) && $v['recherche'] == 1) {

                if (!isset($v['type']) || $v['type'] != 'list_sql') {
                    array_push($colConcat, $key);
                }
            }
        }
		
		

        $reqSql = $reqSql . " WHERE " . $table . ".hidden = 0 ";


        if (isset($filtre) && $filtre == '1') {
            $alreadyChecked = '';

            foreach ($get_filtre as $get_filtreKey => $get_filtreVal) {

				/*
                if (is_int($get_filtreVal) && $get_filtreVal != 0) {
                    $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . " =" . $get_filtreVal;
                }
				*/
                // Si on a autre filtre que Date
				if (trim($get_filtreVal)!="" && trim($get_filtreKey) != trim($champs_array_val[$get_filtreKey]."_date_min")
                    && trim($get_filtreKey) != trim($champs_array_val[$get_filtreKey]."_date_max")) {
                    if(strpos($get_filtreVal, 'hie_') !== false){
                        $reqSql = $reqSql . " AND " . $get_filtreVal ." = ".$_SESSION['UserID'];
                    }
                    else {
                        $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . " =" . intval($get_filtreVal);

                    }
                }
				// Si on a un filtre de type Date avec date_min et date_max
                elseif(trim($get_filtreVal)!="" && array_key_exists ($champs_array_val[$get_filtreKey]."_date_min",$get_filtre)
                    && array_key_exists ($champs_array_val[$get_filtreKey]."_date_max",$get_filtre)
                    && $alreadyChecked != $champs_array_val[$get_filtreKey]){

                    $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . " BETWEEN '".
                        $get_filtre[$champs_array_val[$get_filtreKey]."_date_min"] ." 00:00:00' AND '".$get_filtre[$champs_array_val[$get_filtreKey]."_date_max"]." 23:59:59'" ;

                        if( $champs_array_val[$get_filtreKey] == explode('_date_',$get_filtreKey)[0]){
                            $alreadyChecked = $champs_array_val[$get_filtreKey];
                        } else {
                            $alreadyChecked ='';
                        }
                }
                // Si on a un filtre de type Date avec seulement date_min
                elseif ( trim($get_filtreVal)!="" && array_key_exists ($champs_array_val[$get_filtreKey]."_date_min",$get_filtre)
                    && !array_key_exists ($champs_array_val[$get_filtreKey]."_date_max",$get_filtre)){

                    $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . "  like '".
                        $get_filtre[$champs_array_val[$get_filtreKey]."_date_min"] ."%'" ;

                }

            }

            if (!empty($_GET['search']) && strlen($_GET['search']) < 100) {

                $reqSql = $reqSql . " AND CONCAT (";
                foreach ($champs as $key => $v) {
                    if (isset($v['type']) && isset($v['recherche']) && $v['recherche'] == 1 && $v['type'] == 'list_sql') {
                        $reqSql = $reqSql . " IFNULL(" . $v['bdd_table_t'] . "." . $v['bdd_value'] . ", ' '),";
                    }
                }
                if(count($colConcat)>0) {
					$reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
					$reqSql = $reqSql . " ,' ') ) LIKE ? ";
				} else {
					$reqSql = trim($reqSql,",") . " ) LIKE ? ";
				}
            } elseif (!empty($_SESSION[$_GET['XLS']]['search']) && strlen($_SESSION[$_GET['XLS']]['search']) < 100 && !isset($_GET['search'])) {

                $reqSql = $reqSql . " AND CONCAT (";
                foreach ($champs as $key => $v) {
                    if (isset($v['type']) && isset($v['recherche']) && $v['recherche'] == 1 && $v['type'] == 'list_sql') {
                        $reqSql = $reqSql . " IFNULL(" . $v['bdd_table_t'] . "." . $v['bdd_value'] . ", ' '),";
                    }
                }
                if(count($colConcat)>0) {
					$reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
					$reqSql = $reqSql . " ,' ') ) LIKE ? ";
				} else {
					$reqSql = trim($reqSql,",") . " ) LIKE ? ";
				}
            }

        }
        if (isset($profilSql[$_SESSION['Droit']]['req_sql']) && $profilSql[$_SESSION['Droit']]['req_sql'] == 1) {

            $reqSql = $reqSql . $profilSql[$_SESSION['Droit']]['requete'];

        }

        return $reqSql;
    }

    // Recup le nb d'item.
    public function countAllItems($reqSql)
    {
		$tmpTimeStart=microtime(true);
		
        if (isset($_GET['search'])) {
            $req = $this->query($reqSql, ['%' . $_GET['search'] . '%'], false);
        } elseif (isset($_GET['XLS']) && !isset($_GET['search']) && isset($_SESSION[$_GET['XLS']]['search'])) {
            $req = $this->query($reqSql, ['%' . $_SESSION[$_GET['XLS']]['search'] . '%'], false);
        } else {
            $req = $this->query($reqSql);
        }
		
		
        return $req;

    }

    // Renvoie la page courante ou est affichée l'item.
    public function getCurrent($page, $nbPage)
    {
        if (isset($page) && !empty($page)) {
            if ($page > $nbPage) {
                $pageCurrent = $nbPage;
            } else {
                $pageCurrent = $page;
            }
        } else {
            $pageCurrent = 1;
        }
        return $pageCurrent;
    }

    // Recup la requete sql.
    public function getItemQuery($profilSql, $table, $champs, $pageCurrent, $ParPage, $filtre, $label_id, $get_filtre = null, $champs_array_val = null)
    {
		
		$tmpTimeStart=microtime(true);

		/*
        $premierePage = ($pageCurrent - 1) * $ParPage;
        if ($premierePage == -1) {
            $premierePage = 1;
        }
		*/
		$premierePage=max($pageCurrent-1,0)*$ParPage;
		

        // Récup des champs quand type = sql(jointure).
        $leftjoin = array();
        $alias = array();
        foreach ($champs as $key => $v) {
            if (isset($v['bdd_table']) && $v['bdd_table'] != null) {
                if ($v['type'] == "list_sql") {
                    if(isset($v['left_join_brut']) && $v['left_join_brut'] != '' ){

                        $leftjoin[] = $v['left_join_brut'];

                    }else{
                        $leftjoin[] = " LEFT JOIN " . $v['bdd_table'] . " as " . $v['bdd_table_t'] . "  ON " . $table . "." . $key . " = " . $v['bdd_table_t'] . "." . $v['bdd_id'];

                    }
                        if (isset($v['jointure_concat_list'])) {
                            $alias[] = $v['jointure_concat_list'] . " as " . $v['bdd_table_t'];
                        } else {
                            $alias[] = $v['bdd_table_t'] . "." . $v['bdd_value'] . " as " . $v['bdd_table_t'];
                        }
                }
            }
        }

        // Récup des champs ou liste = 1.
        $col = array();
        foreach ($champs as $key => $v) {
            if (isset($v['liste']) && $v['liste'] == 1 && isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                array_push($col, $key);
            }
            if(isset($v['profilChamps'][$_SESSION['Droit']]['read_only']) && $v['profilChamps'][$_SESSION['Droit']]['read_only'] == 1){
                array_push($col, $key);
            }
        }
        $col = "" . $table . "." . implode(", " . $table . ".", $col);


        if ($leftjoin) {
            $col .= ", " . implode(",", $alias);
        }

        $reqSql = "SELECT " . $table . "." . $label_id . "," . $col . " FROM " . $table;

        if ($leftjoin) {
            $reqSql = $reqSql . implode(" ", $leftjoin);
        }

        // Récup des champs ou recherche = 1.
        $colConcat = array();
        foreach ($champs as $key => $v) {
            if (isset($v['recherche']) && $v['recherche'] == 1) {
                if (!isset($v['type']) || $v['type'] != 'list_sql') {
                    array_push($colConcat, $key);
                }
            }
        }
        $reqSql = $reqSql . " WHERE " . $table . ".hidden = 0 ";

        if (isset($filtre) && $filtre == 1) {
            $alreadyChecked = '';
            foreach ($get_filtre as $get_filtreKey => $get_filtreVal) {

                /*
                if (is_int($get_filtreVal) && $get_filtreVal != 0) {
                    $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . " =" . $get_filtreVal;
                }
                */
                // Si on a autre filtre que Date


                if (trim($get_filtreVal)!="" && trim($get_filtreKey) != trim($champs_array_val[$get_filtreKey]."_date_min")
                    && trim($get_filtreKey) != trim($champs_array_val[$get_filtreKey]."_date_max")) {

                    if(strpos($get_filtreVal, 'hie_') !== false){
                        $reqSql = $reqSql . " AND " . $get_filtreVal ." = ".$_SESSION['UserID'];
                    }
                    else {
                        $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . " =" . intval($get_filtreVal);
                    }
                }
                // Si on a un filtre de type Date avec date_min et date_max
                elseif(trim($get_filtreVal)!="" && array_key_exists ($champs_array_val[$get_filtreKey]."_date_min",$get_filtre)
                    && array_key_exists ($champs_array_val[$get_filtreKey]."_date_max",$get_filtre)
                    && $alreadyChecked != $champs_array_val[$get_filtreKey]){

                    $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . " BETWEEN '".
                        $get_filtre[$champs_array_val[$get_filtreKey]."_date_min"] ." 00:00:00' AND '".$get_filtre[$champs_array_val[$get_filtreKey]."_date_max"]." 23:59:59'" ;

                    if( $champs_array_val[$get_filtreKey] == explode('_date_',$get_filtreKey)[0]){
                        $alreadyChecked = $champs_array_val[$get_filtreKey];
                    } else {
                        $alreadyChecked ='';
                    }
                }
                // Si on a un filtre de type Date avec seulement date_min
                elseif ( trim($get_filtreVal)!="" && array_key_exists ($champs_array_val[$get_filtreKey]."_date_min",$get_filtre)
                    && !array_key_exists ($champs_array_val[$get_filtreKey]."_date_max",$get_filtre)){

                    $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . "  like '".
                        $get_filtre[$champs_array_val[$get_filtreKey]."_date_min"] ."%'" ;

                }

            }


            if (!empty($_GET['search']) && strlen($_GET['search']) < 100) {

                $reqSql = $reqSql . " AND CONCAT (";
                foreach ($champs as $key => $v) {
                    if (isset($v['type']) && isset($v['recherche']) && $v['recherche'] == 1 && $v['type'] == 'list_sql') {
                        $reqSql = $reqSql . " IFNULL(" . $v['bdd_table_t'] . "." . $v['bdd_value'] . ", ' '),";
                    }
                }
				
				if(count($colConcat)>0) {
					$reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
					$reqSql = $reqSql . " ,' ') ) LIKE ? ";
				} else {
					$reqSql = trim($reqSql,",") . " ) LIKE ? ";
				}
				
                //$reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
                //$reqSql = $reqSql . " ,' ') ) LIKE ? ";
            } elseif (!empty($_SESSION[$_GET['XLS']]['search']) && strlen($_SESSION[$_GET['XLS']]['search']) < 100 && !isset($_GET['search'])) {

                $reqSql = $reqSql . " AND CONCAT (";
                foreach ($champs as $key => $v) {
                    if (isset($v['type']) && isset($v['recherche']) && $v['recherche'] == 1 && $v['type'] == 'list_sql') {
                        $reqSql = $reqSql . " IFNULL(" . $v['bdd_table_t'] . "." . $v['bdd_value'] . ", ' '),";
                    }
                }
                //$reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
                //$reqSql = $reqSql . " ,' ') ) LIKE ? ";
				
				if(count($colConcat)>0) {
					$reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
					$reqSql = $reqSql . " ,' ') ) LIKE ? ";
				} else {
					$reqSql = trim($reqSql,",") . " ) LIKE ? ";
				}
            }

        }
        if (isset($profilSql[$_SESSION['Droit']]['req_sql']) && $profilSql[$_SESSION['Droit']]['req_sql'] == 1) {

            $reqSql = $reqSql . $profilSql[$_SESSION['Droit']]['requete'];

        }


        // Récup des champs ou liste_tri = 1.
        $liste_tri = array();
        foreach ($champs as $key => $v) {

            if (isset($v['liste_tri']) && $v['liste_tri'] == 1) {


                if (isset($v['type']) && $v['type'] == 'list_sql') {
                    array_push($liste_tri, $v['bdd_table_t']);
                } else {
                    array_push($liste_tri, $key);
                }

            }


        }
//      var_dump($reqSql);

        if (!empty($_GET['order']) && !empty($_GET['champ'])) {
            if ($_GET['order'] == "ASC" || $_GET['order'] == "DESC") {
                foreach ($liste_tri as $champs_tri => $c_t) {
                    if ($_GET['champ'] == $c_t) {
                        $reqSql = $reqSql . " ORDER BY " . $_GET['champ'] . " " . $_GET['order'] . " ";
                    }
                }
            }
        } elseif (isset($_GET['XLS']) && !empty($_SESSION[$_GET['XLS']]['order']) && !empty($_SESSION[$_GET['XLS']]['champ']) && !isset($_GET['order']) && !isset($_GET['champ'])) {
            if ($_SESSION[$_GET['XLS']]['order'] == "ASC" || $_SESSION[$_GET['XLS']]['order'] == "DESC") {
                foreach ($liste_tri as $champs_tri => $c_t) {
                    if ($_SESSION[$_GET['XLS']]['champ'] == $c_t) {
                        $reqSql = $reqSql . " ORDER BY " . $_SESSION[$_GET['XLS']]['champ'] . " " . $_SESSION[$_GET['XLS']]['order'] . " ";
                    }
                }
            }
        } elseif (isset($profilSql[$_SESSION['Droit']]) && $profilSql[$_SESSION['Droit']]['order'] == 1) {
            $reqSql = $reqSql . $profilSql[$_SESSION['Droit']]['orderBy'];
        } else {
            //$reqSql = $reqSql . " ORDER BY " . $table . "." . $label_id . " DESC";//2
        }
        $reqSql .= " LIMIT " . abs($premierePage) . "," . abs($ParPage);


        return $reqSql;
    }

    // Recup tous les item.
    public function getAllItems($req)
    {
		$tmpTimeStart=microtime(true);
		$req0=$req;
		
        if (isset($_GET['search'])) {
            $req = $this->query($req, ['%' . $_GET['search'] . '%'], false);
        } elseif (isset($_GET['XLS']) && !isset($_GET['search']) && isset($_SESSION[$_GET['XLS']]['search'])) {
            $req = $this->query($req, ['%' . $_SESSION[$_GET['XLS']]['search'] . '%'], false);
        } else {
            $req = $this->query($req);
        }
		
		
        return $req;
    }

    public function getAllOldItem( $table, $champs, $relatedChamps,$requeteWhere){
        $col = array();
        $reqLeftJoin = '';
        if(isset($relatedChamps) && !empty($relatedChamps)){
            array_push($col, $table.'.'.$relatedChamps);
        }
        foreach ($champs as $key => $v) {
            if (isset($v['placeholder']) && $v['placeholder'] == 1 && isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                if(isset($v['type']) && $v['type'] == 'list_sql'){
                    $reqLeftJoin .= ' LEFT JOIN '.$v['bdd_table'].' AS '.$v['bdd_table_t'].' ON '.$v['bdd_table_t'].'.'.$v['bdd_id'].' = '.$table.'.'.$key;
                    array_push($col, $v['bdd_table_t'].'.'.$v['bdd_value'].' AS old_'.$key);
                } else {
                    array_push($col, $table.'.'.$key);
                }
            }
        }

        $col = implode(", " , $col);
        $reqSql = "SELECT "  . $col . " FROM " . $table . $reqLeftJoin." WHERE ".$table.".hidden = 0 ".$requeteWhere;
        $req = $this->query($reqSql);
        return $req;
}

    // Recup la requete de l' item par ID de l'item.
    public function findItemQuery($id, $table, $champs, $ID)
    {

        // Récup des champs quand type = sql(jointure).
        $leftjoin = array();
        $alias = array();
        foreach ($champs as $key => $v) {
            if (isset($v['bdd_table']) && $v['bdd_table'] != null) {
                if ($v['type'] == "list_sql") {
                    if(isset($v['left_join_brut']) && $v['left_join_brut'] != '' ){

                        $leftjoin[] = $v['left_join_brut'];

                    }else {
                        $leftjoin[] = " LEFT JOIN " . $v['bdd_table'] . " as " . $v['bdd_table_t'] . "  ON " . $table . "." . $key . " = " . $v['bdd_table_t'] . "." . $v['bdd_id'];

                    }
                    if (isset($v['jointure_concat_list'])) {
                        $alias[] = $v['jointure_concat_list'] . " as " . $v['bdd_table_t'];
                    } else {
                        $alias[] = $v['bdd_table_t'] . "." . $v['bdd_value'] . " as " . $v['bdd_table_t'];
                    }


                }

            }
        }

        // Récup des champs ou liste_detail = 1.
        $col = array();
        foreach ($champs as $key => $v) {
            if (isset($v['liste_detail']) && $v['liste_detail'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                array_push($col, $key);
            }
            if(isset($v['profilChamps'][$_SESSION['Droit']]['read_only']) && $v['profilChamps'][$_SESSION['Droit']]['read_only'] == 1){
                array_push($col, $key);
            }
        }
        $col = "" . $table . "." . implode(", " . $table . ".", $col);


        if ($leftjoin) {
            $col .= ", " . implode(",", $alias);
        }

        $reqSql = "SELECT " . $table . "." . $id . "," . $col . " FROM " . $table;

        if ($leftjoin) {
            $reqSql = $reqSql . implode(" ", $leftjoin);
        }
        $reqSql .= " WHERE " . $table . "." . $id . " = " . $ID;

        return $reqSql;
    }

    // Recup la requete de l' item par ID de l'item.
    public function findFileQuery($id, $table, $champs, $ID, $xls)
    {

        $reqSql = "SELECT " . $table . ".id," . $champs . " FROM " . $table;

        $reqSql .= " WHERE " . $table . "." . $id . " = " . $ID . " AND " . $table . ".conf = '" . $xls . "' AND .$table.hidden = 0";

        return $reqSql;
    }

    // Recup un item par ID de l'item.
    public function findItem($reqSql)
    {
        return $this->query($reqSql);
    }

    public function itemCSVQuery($profilSql, $table, $champs, $filtre, $label_id, $get_filtre = null, $champs_array_val = null)
    {

		
        // Récup des champs quand type = sql(jointure).
        $leftjoin = array();
        $alias = array();


        foreach ($champs as $key => $v) {
            if (isset($v['bdd_table']) && $v['bdd_table'] != null) {
                if ($v['type'] == "list_sql") {
                    if(isset($v['left_join_brut']) && $v['left_join_brut'] != '' ){

                        $leftjoin[] = $v['left_join_brut'];

                    }else{
                        $leftjoin[] = " LEFT JOIN " . $v['bdd_table'] . " as " . $v['bdd_table_t'] . "  ON " . $table . "." . $key . " = " . $v['bdd_table_t'] . "." . $v['bdd_id'];

                    }
                    if (isset($v['jointure_concat_list'])) {
                        $alias[] = $v['jointure_concat_list'] . " as " . $v['bdd_table_t'];
                    } else {
                        $alias[] = $v['bdd_table_t'] . "." . $v['bdd_value'] . " as " . $v['bdd_table_t'];
                    }
                }
            }
        }

        // Récup des champs ou liste = 1.
        $col = array();
        foreach ($champs as $key => $v) {
            if (isset($v['csv']) && $v['csv'] == 1 && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                array_push($col, $key);
            }
        }
        $col = "" . $table . "." . implode(", " . $table . ".", $col);


        if ($leftjoin) {
            $col .= ", " . implode(",", $alias);
        }

        $reqSql = "SELECT " . $col . " FROM " . $table;

        if ($leftjoin) {
            $reqSql = $reqSql . implode(" ", $leftjoin);
        }

        // Récup des champs ou recherche = 1.
        $colConcat = array();
        foreach ($champs as $key => $v) {
            if (isset($v['recherche']) && $v['recherche'] == 1) {

                if (!isset($v['type']) || $v['type'] != 'list_sql') {
                    array_push($colConcat, $key);
                }
            }
        }

        $reqSql = $reqSql . " WHERE " . $table . ".hidden = 0 ";


        if (isset($filtre) && $filtre == 1) {

            foreach ($get_filtre as $get_filtreKey => $get_filtreVal) {

				/*
                if (is_int($get_filtreVal) && $get_filtreVal != 0) {
                    $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . " =" . $get_filtreVal;
                }
				*/
				if (trim($get_filtreVal)!="") {
                    $reqSql = $reqSql . " AND " . $table . "." . $champs_array_val[$get_filtreKey] . " =" . intval($get_filtreVal);
                }

            }
            if (!empty($_GET['search']) && strlen($_GET['search']) < 100) {

                $reqSql = $reqSql . " AND CONCAT (";
                foreach ($champs as $key => $v) {
                    if (isset($v['type']) && isset($v['recherche']) && $v['recherche'] == 1 && $v['type'] == 'list_sql') {
                        $reqSql = $reqSql . " IFNULL(" . $v['bdd_table_t'] . "." . $v['bdd_value'] . ", ' '),";
                    }
                }
				if(count($colConcat)>0) {
					$reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
					$reqSql = $reqSql . " ,' ') ) LIKE ? ";
				} else {
					$reqSql = trim($reqSql,",") . " ) LIKE ? ";
				}
				/*
                $reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
                $reqSql = $reqSql . " ,' ') ) LIKE ? ";
				*/
            } elseif (!empty($_SESSION[$_GET['XLS']]['search']) && strlen($_SESSION[$_GET['XLS']]['search']) < 100 && !isset($_GET['search'])) {

                $reqSql = $reqSql . " AND CONCAT (";
                foreach ($champs as $key => $v) {
                    if (isset($v['type']) && isset($v['recherche']) && $v['recherche'] == 1 && $v['type'] == 'list_sql') {
                        $reqSql = $reqSql . " IFNULL(" . $v['bdd_table_t'] . "." . $v['bdd_value'] . ", ' '),";
                    }
                }
				if(count($colConcat)>0) {
					$reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
					$reqSql = $reqSql . " ,' ') ) LIKE ? ";
				} else {
					$reqSql = trim($reqSql,",") . " ) LIKE ? ";
				}
				/*
                $reqSql = $reqSql . "IFNULL(" . $table . "." . implode(", ' '), IFNULL(" . $table . ".", $colConcat);
                $reqSql = $reqSql . " ,' ') ) LIKE ? ";
				*/
            }

        }

        foreach ($profilSql as $profil_s => $p_s) {

            if (isset($profilSql[$_SESSION['Droit']]['req_sql']) && $profilSql[$_SESSION['Droit']]['req_sql'] == 1) {

                $reqSql = $reqSql . $profilSql[$_SESSION['Droit']]['requete'];

            }
        }
        // Récup des champs ou liste_tri = 1.
        $liste_tri = array();
        foreach ($champs as $key => $v) {
            if (isset($v['liste_tri']) && $v['liste_tri'] == 1) {

                if (isset($v['type']) && $v['type'] == 'list_sql') {
                    array_push($liste_tri, $v['bdd_table_t']);
                } else {
                    array_push($liste_tri, $key);
                }

            }
        }

        if (!empty($_GET['order']) && !empty($_GET['champ'])) {
            if ($_GET['order'] == "ASC" || $_GET['order'] == "DESC") {
                foreach ($liste_tri as $champs_tri => $c_t) {
                    if ($_GET['champ'] == $c_t) {
                        $reqSql = $reqSql . " ORDER BY " . $_GET['champ'] . " " . $_GET['order'] . " ";
                    }
                }
            }
        } elseif (!empty($_SESSION[$_GET['XLS']]['order']) && !empty($_SESSION[$_GET['XLS']]['champ']) && !isset($_GET['order']) && !isset($_GET['champ'])) {
            if ($_SESSION[$_GET['XLS']]['order'] == "ASC" || $_SESSION[$_GET['XLS']]['order'] == "DESC") {
                foreach ($liste_tri as $champs_tri => $c_t) {
                    if ($_SESSION[$_GET['XLS']]['champ'] == $c_t) {
                        $reqSql = $reqSql . " ORDER BY " . $_SESSION[$_GET['XLS']]['champ'] . " " . $_SESSION[$_GET['XLS']]['order'] . " ";
                    }
                }
            }
        } elseif ($profilSql[$_SESSION['Droit']] && $profilSql[$_SESSION['Droit']]['order'] == 1) {
            $reqSql = $reqSql . $profilSql[$_SESSION['Droit']]['orderBy'];
        } else {
            //$reqSql = $reqSql . " ORDER BY " . $table . "." . $label_id . " DESC";
        }
		
        return $reqSql;
    }

    // Recup tous les item.
    public function getAllICSVItems($profilSql, $table, $champs, $filtre, $label_id, $get_filtre = null, $champs_array_val = null)
    {
        $reqSql = $this->itemCSVQuery($profilSql, $table, $champs, $filtre, $label_id, $get_filtre,$champs_array_val);

        if (isset($_GET['search'])) {
            $req = $this->query($reqSql, ['%' . $_GET['search'] . '%'], false);
        } elseif (!isset($_GET['search']) && isset($_SESSION[$_GET['XLS']]['search'])) {
            $req = $this->query($reqSql, ['%' . $_SESSION[$_GET['XLS']]['search'] . '%'], false);
        } else {
            $req = $this->query($reqSql);
        }
        return $req;
    }

    public function listeOption($table, $col)
    {
        $reqSql = 'SELECT id,' . $col . ' FROM ' . $table;
        return $reqSql = $this->query($reqSql);
    }

    public function listeOptionLimit($table, $col, $limit)
    {
        $reqSql = 'SELECT id,' . $col . ' FROM ' . $table.' LIMIT '.$limit;
        return $reqSql = $this->query($reqSql);
    }

    public function listeOptionConf($req)
    {
        $reqSql = $req;
        return $reqSql = $this->query($reqSql);

    }

    public function allItemsForSelectQuery($val)
    {
        if (isset($val['reqPart']) && $val['reqPart'] != null) {

            $reqString = $val['reqPart'];
        } else {

            if (isset($val['jointure_concat'])) {
                $reqString = "SELECT " . $val['bdd_id'] . ", " . $val['jointure_concat'] . " as " . $val['bdd_table_t'] . " FROM " . $val['bdd_table'];
            } else {
                $reqString = "SELECT " . $val['bdd_id'] . ", " . $val['bdd_value'] . " as " . $val['bdd_table_t'] . " FROM " . $val['bdd_table'];
            }

            if (isset($val['bdd_condition']) && $val['bdd_condition'] != null) {
                $reqString .= ' WHERE ' . $val['bdd_condition'] . ' ';
            }

            if (isset($val['bdd_ordre']) && $val['bdd_ordre'] != null) {
                $reqString .= ' ORDER BY ' . $val['bdd_ordre'] . ' ';
            }

        }
        return $reqString;
    }

    public function allItemsForSelect($reqString)
    {
        $req = $this->query($reqString);
        return $req;
    }



    // Pour le diagram (requete mis en dur pour éviter des foreach et traitement lourd pour une action liée à un projet spécifique).
    public function allItemsByGroupQuery($getGroupe, $table, $champs)
    {
        // Récup des champs quand type = sql(jointure).
        $leftjoin = array();
        $alias = array();
        foreach ($champs as $key => $v) {
            if (isset($v['bdd_table']) && $v['bdd_table'] != null) {
                if ($v['type'] == "list_sql" && isset($v['diagram']) && $v['diagram'] == 1 && isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {

                    $leftjoin[] = " LEFT JOIN " . $v['bdd_table'] . " as " . $v['bdd_table_t'] . "  ON " . $table . "." . $key . " = " . $v['bdd_table_t'] . "." . $v['bdd_id'];
                    $alias[] = $v['bdd_table_t'] . "." . $v['bdd_value'] . " as " . $v['bdd_table_t'];
                }
            }
        }
        // Récup des champs ou diagram = 1.
        $col = array();
        foreach ($champs as $key => $v) {
            if (isset($v['diagram']) && $v['diagram'] == 1 && isset($v['profilChamps'][$_SESSION['Droit']]['lecture']) && $v['profilChamps'][$_SESSION['Droit']]['lecture'] == 1) {
                array_push($col, $key);
            }
        }
        $col = "" . $table . "." . implode(", " . $table . ".", $col);
        if ($leftjoin) {
            $col .= ", " . implode(",", $alias);
        }
        $reqSql = "SELECT " . $table . ".id," . $col . " FROM " . $table;
        if ($leftjoin) {
            $reqSql = $reqSql . implode(" ", $leftjoin);
        }
        $reqSql = $reqSql . " WHERE " . $table . ".hidden = 0 AND " . $table . ".id_groupe=" . $getGroupe;
        return $reqSql;
    }

    public function allItemsByGroup($req)
    {
        $req = $this->query($req);
        return $req;
    }

    public function countItemsSuivi($table, $col, $param, $annee = null)
    {

        if (isset($annee) && !empty($annee) && $annee != null) {
            $reqSql = "SELECT COUNT(*) AS total FROM " . $table . " WHERE " . $col . " = " . $param . " AND YEAR(en_date_du)=" . $annee;
        } else {
            $reqSql = "SELECT COUNT(*) AS total FROM " . $table . " WHERE " . $col . " = " . $param;
        }


        $reqSql = $this->query($reqSql);
        return $reqSql;
    }

    // Récupération des données de l'historique en ajax.
    public function getHistorique($id, $nom_feuille)
    {
        return $this->db->getHistoriqueDatabase($id, $nom_feuille);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    // Delete des données de l'historique.
    public function deleteHistorique($jours)
    {
        return $this->db->deleteHistoriqueDatabase($jours);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    // Autocomplétion des label.
    public function autoCompletion($champsTabXsl, $champs, $conf)
    {


        if(isset($champsTabXsl['jointure_concat']) && $champsTabXsl['jointure_concat'] != NULL){
            $strQuery = "SELECT id idItem, " . $champsTabXsl['jointure_concat'] . " label FROM " . $conf;
        }else{
            $strQuery = "SELECT id idItem, " . $champs . " label FROM " . $conf;
        }
            $strQuery .= " WHERE ";


        if(isset($champsTabXsl['jointure_concat']) && $champsTabXsl['jointure_concat'] != NULL){
            $strQuery .= $champsTabXsl['jointure_concat'] . " LIKE :variable ";
        }else{
            $strQuery .= $champs . " LIKE :variable";
        }


        $maxRows = null;

            if (isset($champsTabXsl['type']) && $champsTabXsl['type'] == 'list_sql' && $champsTabXsl['autocomplete'] == 1) {

                if (isset($champsTabXsl['profilChamps'][$_SESSION['Droit']]['ajax']) && $champsTabXsl['profilChamps'][$_SESSION['Droit']]['ajax'] == 1 && $champsTabXsl['bdd_value'] == $champs) {
                    $strQuery .= $champsTabXsl['profilChamps'][$_SESSION['Droit']]['req_ajax'];
                }

                if (isset($champsTabXsl['profilChamps'][$_SESSION['Droit']]['ajax']) && $champsTabXsl['profilChamps'][$_SESSION['Droit']]['ajax'] == 2 && $champsTabXsl['bdd_value'] == $champs) {
                    $strQuery .= ' AND ' . $champsTabXsl['profilChamps'][$_SESSION['Droit']]['req_ajax'];
                }

                    if (isset($champsTabXsl['maxRows'])) {

                        $maxRows = $champsTabXsl['maxRows'];
                    }

            }

        if (isset($maxRows)) {
            $strQuery .= " LIMIT 0, :maxRows";
        }


        return $this->db->autoCompletionDatabase($strQuery, $maxRows); // (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    public function autoCompletionWithConcatForResp($champsTabXsl, $champs, $conf, $profil_user)
    {


        $strQuery = "SELECT id idItem, " . $champsTabXsl['jointure_concat'] . " label FROM " . $conf;



        $strQuery .= " WHERE ";

        if (isset($_POST[$champs])) {
            $strQuery .= $champsTabXsl['jointure_concat'] . " LIKE :variable ";
        }


        if ($_SESSION['Droit'] == 1 || $_SESSION['Droit'] == 10000) {
            if ($profil_user != null) {
                switch ($profil_user) {
                    case 3: // RR
                        $strQuery .= ' AND roleId = 1 ';
                        break;
                    case 4: // VPI
                        $strQuery .= ' AND roleId = 3 ';
                        break;
                    case 5: // PCI
                        $strQuery .= ' AND roleId = 4 ';
                        break;
                    case 7: // STT
                        $strQuery .= ' AND roleId = 4 ';
                        break;
                }
            }
        } else {
            if ($profil_user != null) {
                switch ($profil_user) {
                    case 3: // RR
                        $strQuery .= ' AND roleId = 1 AND responsable = ' . $_SESSION['UserID'];
                        break;
                    case 4: // VPI
                        $strQuery .= ' AND roleId = 3 AND responsable = ' . $_SESSION['UserID'];
                        break;
                    case 5: // PCI
                        $strQuery .= ' AND roleId = 4 AND responsable = ' . $_SESSION['UserID'];
                        break;
                    case 7: // STT
                        $strQuery .= ' AND roleId = 4 AND responsable = ' . $_SESSION['UserID'];
                        break;
                }
            }
        }


        $maxRows = null;


            if (isset($champsTabXsl['type']) && $champsTabXsl['type'] == 'list_sql' && $champsTabXsl['autocomplete'] == 1) {

                if (isset($champsTabXsl['profilChamps'][$_SESSION['Droit']]['ajax']) && $champsTabXsl['profilChamps'][$_SESSION['Droit']]['ajax'] == 1 && $champsTabXsl['bdd_value'] == $champs) {
                    $strQuery .= ' ORDER BY ' . $champsTabXsl['profilChamps'][$_SESSION['Droit']]['req_ajax'];
                }

                if (isset($champsTabXsl['profilChamps'][$_SESSION['Droit']]['ajax']) && $champsTabXsl['profilChamps'][$_SESSION['Droit']]['ajax'] == 2 && $champsTabXsl['bdd_value'] == $champs) {
                    $strQuery .= ' AND ' . $champsTabXsl['profilChamps'][$_SESSION['Droit']]['req_ajax'];
                }


                if (isset($champsTabXsl['profilChamps'][$_SESSION['Droit']]['lecture']) && $champsTabXsl['profilChamps'][$_SESSION['Droit']]['lecture'] == 1 && $champsTabXsl['bdd_value'] == $champs) {
                    if (isset($champsTabXsl['maxRows'])) {

                        $maxRows = $champsTabXsl['maxRows'];
                    }
                }


            }


        if (isset($maxRows)) {
            $strQuery .= " LIMIT 0, :maxRows";
        }


        return $this->db->autoCompletionDatabase($strQuery, $maxRows); // (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }





    // Verification si le mail existe déjà en bdd pour addUser et editUser.(Fonction liée à la page MysqlDatabase dans le dossier Core.)
    public function existItem($col, $variable, $table, $edit, $id)
    {
        return $this->db->ExistItemDatabase($col, $variable, $table, $edit, $id);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    // Mise à jour d'un item.(Fonction liée à la page MysqlDatabase dans le dossier Core.)
    public function updateItem($id, $table, $colInsert, $valueInsert, $valueParam, $getID, $webService = null)
    {

        return $this->db->updateItemDatabase($id, $table, $colInsert, $valueInsert, $valueParam, $getID, $webService);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    // Ajout d'un nouvel Item.(Fonction liée à la page MysqlDatabase dans le dossier Core.)
    public function insertItem($table, $colInsert, $valueInsert, $valueParam, $webService = null)
    {
        return $this->db->insertItemDatabase($table, $colInsert, $valueInsert, $valueParam, $webService);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    // Ajout d'un nouvel User.(Fonction liée à la page MysqlDatabase dans le dossier Core.)
    public function insertUserItem($table, $colInsert, $valueInsert, $valueParam, $webService = null)
    {
        return $this->db->insertItemUserDatabase($table, $colInsert, $valueInsert, $valueParam, $webService);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    // Désactive l'item en base de données, item non visible ou accessible dans l'application.(Fonction liée à la page MysqlDatabase dans le dossier Core.)
    public function desactiveitem($id, $table, $value, $getID)
    {
        return $this->db->desactiveitemDatabase($id, $table, $value, $getID);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    public function findItemData($reqVal = null, $param = null, $webService = null)
    {
        return $this->db->findItemDatabase($reqVal, $param, $webService);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    public function findItemData2($reqVal = null, $param = null, $webService = null)
    {
        return $this->db->findItemDatabase2($reqVal, $param, $webService);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    public function deleteItem($id, $table, $webService = null)
    {
        return $this->db->deleteItemDatabase($id, $table, $webService);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

    // Delete des données de l'historique.
    public function deleteFileTempo($minute)
    {
        return $this->db->deleteFileTempoDatabase($minute);// (Fonction liée à la page MysqlDatabase dans le dossier *racine*/Core/MysqlDatabase.php.)
    }

}
