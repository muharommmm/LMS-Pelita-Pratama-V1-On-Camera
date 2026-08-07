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
    <script src="<?= base_url('assets/plugins/jquery/jquery.min.js') ?>"></script>
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
                <h2 class="text-2xl font-bold font-headline text-primary">Koleksi E-Book Digital</h2>
                <p class="text-xs text-on-surface-variant mt-1">Akses buku pelajaran, panduan ekstra, dan catatan materi untuk menunjang belajar Anda.</p>
            </div>

            <!-- Filters -->
            <?php 
            $available_mapels = [];
            $available_categories = [];
            if (!empty($ebooks)) {
                foreach ($ebooks as $eb) {
                    if ($eb->mapel_id) {
                        $available_mapels[$eb->mapel_id] = true;
                        $available_categories['mapel'] = true;
                    }
                    if ($eb->ekstra_id) {
                        $available_categories['ekskul'] = true;
                    }
                    if ($eb->custom_category) {
                        $available_categories['lainnya'] = true;
                    }
                }
            }
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="filter_mapel" class="text-xs font-bold text-on-surface-variant block mb-1">Mata Pelajaran</label>
                    <select id="filter_mapel" class="w-full py-2 px-3 border border-outline-variant rounded-lg text-xs focus:outline-none focus:border-primary">
                        <option value="">Semua Mapel</option>
                        <?php foreach ($mapels as $mapel) : ?>
                            <?php if (isset($available_mapels[$mapel->id_mapel])) : ?>
                                <option value="<?= $mapel->id_mapel ?>"><?= htmlspecialchars($mapel->nama_mapel) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="filter_kategori" class="text-xs font-bold text-on-surface-variant block mb-1">Kategori Buku</label>
                    <select id="filter_kategori" class="w-full py-2 px-3 border border-outline-variant rounded-lg text-xs focus:outline-none focus:border-primary">
                        <option value="">Semua Kategori</option>
                        <?php if (isset($available_categories['mapel'])) : ?><option value="mapel">Mata Pelajaran (Mapel)</option><?php endif; ?>
                        <?php if (isset($available_categories['ekskul'])) : ?><option value="ekskul">Ekstrakurikuler (Ekskul)</option><?php endif; ?>
                        <?php if (isset($available_categories['lainnya'])) : ?><option value="lainnya">Catatan Khusus (Kustom)</option><?php endif; ?>
                    </select>
                </div>
            </div>

            <!-- E-Book Grid List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="ebook-list-container">
                <?php if (!empty($ebooks)) : ?>
                    <?php foreach ($ebooks as $ebook) : 
                        $cat = $ebook->mapel_id ? 'mapel' : ($ebook->ekstra_id ? 'ekskul' : ($ebook->custom_category ? 'lainnya' : ''));
                    ?>
                        <div class="ebook-card lumina-card p-5 flex flex-col justify-between gap-4" 
                             data-mapel="<?= $ebook->mapel_id ?>" 
                             data-category="<?= $cat ?>">
                            <div class="space-y-2">
                                <div class="flex justify-between items-start gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary-container text-primary">
                                        <?= $ebook->class_id ? 'Kelas: ' . htmlspecialchars($ebook->nama_kelas) : 'Semua Kelas' ?>
                                    </span>
                                    <?php if ($ebook->mapel_id) : ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                                            <?= htmlspecialchars($ebook->nama_mapel) ?>
                                        </span>
                                    <?php elseif ($ebook->ekstra_id) : ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200">
                                            Ekskul: <?= htmlspecialchars($ebook->nama_ekstra) ?>
                                        </span>
                                    <?php elseif ($ebook->custom_category) : ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <?= htmlspecialchars($ebook->custom_category) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h4 class="font-headline font-bold text-base text-primary leading-snug"><?= htmlspecialchars($ebook->title) ?></h4>
                                <p class="text-xs text-on-surface-variant">Oleh: <?= htmlspecialchars($ebook->first_name . ' ' . $ebook->last_name) ?></p>

                                <?php if ($ebook->last_page) : ?>
                                    <div class="pt-2 text-[10px] text-on-surface-variant">
                                        <div class="flex justify-between font-bold mb-1">
                                            <span>Terakhir Dibaca: Hal. <?= $ebook->last_page ?> / <?= $ebook->total_pages ?></span>
                                            <span><?= round(($ebook->last_page / $ebook->total_pages) * 100) ?>%</span>
                                        </div>
                                        <div class="w-full bg-outline-variant h-1 rounded-full overflow-hidden">
                                            <div class="bg-green-600 h-full" style="width: <?= ($ebook->last_page / $ebook->total_pages) * 100 ?>%"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex gap-2">
                                <?php if (pathinfo($ebook->file_path, PATHINFO_EXTENSION) === 'pdf') : ?>
                                    <a href="<?= base_url('ebooks/view/' . $ebook->id_ebook) ?>" target="_blank" class="flex-1 py-2 border border-primary text-primary hover:bg-primary/5 text-center text-xs font-semibold rounded-lg transition-colors">
                                        Baca Online
                                    </a>
                                <?php endif; ?>
                                <a href="<?= base_url($ebook->file_path) ?>" target="_blank" class="flex-1 py-2 bg-primary hover:bg-primary/90 text-white text-center text-xs font-semibold rounded-lg transition-colors">
                                    Download
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="col-span-2 lumina-card p-6 text-center text-on-surface-variant text-sm">Belum ada e-book yang tersedia.</div>
                <?php endif; ?>
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
    $(document).ready(function() {
        $('#filter_mapel, #filter_kategori').on('change', function() {
            var selectedMapel = $('#filter_mapel').val();
            var selectedCategory = $('#filter_kategori').val();

            $('.ebook-card').each(function() {
                var rowMapel = $(this).data('mapel');
                var rowCategory = $(this).data('category');

                var matchMapel = !selectedMapel || rowMapel == selectedMapel;
                var matchCategory = !selectedCategory || rowCategory == selectedCategory;

                if (matchMapel && matchCategory) {
                    $(this).removeClass('hidden');
                } else {
                    $(this).addClass('hidden');
                }
            });
        });
    });

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
