<?php
defined('BASEPATH') or exit('No direct script access allowed');

$arrBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$status_label = ['H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpha'];
$status_color = [
    'H' => 'bg-green-50 text-green-700 border-green-200',
    'S' => 'bg-blue-50 text-blue-700 border-blue-200',
    'I' => 'bg-amber-50 text-amber-700 border-amber-200',
    'A' => 'bg-red-50 text-red-700 border-red-200'
];

$foto_profil = $siswa->foto ? base_url($siswa->foto) : base_url('assets/img/siswa.png');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $judul ?> - Lumina Learning System</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#334779", // Midnight Navy
                        "secondary": "#855300", // Golden Ochre
                        "background": "#f7f9fb", // Neutral-50
                        "surface": "#ffffff",
                        "outline-variant": "#e0e3e5",
                        "on-background": "#191c1e",
                        "on-surface": "#191c1e",
                        "on-primary": "#ffffff",
                        "primary-container": "#dae2ff",
                        "on-primary-container": "#001847",
                        "on-surface-variant": "#464555",
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem", // 8px for buttons/inputs
                        "xl": "1rem",   // 16px for cards
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "1.5rem",
                        "container-padding-desktop": "2.5rem",
                        "stack-sm": "0.5rem",
                        "base-unit": "8px",
                        "container-padding-mobile": "1rem",
                        "stack-md": "1.5rem",
                        "stack-lg": "3rem"
                    },
                    "fontFamily": {
                        "headline": ["Plus Jakarta Sans"],
                        "body": ["Inter"],
                    },
                    "boxShadow": {
                        "lumina": "0px 4px 20px rgba(0,0,0,0.05)"
                    }
                }
            }
        }
    </script>
    <style>
        .lumina-card {
            background-color: #ffffff;
            border: 1px solid #e0e3e5;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.05);
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .lumina-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0px 8px 30px rgba(0,0,0,0.08);
        }
        body.menu-open {
            overflow: hidden;
        }
        #mobile-menu-overlay.active {
            visibility: visible;
            opacity: 1;
        }
        #mobile-menu-sheet.active {
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen font-body selection:bg-primary-container selection:text-on-primary-container">

<!-- Top Navigation (Desktop) -->
<header class="hidden md:flex justify-between items-center w-full px-container-padding-desktop max-w-[1280px] mx-auto h-16 bg-surface border-b border-outline-variant sticky top-0 z-40">
    <div class="font-headline text-xl font-bold text-primary"><?= $setting->nama_aplikasi ?></div>
    <nav class="flex items-center gap-8">
        <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-medium" href="<?= base_url('dashboard') ?>">Dashboard</a>
        <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-medium" href="<?= base_url('siswa/materi') ?>">Materi</a>
        <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-medium" href="<?= base_url('siswa/tugas') ?>">Tugas</a>
        <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-medium" href="<?= base_url('chat') ?>">Obrolan</a>
    </nav>
    <div class="flex items-center gap-4 text-primary">
        <span class="text-xs text-on-surface-variant font-semibold">TP: <?= $tp_active->tahun ?> (Smt: <?= $smt_active->smt ?>)</span>
        <a href="<?= base_url('logout') ?>" class="hover:bg-red-50 text-red-600 p-2 rounded-full transition-colors" title="Logout">
            <span class="material-symbols-outlined">logout</span>
        </a>
    </div>
</header>

<div class="flex max-w-[1280px] mx-auto w-full">
    <!-- Side Navigation (Desktop) -->
    <aside class="hidden lg:flex flex-col h-screen sticky top-16 w-[240px] bg-surface border-r border-outline-variant p-4 gap-2 z-30">
        <a class="flex items-center gap-3 text-on-surface-variant px-4 py-2.5 hover:bg-background rounded-lg text-sm font-medium transition-all" href="<?= base_url('dashboard') ?>">
            <span class="material-symbols-outlined">home</span> Home
        </a>
        <a class="flex items-center gap-3 text-on-surface-variant hover:bg-background rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('siswa/materi') ?>">
            <span class="material-symbols-outlined">school</span> Materi
        </a>
        <a class="flex items-center gap-3 text-on-surface-variant hover:bg-background rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('siswa/tugas') ?>">
            <span class="material-symbols-outlined">assignment</span> Tugas
        </a>
        <a class="flex items-center gap-3 text-on-surface-variant hover:bg-background rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('siswa/cbt') ?>">
            <span class="material-symbols-outlined">quiz</span> Ujian / CBT
        </a>
        <a class="flex items-center gap-3 bg-primary text-white font-semibold rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('siswa/kehadiran') ?>">
            <span class="material-symbols-outlined">person_check</span> Absensi
        </a>
        <div class="mt-auto pt-4 border-t border-outline-variant flex flex-col gap-2">
            <a class="flex items-center gap-3 text-on-surface-variant px-4 py-2.5 hover:bg-background rounded-lg text-sm font-medium transition-all" href="<?= base_url('chat') ?>">
                <span class="material-symbols-outlined">chat</span> Chat
            </a>
            <a class="flex items-center gap-3 text-red-600 px-4 py-2.5 hover:bg-red-50 rounded-lg text-sm font-medium transition-all" href="<?= base_url('logout') ?>">
                <span class="material-symbols-outlined">logout</span> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="flex-1 p-container-padding-mobile md:p-container-padding-desktop pb-24 lg:pb-container-padding-desktop">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header with Month/Year Filter -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold font-headline text-primary">Rekapitulasi Kehadiran</h2>
                    <p class="text-xs text-on-surface-variant mt-1">Daftar kehadiran tatap muka harian serta log aktivitas pembelajaran online Anda.</p>
                </div>
                <form method="GET" action="" class="flex gap-2 w-full sm:w-auto">
                    <select name="bulan" onchange="this.form.submit()" class="text-xs rounded-lg border-outline-variant bg-surface text-on-surface focus:border-primary focus:ring-primary">
                        <?php for ($m = 1; $m <= 12; $m++) : 
                            $m_pad = str_pad($m, 2, '0', STR_PAD_LEFT);
                        ?>
                            <option value="<?= $m_pad ?>" <?= $m_pad == $bulan ? 'selected' : '' ?>><?= $arrBulan[$m] ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="tahun" onchange="this.form.submit()" class="text-xs rounded-lg border-outline-variant bg-surface text-on-surface focus:border-primary focus:ring-primary">
                        <?php 
                        $cur_yr = date('Y');
                        for ($y = $cur_yr - 2; $y <= $cur_yr + 1; $y++) : ?>
                            <option value="<?= $y ?>" <?= $y == $tahun ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </form>
            </div>

            <!-- Kehadiran Hari Ini -->
            <section class="lumina-card overflow-hidden">
                <div class="p-5 border-b border-outline-variant flex justify-between items-center">
                    <h3 class="font-headline text-sm font-bold text-primary flex items-center gap-2 tracking-wide uppercase">
                        <span class="material-symbols-outlined text-xl">today</span> Kehadiran Hari Ini
                    </h3>
                    <span class="text-xs text-on-surface-variant font-medium"><?= date('l, d M Y') ?></span>
                </div>
                <div class="p-5">
                    <?php if (empty($jadwal_flex_today)) : ?>
                        <p class="text-center text-on-surface-variant text-sm py-4">Tidak ada jadwal KBM kelas hari ini.</p>
                    <?php else : ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="border-b border-outline-variant text-on-surface-variant">
                                        <th class="pb-3 font-semibold">Waktu</th>
                                        <th class="pb-3 font-semibold">Mata Pelajaran</th>
                                        <th class="pb-3 font-semibold text-center">Tatap Muka</th>
                                        <th class="pb-3 font-semibold text-center">Materi</th>
                                        <th class="pb-3 font-semibold text-center">Tugas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant">
                                    <?php foreach ($jadwal_flex_today as $jf) :
                                        $mapel_id = $jf->mapel_id;
                                        $tatap = isset($official_logs[$today][$mapel_id]) ? $official_logs[$today][$mapel_id] : null;
                                        $absenTatap = $tatap ? ($status_label[$tatap->status] ?? $tatap->status) : '-';

                                        $log_materi = isset($flex_logs[1][$today][$mapel_id]) ? $flex_logs[1][$today][$mapel_id] : null;
                                        $absenMateri = ($log_materi && !empty($log_materi->finish_time)) ? date('H:i', strtotime($log_materi->finish_time)) : '-';

                                        $log_tugas = isset($flex_logs[2][$today][$mapel_id]) ? $flex_logs[2][$today][$mapel_id] : null;
                                        $absenTugas = ($log_tugas && !empty($log_tugas->finish_time)) ? date('H:i', strtotime($log_tugas->finish_time)) : '-';
                                    ?>
                                        <tr class="hover:bg-background transition-colors">
                                            <td class="py-3 font-bold text-primary">
                                                <?= date('H:i', strtotime($jf->start_time)) ?> - <?= date('H:i', strtotime($jf->end_time)) ?>
                                            </td>
                                            <td class="py-3 text-on-surface"><?= htmlspecialchars($jf->nama_mapel) ?></td>
                                            <td class="py-3 text-center font-bold text-on-surface"><?= $absenTatap ?></td>
                                            <td class="py-3 text-center font-semibold text-primary"><?= $absenMateri ?></td>
                                            <td class="py-3 text-center font-semibold text-primary"><?= $absenTugas ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Detail Kehadiran Bulanan -->
            <section class="lumina-card overflow-hidden">
                <div class="p-5 border-b border-outline-variant">
                    <h3 class="font-headline text-sm font-bold text-primary flex items-center gap-2 tracking-wide uppercase">
                        <span class="material-symbols-outlined text-xl">calendar_month</span> Log Kehadiran Bulan Ini (<?= $arrBulan[(int)$bulan] ?>)
                    </h3>
                </div>
                <div class="p-5">
                    <?php if (empty($jadwals_flex)) : ?>
                        <p class="text-center text-on-surface-variant text-sm py-4">Jadwal pembelajaran belum dibuat.</p>
                    <?php else : ?>
                        <div class="space-y-4">
                            <?php
                            $is_current_month = ($tahun == date('Y') && $bulan == date('m'));
                            $tanggal_hari_ini = (int)date('d');
                            foreach ($arrtgl as $tgl) :
                                $tgl_skrg = $tahun . '-' . $bulan . '-' . $tgl;
                                $hari_n   = (int)date('N', strtotime($tgl_skrg));
                                $day_d    = (int)date('d', strtotime($tgl_skrg));
                                if ($hari_n === 7) continue;
                                if ($is_current_month && $day_d > $tanggal_hari_ini) continue;

                                $week    = (int)ceil($day_d / 7);
                                $is_odd  = ($week % 2 === 1);

                                $jadwal_hari = isset($jadwals_flex[$hari_n]) ? $jadwals_flex[$hari_n] : [];
                                $aktif = [];
                                foreach ($jadwal_hari as $jf) {
                                    if ($jf->pola_mingguan === 'Semua' ||
                                        ($jf->pola_mingguan === 'Ganjil' && $is_odd) ||
                                        ($jf->pola_mingguan === 'Genap' && !$is_odd)) {
                                        $aktif[] = $jf;
                                    }
                                }
                                if (empty($aktif)) continue;
                                $nama_hari = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                            ?>
                                <div class="border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                                    <div class="bg-primary/5 px-4 py-2 border-b border-outline-variant flex justify-between items-center text-xs">
                                        <span class="font-bold text-primary">
                                            <?= $nama_hari[$hari_n] ?>, <?= (int)$tgl ?> <?= $arrBulan[(int)$bulan] ?>
                                        </span>
                                        <span class="text-on-surface-variant">Minggu ke-<?= $week ?> (<?= $is_odd ? 'Ganjil' : 'Genap' ?>)</span>
                                    </div>
                                    <div class="p-3 divide-y divide-outline-variant">
                                        <?php foreach ($aktif as $jf) :
                                            $mapel_id = $jf->mapel_id;
                                            $tatap = isset($official_logs[$tgl_skrg][$mapel_id]) ? $official_logs[$tgl_skrg][$mapel_id] : null;
                                            $absenTatap = $tatap ? ($status_label[$tatap->status] ?? $tatap->status) : '-';
                                            $badge_class = $tatap ? ($status_color[$tatap->status] ?? '') : '';
                                        ?>
                                            <div class="py-2.5 flex justify-between items-center text-xs">
                                                <div>
                                                    <p class="font-semibold text-on-surface"><?= htmlspecialchars($jf->nama_mapel) ?></p>
                                                    <span class="text-on-surface-variant text-[10px]">
                                                        <?= date('H:i', strtotime($jf->start_time)) ?> - <?= date('H:i', strtotime($jf->end_time)) ?>
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <?php if ($tatap) : ?>
                                                        <span class="px-2 py-0.5 rounded border text-[10px] font-bold <?= $badge_class ?>">
                                                            <?= $absenTatap ?>
                                                        </span>
                                                    <?php else : ?>
                                                        <span class="text-on-surface-variant">-</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</div>

<!-- Bottom Menu Overlay -->
<div id="mobile-menu-overlay" onclick="toggleMobileMenu()" class="lg:hidden fixed inset-0 bg-black/50 z-50 invisible opacity-0 transition-all duration-300 backdrop-blur-sm"></div>

<!-- Bottom Sheet Mobile -->
<div id="mobile-menu-sheet" class="lg:hidden fixed bottom-0 left-0 right-0 bg-surface rounded-t-2xl z-50 transform translate-y-full transition-transform duration-300 pb-safe shadow-2xl max-w-md mx-auto max-h-[85vh] overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
    <div class="flex justify-center py-3" onclick="toggleMobileMenu()">
        <div class="w-12 h-1.5 bg-outline-variant rounded-full cursor-pointer"></div>
    </div>
    <div class="px-5 pb-3 border-b border-outline-variant flex justify-between items-center">
        <h4 class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Menu Utama</h4>
        <button onclick="toggleMobileMenu()" class="p-1 rounded-full hover:bg-background text-on-surface-variant">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>
    <div class="grid grid-cols-4 gap-4 p-5 pb-16">
        <a class="flex flex-col items-center text-center gap-2" href="<?= base_url('siswa/cbt') ?>">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                <span class="material-symbols-outlined text-xl">quiz</span>
            </div>
            <span class="text-[10px] font-semibold text-on-surface">Ujian</span>
        </a>
        <a class="flex flex-col items-center text-center gap-2" href="<?= base_url('siswa/kehadiran') ?>">
            <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600">
                <span class="material-symbols-outlined text-xl">person_check</span>
            </div>
            <span class="text-[10px] font-semibold text-on-surface">Absensi</span>
        </a>
        <a class="flex flex-col items-center text-center gap-2" href="<?= base_url('spp') ?>">
            <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined text-xl">payments</span>
            </div>
            <span class="text-[10px] font-semibold text-on-surface">SPP</span>
        </a>
        <a class="flex flex-col items-center text-center gap-2" href="<?= base_url('siswa/hasil') ?>">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                <span class="material-symbols-outlined text-xl">verified</span>
            </div>
            <span class="text-[10px] font-semibold text-on-surface">Nilai</span>
        </a>
        <a class="flex flex-col items-center text-center gap-2" href="<?= base_url('ebooks') ?>">
            <div class="w-10 h-10 rounded-lg bg-cyan-50 flex items-center justify-center text-cyan-600">
                <span class="material-symbols-outlined text-xl">library_books</span>
            </div>
            <span class="text-[10px] font-semibold text-on-surface">E-Book</span>
        </a>
        <a class="flex flex-col items-center text-center gap-2" href="<?= base_url('siswa/catatan') ?>">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-xl">rate_review</span>
            </div>
            <span class="text-[10px] font-semibold text-on-surface">Catatan</span>
        </a>
        <a class="flex flex-col items-center text-center gap-2" href="<?= base_url('chat') ?>">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-xl">chat</span>
            </div>
            <span class="text-[10px] font-semibold text-on-surface">Obrolan</span>
        </a>
        <a class="flex flex-col items-center text-center gap-2 text-red-600" href="<?= base_url('logout') ?>">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                <span class="material-symbols-outlined text-xl">logout</span>
            </div>
            <span class="text-[10px] font-semibold">Logout</span>
        </a>
    </div>
</div>

<!-- Bottom Navigation (Mobile) -->
<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-2 py-3 bg-surface border-t border-outline-variant shadow-lg pb-safe">
    <a class="flex flex-col items-center justify-center text-on-surface-variant" href="<?= base_url('dashboard') ?>">
        <span class="material-symbols-outlined">home</span>
        <span class="text-[10px] font-medium mt-1">Home</span>
    </a>
    <a class="flex flex-col items-center justify-center text-on-surface-variant" href="<?= base_url('siswa/materi') ?>">
        <span class="material-symbols-outlined">auto_stories</span>
        <span class="text-[10px] font-medium mt-1">Materi</span>
    </a>
    <a class="flex flex-col items-center justify-center text-on-surface-variant" href="<?= base_url('siswa/tugas') ?>">
        <span class="material-symbols-outlined">assignment</span>
        <span class="text-[10px] font-medium mt-1">Tugas</span>
    </a>
    <a class="flex flex-col items-center justify-center text-on-surface-variant" href="<?= base_url('chat') ?>">
        <span class="material-symbols-outlined">chat</span>
        <span class="text-[10px] font-medium mt-1">Chat</span>
    </a>
    <button onclick="toggleMobileMenu()" class="flex flex-col items-center justify-center text-on-surface-variant focus:outline-none">
        <span class="material-symbols-outlined">grid_view</span>
        <span class="text-[10px] font-medium mt-1">Lainnya</span>
    </button>
</nav>

<script>
    function toggleMobileMenu() {
        const overlay = document.getElementById('mobile-menu-overlay');
        const sheet = document.getElementById('mobile-menu-sheet');
        const body = document.body;

        if (sheet.classList.contains('active')) {
            sheet.classList.remove('active');
            sheet.classList.add('translate-y-full');
            overlay.classList.remove('active');
            overlay.classList.add('invisible', 'opacity-0');
            body.classList.remove('menu-open');
        } else {
            sheet.classList.remove('translate-y-full');
            sheet.classList.add('active');
            overlay.classList.remove('invisible', 'opacity-0');
            overlay.classList.add('active');
            body.classList.add('menu-open');
        }
    }
</script>

<style>
    /* iOS Safe Area Padding for Bottom Nav */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .pb-safe {
            padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));
        }
    }
</style>
</body>
</html>
