<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');

$new_func = <<<'EOD'
public function getStatusMateriSiswa($id_kelas, $id_materi) {
    $this->db->select('a.id_siswa, a.nis, a.nama, a.kelas_id, b.nama_kelas as kelas, c.log_time as mulai, c.finish_time as selesai, c.text, c.nilai, c.file, c.catatan, c.jam_ke');
    $this->db->from('master_siswa a');
    $this->db->join('master_kelas b', 'a.kelas_id = b.id_kelas', 'left');
    $this->db->join('log_materi c', 'a.id_siswa = c.id_siswa AND c.id_materi = "'.$id_materi.'"', 'left');
    $this->db->where('a.kelas_id', $id_kelas);
    $this->db->order_by('a.nama', 'ASC');
    return $this->db->get()->result();
}
EOD;

$c = preg_replace('/public function getStatusMateriSiswa\([^\)]+\)\s*\{[^\}]+(\}[^\}]+)*\s*\}/U', $new_func, $c, 1);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php', $c);
echo "Replaced getStatusMateriSiswa\n";
