<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Marcas
Description: Módulo de gestión de marcas de tequila usando la tabla existente tblmarcas.
Version: 1.0.6
Author: EREISE
*/

register_language_files('marcas', ['marcas']);

hooks()->add_action('admin_init', 'marcas_init_menu_items');
hooks()->add_action('app_admin_head', 'marcas_add_head_components');

function marcas_add_head_components()
{
    // Aquí podrías agregar CSS/JS adicional si hace falta.
}

function marcas_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('marcas', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('marcas', [
            'name'     => 'Marcas',
            'href'     => admin_url('marcas'),
            'icon'     => 'fa fa-star',
            'position' => 50,
        ]);
    }
}
