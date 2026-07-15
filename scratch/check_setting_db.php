<?php
define('BASEPATH', true);
require 'application/config/database.php';
$conn = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
$res = $conn->query("SELECT logo_kanan, logo_kiri FROM setting");
print_r($res->fetch_assoc());
