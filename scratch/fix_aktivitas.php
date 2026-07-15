<?php
// 1. Update Dashboard_model.php
$model_file = 'C:\xampp\htdocs\garuda_cbt\application\models\Dashboard_model.php';
$model_content = file_get_contents($model_file);

// Replace the getAktivitasGuru method
$pattern_model = '/public function getAktivitasGuru.*?\$.*?\}?\s*$/s';
$replacement_model = <<<PHP
    public function getAktivitasGuru(\$id_user, \$id_guru) {
        // PERBAIKAN: log_materi.id_materi berelasi ke kelas_jadwal_materi.id_kjm
        \$sql_tugas = "
            SELECT 
                l.id_log as id,
                'tugas' as tipe,
                m.judul_materi as judul,
                s.nama as nama_siswa,
                m.id_materi as id_referensi,
                l.log_time as waktu,
                0 as is_read
            FROM log_materi l
            JOIN kelas_jadwal_materi kjm ON l.id_materi = kjm.id_kjm
            JOIN kelas_materi m ON kjm.id_materi = m.id_materi
            JOIN master_siswa s ON l.id_siswa = s.id_siswa
            WHERE m.id_guru = ? 
              AND m.jenis = 2 
              AND (l.nilai IS NULL OR l.nilai = '' OR l.nilai = '0')
        ";

        \$sql_ujian = "
            SELECT 
                n.id_nilai as id,
                'ujian' as tipe,
                b.bank_nama as judul,
                s.nama as nama_siswa,
                j.id_jadwal as id_referensi,
                n.time_create as waktu,
                0 as is_read
            FROM cbt_nilai n
            JOIN cbt_jadwal j ON n.id_jadwal = j.id_jadwal
            JOIN cbt_bank_soal b ON j.id_bank = b.id_bank
            JOIN master_siswa s ON n.id_siswa = s.id_siswa
            WHERE b.bank_guru_id = ?
              AND b.jml_esai > 0
              AND (n.dikoreksi = 0 OR n.dikoreksi IS NULL)
        ";

        \$sql_chat = "
            SELECT 
                c.id_pesan as id,
                'chat' as tipe,
                'Pesan Baru' as judul,
                u.first_name as nama_siswa,
                c.pengirim_id as id_referensi,
                c.created_at as waktu,
                c.is_read as is_read
            FROM chat_messages c
            JOIN users u ON c.pengirim_id = u.id
            WHERE c.penerima_id = ? 
              AND c.is_read = 0
        ";

        \$sql = "(\$sql_tugas) UNION (\$sql_ujian) UNION (\$sql_chat) ORDER BY waktu DESC LIMIT 15";
        return \$this->db->query(\$sql, array(\$id_guru, \$id_guru, \$id_user))->result();
    }
}
PHP;
$model_content = preg_replace($pattern_model, $replacement_model, $model_content);
file_put_contents($model_file, $model_content);

// 2. Update Dashboard.php (Controller) to include waktu
$controller_file = 'C:\xampp\htdocs\garuda_cbt\application\controllers\Dashboard.php';
$controller_content = file_get_contents($controller_file);
$controller_content = str_replace(
    "'is_read'      => \$row->is_read == 1 ? true : false",
    "'waktu'        => date('d M Y, H:i', strtotime(\$row->waktu)),\n                    'is_read'      => \$row->is_read == 1 ? true : false",
    $controller_content
);
file_put_contents($controller_file, $controller_content);

// 3. Update dashboard.php (Frontend JS) to include age_label and correct URLs
$view_file = 'C:\xampp\htdocs\garuda_cbt\application\views\members\guru\dashboard.php';
$view_content = file_get_contents($view_file);

$pattern_js = '/if \(item\.tipe === \'tugas\'\) \{.*?renderFeedItem\(feedItem\);\s*\}/s';
$replacement_js = <<<JS
if (item.tipe === 'tugas') {
                              title = 'Tugas: ' + item.judul;
                              body = item.nama_siswa + ' mengumpulkan tugas.';
                              icon = '<i class="fas fa-book-open text-blue-500"></i>';
                              url = 'kelasstatus';
                          } else if (item.tipe === 'ujian') {
                              title = 'Ujian: ' + item.judul;
                              body = item.nama_siswa + ' butuh koreksi esai.';
                              icon = '<i class="fas fa-check-circle text-green-500"></i>';
                              url = 'cbtnilai';
                          } else if (item.tipe === 'chat') {
                              title = item.judul;
                              body = 'Pesan dari: ' + item.nama_siswa;
                              icon = '<i class="fas fa-comments text-indigo-500"></i>';
                              url = 'chat';
                          }
                          var feedItem = {
                              title: title,
                              body: body,
                              icon: icon,
                              color: 'primary',
                              url: url,
                              age_label: item.waktu
                          };
                          html += renderFeedItem(feedItem); 
                      }
JS;
$view_content = preg_replace($pattern_js, $replacement_js, $view_content);
file_put_contents($view_file, $view_content);

echo "Fixes applied.\n";
