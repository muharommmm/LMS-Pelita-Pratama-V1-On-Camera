<?php
$c = mysqli_connect('localhost', 'root', '');
// Create garuda
mysqli_query($c, 'CREATE DATABASE garuda');
// Get all tables in dbgaruda
$r = mysqli_query($c, 'SHOW TABLES IN dbgaruda');
while($row = mysqli_fetch_row($r)) {
    $table = $row[0];
    mysqli_query($c, "RENAME TABLE dbgaruda.{$table} TO garuda.{$table}");
}
// Drop dbgaruda
mysqli_query($c, 'DROP DATABASE dbgaruda');
echo "Database renamed successfully back to garuda.";
