<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Clear output buffer to remove AdminLTE header template
if (ob_get_level() > 0) {
    ob_clean();
}

// Load the new standalone Hermony Tugas view
$this->load->view('members/siswa/tugas/tugas_hermony');

// Stop execution so AdminLTE footer template is not appended
exit();
