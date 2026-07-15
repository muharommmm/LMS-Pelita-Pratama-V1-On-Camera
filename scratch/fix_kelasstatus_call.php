<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php');
$c = str_replace('$log = $this->kelas->getStatusMateriSiswa($id_kelas, $id_materi);', '$log = $this->kelas->getStatusMateriSiswaGuru($id_kelas, $id_materi);', $c);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php', $c);
echo "Updated Kelasstatus to use getStatusMateriSiswaGuru\n";
