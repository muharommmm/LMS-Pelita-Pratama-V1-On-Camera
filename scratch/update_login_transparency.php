<?php
$file = 'C:\xampp\htdocs\garuda_cbt\application\views\auth\login.php';
$content = file_get_contents($file);

// Replace bg-white/30 with bg-white/10 and border-white/40 with border-white/30
$search = '<div class="w-full max-w-sm mx-auto mt-20 p-8 rounded-[2rem] bg-white/30 backdrop-blur-md border border-white/40 shadow-2xl relative z-10">';
$replace = '<div class="w-full max-w-sm mx-auto mt-20 p-8 rounded-[2rem] bg-white/10 backdrop-blur-md border border-white/30 shadow-2xl relative z-10">';

$new_content = str_replace($search, $replace, $content);
file_put_contents($file, $new_content);

echo "Login card transparency updated.\n";
