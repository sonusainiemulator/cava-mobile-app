<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'escaneo_bebidas')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "escaneo_bebidas` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `codigo_barras` VARCHAR(13) NOT NULL,
        `marca` VARCHAR(191) NOT NULL,
        `nombre_tequila` VARCHAR(191) NOT NULL,
        `presentacion` VARCHAR(191) NOT NULL,
        `grados_alcohol` DECIMAL(5,2) NOT NULL,
        `precio` DECIMAL(10,2) NOT NULL,
        `barcode_svg` LONGTEXT NULL,
        `imagen` VARCHAR(255) NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_codigo_barras` (`codigo_barras`),
        INDEX `idx_marca` (`marca`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . " COLLATE=" . $CI->db->dbcollat . ';');
} else {
    // Migration: add imagen column if missing
    $fields = $CI->db->field_data(db_prefix() . 'escaneo_bebidas');
    $names = [];
    foreach ($fields as $f) { $names[$f->name] = true; }
    if (!isset($names['imagen'])) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . "escaneo_bebidas` ADD COLUMN `imagen` VARCHAR(255) NULL AFTER `barcode_svg`");
    }
}

if (!$CI->db->table_exists(db_prefix() . 'escaneo_scans')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "escaneo_scans` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `bebida_id` INT(11) NULL,
        `codigo_barras` VARCHAR(13) NOT NULL,
        `marca` VARCHAR(191) NOT NULL,
        `nombre_tequila` VARCHAR(191) NOT NULL,
        `presentacion` VARCHAR(191) NOT NULL,
        `grados_alcohol` DECIMAL(5,2) NOT NULL,
        `precio` DECIMAL(10,2) NOT NULL,
        `lat` DECIMAL(10,7) NOT NULL,
        `lng` DECIMAL(10,7) NOT NULL,
        `fecha_escaneo` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `staff_id` INT(11) NULL,
        PRIMARY KEY (`id`),
        INDEX `idx_codigo_barras` (`codigo_barras`),
        INDEX `idx_marca` (`marca`),
        INDEX `idx_bebida_id` (`bebida_id`),
        INDEX `idx_staff_id` (`staff_id`),
        INDEX `idx_fecha` (`fecha_escaneo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . " COLLATE=" . $CI->db->dbcollat . ';');
} else {
    $fields = $CI->db->field_data(db_prefix() . 'escaneo_scans');
    $names = [];
    foreach ($fields as $f) { $names[$f->name] = true; }

    if (!isset($names['bebida_id'])) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . "escaneo_scans` ADD COLUMN `bebida_id` INT(11) NULL AFTER `id`");
        $CI->db->query('ALTER TABLE `' . db_prefix() . "escaneo_scans` ADD INDEX `idx_bebida_id` (`bebida_id`)");
    }
    if (!isset($names['fecha_escaneo'])) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . "escaneo_scans` ADD COLUMN `fecha_escaneo` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `lng`");
        $CI->db->query('ALTER TABLE `' . db_prefix() . "escaneo_scans` ADD INDEX `idx_fecha` (`fecha_escaneo`)");
    }
    if (!isset($names['staff_id'])) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . "escaneo_scans` ADD COLUMN `staff_id` INT(11) NULL AFTER `fecha_escaneo`");
        $CI->db->query('ALTER TABLE `' . db_prefix() . "escaneo_scans` ADD INDEX `idx_staff_id` (`staff_id`)");
    }
}
