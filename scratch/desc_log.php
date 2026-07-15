<?php
$db=new mysqli('localhost','root','','garuda');
$res=$db->query('DESCRIBE log_materi');
while($r=$res->fetch_assoc()) echo $r['Field']."\n";
