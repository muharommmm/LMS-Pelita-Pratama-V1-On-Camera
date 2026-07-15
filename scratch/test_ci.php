<?php
define('ENVIRONMENT', 'development');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('BASEPATH', FCPATH . 'system/');
define('APPPATH', FCPATH . 'application/');

require BASEPATH . 'database/DB.php';
// wait, loading DB outside of CI is hard. Let's just create a dummy controller inside CI.
