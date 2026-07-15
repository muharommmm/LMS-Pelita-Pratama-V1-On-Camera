<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

$func_to_remove = <<<EOT
    public function getMateriKelasSiswa(\$id_materi, \$jenis = null) {
        \$this->db->select('a.*, b.kode as kode_mapel, b.nama_mapel, d.nama_guru, a.id_materi as id_kjm, a.created_on as jadwal_materi');
        \$this->db->from('kelas_materi a');
        \$this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
        \$this->db->join('master_guru d', 'a.id_guru=d.id_guru', 'left');
        \$this->db->where('a.id_materi', \$id_materi);
        if (\$jenis != null) {
            \$this->db->where('a.jenis', \$jenis);
        }
        return \$this->db->get()->row();
    }
EOT;

$content = str_replace($func_to_remove, '', $content);
file_put_contents($file, $content);
echo "Removed!\n";
