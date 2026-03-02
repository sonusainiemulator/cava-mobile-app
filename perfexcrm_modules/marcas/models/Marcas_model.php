<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Marcas_model extends App_Model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        // Usamos directamente la tabla existente
        $this->table = 'tblmarcas';
    }

    public function get($id = '')
    {
        if ($id !== '' && $id !== null) {
            $this->db->where('id', $id);
            return $this->db->get($this->table)->row_array();
        }

        return $this->db->get($this->table)->result_array();
    }

    public function get_fields()
    {
        return $this->db->list_fields($this->table);
    }

    public function get_image_fields()
    {
        // Heurística: consideramos campos de imagen por nombre
        $fields = $this->get_fields();
        $image_fields = [];

        foreach ($fields as $field) {
            $lower = strtolower($field);
            if (strpos($lower, 'imagen') !== false
                || strpos($lower, 'image') !== false
                || strpos($lower, 'logo') !== false
                || strpos($lower, 'foto') !== false) {
                $image_fields[] = $field;
            }
        }

        return $image_fields;
    }

    public function add($data)
    {
        $fields = $this->get_fields();
        $insert = [];

        foreach ($fields as $field) {
            if ($field === 'id') {
                continue;
            }
            if (isset($data[$field])) {
                $insert[$field] = $data[$field];
            }
        }

        if (empty($insert)) {
            return false;
        }

        $this->db->insert($this->table, $insert);
        return $this->db->insert_id();
    }

    public function update($data, $id)
    {
        $fields = $this->get_fields();
        $update = [];

        foreach ($fields as $field) {
            if ($field === 'id') {
                continue;
            }
            if (isset($data[$field])) {
                $update[$field] = $data[$field];
            }
        }

        if (empty($update)) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->update($this->table, $update);
        return $this->db->affected_rows() > 0;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows() > 0;
    }
}
