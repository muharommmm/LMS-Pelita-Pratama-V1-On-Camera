<?php
define('ENVIRONMENT', 'development');
$system_path = 'system';
$application_folder = 'application';
$view_folder = '';
define('BASEPATH', str_replace('\\', '/', $system_path).'/');
define('APPPATH', $application_folder.'/');
define('VIEWPATH', $application_folder.'/views/');

require_once BASEPATH.'core/CodeIgniter.php'; // wait this is too complex to boot
