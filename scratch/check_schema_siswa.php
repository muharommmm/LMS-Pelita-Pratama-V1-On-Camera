<?php
$c = new mysqli('localhost', 'root', '', 'garuda'); 
$r = $c->query('DESCRIBE master_siswa'); 
while ($row = $r->fetch_assoc()) echo $row['Field'] . "\n";
