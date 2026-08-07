<?php
defined('BASEPATH') or exit('No direct script access allowed');
$foto_profil = (isset($siswa) && is_object($siswa) && !empty($siswa->foto)) ? base_url($siswa->foto) : base_url('assets/img/siswa.png');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Menu Pelaporan - Pelita LMS</title>
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
        .header-bg {
            background-color: #334779;
            position: relative;
            overflow: hidden;
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
        .tab-btn {
            flex: 1;
            padding: 8px 12px;
            font-size: 0.75rem;
            border-radius: 0.5rem;
            border: none;
            transition: all 0.2s;
            cursor: pointer;
            font-weight: 600;
            background-color: transparent;
            color: #464555;
        }
        .tab-btn:hover {
            background-color: #f7f9fb;
            color: #334779;
        }
        .tab-btn.active {
            background-color: #334779 !important;
            color: #ffffff !important;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen font-body selection:bg-primary-container selection:text-on-primary-container">

<!-- Top Navigation (Desktop) -->
<header class="hidden md:flex justify-between items-center w-full px-container-padding-desktop max-w-[1280px] mx-auto h-16 bg-surface border-b border-outline-variant sticky top-0 z-40">
    <div class="font-headline text-xl font-bold text-primary"><?= (isset($setting) && is_object($setting)) ? htmlspecialchars($setting->nama_aplikasi) : 'Lumina LMS' ?></div>
    <nav class="flex items-center gap-8">
        <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-medium" href="<?= base_url('dashboard') ?>">Dashboard</a>
        <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-medium" href="<?= base_url('siswa/materi') ?>">Materi</a>
        <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-medium" href="<?= base_url('siswa/tugas') ?>">Tugas</a>
        <a class="text-on-surface-variant hover:text-primary transition-colors text-sm font-medium" href="<?= base_url('chat') ?>">Obrolan</a>
    </nav>
    <div class="flex items-center gap-4 text-primary">
        <span class="text-xs text-on-surface-variant font-semibold">TP: <?= (isset($tp_active) && is_object($tp_active)) ? htmlspecialchars($tp_active->tahun) : date('Y') ?> (Smt: <?= (isset($smt_active) && is_object($smt_active)) ? htmlspecialchars($smt_active->smt) : '1' ?>)</span>
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
            <a class="flex items-center gap-3 bg-primary text-white rounded-lg px-4 py-2.5 text-sm font-semibold transition-all" href="<?= base_url('laporan') ?>">
                <span class="material-symbols-outlined">campaign</span> Lapor
            </a>
            <a class="flex items-center gap-3 text-red-600 px-4 py-2.5 hover:bg-red-50 rounded-lg text-sm font-medium transition-all" href="<?= base_url('logout') ?>">
                <span class="material-symbols-outlined">logout</span> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="flex-1 min-w-0 p-container-padding-mobile md:p-container-padding-desktop pb-24 lg:pb-container-padding-desktop">
        <div class="max-w-2xl mx-auto space-y-6">
            
            <!-- Page Title -->
            <div class="space-y-1">
                <h1 class="font-headline text-2xl font-bold text-slate-800"><?= $judul ?></h1>
                <p class="text-xs text-on-surface-variant"><?= $subjudul ?></p>
            </div>

            <!-- Session Notification Alerts -->
            <?php if ($this->session->flashdata('success')) : ?>
                <div class="p-4 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 rounded-xl" role="alert">
                    ✅ <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')) : ?>
                <div class="p-4 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl" role="alert">
                    ⚠️ <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Tab Navigation (Tabs) -->
            <div class="flex border border-outline-variant bg-surface rounded-xl p-1.5 shadow-lumina gap-1.5">
                <button onclick="switchTab('evaluasi')" id="tab-btn-evaluasi" class="tab-btn active">
                    ⭐ Evaluasi Tutor
                </button>
                <button onclick="switchTab('insiden')" id="tab-btn-insiden" class="tab-btn">
                    🚨 Lapor Insiden / Kejadian
                </button>
            </div>

            <!-- TAB 1 CONTENT: EVALUASI TUTOR -->
            <div id="tab-content-evaluasi" class="lumina-card overflow-hidden block">
                <div class="bg-primary text-white p-5">
                    <h3 class="font-headline font-bold text-sm m-0 text-white">Rapor Evaluasi Kinerja Tutor</h3>
                    <p class="text-white/70 text-[10px] m-0 mt-1">Berikan ulasan Anda terkait proses mengajar guru kelas secara rahasia dan aman.</p>
                </div>
                
                <?= form_open('laporan/simpan_evaluasi', ['class' => 'p-5 space-y-4']) ?>
                    <div>
                        <label for="id_guru" class="block text-xs font-bold text-on-surface-variant mb-2">Pilih Guru / Tutor <span class="text-red-500">*</span></label>
                        <select name="id_guru" id="id_guru" class="w-full text-xs p-2.5 border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-medium" required>
                            <option value="">-- Pilih Tutor --</option>
                            <?php if (isset($teachers) && is_array($teachers)) : ?>
                                <?php foreach ($teachers as $teacher) : ?>
                                    <option value="<?= $teacher->id_guru ?>"><?= htmlspecialchars($teacher->nama_guru) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <?php if (isset($lapor_settings['evaluasi_tanggal_tampil']) && $lapor_settings['evaluasi_tanggal_tampil'] == '1') : 
                        $is_required = (isset($lapor_settings['evaluasi_tanggal_wajib']) && $lapor_settings['evaluasi_tanggal_wajib'] == '1');
                    ?>
                        <div>
                            <label for="tanggal_evaluasi" class="block text-xs font-bold text-on-surface-variant mb-2">Tanggal Pembelajaran yang Dievaluasi <?php if ($is_required) : ?><span class="text-red-500">*</span><?php endif; ?></label>
                            <input type="date" name="tanggal_evaluasi" id="tanggal_evaluasi" class="w-full text-xs p-2.5 border border-outline-variant rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary font-medium" max="<?= date('Y-m-d') ?>" <?= $is_required ? 'required' : '' ?>>
                            <p class="text-[10px] text-on-surface-variant/70 mt-1">Pilih tanggal spesifik kelas di mana tutor ini mengajar Anda.</p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pertanyaan)) : ?>
                        <div class="space-y-4 pt-3 border-t border-outline-variant">
                            <h4 class="text-xs font-bold text-primary uppercase tracking-wider mb-2">Kuesioner Penilaian</h4>
                            
                            <?php foreach ($pertanyaan as $q) : ?>
                                <div class="bg-background p-4 rounded-xl border border-outline-variant space-y-3">
                                    <label class="block text-xs font-bold text-slate-800 leading-relaxed"><?= htmlspecialchars($q->pertanyaan) ?> <span class="text-red-500">*</span></label>
                                    
                                    <?php if ($q->tipe === 'pilihan') : ?>
                                        <div class="space-y-2">
                                        <?php 
                                        $options = explode(',', $q->pilihan_jawaban);
                                        foreach ($options as $opt) : 
                                            $opt = trim($opt);
                                        ?>
                                            <label class="flex items-center gap-3 cursor-pointer text-xs text-on-surface font-medium mb-0">
                                                <input type="radio" name="question_<?= $q->id_pertanyaan ?>" value="<?= htmlspecialchars($opt) ?>" class="text-primary focus:ring-primary h-4 w-4 border-outline-variant" required>
                                                <span><?= htmlspecialchars($opt) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                        </div>
                                    <?php else : ?>
                                        <textarea name="question_<?= $q->id_pertanyaan ?>" rows="3" placeholder="Ketik tanggapan atau masukan Anda di sini..." class="w-full p-2.5 text-xs border border-outline-variant rounded-lg focus:outline-none focus:border-primary" required></textarea>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p class="text-center text-on-surface-variant text-xs py-4">Belum ada kuesioner aktif saat ini.</p>
                    <?php endif; ?>

                    <div class="pt-4 border-t border-outline-variant flex justify-end">
                        <button type="submit" class="bg-primary hover:bg-primary/95 text-white font-bold py-2.5 px-6 rounded-lg border-none cursor-pointer text-xs shadow-sm transition-all">
                            Kirim Rapor Evaluasi
                        </button>
                    </div>
                <?= form_close() ?>
            </div>

            <!-- TAB 2 CONTENT: LAPORAN INSIDEN -->
            <div id="tab-content-insiden" class="lumina-card overflow-hidden hidden">
                <div class="bg-amber-600 text-white p-5">
                    <h3 class="font-headline font-bold text-sm m-0 text-white">Kotak Aduan Insiden & Tindak Kekerasan</h3>
                    <p class="text-white/70 text-[10px] m-0 mt-1">Gunakan formulir ini untuk melaporkan perundungan (bullying), pelecehan seksual, atau kejadian darurat lainnya.</p>
                </div>
                
                <?= form_open_multipart('laporan/simpan_insiden', ['class' => 'p-5 space-y-4']) ?>
                    
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl">
                        <div class="flex gap-3">
                            <span class="material-symbols-outlined text-amber-600 text-lg">verified_user</span>
                            <div class="text-xs text-amber-800 leading-relaxed">
                                <strong>Privasi Terjaga Keamanannya:</strong> Laporan akan diteruskan langsung ke kepala admin. Silakan centang kotak **Kirim sebagai Anonim** di bawah jika Anda tidak ingin mencantumkan identitas nama Anda.
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kategori" class="block text-xs font-bold text-on-surface-variant mb-2">Kategori Laporan <span class="text-red-500">*</span></label>
                            <select name="kategori" id="kategori" class="w-full text-xs p-2.5 border border-outline-variant rounded-lg focus:outline-none focus:border-primary font-medium" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Perundungan (Bullying)">Perundungan (Bullying)</option>
                                <option value="Kekerasan Seksual (Pelecehan)">Kekerasan Seksual (Pelecehan)</option>
                                <option value="Kekerasan Fisik / Perkelahian">Kekerasan Fisik / Perkelahian</option>
                                <option value="Pencurian / Pemerasan (Kompas)">Pencurian / Pemerasan (Kompas)</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label for="tanggal_kejadian" class="block text-xs font-bold text-on-surface-variant mb-2">Tanggal Kejadian <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_kejadian" id="tanggal_kejadian" class="w-full text-xs p-2 border border-outline-variant rounded-lg focus:outline-none focus:border-primary" required>
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-xs font-bold text-on-surface-variant mb-2">Deskripsi Kronologi Kejadian <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" rows="5" placeholder="Tuliskan tempat kejadian, siapa saja yang terlibat, serta kronologi lengkap kejadian secara detail..." class="w-full p-2.5 text-xs border border-outline-variant rounded-lg focus:outline-none focus:border-primary" required></textarea>
                    </div>

                    <div>
                        <label for="bukti_file" class="block text-xs font-bold text-on-surface-variant mb-2">Lampiran Bukti File/Foto (Opsional)</label>
                        <input type="file" name="bukti_file" id="bukti_file" accept="image/*,application/pdf" class="w-full p-2 border border-outline-variant rounded-lg text-xs">
                        <small class="text-slate-400 text-[10px] mt-1 block">Tipe berkas yang diizinkan: JPG, PNG, PDF. Maksimal 10MB.</small>
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer text-xs text-on-surface font-bold mt-2 mb-0">
                        <input type="checkbox" name="is_anonymous" value="1" class="text-primary focus:ring-primary h-4 w-4 border-outline-variant">
                        <span>Kirim laporan secara Anonim (Sembunyikan nama saya)</span>
                    </label>

                    <div class="pt-4 border-t border-outline-variant flex justify-end">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 px-6 rounded-lg border-none cursor-pointer text-xs shadow-sm transition-all">
                            Kirim Laporan Aduan
                        </button>
                    </div>
                <?= form_close() ?>
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
        <button onclick="toggleMobileMenu()" class="p-1 rounded-full hover:bg-background text-on-surface-variant border-none bg-transparent">
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
        <a class="flex flex-col items-center text-center gap-2" href="<?= base_url('laporan') ?>">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-xl">campaign</span>
            </div>
            <span class="text-[10px] font-semibold text-on-surface">Lapor</span>
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
    <button onclick="toggleMobileMenu()" class="flex flex-col items-center justify-center text-primary font-bold focus:outline-none border-none bg-transparent">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">grid_view</span>
        <span class="text-[10px] font-bold mt-1">Lainnya</span>
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

    function switchTab(tab) {
        const evaluasiContent = document.getElementById('tab-content-evaluasi');
        const insidenContent = document.getElementById('tab-content-insiden');
        const evaluasiBtn = document.getElementById('tab-btn-evaluasi');
        const insidenBtn = document.getElementById('tab-btn-insiden');

        if (tab === 'evaluasi') {
            evaluasiContent.classList.replace('hidden', 'block');
            insidenContent.classList.replace('block', 'hidden');
            evaluasiBtn.classList.add('active');
            insidenBtn.classList.remove('active');
        } else {
            insidenContent.classList.replace('hidden', 'block');
            evaluasiContent.classList.replace('block', 'hidden');
            insidenBtn.classList.add('active');
            evaluasiBtn.classList.remove('active');
        }
    }
</script>
</body>
</html>
