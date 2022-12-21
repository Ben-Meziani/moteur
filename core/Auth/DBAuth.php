<?php

namespace Core\Auth;

use Core\Config;
use Core\Database\Database;

require_once("../config/config.php");

$config = Config::getInstance(ROOT . '/config/config.php');
$arrayConfig = (array)$config;
foreach ($arrayConfig as $arrayConfigKey => $arrayConfigVal) {
    $_SESSION['name_projet'] = $arrayConfigVal['name_projet'];
}

class DBAuth
{

    private $db;
    public $valid;

    public function __construct(Database $db)
    {

        $this->db = $db;
    }

    public function loginAuth($identifiant, $password)
    {
        $users = $this->db->prepare('
                SELECT 
                users.id,
                users.nom,
                users.prenom,
                users.email,
                users.login,
                users.password,
                users.roleId,
                users_roles.role_nom as profil
                FROM users
                LEFT JOIN users_roles on users.roleId = users_roles.id
                WHERE users.login = ? AND users.actif = 1 AND users.hidden = 0', [$identifiant], null, true);
        if ($users) {
            if (password_verify($password, $users->password)) {
                $_SESSION["user_depts"] = array();
                $dept = $this->db->query('SELECT t1.id,t1.code_vc FROM (user_dept AS t0,secto_departements AS t1) WHERE t0.id_dept=t1.id AND t0.id_user=' . $users->id);

                if ($dept) {
                    foreach ($dept as $deptKey => $deptVal) {
                        $_SESSION["user_depts"][$deptVal->id] = $deptVal->code_vc;
                    }

                }
				
				$_SESSION["user_backups_to"] = array();
				$backups = $this->db->query('SELECT t0.id FROM (users AS t0) WHERE t0.actif=1 AND (t0.backup1='.$users->id.' OR t0.backup2='.$users->id.' OR t0.backup3='.$users->id.' OR t0.backup4='.$users->id.')');
				if ($backups) {
               //die(var_dump('backup'));
					foreach ($backups as $backupsK => $backupsV) {
						$_SESSION["user_backups_to"][$backupsV->id] = $backupsV->id;
					}
				}
                //die(var_dump('nobackup'));


                // $_SESSION['pole']  = '';
				// if(!in_array($users->roleId, [1,10000]) ){
                //     $get_hie = $this->db->query('SELECT hie_poste_id,hie_poste,hie_sous_service_id FROM cov_hierarchie WHERE id ='.$users->id);
                //     if(isset($get_hie)&& !empty($get_hie)){
                //         $users->roleId = $get_hie[0]->hie_poste_id;
                //         $users->profil = $get_hie[0]->hie_poste;
                //         $_SESSION['pole']  = $get_hie[0]->hie_sous_service_id;
                //     }
                // }

                $_SESSION['debug'] = array();

                $_SESSION['debug']['Update'] = null;
                $_SESSION['debug']['Insert'] = null;

                $_SESSION['UserID'] = $users->id;
                $_SESSION['login'] = $users->login;
                $_SESSION['email'] = $users->email;
                $_SESSION['Droit'] = $users->roleId;
                $_SESSION['nom'] = $users->nom;
                $_SESSION['prenom'] = $users->prenom;
                $_SESSION['profil'] = $users->profil;

                $_SESSION['name_projet_user'] = $_SESSION['name_projet'];
                $valid = 0;
                //die(var_dump($valid));
                return $valid;
            } else {
                $valid = 1;
            }

        } else {
            $valid = 1;
        }

        return $valid;
    }

}

