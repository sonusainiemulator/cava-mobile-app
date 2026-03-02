<?php
defined('BASEPATH') or exit('No direct script access allowed');

function escaneo_ean13_check_digit($code12)
{
    $code12 = preg_replace('/\D+/', '', (string)$code12);
    if (strlen($code12) !== 12) {
        return null;
    }
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $digit = intval($code12[$i]);
        $pos = $i + 1;
        $sum += ($pos % 2 === 0) ? ($digit * 3) : $digit;
    }
    $mod = $sum % 10;
    $check = ($mod === 0) ? 0 : (10 - $mod);
    return (string)$check;
}

function escaneo_normalize_ean13($code)
{
    $code = preg_replace('/\D+/', '', (string)$code);
    if (strlen($code) === 12) {
        $cd = escaneo_ean13_check_digit($code);
        if ($cd === null) return null;
        return $code . $cd;
    }
    if (strlen($code) === 13) {
        $base = substr($code, 0, 12);
        $cd = escaneo_ean13_check_digit($base);
        if ($cd === null) return null;
        return ($cd === substr($code, 12, 1)) ? $code : null;
    }
    return null;
}
