<?php
$c = mysqli_connect('localhost', 'root', '');
$r = mysqli_query($c, 'SHOW DATABASES');
while($row = mysqli_fetch_assoc($r)) {
    echo $row['Database'] . "\n";
}
