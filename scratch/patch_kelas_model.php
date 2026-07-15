<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

// 1. Rename getKodeMateriMapel
$content = str_replace('public function getKodeMateriMapel(', 'public function getKodeMateriMapel_OLD(', $content);

// 2. Rename getAllKodeMateri
$content = str_replace('public function getAllKodeMateri(', 'public function getAllKodeMateri_OLD(', $content);

// 3. Rename getStatusMateriSiswaGuru
$content = str_replace('public function getStatusMateriSiswaGuru(', 'public function getStatusMateriSiswaGuru_OLD(', $content);

// 4. Rename getStatusMateriSiswa
$content = str_replace('public function getStatusMateriSiswa(', 'public function getStatusMateriSiswa_OLD(', $content);


// New methods to inject
$new_methods = <<<EOT

    public function getKodeMateriMapel(\$id_tp, \$id_smt, \$id_mapel, \$id_guru = null) {
        \$this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, a.id_materi as id_kjm, a.created_on as jadwal_materi, d.nama_guru, a.judul_materi');
        \$this->db->from('kelas_materi a');
        \$this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        \$this->db->join('master_guru d', 'a.id_guru=d.id_guru', 'left');
        \$this->db->where('a.id_tp', \$id_tp);
        \$this->db->where('a.id_smt', \$id_smt);
        \$this->db->where('a.id_mapel', \$id_mapel);
        if (\$id_guru != null) {
            \$this->db->where('a.id_guru', \$id_guru);
        }
        \$results = \$this->db->get()->result();
        
        \$expanded = [];
        foreach (\$results as \$row) {
            \$kelas_arr = @unserialize(\$row->materi_kelas);
            if (is_array(\$kelas_arr)) {
                foreach (\$kelas_arr as \$k) {
                    \$new_row = clone \$row;
                    \$new_row->id_kelas = \$k;
                    \$expanded[] = \$new_row;
                }
            }
        }
        return \$expanded;
    }

    public function getAllKodeMateri(\$id_tp, \$id_smt, \$id_guru = null) {
        \$this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, a.id_materi as id_kjm, a.created_on as jadwal_materi, d.nama_guru, a.judul_materi');
        \$this->db->from('kelas_materi a');
        \$this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        \$this->db->join('master_guru d', 'a.id_guru=d.id_guru', 'left');
        \$this->db->where('a.id_tp', \$id_tp);
        \$this->db->where('a.id_smt', \$id_smt);
        if (\$id_guru != null) {
            \$this->db->where('a.id_guru', \$id_guru);
        }
        \$results = \$this->db->get()->result();
        
        \$expanded = [];
        foreach (\$results as \$row) {
            \$kelas_arr = @unserialize(\$row->materi_kelas);
            if (is_array(\$kelas_arr)) {
                foreach (\$kelas_arr as \$k) {
                    \$new_row = clone \$row;
                    \$new_row->id_kelas = \$k;
                    \$expanded[] = \$new_row;
                }
            }
        }
        return \$expanded;
    }

    public function getStatusMateriSiswaGuru(\$id_kelas, \$id_materi) {
        \$this->db->select('a.id_siswa, a.nama, a.nis, a.foto, b.log_time, b.log_desc as status, b.nilai, b.catatan, b.file as file_siswa, b.id_log');
        \$this->db->from('master_siswa a');
        \$this->db->join('log_materi b', 'a.id_siswa=b.id_siswa AND b.id_materi="'.\$id_materi.'"', 'left');
        \$this->db->where('a.id_kelas', \$id_kelas);
        \$this->db->order_by('a.nama', 'ASC');
        return \$this->db->get()->result();
    }

    public function getStatusMateriSiswa(\$id_kjm = null) {
        \$this->db->select('a.*, b.nama, b.nis, b.foto');
        \$this->db->from('log_materi a');
        \$this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
        \$this->db->where('a.id_materi', \$id_kjm); 
        return \$this->db->get()->result();
    }
EOT;

// Find the last closing brace and insert before it
$pos = strrpos($content, '}');
if ($pos !== false) {
    $content = substr_replace($content, $new_methods . "\n}", $pos, 1);
    file_put_contents($file, $content);
    echo "Patch applied successfully.\n";
} else {
    echo "Failed to find end of class.\n";
}
