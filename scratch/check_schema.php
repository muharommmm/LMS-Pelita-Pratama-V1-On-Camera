<?php
$c = new mysqli('localhost', 'root', '', 'garuda'); 
$r = $c->query('DESCRIBE kelas_materi'); 
while ($row = $r->fetch_assoc()) echo $row['Field'] . "\n";
