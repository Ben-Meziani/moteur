<?php

namespace Core\Table;

use Core\Database\Database;

class Table
{

    protected $table;
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db; // Attribut l'instance en cour (connexion base de données)
        if (is_null($this->table)) {
            $parts = explode('\\', get_class($this));
            $class_name = end($parts);
            $this->table = strtolower(str_replace('Table', '', $class_name)); // Charge les moules qui permettent de générer le format objet pour les items.(les moules sont dans le dossier *racine*/app/Table)
        }
    }

    public function all()
    {
        $req = $this->query('SELECT * FROM ' . $this->table);
        return $req;
    }

    public function find($id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE ID = ? ", [$id], true);
    }

    public function update($id, $fields)
    {

        $sql_parts = [];
        $attributes = [];

        foreach ($fields as $k => $v) {
            $sql_parts[] = "$k = ?";
            $attributes[] = $v;
        }
        $attributes[] = $id;
        $sql_part = implode(', ', $sql_parts);

        return $this->query("UPDATE {$this->table} SET $sql_part WHERE ID = ?", $attributes, true);
    }

    public function create($fields)
    {


        $sql_parts = [];
        $attributes = [];
        foreach ($fields as $k => $v) {
            $sql_parts[] = "$k = ?";
            $attributes[] = $v;
        }
        $sql_part = implode(', ', $sql_parts);

        return $this->query("INSERT INTO {$this->table} SET $sql_part", $attributes, true);
    }

    public function delete($id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE ID = ?", [$id], true);
    }

    public function liste($key, $value)
    {
        $records = $this->all();
        $return = [];
        foreach ($records as $v) {
            $return[$v->$key] = $v->$value;
        }
        return $return;
    }

    public function query($statement, $attributes = null, $one = false)
    {

        if ($attributes) {
            return $this->db->prepare($statement, $attributes, str_replace('Table', 'Entity', get_class($this)), $one);
        } else {
            return $this->db->query($statement, str_replace('Table', 'Entity', get_class($this)), $one);
        }

    }

}

