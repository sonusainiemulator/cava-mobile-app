<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI =& get_instance();

if (!$CI->db->table_exists(db_prefix() . 'tequila_items')) {
    $charset = $CI->db->char_set;
    $collate = $CI->db->dbcollat;

    $sql = 'CREATE TABLE `' . db_prefix() . "tequila_items` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(20) NOT NULL,
        `title` varchar(191) DEFAULT NULL,
        `content` text DEFAULT NULL,
        `file_path` varchar(255) DEFAULT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate};";

    $CI->db->query($sql);
}
