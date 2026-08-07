<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Null-safety: if tugas or siswa is not a valid object, exit gracefully
if (!is_object($tugas) || !is_object($siswa)) {
    echo '<div style="text-align:center;padding:40px;font-family:sans-serif;">';
    echo '<h3 style="color:#e74c3c;">Data tidak ditemukan</h3>';
    echo '<p>Tugas atau data siswa tidak tersedia. Silakan kembali dan coba lagi.</p>';
    echo '<a href="javascript:history.back()" style="color:#334779;">&#8592; Kembali</a>';
    echo '</div>';
    return;
}

$dataFileAttach = [];
if ($log_selesai != null && $log_selesai->file != null) {
    $dataFileAttach = is_array($log_selesai->file) ? $log_selesai->file : @unserialize($log_selesai->file ?: '');
}
if ($dataFileAttach === false && $log_selesai != null && $log_selesai->file != 'b:0;') {
    $dataFileAttach = []; // fallback if unserialize fails
}
$sudah_selesai = (isset($log_selesai) && $log_selesai != null) ? true : false;
$foto_profil = !empty($siswa->foto) ? base_url($siswa->foto) : base_url('assets/img/siswa.png');
$avatar_guru = !empty($tugas->foto) ? base_url($tugas->foto) : base_url('assets/img/guru.png');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $judul ?> - Pelita LMS</title>
    
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
        /* Override Bootstrap class resets that interfere with our Tailwind styles */
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
        
        /* Style adjustments for uploader */
        #media-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-cols: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.75rem;
        }
        #media-list li {
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
        #media-list li img, #media-list li video {
            width: 100%;
            height: 100%;
            object-cover: cover;
        }
        #media-list li.myupload {
            border: 2px dashed #334779;
            background: #dae2ff/20;
            cursor: pointer;
        }
        #media-list li.myupload span {
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
        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        .heic-loading {
            animation: pulse 1.5s infinite ease-in-out;
            background-color: #f1f5f9;
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
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
    
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
        <a class="text-primary border-b-2 border-primary pb-1 font-semibold text-sm" href="<?= base_url('siswa/tugas') ?>">Tugas</a>
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
        <a class="flex items-center gap-3 bg-primary text-white rounded-lg px-4 py-2.5 text-sm font-semibold transition-all" href="<?= base_url('siswa/tugas') ?>">
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
                <a href="<?= base_url('siswa/tugas') ?>" class="hover:text-primary transition-all flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Tugas
                </a>
            </div>

            <!-- Page Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                
                <!-- Left Column: Tugas Details (7 Cols) -->
                <div class="md:col-span-7 space-y-6">
                    
                    <!-- Tugas Content Card -->
                    <div class="lumina-card overflow-hidden">
                        <!-- Card Header -->
                        <div class="p-5 border-b border-outline-variant flex items-center gap-4 bg-slate-50/50">
                            <img class="w-12 h-12 rounded-full object-cover border border-outline-variant bg-surface" src="<?= $avatar_guru ?>" alt="Guru" onerror="this.src='<?= base_url('assets/img/guru.png') ?>'"/>
                            <div>
                                <h3 class="font-headline font-bold text-sm text-primary"><?= htmlspecialchars($tugas->nama_guru) ?></h3>
                                <p class="text-[10px] text-on-surface-variant mt-0.5"><?= htmlspecialchars($tugas->nama_mapel) ?> &bull; Kelas <?= htmlspecialchars($siswa->nama_kelas) ?></p>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-4">
                            <div class="space-y-1">
                                <span class="text-[9px] font-bold text-secondary tracking-widest uppercase"><?= htmlspecialchars($tugas->kode_materi) ?></span>
                                <h2 class="font-headline font-bold text-lg text-primary leading-snug"><?= htmlspecialchars($tugas->judul_materi) ?></h2>
                            </div>
                            
                            <!-- Isi Tugas (HTML Content) -->
                            <div class="text-xs text-on-surface leading-relaxed text-justify space-y-3 prose max-w-none pt-2 border-t border-outline-variant/60">
                                <?php
                                $isi_tugas = $tugas->isi_materi;
                                if (!empty($isi_tugas)) {
                                    $dom = new DOMDocument();
                                    @$dom->loadHTML(mb_convert_encoding($isi_tugas, 'HTML-ENTITIES', 'UTF-8'));
                                    $images = $dom->getElementsByTagName('img');
                                    foreach ($images as $image) {
                                        if ($image instanceof DOMElement) {
                                            $curSrc = $image->getAttribute('src');
                                            if (strpos($curSrc, 'http://') === false && strpos($curSrc, 'https://') === false && strpos($curSrc, '//') === false) {
                                                $cleanSrc = ltrim($curSrc, '/');
                                                $absoluteUrl = base_url($cleanSrc);
                                                $absoluteUrl = str_replace('\\', '/', $absoluteUrl);
                                                $image->setAttribute('src', $absoluteUrl);
                                            }
                                        }
                                    }
                                    $body = $dom->getElementsByTagName('body')->item(0);
                                    if ($body) {
                                        $isi_tugas = '';
                                        foreach ($body->childNodes as $child) {
                                            $isi_tugas .= $dom->saveHTML($child);
                                        }
                                    } else {
                                        $isi_tugas = $dom->saveHTML();
                                    }
                                }
                                echo $isi_tugas;
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- File Pendukung Card -->
                    <div class="lumina-card p-5 space-y-3">
                        <h4 class="font-headline font-bold text-xs text-primary uppercase tracking-wider">File Pendukung</h4>
                        
                        <?php
                        $files = unserialize($tugas->file ?? '');
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
                                                <img class="max-h-12 rounded object-cover cursor-pointer" src="<?= base_url() . $file["src"] ?>" onclick="openFilePreview(this.src, 'image', '<?= htmlspecialchars($file['name'], ENT_QUOTES) ?>')" title="Buka gambar"/>
                                            <?php elseif ($is_video) : ?>
                                                <span class="material-symbols-outlined text-3xl text-primary cursor-pointer hover:scale-105 transition-all" onclick="openFilePreview('<?= base_url() . $file["src"] ?>', 'video', '<?= htmlspecialchars($file['name'], ENT_QUOTES) ?>')">play_circle</span>
                                            <?php else : ?>
                                                <span class="material-symbols-outlined text-3xl text-slate-400 cursor-pointer" onclick="openFilePreview('<?= base_url() . $file["src"] ?>', 'document', '<?= htmlspecialchars($file['name'], ENT_QUOTES) ?>')">description</span>
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
                        <?php 
                        $is_redo = isset($is_redo) ? $is_redo : (is_object($log_obj_raw) && isset($log_obj_raw->nilai) && $log_obj_raw->nilai !== null && ((string)$log_obj_raw->nilai === '0' || (float)$log_obj_raw->nilai === 0.0));
                        ?>
                        <div class="flex justify-between items-center">
                            <h4 class="font-headline font-bold text-xs text-primary uppercase tracking-wider">Hasil Pekerjaan</h4>
                            <?php if ($is_redo) : ?>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Perlu Diulang (Nilai: 0)
                                </span>
                            <?php else : ?>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold <?= $log_selesai != null ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                                    <?= $log_selesai != null ? 'Sudah Dikirim' : 'Belum Dikirim' ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Hasil / Form Jawaban -->
                        <?php if ($sudah_selesai && !$is_redo) : ?>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Jawaban / Catatan Siswa</label>
                                    <div class="p-4 bg-surface border border-outline-variant rounded-xl text-sm text-on-surface prose max-w-none shadow-sm">
                                        <?= is_object($log_selesai) && isset($log_selesai->text) && trim(strip_tags($log_selesai->text)) !== '' ? $log_selesai->text : '<em class="text-on-surface-variant text-xs">Tidak ada catatan jawaban teks.</em>' ?>
                                    </div>
                                </div>

                                <?php if (!empty($dataFileAttach)) : ?>
                                    <div class="pt-3 border-t border-outline-variant/60 space-y-2">
                                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Lampiran Siswa</label>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                            <?php foreach ($dataFileAttach as $file) :
                                                $src = isset($file['src']) ? base_url($file['src']) : '';
                                                $name = isset($file['name']) ? $file['name'] : 'File Lampiran';
                                                $type = isset($file['type']) ? $file['type'] : '';
                                                $is_heic = preg_match('/\.(heic|heif)$/i', $src);
                                                $is_image = strpos($type, 'image') !== false || preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $src) || $is_heic;
                                                $is_video = strpos($type, 'video') !== false || preg_match('/\.(mp4|mpeg|avi|webm)$/i', $src);
                                            ?>
                                                <div class="border border-outline-variant rounded-lg p-2 flex flex-col justify-between items-center text-center bg-slate-50/50 hover:bg-slate-50 transition-all min-h-[100px]">
                                                    <div class="flex-1 flex items-center justify-center p-2">
                                                        <?php if ($is_heic) : ?>
                                                            <?php $unique_id = 'heic_' . uniqid(); ?>
                                                            <img id="<?= $unique_id ?>" class="max-h-16 rounded object-cover cursor-pointer hover:opacity-90 transition-opacity heic-loading" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-heic-src="<?= $src ?>" onclick="openFilePreview(this.src.startsWith('data:') ? this.getAttribute('data-heic-src') : this.src, 'image', '<?= htmlspecialchars($name, ENT_QUOTES) ?>')" title="Buka gambar"/>
                                                            <script>
                                                                setTimeout(function() {
                                                                    var imgEl = document.getElementById('<?= $unique_id ?>');
                                                                    if (imgEl) {
                                                                        loadHeicImage(imgEl, '<?= $src ?>');
                                                                    }
                                                                }, 100);
                                                            </script>
                                                        <?php elseif ($is_image) : ?>
                                                            <img class="max-h-16 rounded object-cover cursor-pointer hover:opacity-90 transition-opacity" src="<?= $src ?>" onclick="openFilePreview(this.src, 'image', '<?= htmlspecialchars($name, ENT_QUOTES) ?>')" title="Buka gambar"/>
                                                        <?php elseif ($is_video) : ?>
                                                            <span class="material-symbols-outlined text-3xl text-primary cursor-pointer hover:scale-105 transition-all" onclick="openFilePreview('<?= $src ?>', 'video', '<?= htmlspecialchars($name, ENT_QUOTES) ?>')">play_circle</span>
                                                        <?php else : ?>
                                                            <span class="material-symbols-outlined text-3xl text-slate-400 cursor-pointer" onclick="openFilePreview('<?= $src ?>', 'document', '<?= htmlspecialchars($name, ENT_QUOTES) ?>')">description</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <p class="text-[9px] text-on-surface font-semibold truncate w-full px-1" title="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></p>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <?php if ($is_redo) : ?>
                                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 space-y-1">
                                    <div class="font-bold flex items-center gap-1.5 text-amber-700">
                                        <span class="material-symbols-outlined text-sm">warning</span> Catatan Tutor (Tugas Perlu Diulang):
                                    </div>
                                    <p class="leading-relaxed"><?= (is_object($log_obj_raw) && !empty($log_obj_raw->catatan)) ? htmlspecialchars($log_obj_raw->catatan) : 'Tugas mendapat nilai 0. Silakan perbaiki dan kirimkan kembali tugas Anda.' ?></p>
                                </div>
                            <?php endif; ?>

                            <!-- Form Text/Html Editor -->
                            <?= form_open('', array('id' => 'formhasil')) ?>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Jawaban / Catatan</label>
                                        <textarea id="text-tugas" name='isi_tugas' class='editor'
                                                  data-id="<?= $this->security->get_csrf_hash() ?>"
                                                  data-name="<?= $this->security->get_csrf_token_name() ?>">
                                            <?= is_object($log_selesai) && isset($log_selesai->text) ? $log_selesai->text : (is_object($log_obj_raw) && isset($log_obj_raw->text) ? $log_obj_raw->text : '') ?>
                                        </textarea>
                                    </div>
                                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary/95 transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-sm">send</span> Kirim Ulang Pekerjaan
                                    </button>
                                </div>
                            <?= form_close(); ?>
                            
                            <!-- File Upload Form -->
                            <?= form_open_multipart('', array('id' => 'formfile', 'class' => 'pt-3 border-t border-outline-variant/60')) ?>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Tambahkan File Lampiran</label>
                                        <span class="text-[8px] text-on-surface-variant">Max: 10MB (PDF, DOCX, JPG, MP4, dll)</span>
                                    </div>
                                    
                                    <div class="p-2.5 bg-blue-50/50 border border-blue-100 rounded-lg flex items-start gap-2 text-[10px] text-blue-900/80">
                                        <span class="material-symbols-outlined text-sm text-blue-700 mt-0.5">info</span>
                                        <span>Format file <b>.heic</b> membutuhkan waktu 1-10 detik hingga benar-benar tampil (proses konversi asinkron).</span>
                                    </div>
                                    
                                    <ul id="media-list">
                                        <!-- Dynamic Uploaded Files List -->
                                        <li class="myupload">
                                            <span>
                                                <span class="material-symbols-outlined text-xl">add</span>
                                                <input name="file_uploads[]" type="file" id="picupload" multiple>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            <?= form_close(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Mobile Bottom Navigation templates (if needed for menu sheets, standard for Hermony) -->
<div id="mobile-menu-overlay" onclick="toggleMobileMenu()" class="lg:hidden fixed inset-0 bg-black/50 z-50 invisible opacity-0 transition-all duration-300 backdrop-blur-sm"></div>
<div id="mobile-menu-sheet" class="lg:hidden fixed bottom-0 left-0 right-0 bg-surface rounded-t-2xl z-50 transform translate-y-full transition-transform duration-300 pb-safe shadow-2xl max-w-md mx-auto max-h-[85vh] overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
    <!-- Menu contents matching standard student pages -->
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
        <!-- menu links -->
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
    <a class="flex flex-col items-center justify-center text-on-surface-variant" href="<?= base_url('siswa/materi') ?>">
        <span class="material-symbols-outlined">auto_stories</span>
        <span class="text-[10px] font-medium mt-1">Materi</span>
    </a>
    <a class="flex flex-col items-center justify-center text-primary" href="<?= base_url('siswa/tugas') ?>">
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
    var logMulai = '<?= is_object($log_mulai) && isset($log_mulai->log_time) ? $log_mulai->log_time : '' ?>';
    var logSelesai = '<?= is_object($log_selesai) && isset($log_selesai->log_time) ? $log_selesai->log_time : '' ?>';
    var idSiswa = '<?= $siswa->id_siswa ?>';
    var idKjt = '<?= $tugas->id_kjm ?>';
    var jamKe = '<?= $jamke ?>';

    var dataFiles = [];
    var arrFileAttach = JSON.parse('<?= json_encode($dataFileAttach)?>');
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
            placeholder: 'Tulis keterangan hasil tugas di sini',
            tabsize: 2,
            minHeight: 200,
        });

        // Trigger log load on open
        if (logMulai === '') {
            setTimeout(function () {
                $.ajax({
                    type: "GET",
                    url: base_url + "siswa/savelogtugas?id_siswa=" + idSiswa + '&id_kjm=' + idKjt + '&jamke=' + jamKe,
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

            // 2. AMANKAN SUMMERNOTE (Gunakan Try-Catch agar tidak mematikan JS jika error)
            try {
                if ($('.editor').length > 0 && $('.editor').next().hasClass('note-editor')) {
                    var noteContent = $('.editor').summernote('code');
                    $('.editor').val(noteContent); // Update textarea
                }
            } catch (err) {
                console.warn('Summernote sync error: ', err);
            }

            var htmlContent = $('.editor').val() || ''; 
            var cleanText = htmlContent.replace(/(<([^>]+)>)/gi, "").replace(/&nbsp;/gi, "").trim();
            var adaFile = dataFiles.length > 0;

            if (cleanText.length === 0 && !adaFile) { 
                if (typeof swal === 'function') {
                    swal("Error", "Jawaban teks atau lampiran file tidak boleh kosong!", "error");
                } else {
                    Swal.fire("Error", "Jawaban teks atau lampiran file tidak boleh kosong!", "error");
                }
                return false;
            }

            var update = logSelesai === '' ? '0' : '1';
            
            // 3. AMBIL DATA FORM
            var formData = new FormData(this);
            formData.append('update', update);
            formData.append('id_siswa', idSiswa);
            formData.append('id_kjm', idKjt);
            formData.append('jamke', jamKe);
            formData.append('attach', JSON.stringify(dataFiles));

            Swal.fire({
                title: "Mengirim Pekerjaan",
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
                url: base_url + 'siswa/savefiletugasselesai', // URL eksplisit backend tugas
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log('Respon server:', response);
                    if (response.status === 'error' || response.status === false) {
                        Swal.fire("Gagal Mengirim", response.message || "Terjadi kesalahan saat mengirim data.", "error");
                        return;
                    }

                    var msg = response.message || 'Pekerjaan Anda berhasil dikirim!';
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
                    let errMsg = 'Terjadi kesalahan saat mengirim data ke server.';
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
            var elm = $(this).closest('li');
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
            var form = new FormData($("#formfile")[0]);
            uploadAttach(base_url + 'siswa/uploadfile', form);
        });
    });

    function saveFileToDb() {
        var update = logSelesai === '' ? '0' : '1';
        var dataUpload = $('#formhasil').serialize() + '&update=' + update + '&id_siswa=' + idSiswa + '&jamke=' + jamKe + '&id_kjm=' + idKjt + '&attach=' + JSON.stringify(dataFiles);

        $.ajax({
            type: 'POST',
            url: base_url + 'siswa/savefiletugasselesai',
            data: dataUpload,
            success: function (data) {
                if (typeof showSuccessToast === 'function') {
                    showSuccessToast('Lampiran diperbarui');
                } else {
                    console.log('Lampiran diperbarui');
                }
                createPreviewFile();
            }, error: function (data) {
                if (typeof showDangerToast === 'function') {
                    showDangerToast('Gagal memperbarui lampiran');
                } else {
                    console.error('Gagal memperbarui lampiran');
                }
            }
        });
    }

    function uploadAttach(action, data) {
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
                if (data.multiple) {
                    // Multiple uploads
                    var any_success = false;
                    for (var i = 0; i < data.files.length; i++) {
                        var file = data.files[i];
                        if (file.status) {
                            var item = {};
                            item['size'] = file.size;
                            item["type"] = file.type;
                            item["src"] = file.src;
                            item["name"] = file.filename;
                            dataFiles.push(item);
                            any_success = true;
                        } else {
                            Swal.fire("Gagal Mengunggah", file.src || "File tidak diizinkan", "error");
                        }
                    }
                    if (any_success) {
                        createPreviewFile();
                    }
                } else {
                    // Single upload fallback
                    if (data.status) {
                        var item = {};
                        item['size'] = data.size;
                        item["type"] = data.type;
                        item["src"] = data.src;
                        item["name"] = data.filename;
                        dataFiles.push(item);
                        createPreviewFile();
                    } else {
                        Swal.fire("Gagal Mengunggah", data.src || "File tidak diizinkan", "error");
                    }
                }
            },
            error: function (e) {
                console.error("error", e.responseText);
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
        console.log("deleteImage called for index:", index, "src:", src);
        var dataDelete = {
            src: src
        };
        var csrfInput = $('input[name="csrf_token"]');
        if (csrfInput.length > 0) {
            var csrfName = csrfInput.attr('name');
            var csrfHash = csrfInput.val();
            if (csrfName && csrfHash) {
                dataDelete[csrfName] = csrfHash;
            }
        } else {
            var editor = $('.editor');
            if (editor.length > 0) {
                var csrfName = editor.data('name');
                var csrfHash = editor.data('id');
                if (csrfName && csrfHash) {
                    dataDelete[csrfName] = csrfHash;
                }
            }
        }

        $.ajax({
            data: dataDelete,
            type: "POST",
            url: base_url + "siswa/deletefile",
            cache: false,
            success: function (response) {
                console.log("deletefile success. Response:", response);
                dataFiles.splice(index, 1);
                elm.remove();
                createPreviewFile();
            },
            error: function(xhr, status, error) {
                console.error("deletefile failed:", error, xhr.responseText);
            }
        });
    }

    function loadHeicImage(imgElement, srcUrl) {
        if (typeof heic2any === 'undefined') {
            console.warn("heic2any library is not loaded");
            imgElement.src = srcUrl;
            return;
        }
        imgElement.classList.add('heic-loading');
        fetch(srcUrl)
            .then(res => {
                if (!res.ok) throw new Error("Fetch failed");
                return res.blob();
            })
            .then(blob => heic2any({ blob: blob, toType: "image/jpeg", quality: 0.6 }))
            .then(conversionResult => {
                var blobResult = Array.isArray(conversionResult) ? conversionResult[0] : conversionResult;
                var objectUrl = URL.createObjectURL(blobResult);
                imgElement.src = objectUrl;
                imgElement.classList.remove('heic-loading');
            })
            .catch(err => {
                console.error("HEIC conversion failed:", err);
                imgElement.src = base_url + '/assets/app/img/document-icon.svg';
                imgElement.classList.remove('heic-loading');
            });
    }

    function createPreviewFile() {
        console.log("createPreviewFile called. dataFiles current state:", dataFiles);
        // Clear all except uploader block
        $("#media-list li:not(.myupload)").remove();
        
        try {
            for (var j = 0; j < dataFiles.length; j++) {
                let file = dataFiles[j];
                if (!file) {
                    console.warn("createPreviewFile: file at index " + j + " is null/undefined");
                    continue;
                }
                var div = document.createElement("li");
                div.setAttribute("id", "f-" + (file.name || 'unnamed_' + j));
                
                let innerHTML = "";
                var is_heic = false;
                var file_src_lower = (file.src || '').toLowerCase();
                if (file_src_lower.endsWith('.heic') || file_src_lower.endsWith('.heif')) {
                    is_heic = true;
                }
                
                var is_image = is_heic;
                var is_video = false;
                
                if (!is_heic && file.type && typeof file.type === 'string') {
                    is_image = file.type.match('image');
                    is_video = file.type.match('video');
                }
                
                if (is_heic) {
                    var uniqueId = "heic-preview-" + Math.random().toString(36).substring(2, 9);
                    innerHTML = "<img id='" + uniqueId + "' src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' class='heic-loading'/>";
                    setTimeout(function(uId, path) {
                        var imgEl = document.getElementById(uId);
                        if (imgEl) {
                            loadHeicImage(imgEl, base_url + '/' + path);
                        }
                    }, 50, uniqueId, file.src);
                } else if (is_image) {
                    innerHTML = "<img src='" + base_url + '/' + file.src + "'/>";
                } else if (is_video) {
                    innerHTML = "<video src='" + base_url + '/' + file.src + "'></video>";
                } else {
                    innerHTML = "<img src='" + base_url + "/assets/app/img/document_file.png' class='p-3' style='object-fit: contain'/>";
                }
                
                innerHTML += "<a href='javascript:void(0);' data-id='" + (file.name || '') + "' class='remove-pic'>" +
                             "<span class='material-symbols-outlined text-[10px]'>close</span></a>";
                
                div.innerHTML = innerHTML;
                $("#media-list").prepend(div);
            }
            console.log("createPreviewFile completed successfully. Number of children in #media-list:", $("#media-list li").length);
        } catch (err) {
            console.error("Error in createPreviewFile:", err);
        }
    }

    function openFilePreview(url, type, name) {
        let container = document.getElementById('preview-container');
        let title = document.getElementById('preview-file-name');
        let modal = document.getElementById('pwa-file-preview-modal');
        
        title.textContent = name || "Pratinjau Berkas";
        container.innerHTML = "";
        
        let extension = url.split('.').pop().split('?')[0].toLowerCase();
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'].includes(extension) || type.includes('image') || url.startsWith('blob:')) {
            let img = document.createElement('img');
            img.className = "max-w-full max-h-full object-contain rounded-lg";
            img.src = url;
            container.appendChild(img);
        } else if (['mp4', 'mpeg', 'avi', 'webm', 'mpg'].includes(extension) || type.includes('video')) {
            let video = document.createElement('video');
            video.className = "w-full max-h-full rounded-lg";
            video.controls = true;
            video.src = url;
            container.appendChild(video);
        } else if (extension === 'pdf') {
            let iframe = document.createElement('iframe');
            iframe.className = "w-full h-full border-0 rounded-b-lg";
            iframe.src = url;
            container.appendChild(iframe);
        } else {
            container.innerHTML = `
                <div class="text-center text-white p-6 space-y-4">
                    <span class="material-symbols-outlined text-6xl text-slate-400">description</span>
                    <p class="text-sm">Berkas ini tidak mendukung pratinjau langsung.</p>
                    <a href="${url}" download class="inline-block bg-[#334779] text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-[#334779]/80 transition-all">Unduh Berkas</a>
                </div>
            `;
        }
        
        modal.classList.remove('hidden');
    }

    function closeFilePreviewModal() {
        let modal = document.getElementById('pwa-file-preview-modal');
        let container = document.getElementById('preview-container');
        container.innerHTML = "";
        modal.classList.add('hidden');
    }
</script>

<!-- Modal Pratinjau File PWA -->
<div id="pwa-file-preview-modal" class="fixed inset-0 z-[9999] hidden bg-black/80 flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-4xl bg-white rounded-t-xl p-3 flex justify-between items-center shadow-md">
        <span id="preview-file-name" class="font-headline font-bold text-xs text-primary truncate max-w-[70%]">Nama Berkas</span>
        <button onclick="closeFilePreviewModal()" class="flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-all">
            <span class="material-symbols-outlined text-sm">close</span> Tutup Pratinjau
        </button>
    </div>
    <div id="preview-container" class="w-full max-w-4xl h-[70vh] bg-slate-900 rounded-b-xl flex justify-center items-center overflow-hidden p-2 relative">
    </div>
</div>

<style>
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .pb-safe {
            padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));
        }
    }
</style>
</body>
</html>
