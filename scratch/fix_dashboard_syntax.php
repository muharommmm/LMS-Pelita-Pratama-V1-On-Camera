<?php
$controller_file = 'C:\xampp\htdocs\garuda_cbt\application\controllers\Dashboard.php';
$controller_content = file_get_contents($controller_file);

// Remove duplicate closing brace at the end of the file
$controller_content = preg_replace('/}\s*}\s*$/', "}\n", $controller_content);
file_put_contents($controller_file, $controller_content);

echo "Dashboard.php syntax fixed.\n";
