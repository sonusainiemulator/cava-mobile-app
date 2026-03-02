<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Eventos_actividades extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('eventos_actividades/eventos_actividades_model');
    }

    /**
     * Main module view
     */
    public function index()
    {
        if (!has_permission('eventos_actividades', '', 'view') && !has_permission('eventos_actividades', '', 'view_own')) {
            access_denied('eventos_actividades');
        }

        $data['title']        = _l('eventos_actividades_menu');
        $data['actividades']  = $this->eventos_actividades_model->get_actividades();
        $data['logs']         = $this->eventos_actividades_model->get_logs();
        $data['totales']      = $this->eventos_actividades_model->get_totales_por_usuario();
        $data['staff']        = $this->staff_model->get('', ['active' => 1]);
        $data['eventos']      = $this->eventos_actividades_model->get_eventos();

        $this->load->view('eventos_actividades/manage', $data);
    }

    /**
     * Create or update activity
     */
    public function activity()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            if (empty($data['id'])) {
                if (!has_permission('eventos_actividades', '', 'create')) {
                    access_denied('eventos_actividades');
                }
                $success = $this->eventos_actividades_model->add_actividad($data);
                $message = $success ? _l('added_successfully', _l('eventos_actividades_actividad')) : _l('problem_adding', _l('eventos_actividades_actividad'));
            } else {
                if (!has_permission('eventos_actividades', '', 'edit')) {
                    access_denied('eventos_actividades');
                }
                $id      = $data['id'];
                unset($data['id']);
                $success = $this->eventos_actividades_model->update_actividad($id, $data);
                $message = $success ? _l('updated_successfully', _l('eventos_actividades_actividad')) : _l('problem_updating', _l('eventos_actividades_actividad'));
            }

            set_alert($success ? 'success' : 'danger', $message);
        }

        redirect(admin_url('eventos_actividades'));
    }

    /**
     * Delete activity
     */
    public function delete_activity($id)
    {
        if (!has_permission('eventos_actividades', '', 'delete')) {
            access_denied('eventos_actividades');
        }

        $success = $this->eventos_actividades_model->delete_actividad($id);
        $message = $success ? _l('deleted', _l('eventos_actividades_actividad')) : _l('problem_deleting', _l('eventos_actividades_actividad'));

        set_alert($success ? 'success' : 'danger', $message);
        redirect(admin_url('eventos_actividades'));
    }

    /**
     * View event (New or Edit)
     */
    public function event($id = '')
    {
        if ($this->input->post()) {
            // Handle save
            $this->save_event();
            return;
        }

        if ($id) {
            if (!has_permission('eventos_actividades', '', 'edit')) {
                access_denied('eventos_actividades');
            }
            $evento = $this->eventos_actividades_model->get_eventos($id);
            if (!$evento) {
                show_404();
            }
            $data['evento'] = $evento;
            $data['title']  = 'Edit Event: ' . $evento->nombre;
        } else {
            if (!has_permission('eventos_actividades', '', 'create')) {
                access_denied('eventos_actividades');
            }
            $data['title'] = 'Register New Event';
        }

        $this->load->view('eventos_actividades/evento_detail', $data);
    }

    /**
     * Save event logic
     */
    private function save_event()
    {
        $data = $this->input->post();

        if (empty($data['id'])) {
            if (!has_permission('eventos_actividades', '', 'create')) {
                access_denied('eventos_actividades');
            }
            $success = $this->eventos_actividades_model->add_evento($data);
            $message = $success ? _l('added_successfully', _l('eventos_actividades_evento')) : _l('problem_adding', _l('eventos_actividades_evento'));
        } else {
            if (!has_permission('eventos_actividades', '', 'edit')) {
                access_denied('eventos_actividades');
            }
            $id      = $data['id'];
            unset($data['id']);
            $success = $this->eventos_actividades_model->update_evento($id, $data);
            $message = $success ? _l('updated_successfully', _l('eventos_actividades_evento')) : _l('problem_updating', _l('eventos_actividades_evento'));
        }

        set_alert($success ? 'success' : 'danger', $message);
        redirect(admin_url('eventos_actividades'));
    }

    /**
     * Delete event
     */
    public function delete_event($id)
    {
        if (!has_permission('eventos_actividades', '', 'delete')) {
            access_denied('eventos_actividades');
        }

        $success = $this->eventos_actividades_model->delete_evento($id);
        $message = $success ? _l('deleted', _l('eventos_actividades_evento')) : _l('problem_deleting', _l('eventos_actividades_evento'));

        set_alert($success ? 'success' : 'danger', $message);
        redirect(admin_url('eventos_actividades'));
    }

    /**
     * Manual points award
     */
    public function manual_points()
    {
        if (!has_permission('eventos_actividades', '', 'create')) {
            access_denied('eventos_actividades');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $success = $this->eventos_actividades_model->add_log($data, 'manual');
            $message = $success ? _l('added_successfully', _l('eventos_actividades_log')) : _l('problem_adding', _l('eventos_actividades_log'));
            set_alert($success ? 'success' : 'danger', $message);
        }

        redirect(admin_url('eventos_actividades'));
    }


    /**
     * Endpoint for scanning
     */
    public function scan()
    {
        if (!has_permission('eventos_actividades', '', 'create')) {
            access_denied('eventos_actividades');
        }

        if ($this->input->post()) {
            $staff_id = (int) $this->input->post('staff_id');
            $barcode  = $this->input->post('barcode');

            $this->eventos_actividades_model->add_scan($staff_id, $barcode);
            $awarded = $this->eventos_actividades_model->evaluate_auto_awards_for_staff($staff_id);

            if ($awarded > 0) {
                set_alert('success', _l('eventos_actividades_auto_award_applied') . ' (' . $awarded . ')');
            } else {
                set_alert('success', _l('eventos_actividades_scan_recorded'));
            }
        }

        redirect(admin_url('eventos_actividades'));
    }

    /**
     * Recalculate auto awards
     */
    public function apply_auto()
    {
        if (!has_permission('eventos_actividades', '', 'create')) {
            access_denied('eventos_actividades');
        }

        $awarded = $this->eventos_actividades_model->evaluate_auto_awards_all();
        set_alert('success', _l('eventos_actividades_auto_award_applied') . ' (' . (int)$awarded . ')');

        redirect(admin_url('eventos_actividades'));
    }

}
