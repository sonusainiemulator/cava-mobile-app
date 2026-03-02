<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Avatares extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('avatares/avatares_model');
        $this->load->helper('url');
    }

    public function index()
    {
        if (!is_admin()) {
            access_denied('Avatares');
        }

        if ($this->input->post()) {
            $this->handle_form(null);
            return;
        }

        $data['title']    = _l('avatares_menu');
        $data['avatares'] = $this->avatares_model->get();

        $this->load->view('avatares/manage', $data);
    }

    public function edit($id)
    {
        if (!is_admin()) {
            access_denied('Avatares');
        }

        if ($this->input->post()) {
            $this->handle_form($id);
            return;
        }

        $data['title']   = _l('avatares_edit');
        $data['avatar']  = $this->avatares_model->get($id);

        if (!$data['avatar']) {
            blank_page(_l('avatares_not_found'));
        }

        $data['avatares'] = $this->avatares_model->get();
        $this->load->view('avatares/manage', $data);
    }

    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('Avatares');
        }

        if ($this->avatares_model->delete($id)) {
            set_alert('success', _l('deleted', _l('avatares_singular')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('avatares_singular')));
        }

        redirect(admin_url('avatares'));
    }

    private function handle_form($id = null)
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', _l('avatares_name'), 'required|trim');
        $this->form_validation->set_rules('points_required', _l('avatares_points_required'), 'required|integer');

        if ($this->form_validation->run() == false) {
            $data['title']    = _l('avatares_menu');
            $data['avatares'] = $this->avatares_model->get();

            if ($id !== null) {
                $data['avatar'] = $this->avatares_model->get($id);
            }

            $this->load->view('avatares/manage', $data);
            return;
        }

        $data = [
            'name'            => $this->input->post('name', true),
            'points_required' => $this->input->post('points_required', true),
            'active'          => $this->input->post('active'),
        ];

        $uploaded_image = $this->upload_image();
        if ($uploaded_image) {
            $data['image'] = $uploaded_image;
        }

        if ($id === null) {
            $insert_id = $this->avatares_model->create($data);
            if ($insert_id) {
                set_alert('success', _l('added_successfully', _l('avatares_singular')));
            }
        } else {
            $success = $this->avatares_model->update($id, $data);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('avatares_singular')));
            }
        }

        redirect(admin_url('avatares'));
    }

    private function upload_image()
    {
        if (!isset($_FILES['image']) || $_FILES['image']['size'] == 0) {
            return null;
        }

        // Guardar en /uploads/avatares/ para que sea público y accesible
        $path = avatares_upload_dir();

        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }

        $original_name = $_FILES['image']['name'];
        $extension     = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowed)) {
            set_alert('danger', _l('avatares_invalid_image_type'));
            return null;
        }

        $filename = uniqid('avatar_', true) . '.' . $extension;
        $target   = $path . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            return $filename;
        }

        set_alert('danger', _l('avatares_image_upload_failed'));
        return null;
    }
}
