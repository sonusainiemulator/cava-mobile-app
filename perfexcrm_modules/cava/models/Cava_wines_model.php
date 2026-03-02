<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cava_wines_model extends App_Model
{
    private $table = 'cava_wines';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get(db_prefix() . $this->table)->result();
    }

    public function get($id)
    {
        $this->db->where('id', (int)$id);
        return $this->db->get(db_prefix() . $this->table)->row();
    }

    public function add($data)
    {
        $this->db->insert(db_prefix() . $this->table, $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update($id, $data)
    {
        $this->db->where('id', (int)$id);
        $this->db->update(db_prefix() . $this->table, $data);
        return $this->db->affected_rows() > 0;
    }

    public function get_by_barcode($barcode)
    {
        $this->db->where('barcode', $barcode);
        return $this->db->get(db_prefix() . $this->table)->row();
    }

    public function delete($id)
    {
        $this->db->where('id', (int)$id);
        $this->db->delete(db_prefix() . $this->table);
        return $this->db->affected_rows() > 0;
    }
}
