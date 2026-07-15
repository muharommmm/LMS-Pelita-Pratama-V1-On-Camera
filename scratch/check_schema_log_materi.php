<?php
$c = new mysqli('localhost', 'root', '', 'garuda'); 
$r = $c->query('DESCRIBE log_materi'); 
if($r) {
    while ($row = $r->fetch_assoc()) echo $row['Field'] . "\n";
} else {
    echo "No log_materi table";
}
