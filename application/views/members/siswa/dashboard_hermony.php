<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Fetch helper models
$CI =& get_instance();
$CI->load->model('Agenda_model', 'agenda_helper');
$CI->load->model('Jadwal_fleksibel_model', 'jf_helper');
$CI->load->model('Dashboard_model', 'dashboard_helper');
$CI->load->model('Post_model', 'post_helper');

$active_agendas = $CI->agenda_helper->get_agendas_by_role('siswa', $siswa->id_kelas);

$php_day = date('w'); 
$db_day = ($php_day == 0) ? 7 : $php_day;
$flex_schedules = $CI->jf_helper->get_schedules_by_class($siswa->id_kelas, $tp_active->id_tp, $smt_active->id_smt, $db_day);

// Query post table for announcements
$pengumumans = $CI->post_helper->getPostForUser("'%siswa%'", "'%" . $siswa->kode_kelas . "%'");
$foto_profil = $siswa->foto ? base_url($siswa->foto) : base_url('assets/img/siswa.png');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Student Dashboard - Lumina Learning System</title>
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
        .header-bg {
            background-color: #334779;
            position: relative;
            overflow: hidden;
        }
        .header-pattern {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            opacity: 0.05;
            background-image: radial-gradient(#ffffff 1px, transparent 1px);
            background-size: 24px 24px;
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
        <a class="text-primary border-b-2 border-primary pb-1 font-semibold text-sm" href="<?= base_url('dashboard') ?>">Dashboard</a>
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
        <a class="flex items-center gap-3 bg-primary text-white rounded-lg px-4 py-2.5 text-sm font-semibold transition-all" href="<?= base_url('dashboard') ?>">
            <span class="material-symbols-outlined">home</span> Home
        </a>
        <a class="flex items-center gap-3 text-on-surface-variant px-4 py-2.5 hover:bg-background rounded-lg text-sm font-medium transition-all" href="<?= base_url('siswa/materi') ?>">
            <span class="material-symbols-outlined">school</span> Materi
        </a>
        <a class="flex items-center gap-3 text-on-surface-variant px-4 py-2.5 hover:bg-background rounded-lg text-sm font-medium transition-all" href="<?= base_url('siswa/tugas') ?>">
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
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- 1. Greeting Card (Top) -->
            <section class="header-bg rounded-xl p-8 text-on-primary shadow-lumina relative flex flex-col md:flex-row items-center gap-8">
                <div class="header-pattern"></div>
                <div class="relative z-10 w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white/20 overflow-hidden flex-shrink-0">
                    <img alt="Profile Picture" class="w-full h-full object-cover" src="<?= $foto_profil ?>" onerror="this.src='<?= base_url('assets/img/siswa.png') ?>'"/>
                </div>
                <div class="relative z-10 text-center md:text-left space-y-1">
                    <h1 class="font-headline text-2xl md:text-3xl font-bold">Halo, <?= htmlspecialchars($siswa->nama) ?></h1>
                    <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4 text-white/80 text-sm">
                        <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]">badge</span> NIS: <?= htmlspecialchars($siswa->nis) ?></span>
                        <span class="hidden md:inline text-white/40">•</span>
                        <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]">school</span> Kelas: <?= htmlspecialchars($siswa->nama_kelas) ?></span>
                    </div>
                </div>
            </section>

            <!-- ============================================================
                 ACTIVITY FEED — SISWA
                 Real-time panel: tugas belum kumpul, materi baru, chat, deadline
                 Auto-refresh 30 detik via GET notifikasi/siswa
                 ============================================================ -->
            <section id="siswa-activity-panel" class="lumina-card overflow-hidden">
                <!-- Header -->
                <div class="p-4 flex justify-between items-center"
                     style="background: linear-gradient(135deg, #334779, #1a237e);">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🔔</span>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wide" style="letter-spacing:.6px;">
                            Aktivitas & Pengingat
                        </h3>
                        <span id="siswa-feed-badge"
                              class="hidden text-xs bg-red-500 text-white rounded-full px-2 py-0.5 font-semibold"
                              style="font-size:.65rem;">0</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="siswa-feed-time" class="text-xs text-white/50"></span>
                        <button id="siswa-btn-baca-semua"
                                class="hidden text-xs border border-white/40 text-white/80 px-3 py-1 rounded-full hover:bg-white/10 transition-colors">
                            ✓ Tandai Dibaca
                        </button>
                        <button id="siswa-btn-toggle" class="text-white/70 hover:text-white transition-colors ml-1">
                            <span class="material-symbols-outlined text-base" id="siswa-feed-chevron">expand_less</span>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div id="siswa-feed-body">
                    <!-- Loading -->
                    <div id="siswa-feed-loading" class="flex justify-center items-center py-4">
                        <svg class="animate-spin h-4 w-4 text-primary mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-sm text-on-surface-variant">Memuat aktivitas...</span>
                    </div>

                    <!-- Items -->
                    <div id="siswa-feed-items" class="hidden divide-y divide-outline-variant" style="max-height:300px; overflow-y:auto;"></div>

                    <!-- Empty -->
                    <div id="siswa-feed-empty" class="hidden text-center py-6">
                        <span class="text-3xl">✅</span>
                        <p class="text-sm text-on-surface-variant mt-1">Semua beres! Tidak ada tugas mendesak hari ini.</p>
                    </div>
                </div>
            </section>

            <!-- Feed styles (Tailwind compatible) -->
            <style>
                .siswa-feed-item {
                    display: flex; align-items: flex-start;
                    padding: 12px 20px; transition: background .15s;
                    text-decoration: none; cursor: pointer;
                }
                .siswa-feed-item:hover { background: #f7f9fb; }
                .siswa-feed-item.read { opacity: .55; }
                .siswa-feed-icon {
                    font-size: 1.4rem; margin-right: 12px; flex-shrink: 0;
                    width: 40px; height: 40px; border-radius: 50%;
                    display: flex; align-items: center; justify-content: center;
                    background: #eef1ff;
                }
                .siswa-feed-meta { flex: 1; min-width: 0; }
                .siswa-feed-title {
                    font-weight: 700; font-size: .82rem; color: #334779;
                    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
                }
                .siswa-feed-body { font-size: .73rem; color: #666; margin: 2px 0 0; }
                .siswa-feed-age  { font-size: .66rem; color: #aaa; margin-top: 2px; }
                .siswa-feed-badge-item {
                    font-size: .65rem; padding: 2px 8px; border-radius: 20px;
                    font-weight: 700; margin-left: 8px; flex-shrink: 0;
                    align-self: flex-start; margin-top: 2px;
                }
                .bg-danger-soft  { background: #fce4ec; color: #c62828; }
                .bg-warning-soft { background: #fff8e1; color: #e65100; }
                .bg-info-soft    { background: #e3f2fd; color: #1565c0; }
                .bg-success-soft { background: #e8f5e9; color: #2e7d32; }
            </style>

            <!-- Jadwal Kelas Hari Ini -->
            <section class="lumina-card border-l-[6px] border-l-secondary overflow-hidden">
                <div class="p-5 border-b border-outline-variant flex justify-between items-center">
                    <h3 class="font-headline text-sm font-bold text-secondary flex items-center gap-2 tracking-wide uppercase">
                        <span class="material-symbols-outlined text-xl">calendar_today</span> Jadwal Kelas Hari Ini
                    </h3>
                    <span class="text-xs text-on-surface-variant font-medium"><?= date('l, d M Y') ?></span>
                </div>
                <div class="p-5">
                    <?php if (!empty($flex_schedules)) : ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="border-b border-outline-variant text-on-surface-variant">
                                        <th class="pb-3 font-semibold">Waktu</th>
                                        <th class="pb-3 font-semibold">Mata Pelajaran</th>
                                        <th class="pb-3 font-semibold">Guru</th>
                                        <th class="pb-3 font-semibold text-right">Aksi / Link</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant">
                                    <?php foreach ($flex_schedules as $row) : ?>
                                        <tr class="hover:bg-background transition-colors">
                                            <td class="py-3 font-bold text-primary">
                                                <?= date('H:i', strtotime($row->start_time)) ?> - <?= date('H:i', strtotime($row->end_time)) ?>
                                            </td>
                                            <td class="py-3 text-on-surface"><?= htmlspecialchars($row->nama_mapel) ?></td>
                                            <td class="py-3 text-on-surface-variant"><?= htmlspecialchars($row->nama_guru) ?></td>
                                            <td class="py-3 text-right">
                                                <?php if ($row->jenis_kegiatan == 'Tugas') : ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                        📝 Tugas Mandiri
                                                    </span>
                                                <?php elseif (!empty($row->learning_link)) : ?>
                                                    <a href="<?= htmlspecialchars($row->learning_link) ?>" target="_blank" class="inline-flex items-center gap-1 bg-teal-600 hover:bg-teal-700 text-white px-3 py-1 rounded text-xs font-semibold transition-colors">
                                                        <span class="material-symbols-outlined text-[14px]">open_in_new</span> Masuk Kelas
                                                    </a>
                                                <?php else : ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                                        🏢 Kelas Offline
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-center text-on-surface-variant text-sm py-4">Tidak ada jadwal kelas hari ini.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 2. Announcements & Agenda (Middle) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                <!-- PENGUMUMAN -->
                <?php if (!empty($pengumumans)) : ?>
                <section class="lumina-card border-l-[6px] border-l-primary overflow-hidden">
                    <div class="p-5 border-b border-outline-variant">
                        <h3 class="font-headline text-sm font-bold text-primary flex items-center gap-2 tracking-wide uppercase">
                            <span class="material-symbols-outlined text-xl">campaign</span> Pengumuman
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                            <?php 
                            // Slice to get the first 3 announcements/posts
                            $limited_posts = array_slice($pengumumans, 0, 3);
                            foreach ($limited_posts as $item) : 
                            ?>
                                <div class="border-b border-outline-variant pb-3 last:border-0 last:pb-0 space-y-1">
                                    <p class="text-sm font-semibold text-on-surface line-clamp-2"><?= strip_tags($item->text ?? '') ?></p>
                                    <div class="flex items-center justify-between text-xs text-on-surface-variant">
                                        <span>Oleh: <?= htmlspecialchars($item->nama_guru ?? 'Admin') ?></span>
                                        <span><?= date('d M Y', strtotime($item->tanggal ?? '')) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- AGENDA TERDEKAT -->
                <?php if (!empty($active_agendas)) : ?>
                <section class="lumina-card border-l-[6px] border-l-primary overflow-hidden">
                    <div class="p-5 border-b border-outline-variant">
                        <h3 class="font-headline text-sm font-bold text-primary flex items-center gap-2 tracking-wide uppercase">
                            <span class="material-symbols-outlined text-xl">event</span> Agenda Terdekat
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                            <?php foreach ($active_agendas as $agenda) : ?>
                                <div class="flex items-start gap-3">
                                    <?php 
                                    $is_expired = strtotime($agenda->end_date) < time();
                                    $bullet_color = $is_expired ? 'bg-red-500' : 'bg-secondary';
                                    ?>
                                    <div class="w-2 h-2 rounded-full <?= $bullet_color ?> mt-1.5 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($agenda->title) ?></p>
                                        <span class="text-xs text-on-surface-variant">
                                            <?= date('d M Y H:i', strtotime($agenda->start_date)) ?> s.d. <?= date('d M Y H:i', strtotime($agenda->end_date)) ?>
                                        </span>
                                        <?php if (!empty($agenda->description)) : ?>
                                            <p class="text-xs text-on-surface-variant mt-1"><?= htmlspecialchars($agenda->description) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            </div>

            <!-- 3. Menu Sections (Bottom) -->
            <div class="space-y-8">
                <!-- AKADEMIK -->
                <section class="space-y-4">
                    <h2 class="font-headline text-sm font-bold text-on-surface-variant tracking-widest uppercase">Akademik</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('jadwal_fleksibel/siswa') ?>">
                            <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">calendar_today</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">Jadwal Pembelajaran</span>
                        </a>
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('siswa/kehadiran') ?>">
                            <div class="w-12 h-12 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">person_check</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">Absensi</span>
                        </a>
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('siswa/materi') ?>">
                            <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">auto_stories</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">Materi</span>
                        </a>
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('siswa/catatan') ?>">
                            <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">rate_review</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">Catatan Guru</span>
                        </a>
                    </div>
                </section>

                <!-- EVALUASI -->
                <section class="space-y-4">
                    <h2 class="font-headline text-sm font-bold text-on-surface-variant tracking-widest uppercase">Evaluasi</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('siswa/cbt') ?>">
                            <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">quiz</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">Ujian / Ulangan</span>
                        </a>
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('siswa/tugas') ?>">
                            <div class="w-12 h-12 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">assignment</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">Tugas</span>
                        </a>
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('siswa/hasil') ?>">
                            <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">verified</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">Nilai Hasil</span>
                        </a>
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('ebooks') ?>">
                            <div class="w-12 h-12 rounded-lg bg-cyan-50 flex items-center justify-center text-cyan-600">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">library_books</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">E-Book</span>
                        </a>
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('spp') ?>">
                            <div class="w-12 h-12 rounded-lg bg-yellow-50 flex items-center justify-center text-secondary">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">payments</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">SPP</span>
                        </a>
                        <a class="lumina-card lumina-card-hover p-4 flex flex-col items-center text-center gap-3" href="<?= base_url('chat') ?>">
                            <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">chat</span>
                            </div>
                            <span class="text-xs font-semibold text-on-surface">Obrolan</span>
                        </a>
                    </div>
                </section>
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
    <a class="flex flex-col items-center justify-center text-primary" href="<?= base_url('dashboard') ?>">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">home</span>
        <span class="text-[10px] font-bold mt-1">Home</span>
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

    // ==========================================================
    // ACTIVITY FEED — SISWA
    // Polling setiap 30 detik ke GET notifikasi/siswa
    // ==========================================================
    var baseUrl = '<?= base_url() ?>';
    var siswaFeedCollapsed = false;
    var siswaFeedInterval = null;

    function getBadgeClass(color) {
        var map = {
            'danger': 'bg-danger-soft',
            'warning': 'bg-warning-soft',
            'info': 'bg-info-soft',
            'success': 'bg-success-soft',
        };
        return map[color] || 'bg-info-soft';
    }

    function getIconBg(color) {
        var map = {
            'danger': '#fce4ec',
            'warning': '#fff8e1',
            'info': '#e3f2fd',
            'success': '#e8f5e9',
            'primary': '#eef1ff',
        };
        return map[color] || '#eef1ff';
    }

        function renderSiswaFeedItem(item) {
        var href   = item.url ? baseUrl + item.url : '#';
        var idAttr = item.id ? 'data-notif-id="' + item.id + '"' : '';
        var readCls = item.is_read ? 'read' : '';
        var badgeHtml = item.badge
            ? '<span class="siswa-feed-badge-item ' + getBadgeClass(item.color) + '">' + item.badge + '</span>'
            : '';

        var dismissBtn = item.id ? '<button class="absolute top-2 right-2 text-slate-300 hover:text-red-500 z-10 dismiss-notif-btn transition-colors" data-id="' + item.id + '" title="Hapus Notifikasi"><i class="fas fa-times"></i></button>' : '';

        return '<div class="relative siswa-feed-item-wrapper group">' + dismissBtn + 
        '<a href="' + href + '" class="siswa-feed-item ' + readCls + '" ' + idAttr + '>' +
            '<div class="siswa-feed-icon" style="background:' + getIconBg(item.color) + '">' + (item.icon || '🔔') + '</div>' +
            '<div class="siswa-feed-meta pr-6">' +
                '<div class="flex items-start">' +
                    '<span class="siswa-feed-title">' + (item.title || '') + '</span>' +
                    badgeHtml +
                '</div>' +
                (item.body ? '<p class="siswa-feed-body">' + item.body + '</p>' : '') +
                (item.age_label ? '<div class="siswa-feed-age">' + item.age_label + '</div>' : '') +
            '</div>' +
            '<span class="material-symbols-outlined text-sm text-gray-300 self-center ml-2">chevron_right</span>' +
        '</a></div>';
    }

    function loadSiswaFeed() {
        fetch(baseUrl + 'notifikasi/siswa')
            .then(function(r) { return r.json(); })
            .then(function(res) {
                document.getElementById('siswa-feed-loading').classList.add('hidden');

                if (!res || !res.items) return;
                var items = res.items;
                var unread = res.unread || 0;

                // Badge
                var badge = document.getElementById('siswa-feed-badge');
                var btnBaca = document.getElementById('siswa-btn-baca-semua');
                if (unread > 0) {
                    badge.textContent = unread;
                    badge.classList.remove('hidden');
                    btnBaca.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                    btnBaca.classList.add('hidden');
                }

                // Timestamp
                document.getElementById('siswa-feed-time').textContent = res.generated ? 'Update: ' + res.generated : '';

                var container = document.getElementById('siswa-feed-items');
                var empty     = document.getElementById('siswa-feed-empty');
                if (items.length === 0) {
                    container.classList.add('hidden');
                    empty.classList.remove('hidden');
                } else {
                    empty.classList.add('hidden');
                    container.innerHTML = items.map(renderSiswaFeedItem).join('');
                    container.classList.remove('hidden');
                }

                // Click: mark stored notif as read
                container.querySelectorAll('.siswa-feed-item[data-notif-id]').forEach(function(el) {
                    el.addEventListener('click', function(e) {
                        var nid = this.dataset.notifId;
                        if (!nid) return;
                        e.preventDefault();
                        var targetHref = this.href;
                        fetch(baseUrl + 'notifikasi/baca', {
                            method: 'POST',
                            headers: {'Content-Type':'application/x-www-form-urlencoded'},
                            body: 'id=' + nid
                        }).then(function() {
                            if (targetHref && targetHref !== '#' && !targetHref.endsWith('#')) {
                                window.location.href = targetHref;
                            }
                        }).catch(function() {
                            if (targetHref && targetHref !== '#' && !targetHref.endsWith('#')) {
                                window.location.href = targetHref;
                            }
                        });
                        this.classList.add('read');
                    });
                });

                // Dismiss Button Event Listener
                container.querySelectorAll('.dismiss-notif-btn').forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var nid = this.getAttribute('data-id');
                        var card = this.closest('.siswa-feed-item-wrapper');
                        if (!nid) return;

                        fetch(baseUrl + 'siswa/dismiss_notifikasi', {
                            method: 'POST',
                            headers: {'Content-Type':'application/x-www-form-urlencoded'},
                            body: 'id_notifikasi=' + nid
                        }).then(function() {
                            card.style.transition = 'opacity 0.3s ease';
                            card.style.opacity = '0';
                            setTimeout(function() {
                                card.remove();
                                var unreadSpan = document.getElementById('siswa-feed-badge');
                                var currentUnread = parseInt(unreadSpan.textContent) || 0;
                                if(currentUnread > 1) {
                                    unreadSpan.textContent = currentUnread - 1;
                                } else {
                                    unreadSpan.classList.add('hidden');
                                }
                            }, 300);
                        });
                    });
                });
            })
            .catch(function() {
                document.getElementById('siswa-feed-loading').classList.add('hidden');
                document.getElementById('siswa-feed-items').innerHTML =
                    '<p class="text-sm text-center text-gray-400 py-3">Gagal memuat aktivitas.</p>';
                document.getElementById('siswa-feed-items').classList.remove('hidden');
            });
    }

    // Mark All Read
    document.getElementById('siswa-btn-baca-semua').addEventListener('click', function() {
        fetch(baseUrl + 'notifikasi/bacasemua', {method:'POST'}).then(function() {
            document.getElementById('siswa-feed-badge').classList.add('hidden');
            document.getElementById('siswa-btn-baca-semua').classList.add('hidden');
            document.querySelectorAll('.siswa-feed-item').forEach(function(el) { el.classList.add('read'); });
        });
    });

    // Toggle collapse
    document.getElementById('siswa-btn-toggle').addEventListener('click', function() {
        siswaFeedCollapsed = !siswaFeedCollapsed;
        var body = document.getElementById('siswa-feed-body');
        var chevron = document.getElementById('siswa-feed-chevron');
        if (siswaFeedCollapsed) {
            body.style.display = 'none';
            chevron.textContent = 'expand_more';
        } else {
            body.style.display = '';
            chevron.textContent = 'expand_less';
        }
    });

    // Load on start + interval
    loadSiswaFeed();
    siswaFeedInterval = setInterval(loadSiswaFeed, 30000);

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
