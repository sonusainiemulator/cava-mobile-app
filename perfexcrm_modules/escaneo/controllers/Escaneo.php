<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Escaneo extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('escaneo/Escaneo_model', 'escaneo_model');
        $this->load->helper('escaneo/escaneo');
    }

    public function index()
    {
        redirect(admin_url('escaneo/escaneos'));
    }

    public function escaneos()
    {
        if (!has_permission('escaneo', '', 'view')) {
            access_denied('escaneo');
        }

        $data['title'] = _l('escaneo_scans_title');
        $data['scans'] = $this->escaneo_model->get_all_scans_with_staff();
        $this->load->view('escaneo/escaneos/manage', $data);
    }

    public function mapa()
    {
        if (!has_permission('escaneo', '', 'view')) {
            access_denied('escaneo');
        }

        $data['title'] = _l('escaneo_mapa_global');
        $data['bebidas'] = $this->escaneo_model->get_all_bebidas();
        $this->load->view('escaneo/map', $data);
    }

    public function store()
    {
        if (!has_permission('escaneo', '', 'create')) {
            access_denied('escaneo');
        }

        $post = $this->input->post();
        $required = ['codigo_barras','marca','nombre_tequila','presentacion','grados_alcohol','precio','lat','lng'];

        foreach ($required as $field) {
            if (!isset($post[$field]) || $post[$field] === '') {
                set_alert('danger', _l('escaneo_validation_error'));
                redirect(admin_url('escaneo/mapa'));
            }
        }

        $ean = escaneo_normalize_ean13($post['codigo_barras']);
        if ($ean === null) {
            set_alert('danger', _l('escaneo_ean_error'));
            redirect(admin_url('escaneo/mapa'));
        }

        $data = [
            'bebida_id'      => isset($post['bebida_id']) && $post['bebida_id'] !== '' ? (int)$post['bebida_id'] : null,
            'codigo_barras'  => $ean,
            'marca'          => $post['marca'],
            'nombre_tequila' => $post['nombre_tequila'],
            'presentacion'   => $post['presentacion'],
            'grados_alcohol' => (float) $post['grados_alcohol'],
            'precio'         => (float) $post['precio'],
            'lat'            => (float) $post['lat'],
            'lng'            => (float) $post['lng'],
            'staff_id'       => get_staff_user_id(),
        ];

        $this->escaneo_model->insert_scan($data);

        set_alert('success', _l('escaneo_scan_saved'));
        redirect(admin_url('escaneo/escaneos'));
    }

    public function scans_json()
    {
        if (!has_permission('escaneo', '', 'view')) {
            ajax_access_denied();
        }

        $scans = $this->escaneo_model->get_all_scans();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($scans);
        die;
    }

    // Maestro de bebidas
    public function bebidas()
    {
        if (!has_permission('escaneo', '', 'view')) {
            access_denied('escaneo');
        }

        $data['title'] = _l('escaneo_bebidas_title');
        $data['bebidas'] = $this->escaneo_model->get_all_bebidas();
        $this->load->view('escaneo/bebidas/manage', $data);
    }

    public function bebida_form($id = null)
    {
        if (!has_permission('escaneo', '', 'create') && !has_permission('escaneo', '', 'edit')) {
            access_denied('escaneo');
        }

        $data['title'] = $id ? _l('escaneo_bebidas_edit') : _l('escaneo_bebidas_new');
        $data['bebida'] = $id ? $this->escaneo_model->get_bebida((int)$id) : null;

        $this->load->view('escaneo/bebidas/form', $data);
    }

    public function save_bebida()
    {
        if (!has_permission('escaneo', '', 'create') && !has_permission('escaneo', '', 'edit')) {
            access_denied('escaneo');
        }

        $post = $this->input->post();
        $id = isset($post['id']) && $post['id'] !== '' ? (int)$post['id'] : null;

        $required = ['codigo_barras','marca','nombre_tequila','presentacion','grados_alcohol','precio'];
        foreach ($required as $field) {
            if (!isset($post[$field]) || $post[$field] === '') {
                set_alert('danger', _l('escaneo_validation_error'));
                redirect(admin_url('escaneo/bebidas'));
            }
        }

        $ean = escaneo_normalize_ean13($post['codigo_barras']);
        if ($ean === null) {
            set_alert('danger', _l('escaneo_ean_error'));
            redirect(admin_url('escaneo/bebida_form' . ($id ? '/' . $id : '')));
        }

        $barcode_svg = isset($post['barcode_svg']) ? $post['barcode_svg'] : null;

        // Upload imagen (opcional)
        $imagenFile = null;
        if (isset($_FILES['imagen']) && !empty($_FILES['imagen']['name'])) {
            $uploadPath = FCPATH . 'uploads/escaneo/bebidas/';
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            $config['upload_path']   = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png|webp';
            $config['max_size']      = 5120; // 5MB
            $config['encrypt_name']  = true;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('imagen')) {
                set_alert('danger', _l('escaneo_imagen_error') . ' ' . $this->upload->display_errors('', ''));
                redirect(admin_url('escaneo/bebida_form' . ($id ? '/' . $id : '')));
            } else {
                $up = $this->upload->data();
                $imagenFile = $up['file_name'];
            }
        }

        // Guardar svg opcional
        if ($barcode_svg && stripos($barcode_svg, '<svg') !== false) {
            $uploadBase = FCPATH . 'uploads/escaneo/barcodes/';
            if (!is_dir($uploadBase)) {
                @mkdir($uploadBase, 0755, true);
            }
            @file_put_contents($uploadBase . $ean . '.svg', $barcode_svg);
        }

        $data = [
            'codigo_barras'  => $ean,
            'marca'          => $post['marca'],
            'nombre_tequila' => $post['nombre_tequila'],
            'presentacion'   => $post['presentacion'],
            'grados_alcohol' => (float)$post['grados_alcohol'],
            'precio'         => (float)$post['precio'],
            'barcode_svg'    => $barcode_svg,
        ];

        if ($imagenFile !== null) {
            $data['imagen'] = $imagenFile;
        }

        $ok = $id ? $this->escaneo_model->update_bebida($id, $data) : $this->escaneo_model->insert_bebida($data);

        if ($ok) {
            set_alert('success', _l('escaneo_bebidas_saved'));
        } else {
            set_alert('danger', _l('escaneo_bebidas_save_error'));
        }

        redirect(admin_url('escaneo/bebidas'));
    }

    public function delete_bebida($id)
    {
        if (!has_permission('escaneo', '', 'delete')) {
            access_denied('escaneo');
        }

        $ok = $this->escaneo_model->delete_bebida((int)$id);

        if ($ok) {
            set_alert('success', _l('escaneo_bebidas_deleted'));
        } else {
            set_alert('danger', _l('escaneo_bebidas_delete_error'));
        }

        redirect(admin_url('escaneo/bebidas'));
    }

    public function bebida_json($id)
    {
        if (!has_permission('escaneo', '', 'view')) {
            ajax_access_denied();
        }

        $bebida = $this->escaneo_model->get_bebida((int)$id);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($bebida);
        die;
    }

    // Reportes
    public function reportes()
    {
        if (!has_permission('escaneo', '', 'view')) {
            access_denied('escaneo');
        }

        $from = $this->input->get('from');
        $to   = $this->input->get('to');

        $data['title'] = _l('escaneo_reportes_title');
        $data['from'] = $from;
        $data['to'] = $to;

        $data['rows'] = [];
        $data['total'] = 0;

        if ($from && $to) {
            $result = $this->escaneo_model->get_scan_report_by_user($from, $to);
            $data['rows'] = $result['rows'];
            $data['total'] = $result['total'];
        }

        $this->load->view('escaneo/reportes/index', $data);
    }
}
