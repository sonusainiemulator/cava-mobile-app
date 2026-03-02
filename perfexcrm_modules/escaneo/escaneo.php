<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Escaneo
Description: Módulo para registrar escaneos de códigos de barras con geolocalización, maestro de bebidas y reportes.
Version: 1.1.4
Author: Ereise
Requires at least: 3.3.*
*/

define('ESCANEO_MODULE_NAME', 'escaneo');

register_language_files(ESCANEO_MODULE_NAME, [ESCANEO_MODULE_NAME]);

register_activation_hook(ESCANEO_MODULE_NAME, 'escaneo_module_activation_hook');
register_uninstall_hook(ESCANEO_MODULE_NAME, 'escaneo_module_uninstall_hook');

function escaneo_module_activation_hook()
{
    require_once(__DIR__ . '/install.php');
}

function escaneo_module_uninstall_hook()
{
    require_once(__DIR__ . '/uninstall.php');
}

hooks()->add_action('admin_init', 'escaneo_module_register_permissions');

function escaneo_module_register_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];
    register_staff_capabilities('escaneo', $capabilities, _l('escaneo_menu'));
}

hooks()->add_action('admin_init', 'escaneo_module_init_menu_items');

function escaneo_module_init_menu_items()
{
    if (!is_admin()) {
        return;
    }

    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('escaneo-menu', [
        'name'     => _l('escaneo_menu'),
        'href'     => admin_url('escaneo/escaneos'),
        'position' => 25,
        'icon'     => 'fa fa-barcode',
    ]);

    $CI->app_menu->add_sidebar_children_item('escaneo-menu', [
        'slug'     => 'escaneo-escaneos',
        'name'     => _l('escaneo_scans_menu'),
        'href'     => admin_url('escaneo/escaneos'),
        'position' => 1,
    ]);

    $CI->app_menu->add_sidebar_children_item('escaneo-menu', [
        'slug'     => 'escaneo-mapa',
        'name'     => _l('escaneo_mapa_menu'),
        'href'     => admin_url('escaneo/mapa'),
        'position' => 2,
    ]);

    $CI->app_menu->add_sidebar_children_item('escaneo-menu', [
        'slug'     => 'escaneo-bebidas',
        'name'     => _l('escaneo_bebidas_menu'),
        'href'     => admin_url('escaneo/bebidas'),
        'position' => 3,
    ]);

    $CI->app_menu->add_sidebar_children_item('escaneo-menu', [
        'slug'     => 'escaneo-reportes',
        'name'     => _l('escaneo_reportes_menu'),
        'href'     => admin_url('escaneo/reportes'),
        'position' => 4,
    ]);
}
