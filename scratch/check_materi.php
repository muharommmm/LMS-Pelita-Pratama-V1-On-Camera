<?php
$db = new mysqli('localhost','root','','garuda');
$res = $db->query('SELECT * FROM kelas_materi LIMIT 1');
print_r($res->fetch_assoc());
