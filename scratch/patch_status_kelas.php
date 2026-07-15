<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

$search = <<<EOT
    public function getStatusMateriSiswaGuru(\$id_kelas, \$id_materi) {
        \$this->db->select('a.id_siswa, a.nama, a.nis, a.foto, c.id_kelas, b.log_time, b.finish_time, b.log_desc as status, b.nilai, b.text, b.catatan, b.file, b.id_log');
        \$this->db->from('master_siswa a');
        \$this->db->join('kelas_siswa c', 'a.id_siswa = c.id_siswa', 'left');
        \$this->db->join('log_materi b', 'a.id_siswa=b.id_siswa AND b.id_materi="'.\$id_materi.'"', 'left');
        \$this->db->where('c.id_kelas', \$id_kelas);
EOT;

$replace = <<<EOT
    public function getStatusMateriSiswaGuru(\$id_kelas, \$id_materi) {
        \$this->db->select('a.id_siswa, a.nama, a.nis, a.foto, c.id_kelas, d.nama_kelas as kelas, b.log_time, b.finish_time, b.log_desc as status, b.nilai, b.text, b.catatan, b.file, b.id_log');
        \$this->db->from('master_siswa a');
        \$this->db->join('kelas_siswa c', 'a.id_siswa = c.id_siswa', 'left');
        \$this->db->join('master_kelas d', 'c.id_kelas = d.id_kelas', 'left');
        \$this->db->join('log_materi b', 'a.id_siswa=b.id_siswa AND b.id_materi="'.\$id_materi.'"', 'left');
        \$this->db->where('c.id_kelas', \$id_kelas);
EOT;

$content = str_replace($search, $replace, $content);

file_put_contents($file, $content);
echo "Patched getStatusMateriSiswaGuru successfully!\n";
