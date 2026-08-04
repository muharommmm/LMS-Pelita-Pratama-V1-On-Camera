<?php
defined('BASEPATH') or exit('No direct script access allowed');

$today = date("Y-m-d");
$tugas_today = isset($tugass[$today]) ? $tugass[$today] : [];

// Parse Istirahat/Break periods
$arrIst = [];
$arrDur = [];
if (isset($kbm->istirahat)) {
    $ist = json_decode(json_encode($kbm->istirahat));
    $jmlIst = json_decode(json_encode(unserialize($ist ?? '')));
    if ($jmlIst) {
        foreach ($jmlIst as $istirahat) {
            $arrIst[] = $istirahat->ist;
            $arrDur[$istirahat->ist] = $istirahat->dur;
        }
    }
}

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
        
        .tab-btn.active {
            color: #334779;
            border-bottom-color: #334779;
        }
        
        .day-pill.active {
            background-color: #334779;
            color: #ffffff;
        }
    </style>
    <script src="<?= base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
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
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold font-headline text-primary"><?= $judul ?></h2>
                    <p class="text-xs text-on-surface-variant mt-1">Daftar tugas akademik harian dan mingguan</p>
                </div>
                <!-- Tabs switcher -->
                <div class="flex border-b border-outline-variant bg-surface rounded-lg p-1 shadow-sm w-full sm:w-auto">
                    <button onclick="switchTab('today')" id="tab-btn-today" class="tab-btn flex-1 sm:flex-none px-4 py-2 text-xs font-semibold rounded-md transition-all text-primary bg-primary-container">Hari Ini</button>
                    <button onclick="switchTab('weekly')" id="tab-btn-weekly" class="tab-btn flex-1 sm:flex-none px-4 py-2 text-xs font-semibold rounded-md transition-all text-on-surface-variant hover:text-primary">Seminggu Ini</button>
                </div>
            </div>

            <!-- Tab 1: Tugas Hari Ini -->
            <div id="tab-content-today" class="space-y-6">
                <div class="lumina-card p-5">
                    <h3 class="font-headline font-bold text-sm text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">event</span> Tugas Hari Ini
                        <span class="text-xs font-normal text-on-surface-variant">&bull; <?= buat_tanggal(date('D, d M Y')) ?></span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    $jamMulai = new DateTime($kbm->kbm_jam_mulai);
                    $jamSampai = new DateTime($kbm->kbm_jam_mulai);

                    if (count($tugas_today) > 0 && isset($kbm->kbm_jml_mapel_hari)) :
                        for ($i = 0; $i < $kbm->kbm_jml_mapel_hari; $i++) :
                            $jamke = $i + 1;
                            ?>
                            <?php if (in_array($jamke, $arrIst)) :
                                $jamSampai->add(new DateInterval('PT' . $arrDur[$jamke] . 'M'));
                                ?>
                                <!-- Istirahat Card -->
                                <div class="lumina-card p-5 border border-outline-variant bg-slate-50/60 flex flex-col justify-between min-h-[180px]">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-bold text-slate-500 tracking-wider">JAM KE <?= $jamke ?></span>
                                        <span class="text-[10px] text-slate-500 font-semibold flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                                            <?= $jamMulai->format('H:i') ?> - <?= $jamSampai->format('H:i') ?>
                                        </span>
                                    </div>
                                    <div class="text-center py-4">
                                        <span class="material-symbols-outlined text-3xl text-slate-400">coffee</span>
                                        <p class="font-headline font-bold text-sm text-slate-600 mt-1">ISTIRAHAT</p>
                                    </div>
                                    <div></div>
                                </div>
                                <?php
                                $jamMulai->add(new DateInterval('PT' . $arrDur[$jamke] . 'M'));
                            else :
                                $jamSampai->add(new DateInterval('PT' . $kbm->kbm_jam_pel . 'M'));
                                if (isset($tugas_today[$jamke]->id_materi)) :
                                    $tkelas = '';
                                    $arrkelas = unserialize($tugas_today[$jamke]->materi_kelas ?? '');
                                    if ($arrkelas) {
                                        foreach ($arrkelas as $k => $kls) {
                                            if ($k > 0) {
                                                $tkelas .= ', ';
                                            }
                                            $tkelas .= isset($kelas[$kls]) ? $kelas[$kls] : $kls;
                                        }
                                    }
                                    ?>
                                    <!-- Tugas Card -->
                                    <div class="lumina-card lumina-card-hover border border-outline-variant flex flex-col justify-between min-h-[190px]">
                                        <div class="p-4 space-y-3">
                                            <div class="flex justify-between items-start gap-2">
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-primary-container text-primary">JAM KE <?= $jamke ?></span>
                                                <span class="text-[10px] text-on-surface-variant font-semibold flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[12px]">schedule</span>
                                                    <?= $jamMulai->format('H:i') ?> - <?= $jamSampai->format('H:i') ?>
                                                </span>
                                            </div>
                                            <div class="space-y-1">
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-secondary"><?= htmlspecialchars($tugas_today[$jamke]->kode_materi) ?></span>
                                                <h4 class="font-headline font-bold text-sm text-primary truncate" title="<?= htmlspecialchars($tugas_today[$jamke]->nama_mapel) ?>"><?= htmlspecialchars($tugas_today[$jamke]->nama_mapel) ?></h4>
                                                <p class="text-xs text-on-surface truncate"><?= htmlspecialchars($tugas_today[$jamke]->judul_materi) ?></p>
                                                <p class="text-[10px] text-on-surface-variant truncate">Guru: <?= htmlspecialchars($tugas_today[$jamke]->nama_guru) ?></p>
                                            </div>
                                        </div>
                                        <div class="border-t border-outline-variant p-3 bg-slate-50/50 flex justify-between items-center rounded-b-2xl">
                                            <span class="text-[9px] font-semibold text-on-surface-variant">Kls: <?= $tkelas ?></span>
                                            <a href="<?= base_url('siswa/bukatugas/' . $tugas_today[$jamke]->id_kjm . '/' . $jamke) ?>" 
                                               class="flex items-center gap-1 px-3 py-1.5 bg-primary text-white text-[10px] font-bold rounded-lg hover:bg-primary/95 transition-all">
                                                Buka Tugas <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                            </a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- No Tugas Card -->
                                    <div class="lumina-card p-5 border border-outline-variant flex flex-col justify-between min-h-[180px]">
                                        <div class="flex justify-between items-center">
                                            <span class="text-[10px] font-bold text-on-surface-variant tracking-wider">JAM KE <?= $jamke ?></span>
                                            <span class="text-[10px] text-on-surface-variant font-semibold flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">schedule</span>
                                                <?= $jamMulai->format('H:i') ?> - <?= $jamSampai->format('H:i') ?>
                                            </span>
                                        </div>
                                        <div class="text-center py-4">
                                            <p class="font-headline font-semibold text-xs text-on-surface-variant"><?= isset($tugas_today[$jamke]->nama_mapel) ? htmlspecialchars($tugas_today[$jamke]->nama_mapel) : 'Jam Kosong' ?></p>
                                            <p class="font-headline font-bold text-xs text-slate-400 mt-1">Tidak ada tugas</p>
                                        </div>
                                        <div></div>
                                    </div>
                                <?php endif; ?>
                                <?php
                                $jamMulai->add(new DateInterval('PT' . $kbm->kbm_jam_pel . 'M'));
                            endif; ?>
                        <?php endfor; ?>
                    <?php else: ?>
                        <div class="col-span-full lumina-card p-8 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl text-outline-variant mb-2">assignment_late</span>
                            <p class="text-sm font-semibold">Tidak ada jadwal pelajaran hari ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab 2: Tugas Seminggu Ini -->
            <div id="tab-content-weekly" class="space-y-6 hidden animate-in fade-in duration-200">
                <!-- Day selector pill bar -->
                <div class="lumina-card p-4 overflow-x-auto flex gap-2 no-scrollbar">
                    <?php foreach ((array)$week as $index => $tgl) :
                        $is_active = $tgl == $today;
                        ?>
                        <button onclick="selectDay('<?= $tgl ?>')" id="day-pill-<?= $tgl ?>" 
                                class="day-pill flex-shrink-0 px-4 py-2 text-xs font-semibold rounded-full border border-outline-variant bg-surface hover:bg-background transition-all <?= $is_active ? 'active' : '' ?>">
                            <?= date('D, d M', strtotime($tgl)) ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Daily tables content -->
                <div class="space-y-4">
                    <?php
                    foreach ($tugass as $tg => $mat) :
                        $is_hidden = $tg != $today ? 'hidden' : '';
                        ?>
                        <div id="day-content-<?= $tg ?>" class="day-pane space-y-4 <?= $is_hidden ?>">
                            <div class="lumina-card overflow-hidden">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="border-b border-outline-variant bg-slate-50/75 text-on-surface-variant font-semibold">
                                            <th class="p-4 text-center w-24">Jam Ke</th>
                                            <th class="p-4">Mata Pelajaran</th>
                                            <th class="p-4">Tugas</th>
                                            <th class="p-4 text-center w-36">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-outline-variant">
                                        <?php
                                        $jamMulai = new DateTime($kbm->kbm_jam_mulai);
                                        $jamSampai = new DateTime($kbm->kbm_jam_mulai);

                                        foreach ($mat as $jam => $mtr) :
                                            ?>
                                            <?php if (in_array($jam, $arrIst)) :
                                                $jamSampai->add(new DateInterval('PT' . $arrDur[$jam] . 'M'));
                                                ?>
                                                <tr class="hover:bg-slate-50/50">
                                                    <td class="p-4 text-center font-bold text-slate-500">
                                                        <?= $jam ?><br>
                                                        <span class="text-[9px] font-normal text-slate-400"><?= $jamMulai->format('H:i') ?>-<?= $jamSampai->format('H:i') ?></span>
                                                    </td>
                                                    <td colspan="3" class="p-4 font-semibold text-slate-500 tracking-wider">ISTIRAHAT</td>
                                                </tr>
                                                <?php
                                                $jamMulai->add(new DateInterval('PT' . $arrDur[$jam] . 'M'));
                                            else :
                                                $jamSampai->add(new DateInterval('PT' . $kbm->kbm_jam_pel . 'M'));
                                                ?>
                                                <tr class="hover:bg-background/40">
                                                    <td class="p-4 text-center font-bold text-primary">
                                                        <?= $jam ?><br>
                                                        <span class="text-[9px] font-normal text-on-surface-variant"><?= $jamMulai->format('H:i') ?>-<?= $jamSampai->format('H:i') ?></span>
                                                    </td>
                                                    <?php
                                                    if (isset($mtr->id_kjm)) :
                                                        $log = isset($logs[$tg]) ? $logs[$tg] : [];
                                                        $status_class = '';
                                                        $status_label = '';
                                                        
                                                        if (count($log) > 0 && isset($log[$mtr->id_kjm])) {
                                                            $item_log = $log[$mtr->id_kjm];
                                                            $is_redo_item = (isset($item_log->nilai) && $item_log->nilai !== null && ((string)$item_log->nilai === '0' || (float)$item_log->nilai === 0.0));
                                                            if ($is_redo_item) {
                                                                $status_class = 'bg-amber-50 text-amber-700 border-amber-200';
                                                                $status_label = 'Perlu Diulang';
                                                            } elseif ($item_log->finish_time != null) {
                                                                $status_class = 'bg-green-50 text-green-700 border-green-200';
                                                                $status_label = 'Selesai';
                                                            } else {
                                                                $status_class = 'bg-amber-50 text-amber-700 border-amber-200';
                                                                $status_label = 'Belum Selesai';
                                                            }
                                                        } else {
                                                            $status_class = 'bg-red-50 text-red-700 border-red-200';
                                                            $status_label = 'Belum Dikerjakan';
                                                        }
                                                        ?>
                                                        <td class="p-4 font-semibold text-primary"><?= htmlspecialchars($mtr->nama_mapel) ?></td>
                                                        <td class="p-4">
                                                            <div class="space-y-0.5">
                                                                <span class="text-[9px] font-bold text-secondary uppercase tracking-wider"><?= htmlspecialchars($mtr->kode_materi) ?></span>
                                                                <p class="font-medium text-xs text-on-surface"><?= htmlspecialchars($mtr->judul_materi) ?></p>
                                                            </div>
                                                        </td>
                                                        <td class="p-4 text-center">
                                                            <a href="<?= base_url('siswa/bukatugas/' . $mtr->id_kjm . '/' . $tg) ?>" 
                                                               class="inline-block px-3 py-1 text-[10px] font-bold border rounded-full transition-all hover:shadow-sm <?= $status_class ?>">
                                                                <?= $status_label ?>
                                                            </a>
                                                        </td>
                                                    <?php else: ?>
                                                        <td class="p-4 font-semibold text-on-surface-variant"><?= isset($mtr->nama_mapel) ? htmlspecialchars($mtr->nama_mapel) : '-' ?></td>
                                                        <td class="p-4 text-on-surface-variant font-medium">-</td>
                                                        <td class="p-4 text-center text-on-surface-variant">-</td>
                                                    <?php endif; ?>
                                                </tr>
                                                <?php
                                                $jamMulai->add(new DateInterval('PT' . $kbm->kbm_jam_pel . 'M'));
                                            endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
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
        $('.tab-btn').removeClass('text-primary bg-primary-container').addClass('text-on-surface-variant hover:text-primary');
        $('#tab-btn-' + tab).addClass('text-primary bg-primary-container').removeClass('text-on-surface-variant hover:text-primary');
        
        if (tab === 'today') {
            $('#tab-content-today').removeClass('hidden');
            $('#tab-content-weekly').addClass('hidden');
        } else {
            $('#tab-content-today').addClass('hidden');
            $('#tab-content-weekly').removeClass('hidden');
        }
    }

    function selectDay(dateStr) {
        $('.day-pill').removeClass('active');
        $('#day-pill-' + dateStr).addClass('active');

        $('.day-pane').addClass('hidden');
        $('#day-content-' + dateStr).removeClass('hidden');
    }
</script>

<style>
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .pb-safe {
            padding-bottom: calc(0.75rem + env(safe-area-inset-bottom));
        }
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
</body>
</html>
