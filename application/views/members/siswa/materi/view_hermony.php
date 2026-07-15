<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Null-safety guard
if (!is_object($materi) || !is_object($siswa)) {
    echo '<div style="text-align:center;padding:40px;font-family:sans-serif;">';
    echo '<h3 style="color:#e74c3c;">Data tidak ditemukan</h3>';
    echo '<p>Materi atau data siswa tidak tersedia. Silakan kembali dan coba lagi.</p>';
    echo '<a href="javascript:history.back()" style="color:#334779;">&#8592; Kembali</a>';
    echo '</div>';
    return;
}

$ada_nilai = $logs != null && $logs->nilai != null && $logs->nilai != '0';
$sudah_selesai = (isset($logs) && (
    (isset($logs->selesai) && $logs->selesai == 1) || 
    (isset($logs->waktu_selesai) && $logs->waktu_selesai != null) || 
    (isset($logs->finish_time) && $logs->finish_time != null) || 
    (isset($logs->text) && $logs->text != null)
)) ? true : false;

$foto_profil = !empty($siswa->foto) ? base_url($siswa->foto) : base_url('assets/img/siswa.png');
$avatar_guru = ($materi && isset($materi->foto)) ? base_url($materi->foto) : base_url('assets/img/guru.png');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $judul ?> - Lumina Learning System</title>
    
    <!-- Summernote/Bootstrap Dependencies -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/summernote/summernote-bs4.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/app/css/jquery.toast.min.css">

    <!-- Tailwind & Fonts -->
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
        .bg-primary {
            background-color: #334779 !important;
        }
        .text-primary {
            color: #334779 !important;
        }
        .border-primary {
            border-color: #334779 !important;
        }
        .bg-secondary {
            background-color: #855300 !important;
        }
        .text-secondary {
            color: #855300 !important;
        }
        .border-secondary {
            border-color: #855300 !important;
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
        
        /* Reset and Override overrides */
        body {
            background-color: #f7f9fb !important;
            font-family: 'Inter', sans-serif !important;
            color: #191c1e !important;
        }
        a {
            color: inherit;
        }
        a:hover {
            color: inherit;
            text-decoration: none;
        }
        .note-editor {
            border-radius: 0.5rem !important;
            border: 1px solid #e0e3e5 !important;
        }
        
        /* Uploader List styling */
        #media-upload {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.75rem;
        }
        #media-upload li {
            position: relative;
            aspect-ratio: 1;
            border: 1px solid #e0e3e5;
            border-radius: 0.5rem;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f7f9fb;
        }
        #media-upload li img, #media-upload li video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        #media-upload li.myupload {
            border: 2px dashed #334779;
            background: #dae2ff/20;
            cursor: pointer;
        }
        #media-upload li.myupload span {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            color: #334779;
        }
        #picupload {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .remove-pic {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border-radius: 9999px;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            z-index: 10;
        }
        .title-thumb {
            display: none !important; /* Hide bootstrap title text overlay */
        }
    </style>
    
    <!-- Scripts -->
    <script src="<?= base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?= base_url() ?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>/assets/plugins/summernote/summernote-bs4.min.js"></script>
    <script src="<?= base_url() ?>/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?= base_url() ?>/assets/app/js/jquery.toast.min.js"></script>
    <script src="<?= base_url() ?>/assets/app/js/show.toast.js"></script>
    
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
        <a class="flex items-center gap-3 <?= $jenis == '1' ? 'bg-primary text-white font-semibold' : 'text-on-surface-variant hover:bg-background font-medium' ?> rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('siswa/materi') ?>">
            <span class="material-symbols-outlined">school</span> Materi
        </a>
        <a class="flex items-center gap-3 <?= $jenis == '2' ? 'bg-primary text-white font-semibold' : 'text-on-surface-variant hover:bg-background font-medium' ?> rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('siswa/tugas') ?>">
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
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                <a href="<?= base_url('siswa/materi') ?>" class="hover:text-primary transition-all flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Materi
                </a>
            </div>

            <!-- Page Grid -->
            <?php if (empty($materi)) : ?>
                <div class="lumina-card p-8 text-center max-w-md mx-auto space-y-4">
                    <span class="material-symbols-outlined text-6xl text-red-600">error</span>
                    <h3 class="font-headline font-bold text-lg text-primary">Materi/Tugas Tidak Ditemukan</h3>
                    <p class="text-xs text-on-surface-variant">Modul pembelajaran atau tugas yang Anda cari tidak tersedia, sudah dihapus, atau belum dijadwalkan untuk kelas Anda.</p>
                    <a href="<?= base_url('siswa/materi') ?>" class="inline-block px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary/95 transition-all">
                        Kembali ke Materi
                    </a>
                </div>
            <?php else : ?>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                
                <!-- Left Column: Materi Details (7 Cols) -->
                <div class="md:col-span-7 space-y-6">
                    
                    <!-- Materi Content Card -->
                    <div class="lumina-card overflow-hidden">
                        <!-- Card Header -->
                        <div class="p-5 border-b border-outline-variant flex justify-between items-center bg-slate-50/50">
                            <div class="flex items-center gap-4">
                                <img class="w-12 h-12 rounded-full object-cover border border-outline-variant bg-surface" src="<?= $avatar_guru ?>" alt="Guru" onerror="this.src='<?= base_url('assets/img/guru.png') ?>'"/>
                                <div>
                                    <h3 class="font-headline font-bold text-sm text-primary"><?= htmlspecialchars($materi->nama_guru) ?></h3>
                                    <p class="text-[10px] text-on-surface-variant mt-0.5"><?= htmlspecialchars($materi->nama_mapel) ?> &bull; Kelas <?= htmlspecialchars($siswa->nama_kelas) ?></p>
                                </div>
                            </div>
                            
                            <!-- Nilai Box -->
                            <?php if ($ada_nilai) : ?>
                                <div class="px-3 py-1.5 rounded-lg bg-green-50 border border-green-200 text-center flex flex-col items-center">
                                    <span class="text-[8px] font-bold text-green-700 uppercase tracking-wider">Nilai</span>
                                    <span class="text-xl font-bold text-green-700"><?= $logs->nilai ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-4">
                            <?php if ($ada_nilai) : ?>
                                <div class="p-3 bg-amber-50 text-amber-800 border border-amber-200 text-xs rounded-xl flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm text-amber-800">warning</span>
                                    Materi ini sudah dinilai dan tidak dapat dikerjakan ulang!
                                </div>
                            <?php endif; ?>

                            <div class="space-y-1">
                                <span class="text-[9px] font-bold text-secondary tracking-widest uppercase"><?= htmlspecialchars($materi->kode_materi) ?></span>
                                <h2 class="font-headline font-bold text-lg text-primary leading-snug"><?= htmlspecialchars($materi->judul_materi) ?></h2>
                            </div>
                            
                            <!-- Isi Materi (HTML Content with parsed base_url images) -->
                            <div class="text-xs text-on-surface leading-relaxed text-justify space-y-3 prose max-w-none pt-2 border-t border-outline-variant/60">
                                <?php
                                $dom = new DOMDocument();
                                @$dom->loadHTML($materi->isi_materi);
                                $images = $dom->getElementsByTagName('img');
                                foreach ($images as $image) {
                                    if ($image instanceof DOMElement) {
                                        $curSrc = $image->getAttribute('src');
                                        if (strpos($curSrc, 'http') !== false) {
                                            $pathUpload = 'uploads';
                                            $forReplace = explode($pathUpload, $pathUpload);
                                            $image->setAttribute('src', base_url() . $pathUpload . $forReplace[1]);
                                        } else {
                                            $image->setAttribute('src', base_url() . $curSrc);
                                        }
                                    }
                                }
                                $isi_materi = $dom->saveHTML();
                                echo $isi_materi;
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- File Pendukung Card -->
                    <div class="lumina-card p-5 space-y-3">
                        <h4 class="font-headline font-bold text-xs text-primary uppercase tracking-wider">File Pendukung</h4>
                        
                        <?php
                        $files = unserialize($materi->file ?? '');
                        if (!empty($files)) :
                        ?>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <?php foreach ($files as $file) : 
                                    $temp = explode('.', $file["src"] ?? '');
                                    $extension = strtolower(end($temp));
                                    $is_image = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                    $is_video = in_array($extension, ['mpeg', 'mpg', 'mp4', 'avi']);
                                    ?>
                                    <div class="border border-outline-variant rounded-lg p-2 flex flex-col justify-between items-center text-center bg-slate-50/50 hover:bg-slate-50 transition-all min-h-[100px]">
                                        <div class="flex-1 flex items-center justify-center p-2">
                                            <?php if ($is_image) : ?>
                                                <img class="max-h-12 rounded object-cover cursor-pointer" src="<?= base_url() . $file["src"] ?>" onclick="window.open(this.src)" title="Buka gambar"/>
                                            <?php elseif ($is_video) : ?>
                                                <span class="material-symbols-outlined text-3xl text-primary cursor-pointer hover:scale-105 transition-all" onclick="window.open('<?= base_url() . $file["src"] ?>')">play_circle</span>
                                            <?php else : ?>
                                                <span class="material-symbols-outlined text-3xl text-slate-400 cursor-pointer" onclick="window.open('<?= base_url() . $file["src"] ?>')">description</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-[9px] text-on-surface font-semibold truncate w-full px-1" title="<?= $file["name"] ?>"><?= $file["name"] ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <p class="text-[10px] text-on-surface-variant italic">Tidak ada file pendukung.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Submission Form (5 Cols) -->
                <div class="md:col-span-5 space-y-6">
                    
                    <!-- Submission Form Card -->
                    <div class="lumina-card p-5 space-y-4">
                        <div class="flex justify-between items-center">
                            <h4 class="font-headline font-bold text-xs text-primary uppercase tracking-wider">Hasil Catatan Materi</h4>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold <?= $sudah_selesai ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                                <?= $sudah_selesai ? 'Selesai Dibaca' : 'Belum Selesai' ?>
                            </span>
                        </div>

                        <!-- Hasil / Form Jawaban -->
                        <?php if ($sudah_selesai || $ada_nilai) : ?>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Ringkasan / Catatan Siswa</label>
                                    <div class="p-4 bg-surface border border-outline-variant rounded-xl text-sm text-on-surface prose max-w-none shadow-sm">
                                        <?= isset($logs->text) && trim(strip_tags($logs->text)) !== '' ? $logs->text : '<em class="text-on-surface-variant text-xs">Tidak ada catatan jawaban.</em>' ?>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <!-- Form Text/Html Editor -->
                            <?= form_open('', array('id' => 'formhasil')) ?>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Ringkasan / Catatan Siswa</label>
                                        <textarea id="text-materi" name='isi_materi' class='editor'
                                                  data-id="<?= $this->security->get_csrf_hash() ?>"
                                                  data-name="<?= $this->security->get_csrf_token_name() ?>">
                                        </textarea>
                                    </div>
                                    
                                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary/95 transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-sm"><?= $jenis == '1' ? 'check_circle' : 'send' ?></span> <?= $jenis == '1' ? 'Tandai Selesai Belajar' : 'Kirim Pekerjaan' ?>
                                    </button>
                                </div>
                            <?= form_close(); ?>
                            
                            <!-- File Upload Form -->
                            <?= form_open_multipart('', array('id' => 'formfile', 'class' => 'pt-3 border-t border-outline-variant/60 mt-4')) ?>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Tambahkan File (Opsional)</label>
                                        <span class="text-[8px] text-on-surface-variant">PDF, DOCX, JPG, MP4</span>
                                    </div>
                                    
                                    <ul id="media-upload">
                                        <!-- Dynamic Uploaded Files List -->
                                        <li class="myupload">
                                            <span>
                                                <span class="material-symbols-outlined text-xl">add</span>
                                                <input name="file_uploads" type="file" id="picupload">
                                                <input type="hidden" name="max-size" value="2048">
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            <?= form_close(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Mobile Bottom Navigation (Standard for Hermony views) -->
<div id="mobile-menu-overlay" onclick="toggleMobileMenu()" class="lg:hidden fixed inset-0 bg-black/50 z-50 invisible opacity-0 transition-all duration-300 backdrop-blur-sm"></div>
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

<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-2 py-3 bg-surface border-t border-outline-variant shadow-lg pb-safe">
    <a class="flex flex-col items-center justify-center text-on-surface-variant" href="<?= base_url('dashboard') ?>">
        <span class="material-symbols-outlined">home</span>
        <span class="text-[10px] font-medium mt-1">Home</span>
    </a>
    <a class="flex flex-col items-center justify-center <?= $jenis == '1' ? 'text-primary' : 'text-on-surface-variant' ?>" href="<?= base_url('siswa/materi') ?>">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?= $jenis == '1' ? '1' : '0' ?>;">auto_stories</span>
        <span class="text-[10px] <?= $jenis == '1' ? 'font-semibold' : 'font-medium' ?> mt-1">Materi</span>
    </a>
    <a class="flex flex-col items-center justify-center <?= $jenis == '2' ? 'text-primary' : 'text-on-surface-variant' ?>" href="<?= base_url('siswa/tugas') ?>">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?= $jenis == '2' ? '1' : '0' ?>;">assignment</span>
        <span class="text-[10px] <?= $jenis == '2' ? 'font-semibold' : 'font-medium' ?> mt-1">Tugas</span>
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
    var adaNilai = '<?=$ada_nilai?>' == '1';
    var logMateri = JSON.parse('<?= json_encode($logs) ?>');
    var logMulai = '<?= $logs != null && $logs->log_time != null ? $logs->log_time : '' ?>';
    var logSelesai = '<?= $logs != null && $logs->finish_time != null ? $logs->finish_time : '' ?>';
    var idSiswa = '<?= $siswa->id_siswa ?>';
    var idKjm = '<?= $materi->id_kjm ?>';
    var jamKe = '<?= $jamke ?>';
    var mapel = '<?= $materi->id_mapel ?>';

    var dataFiles = [];
    var arrFileAttach = logMateri != null && logMateri.file != null ? logMateri.file : [];
    dataFiles = $.merge(dataFiles, arrFileAttach);

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
        $('.editor').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
            ],
            placeholder: 'Tulis ringkasan hasil belajar di sini',
            tabsize: 2,
            minHeight: 200,
        });

        // Save entry log if empty
        if (logMulai === '') {
            setTimeout(function () {
                $.ajax({
                    type: "GET",
                    url: base_url + "siswa/savelogmateri?id_siswa=" + idSiswa + '&id_kjm=' + idKjm + '&jamke=' + jamKe + '&mapel=' + mapel,
                    success: function (response) {
                        console.log('Opened Log logged successfully', response);
                    }
                });
            }, 1000);
        }

        createPreviewFile();

        $('#formhasil').on('submit', function (e) {
            // 1. BLOKIR REFRESH SECARA ABSOLUT DI BARIS PERTAMA!
            e.preventDefault();
            e.stopImmediatePropagation();

            console.log('AJAX Submit Triggered!'); // Untuk debugging console

            if (adaNilai) return false;

            // 2. AMANKAN SUMMERNOTE (Gunakan Try-Catch agar tidak mematikan JS jika error)
            try {
                if ($('.editor').length > 0 && $('.editor').next().hasClass('note-editor')) {
                    var noteContent = $('.editor').summernote('code');
                    $('.editor').val(noteContent); // Update textarea
                }
            } catch (err) {
                console.warn('Summernote sync error: ', err);
            }

            // VALIDASI: Cegah submit jika jawaban kosong
            var htmlContent = $('.editor').val() || ''; 
            var cleanText = htmlContent.replace(/(<([^>]+)>)/gi, "").replace(/&nbsp;/gi, "").trim();

            if (cleanText.length === 0) {
                if (typeof swal === 'function') {
                    swal("Peringatan", "Catatan/Jawaban tidak boleh kosong!", "warning");
                } else {
                    Swal.fire("Peringatan", "Catatan/Jawaban tidak boleh kosong!", "warning");
                }
                return false;
            }

            var update = logSelesai === '' ? '0' : '1';
            
            // 3. AMBIL DATA FORM
            var formData = new FormData(this);
            formData.append('update', update);
            formData.append('id_siswa', idSiswa);
            formData.append('id_kjm', idKjm);
            formData.append('jamke', jamKe);
            formData.append('mapel', mapel);
            formData.append('attach', JSON.stringify(dataFiles));

            Swal.fire({
                title: "Menyimpan Pekerjaan",
                text: "Silakan tunggu sebentar...",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // 4. KIRIM AJAX
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: base_url + 'siswa/savefilemateriselesai', // URL eksplisit backend materi
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log('Respon server:', response);
                    if (response.status === 'error' || response.status === false) {
                        Swal.fire("Gagal Mengirim", response.message || "Terjadi kesalahan.", "error");
                        return;
                    }

                    var msg = response.message || 'Pekerjaan Anda berhasil dikirim.';
                    Swal.fire({
                        title: 'Berhasil!',
                        text: msg,
                        icon: 'success'
                    }).then(function() {
                        window.location.reload(true);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('XHR Text:', xhr.responseText);
                    let errMsg = 'Terjadi kesalahan sistem saat menghubungi server.';
                    try {
                        const err = JSON.parse(xhr.responseText);
                        if(err.message) errMsg = err.message;
                    } catch(e) {
                        if (xhr.responseText) {
                            errMsg = xhr.responseText.replace(/(<([^>]+)>)/gi, "").substring(0, 100) + '...';
                        }
                    }
                    Swal.fire({
                        title: "Error Server",
                        text: errMsg,
                        icon: "error"
                    });
                }
            });
        });

        $('body').on('click', '.remove-pic', function () {
            var elm = $(this).parent().parent();
            var removeItem = $(this).attr('data-id');
            for (var i = 0; i < dataFiles.length; i++) {
                var cur = dataFiles[i];
                if (cur.name === removeItem) {
                    deleteImage(i, elm, cur.src);
                    break;
                }
            }
        });

        $("#picupload").on('change', function (e) {
            if (adaNilai) return;
            var form = new FormData($("#formfile")[0]);
            uploadAttach(base_url + 'siswa/uploadfile', form);
        });
    });

    function saveFileToDb() {
        if (adaNilai) return;
        var update = logSelesai === '' ? '0' : '1';
        var dataUpload = $('#formhasil').serialize() + '&update=' + update + '&id_siswa=' + idSiswa + '&jamke=' + jamKe + '&id_kjm=' + idKjm + '&attach=' + JSON.stringify(dataFiles);

        $.ajax({
            type: 'POST',
            url: base_url + 'siswa/savefilemateriselesai',
            data: dataUpload,
            success: function (data) {
                showSuccessToast('Lampiran diperbarui');
                createPreviewFile();
            }, error: function (data) {
                showDangerToast('Gagal memperbarui lampiran');
            }
        });
    }

    function uploadAttach(action, data) {
        if (adaNilai) return;
        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: action,
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                if (data.status) {
                    var item = {};
                    item['size'] = data.size;
                    item["type"] = data.type;
                    item["src"] = data.src;
                    item["name"] = data.filename;
                    dataFiles.push(item);
                    saveFileToDb();
                } else {
                    Swal.fire({
                        title: "Gagal Mengunggah",
                        text: data.src || "File tidak diizinkan",
                        icon: "error"
                    });
                }
            },
            error: function (e) {
                $.toast({
                    heading: "Gagal Mengunggah",
                    text: "File tidak terbaca atau melebihi kapasitas",
                    icon: 'error',
                    position: 'top-right'
                });
            }
        });
    }

    function deleteImage(index, elm, src) {
        if (adaNilai) return;
        $.ajax({
            data: {src: src},
            type: "POST",
            url: base_url + "siswa/deletefile",
            cache: false,
            success: function (response) {
                dataFiles.splice(index, 1);
                elm.remove();
                saveFileToDb();
            }
        });
    }

    function createPreviewFile() {
        $("#media-upload li:not(.myupload)").remove();
        
        for (var j = 0; j < dataFiles.length; j++) {
            let file = dataFiles[j];
            var div = document.createElement("li");
            div.setAttribute("id", "f-" + file.name);
            
            let innerHTML = "";
            if (file.type.match('image')) {
                innerHTML = "<img src='" + base_url + file.src + "'/>";
            } else if (file.type.match('video')) {
                innerHTML = "<video src='" + base_url + file.src + "'></video>";
            } else {
                innerHTML = "<img src='" + base_url + "/assets/app/img/document_file.png' class='p-3' style='object-fit: contain'/>";
            }
            
            innerHTML += "<a href='javascript:void(0);' data-id='" + file.name + "' class='remove-pic'>" +
                         "<span class='material-symbols-outlined text-[10px]'>close</span></a>";
            
            div.innerHTML = innerHTML;
            $("#media-upload").prepend(div);
        }
    }
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
