<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

$old_func1 = <<<EOT
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
EOT;

$new_func1 = <<<EOT
    public function getKodeMateriMapel(\$id_tp, \$id_smt, \$id_mapel, \$id_guru = null) {
        \$this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, b.nama_mapel, a.id_materi as id_kjm, a.created_on as jadwal_materi, d.nama_guru, a.judul_materi, a.tgl_mulai, a.deadline as tgl_selesai');
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
        
        \$kelas_res = \$this->db->get('master_kelas')->result();
        \$kelas_map = [];
        foreach(\$kelas_res as \$kr) { \$kelas_map[\$kr->id_kelas] = \$kr->nama_kelas; }

        \$expanded = [];
        foreach (\$results as \$row) {
            \$kelas_arr = @unserialize(\$row->materi_kelas);
            if (is_array(\$kelas_arr)) {
                foreach (\$kelas_arr as \$k) {
                    \$new_row = clone \$row;
                    \$new_row->id_kelas = \$k;
                    \$new_row->materi_kelas = isset(\$kelas_map[\$k]) ? \$kelas_map[\$k] : 'Kelas '.\$k;
                    \$expanded[] = \$new_row;
                }
            }
        }
        return \$expanded;
    }
EOT;

$old_func2 = <<<EOT
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
EOT;

$new_func2 = <<<EOT
    public function getAllKodeMateri(\$id_tp, \$id_smt, \$id_guru = null) {
        \$this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, b.nama_mapel, a.id_materi as id_kjm, a.created_on as jadwal_materi, d.nama_guru, a.judul_materi, a.tgl_mulai, a.deadline as tgl_selesai');
        \$this->db->from('kelas_materi a');
        \$this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        \$this->db->join('master_guru d', 'a.id_guru=d.id_guru', 'left');
        \$this->db->where('a.id_tp', \$id_tp);
        \$this->db->where('a.id_smt', \$id_smt);
        if (\$id_guru != null) {
            \$this->db->where('a.id_guru', \$id_guru);
        }
        \$results = \$this->db->get()->result();
        
        \$kelas_res = \$this->db->get('master_kelas')->result();
        \$kelas_map = [];
        foreach(\$kelas_res as \$kr) { \$kelas_map[\$kr->id_kelas] = \$kr->nama_kelas; }

        \$expanded = [];
        foreach (\$results as \$row) {
            \$kelas_arr = @unserialize(\$row->materi_kelas);
            if (is_array(\$kelas_arr)) {
                foreach (\$kelas_arr as \$k) {
                    \$new_row = clone \$row;
                    \$new_row->id_kelas = \$k;
                    \$new_row->materi_kelas = isset(\$kelas_map[\$k]) ? \$kelas_map[\$k] : 'Kelas '.\$k;
                    \$expanded[] = \$new_row;
                }
            }
        }
        return \$expanded;
    }
EOT;

$content = str_replace($old_func1, $new_func1, $content);
$content = str_replace($old_func2, $new_func2, $content);

file_put_contents($file, $content);
echo "Patched getKodeMateriMapel and getAllKodeMateri!\n";
