<?php
$file = 'application/models/Kelas_model.php';
$content = file_get_contents($file);

// We need to replace the getStatusMateriSiswaGuru that I just injected, with an even better one!
// The previous one was:
/*
    public function getStatusMateriSiswaGuru($id_kelas, $id_materi) {
        $this->db->select('a.id_siswa, a.nama, a.nis, a.foto, b.log_time, b.log_desc as status, b.nilai, b.catatan, b.file as file_siswa, b.id_log');
        $this->db->from('master_siswa a');
        $this->db->join('log_materi b', 'a.id_siswa=b.id_siswa AND b.id_materi="'.$id_materi.'"', 'left');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->order_by('a.nama', 'ASC');
        return $this->db->get()->result();
    }
*/

$old_func = <<<EOT
    public function getStatusMateriSiswaGuru(\$id_kelas, \$id_materi) {
        \$this->db->select('a.id_siswa, a.nama, a.nis, a.foto, b.log_time, b.log_desc as status, b.nilai, b.catatan, b.file as file_siswa, b.id_log');
        \$this->db->from('master_siswa a');
        \$this->db->join('log_materi b', 'a.id_siswa=b.id_siswa AND b.id_materi="'.\$id_materi.'"', 'left');
        \$this->db->where('a.id_kelas', \$id_kelas);
        \$this->db->order_by('a.nama', 'ASC');
        return \$this->db->get()->result();
    }
EOT;

$new_func = <<<EOT
    public function getStatusMateriSiswaGuru(\$id_kelas, \$id_materi) {
        \$this->db->select('a.id_siswa, a.nama, a.nis, a.foto, c.id_kelas, b.log_time, b.finish_time, b.log_desc as status, b.nilai, b.text, b.catatan, b.file, b.id_log');
        \$this->db->from('master_siswa a');
        \$this->db->join('kelas_siswa c', 'a.id_siswa = c.id_siswa', 'left');
        \$this->db->join('log_materi b', 'a.id_siswa=b.id_siswa AND b.id_materi="'.\$id_materi.'"', 'left');
        \$this->db->where('c.id_kelas', \$id_kelas);
        \$this->db->order_by('a.nama', 'ASC');
        \$res = \$this->db->get()->result();
        
        foreach (\$res as \$r) {
            \$r->login = \$r->log_time;
            \$r->mulai = \$r->log_time;
            \$r->selesai = \$r->finish_time;
            \$r->diff = (object)[
                'terlambat' => false,
                'days' => 0,
                'jam' => 0,
                'menit' => 0
            ];
            if (!isset(\$r->text)) \$r->text = '';
            
            if (!empty(\$r->file)) {
                \$unserialized = @unserialize(\$r->file);
                if (is_array(\$unserialized) || is_object(\$unserialized)) {
                    \$r->file = \$unserialized;
                } else {
                    \$decoded = @json_decode(\$r->file);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        \$r->file = \$decoded;
                    } else {
                        \$r->file = [];
                    }
                }
            } else {
                \$r->file = [];
            }
        }
        return \$res;
    }
EOT;

$content = str_replace($old_func, $new_func, $content);
file_put_contents($file, $content);
echo "Replaced successfully!\n";
