<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tequila_model extends App_Model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'tequila_items';
    }

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_by_type($type)
    {
        $this->db->where('type', $type);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result_array();
    }
}
