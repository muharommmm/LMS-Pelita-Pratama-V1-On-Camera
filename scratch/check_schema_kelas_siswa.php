<?php
$c = new mysqli('localhost', 'root', '', 'garuda'); 
$r = $c->query('DESCRIBE kelas_siswa'); 
if($r) {
    while ($row = $r->fetch_assoc()) echo $row['Field'] . "\n";
} else {
    echo "No kelas_siswa table";
}
