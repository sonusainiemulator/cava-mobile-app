<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Marcas extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('marcas/marcas_model');
    }

    public function index()
    {
        if (!has_permission('marcas', '', 'view')) {
            access_denied('marcas');
        }

        $data['title']        = _l('marcas');
        $data['marcas']       = $this->marcas_model->get();
        $data['fields']       = $this->marcas_model->get_fields();
        $data['image_fields'] = $this->marcas_model->get_image_fields();

        // Sin modo edición por defecto
        $data['edit_mode'] = false;
        $data['marca']     = [];

        $this->load->view('manage', $data);
    }

    public function edit($id)
    {
        if (!has_permission('marcas', '', 'edit')) {
            access_denied('marcas');
        }

        $marca = $this->marcas_model->get($id);
        if (!$marca) {
            set_alert('danger', _l('marca_not_found'));
            redirect(admin_url('marcas'));
        }

        $data['title']        = _l('edit_marca');
        $data['marcas']       = $this->marcas_model->get();
        $data['fields']       = $this->marcas_model->get_fields();
        $data['image_fields'] = $this->marcas_model->get_image_fields();

        $data['edit_mode'] = true;
        $data['marca']     = $marca;

        $this->load->view('manage', $data);
    }

    protected function handle_image_uploads($image_fields)
    {
        $uploaded = [];

        if (empty($image_fields)) {
            return $uploaded;
        }

        $path = FCPATH . 'uploads/marcas/';
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }

        $this->load->library('upload');

        foreach ($image_fields as $field) {
            if (isset($_FILES[$field]) && !empty($_FILES[$field]['name'])) {
                $config = [];
                $config['upload_path']   = $path;
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size']      = 2048;
                $config['file_name']     = $field . '_' . time() . '_' . uniqid();

                $this->upload->initialize($config);

                if ($this->upload->do_upload($field)) {
                    $data = $this->upload->data();
                    $uploaded[$field] = $data['file_name'];
                }
            }
        }

        return $uploaded;
    }

    public function create()
    {
        if (!has_permission('marcas', '', 'create')) {
            access_denied('marcas');
        }

        $post = $this->input->post(null, false);

        if ($post) {
            $image_fields = $this->marcas_model->get_image_fields();
            $uploaded     = $this->handle_image_uploads($image_fields);

            foreach ($uploaded as $field => $filename) {
                $post[$field] = $filename;
            }

            $id = $this->marcas_model->add($post);
            if ($id) {
                set_alert('success', _l('added_successfully', _l('marcas')));
            } else {
                set_alert('danger', _l('problem_adding', _l('marcas')));
            }
        }

        redirect(admin_url('marcas'));
    }

    public function update($id)
    {
        if (!has_permission('marcas', '', 'edit')) {
            access_denied('marcas');
        }

        $post = $this->input->post(null, false);

        if ($post) {
            $image_fields = $this->marcas_model->get_image_fields();
            $uploaded     = $this->handle_image_uploads($image_fields);

            foreach ($uploaded as $field => $filename) {
                $post[$field] = $filename;
            }

            $success = $this->marcas_model->update($post, $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('marcas')));
            } else {
                set_alert('danger', _l('problem_updating', _l('marcas')));
            }
        }

        redirect(admin_url('marcas'));
    }

    public function delete($id)
    {
        if (!has_permission('marcas', '', 'delete')) {
            access_denied('marcas');
        }

        $success = $this->marcas_model->delete($id);
        if ($success) {
            set_alert('success', _l('deleted', _l('marcas')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('marcas')));
        }
        redirect(admin_url('marcas'));
    }
}
