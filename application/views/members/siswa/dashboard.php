<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Clear the header that was loaded before this view
if (ob_get_level() > 0) {
    ob_clean();
}

// Load the new standalone Hermony dashboard
$this->load->view('members/siswa/dashboard_hermony');

// Exit to prevent the template/footer from being appended by the controller
exit();
