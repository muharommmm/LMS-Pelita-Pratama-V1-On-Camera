<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Clear output buffer to remove AdminLTE header template
if (ob_get_level() > 0) {
    ob_clean();
}

// Load the appropriate standalone Hermony view based on type
if ($judul === 'Tugas') {
    $data_tugas = $this->load->get_vars();
    $data_tugas['tugas']  = $this->load->get_var('materi');
    
    // logs is the individual student log object (or null)
    $log_obj = $this->load->get_var('logs');
    // log_mulai = the log object itself (opened = has log_time)
    $data_tugas['log_mulai']  = $log_obj;
    $data_tugas['log_obj_raw'] = $log_obj;
    
    // Check if task is graded as 0 (re-do required)
    $is_redo = ($log_obj != null && isset($log_obj->nilai) && $log_obj->nilai !== null && ((string)$log_obj->nilai === '0' || (float)$log_obj->nilai === 0.0));
    $data_tugas['is_redo'] = $is_redo;
    
    // log_selesai = the log object only if the student has submitted AND it is NOT marked for re-do (nilai == 0)
    $data_tugas['log_selesai'] = ($log_obj != null && (!empty($log_obj->text) || !empty($log_obj->file)) && !$is_redo) ? $log_obj : null;
    
    $this->load->view('members/siswa/tugas/view_hermony', $data_tugas);
} else {
    $this->load->view('members/siswa/materi/view_hermony');
}

// Stop execution so AdminLTE footer template is not appended
exit();

