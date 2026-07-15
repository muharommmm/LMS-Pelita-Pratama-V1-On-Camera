<?php
$file = 'C:\xampp\htdocs\garuda_cbt\application\views\members\guru\dashboard.php';
$content = file_get_contents($file);

$old_block = <<<HTML
    <!-- APP HUB: Menu Pintasan -->
    <div class="mb-8 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <h6 class="text-sm font-bold text-slate-800 mb-5 uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-th-large text-indigo-500"></i> Menu Pintasan
        </h6>
        <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
            <?php 
            \$app_hub = [
                ['title' => 'Jadwal Mengajar', 'icon' => 'fas fa-calendar-alt', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50', 'link' => 'kelasjadwal'],
                ['title' => 'Materi', 'icon' => 'fas fa-pencil-ruler', 'color' => 'text-indigo-500', 'bg' => 'bg-indigo-50', 'link' => 'kelasmateri/materi'],
                ['title' => 'Tugas', 'icon' => 'fas fa-drafting-compass', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'link' => 'kelasmateri/tugas'],
                ['title' => 'Penilaian', 'icon' => 'far fa-clipboard', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'link' => 'kelasstatus'],
                ['title' => 'Modul E-Book', 'icon' => 'fas fa-book', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50', 'link' => 'ebooks'],
                ['title' => 'Honor', 'icon' => 'fas fa-hand-holding-usd', 'color' => 'text-green-600', 'bg' => 'bg-green-50', 'link' => 'honor'],
                ['title' => 'Absensi', 'icon' => 'fas fa-user-check', 'color' => 'text-teal-500', 'bg' => 'bg-teal-50', 'link' => 'absensi'],
                ['title' => 'Hasil Ujian', 'icon' => 'fas fa-file-alt', 'color' => 'text-cyan-500', 'bg' => 'bg-cyan-50', 'link' => 'cbtnilai'],
                ['title' => 'Bank Soal', 'icon' => 'far fa-folder-open', 'color' => 'text-orange-500', 'bg' => 'bg-orange-50', 'link' => 'cbtbanksoal'],
                ['title' => 'Jadwal Ujian', 'icon' => 'far fa-calendar-alt', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'link' => 'cbtjadwal'],
                ['title' => 'Chat Internal', 'icon' => 'fas fa-comments', 'color' => 'text-sky-500', 'bg' => 'bg-sky-50', 'link' => 'chat'],
                ['title' => 'Pengumuman', 'icon' => 'fas fa-bullhorn', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-50', 'link' => 'pengumuman'],
                ['title' => 'Logout', 'icon' => 'fas fa-sign-out-alt', 'color' => 'text-red-600', 'bg' => 'bg-red-50', 'link' => 'logout', 'is_danger' => true],
            ];
            foreach (\$app_hub as \$app) : 
                \$bg_hover = isset(\$app['is_danger']) ? 'hover:bg-red-50 border-red-100 hover:border-red-200' : 'hover:bg-slate-50 border-transparent hover:border-slate-200';
            ?>
            <a href="<?= base_url(\$app['link']) ?>" class="flex flex-col items-center justify-center bg-white border rounded-2xl p-3 shadow-sm transition-all duration-300 <?= \$bg_hover ?> hover:-translate-y-1 group">
                <div class="w-12 h-12 flex items-center justify-center rounded-xl <?= \$app['bg'] ?> group-hover:scale-110 transition-transform duration-300 mb-2">
                    <i class="<?= \$app['icon'] ?> text-2xl <?= \$app['color'] ?>"></i>
                </div>
                <span class="text-[10px] sm:text-xs font-semibold text-slate-600 text-center leading-tight <?php if(isset(\$app['is_danger'])) echo 'text-red-600'; ?>"><?= \$app['title'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
HTML;

// In case the encoding of the file changed due to powershell, we'll extract everything after the block.
$pos = strpos(mb_convert_encoding($content, 'UTF-8', 'UTF-8'), "<!-- APP HUB: Menu Pintasan -->");
$pos_end = strpos(mb_convert_encoding($content, 'UTF-8', 'UTF-8'), "<!-- 4 PANEL WIDGET -->");

$new_block = <<<HTML
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
    </div>\n
HTML;

$content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
$updated_content = substr($content, 0, $pos) . $new_block . substr($content, $pos_end);

// Force save as valid UTF-8 without BOM
file_put_contents($file, $updated_content);
echo "Replaced successfully!\n";
