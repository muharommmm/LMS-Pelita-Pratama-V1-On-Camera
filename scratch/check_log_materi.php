<?php
$c = new mysqli('localhost', 'root', '', 'garuda');
$r = $c->query('SHOW COLUMNS FROM log_materi');
while($row = $r->fetch_assoc()) {
    echo "Field: " . $row["Field"] . "\n";
}
?>
