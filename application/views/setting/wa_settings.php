<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fab fa-whatsapp text-success mr-2"></i><?= $judul ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <!-- Pengaturan API Token -->
            <div class="card my-shadow">
                <div class="card-header bg-success text-white">
                    <div class="card-title">
                        <h6><i class="fas fa-cog mr-1"></i> Konfigurasi Fonnte API</h6>
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-light btn-sm" onclick="saveWaSettings()">
                            <i class="fa fa-save mr-1"></i>Simpan Pengaturan
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="form-wa-settings">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label><i class="fas fa-key mr-1 text-warning"></i> API Token Fonnte <span class="text-danger">*</span></label>
                                <input type="text" name="wa_api_token" id="wa_api_token" class="form-control"
                                       value="<?= isset($setting->wa_api_token) ? $setting->wa_api_token : '' ?>"
                                       placeholder="Masukkan token dari fonnte.com">
                                <small class="text-muted">Dapatkan token di <a href="https://fonnte.com" target="_blank">fonnte.com</a> setelah menghubungkan nomor WA pengirim.</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label><i class="far fa-clock mr-1 text-info"></i> Jam Pengiriman Otomatis</label>
                                <input type="time" name="wa_reminder_time" id="wa_reminder_time" class="form-control"
                                       value="<?= isset($setting->wa_reminder_time) ? $setting->wa_reminder_time : '08:30' ?>">
                                <small class="text-muted">Default: 08:30 WIB</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label><i class="fas fa-toggle-on mr-1 text-primary"></i> Status Reminder Otomatis</label>
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" class="custom-control-input" id="wa_reminder_enabled" name="wa_reminder_enabled" value="1"
                                        <?= (isset($setting->wa_reminder_enabled) && $setting->wa_reminder_enabled == 1) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="wa_reminder_enabled">
                                        <span id="status-label"><?= (isset($setting->wa_reminder_enabled) && $setting->wa_reminder_enabled == 1) ? 'Aktif' : 'Nonaktif' ?></span>
                                    </label>
                                </div>
                                <small class="text-muted">Jika aktif, cron job akan mengirim reminder setiap hari.</small>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <!-- Info Cron Job -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Pengaturan Cron Job:</strong> Untuk menjalankan reminder otomatis, tambahkan cron job berikut di server Anda:
                        <pre class="mt-2 mb-0 bg-dark text-white p-2 rounded" style="font-size: 12px;">
# Kirim reminder jadwal via WA setiap hari jam 08:30
30 8 * * * cd /var/www/html && php index.php cron send_jadwal_reminder >> /var/log/wa_reminder.log 2>&1</pre>
                        <small class="text-muted mt-1 d-block">Atau gunakan cron external: <code>curl -s "<?= base_url('cron/send_jadwal_reminder?token=lms_pelita_cron_2026') ?>"</code></small>
                    </div>
                </div>
            </div>

            <!-- Tombol Kirim Manual -->
            <div class="card my-shadow">
                <div class="card-header bg-primary text-white">
                    <div class="card-title">
                        <h6><i class="fas fa-paper-plane mr-1"></i> Kirim Reminder & Broadcast Manual</h6>
                    </div>
                </div>
                <div class="card-body text-center py-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="mb-2">Kirim reminder jadwal mengajar hari ini ke semua tutor via WhatsApp.</p>
                            <button class="btn btn-md btn-success" id="btn-kirim-reminder" onclick="kirimReminderManual()">
                                <i class="fab fa-whatsapp mr-2"></i> Kirim Reminder Tutor
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-2">Kirim broadcast jadwal pelajaran harian ke semua Grup WA Kelas terdaftar.</p>
                            <button class="btn btn-md btn-info" id="btn-kirim-broadcast-kelas" onclick="kirimBroadcastKelasManual()">
                                <i class="fab fa-whatsapp mr-2"></i> Broadcast Jadwal Grup Kelas
                            </button>
                        </div>
                    </div>
                    <div id="hasil-kirim" class="mt-3" style="display:none;"></div>
                </div>
            </div>

            <!-- Log Pengiriman -->
            <div class="card my-shadow">
                <div class="card-header">
                    <div class="card-title">
                        <h6><i class="fas fa-history mr-1"></i> Log Pengiriman Terakhir</h6>
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-danger btn-sm" onclick="clearLogs()">
                            <i class="fas fa-trash mr-1"></i>Hapus Semua Log
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:30px">#</th>
                                    <th>Waktu</th>
                                    <th>No HP</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($logs) && !empty($logs)): ?>
                                    <?php $no = 1; foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><small><?= $log->sent_at ?></small></td>
                                            <td><?= $log->no_hp ?></td>
                                            <td><span class="badge badge-info"><?= $log->type ?></span></td>
                                            <td>
                                                <?php if ($log->status === 'sent'): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Terkirim</span>
                                                <?php elseif ($log->status === 'failed'): ?>
                                                    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Gagal</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><small class="text-muted" style="max-width:200px; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars(substr($log->response ?? '', 0, 100)) ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada log pengiriman.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
// Toggle label
$('#wa_reminder_enabled').on('change', function() {
    $('#status-label').text(this.checked ? 'Aktif' : 'Nonaktif');
});

function saveWaSettings() {
    var data = {
        wa_api_token: $('#wa_api_token').val(),
        wa_reminder_enabled: $('#wa_reminder_enabled').is(':checked') ? 1 : 0,
        wa_reminder_time: $('#wa_reminder_time').val(),
        csrf_token: '<?= $this->security->get_csrf_hash() ?>'
    };

    $.post('<?= base_url("wa_settings/save") ?>', data, function(res) {
        if (res.status) {
            Swal.fire({icon: 'success', title: 'Berhasil', text: res.message, timer: 2000, showConfirmButton: false});
        } else {
            Swal.fire({icon: 'error', title: 'Gagal', text: res.message});
        }
    }, 'json').fail(function() {
        // Fallback jika SweetAlert tidak tersedia
        alert('Pengaturan berhasil disimpan!');
        location.reload();
    });
}

function kirimReminderManual() {
    var btn = $('#btn-kirim-reminder');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...');
    $('#hasil-kirim').hide();

    $.post('<?= base_url("cron/send_jadwal_reminder") ?>', {
        manual: '1',
        csrf_token: '<?= $this->security->get_csrf_hash() ?>'
    }, function(res) {
        btn.prop('disabled', false).html('<i class="fab fa-whatsapp mr-2"></i> Kirim Reminder Sekarang');

        var html = '';
        if (res.status) {
            html += '<div class="alert alert-success">';
            html += '<i class="fas fa-check-circle mr-1"></i> ' + res.message;
            html += '</div>';

            if (res.data && res.data.details && res.data.details.length > 0) {
                html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
                html += '<thead><tr><th>Nama Tutor</th><th>No HP</th><th>Status</th><th>Jadwal</th></tr></thead><tbody>';
                res.data.details.forEach(function(d) {
                    var badge = d.status === 'sent' ? 'badge-success' : (d.status === 'skipped' ? 'badge-warning' : 'badge-danger');
                    html += '<tr>';
                    html += '<td>' + d.nama + '</td>';
                    html += '<td>' + (d.no_hp || '-') + '</td>';
                    html += '<td><span class="badge ' + badge + '">' + d.status + '</span></td>';
                    html += '<td>' + (d.jadwal || (d.reason || '-')) + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
            }
        } else {
            html += '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-1"></i> ' + res.message + '</div>';
        }

        $('#hasil-kirim').html(html).show();
    }, 'json').fail(function(xhr) {
        btn.prop('disabled', false).html('<i class="fab fa-whatsapp mr-2"></i> Kirim Reminder Sekarang');
        $('#hasil-kirim').html('<div class="alert alert-danger">Terjadi kesalahan server. Cek konsol untuk detail.</div>').show();
        console.error(xhr.responseText);
    });
}

function clearLogs() {
    if (!confirm('Apakah Anda yakin ingin menghapus semua log pengiriman?')) return;
    $.post('<?= base_url("wa_settings/clear_logs") ?>', {
        csrf_token: '<?= $this->security->get_csrf_hash() ?>'
    }, function(res) {
        if (res.status) {
            location.reload();
        }
    }, 'json');
}

function kirimBroadcastKelasManual() {
    if (!confirm('Kirim broadcast jadwal pelajaran harian ke semua Grup WA Kelas terhubung sekarang?')) return;
    var btn = $('#btn-kirim-broadcast-kelas');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...');

    $.get('<?= base_url("cron/send_jadwal_kelas_reminder?manual=1") ?>', function(res) {
        btn.prop('disabled', false).html('<i class="fab fa-whatsapp mr-2"></i> Broadcast Jadwal Grup Kelas');

        var html = '';
        if (res.status) {
            html += '<div class="alert alert-info"><i class="fas fa-check-circle mr-1"></i> ' + res.message + '</div>';
            if (res.data && res.data.details && res.data.details.length > 0) {
                html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
                html += '<thead><tr><th>Nama Kelas</th><th>Group ID</th><th>Status</th><th>Keterangan</th></tr></thead><tbody>';
                res.data.details.forEach(function(d) {
                    var badge = d.status === 'sent' ? 'badge-success' : (d.status === 'skipped' ? 'badge-warning' : 'badge-danger');
                    html += '<tr>';
                    html += '<td>' + (d.kelas || '-') + '</td>';
                    html += '<td>' + (d.group || '-') + '</td>';
                    html += '<td><span class="badge ' + badge + '">' + d.status + '</span></td>';
                    html += '<td>' + (d.jadwal || (d.reason || '-')) + '</td>';
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
            }
        } else {
            html += '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-1"></i> ' + res.message + '</div>';
        }
        $('#hasil-kirim').html(html).show();
    }, 'json').fail(function(xhr) {
        btn.prop('disabled', false).html('<i class="fab fa-whatsapp mr-2"></i> Broadcast Jadwal Grup Kelas');
        $('#hasil-kirim').html('<div class="alert alert-danger">Terjadi kesalahan server.</div>').show();
    });
}
</script>
