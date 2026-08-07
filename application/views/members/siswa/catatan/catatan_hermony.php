<?php
defined('BASEPATH') or exit('No direct script access allowed');

$level_labels = [
    '1' => 'Saran',
    '2' => 'Teguran',
    '3' => 'Peringatan',
    '4' => 'Sangsi'
];

$level_colors = [
    '1' => 'bg-green-50 text-green-700 border-green-200',
    '2' => 'bg-amber-50 text-amber-700 border-amber-200',
    '3' => 'bg-pink-50 text-pink-700 border-pink-200',
    '4' => 'bg-red-50 text-red-700 border-red-200'
];

$level_dots = [
    '1' => 'bg-green-500',
    '2' => 'bg-amber-500',
    '3' => 'bg-pink-500',
    '4' => 'bg-red-500'
];

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
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .lumina-card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.08);
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
    <script src="<?= base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript">
        let base_url = '<?= base_url() ?>';
    </script>
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
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header -->
            <div>
                <h2 class="text-2xl font-bold font-headline text-primary"><?= $subjudul ?></h2>
                <p class="text-xs text-on-surface-variant mt-1">Daftar catatan perilaku, bimbingan, dan perkembangan siswa</p>
            </div>

            <!-- Content Split Pane Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                
                <!-- Left Pane: Notes List (5 cols) -->
                <div class="md:col-span-5 space-y-3">
                    <h3 class="font-headline text-xs font-bold text-primary uppercase tracking-wider mb-2">Daftar Catatan</h3>
                    
                    <?php if (count($catatan) > 0) : ?>
                        <div class="space-y-3 overflow-y-auto max-h-[600px] pr-2">
                            <?php foreach ($catatan as $cat) :
                                $for = $cat->type === '1' ? 'Semua siswa kelas ' . $siswa->nama_kelas : $siswa->nama;
                                $readed1 = $cat->type === '1' && ($cat->reading && in_array($cat->id_siswa, $cat->reading));
                                $readed2 = $cat->type === '2' && $cat->readed !== '0';
                                $is_unread = !($readed1 || $readed2);
                                
                                $dot_color = isset($level_dots[$cat->level]) ? $level_dots[$cat->level] : 'bg-gray-400';
                                $label_text = isset($level_labels[$cat->level]) ? $level_labels[$cat->level] : 'Catatan';
                                $badge_style = isset($level_colors[$cat->level]) ? $level_colors[$cat->level] : 'bg-gray-50 text-gray-700 border-gray-200';
                                $avatar_guru = $cat->foto_guru ? base_url($cat->foto_guru) : base_url('assets/img/guru.png');
                                ?>
                                <div class="lumina-card p-4 cursor-pointer hover:bg-background transition-all border <?= $is_unread ? 'border-primary/30 shadow-md' : 'border-outline-variant' ?> flex items-start gap-3 relative note-item"
                                     data-table="<?= $cat->table ?>"
                                     data-id="<?= $cat->id_catatan ?>"
                                     data-foto="<?= $avatar_guru ?>">
                                    
                                    <!-- Read indicator dot -->
                                    <?php if ($is_unread) : ?>
                                        <span class="absolute top-3 right-3 w-2.5 h-2.5 rounded-full bg-primary unread-dot"></span>
                                    <?php endif; ?>

                                    <img class="w-10 h-10 rounded-full object-cover border border-outline-variant bg-surface" src="<?= $avatar_guru ?>" alt="Guru" onerror="this.src='<?= base_url('assets/img/guru.png') ?>'"/>
                                    <div class="flex-1 space-y-1 min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="font-semibold text-xs text-on-surface truncate"><?= htmlspecialchars($cat->nama_guru) ?></p>
                                            <span class="px-2 py-0.5 rounded border text-[9px] font-bold <?= $badge_style ?>">
                                                <?= $label_text ?>
                                            </span>
                                        </div>
                                        <p class="text-[10px] text-on-surface-variant">Kepada: <?= htmlspecialchars($for) ?></p>
                                        <div class="flex justify-between items-center pt-1">
                                            <span class="text-[9px] text-on-surface-variant font-medium"><?= buat_tanggal(date('D, d M Y H:i', strtotime($cat->tgl))) ?></span>
                                            <span class="text-[9px] font-semibold text-primary isreaded-text">
                                                <?= $is_unread ? 'Belum dibaca' : 'Sudah dibaca' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="lumina-card p-8 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl text-outline-variant mb-2">rate_review</span>
                            <p class="text-sm font-semibold">Tidak ada catatan</p>
                            <p class="text-xs mt-1">Belum ada catatan bimbingan atau perilaku dari guru.</p>
                        </div>
                    <?php endif; ?>

                    <!-- Legend -->
                    <div class="lumina-card p-4 space-y-2 mt-4">
                        <h4 class="font-headline text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Keterangan Tingkat Catatan</h4>
                        <div class="grid grid-cols-2 gap-2 text-[10px] text-on-surface-variant">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> Saran / Perkembangan
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Teguran / Ringan
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-pink-500"></span> Peringatan Sedang
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Sangsi / Keras
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Pane: Detail View (7 cols) -->
                <div class="md:col-span-7">
                    <!-- Default Empty State -->
                    <div id="detail-empty" class="lumina-card p-12 text-center text-on-surface-variant flex flex-col items-center justify-center min-h-[350px]">
                        <span class="material-symbols-outlined text-5xl text-primary/20 mb-3">quick_reference_all</span>
                        <p class="text-sm font-semibold text-primary">Detail Catatan</p>
                        <p class="text-xs mt-1 max-w-xs">Pilih salah satu catatan di sebelah kiri untuk melihat pesan detail dari guru.</p>
                    </div>

                    <!-- Detail Card Container (Initially hidden on desktop, shown after AJAX load) -->
                    <div id="detail-card" class="lumina-card overflow-hidden hidden relative space-y-4">
                        <div class="p-5 border-b border-outline-variant bg-surface flex items-start gap-4">
                            <img id="detail-guru-foto" class="w-12 h-12 rounded-full object-cover border border-outline-variant bg-background" src="" alt="Guru" onerror="this.src='<?= base_url('assets/img/guru.png') ?>'"/>
                            <div class="flex-1 min-w-0">
                                <h4 id="detail-guru-nama" class="font-headline font-bold text-sm text-primary">Nama Guru</h4>
                                <p id="detail-guru-jabatan" class="text-xs text-on-surface-variant mt-0.5">Jabatan Guru</p>
                            </div>
                            <span id="detail-note-badge" class="px-2.5 py-0.5 rounded border text-[10px] font-bold">
                                Badge
                            </span>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="prose max-w-none text-xs text-on-surface leading-relaxed text-justify whitespace-pre-line" id="detail-isi">
                                Detail isi catatan akan muncul di sini.
                            </div>
                        </div>
                        <!-- Loader overlay inside detail card -->
                        <div id="detail-loader" class="absolute inset-0 bg-surface/80 flex items-center justify-center z-10 hidden">
                            <div class="animate-spin rounded-full h-8 w-8 border-2 border-primary border-t-transparent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Mobile Detail Modal overlay -->
<div id="detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 hidden backdrop-blur-sm">
    <div class="bg-surface rounded-2xl w-full max-w-md overflow-hidden flex flex-col shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-5 border-b border-outline-variant flex justify-between items-center">
            <h4 class="font-headline font-bold text-sm text-primary">Detail Catatan</h4>
            <button onclick="closeDetailModal()" class="p-1 rounded-full hover:bg-background text-on-surface-variant">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <div class="p-5 space-y-4 overflow-y-auto max-h-[70vh]">
            <div class="flex items-center gap-3">
                <img id="modal-guru-foto" class="w-10 h-10 rounded-full object-cover border border-outline-variant" src="" alt="Guru" onerror="this.src='<?= base_url('assets/img/guru.png') ?>'"/>
                <div>
                    <h5 id="modal-guru-nama" class="font-semibold text-xs text-on-surface">Nama Guru</h5>
                    <p id="modal-guru-jabatan" class="text-[10px] text-on-surface-variant mt-0.5">Jabatan Guru</p>
                </div>
            </div>
            <div class="pt-3 border-t border-outline-variant">
                <div class="flex items-center justify-between mb-3">
                    <span id="modal-note-badge" class="px-2 py-0.5 rounded border text-[9px] font-bold">Level</span>
                    <span id="modal-note-tgl" class="text-[9px] text-on-surface-variant font-medium">Tanggal</span>
                </div>
                <div class="text-xs text-on-surface leading-relaxed text-justify whitespace-pre-line" id="modal-isi">
                    Isi catatan di modal.
                </div>
            </div>
        </div>
        <div class="p-4 border-t border-outline-variant flex justify-end bg-background">
            <button onclick="closeDetailModal()" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary/95 transition-all">Tutup</button>
        </div>
    </div>
</div>

<!-- Bottom Menu Overlay (Mobile) -->
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
    let itemsClicked = [];
    const levelColors = {
        '1': 'bg-green-50 text-green-700 border-green-200',
        '2': 'bg-amber-50 text-amber-700 border-amber-200',
        '3': 'bg-pink-50 text-pink-700 border-pink-200',
        '4': 'bg-red-50 text-red-700 border-red-200'
    };
    const levelLabels = {
        '1': 'Saran',
        '2': 'Teguran',
        '3': 'Peringatan',
        '4': 'Sangsi'
    };

    function screenSize() {
        const w = window.innerWidth;
        return (w < 768) ? 'xs' : ((w < 992) ? 'sm' : ((w < 1200) ? 'md' : 'lg'));
    }

    function closeDetailModal() {
        $('#detail-modal').addClass('hidden');
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

    $(document).ready(function () {
        $('.note-item').click(function () {
            const $this = $(this);
            const id = $this.data('id');
            const table = $this.data('table');
            const fotoGuru = $this.data('foto');

            // Show loaders
            $('#detail-loader').removeClass('hidden');

            $.ajax({
                url: base_url + 'siswa/detailcatatan/' + table + '/' + id,
                type: 'GET',
                success: function (response) {
                    $('#detail-loader').addClass('hidden');
                    const detail = response.detail;
                    const reading = response.reading;
                    const mapel = detail.nama_mapel == null ? detail.jabatan + ' ' + detail.nama_kelas : 'Guru ' + detail.nama_mapel;
                    
                    const badgeClass = levelColors[detail.level] || 'bg-gray-50 text-gray-700 border-gray-200';
                    const badgeText = levelLabels[detail.level] || 'Catatan';

                    // Update UI text and tags
                    if (screenSize() === 'xs') {
                        // Modal view for mobile
                        $('#modal-guru-nama').text(detail.nama_guru);
                        $('#modal-guru-jabatan').text(mapel);
                        $('#modal-isi').html(detail.text);
                        $('#modal-guru-foto').attr('src', fotoGuru);
                        $('#modal-note-badge').attr('class', 'px-2 py-0.5 rounded border text-[9px] font-bold ' + badgeClass).text(badgeText);
                        
                        const dateObj = new Date(detail.tgl);
                        $('#modal-note-tgl').text(dateObj.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}));
                        
                        $('#detail-modal').removeClass('hidden');
                    } else {
                        // Split pane view for desktop
                        $('#detail-guru-nama').text(detail.nama_guru);
                        $('#detail-guru-jabatan').text(mapel);
                        $('#detail-isi').html(detail.text);
                        $('#detail-guru-foto').attr('src', fotoGuru);
                        $('#detail-note-badge').attr('class', 'px-2.5 py-0.5 rounded border text-[10px] font-bold ' + badgeClass).text(badgeText);

                        $('#detail-empty').addClass('hidden');
                        $('#detail-card').removeClass('hidden');
                    }

                    // Handle Read status
                    var alreadyRead = reading.length > 0 && $.inArray(detail.id_siswa, reading) > -1;
                    var readed = detail.type === '1' ? alreadyRead : detail.readed !== '0';
                    const clicked = $.inArray(table + '-' + detail.id_catatan, itemsClicked) > -1;

                    if (!readed && !clicked) {
                        itemsClicked.push(table + '-' + id);
                        $.ajax({
                            url: base_url + 'siswa/readed/' + table + '/' + detail.id_catatan,
                            type: 'GET',
                            success: function (res) {
                                console.log('Read logged successfully', res);
                            }
                        });
                    }

                    // Remove visual unread indicators
                    $this.removeClass('border-primary/30 shadow-md');
                    $this.addClass('border-outline-variant');
                    $this.find('.unread-dot').remove();
                    $this.find('.isreaded-text').text('Sudah dibaca');

                    // Manage list selection visuals
                    $('.note-item').removeClass('bg-primary/5 border-primary/45');
                    $this.addClass('bg-primary/5 border-primary/45');
                },
                error: function (xhr, error, status) {
                    $('#detail-loader').addClass('hidden');
                    console.error('AJAX Error: ' + xhr.responseText);
                }
            });
        });
    });
</script>

<style>
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .pb-safe {
            padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));
        }
    }
</style>
</body>
</html>
