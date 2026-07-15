<?php
/**
 * ============================================================
 *  GARUDA CBT - FACTORY RESET SCRIPT
 *  Pre-Deployment Database Cleanup
 * ============================================================
 *  PERINGATAN: Skrip ini akan MENGHAPUS PERMANEN seluruh data 
 *  dummy dari database. Hanya akun Admin (ID=1) yang dipertahankan.
 *  
 *  Jalankan dengan: php scratch/factory_reset.php
 * ============================================================
 */

$db = new mysqli('localhost', 'root', '', 'garuda');
if ($db->connect_error) { die("❌ Koneksi gagal: " . $db->connect_error . "\n"); }

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║    GARUDA CBT - FACTORY RESET (Pre-Deployment)      ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

// Disable foreign key checks temporarily
$db->query("SET FOREIGN_KEY_CHECKS = 0");

$success = 0;
$failed  = 0;

// ──────────────────────────────────────────────────────────
// 1. TRUNCATE: Data aktivitas, nilai, ujian
// ──────────────────────────────────────────────────────────
$truncate_tables = [
    // --- Nilai & Ujian ---
    'cbt_nilai',
    'cbt_soal_siswa',
    'cbt_durasi_siswa',
    'cbt_sesi_siswa',
    'cbt_nomor_peserta',
    'cbt_rekap',
    'cbt_rekap_nilai',
    'cbt_token',
    'cbt_pengawas',
    'cbt_kelas_ruang',
    'cbt_kop_absensi',
    'cbt_kop_berita',
    'cbt_kop_kartu',
    'log_ujian',

    // --- Bank Soal & Jadwal Ujian ---
    'cbt_soal',
    'cbt_bank_soal',
    'cbt_jadwal',

    // --- Log & Aktivitas ---
    'log_materi',
    'log',
    'dashboard_notifications',

    // --- Materi & Tugas Pembelajaran ---
    'kelas_jadwal_materi',
    'kelas_materi',

    // --- Absensi ---
    'absensi_siswa',

    // --- Chat ---
    'chat_messages',

    // --- Ebook ---
    'ebook_reading_history',
    'ebooks',

    // --- Honor/Gaji ---
    'honor_mutations',
    'honor_records',

    // --- Jadwal ---
    'jadwal_fleksibel',
    'kelas_jadwal_kbm',
    'kelas_jadwal_mapel',

    // --- Posting/Pengumuman ---
    'post',
    'post_comments',
    'post_reply',

    // --- Agenda ---
    'agendas',

    // --- Rapor (data nilai rapor) ---
    'rapor_catatan_wali',
    'rapor_data_catatan',
    'rapor_data_fisik',
    'rapor_data_sikap',
    'rapor_fisik',
    'rapor_kikd',
    'rapor_kkm',
    'rapor_naik',
    'rapor_nilai_akhir',
    'rapor_nilai_ekstra',
    'rapor_nilai_harian',
    'rapor_nilai_pts',
    'rapor_nilai_sikap',
    'rapor_prestasi',

    // --- Buku Induk ---
    'buku_induk',

    // --- Kelas Siswa (relasi siswa-kelas) ---
    'kelas_siswa',

    // --- Ekstra ---
    'kelas_ekstra',
    'kelas_catatan_mapel',
    'kelas_catatan_wali',
    'kelas_struktur',

    // --- SPP ---
    'spp_billing',

    // --- Login Attempts ---
    'login_attempts',

    // --- Users Profile ---
    'users_profile',
];

echo "── FASE 1: TRUNCATE Tabel Data Dummy ──\n\n";

foreach ($truncate_tables as $table) {
    // Check if table exists first
    $check = $db->query("SHOW TABLES LIKE '{$table}'");
    if ($check->num_rows === 0) {
        echo "  ⚠️  SKIP (tidak ada): {$table}\n";
        continue;
    }
    
    // Get row count before truncate
    $cnt = $db->query("SELECT COUNT(*) as c FROM `{$table}`");
    $row = $cnt->fetch_assoc();
    $count = $row['c'];

    if ($db->query("TRUNCATE TABLE `{$table}`")) {
        echo "  ✅ TRUNCATED: {$table} ({$count} baris dihapus)\n";
        $success++;
    } else {
        echo "  ❌ GAGAL: {$table} - {$db->error}\n";
        $failed++;
    }
}

// ──────────────────────────────────────────────────────────
// 2. DELETE: Data master (siswa, guru, kelas, mapel, jabatan)
// ──────────────────────────────────────────────────────────
echo "\n── FASE 2: TRUNCATE Tabel Master ──\n\n";

$master_tables = [
    'master_siswa',
    'master_guru',
    'master_kelas',
    'jabatan_guru',
];

foreach ($master_tables as $table) {
    $cnt = $db->query("SELECT COUNT(*) as c FROM `{$table}`");
    $row = $cnt->fetch_assoc();
    $count = $row['c'];

    if ($db->query("TRUNCATE TABLE `{$table}`")) {
        echo "  ✅ TRUNCATED: {$table} ({$count} baris dihapus)\n";
        $success++;
    } else {
        echo "  ❌ GAGAL: {$table} - {$db->error}\n";
        $failed++;
    }
}

// ──────────────────────────────────────────────────────────
// 3. DELETE: Akun users (pertahankan Admin ID=1)
// ──────────────────────────────────────────────────────────
echo "\n── FASE 3: Bersihkan Akun Users (pertahankan Admin) ──\n\n";

// Count users to be deleted
$cnt = $db->query("SELECT COUNT(*) as c FROM users WHERE id != 1");
$row = $cnt->fetch_assoc();
$user_count = $row['c'];

if ($db->query("DELETE FROM users WHERE id != 1")) {
    echo "  ✅ DELETE users: {$user_count} akun dummy dihapus (Admin ID=1 aman)\n";
    $success++;
} else {
    echo "  ❌ GAGAL DELETE users: {$db->error}\n";
    $failed++;
}

// Delete users_groups relation
$cnt = $db->query("SELECT COUNT(*) as c FROM users_groups WHERE user_id != 1");
$row = $cnt->fetch_assoc();
$ug_count = $row['c'];

if ($db->query("DELETE FROM users_groups WHERE user_id != 1")) {
    echo "  ✅ DELETE users_groups: {$ug_count} relasi dummy dihapus\n";
    $success++;
} else {
    echo "  ❌ GAGAL DELETE users_groups: {$db->error}\n";
    $failed++;
}

// Re-enable foreign key checks
$db->query("SET FOREIGN_KEY_CHECKS = 1");

// ──────────────────────────────────────────────────────────
// 4. LAPORAN AKHIR
// ──────────────────────────────────────────────────────────
echo "\n╔══════════════════════════════════════════════════════╗\n";
echo "║              LAPORAN FACTORY RESET                  ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
echo "║  ✅ Berhasil dibersihkan : {$success} tabel               ║\n";
echo "║  ❌ Gagal               : {$failed} tabel                ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
echo "║  TABEL YANG DIPERTAHANKAN (tidak disentuh):         ║\n";
echo "║  • setting (konfigurasi sekolah)                    ║\n";
echo "║  • groups (role admin/guru/siswa)                   ║\n";
echo "║  • master_tp (tahun pelajaran)                      ║\n";
echo "║  • master_smt (semester)                            ║\n";
echo "║  • master_mapel (mata pelajaran)                    ║\n";
echo "║  • master_jurusan (jurusan)                         ║\n";
echo "║  • master_kelompok_mapel (kelompok mapel)           ║\n";
echo "║  • master_ekstra (ekstrakurikuler)                  ║\n";
echo "║  • level_guru, level_kelas (level/jenjang)          ║\n";
echo "║  • hari, bulan (referensi waktu)                    ║\n";
echo "║  • cbt_jenis (jenis ujian)                          ║\n";
echo "║  • cbt_sesi, cbt_ruang (sesi & ruang ujian)         ║\n";
echo "║  • rapor_admin_setting (pengaturan rapor)           ║\n";
echo "║  • running_text (teks berjalan)                     ║\n";
echo "║  • absensi_setting_barcode (setting absensi)        ║\n";
echo "║  • migrations (versi database)                      ║\n";
echo "║  • users ID=1 (Admin Utama)                         ║\n";
echo "╚══════════════════════════════════════════════════════╝\n";

// Verify admin still exists
$admin = $db->query("SELECT id, username FROM users WHERE id = 1")->fetch_assoc();
if ($admin) {
    echo "\n🔐 Admin aman: ID={$admin['id']} | Username={$admin['username']}\n";
} else {
    echo "\n⚠️  PERINGATAN: Admin tidak ditemukan! Periksa manual!\n";
}

$db->close();
echo "\n✨ Factory Reset selesai. Database siap untuk produksi!\n";
