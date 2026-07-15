<?php
$c = new mysqli('localhost', 'root', '', 'garuda');
$r = $c->query('SHOW COLUMNS FROM jadwal_fleksibel');
while($row = $r->fetch_assoc()) {
    echo "Field: " . $row["Field"] . " Type: " . $row["Type"] . "\n";
}
?>
