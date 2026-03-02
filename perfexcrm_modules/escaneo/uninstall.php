<?php
defined('BASEPATH') or exit('No direct script access allowed');
$CI = &get_instance();

if ($CI->db->table_exists(db_prefix() . 'escaneo_scans')) {
    $CI->db->query('DROP TABLE `' . db_prefix() . 'escaneo_scans`');
}
if ($CI->db->table_exists(db_prefix() . 'escaneo_bebidas')) {
    $CI->db->query('DROP TABLE `' . db_prefix() . 'escaneo_bebidas`');
}
