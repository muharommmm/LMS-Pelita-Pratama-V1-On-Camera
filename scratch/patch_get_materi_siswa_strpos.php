<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

$start_pos = strpos($content, 'public function getMateriKelasSiswa($id_materi, $jenis) {');
if ($start_pos !== false) {
    $end_pos = strpos($content, 'n4p02: }', $start_pos);
    if ($end_pos !== false) {
        $end_pos += strlen('n4p02: }');
        $old_func = substr($content, $start_pos, $end_pos - $start_pos);
        
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
EOT;
        $content = substr_replace($content, $new_func, $start_pos, $end_pos - $start_pos);
        file_put_contents($file, $content);
        echo "Successfully replaced getMateriKelasSiswa using strpos/substr_replace.\n";
    } else {
        echo "End pos not found\n";
    }
} else {
    echo "Start pos not found\n";
}
