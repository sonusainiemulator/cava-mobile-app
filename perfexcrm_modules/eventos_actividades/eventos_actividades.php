<?php
// Path: modules/eventos_actividades/eventos_actividades.php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Eventos y actividades
Description: Gestión de eventos, actividades y puntos por usuario.
Version: 1.0.2
Requires at least: 3.3.*
Author: erei..se
*/

hooks()->add_action('admin_init', 'eventos_actividades_init_menu_items');
hooks()->add_action('admin_init', 'eventos_actividades_init_permissions');
register_activation_hook('eventos_actividades', 'eventos_actividades_install');
register_language_files('eventos_actividades', ['eventos_actividades']);

/**
 * Crear menú en el panel de administración
 */
function eventos_actividades_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('eventos_actividades', '', 'view') || has_permission('eventos_actividades', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('eventos_actividades', [
            'name'     => _l('eventos_actividades_menu'),
            'icon'     => 'fa fa-calendar',
            'position' => 15,
            'href'     => admin_url('eventos_actividades'),
        ]);
    }
}

/**
 * Crear permisos del módulo
 */
function eventos_actividades_init_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'      => _l('permission_view'),
        'view_own'  => _l('permission_view_own'),
        'create'    => _l('permission_create'),
        'edit'      => _l('permission_edit'),
        'delete'    => _l('permission_delete'),
    ];

    register_staff_capabilities('eventos_actividades', $capabilities, _l('eventos_actividades_menu'));
}

/**
 * Ejecutado al activar el módulo
 */
function eventos_actividades_install()
{
    require_once(__DIR__ . '/install.php');
    eventos_actividades_install_module();
}
