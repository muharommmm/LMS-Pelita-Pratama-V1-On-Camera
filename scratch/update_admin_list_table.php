<?php
$f = 'C:\xampp\htdocs\garuda_cbt\application\views\ebooks\admin_list.php';
$c = file_get_contents($f);

$old_text = "<?= \$ebook->class_id ? 'Kelas: ' . htmlspecialchars(\$ebook->nama_kelas) : 'Semua Kelas' ?>";
$new_text = "<?= \$ebook->class_id ? (strpos(\$ebook->class_id, ',') !== false ? 'Multi Kelas' : 'Kelas: ' . htmlspecialchars(\$ebook->nama_kelas)) : 'Semua Kelas' ?>";

$c = str_replace($old_text, $new_text, $c);
file_put_contents($f, $c);
echo "Updated class label in admin_list.php\n";
