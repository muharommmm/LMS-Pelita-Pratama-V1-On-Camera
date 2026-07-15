<?php
$c = file_get_contents('C:\xampp\htdocs\garuda_cbt\application\views\kelas\status\data.php');

// Replace arrKelasMateri loop
$c = preg_replace('/for\s*\(\s*let\s*j\s*=\s*0;\s*j\s*<\s*arrKelasMateri\[kelas\]\.length;\s*j\+\+\s*\)\s*\{[^\}]+\}/s', 
    'for (let j = 0; j < arrKelasMateri[kelas].length; j++) {
        var item = arrKelasMateri[kelas][j];
        var textOpsi = item.kode + " - " + (item.judul_materi || "Materi");
        dropMateri.append(\'<option value="\' + item.id_kjm + \'">\' + textOpsi + \'</option>\');
    }', $c);

// Replace arrKelasTugas loop
$c = preg_replace('/for\s*\(\s*let\s*k\s*=\s*0;\s*k\s*<\s*arrKelasTugas\[kelas\]\.length;\s*k\+\+\s*\)\s*\{[^\}]+\}/s', 
    'for (let k = 0; k < arrKelasTugas[kelas].length; k++) {
        var item = arrKelasTugas[kelas][k];
        var textOpsi = item.kode + " - " + (item.judul_materi || "Tugas");
        dropMateri.append(\'<option value="\' + item.id_kjm + \'">\' + textOpsi + \'</option>\');
    }', $c);

file_put_contents('C:\xampp\htdocs\garuda_cbt\application\views\kelas\status\data.php', $c);
echo "Regex replaced!\n";
