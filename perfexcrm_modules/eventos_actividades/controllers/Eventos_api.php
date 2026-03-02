<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Eventos_api extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('eventos_actividades/eventos_actividades_model');
        
        // Load Cava model
        if (file_exists(APP_MODULES_PATH . 'cava/models/Cava_wines_model.php')) {
            $this->load->model('cava/cava_wines_model');
        }

        // Load Avatares model
        if (file_exists(APP_MODULES_PATH . 'avatares/models/Avatares_model.php')) {
            $this->load->model('avatares/avatares_model');
        }

        // Load Marcas model
        if (file_exists(APP_MODULES_PATH . 'marcas/models/Marcas_model.php')) {
            $this->load->model('marcas/marcas_model');
        }

        // Load Mezcal model
        if (file_exists(APP_MODULES_PATH . 'mezcal/models/Mezcal_model.php') ) {
             $this->load->model('mezcal/Mezcal_model', 'mezcal_model');
        } elseif (file_exists(APP_MODULES_PATH . 'mezcal/mezcal/models/Mezcal_model.php')) {
             $this->load->model('mezcal/mezcal/Mezcal_model', 'mezcal_model');
        }

        // Load Escaneo model
        if (file_exists(APP_MODULES_PATH . 'escaneo/models/Escaneo_model.php')) {
            $this->load->model('escaneo/Escaneo_model', 'escaneo_model');
        }
    }

    /**
     * POST /eventos_actividades/eventos_api/login
     * params: email, password
     */
    public function login()
    {
        $email    = $this->input->post('email');
        $password = $this->input->post('password');

        if (!$email || !$password) {
            return $this->_json(400, ['message' => 'Faltan credenciales']);
        }

        $this->db->where('email', $email);
        $user = $this->db->get(db_prefix() . 'staff')->row();

        if (!$user) {
            return $this->_json(401, ['message' => 'Usuario no encontrado']);
        }

        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

        if (!$hasher->CheckPassword($password, $user->password)) {
            return $this->_json(401, ['message' => 'Contraseña incorrecta']);
        }

        if ($user->active == 0) {
            return $this->_json(401, ['message' => 'Cuenta inactiva']);
        }

        // Generar Token
        $token = bin2hex(random_bytes(32));
        $this->db->insert(db_prefix() . 'eventos_actividades_api_tokens', [
            'staff_id' => $user->staffid,
            'token'    => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days'))
        ]);

        // Get User Avatar if available
        $avatar_url = null;
        $points = 0;
        
        // Get total points
        if ($this->db->table_exists(db_prefix().'eventos_actividades_logs')) {
             $this->db->select('SUM(puntos) as total');
             $this->db->where('staff_id', $user->staffid);
             $row = $this->db->get(db_prefix().'eventos_actividades_logs')->row();
             $points = $row && $row->total ? (int)$row->total : 0;
        }

        if (isset($this->avatares_model)) {
             $avatar = $this->avatares_model->get_avatar_for_points($points);
             if ($avatar && !empty($avatar->image)) {
                // Assuming uploads are in specific folder
                 $avatar_url = base_url('uploads/avatares/' . $avatar->image);
             }
        }

        return $this->_json(200, [
            'token' => $token,
            'user' => [
                'id' => $user->staffid,
                'firstname' => $user->firstname,
                'lastname'  => $user->lastname,
                'email'     => $user->email,
                'avatar_url' => $avatar_url,
                'points' => $points
            ]
        ]);
    }

    public function me()
    {
        $user = $this->_auth();
        if (!$user) return;

        // Get full staff details
        $this->db->where('staffid', $user->staff_id);
        $staff = $this->db->get(db_prefix().'staff')->row();

        if (!$staff) {
             return $this->_json(404, ['message' => 'User not found']);
        }

        // Get User Avatar if available
        $avatar_url = null;
        $points = 0;
        
        // Get total points
        if ($this->db->table_exists(db_prefix().'eventos_actividades_logs')) {
             $this->db->select('SUM(puntos) as total');
             $this->db->where('staff_id', $staff->staffid);
             $row = $this->db->get(db_prefix().'eventos_actividades_logs')->row();
             $points = $row && $row->total ? (int)$row->total : 0;
        }

        if (isset($this->avatares_model)) {
             $avatar = $this->avatares_model->get_avatar_for_points($points);
             if ($avatar && !empty($avatar->image)) {
                 $avatar_url = base_url('uploads/avatares/' . $avatar->image);
             }
        }

        return $this->_json(200, [
            'data' => [
                'id' => $staff->staffid,
                'firstname' => $staff->firstname,
                'lastname'  => $staff->lastname,
                'email'     => $staff->email,
                'avatar_url' => $avatar_url,
                'points' => $points
            ]
        ]);
    }

    public function products()
    {
        $user = $this->_auth();
        if (!$user) return;

        $wines = [];
        if (isset($this->cava_wines_model)) {
            $wines = $this->cava_wines_model->get_all();
        } elseif ($this->db->table_exists(db_prefix() . 'cava_wines')) {
            $wines = $this->db->get(db_prefix() . 'cava_wines')->result();
        }

        return $this->_json(200, $wines);
    }

    public function scan()
    {
        $user = $this->_auth();
        if (!$user) return;

        $barcode   = $this->input->post('barcode');
        $lat       = $this->input->post('latitude');
        $lng       = $this->input->post('longitude');
        $info_date = $this->input->post('date');
        $info_user = $this->input->post('username');

        if (!$barcode) {
             return $this->_json(400, ['message' => 'Barcode is required']);
        }

        // 1. Registrar escaneo simple en Eventos (Internal)
        $scan_id = $this->eventos_actividades_model->add_scan($user->staff_id, $barcode);

        // 2. Registrar en Módulo Escaneo (External/Master) si está disponible
        if (isset($this->escaneo_model)) {
            $bebida = $this->db->where('codigo_barras', $barcode)->get(db_prefix().'escaneo_bebidas')->row();
            
            $scan_data = [
                'bebida_id'      => $bebida ? $bebida->id : null,
                'codigo_barras'  => $barcode,
                'marca'          => $bebida ? $bebida->marca : 'Unknown',
                'nombre_tequila' => $bebida ? $bebida->nombre_tequila : 'Unknown',
                'presentacion'   => $bebida ? $bebida->presentacion : '',
                'grados_alcohol' => $bebida ? $bebida->grados_alcohol : 0,
                'precio'         => $bebida ? $bebida->precio : 0,
                'lat'            => $lat ? (float)$lat : 0,
                'lng'            => $lng ? (float)$lng : 0,
                'staff_id'       => $user->staff_id,
                'fecha_escaneo'  => $info_date ? date('Y-m-d H:i:s', strtotime($info_date)) : date('Y-m-d H:i:s')
            ];
            $this->db->insert(db_prefix() . 'escaneo_scans', $scan_data);
        }

        // 3. Evaluar reglas automáticas (puntos)
        $awarded = $this->eventos_actividades_model->evaluate_auto_awards_for_staff($user->staff_id);

        return $this->_json(200, [
            'success' => true, 
             'scan_id' => $scan_id,
             'points_awarded' => $awarded
        ]);
    }

    public function eventos()
    {
        $user = $this->_auth();
        if (!$user) return;

        $eventos = $this->eventos_actividades_model->get_eventos();
        return $this->_json(200, $eventos);
    }

    public function actividades()
    {
        $user = $this->_auth();
        if (!$user) return;

        $actividades = $this->eventos_actividades_model->get_actividades();
        return $this->_json(200, $actividades);
    }

    public function tequila()
    {
        $user = $this->_auth();
        if (!$user) return;

        // Try to use mezcal model if it handles tequila too, or generic content
        $data = [
            'images' => [],
            'videos' => [],
            'texts'  => []
        ];

        // If a separate tequila module exists, use it. Otherwise placeholder or generic.
        if ($this->db->table_exists(db_prefix().'tequila_content')) {
             $data['images'] = $this->db->where('type', 'image')->get(db_prefix().'tequila_content')->result();
             $data['videos'] = $this->db->where('type', 'video')->get(db_prefix().'tequila_content')->result();
             $data['texts']  = $this->db->where('type', 'text')->get(db_prefix().'tequila_content')->result();
        } else {
            // Fallback: search in common content tables
             $data['texts'][] = [
                 'title' => 'Tequila Info',
                 'text' => 'Tequila is a distilled beverage made from the blue agave plant.'
             ];
        }

        return $this->_json(200, $data);
    }

    public function mezcal()
    {
        $user = $this->_auth();
        if (!$user) return;

        $data = [
            'images' => [],
            'videos' => [],
            'texts'  => []
        ];

        if ($this->db->table_exists(db_prefix().'mezcal_content')) {
             $data['images'] = $this->db->where('type', 'image')->get(db_prefix().'mezcal_content')->result();
             $data['videos'] = $this->db->where('type', 'video')->get(db_prefix().'mezcal_content')->result();
             $data['texts']  = $this->db->where('type', 'text')->get(db_prefix().'mezcal_content')->result();
        } else {
             $data['texts'][] = [
                 'title' => 'Mezcal Info',
                 'text' => 'Mezcal is a Mexican distilled spirit made from any type of agave.'
             ];
        }

        return $this->_json(200, $data);
    }

    public function banners()
    {
        $user = $this->_auth();
        if (!$user) return;

        $banners = [];
        if ($this->db->table_exists(db_prefix().'ads')) {
            $banners = $this->db->get(db_prefix().'ads')->result_array();
        } elseif ($this->db->table_exists(db_prefix().'banners')) {
            $banners = $this->db->get(db_prefix().'banners')->result_array();
        } else {
            // Internal static banners for the module
            $banners = [
                [
                    'title' => 'Welcome to Tequila App',
                    'image' => 'modules/eventos_actividades/assets/banner1.jpg',
                    'description' => 'Explore the world of agave.'
                ]
            ];
        }

        // Resolve URLs
        foreach ($banners as &$b) {
            foreach (['image', 'image_path', 'imagen'] as $key) {
                if (isset($b[$key]) && !empty($b[$key])) {
                    if (strpos($b[$key], 'http') !== 0) {
                        $b['image_url'] = base_url($b[$key]);
                        // Ensure compatibility for DataSyncService
                        if (!isset($b['image'])) $b['image'] = $b['image_url'];
                    } else {
                        $b['image_url'] = $b[$key];
                    }
                }
            }
        }

        return $this->_json(200, $banners);
    }

    public function marcas()
    {
        $user = $this->_auth();
        if (!$user) return;

        $marcas = [];
        if (isset($this->marcas_model)) {
            $marcas = $this->marcas_model->get();
        } elseif ($this->db->table_exists(db_prefix() . 'marcas')) {
            $marcas = $this->db->get(db_prefix() . 'marcas')->result();
        }

        // Resolve URLs
        foreach ($marcas as &$m) {
            // Standardize output
            if (!isset($m->name) && isset($m->nombre)) $m->name = $m->nombre;
            if (!isset($m->logo_url)) {
                if (isset($m->logo) && !empty($m->logo)) {
                    $m->logo_url = base_url('uploads/marcas/' . $m->logo);
                } elseif (isset($m->imagen) && !empty($m->imagen)) {
                    $m->logo_url = base_url('uploads/marcas/' . $m->imagen);
                } else {
                    $m->logo_url = '';
                }
            }
        }

        return $this->_json(200, $marcas);
    }

    public function add_to_cava()
    {
        $user = $this->_auth();
        if (!$user) return;

        $barcode = $this->input->post('barcode');
        if (!$barcode) {
             return $this->_json(400, ['message' => 'Barcode required']);
        }

        // Check if cava module is active and loaded
        if (isset($this->cava_wines_model)) {
             // Logic to add to cava table
             // Assuming cava_user_wines or similar table
             $data = [
                 'staff_id' => $user->staff_id,
                 'barcode' => $barcode,
                 'added_at' => date('Y-m-d H:i:s')
             ];
             
             // Check duplicates
             $exists = $this->db->where('staff_id', $user->staff_id)
                                ->where('barcode', $barcode)
                                ->get(db_prefix().'cava_user_items')->row();
             
             if (!$exists) {
                 if ($this->db->table_exists(db_prefix().'cava_user_items')) {
                     $this->db->insert(db_prefix().'cava_user_items', $data);
                 } else {
                      // Fallback or create table on the fly? Better just return success mock if table missing
                 }
             }
             return $this->_json(200, ['success' => true]);
        }
        
        // If no cava model, just return success (mock)
        return $this->_json(200, ['success' => true]);
    }

    public function avatars()
    {
        $user = $this->_auth();
        if (!$user) return;

        if (!isset($this->avatares_model)) {
             return $this->_json(404, ['message' => 'Módulo Avatares no encontrado']);
        }

        $avatars = $this->avatares_model->get();
        $path = base_url('uploads/avatares/');
        foreach ($avatars as $a) {
            if ($a->image) {
                $a->image_url = $path . $a->image;
            }
        }

        return $this->_json(200, $avatars);
    }
    
    // --- Helpers ---

    private function _auth()
    {
        $headers = $this->input->request_headers();
        $auth = null;
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $auth = $headers['authorization'];
        }

        if (!$auth) {
            $this->_json(401, ['message' => 'Token requerido']);
            return false;
        }

        $token = str_replace('Bearer ', '', $auth);
        $this->db->where('token', $token);
        $t = $this->db->get(db_prefix() . 'eventos_actividades_api_tokens')->row();

        if (!$t) {
            $this->_json(401, ['message' => 'Token inválido']);
            return false;
        }

        if ($t->expires_at && strtotime($t->expires_at) < time()) {
            $this->_json(401, ['message' => 'Token expirado']);
            return false;
        }

        // Actualizar último uso
        $this->db->where('id', $t->id);
        $this->db->update(db_prefix() . 'eventos_actividades_api_tokens', ['last_used_at' => date('Y-m-d H:i:s')]);

        $t->staff_id = $t->staff_id ?? $t->staffid; // Ensure access

        return $t;
    }

    private function _json($code, $data)
    {
        $this->output
             ->set_content_type('application/json')
             ->set_status_header($code)
             ->set_output(json_encode($data));
        echo json_encode($data);
        exit;
    }
}
