<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Avatares_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        if ($id !== null) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'avatares')->row();
        }

        $this->db->order_by('points_required', 'ASC');
        return $this->db->get(db_prefix() . 'avatares')->result();
    }

    public function create($data)
    {
        $avatar_data = [
            'name'            => $data['name'],
            'points_required' => (int) $data['points_required'],
            'active'          => isset($data['active']) ? 1 : 0,
        ];

        if (!empty($data['image'])) {
            $avatar_data['image'] = $data['image'];
        }

        $this->db->insert(db_prefix() . 'avatares', $avatar_data);

        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $avatar_data = [
            'name'            => $data['name'],
            'points_required' => (int) $data['points_required'],
            'active'          => isset($data['active']) ? 1 : 0,
        ];

        if (!empty($data['image'])) {
            $avatar_data['image'] = $data['image'];
        }

        $this->db->where('id', $id);

        return $this->db->update(db_prefix() . 'avatares', $avatar_data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete(db_prefix() . 'avatares');
    }

    public function get_avatar_for_points($points)
    {
        $this->db->where('active', 1);
        $this->db->where('points_required <=', (int) $points);
        $this->db->order_by('points_required', 'DESC');
        $this->db->limit(1);

        return $this->db->get(db_prefix() . 'avatares')->row();
    }
}
