<?php
$file = 'C:\xampp\htdocs\garuda_cbt\application\views\members\guru\dashboard.php';
$content = file_get_contents($file);

// 1. Grid Wrapper
$content = str_replace('<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-12">', '<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start pb-12">', $content);

// 2. Fix the header of Jadwal Mengajar
// Current Jadwal Mengajar might have: <div class="card-header text-white"> ... <div class="card-title">...</div> </div>
$jadwal_search = '<div class="mb-4 border-b border-slate-100 pb-3">
                                <div class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">
                                    <i class="fas fa-chalkboard-teacher mr-1"></i> JADWAL MENGAJAR HARI INI
                                </div>
                            </div>';
// It might be using `card-header text-white` because my `fix_dashboard_encoding.php` might have missed it or it wasn't there exactly. Let's use preg_replace for safety.
$content = preg_replace(
    '/<div class="mb-4 border-b border-slate-100 pb-3">\s*<div class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2">\s*<i class="fas fa-chalkboard-teacher mr-1"><\/i> JADWAL MENGAJAR HARI INI\s*<\/div>\s*<\/div>/',
    '<div class="font-bold text-slate-700 uppercase tracking-wide text-sm border-b border-slate-100 pb-3 mb-4 flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-blue-500"></i> JADWAL MENGAJAR HARI INI</div>',
    $content
);

// If it has 'card-header text-white' from earlier backup version
$content = preg_replace(
    '/<div class="card-header text-white">\s*<div class="card-title">\s*<i class="fas fa-chalkboard-teacher mr-1"><\/i> JADWAL MENGAJAR HARI INI\s*<\/div>\s*<\/div>/',
    '<div class="font-bold text-slate-700 uppercase tracking-wide text-sm border-b border-slate-100 pb-3 mb-4 flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-blue-500"></i> JADWAL MENGAJAR HARI INI</div>',
    $content
);


// 3. Fix Agenda header
$content = preg_replace(
    '/<div class="mb-4 border-b border-slate-100 pb-3">\s*<div class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2"<i class="fas fa-calendar-alt text-indigo-500"><\/i> AGENDA TERDEKAT\s*<\/div>\s*<\/div>/',
    '<div class="font-bold text-slate-700 uppercase tracking-wide text-sm border-b border-slate-100 pb-3 mb-4 flex items-center gap-2"><i class="fas fa-calendar-alt text-indigo-500"></i> AGENDA TERDEKAT</div>',
    $content
);
$content = preg_replace(
    '/<div class="card-header">\s*<div class="card-title">AGENDA TERDEKAT<\/div>\s*<\/div>/',
    '<div class="font-bold text-slate-700 uppercase tracking-wide text-sm border-b border-slate-100 pb-3 mb-4 flex items-center gap-2"><i class="fas fa-calendar-alt text-indigo-500"></i> AGENDA TERDEKAT</div>',
    $content
);


// 4. Fix Pengumuman header
$content = preg_replace(
    '/<div class="mb-4 border-b border-slate-100 pb-3">\s*<h6 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2"><i class="fas fa-bullhorn text-yellow-500"><\/i> PENGUMUMAN<\/h6>\s*<\/div>/',
    '<div class="font-bold text-slate-700 uppercase tracking-wide text-sm border-b border-slate-100 pb-3 mb-4 flex items-center gap-2"><i class="fas fa-bullhorn text-yellow-500"></i> PENGUMUMAN</div>',
    $content
);
$content = preg_replace(
    '/<div class="card-header"><b>INFO\/PENGUMUMAN<\/b><\/div>/',
    '<div class="font-bold text-slate-700 uppercase tracking-wide text-sm border-b border-slate-100 pb-3 mb-4 flex items-center gap-2"><i class="fas fa-bullhorn text-yellow-500"></i> PENGUMUMAN</div>',
    $content
);


// 5. Activity Panel Icons
// Replace `dx     `
$content = preg_replace('/<div class="bg-indigo-100 text-indigo-600 rounded-full w-8 h-8 flex items-center justify-center text-sm">\s*dx     \s*<\/div>/', '<div class="bg-indigo-100 text-indigo-600 rounded-full w-8 h-8 flex items-center justify-center text-sm"><i class="fas fa-bolt text-indigo-500"></i></div>', $content);

// Replace `S    `
$content = preg_replace('/S\s+Tandai Semua Dibaca/', '<i class="fas fa-check-double"></i> Tandai Semua Dibaca', $content);

// Replace `dx } 0`
$content = preg_replace('/<div class="text-4xl mb-3 opacity-50">dx \} 0 <\/div>/', '<div class="text-4xl mb-3 opacity-50"><i class="fas fa-check-circle text-indigo-300"></i></div>', $content);


// 6. JS Replacements
// renderFeedItem icon
$content = preg_replace('/\'<div class="feed-icon">\' \+ \(item\.icon \|\| \'.*?\'\) \+ \'<\/div>\'/', '\'<div class="feed-icon">\' + (item.icon || \'<i class="fas fa-bell text-slate-400"></i>\') + \'</div>\'', $content);

// loadFeedGuru icon assignments
$content = preg_replace('/icon: item\.tipe === \'tugas\' \? \'.*?\' : \'.*?\'/', 'icon: item.tipe === \'tugas\' ? \'<i class="fas fa-book-open text-blue-500"></i>\' : \'<i class="fas fa-check-circle text-green-500"></i>\'', $content);


file_put_contents($file, $content);
echo "All requested fixes applied successfully.\n";
