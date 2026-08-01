<?php
defined('BASEPATH') or exit('No direct script access allowed');
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
        <a class="text-primary border-b-2 border-primary pb-1 font-semibold text-sm" href="<?= base_url('chat') ?>">Obrolan</a>
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
            <a class="flex items-center gap-3 bg-primary text-white font-semibold rounded-lg px-4 py-2.5 text-sm transition-all" href="<?= base_url('chat') ?>">
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
                <h2 class="text-2xl font-bold font-headline text-primary">Pusat Diskusi & Obrolan</h2>
                <p class="text-xs text-on-surface-variant mt-1">Kirim pesan kepada Guru pengampu, teman kelas, atau ruang komunitas.</p>
            </div>

            <!-- Chat Room Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative overflow-hidden">
                <!-- Contacts Card -->
                <div id="chat-contacts-card" class="lumina-card flex flex-col h-[520px] overflow-hidden w-full md:flex">
                    <div class="p-4 border-b border-outline-variant bg-primary/5">
                        <input type="text" id="cari-kontak" placeholder="Cari kontak..." class="w-full text-xs py-1.5 px-3 border border-outline-variant rounded-lg focus:outline-none focus:border-primary"/>
                    </div>
                    <div class="flex-1 overflow-y-auto divide-y divide-outline-variant" id="daftar-kontak">
                        <?php if(isset($kontak) && !empty($kontak)): ?>
                            <?php foreach($kontak as $k): ?>
                                <div class="kontak-item p-3.5 hover:bg-background transition-colors cursor-pointer flex justify-between items-center" 
                                     data-id="<?= $k->id_user ?>" 
                                     data-role="<?= $k->role ?>" 
                                     data-nama="<?= htmlspecialchars($k->nama) ?>" 
                                     data-kelas="<?= $k->kelas ? htmlspecialchars($k->kelas) : '' ?>">
                                    <div class="flex items-center gap-2 text-xs truncate">
                                        <span class="material-symbols-outlined text-xl text-primary-container bg-primary text-primary rounded-full p-1 flex items-center justify-center">
                                            <?= $k->role == 'komunitas' ? 'group' : 'person' ?>
                                        </span>
                                        <div class="truncate">
                                            <p class="font-bold text-on-surface truncate"><?= htmlspecialchars($k->nama) ?></p>
                                            <?php if($k->kelas): ?>
                                                <span class="text-[10px] text-on-surface-variant">- <?= htmlspecialchars($k->kelas) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="badge-container flex items-center gap-1.5">
                                        <?php if(isset($k->unread) && $k->unread > 0): ?>
                                            <span class="badge-unread bg-red-600 text-white font-bold text-[10px] px-1.5 py-0.5 rounded-full"><?= $k->unread ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-on-surface-variant text-xs py-6">Belum ada kontak</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Chat Box Card -->
                <div id="chat-box-card" class="lumina-card md:col-span-2 flex flex-col h-[520px] overflow-hidden w-full md:flex hidden">
                    <div class="p-4 border-b border-outline-variant bg-primary text-white flex justify-between items-center">
                        <div class="flex items-center gap-2 max-w-[80%]">
                            <button id="btn-back-to-contacts" class="md:hidden flex items-center justify-center p-1 rounded-full hover:bg-white/10 text-white mr-1">
                                <span class="material-symbols-outlined text-lg">arrow_back</span>
                            </button>
                            <h4 class="font-headline font-bold text-sm truncate" id="judul-chat-room">Memuat...</h4>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" id="cari-pesan" placeholder="Cari isi pesan..." class="hidden text-xs text-on-surface py-1 px-2.5 rounded-lg border border-outline-variant focus:outline-none focus:border-primary" style="width: 150px;"/>
                            <button id="btn-toggle-cari-pesan" class="hover:bg-white/10 p-1.5 rounded-full transition-colors flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">search</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-background/30" id="area-pesan">
                        <p class="text-center text-on-surface-variant text-xs py-6">Memuat obrolan...</p>
                    </div>

                    <div class="p-3 border-t border-outline-variant bg-surface">
                        <form id="form-kirim-pesan" class="flex gap-2">
                            <input type="text" name="pesan" id="input-pesan" placeholder="Ketik pesan Anda di sini..." class="flex-1 py-2 px-3 border border-outline-variant rounded-lg text-xs focus:outline-none focus:border-primary" autocomplete="off" required/>
                            <button type="submit" id="btn-kirim" class="py-2 px-4 bg-primary hover:bg-primary/90 text-white font-bold text-xs rounded-lg transition-colors flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm">send</span> Kirim
                            </button>
                        </form>
                    </div>
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
    <a class="flex flex-col items-center justify-center text-primary" href="<?= base_url('chat') ?>">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">chat</span>
        <span class="text-[10px] font-semibold mt-1">Chat</span>
    </a>
    <button onclick="toggleMobileMenu()" class="flex flex-col items-center justify-center text-on-surface-variant focus:outline-none">
        <span class="material-symbols-outlined">grid_view</span>
        <span class="text-[10px] font-medium mt-1">Lainnya</span>
    </button>
</nav>

<script type="text/javascript">
var aktif_lawan_bicara_id = null;
var aktif_role = '';
var aktif_nama = '';
var kelas_id_komunitas = null;
var current_user_id = <?= $user->id ?>;
var previous_messages_count = 0;

function replaceURLWithHTMLLinks(text) {
    if (!text) return "";
    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    var text1 = text.replace(exp, '<a href="$1" target="_blank" class="text-white underline font-weight-bold break-all hover:opacity-85">$1</a>');
    var exp2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    return text1.replace(exp2, '$1<a href="http://$2" target="_blank" class="text-white underline font-weight-bold break-all hover:opacity-85">$2</a>');
}

function replaceURLWithHTMLLinksLeft(text) {
    if (!text) return "";
    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    var text1 = text.replace(exp, '<a href="$1" target="_blank" class="text-primary underline font-weight-bold break-all hover:opacity-85">$1</a>');
    var exp2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    return text1.replace(exp2, '$1<a href="http://$2" target="_blank" class="text-primary underline font-weight-bold break-all hover:opacity-85">$2</a>');
}

$(document).ready(function() {

    function scrollToBottom() {
        var chatArea = $('#area-pesan');
        chatArea.scrollTop(chatArea[0].scrollHeight);
    }

    function loadChatHistory() {
        if (aktif_role === '') return;

        var postData = {
            '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
        };

        if (aktif_role === 'komunitas') {
            postData.lawan_bicara_id = null;
            postData.is_komunitas = 1;
            postData.id_kelas_komunitas = kelas_id_komunitas;
        } else {
            postData.lawan_bicara_id = aktif_lawan_bicara_id;
            postData.is_komunitas = 0;
            postData.id_kelas_komunitas = null;
        }

        $.ajax({
            url: '<?= base_url("chat/get_pesan") ?>',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                var html = '';
                var messages = response.messages;

                if (messages && messages.length > 0) {
                    $.each(messages, function(i, msg) {
                        var isRight = (parseInt(msg.pengirim_id) === current_user_id);
                        
                        var displayName = isRight ? 'Anda' : msg.nama_pengirim;
                        if (!isRight && msg.kelas_pengirim) {
                            displayName += ' - ' + msg.kelas_pengirim;
                        }

                        if (isRight) {
                            // Message Right (Sender)
                            html += '<div class="flex items-end justify-end gap-2.5 direct-chat-msg">' +
                                    '  <div class="flex flex-col gap-1 max-w-[70%]">' +
                                    '    <span class="text-[10px] text-on-surface-variant text-right">' + displayName + ' &bull; ' + msg.created_at + '</span>' +
                                    '    <div class="bg-primary text-white p-3 rounded-2xl rounded-tr-none text-xs shadow-sm direct-chat-text">' + replaceURLWithHTMLLinks(msg.pesan) + '</div>' +
                                    '  </div>' +
                                    '  <img class="w-8 h-8 rounded-full border" src="<?= base_url() ?>' + msg.foto_pengirim + '" alt="avatar">' +
                                    '</div>';
                        } else {
                            // Message Left (Receiver)
                            html += '<div class="flex items-end gap-2.5 direct-chat-msg">' +
                                    '  <img class="w-8 h-8 rounded-full border" src="<?= base_url() ?>' + msg.foto_pengirim + '" alt="avatar">' +
                                    '  <div class="flex flex-col gap-1 max-w-[70%]">' +
                                    '    <span class="text-[10px] text-on-surface-variant">' + displayName + ' &bull; ' + msg.created_at + '</span>' +
                                    '    <div class="bg-surface text-on-surface border border-outline-variant p-3 rounded-2xl rounded-tl-none text-xs shadow-sm direct-chat-text">' + replaceURLWithHTMLLinksLeft(msg.pesan) + '</div>' +
                                    '  </div>' +
                                    '</div>';
                        }
                    });
                } else {
                    html = '<div class="text-center text-on-surface-variant text-xs py-6">Mulai obrolan baru dengan ' + aktif_nama + '</div>';
                }

                if (response.tampilkan_tombol_wa && response.wa_number) {
                    html += '<div class="my-4 text-center">' +
                            '  <a href="https://wa.me/' + response.wa_number + '" target="_blank" class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white font-bold text-xs py-2 px-4 rounded-lg transition-colors shadow-sm">' +
                            '    Hubungi via WhatsApp' +
                            '  </a>' +
                            '</div>';
                }

                $('#area-pesan').html(html);

                // Run message filter
                var searchKey = $('#cari-pesan').val().toLowerCase();
                if (searchKey !== '') {
                    $('#area-pesan .direct-chat-msg').each(function() {
                        var hasText = $(this).find('.direct-chat-text').text().toLowerCase().indexOf(searchKey) > -1;
                        $(this).toggle(hasText);
                    });
                }

                var current_count = messages ? messages.length : 0;
                if (current_count > previous_messages_count) {
                    scrollToBottom();
                    previous_messages_count = current_count;
                }

                // Update unread count badges
                $('#daftar-kontak .kontak-item').each(function() {
                    var targetId = $(this).data('id');
                    var targetRole = $(this).data('role');
                    if (targetRole !== 'komunitas') {
                        var count = (response.unread_counts && response.unread_counts[targetId]) ? response.unread_counts[targetId] : 0;
                        var badgeContainer = $(this).find('.badge-container');
                        var badge = $(this).find('.badge-unread');
                        if (count > 0) {
                            if (badge.length === 0) {
                                badgeContainer.prepend('<span class="badge-unread bg-red-600 text-white font-bold text-[10px] px-1.5 py-0.5 rounded-full">' + count + '</span>');
                            } else {
                                badge.text(count).show();
                            }
                        } else {
                            badge.remove();
                        }
                    }
                });
            }
        });
    }

    // Filter Contacts
    $('#cari-kontak').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#daftar-kontak .kontak-item').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Toggle search
    $('#btn-toggle-cari-pesan').on('click', function() {
        $('#cari-pesan').toggleClass('hidden').val('').focus();
        $('#area-pesan .direct-chat-msg').show();
    });

    // Realtime message search
    $('#cari-pesan').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        if (value === '') {
            $('#area-pesan .direct-chat-msg').show();
        } else {
            $('#area-pesan .direct-chat-msg').each(function() {
                var hasText = $(this).find('.direct-chat-text').text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(hasText);
            });
        }
    });

    // Fungsi untuk menset detail obrolan tanpa men-trigger efek transisi slide mobile (untuk startup load)
    function selectContactSilent(el) {
        $('.kontak-item').removeClass('bg-primary/5 border-l-4 border-l-primary');
        el.addClass('bg-primary/5 border-l-4 border-l-primary');

        var targetId = el.data('id');
        var targetRole = el.data('role');
        var targetNama = el.data('nama');
        var targetKelas = el.data('kelas');

        var idParts = String(targetId).split('_');

        if (targetRole === 'komunitas' || idParts[0] === 'komunitas') {
            aktif_lawan_bicara_id = null;
            aktif_role = 'komunitas';
            kelas_id_komunitas = idParts[1] ? parseInt(idParts[1]) : null;
            aktif_nama = targetNama;
        } else {
            aktif_lawan_bicara_id = parseInt(targetId);
            aktif_role = targetRole;
            aktif_nama = targetNama;
            if (targetKelas) {
                aktif_nama += ' - ' + targetKelas;
            }
            kelas_id_komunitas = null;
        }

        el.find('.badge-unread').remove();
        $('#judul-chat-room').text(aktif_nama);
        $('#area-pesan').html('<div class="text-center text-on-surface-variant text-xs py-6">Memuat obrolan...</div>');
        $('#cari-pesan').addClass('hidden').val('');

        previous_messages_count = 0;
        loadChatHistory();
    }

    // Contact selection
    $(document).on('click', '.kontak-item', function() {
        // Jika mode mobile, sembunyikan daftar kontak dan tampilkan box obrolan
        if ($(window).width() < 768) {
            $('#chat-contacts-card').addClass('hidden');
            $('#chat-box-card').removeClass('hidden');
        }

        selectContactSilent($(this));
    });

    // Kembali ke daftar kontak (hanya aktif di mobile)
    $(document).on('click', '#btn-back-to-contacts', function() {
        $('#chat-box-card').addClass('hidden');
        $('#chat-contacts-card').removeClass('hidden');
    });

    // Select target contact from URL or default to first contact
    const urlParams = new URLSearchParams(window.location.search);
    const targetUser = urlParams.get('user');
    var targetContact = null;
    if (targetUser) {
        $('#daftar-kontak .kontak-item').each(function() {
            var id = $(this).data('id') || $(this).attr('data-id');
            if (String(id) === String(targetUser)) {
                targetContact = $(this);
                return false;
            }
        });
    }

    if (targetContact && targetContact.length > 0) {
        targetContact.click();
        if (targetContact[0] && targetContact[0].scrollIntoView) {
            targetContact[0].scrollIntoView({ block: 'nearest' });
        }
    } else if ($('#daftar-kontak .kontak-item').length > 0) {
        var firstContact = $('#daftar-kontak .kontak-item').first();
        if ($(window).width() >= 768) {
            firstContact.click();
        } else {
            selectContactSilent(firstContact);
        }
    } else {
        $('#judul-chat-room').text('Tidak ada kontak');
        $('#area-pesan').html('<div class="text-center text-on-surface-variant text-xs py-6">Belum ada kontak obrolan tersedia.</div>');
    }

    // Poll updates
    setInterval(loadChatHistory, 3000);

    // Form submit
    $('#form-kirim-pesan').on('submit', function(e) {
        e.preventDefault();
        var isiPesan = $('#input-pesan').val();
        var btnKirim = $('#btn-kirim');

        if (isiPesan.trim() !== '') {
            btnKirim.prop('disabled', true);
            var postData = {
                pesan: isiPesan,
                pengirim_role: 'siswa',
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            };

            if (aktif_role === 'komunitas') {
                postData.penerima_id = null;
                postData.penerima_role = null;
                postData.id_kelas_komunitas = kelas_id_komunitas;
            } else {
                postData.penerima_id = aktif_lawan_bicara_id;
                postData.penerima_role = aktif_role;
                postData.id_kelas_komunitas = null;
            }

            $.ajax({
                url: '<?= base_url("chat/kirim_pesan") ?>',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        $('#input-pesan').val('');
                        loadChatHistory();
                    } else {
                        alert('Gagal mengirim pesan: ' + response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan jaringan.');
                },
                complete: function() {
                    btnKirim.prop('disabled', false);
                    $('#input-pesan').focus();
                }
            });
        }
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
