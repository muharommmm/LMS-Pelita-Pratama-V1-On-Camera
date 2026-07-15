<?php
$file = 'C:\xampp\htdocs\garuda_cbt\application\views\members\guru\dashboard.php';
$content = file_get_contents($file);

// Replace the main wrapper class to fix layout overflow
$search = '<main class="flex-1 mt-16 p-4 md:p-6 lg:ml-64 w-full overflow-x-hidden bg-slate-50 min-h-screen">';
$replace = '<main class="mt-16 lg:ml-64 p-4 md:p-6 w-full lg:w-[calc(100%-16rem)] overflow-x-hidden min-h-screen bg-slate-50">';

// Just in case it was written slightly differently
$content = str_replace($search, $replace, $content);

// Also try regex if exact string mismatch
if (strpos($content, $replace) === false) {
    $content = preg_replace(
        '/<main class="[^"]*w-full[^"]*lg:ml-64[^"]*">/',
        $replace,
        $content
    );
}

// Write back
file_put_contents($file, $content);
echo "Main wrapper layout fixed.\n";
