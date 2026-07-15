<?php
$zip = new ZipArchive;
if ($zip->open('C:\xampp\htdocs\garuda_cbt.zip') === TRUE) {
    $content = $zip->getFromName('garuda_cbt/application/models/Kelas_model.php');
    if ($content !== false) {
        file_put_contents('C:\xampp\htdocs\garuda_cbt\scratch\Kelas_model_clean.php', $content);
        echo "Extracted successfully!\n";
    } else {
        echo "File not found in zip!\n";
    }
    $zip->close();
} else {
    echo "Failed to open zip!\n";
}
