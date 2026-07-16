<?php
$files = [
    '2.png' => '2.jpg',
    '3.png' => '3.jpg',
    '4.png' => '4.jpg'
];

$dir = 'c:/xampp/htdocs/garuda_cbt/assets/img/';

foreach ($files as $srcName => $dstName) {
    $srcPath = $dir . $srcName;
    $dstPath = $dir . $dstName;

    if (!file_exists($srcPath)) {
        echo "Source file $srcName not found.\n";
        continue;
    }

    echo "Compressing $srcName...\n";

    // 1. Create image resource from PNG
    $img = imagecreatefrompng($srcPath);
    if (!$img) {
        echo "Failed to load $srcName.\n";
        continue;
    }

    $width = imagesx($img);
    $height = imagesy($img);

    // 2. Determine new dimensions (max width 1920px)
    $newWidth = 1920;
    if ($width > $newWidth) {
        $newHeight = floor($height * ($newWidth / $width));
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }

    // 3. Create canvas and resize
    $canvas = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency if any, although backgrounds usually don't have it
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);
    
    imagecopyresampled($canvas, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // 4. Save as JPEG with quality 80
    if (imagejpeg($canvas, $dstPath, 80)) {
        $srcSize = filesize($srcPath);
        $dstSize = filesize($dstPath);
        echo "Successfully created $dstName.\n";
        echo "Original Size: " . number_format($srcSize / 1024 / 1024, 2) . " MB\n";
        echo "New Size: " . number_format($dstSize / 1024, 2) . " KB\n";
        echo "Compression ratio: " . number_format((1 - ($dstSize / $srcSize)) * 100, 2) . "%\n";
    } else {
        echo "Failed to save $dstName.\n";
    }

    // Free memory
    imagedestroy($img);
    imagedestroy($canvas);
    echo "---------------------------\n";
}
