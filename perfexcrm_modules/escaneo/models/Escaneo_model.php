<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Escaneo_model extends App_Model
{
    public function insert_scan($data)
    {
        $this->db->insert(db_prefix() . 'escaneo_scans', $data);
        return $this->db->insert_id();
    }

    public function get_all_scans()
    {
        return $this->db->order_by('fecha_escaneo', 'DESC')
            ->get(db_prefix() . 'escaneo_scans')
            ->result_array();
    }

    public function get_all_scans_with_staff()
    {
        $this->db->select('s.*, st.firstname, st.lastname');
        $this->db->from(db_prefix() . 'escaneo_scans s');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = s.staff_id', 'left');
        $this->db->order_by('s.fecha_escaneo', 'DESC');
        return $this->db->get()->result_array();
    }

    // Maestro de bebidas
    public function get_all_bebidas()
    {
        return $this->db->order_by('id', 'DESC')
            ->get(db_prefix() . 'escaneo_bebidas')
            ->result_array();
    }

    public function get_bebida($id)
    {
        return $this->db->where('id', (int)$id)
            ->get(db_prefix() . 'escaneo_bebidas')
            ->row_array();
    }

    public function insert_bebida($data)
    {
        $exists = $this->db->where('codigo_barras', $data['codigo_barras'])
            ->get(db_prefix() . 'escaneo_bebidas')
            ->row_array();
        if ($exists) {
            return false;
        }
        $this->db->insert(db_prefix() . 'escaneo_bebidas', $data);
        return $this->db->insert_id();
    }

    public function update_bebida($id, $data)
    {
        $other = $this->db->where('codigo_barras', $data['codigo_barras'])
            ->where('id !=', (int)$id)
            ->get(db_prefix() . 'escaneo_bebidas')
            ->row_array();
        if ($other) {
            return false;
        }
        $this->db->where('id', (int)$id)->update(db_prefix() . 'escaneo_bebidas', $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete_bebida($id)
    {
        $this->db->where('id', (int)$id)->delete(db_prefix() . 'escaneo_bebidas');
        return $this->db->affected_rows() > 0;
    }

    // Reporte por rango: totales por usuario
    public function get_scan_report_by_user($from, $to)
    {
        $fromDT = $from . ' 00:00:00';
        $toDT   = $to   . ' 23:59:59';

        $this->db->select('s.staff_id, st.firstname, st.lastname, COUNT(*) as total_scans, MIN(s.fecha_escaneo) as first_scan, MAX(s.fecha_escaneo) as last_scan');
        $this->db->from(db_prefix() . 'escaneo_scans s');
        $this->db->join(db_prefix() . 'staff st', 'st.staffid = s.staff_id', 'left');
        $this->db->where('s.fecha_escaneo >=', $fromDT);
        $this->db->where('s.fecha_escaneo <=', $toDT);
        $this->db->group_by('s.staff_id');
        $this->db->order_by('total_scans', 'DESC');
        $rows = $this->db->get()->result_array();

        $total = 0;
        foreach ($rows as $r) {
            $total += (int)$r['total_scans'];
        }

        return ['rows' => $rows, 'total' => $total];
    }
}
