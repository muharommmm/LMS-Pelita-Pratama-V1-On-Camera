<?php
$c = new mysqli('localhost', 'root', '', 'garuda');
$r = $c->query('SHOW TABLES LIKE "%jadwal%"');
while($row = $r->fetch_row()) echo $row[0] . "\n";
?>
