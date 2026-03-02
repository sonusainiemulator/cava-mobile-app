<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cava_user_wines_model extends App_Model
{
    private $table = 'cava_user_wines';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_user_wine_ids($user_id)
    {
        $this->db->select('wine_id');
        $this->db->where('user_id', (int)$user_id);
        $rows = $this->db->get(db_prefix() . $this->table)->result_array();
        return array_map(function($r){ return (int)$r['wine_id']; }, $rows);
    }

    public function add_for_user($user_id, $wine_id)
    {
        $this->db->insert(db_prefix() . $this->table, [
            'user_id' => (int)$user_id,
            'wine_id' => (int)$wine_id,
        ]);
        return $this->db->affected_rows() > 0;
    }

    public function remove_for_user($user_id, $wine_id)
    {
        $this->db->where('user_id', (int)$user_id);
        $this->db->where('wine_id', (int)$wine_id);
        $this->db->delete(db_prefix() . $this->table);
        return $this->db->affected_rows() > 0;
    }

    public function get_all_with_users()
    {
        $sql = 'SELECT uw.id, uw.user_id, uw.wine_id, uw.date_added,
                       w.name as wine_name, w.image as wine_image,
                       s.firstname, s.lastname, s.email
                FROM ' . db_prefix() . 'cava_user_wines uw
                LEFT JOIN ' . db_prefix() . 'cava_wines w ON w.id = uw.wine_id
                LEFT JOIN ' . db_prefix() . 'staff s ON s.staffid = uw.user_id
                ORDER BY uw.date_added DESC';
        return $this->db->query($sql)->result();
    }
}
