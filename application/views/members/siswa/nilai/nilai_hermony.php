<?php
defined('BASEPATH') or exit('No direct script access allowed');

$foto_profil = $siswa->foto ? base_url($siswa->foto) : base_url('assets/img/siswa.png');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $judul ?> - Pelita LMS</title>
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
            <!-- Header -->
            <div>
                <h2 class="text-2xl font-bold font-headline text-primary">Nilai Hasil Evaluasi</h2>
                <p class="text-xs text-on-surface-variant mt-1">Pantau perkembangan nilai tugas, materi, dan hasil ujian Anda secara berkala.</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex border-b border-outline-variant gap-4 text-sm font-medium">
                <button onclick="switchTab('materi')" id="tab-btn-materi" class="tab-link pb-3 border-b-2 border-primary text-primary font-semibold">
                    Nilai Materi (<?= count($nilai_materi) ?>)
                </button>
                <button onclick="switchTab('tugas')" id="tab-btn-tugas" class="tab-link pb-3 border-b-2 border-transparent text-on-surface-variant">
                    Nilai Tugas (<?= count($nilai_tugas) ?>)
                </button>
                <button onclick="switchTab('ujian')" id="tab-btn-ujian" class="tab-link pb-3 border-b-2 border-transparent text-on-surface-variant">
                    Hasil Ujian (<?= count($jadwal) ?>)
                </button>
            </div>

            <div class="space-y-4">
                <!-- TAB 1: NILAI HASIL MATERI -->
                <div id="nilai-materi" class="nilai-tab-content space-y-4">
                    <?php if (empty($nilai_materi)) : ?>
                        <div class="lumina-card p-6 text-center text-on-surface-variant text-sm">Belum ada nilai materi.</div>
                    <?php else : ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($nilai_materi as $nil) : ?>
                                <div class="lumina-card p-5 flex justify-between items-center gap-4">
                                    <div class="space-y-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary-container text-primary">
                                            <?= htmlspecialchars($nil->kode) ?>
                                        </span>
                                        <h4 class="font-headline font-bold text-sm text-on-surface leading-tight mt-1"><?= htmlspecialchars($nil->judul_materi) ?></h4>
                                        <p class="text-xs text-on-surface-variant">Tanggal: <?= date('d M Y', strtotime($nil->jadwal_materi)) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-bold text-primary font-headline"><?= $nil->nilai ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB 2: NILAI HASIL TUGAS -->
                <div id="nilai-tugas" class="nilai-tab-content hidden space-y-4">
                    <?php if (empty($nilai_tugas)) : ?>
                        <div class="lumina-card p-6 text-center text-on-surface-variant text-sm">Belum ada nilai tugas.</div>
                    <?php else : ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($nilai_tugas as $nil) : 
                                $is_redo_val = ($nil->nilai !== null && ((string)$nil->nilai === '0' || (float)$nil->nilai === 0.0));
                            ?>
                                <div class="lumina-card p-5 flex justify-between items-center gap-4 <?= $is_redo_val ? 'border-amber-300 bg-amber-50/30' : '' ?>">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                                <?= htmlspecialchars($nil->kode) ?>
                                            </span>
                                            <?php if ($is_redo_val) : ?>
                                                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    Perlu Diulang
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="font-headline font-bold text-sm text-on-surface leading-tight mt-1"><?= htmlspecialchars($nil->judul_materi) ?></h4>
                                        <p class="text-xs text-on-surface-variant">Tanggal: <?= date('d M Y', strtotime($nil->jadwal_materi)) ?></p>
                                        <?php if ($is_redo_val && !empty($nil->catatan)) : ?>
                                            <p class="text-[11px] text-amber-800 italic mt-1"><i class="fas fa-info-circle mr-1"></i>Catatan: <?= htmlspecialchars($nil->catatan) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right flex flex-col items-end gap-1">
                                        <span class="text-2xl font-bold <?= $is_redo_val ? 'text-amber-600' : 'text-indigo-600' ?> font-headline"><?= $nil->nilai ?></span>
                                        <?php if ($is_redo_val) : 
                                            $target_task_id = !empty($nil->id_materi) ? $nil->id_materi : (!empty($nil->id_materi_log) ? $nil->id_materi_log : (!empty($nil->id_kjm) ? $nil->id_kjm : ''));
                                        ?>
                                            <a href="<?= base_url('siswa/bukatugas/' . $target_task_id . '/0') ?>" class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-100 hover:bg-amber-200 px-2 py-1 rounded transition-colors">
                                                <span class="material-symbols-outlined text-xs">edit</span> Ulangi
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- TAB 3: HASIL UJIAN -->
                <div id="nilai-ujian" class="nilai-tab-content hidden space-y-4">
                    <?php if (empty($jadwal)) : ?>
                        <div class="lumina-card p-6 text-center text-on-surface-variant text-sm">Belum ada hasil ujian.</div>
                    <?php else : ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($jadwal as $j) : 
                                $hanya_pg = $j->tampil_pg > 0 && $j->tampil_kompleks == 0 && $j->tampil_jodohkan == 0 && $j->tampil_isian == 0 && $j->tampil_esai == 0;
                                $total = !$hanya_pg && isset($skor[$j->id_jadwal]->dikoreksi) && $skor[$j->id_jadwal]->dikoreksi == 0 ? '*' : ($j->hasil_tampil == '0' ? '**' : $skor[$j->id_jadwal]->skor_total);
                            ?>
                                <div class="lumina-card p-5 flex flex-col justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex justify-between items-center text-[10px] font-bold text-on-surface-variant">
                                            <span><?= htmlspecialchars($j->nama_jenis) ?></span>
                                            <span><?= date('d M Y', strtotime($j->tgl_mulai)) ?></span>
                                        </div>
                                        <h4 class="font-headline font-bold text-base text-primary leading-tight mt-1"><?= htmlspecialchars($j->nama_mapel) ?></h4>
                                        <p class="text-xs text-on-surface-variant">Kode Bank: <?= htmlspecialchars($j->bank_kode) ?></p>
                                    </div>
                                    <div class="flex justify-between items-center border-t border-outline-variant pt-3">
                                        <span class="text-xs text-on-surface-variant">Skor Akhir:</span>
                                        <span class="text-xl font-bold text-red-600 font-headline"><?= $total ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="lumina-card p-4 bg-primary-container/10 border border-primary/20 text-xs text-on-surface-variant space-y-1">
                            <p><b>Catatan:</b></p>
                            <p>(-) Belum dikerjakan</p>
                            <p>(*) Menunggu hasil koreksi</p>
                            <p>(**) Hubungi Guru Pengampu jika ingin mengetahui nilai</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
    function switchTab(tabId) {
        document.querySelectorAll('.nilai-tab-content').forEach(el => {
            el.classList.add('hidden');
        });
        document.getElementById('nilai-' + tabId).classList.remove('hidden');

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
