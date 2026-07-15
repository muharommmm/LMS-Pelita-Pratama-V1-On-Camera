<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

$new_methods = <<<EOT

    public function getMateriTugasSiswaDeadline(\$id_tp, \$id_smt, \$id_kelas, \$jenis) {
        \$this->db->select('a.*, b.kode as kode_mapel, b.nama_mapel, d.nama_guru, a.id_materi as id_kjm, a.created_on as jadwal_materi');
        \$this->db->from('kelas_materi a');
        \$this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
        \$this->db->join('master_guru d', 'a.id_guru=d.id_guru', 'left');
        \$this->db->where('a.id_tp', \$id_tp);
        \$this->db->where('a.id_smt', \$id_smt);
        \$this->db->where('a.jenis', \$jenis);
        \$this->db->order_by('a.created_on', 'DESC');
        \$results = \$this->db->get()->result();
        
        \$filtered = [];
        foreach (\$results as \$row) {
            \$kelas_arr = @unserialize(\$row->materi_kelas);
            if (is_array(\$kelas_arr) && in_array(\$id_kelas, \$kelas_arr)) {
                \$filtered[] = \$row;
            }
        }
        return \$filtered;
    }

    public function getLogsSiswa(\$id_siswa, \$id_tp, \$id_smt) {
        \$this->db->select('*');
        \$this->db->from('log_materi');
        \$this->db->where('id_siswa', \$id_siswa);
        \$results = \$this->db->get()->result();
        
        \$logs = [];
        foreach(\$results as \$log) {
            \$logs[\$log->id_materi] = \$log;
        }
        return \$logs;
    }

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

    public function saveLog(\$table, \$id_siswa, \$id_materi, \$jamke, \$mapel, \$desc) {
        \$data = [
            'id_siswa' => \$id_siswa,
            'id_materi' => \$id_materi,
            'jam_ke' => \$jamke,
            'id_mapel' => \$mapel,
            'log_type' => '1',
            'log_desc' => \$desc,
            'log_time' => date('Y-m-d H:i:s')
        ];
        
        \$exists = \$this->db->get_where(\$table, [
            'id_siswa' => \$id_siswa,
            'id_materi' => \$id_materi
        ])->row();
        
        if (\$exists) {
            // If already exists, we might not want to overwrite log_time if it's already there
            return true;
        } else {
            return \$this->db->insert(\$table, \$data);
        }
    }
EOT;

$pos = strrpos($content, '}');
if ($pos !== false) {
    $content = substr_replace($content, $new_methods . "\n}", $pos, 1);
    file_put_contents($file, $content);
    echo "Methods added successfully!\n";
} else {
    echo "Failed to find end of class.\n";
}
