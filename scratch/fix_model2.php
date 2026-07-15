<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php');

$new_func = <<<'EOD'
public function getStatusMateriSiswaGuru($id_kelas, $id_materi) {
    $this->db->select('a.id_siswa, b.nis, b.nama, a.id_kelas as kelas_id, c.nama_kelas as kelas, d.log_time as mulai, d.finish_time as selesai, d.text, d.nilai, d.file, d.catatan, d.jam_ke');
    $this->db->from('kelas_siswa a');
    $this->db->join('master_siswa b', 'a.id_siswa = b.id_siswa', 'left');
    $this->db->join('master_kelas c', 'a.id_kelas = c.id_kelas', 'left');
    $this->db->join('log_materi d', 'a.id_siswa = d.id_siswa AND d.id_materi = "'.$id_materi.'"', 'left');
    $this->db->where('a.id_kelas', $id_kelas);
    $this->db->order_by('b.nama', 'ASC');
    $result = $this->db->get()->result();
    $ret = [];
    if ($result) {
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
    }
    return $ret;
}

public function getStatusMateriSiswa($id_materi = null) {
    $this->db->select('*');
    $this->db->from('log_materi');
    $this->db->where('id_materi', $id_materi);
    $result = $this->db->get()->result();
    $ret = [];
    if ($result) {
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
    }
    return $ret;
}
EOD;

$c = preg_replace('/public function getStatusMateriSiswa\([^\)]+\)\s*\{[^\}]+(\}[^\}]+)*\s*\}/U', $new_func, $c, 1);
file_put_contents('C:\xampp\htdocs\garuda_cbt\application\models\Kelas_model.php', $c);
echo "Restored getStatusMateriSiswa and added getStatusMateriSiswaGuru\n";
