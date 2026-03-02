<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cava extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('cava/Cava_wines_model', 'wines_model');
        $this->load->model('cava/Cava_user_wines_model', 'user_wines_model');
    }

    public function index()
    {
        if (!has_permission('cava', '', 'view') && !is_admin()) {
            access_denied('cava');
        }

        $user_id = get_staff_user_id();

        $data['title'] = 'Mi Cava';
        $data['wines'] = $this->wines_model->get_all();
        $data['selected_ids'] = $this->user_wines_model->get_user_wine_ids($user_id);

        $this->load->view('my_cava', $data);
    }

    public function add_to_my($wine_id)
    {
        if ((!has_permission('cava', '', 'edit') && !has_permission('cava', '', 'create')) && !is_admin()) {
            access_denied('cava');
        }

        $this->user_wines_model->add_for_user(get_staff_user_id(), (int)$wine_id);
        redirect(admin_url('cava'));
    }

    public function remove_from_my($wine_id)
    {
        if ((!has_permission('cava', '', 'edit') && !has_permission('cava', '', 'delete')) && !is_admin()) {
            access_denied('cava');
        }

        $this->user_wines_model->remove_for_user(get_staff_user_id(), (int)$wine_id);
        redirect(admin_url('cava'));
    }

    public function master()
    {
        if (!is_admin()) {
            access_denied('cava');
        }

        $data['title'] = 'Catálogo Maestro';
        $data['wines'] = $this->wines_model->get_all();
        $data['selections'] = $this->user_wines_model->get_all_with_users();

        $this->load->view('master', $data);
    }

    public function master_add()
    {
        if (!is_admin()) {
            access_denied('cava');
        }

        if (!$this->input->post()) {
            redirect(admin_url('cava/master'));
        }

        $name = trim($this->input->post('name'));
        if ($name === '') {
            set_alert('danger', _l('cava_error_wine_name_required'));
            redirect(admin_url('cava/master'));
        }

        if (!isset($_FILES['image']) || empty($_FILES['image']['name'])) {
            set_alert('danger', _l('cava_error_png_required'));
            redirect(admin_url('cava/master'));
        }

        $upload = $this->upload_png('image');
        if (!$upload['success']) {
            set_alert('danger', $upload['error']);
            redirect(admin_url('cava/master'));
        }

        $id = $this->wines_model->add([
            'name'       => $name,
            'image'      => $upload['filename'],
            'created_by' => get_staff_user_id(),
        ]);

        if ($id) {
            set_alert('success', _l('cava_success_wine_added'));
        } else {
            set_alert('danger', _l('cava_error_save_failed'));
        }

        redirect(admin_url('cava/master'));
    }

    public function master_delete($id)
    {
        if (!is_admin()) {
            access_denied('cava');
        }

        $wine = $this->wines_model->get($id);
        if (!$wine) {
            redirect(admin_url('cava/master'));
        }

        if ($this->wines_model->delete((int)$id)) {
            $this->db->where('wine_id', (int)$id);
            $this->db->delete(db_prefix() . 'cava_user_wines');

            $path = FCPATH . CAVA_UPLOADS_REL . $wine->image;
            if (is_file($path)) {
                @unlink($path);
            }

            set_alert('success', _l('cava_success_wine_deleted'));
        }

        redirect(admin_url('cava/master'));
    }

    private function upload_png($field)
    {
        $target_dir = FCPATH . CAVA_UPLOADS_REL;
        if (!is_dir($target_dir)) {
            @mkdir($target_dir, 0755, true);
        }

        $config['upload_path']   = $target_dir;
        $config['allowed_types'] = 'png';
        $config['max_size']      = 4096;
        $config['encrypt_name']  = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($field)) {
            return ['success' => false, 'error' => strip_tags($this->upload->display_errors())];
        }

        $data = $this->upload->data();

        if (strtolower($data['file_ext']) !== '.png') {
            @unlink($data['full_path']);
            return ['success' => false, 'error' => _l('cava_error_only_png')];
        }

        return ['success' => true, 'filename' => $data['file_name']];
    }
}
