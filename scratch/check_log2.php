<?php
$mysqli = new mysqli("localhost", "root", "", "garuda");
$res = $mysqli->query("SELECT id_log, id_siswa, id_materi, log_type FROM log_materi LIMIT 5");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
