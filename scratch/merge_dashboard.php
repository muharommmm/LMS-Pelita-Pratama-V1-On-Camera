<?php
$recovered = file_get_contents('C:\xampp\htdocs\garuda_cbt\scratch\dashboard_recovered.php');
$pos = strpos($recovered, "<!-- 4 PANEL WIDGET -->");
$bottom_part = substr($recovered, $pos);

// Fix some broken emoji artifacts that survived null byte stripping
$bottom_part = str_replace('dx } 0', '<i class="fas fa-check-circle text-indigo-300"></i>', $bottom_part);
$bottom_part = str_replace('?x  ', '<i class="fas fa-bell"></i>', $bottom_part);
$bottom_part = str_replace('?x a', '<i class="fas fa-book"></i>', $bottom_part);
$bottom_part = str_replace('?x ?', '<i class="fas fa-edit"></i>', $bottom_part);
$bottom_part = str_replace('?S', '<i class="fas fa-check-double"></i>', $bottom_part);
$bottom_part = str_replace('?x', '<i class="fas fa-bell"></i>', $bottom_part); // catch all

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

{$bottom_part}
HTML;

file_put_contents('C:\xampp\htdocs\garuda_cbt\application\views\members\guru\dashboard.php', $new_dashboard);
echo "Dashboard fully restored and combined with App Hub.\n";
