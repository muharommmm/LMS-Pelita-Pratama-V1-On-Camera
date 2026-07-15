<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Clear the output loaded before this view
if (ob_get_level() > 0) {
    ob_clean();
}

// Load the new standalone Hermony Catatan view
$this->load->view('members/siswa/catatan/catatan_hermony');

// Stop execution so template footer isn't appended
exit();