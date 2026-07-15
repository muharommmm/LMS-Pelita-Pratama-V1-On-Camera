<?php
defined('BASEPATH') or exit('No direct script access allowed');

$tugas_aktif = [];
$tugas_terlewat = [];
$tugas_riwayat = [];
$now = time();

foreach ($materis as $task) {
    $log = isset($logs[$task->id_kjm]) ? $logs[$task->id_kjm] : null;
    $is_completed = ($log != null && $log->finish_time != null);
    
    if ($is_completed) {
        $tugas_riwayat[] = [
            'task' => $task,
            'log' => $log
        ];
    } else {
        $is_missed = (!empty($task->deadline) && strtotime($task->deadline) < $now);
        if ($is_missed) {
            $tugas_terlewat[] = [
                'task' => $task,
                'log' => $log
            ];
        } else {
            $tugas_aktif[] = [
                'task' => $task,
                'log' => $log
            ];
        }
    }
}

$theme_color = $jenis == "1" ? "success" : "indigo";
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
        <a class="<?= $jenis == '1' ? 'text-primary border-b-2 border-primary pb-1 font-semibold' : 'text-on-surface-variant hover:text-primary transition-colors font-medium' ?> text-sm" href="<?= base_url('siswa/materi') ?>">Materi</a>
        <a class="<?= $jenis == '2' ? 'text-primary border-b-2 border-primary pb-1 font-semibold' : 'text-on-surface-variant hover:text-primary transition-colors font-medium' ?> text-sm" href="<?= base_url('siswa/tugas') ?>">Tugas</a>
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
        <a class="flex items-center gap-3 <?= $jenis == '1' ? 'bg-primary text-white font-semibold' : 'text-on-surface-variant hover:bg-background' ?> rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('siswa/materi') ?>">
            <span class="material-symbols-outlined">school</span> Materi
        </a>
        <a class="flex items-center gap-3 <?= $jenis == '2' ? 'bg-primary text-white font-semibold' : 'text-on-surface-variant hover:bg-background' ?> rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('siswa/tugas') ?>">
            <span class="material-symbols-outlined">assignment</span> Tugas
        </a>
        <a class="flex items-center gap-3 text-on-surface-variant px-4 py-2.5 hover:bg-background rounded-lg text-sm font-medium transition-all" href="<?= base_url('siswa/cbt') ?>">
            <span class="material-symbols-outlined">quiz</span> Ujian / CBT
        </a>
        <a class="flex items-center gap-3 text-on-surface-variant px-4 py-2.5 hover:bg-background rounded-lg text-sm font-medium transition-all" href="<?= base_url('siswa/kehadiran') ?>">
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
            <!-- Title Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold font-headline text-primary"><?= $jenis == "1" ? "Materi Pembelajaran" : "Tugas Mandiri" ?></h2>
                    <p class="text-xs text-on-surface-variant mt-1">Kelola dan pelajari modul serta tugas sekolah Anda dengan mudah.</p>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex border-b border-outline-variant gap-4 text-sm font-medium">
                <button onclick="switchTab('aktif')" id="tab-btn-aktif" class="tab-link pb-3 border-b-2 border-primary text-primary font-semibold">
                    <?= $jenis == "1" ? "Materi" : "Tugas" ?> Aktif (<?= count($tugas_aktif) ?>)
                </button>
                <button onclick="switchTab('terlewat')" id="tab-btn-terlewat" class="tab-link pb-3 border-b-2 border-transparent text-on-surface-variant">
                    Terlewat (<?= count($tugas_terlewat) ?>)
                </button>
                <button onclick="switchTab('riwayat')" id="tab-btn-riwayat" class="tab-link pb-3 border-b-2 border-transparent text-on-surface-variant">
                    Riwayat Selesai (<?= count($tugas_riwayat) ?>)
                </button>
            </div>

            <!-- Tab Contents -->
            <div class="space-y-4">

                <!-- TAB: AKTIF -->
                <div id="materi-aktif" class="materi-tab-content space-y-4">
                    <?php if (empty($tugas_aktif)) : ?>
                        <div class="lumina-card p-6 text-center text-on-surface-variant text-sm">
                            <span class="material-symbols-outlined text-4xl block text-on-surface-variant mb-2">info</span>
                            Tidak ada <?= $subjudul ?> aktif untuk saat ini.
                        </div>
                    <?php else : ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($tugas_aktif as $item) : 
                                $task = $item['task'];
                                $log = $item['log'];
                                $start_date = !empty($task->tgl_mulai) ? $task->tgl_mulai : (!empty($task->jadwal_materi) ? $task->jadwal_materi . ' 00:00:00' : '');
                                $start_date_formatted = !empty($start_date) ? date('d M Y H:i', strtotime($start_date)) : '-';
                                $deadline_formatted = !empty($task->deadline) ? date('d M Y H:i', strtotime($task->deadline)) : 'Tanpa Batas Waktu';
                                
                                $time_left = !empty($task->deadline) ? strtotime($task->deadline) - time() : 99999999;
                                $almost_expired = ($time_left > 0 && $time_left <= 12 * 3600);
                            ?>
                                <div class="lumina-card p-5 flex flex-col justify-between gap-4">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary-container text-primary">
                                                <?= htmlspecialchars($task->nama_mapel) ?>
                                            </span>
                                            <?php if ($almost_expired) : ?>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 animate-pulse">
                                                    Sisa <?= round($time_left / 3600, 1) ?> Jam!
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="font-headline font-bold text-base text-primary leading-snug"><?= htmlspecialchars($task->judul_materi) ?></h4>
                                        <div class="text-xs text-on-surface-variant space-y-1">
                                            <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">person</span> <?= htmlspecialchars($task->nama_guru) ?></p>
                                            <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">calendar_today</span> Mulai: <?= $start_date_formatted ?></p>
                                            <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">alarm</span> Batas: <?= $deadline_formatted ?></p>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('siswa/buka' . ($jenis == "1" ? "materi" : "tugas") . '/' . $task->id_kjm . '/1') ?>" class="w-full py-2 bg-primary hover:bg-primary/90 text-white font-semibold text-center text-xs rounded-lg transition-colors block">
                                        Buka <?= $jenis == "1" ? "Materi" : "Tugas" ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB: TERLEWAT -->
                <div id="materi-terlewat" class="materi-tab-content hidden space-y-4">
                    <?php if (empty($tugas_terlewat)) : ?>
                        <div class="lumina-card p-6 text-center text-on-surface-variant text-sm">
                            <span class="material-symbols-outlined text-4xl block text-green-600 mb-2">check_circle</span>
                            Keren! Tidak ada <?= $subjudul ?> yang terlewatkan.
                        </div>
                    <?php else : ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($tugas_terlewat as $item) : 
                                $task = $item['task'];
                                $start_date = !empty($task->tgl_mulai) ? $task->tgl_mulai : (!empty($task->jadwal_materi) ? $task->jadwal_materi . ' 00:00:00' : '');
                                $start_date_formatted = !empty($start_date) ? date('d M Y H:i', strtotime($start_date)) : '-';
                                $deadline_formatted = !empty($task->deadline) ? date('d M Y H:i', strtotime($task->deadline)) : 'Tanpa Batas Waktu';
                            ?>
                                <div class="lumina-card p-5 border-l-[6px] border-l-red-500 flex flex-col justify-between gap-4">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                                                <?= htmlspecialchars($task->nama_mapel) ?>
                                            </span>
                                            <span class="text-xs text-red-600 font-bold">Terlewat</span>
                                        </div>
                                        <h4 class="font-headline font-bold text-base text-primary leading-snug"><?= htmlspecialchars($task->judul_materi) ?></h4>
                                        <div class="text-xs text-on-surface-variant space-y-1">
                                            <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">person</span> <?= htmlspecialchars($task->nama_guru) ?></p>
                                            <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">calendar_today</span> Mulai: <?= $start_date_formatted ?></p>
                                            <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">alarm</span> Batas: <?= $deadline_formatted ?></p>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('siswa/buka' . ($jenis == "1" ? "materi" : "tugas") . '/' . $task->id_kjm . '/1') ?>" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-center text-xs rounded-lg transition-colors block">
                                        Buka <?= $jenis == "1" ? "Materi" : "Tugas" ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB: RIWAYAT -->
                <div id="materi-riwayat" class="materi-tab-content hidden space-y-4">
                    <?php if (empty($tugas_riwayat)) : ?>
                        <div class="lumina-card p-6 text-center text-on-surface-variant text-sm">
                            <span class="material-symbols-outlined text-4xl block text-on-surface-variant mb-2">history</span>
                            Belum ada riwayat pengerjaan <?= $subjudul ?>.
                        </div>
                    <?php else : ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($tugas_riwayat as $item) : 
                                $task = $item['task'];
                                $log = $item['log'];
                            ?>
                                <div class="lumina-card p-5 border-l-[6px] border-l-green-500 flex flex-col justify-between gap-4">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-start">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">
                                                <?= htmlspecialchars($task->nama_mapel) ?>
                                            </span>
                                            <span class="text-xs text-green-600 font-bold">Selesai</span>
                                        </div>
                                        <h4 class="font-headline font-semibold text-base text-primary leading-snug"><?= htmlspecialchars($task->judul_materi) ?></h4>
                                        <div class="text-xs text-on-surface-variant space-y-1">
                                            <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">person</span> <?= htmlspecialchars($task->nama_guru) ?></p>
                                            <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">check_circle</span> Selesai: <?= date('d M Y H:i', strtotime($log->finish_time)) ?></p>
                                        </div>
                                    </div>
                                    <a href="<?= base_url('siswa/buka' . ($jenis == "1" ? "materi" : "tugas") . '/' . $task->id_kjm . '/1') ?>" class="w-full py-2 bg-background hover:bg-outline-variant text-on-surface font-semibold text-center text-xs rounded-lg transition-colors border border-outline-variant block">
                                        Lihat Materi
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>
</div>

<!-- Bottom Menu Overlay (Backdrop) -->
<div id="mobile-menu-overlay" onclick="toggleMobileMenu()" class="lg:hidden fixed inset-0 bg-black/50 z-50 invisible opacity-0 transition-all duration-300 backdrop-blur-sm"></div>

<!-- Bottom Sheet (Laci Menu Mobile) -->
<div id="mobile-menu-sheet" class="lg:hidden fixed bottom-0 left-0 right-0 bg-surface rounded-t-2xl z-50 transform translate-y-full transition-transform duration-300 pb-safe shadow-2xl max-w-md mx-auto max-h-[85vh] overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
    <!-- Indicator Bar -->
    <div class="flex justify-center py-3" onclick="toggleMobileMenu()">
        <div class="w-12 h-1.5 bg-outline-variant rounded-full cursor-pointer"></div>
    </div>
    <!-- Sheet Title -->
    <div class="px-5 pb-3 border-b border-outline-variant flex justify-between items-center">
        <h4 class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Menu Utama</h4>
        <button onclick="toggleMobileMenu()" class="p-1 rounded-full hover:bg-background text-on-surface-variant">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>
    <!-- Menu Grid -->
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
    <a class="flex flex-col items-center justify-center <?= $jenis == '1' ? 'text-primary' : 'text-on-surface-variant' ?>" href="<?= base_url('siswa/materi') ?>">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?= $jenis == '1' ? '1' : '0' ?>;">auto_stories</span>
        <span class="text-[10px] font-semibold mt-1">Materi</span>
    </a>
    <a class="flex flex-col items-center justify-center <?= $jenis == '2' ? 'text-primary' : 'text-on-surface-variant' ?>" href="<?= base_url('siswa/tugas') ?>">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?= $jenis == '2' ? '1' : '0' ?>;">assignment</span>
        <span class="text-[10px] font-semibold mt-1">Tugas</span>
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
    function switchTab(tabId) {
        document.querySelectorAll('.materi-tab-content').forEach(el => {
            el.classList.add('hidden');
        });
        document.getElementById('materi-' + tabId).classList.remove('hidden');

        document.querySelectorAll('.tab-link').forEach(btn => {
            btn.classList.remove('border-primary', 'text-primary', 'font-semibold');
            btn.classList.add('border-transparent', 'text-on-surface-variant');
        });
        
        const activeBtn = document.getElementById('tab-btn-' + tabId);
        activeBtn.classList.remove('border-transparent', 'text-on-surface-variant');
        activeBtn.classList.add('border-primary', 'text-primary', 'font-semibold');
    }

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
