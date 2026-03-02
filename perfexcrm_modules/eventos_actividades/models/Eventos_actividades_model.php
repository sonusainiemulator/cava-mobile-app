<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Eventos_actividades_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get activities list or single activity
     * @param  string $id Optional activity ID
     * @return mixed     Array of activities or object
     */
    public function get_actividades($id = '')
    {
        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'eventos_actividades_catalogo')->row();
        }

        return $this->db->get(db_prefix() . 'eventos_actividades_catalogo')->result_array();
    }

    /**
     * Add new activity
     * @param array $data Activity data
     * @return boolean
     */
    public function add_actividad($data)
    {
        $insert = [
            'nombre' => isset($data['nombre']) ? $data['nombre'] : '',
            'puntos' => isset($data['puntos']) ? (int) $data['puntos'] : 0,
            'auto_award'   => isset($data['auto_award']) ? 1 : 0,
            'trigger_type' => isset($data['trigger_type']) ? $data['trigger_type'] : null,
            'threshold'    => isset($data['threshold']) && $data['threshold'] !== '' ? (int)$data['threshold'] : 0,
        ];

        $this->db->insert(db_prefix() . 'eventos_actividades_catalogo', $insert);
        return (bool) $this->db->insert_id();
    }

    /**
     * Update activity
     * @param  int $id   Activity ID
     * @param  array $data Activity data
     * @return boolean
     */
    public function update_actividad($id, $data)
    {
        $update = [
            'nombre' => isset($data['nombre']) ? $data['nombre'] : '',
            'puntos' => isset($data['puntos']) ? (int) $data['puntos'] : 0,
            'auto_award'   => isset($data['auto_award']) ? 1 : 0,
            'trigger_type' => isset($data['trigger_type']) ? $data['trigger_type'] : null,
            'threshold'    => isset($data['threshold']) && $data['threshold'] !== '' ? (int)$data['threshold'] : 0,
        ];

        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'eventos_actividades_catalogo', $update);
    }

    /**
     * Delete activity
     * @param  int $id Activity ID
     * @return boolean
     */
    public function delete_actividad($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'eventos_actividades_catalogo');

        return $this->db->affected_rows() > 0;
    }

    
    /**
     * Add points log
     * @param array $data   Log data
     * @param string $source Source (manual/auto)
     * @param mixed $meta   Metadata
     * @return boolean
     */
    public function add_log($data, $source = 'manual', $meta = null)
    {
        $actividad = $this->get_actividades($data['actividad_id']);

        $insert = [
            'staff_id'      => (int) $data['staff_id'],
            'actividad_id'  => (int) $data['actividad_id'],
            'puntos'        => $actividad ? (int) $actividad->puntos : 0,
            'fecha'         => date('Y-m-d H:i:s'),
            'meta'          => $meta ? json_encode($meta) : json_encode(['source' => $source]),
        ];

        $this->db->insert(db_prefix() . 'eventos_actividades_logs', $insert);
        $ok = (bool) $this->db->insert_id();

        if ($ok) {
            $this->sync_staff_points_custom_field((int)$data['staff_id']);
        }

        return $ok;
    }


    /**
     * Get points logs
     * @return array
     */
    public function get_logs()
    {
        $this->db->select('l.*, a.nombre as actividad_nombre, s.firstname, s.lastname');
        $this->db->from(db_prefix() . 'eventos_actividades_logs as l');
        $this->db->join(db_prefix() . 'eventos_actividades_catalogo as a', 'a.id = l.actividad_id', 'left');
        $this->db->join(db_prefix() . 'staff as s', 's.staffid = l.staff_id', 'left');
        $this->db->order_by('l.fecha', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Get totals by user
     * @return array
     */
    public function get_totales_por_usuario()
    {
        $this->db->select('l.staff_id, SUM(l.puntos) as total_puntos, s.firstname, s.lastname');
        $this->db->from(db_prefix() . 'eventos_actividades_logs as l');
        $this->db->join(db_prefix() . 'staff as s', 's.staffid = l.staff_id', 'left');
        $this->db->group_by('l.staff_id');
        $this->db->order_by('total_puntos', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Get events list or single event
     * @param  string $id Optional event ID
     * @return mixed     Array or Object
     */
    public function get_eventos($id = '')
    {
        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'eventos_actividades_eventos')->row();
        }

        return $this->db->get(db_prefix() . 'eventos_actividades_eventos')->result_array();
    }

    /**
     * Add event
     * @param array $data Event data
     * @return boolean
     */
    public function add_evento($data)
    {
        $insert = [
            'nombre'    => isset($data['nombre']) ? $data['nombre'] : '',
            'fecha'     => !empty($data['fecha']) ? $data['fecha'] : null,
            'hora'      => !empty($data['hora']) ? $data['hora'] : null,
            'pais'      => isset($data['pais']) ? $data['pais'] : null,
            'estado'    => isset($data['estado']) ? $data['estado'] : null,
            'ciudad'    => isset($data['ciudad']) ? $data['ciudad'] : null,
            'direccion' => isset($data['direccion']) ? $data['direccion'] : null,
            'lat'       => isset($data['lat']) && $data['lat'] !== '' ? (float) $data['lat'] : 0,
            'lng'       => isset($data['lng']) && $data['lng'] !== '' ? (float) $data['lng'] : 0,
        ];

        $this->db->insert(db_prefix() . 'eventos_actividades_eventos', $insert);
        return (bool) $this->db->insert_id();
    }

    /**
     * Update event
     * @param  int $id   Event ID
     * @param  array $data Event data
     * @return boolean
     */
    public function update_evento($id, $data)
    {
        $update = [
            'nombre'    => isset($data['nombre']) ? $data['nombre'] : '',
            'fecha'     => !empty($data['fecha']) ? $data['fecha'] : null,
            'hora'      => !empty($data['hora']) ? $data['hora'] : null,
            'pais'      => isset($data['pais']) ? $data['pais'] : null,
            'estado'    => isset($data['estado']) ? $data['estado'] : null,
            'ciudad'    => isset($data['ciudad']) ? $data['ciudad'] : null,
            'direccion' => isset($data['direccion']) ? $data['direccion'] : null,
            'lat'       => isset($data['lat']) && $data['lat'] !== '' ? (float) $data['lat'] : 0,
            'lng'       => isset($data['lng']) && $data['lng'] !== '' ? (float) $data['lng'] : 0,
        ];

        $this->db->where('id', $id);
        return $this->db->update(db_prefix() . 'eventos_actividades_eventos', $update);
    }

    /**
     * Delete event
     * @param  int $id Event ID
     * @return boolean
     */
    public function delete_evento($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'eventos_actividades_eventos');

        return $this->db->affected_rows() > 0;
    }


    // ------------------- Scans + Auto-award -------------------

    /**
     * Register scan
     * @param int $staff_id Staff ID
     * @param string $barcode  Barcode
     */
    public function add_scan($staff_id, $barcode = null)
    {
        $insert = [
            'staff_id' => (int)$staff_id,
            'barcode'  => $barcode,
            'fecha'    => date('Y-m-d H:i:s'),
        ];
        $this->db->insert(db_prefix().'eventos_actividades_scans', $insert);
        return (bool)$this->db->insert_id();
    }

    // ... (helper methods for scan integration would follow, keeping existing logic) ...
    
    private function get_option($name, $default = null)
    {
        if (!$this->db->table_exists(db_prefix().'options')) {
            return $default;
        }
        $this->db->where('name', $name);
        $row = $this->db->get(db_prefix().'options')->row();
        if (!$row) return $default;
        return $row->value;
    }

    private function detect_external_scan_table()
    {
        $candidates = [
            db_prefix().'escaneo_scans',
            db_prefix().'escaneo',
            db_prefix().'escaneos',
            db_prefix().'escaneo_logs',
            db_prefix().'barcode_scans',
            db_prefix().'scans',
        ];

        foreach ($candidates as $t) {
            if ($this->db->table_exists($t)) {
                return $t;
            }
        }
        return null;
    }

    private function detect_staff_col($table)
    {
        $cols = $this->db->query("SHOW COLUMNS FROM `{$table}`")->result_array();
        $names = array_map(function($c){ return $c['Field']; }, $cols);

        $candidates = ['staff_id', 'staffid', 'user_id', 'userid', 'rel_id', 'relid', 'contact_id'];
        foreach ($candidates as $c) {
            if (in_array($c, $names)) return $c;
        }
        return null;
    }

    public function count_scans_for_staff($staff_id)
    {
        $staff_id = (int)$staff_id;

        $source = $this->get_option('eventos_actividades_scan_source', 'auto'); 
        $table  = $this->get_option('eventos_actividades_scan_source_table', null);
        $col    = $this->get_option('eventos_actividades_scan_source_staff_col', 'staff_id');

        $external_table = null;

        if ($source === 'external') {
            $external_table = $table ?: $this->detect_external_scan_table();
        } elseif ($source === 'auto') {
            $external_table = $table && $this->db->table_exists($table) ? $table : $this->detect_external_scan_table();
        }

        if ($external_table && $this->db->table_exists($external_table)) {
            $staff_col = $col;
            $cols = $this->db->query("SHOW COLUMNS FROM `{$external_table}`")->result_array();
            $names = array_map(function($c){ return $c['Field']; }, $cols);
            if (!in_array($staff_col, $names)) {
                $staff_col = $this->detect_staff_col($external_table);
            }

            if ($staff_col) {
                $this->db->where($staff_col, $staff_id);
                return (int)$this->db->count_all_results($external_table);
            }
        }

        if ($this->db->table_exists(db_prefix().'eventos_actividades_scans')) {
            $this->db->where('staff_id', $staff_id);
            return (int)$this->db->count_all_results(db_prefix().'eventos_actividades_scans');
        }

        return 0;
    }

    public function evaluate_auto_awards_for_staff($staff_id)
    {
        $staff_id = (int)$staff_id;
        $awarded = 0;

        $this->db->where('auto_award', 1);
        $this->db->where('trigger_type', 'scans');
        $acts = $this->db->get(db_prefix().'eventos_actividades_catalogo')->result_array();

        if (!$acts || count($acts) === 0) {
            return 0;
        }

        $scan_count = $this->count_scans_for_staff($staff_id);

        foreach ($acts as $a) {
            $threshold = isset($a['threshold']) ? (int)$a['threshold'] : 0;
            if ($threshold <= 0) continue;

            if ($scan_count >= $threshold) {
                $this->db->where('staff_id', $staff_id);
                $this->db->where('actividad_id', (int)$a['id']);
                $this->db->limit(1);
                $existing = $this->db->get(db_prefix().'eventos_actividades_logs')->row();

                if (!$existing) {
                    $meta = [
                        'source'    => 'auto',
                        'trigger'   => 'scans',
                        'threshold' => $threshold,
                        'scan_count'=> $scan_count,
                    ];

                    $ok = $this->add_log([
                        'staff_id' => $staff_id,
                        'actividad_id' => (int)$a['id'],
                    ], 'auto', $meta);

                    if ($ok) { $awarded++; }
                }
            }
        }

        return $awarded;
    }

    public function evaluate_auto_awards_all()
    {
        $awarded = 0;

        $this->db->select('staff_id');
        $this->db->group_by('staff_id');
        $staffs = $this->db->get(db_prefix().'eventos_actividades_scans')->result_array();

        foreach ($staffs as $s) {
            $awarded += $this->evaluate_auto_awards_for_staff((int)$s['staff_id']);
        }

        return $awarded;
    }

    public function sync_staff_points_custom_field($staff_id)
    {
        $staff_id = (int)$staff_id;

        if (!$this->db->table_exists(db_prefix().'customfields') || !$this->db->table_exists(db_prefix().'customfieldsvalues')) {
            return false;
        }

        $this->db->select('SUM(puntos) as total');
        $this->db->where('staff_id', $staff_id);
        $row = $this->db->get(db_prefix().'eventos_actividades_logs')->row();
        $total = $row && $row->total ? (int)$row->total : 0;

        $this->db->where('slug', 'eventos_actividades_puntos');
        $cf = $this->db->get(db_prefix().'customfields')->row();
        if (!$cf) return false;

        $this->db->where('fieldid', (int)$cf->id);
        $this->db->where('relid', $staff_id);
        $existing = $this->db->get(db_prefix().'customfieldsvalues')->row();

        if ($existing) {
            $this->db->where('id', (int)$existing->id);
            $this->db->update(db_prefix().'customfieldsvalues', ['value' => (string)$total]);
        } else {
            $this->db->insert(db_prefix().'customfieldsvalues', [
                'relid'   => $staff_id,
                'fieldid' => (int)$cf->id,
                'value'   => (string)$total,
            ]);
        }

        return true;
    }

}
