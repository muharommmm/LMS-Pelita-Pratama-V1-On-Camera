<?php
$mysqli = new mysqli("localhost", "root", "", "garudacbt");
$result = $mysqli->query("DESCRIBE log_materi;");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
