<?php
$c = new mysqli('localhost', 'root', '', 'garuda');
$r = $c->query('SHOW COLUMNS FROM kelas_materi');
while($row = $r->fetch_assoc()) {
    echo "Field: " . $row["Field"] . "\n";
}
?>
