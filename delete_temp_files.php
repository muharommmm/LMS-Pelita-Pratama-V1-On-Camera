<?php
$files_to_delete = [
    'hello.php',
    'test_count.php',
    'test_db.php',
    'test_db_columns.php',
    'test_db_columns_new.php',
    'test_db_pdo.php',
    'hermony_preview.html',
    'application/controllers/Check_absensi_schema.php',
    'application/controllers/Check_cbt_schema.php',
    'application/controllers/TestApi.php',
    'application/controllers/TestNotif.php',
    'application/controllers/Test_ajax.php',
    'application/controllers/Test_methods.php'
];

echo "<h3>Pembersihan File LMS V1</h3><ul>";
foreach ($files_to_delete as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        if (unlink($full_path)) {
            echo "<li style='color: green;'>Berhasil dihapus: $file</li>";
        } else {
            echo "<li style='color: red;'>Gagal menghapus: $file</li>";
        }
    } else {
        echo "<li style='color: gray;'>File tidak ditemukan (sudah terhapus): $file</li>";
    }
}
echo "</ul>";

// Self-destruct
unlink(__FILE__);
echo "<p><b>Proses pembersihan selesai. Script ini telah menghapus dirinya sendiri demi keamanan.</b></p>";
