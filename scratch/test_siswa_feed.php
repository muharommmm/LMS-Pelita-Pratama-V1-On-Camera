<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('ENVIRONMENT', 'development');
require_once 'index.php';

$CI =& get_instance();
$CI->load->model('Notifikasi_model', 'notif');

$tugas = $CI->notif->getLiveTugasSiswa(1, 1, 1, 1);
print_r($tugas);

$materis = $CI->notif->getLiveMateriBaruSiswa(1, 1, 1, 1, 7);
print_r($materis);
