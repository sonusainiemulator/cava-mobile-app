<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Cava
Description: Catálogo maestro de vinos (con imagen PNG) y cava personal por usuario (staff). Admin gestiona catálogo; usuarios seleccionan vinos.
Version: 1.0
Author: EREISE
*/

define('CAVA_MODULE_NAME', 'cava');
define('CAVA_UPLOADS_REL', 'modules/cava/uploads/wines/');

register_language_files(CAVA_MODULE_NAME, [CAVA_MODULE_NAME]);

hooks()->add_action('admin_init', 'cava_module_init_menu_items');
hooks()->add_action('admin_init', 'cava_module_permissions');

register_activation_hook(CAVA_MODULE_NAME, 'cava_module_activation_hook');
register_uninstall_hook(CAVA_MODULE_NAME, 'cava_module_uninstall_hook');

function cava_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function cava_module_uninstall_hook()
{
    // No eliminamos tablas/archivos por seguridad.
}

/**
 * Menú izquierdo:
 * - Staff con permiso view: ve Cava -> Mi Cava
 * - Admin SIEMPRE ve Cava y Catálogo Maestro (aunque no tenga permisos asignados en Roles)
 */
function cava_module_init_menu_items()
{
    $CI = &get_instance();

    $canView = has_permission('cava', '', 'view') || is_admin();
    if (!$canView) {
        return;
    }

    $CI->app_menu->add_sidebar_menu_item('cava', [
        'name'     => 'Cava',
        'href'     => admin_url('cava'),
        'icon'     => 'fa fa-glass',
        'position' => 60,
    ]);

    $CI->app_menu->add_sidebar_children_item('cava', [
        'slug'     => 'cava-my',
        'name'     => 'Mi Cava',
        'href'     => admin_url('cava'),
        'position' => 1,
    ]);

    if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('cava', [
            'slug'     => 'cava-master',
            'name'     => 'Catálogo Maestro',
            'href'     => admin_url('cava/master'),
            'position' => 2,
        ]);
    }
}

function cava_module_permissions()
{
    $capabilities = [
        'view'   => _l('permission_view') . ' (Cava)',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('cava', $capabilities, 'Cava');
}
