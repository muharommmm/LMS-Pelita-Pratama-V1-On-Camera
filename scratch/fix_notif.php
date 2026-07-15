<?php
$f = 'C:\xampp\htdocs\garuda_cbt\application\models\Notifikasi_model.php';
$c = file_get_contents($f);
$c = str_replace("'id'       => \$notif->id,", "'id'       => isset(\$notif->id_notif) ? \$notif->id_notif : (isset(\$notif->id) ? \$notif->id : null),", $c);
file_put_contents($f, $c);
echo "Replaced all occurrences.\n";
