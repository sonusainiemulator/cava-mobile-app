<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mezcal_model extends App_Model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'mezcal_contents';
    }

    public function get_by_type($type)
    {
        return $this->db
            ->where('type', $type)
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    public function add($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        $content = $this->db->where('id', $id)->get($this->table)->row();

        if ($content && !empty($content->file)) {
            $path = FCPATH . $content->file;
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $this->db->where('id', $id)->delete($this->table);
        return $this->db->affected_rows() > 0;
    }
}
