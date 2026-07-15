<?php
$backup_file = 'C:\xampp\htdocs\garuda_cbt\scratch\dashboard_backup.php';
$content = file_get_contents($backup_file);

// Remove UTF-16 null bytes if they exist
$content = str_replace("\0", "", $content);
// Also convert to UTF-8 properly if it has a BOM or something, but usually stripping \0 is enough for basic ASCII/Latin
// Let's be safe and convert from UTF-16LE to UTF-8 if we detect it
if (substr($content, 0, 2) === "\xFF\xFE") {
    $content = mb_convert_encoding(substr($content, 2), "UTF-8", "UTF-16LE");
} else {
    // Just in case it's a mix, let's strip nulls
    $content = str_replace("\0", "", $content);
}

$lines = explode("\n", str_replace("\r", "", $content));

// Helper to extract lines
function get_lines($lines, $start, $end) {
    return implode("\n", array_slice($lines, $start - 1, $end - $start + 1));
}

// Ensure the lines are clean
$activity_feed = get_lines($lines, 24, 101);
$agenda = get_lines($lines, 212, 239);
$jadwal_mengajar = get_lines($lines, 242, 304);
$pengumuman = get_lines($lines, 428, 444);
$modals = get_lines($lines, 450, 510);
$scripts = get_lines($lines, 512, 1094);

// Transform the extracted components to match the "Hermony" Tailwind style
// 1. Agenda
$agenda = str_replace(
    ['class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-3"', 'class="card-header"', 'class="card-title"', 'class="card-body p-2"'],
    ['class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 h-full flex flex-col"', 'class="mb-4 border-b border-slate-100 pb-3"', 'class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2"<i class="fas fa-calendar-alt text-indigo-500"></i> AGENDA TERDEKAT', 'class="flex-1 overflow-y-auto"'],
    $agenda
);
$agenda = str_replace('class="list-group list-group-flush small"', 'class="space-y-3"', $agenda);
$agenda = preg_replace('/class="list-group-item p-2"/', 'class="bg-slate-50 rounded-xl p-3 border border-slate-100 hover:border-indigo-200 transition-colors"', $agenda);

// 2. Jadwal Mengajar
$jadwal_mengajar = str_replace(
    ['class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-3"', 'class="card-header text-white"', 'class="card-title"', 'class="card-body p-2"'],
    ['class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 h-full flex flex-col"', 'class="mb-4 border-b border-slate-100 pb-3"', 'class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2"', 'class="flex-1 overflow-y-auto"'],
    $jadwal_mengajar
);
$jadwal_mengajar = str_replace('class="w-full text-sm text-left [&_tr]:border-b [&_tr]:border-slate-100"', 'class="w-full text-sm text-left"', $jadwal_mengajar);
$jadwal_mengajar = preg_replace('/<thead>\s*<tr>\s*<th>(.*?)<\/th>\s*<th>(.*?)<\/th>\s*<th>(.*?)<\/th>\s*<th class="text-center">(.*?)<\/th>\s*<\/tr>\s*<\/thead>/s', '<thead><tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-100"><th class="pb-3 font-semibold">$1</th><th class="pb-3 font-semibold">$2</th><th class="pb-3 font-semibold">$3</th><th class="pb-3 font-semibold text-center">$4</th></tr></thead>', $jadwal_mengajar);
$jadwal_mengajar = str_replace('<tr>', '<tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">', $jadwal_mengajar);
$jadwal_mengajar = str_replace('class="align-middle font-weight-bold text-teal"', 'class="py-3 align-middle font-bold text-teal-600"', $jadwal_mengajar);
$jadwal_mengajar = str_replace('class="align-middle"', 'class="py-3 align-middle text-slate-700"', $jadwal_mengajar);

// 3. Pengumuman
$pengumuman = str_replace(
    ['class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-3"', 'class="card-header"', '<b>INFO/PENGUMUMAN</b>', 'class="card-body"'],
    ['class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 h-full flex flex-col"', 'class="mb-4 border-b border-slate-100 pb-3"', '<h6 class="text-sm font-bold text-slate-800 tracking-tight flex items-center gap-2"><i class="fas fa-bullhorn text-yellow-500"></i> PENGUMUMAN</h6>', 'class="flex-1 overflow-y-auto"'],
    $pengumuman
);

// New layout for App Hub as requested
$new_dashboard = <<<HTML
<!-- Content Wrapper. Contains page content -->
<main class="flex-1 mt-16 p-4 md:p-6 lg:ml-64 w-full overflow-x-hidden bg-slate-50 min-h-screen">
    
    <!-- APP HUB: Menu Pintasan -->
    <div class="mb-8 bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-slate-100">
        <h6 class="text-sm font-bold text-slate-800 mb-2 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-th-large text-indigo-500"></i> Menu Pintasan
        </h6>
        
        <?php 
        \$hub_categories = [
            'Akademik & E-Learning' => [
                ['title' => 'Jadwal Mengajar', 'icon' => 'fas fa-calendar-alt', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50', 'link' => 'kelasjadwal'],
                ['title' => 'Materi', 'icon' => 'fas fa-pencil-ruler', 'color' => 'text-indigo-500', 'bg' => 'bg-indigo-50', 'link' => 'kelasmateri/materi'],
                ['title' => 'Tugas', 'icon' => 'fas fa-drafting-compass', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'link' => 'kelasmateri/tugas'],
                ['title' => 'Penilaian Harian', 'icon' => 'far fa-clipboard', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'link' => 'kelasstatus'],
                ['title' => 'Modul E-Book', 'icon' => 'fas fa-book', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50', 'link' => 'ebooks'],
            ],
            'Evaluasi & Ujian' => [
                ['title' => 'Bank Soal', 'icon' => 'far fa-folder-open', 'color' => 'text-orange-500', 'bg' => 'bg-orange-50', 'link' => 'cbtbanksoal'],
                ['title' => 'Jadwal Ujian', 'icon' => 'far fa-calendar-alt', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'link' => 'cbtjadwal'],
                ['title' => 'Hasil Ujian Siswa', 'icon' => 'fas fa-file-alt', 'color' => 'text-cyan-500', 'bg' => 'bg-cyan-50', 'link' => 'cbtnilai'],
            ],
            'Administrasi Tutor' => [
                ['title' => 'Input Absensi', 'icon' => 'fas fa-user-check', 'color' => 'text-teal-500', 'bg' => 'bg-teal-50', 'link' => 'absensi'],
                ['title' => 'Laporan Honor', 'icon' => 'fas fa-hand-holding-usd', 'color' => 'text-green-600', 'bg' => 'bg-green-50', 'link' => 'honor'],
            ],
            'Komunikasi & Sistem' => [
                ['title' => 'Pengumuman', 'icon' => 'fas fa-bullhorn', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-50', 'link' => 'pengumuman'],
                ['title' => 'Chat Internal', 'icon' => 'fas fa-comments', 'color' => 'text-sky-500', 'bg' => 'bg-sky-50', 'link' => 'chat'],
                ['title' => 'Logout', 'icon' => 'fas fa-sign-out-alt', 'color' => 'text-red-600', 'bg' => 'bg-red-50', 'link' => 'logout', 'is_danger' => true],
            ]
        ];

        foreach (\$hub_categories as \$category_name => \$apps) : 
        ?>
            <h6 class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 mt-6"><?= \$category_name ?></h6>
            <div class="grid grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-3 lg:gap-4">
                <?php 
                foreach (\$apps as \$app) : 
                    \$bg_hover = isset(\$app['is_danger']) ? 'hover:bg-red-50 border-red-100 hover:border-red-200' : 'hover:bg-slate-50 border-transparent hover:border-slate-200';
                ?>
                <a href="<?= base_url(\$app['link']) ?>" class="flex flex-col items-center justify-center bg-white border rounded-xl p-2 md:p-3 shadow-sm transition-all duration-300 <?= \$bg_hover ?> hover:-translate-y-1 group">
                    <div class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-xl <?= \$app['bg'] ?> group-hover:scale-110 transition-transform duration-300 mb-2">
                        <i class="<?= \$app['icon'] ?> text-xl md:text-2xl <?= \$app['color'] ?>"></i>
                    </div>
                    <span class="text-[10px] md:text-xs font-semibold text-slate-600 text-center leading-tight <?php if(isset(\$app['is_danger'])) echo 'text-red-600'; ?>"><?= \$app['title'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 4 PANEL WIDGET -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-12">
        
        <!-- PANEL 1: AKTIVITAS & PENGINGAT -->
        {$activity_feed}

        <!-- PANEL 2: JADWAL HARI INI -->
        {$jadwal_mengajar}

        <!-- PANEL 3: AGENDA TERDEKAT -->
        {$agenda}

        <!-- PANEL 4: PENGUMUMAN -->
        {$pengumuman}

    </div>

</main>

<!-- Modals -->
{$modals}

<!-- Scripts -->
{$scripts}
HTML;

file_put_contents('C:\xampp\htdocs\garuda_cbt\application\views\members\guru\dashboard.php', $new_dashboard);
echo "Fixed encoding and rewrote dashboard.php.\n";
