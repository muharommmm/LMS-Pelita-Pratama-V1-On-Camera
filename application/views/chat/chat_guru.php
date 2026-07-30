<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Load Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

<!-- Load Tailwind CDN with Preflight disabled to avoid breaking AdminLTE -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false,
        },
        theme: {
            extend: {
                colors: {
                    primary: '#3c8dbc', // Midnight Blue/Navy matching primary AdminLTE
                }
            }
        }
    }
</script>

<div class="content-wrapper bg-slate-50 pt-4">
    <!-- Header -->
    <div class="content-header px-4 md:px-6">
        <h1 class="text-2xl font-bold text-slate-800"><?= $judul ?></h1>
        <p class="text-sm text-slate-500"><?= $subjudul ?></p>
    </div>

    <section class="content px-4 md:px-6 pb-6">
    <!-- Chat Interface Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative overflow-hidden">
        <!-- Contact List -->
        <div id="chat-contacts-card" class="lg:col-span-1 w-full lg:block">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-[600px]">
                <div class="bg-primary text-white p-4">
                    <h3 class="font-semibold m-0 text-white">Daftar Obrolan</h3>
                </div>
                <div class="p-3 border-b border-slate-100 bg-slate-50">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-slate-400 text-lg">search</span>
                        </div>
                        <input type="text" id="cari-kontak" placeholder="Cari nama kontak..." class="w-full pl-10 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-0 custom-scrollbar" id="daftar-kontak">
                    <ul class="flex flex-col list-none p-0 m-0">
                        <?php if(isset($kontak) && !empty($kontak)): ?>
                            <?php foreach($kontak as $k): ?>
                            <li class="kontak-item p-3 border-b border-slate-50 hover:bg-slate-50 cursor-pointer transition-colors" data-id="<?= $k->id_user ?>" data-role="<?= $k->role ?>" data-nama="<?= htmlspecialchars($k->nama) ?>" data-kelas="<?= $k->kelas ? htmlspecialchars($k->kelas) : '' ?>">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 <?= $k->role == 'komunitas' ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-500' ?>">
                                            <span class="material-symbols-outlined"><?= $k->role == 'komunitas' ? 'groups' : 'person' ?></span>
                                        </div>
                                        <div class="flex flex-col overflow-hidden">
                                            <span class="font-semibold text-sm text-slate-800 truncate"><?= htmlspecialchars($k->nama) ?></span>
                                            <?php if($k->kelas): ?>
                                                <span class="text-xs text-slate-500 truncate"><?= htmlspecialchars($k->kelas) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="badge-container flex items-center gap-2 flex-shrink-0 ml-2">
                                        <?php if(isset($k->unread) && $k->unread > 0): ?>
                                            <span class="badge-unread bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $k->unread ?></span>
                                        <?php endif; ?>
                                        <span class="bg-primary/10 text-primary text-[10px] font-semibold px-2 py-0.5 rounded-md capitalize"><?= $k->role ?></span>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="p-4 text-center text-slate-500 text-sm">Belum ada kontak</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Chat Room -->
        <div id="chat-box-card" class="lg:col-span-2 w-full lg:block hidden">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-[600px]">
                <!-- Chat Header -->
                <div class="bg-primary text-white p-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 max-w-[80%]">
                        <button type="button" id="btn-back-to-contacts" class="lg:hidden flex items-center justify-center p-1 rounded-full hover:bg-white/20 text-white mr-1 border-none bg-transparent cursor-pointer">
                            <span class="material-symbols-outlined text-lg text-white">arrow_back</span>
                        </button>
                        <h3 class="font-semibold truncate m-0 text-white" id="judul-chat-room">Memuat...</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" id="cari-pesan" placeholder="Cari pesan..." class="hidden text-sm text-slate-800 px-3 py-1 rounded-md border-none focus:ring-2 focus:ring-white/50 w-40">
                        <button type="button" id="btn-toggle-cari-pesan" class="p-1 rounded-md hover:bg-white/20 transition-colors" title="Cari pesan">
                            <span class="material-symbols-outlined text-sm">search</span>
                        </button>
                    </div>
                </div>
                
                <!-- Chat Body -->
                <div class="flex-1 overflow-y-auto p-4 bg-[#e5ddd5] custom-scrollbar" id="area-pesan">
                    <div class="text-center text-slate-500 mt-4 text-sm">Memuat obrolan...</div>
                </div>

                <!-- Chat Footer -->
                <div class="p-4 bg-slate-50 border-t border-slate-200">
                    <form id="form-kirim-pesan" method="post" class="flex gap-2">
                        <input type="text" name="pesan" id="input-pesan" placeholder="Ketik pesan Anda di sini..." class="flex-1 text-sm border border-slate-300 rounded-full px-4 py-2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" autocomplete="off" required>
                        <button type="submit" id="btn-kirim" class="bg-primary hover:bg-primary/90 text-white rounded-full w-10 h-10 flex items-center justify-center transition-colors shadow-sm flex-shrink-0">
                            <span class="material-symbols-outlined text-sm text-white">send</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Scripts -->
<style>
    .chat-bubble { max-width: 75%; padding: 8px 12px; border-radius: 12px; position: relative; margin-bottom: 12px; clear: both; }
    .chat-bubble-right { float: right; background-color: #dcf8c6; border-top-right-radius: 0; box-shadow: 0 1px 1px rgba(0,0,0,0.1); }
    .chat-bubble-left { float: left; background-color: #ffffff; border-top-left-radius: 0; box-shadow: 0 1px 1px rgba(0,0,0,0.1); }
    .chat-meta { font-size: 10px; color: #999; margin-top: 4px; display: flex; justify-content: flex-end; }
    .chat-name { font-size: 11px; font-weight: 600; color: #128c7e; margin-bottom: 2px; }
    .kontak-item.active { background-color: #f1f5f9; border-left: 4px solid var(--primary-color); }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 10px; }
</style>

<script type="text/javascript">
var aktif_lawan_bicara_id = null;
var aktif_role = '';
var aktif_nama = '';
var kelas_id_komunitas = null;
var current_user_id = <?= $user->id ?>;
var previous_messages_count = 0;

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
                        var bubbleClass = isRight ? 'chat-bubble-right' : 'chat-bubble-left';
                        
                        var displayName = isRight ? 'Anda' : msg.nama_pengirim;
                        if (!isRight && msg.kelas_pengirim) {
                            displayName += ' - ' + msg.kelas_pengirim;
                        }
                        
                        html += '<div class="chat-bubble ' + bubbleClass + '">' +
                                (!isRight ? '<div class="chat-name">' + displayName + '</div>' : '') +
                                '<div class="chat-text text-sm text-slate-800">' + msg.pesan + '</div>' +
                                '<div class="chat-meta">' + msg.created_at + '</div>' +
                                '</div>';
                    });
                    html += '<div style="clear:both;"></div>';
                } else {
                    html = '<div class="text-center text-slate-500 mt-4 text-sm bg-white/60 inline-block px-4 py-2 rounded-full mx-auto table shadow-sm">Mulai obrolan baru dengan ' + aktif_nama + '</div>';
                }

                if (response.tampilkan_tombol_wa && response.wa_number) {
                    html += '<div class="mt-4 text-center" style="clear:both;">' +
                            '  <a href="https://wa.me/' + response.wa_number + '" target="_blank" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors shadow-sm">' +
                            '    <span class="material-symbols-outlined text-sm">chat</span> Hubungi via WhatsApp' +
                            '  </a>' +
                            '</div>';
                }

                $('#area-pesan').html(html);

                var searchKey = $('#cari-pesan').val().toLowerCase();
                if (searchKey !== '') {
                    $('#area-pesan .chat-bubble').each(function() {
                        var hasText = $(this).find('.chat-text').text().toLowerCase().indexOf(searchKey) > -1;
                        $(this).toggle(hasText);
                    });
                }

                var current_count = messages ? messages.length : 0;
                if (current_count > previous_messages_count) {
                    scrollToBottom();
                    previous_messages_count = current_count;
                }

                $('#daftar-kontak .kontak-item').each(function() {
                    var targetId = $(this).data('id');
                    var targetRole = $(this).data('role');
                    if (targetRole !== 'komunitas') {
                        var count = (response.unread_counts && response.unread_counts[targetId]) ? response.unread_counts[targetId] : 0;
                        var badgeContainer = $(this).find('.badge-container');
                        var badge = $(this).find('.badge-unread');
                        if (count > 0) {
                            if (badge.length === 0) {
                                badgeContainer.prepend('<span class="badge-unread bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">' + count + '</span>');
                            } else {
                                badge.text(count).show();
                            }
                        } else {
                            badge.remove();
                        }
                    }
                });
            },
            error: function() {
                console.log('Terjadi kesalahan saat memuat obrolan.');
            }
        });
    }

    $('#cari-kontak').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#daftar-kontak .kontak-item').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    $('#btn-toggle-cari-pesan').on('click', function() {
        $('#cari-pesan').toggleClass('hidden').val('').focus();
        $('#area-pesan .chat-bubble').show();
    });

    $('#cari-pesan').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        if (value === '') {
            $('#area-pesan .chat-bubble').show();
        } else {
            $('#area-pesan .chat-bubble').each(function() {
                var hasText = $(this).find('.chat-text').text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(hasText);
            });
        }
    });

    // Fungsi untuk menset detail obrolan tanpa men-trigger efek transisi slide mobile (untuk startup load)
    function selectContactSilent(el) {
        $('.kontak-item').removeClass('active');
        el.addClass('active');

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
        $('#area-pesan').html('<div class="text-center text-slate-500 mt-4 text-sm bg-white/60 inline-block px-4 py-2 rounded-full mx-auto table shadow-sm">Memuat obrolan...</div>');
        $('#cari-pesan').addClass('hidden').val('');

        previous_messages_count = 0;
        loadChatHistory();
    }

    // Contact selection
    $('.kontak-item').on('click', function() {
        // Jika mode mobile (lebar layar < 1024px), sembunyikan daftar kontak dan tampilkan chat box
        if ($(window).width() < 1024) {
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
        if ($(window).width() >= 1024) {
            firstContact.click();
        } else {
            selectContactSilent(firstContact);
        }
    } else {
        $('#judul-chat-room').text('Tidak ada kontak');
        $('#area-pesan').html('<div class="text-center text-slate-500 mt-4 text-sm bg-white/60 inline-block px-4 py-2 rounded-full mx-auto table shadow-sm">Belum ada kontak obrolan tersedia.</div>');
    }

    setInterval(loadChatHistory, 3000);

    $('#form-kirim-pesan').on('submit', function(e) {
        e.preventDefault();

        var isiPesan = $('#input-pesan').val();
        var btnKirim = $('#btn-kirim');

        if (isiPesan.trim() !== '') {
            btnKirim.prop('disabled', true);

            var postData = {
                pesan: isiPesan,
                pengirim_role: '<?= $this->ion_auth->in_group("siswa") ? "siswa" : ($this->ion_auth->in_group("guru") ? "guru" : "admin") ?>',
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
</script>
