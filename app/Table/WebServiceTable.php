<?php

namespace App\Table;

use Core\Table\Table;


class WebServiceTable extends Table
{

    protected $json;

    public function getFileJson()
    {
        $ObjJson = $this->json = @file_get_contents("../json/data.json");
        if ($ObjJson) {
            return json_decode($ObjJson);
        } else {
            return "Aucun fichier.";
        }
    }

    public function lastID($table)
    {
        return $this->query("
			SELECT id FROM " . $table . " WHERE id = LAST_INSERT_ID();
		");
    }

    // Recup un item par ID de l'item.
    public function findItem($reqSql)
    {
        return $this->query($reqSql);
    }

}
