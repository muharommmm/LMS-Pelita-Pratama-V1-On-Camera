<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

// Rename old method
$content = str_replace('public function getMateriKelasSiswa(', 'public function getMateriKelasSiswa_OLD(', $content);

// Ensure we don't append multiple times
if (strpos($content, 'public function getMateriKelasSiswa(') === false) {
    // Append new method before the last closing brace
    $new_func = <<<EOT

    public function getMateriKelasSiswa(\$id_materi, \$jenis) {
        \$this->db->select("a.id_materi as id_kjm, a.id_mapel, a.id_materi, a.*, c.nama_mapel, d.nama_guru, d.foto");
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
}
EOT;
    
    // Replace the very last '}' in the file
    $content = preg_replace('/\}\s*$/', $new_func, $content);
    
    file_put_contents($file, $content);
    echo "Successfully replaced getMateriKelasSiswa.\n";
} else {
    echo "Already replaced or still exists.\n";
}
