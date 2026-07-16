<?php
$files = ['2.png', '3.png', '4.png'];
foreach ($files as $f) {
    $path = 'c:/xampp/htdocs/garuda_cbt/assets/img/' . $f;
    if (file_exists($path)) {
        $size = filesize($path);
        $mtime = filemtime($path);
        $info = getimagesize($path);
        echo "File: $f\n";
        echo "Size: " . number_format($size / 1024 / 1024, 2) . " MB ($size bytes)\n";
        echo "Last Modified: " . date("Y-m-d H:i:s", $mtime) . "\n";
        if ($info) {
            echo "Dimension: {$info[0]}x{$info[1]}\n";
            echo "Mime: {$info['mime']}\n";
        } else {
            echo "Not a valid image or unable to read dimensions.\n";
        }
        echo "---------------------------\n";
    } else {
        echo "File $f not found.\n";
    }
}
