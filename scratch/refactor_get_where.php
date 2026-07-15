<?php

function refactor_file($filepath) {
    if (!file_exists($filepath)) return;
    $content = file_get_contents($filepath);
    
    // We will use a regex that matches $this->db->get_where('table', [...])
    // Because arrays can have multiple lines, we use the `s` modifier and match balanced brackets.
    // However, a simple regex might fail on nested brackets. 
    // Let's use a simpler approach: replace `$this->db->get_where(` with a custom logic or careful regex.
    
    // The regex: \$this->db->get_where\(\s*([^,]+)\s*,\s*(\[(?:[^\[\]]|(?2))*\])\s*\)
    // (?2) is a recursive pattern for the second capturing group (the array).
    $pattern = '/\$this->db->get_where\(\s*([^,]+)\s*,\s*(\[(?:[^\[\]]|(?2))*\])\s*\)/s';
    
    $count = 0;
    $new_content = preg_replace_callback($pattern, function($matches) {
        $table = trim($matches[1]);
        $array = trim($matches[2]);
        return '$this->db->where(' . $array . ')->get(' . $table . ')';
    }, $content, -1, $count);
    
    // Also try array() syntax if any
    $pattern2 = '/\$this->db->get_where\(\s*([^,]+)\s*,\s*(array\((?:[^\(\)]|(?2))*\))\s*\)/s';
    $new_content = preg_replace_callback($pattern2, function($matches) {
        $table = trim($matches[1]);
        $array = trim($matches[2]);
        return '$this->db->where(' . $array . ')->get(' . $table . ')';
    }, $new_content, -1, $count2);
    
    if ($count > 0 || $count2 > 0) {
        file_put_contents($filepath, $new_content);
        echo "Updated $filepath (" . ($count + $count2) . " replacements)\n";
    }
}

$files = [
    'c:\xampp\htdocs\garuda_cbt\application\controllers\Absensi.php',
    'c:\xampp\htdocs\garuda_cbt\application\controllers\Honor.php',
    'c:\xampp\htdocs\garuda_cbt\application\controllers\Jadwal_fleksibel.php',
    'c:\xampp\htdocs\garuda_cbt\application\controllers\Kelasstatus.php',
    'c:\xampp\htdocs\garuda_cbt\application\controllers\Siswa.php',
    'c:\xampp\htdocs\garuda_cbt\application\models\Absensi_model.php',
    'c:\xampp\htdocs\garuda_cbt\application\models\Jadwal_fleksibel_model.php',
    'c:\xampp\htdocs\garuda_cbt\application\models\Spp_model.php',
];

foreach ($files as $file) {
    refactor_file($file);
}

echo "Done\n";
