<?php
$file = 'C:\xampp\htdocs\garuda_cbt\application\controllers\Datasiswa.php';
if (!file_exists($file)) die("File tidak ditemukan!");

$content = file_get_contents($file);

// Cari semua string dalam kutip ganda
preg_match_all('/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"/', $content, $matches);

$count = 0;
foreach ($matches[0] as $index => $full_match) {
    $inner_string = $matches[1][$index];
    $decoded = stripcslashes($inner_string); // Dekripsi octal/hex

    // Jika ini adalah string validasi CI
    if (strpos($decoded, 'numeric') !== false && (strpos($decoded, 'required') !== false || strpos($decoded, 'trim') !== false || strpos($decoded, 'min_length') !== false)) {

        // Hapus aturan numeric
        $new_rule = str_replace(['|numeric', 'numeric|'], '', $decoded);

        // Ganti di dalam file asli
        $content = str_replace($full_match, '"' . $new_rule . '"', $content);
        $count++;
    }
}

if ($count > 0) {
    file_put_contents($file, $content);
    echo "SUKSES! Ditemukan dan dibersihkan $count validasi 'numeric' tersembunyi.\n";
} else {
    echo "GAGAL/TIDAK DITEMUKAN. Format mungkin berbeda.\n";
}
?>
