<?php
$mysqli = new mysqli("localhost", "root", "", "garuda");
$res = $mysqli->query("SHOW COLUMNS FROM log_materi");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
