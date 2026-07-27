<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Clear output buffer to remove AdminLTE header template
if (ob_get_level() > 0) {
    ob_clean();
}

// Auto-clear all grade notifications when student visits the results page
$CI =& get_instance();
$CI->load->model('Notifikasi_model', 'notif_clear');
$user_obj = $CI->ion_auth->user()->row();
if ($user_obj) {
    $CI->notif_clear->clearNilaiNotifications($user_obj->id);
}

// Load the new standalone Hermony Nilai view
$this->load->view('members/siswa/nilai/nilai_hermony');

// Stop execution so AdminLTE footer template is not appended
exit();
