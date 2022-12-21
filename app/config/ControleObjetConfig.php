<?php

namespace App\config;

use App\Controller\AppController;
use Core\Database\MysqlDatabase;

class ControleObjetConfig extends AppController
{

    public $errors;
    protected $tableName;

    public function __construct()
    {
        parent::__construct();
        $this->loadModel('Nom_Tab');
    }

    /*--------------------------------------------------------------------------------------------------------------------*/
    /* Contrôle des identifiants, de session et des objets concérnant les users ------------------------------------------*/
    /*--------------------------------------------------------------------------------------------------------------------*/

    public function existSession($session)
    {
        if (empty($session)) {
            return $this->errors['Session'] = "Vous devez être connecté.";
        } else {
            return $this->errors['Session'] = null;
        }
    }

    public function getErrorLogin()
    {
        return $this->errors['Login'] = "Nom d'utilisateur ou mot de passe incorrect.";
    }

    public function validIdentifiant($Login, $Password)
    {
        if (empty($Login) || !filter_var($Login, FILTER_SANITIZE_STRING)) {
            $this->errors['Login'] = "Le Login n'est pas valide ou n'est pas renseigné.";
        } elseif (!empty($Login) && preg_match('#([<>][a-zA-Z\s]+[<>]|[<>]\/[a-z]+[<>])#', $Login)) {
            return $this->errors['Login'] = "Le champs Login ne doit pas comporté de chevron ou de balise.";
        } else {
            if (empty($Password)) {
                $this->errors['Login'] = "Le mot de passe n'est pas rempli.";
            } elseif (!empty($Password) && preg_match('#([<>][a-zA-Z\s]+[<>]|[<>]\/[a-z]+[<>])#', $Password)) {
                return $this->errors['Login'] = "Le champs mot de passe ne doit pas comporté de chevron ou de balise.";
            } else {
                $this->errors['Login'] = null;
            }
        }
        return $this->errors['Login'];
    }

    public function validMail($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {


        if ($obligatoire == 1) {
            if (empty($variable)) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }
        if (!empty($variable) && !filter_var($variable, FILTER_VALIDATE_EMAIL)) {
            return $this->errors[$champs] = " <em>n'est pas conforme.</em>";
        } else {
            if (!empty($variable) && strlen($variable) < $tailleMin || strlen($variable) > $tailleMax) {
                return $this->errors[$champs] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " caractères.</em>";
            } else {
                return $this->errors[$champs] = null;
            }
        }
    }

    public function validlogin_exist($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {

        if ($obligatoire == 1) {
            if (empty($variable)) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }

        if (!empty($variable) && !filter_var($variable, FILTER_SANITIZE_STRING)) {
            return $this->errors[$champs] = "<em> n'est pas conforme.</em>";
        } else {
            if (!empty($variable) && strlen($variable) < $tailleMin || strlen($variable) > $tailleMax) {
                return $this->errors[$champs] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " caractères.</em>";
            } else {
                $tableName = $this->Nom_Tab->existItem($champs, $variable, $table, $edit, $id);
                if ($tableName == TRUE) {
                    return $this->errors[$champs] = '<em>est déjà utilisé pour un autre compte.</em>';
                } else {
                    return $this->errors[$champs] = null;
                }
            }
        }
    }

    public function validPassword($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {

        if ($obligatoire == 1) {
            if (empty($variable)) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }
        if (!empty($variable) && !filter_var($variable, FILTER_SANITIZE_STRING)) {
            return $this->errors[$champs] = " <em>n'est pas conforme.</em>";
        } else {
            if (!empty($variable) && !preg_match('#^[[:alnum:][@]{' . $tailleMin . ',' . $tailleMax . '}$#', $variable)) {
                $this->errors[$champs] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " caractères, il peut être composé de
                                           lettres (sans accents) et/ou de chiffres, et seulement le @ est accepté en tant que caractères spéciaux.</em>";
            } else {
                $this->errors[$champs] = null;
            }
        }
        return $this->errors[$champs];
    }

    /*--------------------------------------------------------------------------------------------------------------------*/
    /* Contrôle des objets générique -------------------------------------------------------------------------------------*/
    /*--------------------------------------------------------------------------------------------------------------------*/

    public function validVarchar($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {
        if ($obligatoire == 1) {
            if (empty($variable)) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }
        if (!empty($variable) && !filter_var($variable, FILTER_SANITIZE_STRING)) {
            return $this->errors[$champs] = " <em>n'est pas conforme.</em>";
        } else {
            if (!empty($variable) && strlen($variable) < $tailleMin || strlen($variable) > $tailleMax) {
                return $this->errors[$champs] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " caractères.</em>";
            } else {
                if (!empty($variable) && $controle_balise == 1 && preg_match('#([<>][a-zA-Z\s]+[<>]|[<>]\/[a-z]+[<>])#', $variable)) {
                    return $this->errors[$champs] = " <em>ne doit pas comporter de chevron ou de balise.</em>";
                } else {

                    if ($unique == 1) {
                        $tableName = $this->Nom_Tab->existItem($champs, $variable, $table, $edit, $id);
                        if ($tableName == TRUE) {
                            return $this->errors[$champs] = '<em>est déjà utilisé.</em>';
                        } else {
                            return $this->errors[$champs] = null;
                        }
                    } else {
                        return $this->errors[$champs] = null;
                    }

                }
            }
        }
    }

    public function validVarcharExtraAtrib($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {
        if ($obligatoire == 1) {
            if (empty($variable)) {
                return $this->errors[$champs][] = " <em>n'est pas renseigné.</em>";
            }
        }
        if (!empty($variable) && !filter_var($variable, FILTER_SANITIZE_STRING)) {
            return $this->errors[$champs][] = " <em>n'est pas conforme.</em>";
        } else {
            if (!empty($variable) && strlen($variable) < $tailleMin || strlen($variable) > $tailleMax) {
                return $this->errors[$champs][] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " caractères.</em>";
            } else {
                if (!empty($variable) && $controle_balise == 1 && preg_match('#([<>][a-zA-Z\s]+[<>]|[<>]\/[a-z]+[<>])#', $variable)) {
                    return $this->errors[$champs][] = " <em>ne doit pas comporter de chevron ou de balise.</em>";
                } else {

                    if ($unique == 1) {
                        $tableName = $this->Nom_Tab->existItem($champs, $variable, $table, $edit, $id);
                        if ($tableName == TRUE) {
                            return $this->errors[$champs][] = '<em>est déjà utilisé.</em>';
                        } else {
                            return $this->errors[$champs][] = null;
                        }
                    } else {
                        return $this->errors[$champs][] = null;
                    }

                }
            }
        }
    }

    public function validInt($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {

        if ($obligatoire == 1) {

            if (empty($variable) && $variable < 0) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }

            if ($variable == '' || $variable == null) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }
        $options = array('options' => array(
            'min_range' => $tailleMin,
            'max_range' => $tailleMax
        ));

        if ((!empty($variable) || $variable == 0) && is_int($variable) && !filter_var($variable, FILTER_VALIDATE_INT, $options)) {
            return $this->errors[$champs] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " et être un nombre entier.</em>";
        } elseif (!empty($variable) && !is_float($variable) && !filter_var($variable, FILTER_VALIDATE_FLOAT, $options)) {
            return $this->errors[$champs] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " et être un nombre entier ou décimal.</em>";
        } else {
            if ($unique == 1) {
                $tableName = $this->Nom_Tab->existItem($champs, $variable, $table, $edit, $id);
                if ($tableName == TRUE) {
                    return $this->errors[$champs] = '<em>est déjà utilisé.</em>';
                } else {
                    return $this->errors[$champs] = null;
                }
            } else {
                return $this->errors[$champs] = null;
            }
        }

    }

    public function validBoolean($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {
        if ($obligatoire == 1) {
            if (empty($variable)) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }
        if (!empty($variable) && !filter_var($variable, FILTER_VALIDATE_BOOLEAN)) {
            return $this->errors[$champs] = " <em>n'est pas conforme.</em>";
        } else {
            if (!empty($variable) && strlen($variable) < $tailleMin || strlen($variable) > $tailleMax) {
                return $this->errors[$champs] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " caractères.</em>";
            } else {
                if (!empty($variable) && $controle_balise == 1 && preg_match('#([<>][a-zA-Z\s]+[<>]|[<>]\/[a-z]+[<>])#', $variable)) {
                    return $this->errors[$champs] = " <em>ne doit pas comporter de chevron ou de balise.</em>";
                } else {
                    return $this->errors[$champs] = null;
                }
            }
        }
    }

    public function invalidDateTime($champs)
    {

        return $this->errors[$champs] = " <em>incomplet.</em>";

    }

    public function validDate($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {

        if ($obligatoire == 1) {
            if (empty($variable)) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }

        if (!empty($variable) && preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $variable, $parts)) {
            if (checkdate($parts[2], $parts[3], $parts[1])) {
                return $this->errors[$champs] = null;
            } else {
                return $this->errors[$champs] = " n'est pas valide.";
            }
        } else {
            return $this->errors[$champs] = null;
        }
    }

    public function validDateTime($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {
        if ($obligatoire == 1) {
            if (empty($variable)) {

                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }

        if (!empty($variable) && !preg_match('#(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})#', $variable)) {

            return $this->errors[$champs] = " <em>n'est pas au bon format.</em>";
        } else {
            return $this->errors[$champs] = null;
        }
    }

    public function validUrl($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {
        if ($obligatoire == 1) {
            if (empty($variable)) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }
        if (!empty($variable) && !filter_var($variable, FILTER_VALIDATE_URL)) {
            return $this->errors[$champs] = " <em>n'est pas conforme.</em>";
        } else {
            if (!empty($variable) && strlen($variable) < $tailleMin || strlen($variable) > $tailleMax) {
                return $this->errors[$champs] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " caractères.</em>";
            } else {
                if (!empty($variable) && $controle_balise == 1 && preg_match('#([<>][a-zA-Z\s]+[<>]|[<>]\/[a-z]+[<>])#', $variable)) {
                    return $this->errors[$champs] = " <em>ne doit pas comporter de chevron ou de balise.</em>";
                } else {

                    if ($unique == 1) {
                        $tableName = $this->Nom_Tab->existItem($champs, $variable, $table, $edit, $id);
                        if ($tableName == TRUE) {
                            return $this->errors[$champs] = '<em>est déjà utilisé.</em>';
                        } else {
                            return $this->errors[$champs] = null;
                        }
                    } else {
                        return $this->errors[$champs] = null;
                    }

                }
            }
        }
    }

    public function validRadio($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {

    }

    public function validCheckbox($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {

    }

    public function validFile($post_file, $extValue, $ListeTypeMIME, $champs, $confField, $fileName)
    {

        if (isset($post_file) && !empty($post_file) && isset($extValue) && !empty($extValue)) {

            // Récup le Type MIME du fichier. --------------------------------------------------------------------------
            $file = $post_file;
            $mime = substr($file, strpos($file, "data:"), strpos($file, ",") - strlen($file));
            $mimeFilter = explode(':', $mime);
            $TypeMimeFile = explode(';', $mimeFilter[1]);
            $TypeMimeFile = $TypeMimeFile[0];

            // Vérifie le type MIME et l'extention du fichier et si il est listé dans la conf MIME.---------------------
            if (array_key_exists($extValue, $ListeTypeMIME)) {
                foreach ($ListeTypeMIME[$extValue] as $l_ext_key => $l_ext) {
                    if (in_array('.' . $extValue, $ListeTypeMIME[$extValue])) {
                        // Vérifie si l'extention est autorisé dans la conf Field. -------------------------------------
                        if ($confField['type_input'] == 'file') {
                            if (in_array($extValue, $confField['extension'])) {
                                if (!array_key_exists($TypeMimeFile, $ListeTypeMIME[$extValue])) {
                                    return $this->errors[$champs][$fileName . '.' . $extValue] = '<em>Fichier non autorisée.</em>';
                                } else {
                                    return $this->errors[$champs][$fileName . '.' . $extValue] = null;
                                }
                            } else {
                                return $this->errors[$champs][$fileName . '.' . $extValue] = '<em>Fichier non autorisée.</em>';
                            }
                        } else {
                            return $this->errors[$champs][$fileName . '.' . $extValue] = '<em>Fichier reconnu.</em>';
                        }
                    } else {
                        return $this->errors[$champs][$fileName . '.' . $extValue] = '<em>Fichier non reconnu</em>';
                    }
                }
            } else {
                return $this->errors[$champs][$fileName . '.' . $extValue] = '<em>Fichier non reconnu.</em>';
            }
        } elseif ($post_file == null || $extValue == null) {
            return $this->errors[$champs][$fileName . '.' . $extValue] = null;
        }


    }

    public function validPhone($variable, $champs, $obligatoire, $controle_balise, $tailleMin, $tailleMax, $unique, $table = null, $edit = null, $id = null)
    {

        if ($obligatoire == 1) {
            if (empty($variable)) {
                return $this->errors[$champs] = " <em>n'est pas renseigné.</em>";
            }
        }
        if (!empty($variable) && !preg_match('#(0|\+33)[1-9]( *[0-9]{2}){4}#', $variable)) {
            return $this->errors[$champs] = "<em> n'est pas valide.</em>";
        } elseif (!empty($variable) && strlen($variable) < $tailleMin || strlen($variable) > $tailleMax) {
            return $this->errors[$champs] = " <em>doit être compris entre " . $tailleMin . " et " . $tailleMax . " caractères.</em>";
        } else {
            $international = false;
            $variable = preg_replace('/[^0-9]+/', '', $variable);
            $variable = substr($variable, -9);
            $motif = $international ? '+33 (\1) \2 \3 \4 \5' : '0\1 \2 \3 \4 \5';
            $variable = preg_replace('/(\d{1})(\d{2})(\d{2})(\d{2})(\d{2})/', $motif, $variable);
            if ($unique == 1) {
                $tableName = $this->Nom_Tab->existItem($variable, $table, $edit, $id);
                if ($tableName == TRUE) {
                    return $this->errors[$champs] = '<em>est déjà utilisé.</em>';
                } else {
                    return $this->errors[$champs] = null;
                }
            } else {
                return $this->errors[$champs] = null;
            }

        }
    }

    public function validDateInter($date_debut, $date_fin, $tech_id, $edit = null, $date_debut_old = null, $date_fin_old = null)
    {


        if ($date_debut > $date_fin) {
            return $this->errors['dateinter'] = '<em>La date de début ne peut pas être inférieur à la date de fin.</em>';
        } else {
            if (isset($date_debut) && !empty($date_debut) && isset($date_fin) && !empty($date_fin) && isset($tech_id) && !empty($tech_id)) {

                $sql = "SELECT id FROM planning_inter 
                    WHERE user_id = " . abs($tech_id) . " 
                    AND hidden = 0
                    AND date_debut BETWEEN '" . $date_debut . "' AND '" . $date_fin . "'
                    AND date_fin BETWEEN '" . $date_debut . "' AND '" . $date_fin . "'";


                $Exist = $this->Nom_Tab->findItem($sql);
                if ($Exist) {
                    return $this->errors['dateinter'] = '<em>La période indiquée pour l\'intervention est déjà prise pour ce technicien.</em>';
                } else {
                    return $this->errors['dateinter'] = null;
                }
            } else {
                return $this->errors['dateinter'] = '<em>Aucune période de planification et/ou technicien n\'a été renseigné.</em>';
            }
        }

    }

//    public function VerifPlaniftech($champs, $tech_id, $id_planif, $date_debut, $date_fin)
//    {
//
//        if ($date_debut > $date_fin) {
//            return $this->errors['dateinter'] = '<em>La date de début ne peut pas être inférieur à la date de fin.</em>';
//        } else {
//            $planifForTech1 = 0;
//            $ifPlanifForTech = $this->Nom_Tab->findItem('SELECT id_user, id_planif
//                                                                FROM users_planning_inter
//                                                                WHERE id_user =' . abs($tech_id) . ' AND id_planif != ' . abs($id_planif));
//
//            foreach ($ifPlanifForTech as $ifPlanifForTechK => $ifPlanifForTechV) {
//                $ifDejaPlanif = $this->Nom_Tab->findItem('SELECT id
//                                                 FROM planning_inter
//                                                 WHERE id = ' . $ifPlanifForTechV->id_planif . '
//                                                 AND (date_debut BETWEEN "' . $date_debut . '" AND "' . $date_fin . '"
//                                                 OR date_fin BETWEEN "' . $date_debut . '" AND "' . $date_fin . '")');
//                if ($ifDejaPlanif != null) {
//                    $planifForTech1++;
//                }
//            }
//
//
//            if ($planifForTech1 != 0) {
//                return $this->errors[$champs] = '<em>déjà prit sur cette plage horaire.</em>';
//            } else {
//                return $this->errors[$champs] = null;
//            }
//
//        }
//
//
//    }

    public function sendError($champ, $str)
    {
        return $this->errors[$champ] = '<em>' . $str . '</em>';
    }

    public function TypeError($type)
    {
        return $this->errors['typeError'] = $type;
    }

}
