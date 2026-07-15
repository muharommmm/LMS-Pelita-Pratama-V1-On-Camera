<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1><?= $judul ?></h1>
                <small><?= $subjudul ?></small>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Column 1: Contacts List -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">Daftar Obrolan</h3>
                        </div>
                        <!-- Search Box for Contacts -->
                        <div class="p-2 border-bottom bg-light">
                            <div class="input-group">
                                <input type="text" id="cari-kontak" placeholder="Cari nama kontak..." class="form-control form-control-sm">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search text-xs"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0" style="height: 450px; overflow-y: auto;">
                            <ul class="list-group list-group-flush" id="daftar-kontak">
                                <!-- Individual & Community Contacts rendered dynamically -->
                                <?php if(isset($kontak) && !empty($kontak)): ?>
                                    <?php foreach($kontak as $k): ?>
                                    <li class="list-group-item kontak-item" style="cursor: pointer;" data-id="<?= $k->id_user ?>" data-role="<?= $k->role ?>" data-nama="<?= htmlspecialchars($k->nama) ?>" data-kelas="<?= $k->kelas ? htmlspecialchars($k->kelas) : '' ?>">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="text-truncate">
                                                <i class="fas <?= $k->role == 'komunitas' ? 'fa-users text-primary' : 'fa-user-circle text-secondary' ?> mr-2"></i> 
                                                <strong><?= htmlspecialchars($k->nama) ?></strong>
                                                <?php if($k->kelas): ?>
                                                    <small class="text-muted font-weight-normal text-xs ml-1">- <?= htmlspecialchars($k->kelas) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="badge-container d-flex align-items-center">
                                                <?php if(isset($k->unread) && $k->unread > 0): ?>
                                                    <span class="badge badge-danger badge-unread float-right mr-2"><?= $k->unread ?></span>
                                                <?php endif; ?>
                                                <span class="badge badge-info text-xs"><?= ucfirst($k->role) ?></span>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted text-center"><small>Belum ada kontak</small></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Chat Room -->
                <div class="col-md-8">
                    <div class="card direct-chat direct-chat-primary shadow-sm">
                        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                            <h3 class="card-title" id="judul-chat-room">Memuat...</h3>
                            <div class="card-tools d-flex align-items-center">
                                <input type="text" id="cari-pesan" placeholder="Cari isi pesan..." class="form-control form-control-sm mr-2" style="width: 150px; display: none; height: 28px;">
                                <button type="button" class="btn btn-tool" id="btn-toggle-cari-pesan" title="Cari pesan">
                                    <i class="fas fa-search text-white"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="direct-chat-messages" id="area-pesan" style="height: 450px; overflow-y: auto;">
                                <div class="text-center text-muted mt-3"><small>Memuat obrolan...</small></div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <form id="form-kirim-pesan" method="post">
                                <div class="input-group">
                                    <input type="text" name="pesan" id="input-pesan" placeholder="Ketik pesan Anda di sini..." class="form-control" autocomplete="off" required>
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-primary" id="btn-kirim">
                                            <i class="fas fa-paper-plane"></i> Kirim
                                        </button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- jQuery -->
<script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>

<script type="text/javascript">
// Global Chat State
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
        // Prevent request if no active chat session is set
        if (aktif_role === '') {
            return;
        }

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
                        var bubbleAlign = isRight ? 'right' : '';
                        var nameAlign = isRight ? 'float-right' : 'float-left';
                        var timeAlign = isRight ? 'float-left' : 'float-right';
                        
                        var displayName = isRight ? 'Anda' : msg.nama_pengirim;
                        if (!isRight && msg.kelas_pengirim) {
                            displayName += ' - ' + msg.kelas_pengirim;
                        }
                        
                        html += '<div class="direct-chat-msg ' + bubbleAlign + '">' +
                                '  <div class="direct-chat-infos clearfix">' +
                                '    <span class="direct-chat-name ' + nameAlign + '">' + displayName + '</span>' +
                                '    <span class="direct-chat-timestamp ' + timeAlign + '">' + msg.created_at + '</span>' +
                                '  </div>' +
                                '  <img class="direct-chat-img" src="<?= base_url() ?>' + msg.foto_pengirim + '" alt="avatar">' +
                                '  <div class="direct-chat-text">' +
                                msg.pesan +
                                '  </div>' +
                                '</div>';
                    });
                } else {
                    html = '<div class="text-center text-muted mt-3"><small>Mulai obrolan baru dengan ' + aktif_nama + '</small></div>';
                }

                // Append WhatsApp Fallback if triggered
                if (response.tampilkan_tombol_wa && response.wa_number) {
                    html += '<div class="wa-fallback-container my-3">' +
                            '  <a href="https://wa.me/' + response.wa_number + '" target="_blank" class="btn btn-success btn-sm btn-block">' +
                            '    <i class="fab fa-whatsapp mr-1"></i> Guru belum membalas > 3 days. Hubungi via WhatsApp' +
                            '  </a>' +
                            '</div>';
                }

                $('#area-pesan').html(html);

                // Run message filter if text search is active
                var searchKey = $('#cari-pesan').val().toLowerCase();
                if (searchKey !== '') {
                    $('#area-pesan .direct-chat-msg').each(function() {
                        var hasText = $(this).find('.direct-chat-text').text().toLowerCase().indexOf(searchKey) > -1;
                        $(this).toggle(hasText);
                    });
                }

                // Scroll to bottom if new messages arrived
                var current_count = messages ? messages.length : 0;
                if (current_count > previous_messages_count) {
                    scrollToBottom();
                    previous_messages_count = current_count;
                }

                // Update unread count badges dynamically
                $('#daftar-kontak .kontak-item').each(function() {
                    var targetId = $(this).data('id');
                    var targetRole = $(this).data('role');
                    if (targetRole !== 'komunitas') {
                        var count = (response.unread_counts && response.unread_counts[targetId]) ? response.unread_counts[targetId] : 0;
                        var badgeContainer = $(this).find('.badge-container');
                        var badge = $(this).find('.badge-unread');
                        if (count > 0) {
                            if (badge.length === 0) {
                                badgeContainer.prepend('<span class="badge badge-danger badge-unread float-right mr-2">' + count + '</span>');
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

    // Filter Contacts dynamically
    $('#cari-kontak').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#daftar-kontak .kontak-item').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Toggle and Filter messages
    $('#btn-toggle-cari-pesan').on('click', function() {
        $('#cari-pesan').toggle().val('').focus();
        $('#area-pesan .direct-chat-msg').show();
    });

    // Realtime message filter
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

    // Handle Contact Selection
    $('.kontak-item').on('click', function() {
        $('.kontak-item').removeClass('active bg-light text-primary');
        $(this).addClass('active bg-light text-primary');

        var targetId = $(this).data('id');
        var targetRole = $(this).data('role');
        var targetNama = $(this).data('nama');
        var targetKelas = $(this).data('kelas');

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

        // Instantly clear/reset unread badge locally for active room
        $(this).find('.badge-unread').remove();

        $('#judul-chat-room').text(aktif_nama);
        $('#area-pesan').html('<div class="text-center text-muted mt-3"><small>Memuat obrolan...</small></div>');
        
        // Hide message search when changing contacts
        $('#cari-pesan').hide().val('');

        previous_messages_count = 0; // Reset scroll tracker
        loadChatHistory();
    });

    // Automatically click first contact on load to populate
    if ($('#daftar-kontak .kontak-item').length > 0) {
        $('#daftar-kontak .kontak-item').first().click();
    } else {
        $('#judul-chat-room').text('Tidak ada kontak');
        $('#area-pesan').html('<div class="text-center text-muted mt-3"><small>Belum ada kontak obrolan tersedia.</small></div>');
    }

    // Poll chat updates every 3 seconds
    setInterval(loadChatHistory, 3000);

    // Form Submission
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
