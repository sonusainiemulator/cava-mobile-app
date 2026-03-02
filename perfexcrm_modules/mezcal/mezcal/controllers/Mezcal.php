<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mezcal extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mezcal/Mezcal_model', 'mezcal_model');
    }

    public function index()
    {
        if (!has_permission('mezcal', '', 'view')) {
            access_denied('mezcal');
        }

        if ($this->input->post()) {
            $this->add();
            return;
        }

        $data['images'] = $this->mezcal_model->get_by_type('image');
        $data['texts']  = $this->mezcal_model->get_by_type('text');
        $data['videos'] = $this->mezcal_model->get_by_type('video');

        $data['title'] = 'Mezcal';
        $this->load->view('manage', $data);
    }

    private function add()
    {
        if (!has_permission('mezcal', '', 'create')) {
            access_denied('mezcal');
        }

        $data = $this->input->post();
        $type = $data['type'];

        $uploaded_file = null;

        if ($type == 'image' || $type == 'video') {
            if (!empty($_FILES['file']['name'])) {
                $this->load->library('upload');

                $uploads_path = FCPATH . 'modules/mezcal/uploads/';

                if (!is_dir($uploads_path)) {
                    mkdir($uploads_path, 0755, true);
                }

                $config                  = [];
                $config['upload_path']   = $uploads_path;
                $config['allowed_types'] = $type == 'image' ? 'jpg|jpeg|png|gif' : 'mp4|mov|avi|mkv';
                $config['max_size']      = 102400;

                $this->upload->initialize($config);

                if ($this->upload->do_upload('file')) {
                    $uploaded      = $this->upload->data();
                    $uploaded_file = 'modules/mezcal/uploads/' . $uploaded['file_name'];
                } else {
                    set_alert('danger', $this->upload->display_errors());
                    redirect(admin_url('mezcal/mezcal'));
                }
            }
        }

        $insert = [
            'title'       => $data['title'],
            'type'        => $type,
            'description' => isset($data['description']) ? $data['description'] : null,
            'file'        => $uploaded_file,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $this->mezcal_model->add($insert);
        set_alert('success', _l('mezcal_added_success'));
        redirect(admin_url('mezcal/mezcal'));
    }

    public function delete($id)
    {
        if (!has_permission('mezcal', '', 'delete')) {
            access_denied('mezcal');
        }

        if ($this->mezcal_model->delete($id)) {
            set_alert('success', _l('mezcal_deleted_success'));
        } else {
            set_alert('danger', _l('mezcal_delete_error'));
        }

        redirect(admin_url('mezcal/mezcal'));
    }
}
