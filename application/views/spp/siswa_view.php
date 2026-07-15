<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Clear output buffer to remove AdminLTE header template
if (ob_get_level() > 0) {
    ob_clean();
}

// Load the new standalone Hermony SPP view
$this->load->view('spp/siswa_view_hermony');

// Stop execution so AdminLTE footer template is not appended
exit();
