<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

$search_regex = '/public function getMateriKelasSiswa.*?n4p02: \}/s';

if (preg_match($search_regex, $content, $matches)) {
    echo "Found old getMateriKelasSiswa function.\n";
    
    $new_func = <<<EOT
public function getMateriKelasSiswa(\$id_materi, \$jenis) {
        \$this->db->select("a.id_materi as id_kjm, a.id_mapel, a.id_materi, a.materi_kelas, a.judul_materi, a.isi_materi, a.file, a.jenis, a.id_guru, c.nama_mapel, d.nama_guru, d.foto");
        \$this->db->from("kelas_materi a");
        \$this->db->join("master_mapel c", "c.id_mapel=a.id_mapel", "left");
        \$this->db->join("master_guru d", "d.id_guru=a.id_guru", "left");
        \$this->db->where("a.id_materi", \$id_materi);
        if (\$jenis == "1") {
            \$this->db->where("a.jenis", "1");
        } else {
            \$this->db->where("a.jenis", "2");
        }
        return \$this->db->get()->row();
    }
EOT;

    $content = preg_replace($search_regex, $new_func, $content);
    file_put_contents($file, $content);
    echo "Successfully replaced getMateriKelasSiswa.\n";
} else {
    echo "Not found.\n";
}
