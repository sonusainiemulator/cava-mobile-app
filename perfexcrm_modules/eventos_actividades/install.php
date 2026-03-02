<?php
// Path: modules/eventos_actividades/install.php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Crear tablas necesarias para el módulo
 */
function eventos_actividades_install_module()
{
    $CI =& get_instance();

    // Tabla de catálogo de actividades
    if (!$CI->db->table_exists(db_prefix() . 'eventos_actividades_catalogo')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "eventos_actividades_catalogo` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `nombre` VARCHAR(191) NOT NULL,
            `puntos` INT(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    // Tabla de log de puntos por usuario
    if (!$CI->db->table_exists(db_prefix() . 'eventos_actividades_logs')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "eventos_actividades_logs` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `staff_id` INT(11) NOT NULL,
            `actividad_id` INT(11) NOT NULL,
            `puntos` INT(11) NOT NULL DEFAULT 0,
            `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `staff_id` (`staff_id`),
            INDEX `actividad_id` (`actividad_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    // Tabla de eventos con ubicación
    if (!$CI->db->table_exists(db_prefix() . 'eventos_actividades_eventos')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "eventos_actividades_eventos` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `nombre` VARCHAR(191) NOT NULL,
            `fecha` DATE DEFAULT NULL,
            `hora` TIME DEFAULT NULL,
            `pais` VARCHAR(120) DEFAULT NULL,
            `estado` VARCHAR(120) DEFAULT NULL,
            `ciudad` VARCHAR(120) DEFAULT NULL,
            `direccion` VARCHAR(255) DEFAULT NULL,
            `lat` DECIMAL(10,7) DEFAULT NULL,
            `lng` DECIMAL(10,7) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }


    // ---- AUTO AWARD EXTENSION (v1.0.1) ----

    // Agregar columnas para auto-award en catálogo de actividades
    if ($CI->db->table_exists(db_prefix() . 'eventos_actividades_catalogo')) {
        $cols = $CI->db->query("SHOW COLUMNS FROM `" . db_prefix() . "eventos_actividades_catalogo`")->result_array();
        $names = array_map(function($c){ return $c['Field']; }, $cols);

        if (!in_array('auto_award', $names)) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'eventos_actividades_catalogo` ADD `auto_award` TINYINT(1) NOT NULL DEFAULT 0');
        }
        if (!in_array('trigger_type', $names)) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'eventos_actividades_catalogo` ADD `trigger_type` VARCHAR(50) NULL DEFAULT NULL');
        }
        if (!in_array('threshold', $names)) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'eventos_actividades_catalogo` ADD `threshold` INT(11) NULL DEFAULT NULL');
        }
    }

    // Agregar columna meta al log de puntos
    if ($CI->db->table_exists(db_prefix() . 'eventos_actividades_logs')) {
        $cols = $CI->db->query("SHOW COLUMNS FROM `" . db_prefix() . "eventos_actividades_logs`")->result_array();
        $names = array_map(function($c){ return $c['Field']; }, $cols);

        if (!in_array('meta', $names)) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'eventos_actividades_logs` ADD `meta` TEXT NULL DEFAULT NULL');
        }
    }

    // Tabla de escaneos (para actividades automáticas por escaneos)
    if (!$CI->db->table_exists(db_prefix() . 'eventos_actividades_scans')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "eventos_actividades_scans` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `staff_id` INT(11) NOT NULL,
            `barcode` VARCHAR(191) NULL DEFAULT NULL,
            `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `staff_id` (`staff_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    // Crear custom field para ver puntos en el perfil del usuario (Staff)
    if ($CI->db->table_exists(db_prefix() . 'customfields')) {
        $CI->db->where('slug', 'eventos_actividades_puntos');
        $existing = $CI->db->get(db_prefix().'customfields')->row();

        if (!$existing) {
            $CI->db->insert(db_prefix().'customfields', [
                'fieldto'       => 'staff',
                'name'          => 'Puntos (Eventos y actividades)',
                'type'          => 'number',
                'slug'          => 'eventos_actividades_puntos',
                'required'      => 0,
                'active'        => 1,
                'show_on_table' => 1,
                'bs_column'     => 6,
                'options'       => '',
                'display_inline'=> 0
            ]);
        }
    }



    // Opciones (para integración con módulo ESCANEO)
    if ($CI->db->table_exists(db_prefix().'options')) {
        $defaults = [
            'eventos_actividades_scan_source' => 'auto', // auto|internal|external
            'eventos_actividades_scan_source_table' => db_prefix().'escaneo_scans',
            'eventos_actividades_scan_source_staff_col' => 'staff_id',
            'eventos_actividades_scan_source_date_col' => 'fecha',
        ];

        foreach ($defaults as $k => $v) {
            $CI->db->where('name', $k);
            $ex = $CI->db->get(db_prefix().'options')->row();
            if (!$ex) {
                $CI->db->insert(db_prefix().'options', ['name' => $k, 'value' => $v]);
            }
        }
    }

    // Tabla de tokens API (para App móvil)
    if (!$CI->db->table_exists(db_prefix() . 'eventos_actividades_api_tokens')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . "eventos_actividades_api_tokens` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `staff_id` INT(11) NOT NULL,
            `token` VARCHAR(64) NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `last_used_at` DATETIME DEFAULT NULL,
            `expires_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `token` (`token`),
            INDEX `staff_id` (`staff_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

}
