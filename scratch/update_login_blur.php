<?php
$file = 'C:\xampp\htdocs\garuda_cbt\application\views\auth\login.php';
$content = file_get_contents($file);

// 1. Reduce blur on wrapper from backdrop-blur-md to backdrop-blur-sm and keep bg-white/10
$content = str_replace(
    '<div class="w-full max-w-sm mx-auto mt-20 p-8 rounded-[2rem] bg-white/10 backdrop-blur-md border border-white/30 shadow-2xl relative z-10">',
    '<div class="w-full max-w-sm mx-auto mt-20 p-8 rounded-[2rem] bg-white/10 backdrop-blur-sm border border-white/30 shadow-2xl relative z-10">',
    $content
);

// 2. Reduce opacity on input fields from bg-white/60 to bg-white/20 and remove their internal blur
$content = str_replace(
    'bg-white/60 backdrop-blur-sm border border-white/50',
    'bg-white/20 border border-white/30',
    $content
);

file_put_contents($file, $content);
echo "Opacity and blur reduced significantly.\n";
