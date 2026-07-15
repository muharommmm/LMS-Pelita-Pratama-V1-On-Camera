<?php
$mysqli = new mysqli("localhost", "root", "", "garuda");
$res = $mysqli->query("SELECT * FROM kelas_materi LIMIT 5");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
