<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

    $CI->db->query('CREATE TABLE `' . db_prefix() . "cava_wines` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(191) NOT NULL,
        `barcode` VARCHAR(191) NULL DEFAULT NULL,
        `brand` VARCHAR(191) NULL DEFAULT NULL,
        `presentation` VARCHAR(191) NULL DEFAULT NULL,
        `alcohol_degrees` DECIMAL(5,2) NULL DEFAULT NULL,
        `price` DECIMAL(15,2) NULL DEFAULT NULL,
        `image` VARCHAR(191) NOT NULL,
        `created_by` INT(11) DEFAULT NULL,
        `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        INDEX `barcode` (`barcode`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// Add columns if table exists but columns do not
if ($CI->db->table_exists(db_prefix() . 'cava_wines')) {
    $cols = $CI->db->query("SHOW COLUMNS FROM `" . db_prefix() . "cava_wines`")->result_array();
    $names = array_map(function($c){ return $c['Field']; }, $cols);

    if (!in_array('barcode', $names)) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'cava_wines` ADD `barcode` VARCHAR(191) NULL DEFAULT NULL AFTER `name`, ADD INDEX `barcode` (`barcode`)');
    }
    if (!in_array('brand', $names)) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'cava_wines` ADD `brand` VARCHAR(191) NULL DEFAULT NULL AFTER `barcode`');
    }
    if (!in_array('presentation', $names)) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'cava_wines` ADD `presentation` VARCHAR(191) NULL DEFAULT NULL AFTER `brand`');
    }
    if (!in_array('alcohol_degrees', $names)) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'cava_wines` ADD `alcohol_degrees` DECIMAL(5,2) NULL DEFAULT NULL AFTER `presentation`');
    }
    if (!in_array('price', $names)) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'cava_wines` ADD `price` DECIMAL(15,2) NULL DEFAULT NULL AFTER `alcohol_degrees`');
    }
}


if (!$CI->db->table_exists(db_prefix() . 'cava_user_wines')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "cava_user_wines` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `wine_id` INT(11) NOT NULL,
        `date_added` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_wine_unique` (`user_id`, `wine_id`),
        KEY `user_id` (`user_id`),
        KEY `wine_id` (`wine_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}
