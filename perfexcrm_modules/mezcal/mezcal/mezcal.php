<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Mezcal
Description: Módulo Mezcal para subir imágenes, textos y videos sobre Mezcal.
Version: 1.0
Author: EREISE
*/

register_language_files('mezcal', ['mezcal']);

register_activation_hook('mezcal', 'mezcal_install');
register_uninstall_hook('mezcal', 'mezcal_uninstall');

hooks()->add_action('admin_init', 'mezcal_init_menu');
hooks()->add_action('admin_init', 'mezcal_permissions');

/**
 * Add Mezcal option to left admin menu
 */
function mezcal_init_menu()
{
    $CI = &get_instance();

    if (has_permission('mezcal', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('mezcal', [
            'name'     => 'Mezcal',
            'href'     => admin_url('mezcal/mezcal'),
            'icon'     => 'fa fa-beer',
            'position' => 20,
        ]);
    }
}

/**
 * Register permissions for Mezcal module
 */
function mezcal_permissions()
{
    $capabilities                 = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . ' (Mezcal)',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('mezcal', $capabilities, _l('mezcal'));
}

/**
 * Install function - create database table
 */
function mezcal_install()
{
    $CI = &get_instance();

    if (!$CI->db->table_exists(db_prefix() . 'mezcal_contents')) {
        $CI->db->query('CREATE TABLE `'.db_prefix().'mezcal_contents` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(191) NOT NULL,
            `type` ENUM("image","text","video") NOT NULL,
            `description` TEXT NULL,
            `file` VARCHAR(255) NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET='.$CI->db->char_set.';');
    }
}

/**
 * Uninstall - keep data by default
 */
function mezcal_uninstall()
{
    // No borrar datos por defecto
}

/**
 * Upgrade handler (placeholder)
 */
function mezcal_upgrade($old_version)
{
    // Lógica de actualización futura
}
