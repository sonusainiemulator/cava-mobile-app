<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Avatares
Description: Gestión de avatares por puntos para usuarios de Perfex CRM.
Version: 1.0.3
Requires at least: 3.3.*
Author: erei..se
*/

register_language_files('avatares', ['avatares']);
register_activation_hook('avatares', 'avatares_module_activation');

hooks()->add_action('admin_init', 'avatares_module_init_menu_items');
hooks()->add_action('after_dashboard_top_container', 'avatares_show_dashboard_avatar');

/**
 * Directorio público recomendado para uploads (NO dentro de /modules/).
 * Queda en: /uploads/avatares/
 */
function avatares_upload_dir()
{
    return rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'avatares' . DIRECTORY_SEPARATOR;
}

/**
 * URL pública para un archivo de avatar.
 */
function avatares_upload_url($filename)
{
    return site_url('uploads/avatares/' . $filename);
}

function avatares_module_activation()
{
    $CI = &get_instance();

    if (!$CI->db->table_exists(db_prefix() . 'avatares')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "avatares` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `points_required` INT(11) NOT NULL DEFAULT 0,
            `image` VARCHAR(191) DEFAULT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    // Crear carpeta pública de uploads si no existe
    $upload_dir = avatares_upload_dir();
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0755, true);
    }

    // Evitar listado de directorio
    if (is_dir($upload_dir) && !file_exists($upload_dir . 'index.html')) {
        @file_put_contents($upload_dir . 'index.html', '<!-- avatares uploads -->');
    }
}

function avatares_module_init_menu_items()
{
    $CI = &get_instance();

    if (!is_staff_logged_in()) {
        return;
    }

    if (!is_admin()) {
        return;
    }

    $CI->app_menu->add_sidebar_menu_item('avatares', [
        'name'     => _l('avatares_menu'),
        'href'     => admin_url('avatares'),
        'icon'     => 'fa fa-user-circle',
        'position' => 18,
    ]);
}

function avatares_show_dashboard_avatar()
{
    if (!is_staff_logged_in()) {
        return;
    }

    $CI = &get_instance();
    $CI->load->model('avatares/avatares_model');

    $staff_id   = get_staff_user_id();
    $option_key = 'avatar_points_staff_' . $staff_id;
    $points     = (int) get_option($option_key);

    $avatar = $CI->avatares_model->get_avatar_for_points($points);

    if (!$avatar) {
        return;
    }

    $image_url = '';
    if (!empty($avatar->image)) {
        $image_url = avatares_upload_url($avatar->image);
    }

    echo '<div class="row">
            <div class="col-md-3">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h4>' . _l('avatares_your_avatar') . '</h4>';

    if ($image_url !== '') {
        echo '<img src="' . $image_url . '" class="img-responsive" style="max-width:150px;margin:0 auto 10px;">';
    } else {
        echo '<div class="text-muted" style="margin-bottom:10px;">' . _l('avatares_no_image') . '</div>';
    }

    echo '              <p><strong>' . htmlspecialchars($avatar->name) . '</strong></p>
                        <p class="text-muted">' . _l('avatares_points_required') . ': ' . (int) $avatar->points_required . '</p>
                    </div>
                </div>
            </div>
        </div>';
}

function avatares_get_avatar_by_points($points)
{
    $CI = &get_instance();
    $CI->load->model('avatares/avatares_model');

    return $CI->avatares_model->get_avatar_for_points((int) $points);
}
