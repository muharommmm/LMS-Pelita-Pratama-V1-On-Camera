<?php
defined('BASEPATH') or exit('No direct script access allowed');

$arrGuru = [];
foreach ($guru as $g) {
    $arrGuru[$g->id_guru] = $g->nama_guru;
}

$cbt_setting = [];
$foto_profil = $siswa->foto ? base_url($siswa->foto) : base_url('assets/img/siswa.png');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $judul ?> - Lumina Learning System</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <a class="flex items-center gap-3 bg-primary text-white font-semibold rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('siswa/cbt') ?>">
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
                <h2 class="text-2xl font-bold font-headline text-primary">Info Ulangan / Ujian</h2>
                <p class="text-xs text-on-surface-variant mt-1">Harap periksa jadwal penilaian dan patuhi instruksi selama ujian berlangsung.</p>
            </div>

            <!-- Profile and Info Card -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info Siswa -->
                <div class="lumina-card p-5 space-y-4">
                    <h4 class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Identitas Peserta</h4>
                    <?php if ($cbt_info == null) : ?>
                        <div class="p-3 bg-red-50 text-red-700 text-xs rounded border border-red-200">Tidak ada jadwal penilaian aktif.</div>
                    <?php else: 
                        $arrTitle = ['No. Peserta', 'Ruang', 'Sesi', 'Mulai', 'Selesai'];
                        $arrSub = [$cbt_info->no_peserta->nomor_peserta ?? '-', $cbt_info->nama_ruang ?? '-', $cbt_info->nama_sesi ?? '-', substr($cbt_info->waktu_mulai, 0, -3), substr($cbt_info->waktu_akhir, 0, -3)];
                    ?>
                        <div class="space-y-2 text-xs">
                            <?php foreach ($arrTitle as $key => $title) : 
                                if ($arrSub[$key] == null) $cbt_setting[] = $title;
                            ?>
                                <div class="flex justify-between py-1.5 border-b border-outline-variant last:border-0">
                                    <span class="text-on-surface-variant"><?= $title ?></span>
                                    <span class="font-bold text-on-surface"><?= $arrSub[$key] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Peraturan Ujian -->
                <div class="lumina-card p-5 md:col-span-2 space-y-3 border-l-4 border-l-red-500">
                    <h4 class="font-headline font-bold text-sm text-red-700 uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-lg">warning</span> Dilarang Selama Ujian:
                    </h4>
                    <ul class="list-disc pl-5 text-xs text-on-surface-variant space-y-1.5">
                        <li>Meninggalkan ruang ujian atau menutup aplikasi tanpa izin pengawas.</li>
                        <li>Mengambil tangkapan layar (screenshot) atau membuka tab lain pada peramban.</li>
                        <li>Saling memberitahukan atau menanyakan jawaban ke peserta lain.</li>
                        <li>Membawa alat bantu selain yang diizinkan oleh Proktor/Admin.</li>
                    </ul>
                </div>
            </div>

            <!-- Jadwal Penilaian Hari Ini -->
            <section class="lumina-card overflow-hidden">
                <div class="p-5 border-b border-outline-variant">
                    <h3 class="font-headline text-sm font-bold text-primary flex items-center gap-2 tracking-wide uppercase">
                        <span class="material-symbols-outlined text-xl">calendar_today</span> Jadwal Penilaian Hari Ini
                    </h3>
                </div>
                <div class="p-5">
                    <?php
                    if ($cbt_info == null || count($cbt_setting) > 0) : ?>
                        <p class="text-center text-on-surface-variant text-sm py-4">Tidak ada jadwal penilaian aktif hari ini. Silakan hubungi Proktor/Admin.</p>
                    <?php else:
                        $jamSesi = $cbt_info == null ? '0' : (isset($cbt_info->sesi_id) ? $cbt_info->sesi_id : $cbt_info->id_sesi);
                        if (isset($cbt_jadwal[date('Y-m-d')]) && count($cbt_jadwal[date('Y-m-d')]) > 0) : ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($cbt_jadwal[date('Y-m-d')] as $key => $jadwal) : 
                                    $kk = unserialize($jadwal->bank_kelas ?? '');
                                    $arrKelasCbt = [];
                                    foreach ($kk as $k) {
                                        $arrKelasCbt[] = $k['kelas_id'];
                                    }

                                    $startDay = strtotime($jadwal->tgl_mulai);
                                    $endDay = strtotime($jadwal->tgl_selesai);
                                    $today = strtotime(date('Y-m-d'));

                                    $sesiMulai = new DateTime($sesi[$jamSesi]['mulai']);
                                    $sesiSampai = new DateTime($sesi[$jamSesi]['akhir']);
                                    $now = strtotime(date('H:i'));

                                    $durasi = $elapsed[$jadwal->id_jadwal];
                                    
                                    if ($durasi != null) {
                                        $selesai = $durasi->selesai != null;
                                        $lanjutkan = $durasi->lama_ujian != null;
                                        $reset = $durasi->reset;
                                    } else {
                                        $selesai = false;
                                        $lanjutkan = false;
                                        $reset = 0;
                                    }
                                    $jam_ke = $jadwal->jam_ke == '0' ? '1' : $jadwal->jam_ke;
                                ?>
                                    <div class="lumina-card p-5 border border-outline-variant flex flex-col justify-between gap-4">
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-bold text-primary">Jam ke-<?= $jam_ke ?></span>
                                                <span class="font-semibold text-on-surface-variant flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[14px]">alarm</span> <?= $jadwal->durasi_ujian ?> Menit
                                                </span>
                                            </div>
                                            <h4 class="font-headline font-bold text-lg text-primary"><?= htmlspecialchars($jadwal->nama_mapel) ?></h4>
                                            <div class="text-xs text-on-surface-variant flex gap-4">
                                                <span>Jenis: <b><?= htmlspecialchars($jadwal->nama_jenis) ?></b></span>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <?php if (!$lanjutkan && $reset == 0 && !$selesai) : ?>
                                                <?php if ($today < $startDay) : ?>
                                                    <span class="w-full block text-center py-2 bg-gray-100 text-gray-800 text-xs font-bold rounded-lg">BELUM DIMULAI</span>
                                                <?php elseif ($today > $endDay) : ?>
                                                    <span class="w-full block text-center py-2 bg-red-50 text-red-700 text-xs font-bold rounded-lg">SUDAH BERAKHIR</span>
                                                <?php else: ?>
                                                    <?php if ($now < strtotime($sesiMulai->format('H:i'))) : ?>
                                                        <span class="w-full block text-center py-2 bg-gray-100 text-gray-800 text-xs font-bold rounded-lg">BELUM MASUK SESI UJIAN</span>
                                                    <?php elseif ($now > strtotime($sesiSampai->format('H:i'))) : ?>
                                                        <span class="w-full block text-center py-2 bg-red-50 text-red-700 text-xs font-bold rounded-lg">SESI UJIAN BERAKHIR</span>
                                                    <?php else: ?>
                                                        <!-- Token Input Form -->
                                                        <?= form_open('siswa/validasisiswa', ['id' => 'validasi' . $jadwal->id_jadwal, 'class' => 'space-y-2 form-validasi-ujian']) ?>
                                                            <input type="hidden" name="jadwal" value="<?= $jadwal->id_jadwal ?>"/>
                                                            <input type="hidden" name="siswa" value="<?= $siswa->id_siswa ?>"/>
                                                            <input type="hidden" name="bank" value="<?= $jadwal->id_bank ?>"/>
                                                            
                                                            <?php if ($jadwal->token == '1') : ?>
                                                                <input type="text" name="token" placeholder="Masukkan Token Ujian" class="w-full text-center py-1.5 border border-outline-variant rounded-lg text-xs font-bold uppercase focus:outline-none focus:border-primary" required/>
                                                            <?php endif; ?>
                                                            <button type="submit" class="w-full py-2 bg-secondary hover:bg-secondary/90 text-white font-bold text-xs rounded-lg transition-colors">
                                                                Mulai Ujian
                                                            </button>
                                                        <?= form_close() ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php elseif ($lanjutkan && !$selesai) : ?>
                                                <a href="<?= base_url('siswa/penilaian/' . $jadwal->id_jadwal) ?>" class="w-full block text-center py-2 bg-primary hover:bg-primary/90 text-white text-xs font-bold rounded-lg transition-colors">
                                                    Lanjutkan Ujian
                                                </a>
                                            <?php elseif ($selesai) : ?>
                                                <span class="w-full block text-center py-2 bg-green-50 text-green-700 text-xs font-bold rounded-lg border border-green-200">UJIAN SELESAI</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <p class="text-center text-on-surface-variant text-sm py-4">Tidak ada jadwal penilaian untuk hari ini.</p>
                        <?php endif; ?>
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

    $(document).ready(function() {
        $('.form-validasi-ujian').submit(function (e) {
            e.stopPropagation();
            e.preventDefault();

            swal.fire({
                title: "Membuka Soal",
                text: "Silahkan tunggu....",
                button: false,
                closeOnClickOutside: false,
                closeOnEsc: false,
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    swal.showLoading();
                }
            });

            var form = $(this);
            var jadwal = form.find('input[name="jadwal"]').val();
            var base_url = '<?= base_url() ?>';

            $.ajax({
                type: 'POST',
                url: base_url + 'siswa/validasisiswa',
                data: form.serialize(),
                success: function (data) {
                    console.log(data);
                    if (data.token === true) {
                        if (data.support === false) {
                            swal.fire({
                                "title": "Error",
                                "html": "Browser tidak mendukung!<br>Gunakan browser Chrome, atau Mozilla<br>005",
                                "icon": "error"
                            });
                        } else {
                            if (data.izinkan === true) {
                                if (data.ada_waktu === true) {
                                    if (data.jml_soal > 0) {
                                        window.location.href = base_url + 'siswa/penilaian/' + jadwal;
                                    } else {
                                        swal.fire({
                                            "title": "Error",
                                            "html": "Tidak ada soal ujian<br>Hubungi proktor<br>004",
                                            "icon": "error"
                                        });
                                    }
                                } else {
                                    swal.fire({
                                        "title": "Error",
                                        "html": "Waktu ujian habis!<br>Hubungi proktor<br>003",
                                        "icon": "error"
                                    });
                                }
                            } else {
                                swal.fire({
                                    "title": "Error",
                                    "html": "Anda sedang mengerjakan ujian di perangkat lain<br>Hubungi proktor<br>002",
                                    "icon": "error"
                                });
                            }
                        }
                    } else {
                        swal.fire({
                            "title": "Error",
                            "html": "TOKEN salah!<br>Hubungi proktor<br>001",
                            "icon": "error"
                        });
                    }
                }, error: function (xhr, error, status) {
                    swal.fire({
                        "title": "Error",
                        "html": "Coba kembali ke beranda, lalu ulangi lagi<br>006",
                        "icon": "error"
                    });
                    console.log(xhr.responseText);
                }
            });
        });
    });
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
