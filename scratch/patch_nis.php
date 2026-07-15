<?php
$file = 'C:\xampp\htdocs\garuda_cbt\application\controllers\Datasiswa.php';

// Baca isi file secara mentah
$content = file_get_contents($file);

// Target pencarian (required|numeric|trim|min_length[6]|max_length[30])
$target = '\162\145\x71\x75\x69\x72\x65\144\174\156\165\155\145\162\x69\143\x7c\164\x72\x69\x6d\174\155\151\x6e\x5f\154\x65\156\147\164\150\133\x36\x5d\174\x6d\141\170\137\x6c\x65\x6e\x67\164\x68\x5b\x33\60\135';

// String pengganti (required|trim|min_length[6]|max_length[30])
$replacement = '\162\145\x71\x75\x69\x72\x65\144\x7c\164\x72\x69\x6d\174\155\151\x6e\x5f\154\x65\156\147\164\150\133\x36\x5d\174\x6d\141\170\137\x6c\x65\x6e\x67\164\x68\x5b\x33\60\135';

// Lakukan penggantian
if (strpos($content, $target) !== false) {
    $new_content = str_replace($target, $replacement, $content);
    
    // Tulis ulang file
    if (file_put_contents($file, $new_content)) {
        echo "✅ SUKSES: Parameter 'numeric' berhasil dihilangkan dari rantai validasi obfuskasi!\n";
        echo "DataSiswa.php sekarang menerima kombinasi NIS Alfanumerik.\n";
    } else {
        echo "❌ GAGAL: Tidak dapat menyimpan perubahan ke file Datasiswa.php. Periksa izin file.\n";
    }
} else {
    echo "⚠️ PERINGATAN: Target string sandi tidak ditemukan di dalam Datasiswa.php. Mungkin sudah pernah dipatch sebelumnya?\n";
}
