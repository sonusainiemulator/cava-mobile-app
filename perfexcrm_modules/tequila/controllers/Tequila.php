<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tequila extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tequila/Tequila_model');
    }

    public function index()
    {
        if ($this->input->post()) {
            $this->create();
            return;
        }

        $data['title']  = _l('Tequila');
        $data['images'] = $this->Tequila_model->get_by_type('image');
        $data['texts']  = $this->Tequila_model->get_by_type('text');
        $data['videos'] = $this->Tequila_model->get_by_type('video');

        $this->load->view('tequila/admin/manage', $data);
    }

    public function create()
    {
        $type    = $this->input->post('type');
        $title   = $this->input->post('title');
        $content = $this->input->post('content');

        $uploadedPath = null;

        if ($type === 'image' || $type === 'video') {
            if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
                $uploadPath = FCPATH . 'modules/tequila/uploads/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $_FILES['file']['name']);
                $fullPath = $uploadPath . $filename;

                if (move_uploaded_file($_FILES['file']['tmp_name'], $fullPath)) {
                    $uploadedPath = 'modules/tequila/uploads/' . $filename;
                } else {
                    set_alert('danger', 'Error al subir el archivo');
                    redirect(admin_url('tequila'));
                }
            } else {
                set_alert('danger', 'Debes seleccionar un archivo');
                redirect(admin_url('tequila'));
            }
        }

        $this->Tequila_model->create([
            'type'       => $type,
            'title'      => $title,
            'content'    => $content,
            'file_path'  => $uploadedPath,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        set_alert('success', 'Contenido agregado correctamente');
        redirect(admin_url('tequila'));
    }
}
