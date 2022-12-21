<?php

namespace Core\Database;

use mysql_xdevapi\Exception;
use \PDO;
use App\Table\AutoCompletionItemLabel;

require_once("../config/FieldsUsers.php");
require_once("../config/FieldsHistorique.php");

class MysqlDatabase extends Database
{

    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_host;
    private $db_charset;
    protected $db_DebugMode;
    protected $db_LocalTime;
    private $pdo;

    public function __construct($db_name, $db_user, $db_pass, $db_host, $db_charset, $db_DebugMode, $db_LocalTime)
    {
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_host = $db_host;
        $this->db_charset = $db_charset;
        $this->db_DebugMode = $db_DebugMode;
        $this->db_LocalTime = $db_LocalTime;
    }

    private function getPDO($webService = null)
    {
		$tmpTimeStart=microtime(true);
		if(!isset($_SESSION['debug'])) $_SESSION['debug']=array("sqlAll"=>array());
		if(!isset($_SESSION['debug']['sqlAll'])) $_SESSION['debug']['sqlAll']=array();
		
		
        if ($this->pdo === null) {
			
            try {
                $pdo = new PDO("mysql:host=$this->db_host; dbname=$this->db_name; charset=$this->db_charset;", $this->db_user, $this->db_pass);
                if ($webService == 1) {
                    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } else {
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }

                $this->pdo = $pdo;
				
				$_SESSION['debug']['sqlAll'][]=array("what"=>"getPDO","query"=>"-","time"=> (microtime(true)-$tmpTimeStart));
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        return $this->pdo;
    }

    public function query($statement, $class_name = null, $one = false)
    {

		$tmpTimeStart = microtime(true);
		
        if ($this->db_DebugMode == 1) {
            try {
                $req = $this->getPDO()->query($statement);
            } catch (\PDOException $e) {
                print("ERREUR SQL : " . $statement);
                exit;
            }
        } else {
            $req = $this->getPDO()->query($statement);
        }

		
		
        if (
            strpos($statement, 'UPDATE') === 0 ||
            strpos($statement, 'INSERT') === 0 ||
            strpos($statement, 'DELETE') === 0
        ) {


            return $req;
        }
        if ($class_name === null) {
            $req->setFetchMode(PDO::FETCH_OBJ);
        } else {
            $req->setFetchMode(PDO::FETCH_CLASS, $class_name);
        }
        if ($one) {
            $datas = $req->fetch();
        } else {
            $datas = $req->fetchAll();
        }
		
		if(!isset($_SESSION['debug'])) $_SESSION['debug']=array("sqlAll"=>array());
		if(!isset($_SESSION['debug']['sqlAll'])) $_SESSION['debug']['sqlAll']=array();
		$_SESSION['debug']['sqlAll'][]=array("what"=>"query","query"=>$statement,"time"=> (microtime(true)-$tmpTimeStart) );
		
        return $datas;
    }

    public function prepare($statement, $attributes, $class_name = null, $one = false)
    {

		$tmpTimeStart=microtime(true);
		
		
		
        if ($this->db_DebugMode == 1) {
            try {
                $req = $this->getPDO()->prepare($statement);
            } catch (\PDOException $e) {
                print("ERREUR SQL : " . $statement);
                exit;
            }
        } else {
            $req = $this->getPDO()->prepare($statement);
        }

        $res = $req->execute($attributes);
        if (
            strpos($statement, 'UPDATE') === 0 ||
            strpos($statement, 'INSERT') === 0 ||
            strpos($statement, 'DELETE') === 0
        ) {
            return $res;
        }
        if ($class_name === null) {
            $req->setFetchMode(PDO::FETCH_OBJ);
        } else {
            $req->setFetchMode(PDO::FETCH_CLASS, $class_name);
        }
        if ($one) {
            $datas = $req->fetch();
        } else {
            $datas = $req->fetchAll();
        }
		
		if(!isset($_SESSION['debug'])) $_SESSION['debug']=array("sqlAll"=>array());
		if(!isset($_SESSION['debug']['sqlAll'])) $_SESSION['debug']['sqlAll']=array();
		$_SESSION['debug']['sqlAll'][]=array("what"=>"prepare","query"=>$statement,"time"=> (microtime(true)-$tmpTimeStart) );
		
        return $datas;
    }

    /*--------------------------------------------------------------------------------------------------------------------*/
    /* Concerne l'application de base ------------------------------------------------------------------------------------*/
    /*--------------------------------------------------------------------------------------------------------------------*/

    public function getHistoriqueDatabase($id, $nom_feuille)
    {

		if(!isset($_SESSION['debug'])) $_SESSION['debug']=array("sqlAll"=>array());
		if(!isset($_SESSION['debug']['sqlAll'])) $_SESSION['debug']['sqlAll']=array();

		$_SESSION['debug']['sqlAll'][]=array("what"=>"getHistoriqueDatabase","query"=>"SELECT
                                        historique.id,
                                        historique.id_user, 
                                        historique.ip_user, 
                                        historique.id_item, 
                                        historique.date_modification, 
                                        historique.cle_conf, 
                                        historique.value_modif,
                                        
                                        users.nom as nom,
                                        users.prenom as prenom
                                        
                                        FROM historique 
                                        LEFT JOIN users ON historique.id_user = users.id 
                                        WHERE historique.id_item = :id_item AND historique.cle_conf = :nom_feuille 
                                        ORDER BY historique.id DESC
                                        LIMIT 0, 30");
		
        $req = $this->getPDO();
        $req = $req->prepare("SELECT
                                        historique.id,
                                        historique.id_user, 
                                        historique.ip_user, 
                                        historique.id_item, 
                                        historique.date_modification, 
                                        historique.cle_conf, 
                                        historique.value_modif,
                                        
                                        users.nom as nom,
                                        users.prenom as prenom
                                        
                                        FROM historique 
                                        LEFT JOIN users ON historique.id_user = users.id 
                                        WHERE historique.id_item = :id_item AND historique.cle_conf = :nom_feuille 
                                        ORDER BY historique.id DESC
                                        LIMIT 0, 30");
        $req->execute(array(
            'id_item' => $id,
            'nom_feuille' => $nom_feuille
        ));
        $res = $req->fetchAll(PDO::FETCH_CLASS);
        return $res;
    }

    public function deleteHistoriqueDatabase($jours)
    {
        $req = $this->getPDO();
        $req = $req->query("DELETE FROM historique WHERE date_modification < DATE_SUB(NOW(), INTERVAL " . $jours . " DAY)");
    }

    public function autoCompletionDatabase($strQuery, $maxRows = null)
    {

        $req = $this->getPDO();

        $query = $req->prepare($strQuery);
        if (isset($_POST[$_GET['champs']])) {
            $value = "%" . $_POST[$_GET['champs']] . "%";
            $query->bindParam(":variable", $value, PDO::PARAM_STR);
        } else {
            $value = "%" . $_POST[$_GET['champs']] . "%";
            $query->bindParam(":variable", $value, PDO::PARAM_STR);
        }

        if (isset($maxRows) && $maxRows != null) {
            $valueRows = intval($maxRows);
            $query->bindParam(":maxRows", $valueRows, PDO::PARAM_INT);
        }
        $query->execute();
        $list = $query->fetchAll(PDO::FETCH_CLASS, AutoCompletionItemLabel::class);

        return $list;
    }

    public function ExistItemDatabase($col, $variable, $table, $edit, $id)
    {
		
		if(!isset($_SESSION['debug'])) $_SESSION['debug']=array("sqlAll"=>array());
		if(!isset($_SESSION['debug']['sqlAll'])) $_SESSION['debug']['sqlAll']=array();
		

        $req = $this->getPDO();
        if ($edit && $edit == 1) {
			$_SESSION['debug']['sqlAll'][]=array("what"=>"ExistItemDatabase","query"=>"SELECT " . $table . "." . $col . " FROM " . $table . " WHERE " . $table . "." . $col . " = ? AND " . $table . ".id <> ?");
			
            $req = $req->prepare("SELECT " . $table . "." . $col . " FROM " . $table . " WHERE " . $table . "." . $col . " = ? AND " . $table . ".id <> ?");
            if ($req->execute(array($variable, $id))) {
                while ($row = $req->fetch()) {
                    return $row;
                }
            }
            return $row;
        } else {
			
			$_SESSION['debug']['sqlAll'][]=array("what"=>"ExistItemDatabase","query"=>"SELECT " . $table . "." . $col . " FROM " . $table . " WHERE " . $table . "." . $col . " = ?");
			
            $req = $req->prepare("SELECT " . $table . "." . $col . " FROM " . $table . " WHERE " . $table . "." . $col . " = ?");
            if ($req->execute(array($variable))) {
                while ($row = $req->fetch()) {
                    return $row;
                }
            }
            return $row;
        }
    }

    public function updateItemDatabase($id, $table, $colInsert, $valueInsert, $valueParam, $getID, $webService = null)
    {

        if (isset($table) && !empty($table) && isset($colInsert) && !empty($colInsert) && isset($valueInsert) && !empty($valueInsert) && isset($valueParam) && !empty($valueParam) && isset($getID) && !empty($getID) && isset($id) && !empty($id)) {
            $req = $this->getPDO($webService);
            $resReq = array();
            foreach ($colInsert as $col => $c) {
                $res = $c . " = :" . $c;
                array_push($resReq, $res);
            }
            $value = implode(",", $resReq);

            $req = $req->prepare("UPDATE " . $table . " SET " . $value . " WHERE " . $table . "." . $id . '=:' . $id);

            $queryDebug = "UPDATE " . $table . " SET " . $value . " WHERE " . $table . "." . $id . '=:' . $id;

            foreach ($valueParam as $p => &$pp) {
                $req->bindParam($valueInsert[$p], $pp);
                $queryDebug = str_replace($valueInsert[$p], "'" . $pp . "'", $queryDebug);
            }

            $req->bindParam(':' . $id, $getID);
            $queryDebug = str_replace(":" . $id, "'" . $getID . "'", $queryDebug);


            $_SESSION['debug']['Update'] = $queryDebug;


            if ($webService == 1) {
                $error = array('Code' => 'Code', 'Erreur' => 'Erreur');
                return $req->execute() or exit (json_encode(array($error['Code'] => 'NOK', $error['Erreur'] => $req->errorInfo()[2])));
            } else {
                $x = $req->execute();
                return $x;
            }
        } else {
            return false;
        }
    }

    public function insertItemDatabase($table, $colInsert, $valueInsert, $valueParam, $webService = null)
    {

        $req = $this->getPDO($webService);

        $error = array();
        $col = implode(",", $colInsert);
        $valIn = implode(",", $valueInsert);

        $req = $req->prepare("INSERT INTO " . $table . " (" . $col . ") VALUES (" . $valIn . ")");

        $queryDebug = "INSERT INTO " . $table . " (" . $col . ") VALUES (" . $valIn . ")";


        foreach ($valueParam as $p => &$pp) {
            $req->bindParam($valueInsert[$p], $pp);
            $queryDebug = str_replace($valueInsert[$p], "'" . $pp . "'", $queryDebug);
        }

        $_SESSION['debug']['Insert'] = $queryDebug;

        if ($webService == 1) {
            $error = array('Code' => 'Code', 'Erreur' => 'Erreur');

            return $req->execute() or exit (json_encode(array($error['Code'] => 'NOK', $error['Erreur'] => $req->errorInfo()[2])));
        } else {
            return $req->execute();
        }
    }

    public function insertItemUserDatabase($table, $colInsert, $valueInsert, $valueParam, $webService = null)
    {


        $req = $this->getPDO($webService);
        $col = implode(",", $colInsert);
        $valIn = implode(",", $valueInsert);

        $req = $req->prepare("INSERT INTO " . $table . " (email_admin_add," . $col . ") VALUES (:email_admin_add," . $valIn . ")");

        $queryDebug = "INSERT INTO " . $table . " (email_admin_add," . $col . ") VALUES (:email_admin_add," . $valIn . ")";
        $req->bindParam(':email_admin_add', $_SESSION['email']);
        foreach ($valueParam as $p => &$pp) {
            $req->bindParam($valueInsert[$p], $pp);
            $queryDebug = str_replace($valueInsert[$p], "'" . $pp . "'", $queryDebug);
        }

        $_SESSION['debug']['Insert'] = $queryDebug;

        return $req->execute();
    }

    public function desactiveitemDatabase($id, $table, $value, $getID, $webService = null)
    {
        $valeur = 1;
        $req = $this->getPDO($webService);
        $req = $req->prepare("UPDATE " . $table . " SET " . $value . "= :" . $value . " WHERE " . $table . "." . $id . '=:' . $id);

        $queryDebug = "UPDATE " . $table . " SET " . $value . "= :" . $value . " WHERE " . $table . "." . $id . '=:' . $id;

        $req->bindParam(':' . $value, $valeur);
        $queryDebug = str_replace($value, "'" . $valeur . "'", $queryDebug);
        $req->bindParam(':' . $id, $getID);
        $queryDebug = str_replace(":" . $id, "'" . $getID . "'", $queryDebug);


        $_SESSION['debug']['Update'] = $queryDebug;
		
		
		if(!isset($_SESSION['debug'])) $_SESSION['debug']=array("sqlAll"=>array());
		if(!isset($_SESSION['debug']['sqlAll'])) $_SESSION['debug']['sqlAll']=array();
		$_SESSION['debug']['sqlAll'][]=array("what"=>"desactiveitemDatabase","query"=>$queryDebug);

        if ($webService == 1) {
            $error = array('Code' => 'Code', 'Erreur' => 'Erreur');

            return $req->execute() or exit (json_encode(array($error['Code'] => 'NOK', $error['Erreur'] => $req->errorInfo()[2])));
        } else {
            return $req->execute();
        }
    }

    public function activeitemDatabase($id, $table, $value, $getID, $webService = null)
    {
        $valeur = 0;
        $req = $this->getPDO($webService);
        $req = $req->prepare("UPDATE " . $table . " SET " . $value . "= :" . $value . " WHERE " . $table . "." . $id . '=:' . $id);

		if(!isset($_SESSION['debug'])) $_SESSION['debug']=array("sqlAll"=>array());
		if(!isset($_SESSION['debug']['sqlAll'])) $_SESSION['debug']['sqlAll']=array();
		$_SESSION['debug']['sqlAll'][]=array("what"=>"activeitemDatabase","query"=>"UPDATE " . $table . " SET " . $value . "= :" . $value . " WHERE " . $table . "." . $id . '=:' . $id);

        $req->bindParam(':' . $value, $valeur);
        $req->bindParam(':' . $id, $getID);
        if ($webService == 1) {
            $error = array('Code' => 'Code', 'Erreur' => 'Erreur');

            return $req->execute() or exit (json_encode(array($error['Code'] => 'NOK', $error['Erreur'] => $req->errorInfo()[2])));
        } else {
            return $req->execute();
        }
    }

    public function findItemDatabase($reqVal = null, $param = null, $webService = null)
    {


	$_SESSION['debug']['sqlAll'][]=array("what"=>"activeitemDatabase","query"=>"UPDATE " . $table . " SET " . $value . "= :" . $value . " WHERE " . $table . "." . $id . '=:' . $id);

        $req = $this->getPDO($webService);
		
		if(!isset($_SESSION['debug'])) $_SESSION['debug']=array("sqlAll"=>array());
		if(!isset($_SESSION['debug']['sqlAll'])) $_SESSION['debug']['sqlAll']=array();
		$_SESSION['debug']['sqlAll'][]=array("what"=>"findItemDatabase","query"=>$reqVal);


        if ($webService == 1 && $param != null) {

            $req = $req->prepare($reqVal);

            $error = array('Code' => 'Code', 'Erreur' => 'Erreur');
            $req->execute(array($param)) or exit (json_encode(array($error['Code'] => 'NOK', $error['Erreur'] => $req->errorInfo()[2])));
            return $list = $req->fetchAll(PDO::FETCH_CLASS);
        } else {

            $req = $req->prepare($reqVal);
            $req->execute(array($param));
            return $list = $req->fetchAll(PDO::FETCH_CLASS);
        }


    }

    public function findItemDatabase2($reqVal = null, $param = null, $webService = null)
    {
        $req = $this->getPDO($webService);
        $req = $req->prepare($reqVal);
        $error = array('Code' => 'Code', 'Erreur' => 'Erreur');
        $req->execute($param) or exit (json_encode(array($error['Code'] => 'NOK', $error['Erreur'] => $req->errorInfo()[2])));

    }

    public function deleteItemDatabase($id, $table, $webService = null)
    {
        $req = $this->getPDO($webService);

        $req = $req->prepare("DELETE FROM " . $table . " WHERE " . $table . ".id = :id");
        $req->bindParam(':id', $id);

        if ($webService == 1) {
            $error = array('Code' => 'Code', 'Erreur' => 'Erreur');

            return $req->execute() or exit (json_encode(array($error['Code'] => 'NOK', $error['Erreur'] => $req->errorInfo()[2])));
        } else {
            return $req->execute();
        }

    }

    public function deleteFileTempoDatabase($minute)
    {
        $req = $this->getPDO();
        $req = $req->query("DELETE FROM file_tmp WHERE file_tmp.datequote < DATE_SUB(NOW(), INTERVAL " . $minute . " MINUTE)");
    }

}

