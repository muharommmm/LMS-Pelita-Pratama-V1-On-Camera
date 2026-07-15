<?php
// Define constants
define('ENVIRONMENT', 'development');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('BASEPATH', FCPATH . 'system/');
define('APPPATH', FCPATH . 'application/');

require BASEPATH . 'core/Common.php';

// Bootstrap CI manually
// This is hard to do without full server environment. Let's just use curl if possible, but curl didn't work.
// I can use file_get_contents to hit localhost!
