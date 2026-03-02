<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Tequila
Description: Módulo Tequila para gestionar imágenes, textos y videos.
Version: 1.0
Requires at least: 3.3.*
Author: EREISE
*/

// Hook de activación del módulo
register_activation_hook('tequila', 'tequila_module_activation_hook');

function tequila_module_activation_hook()
{
    $CI =& get_instance();
    require_once(__DIR__ . '/install.php');
}

// Hook de desinstalación del módulo
register_uninstall_hook('tequila', 'tequila_module_uninstall_hook');

function tequila_module_uninstall_hook()
{
    $CI =& get_instance();
    require_once(__DIR__ . '/uninstall.php');
}

// Añadir ítem al menú del admin
hooks()->add_action('admin_init', 'tequila_module_init_menu_items');

function tequila_module_init_menu_items()
{
    $CI =& get_instance();
    if (has_permission('customers', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('tequila', [
            'name'     => 'Tequila',
            'href'     => admin_url('tequila'),
            'position' => 31,
            'icon'     => 'fa fa-circle', // icono seguro
        ]);
    }
}
