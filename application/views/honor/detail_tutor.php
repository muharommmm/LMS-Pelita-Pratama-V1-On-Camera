<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1>Detail Rincian Honor Tutor</h1>
                <a href="<?= base_url('honor') ?>" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Rekapitulasi
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Alert Messages -->
            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-check text-white"></i> <?= $this->session->flashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-ban text-white"></i> <?= $this->session->flashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Tutor Profil Card -->
            <div class="card card-default my-shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th style="width: 150px;">Nama Tutor</th>
                                    <td>: <strong><?= htmlspecialchars($tutor->nama_guru) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>NIP</th>
                                    <td>: <?= htmlspecialchars($tutor->nip ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Kode Tutor / Guru</th>
                                    <td>: <span class="badge badge-info"><?= htmlspecialchars($tutor->kode_guru ?? '-') ?></span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4 border-left">
                            <h5>Filter Rentang Tanggal</h5>
                            <?= form_open('honor/detail_tutor/' . $tutor->id_guru, ['method' => 'GET', 'class' => 'form-inline']) ?>
                            <div class="form-group mb-2 mr-2">
                                <label class="mr-2">Mulai:</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $start_date ?>" required>
                            </div>
                            <div class="form-group mb-2 mr-2">
                                <label class="mr-2">s/d:</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $end_date ?>" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mb-2 mr-1">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <!-- Print Slip Link based on active filtered date range -->
                            <a href="<?= base_url('honor/cetak_slip/' . $tutor->id_guru . '?start=' . $start_date . '&end=' . $end_date) ?>" class="btn btn-sm btn-danger mb-2" target="_blank">
                                <i class="fas fa-print mr-1"></i> Cetak Slip
                            </a>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info-boxes for Totals -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="info-box bg-light my-shadow">
                        <span class="info-box-icon bg-danger"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-muted">Belum Dibayar (Siap Bayar)</span>
                            <span class="info-box-number text-danger" style="font-size: 1.25rem;">Rp <?= number_format($total_unpaid, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-light my-shadow">
                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-muted">Sudah Dibayar (Paid)</span>
                            <span class="info-box-number text-success" style="font-size: 1.25rem;">Rp <?= number_format($total_paid, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-light my-shadow">
                        <span class="info-box-icon bg-info"><i class="fas fa-calculator"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-muted">Total Honorarium Bulan Ini</span>
                            <span class="info-box-number text-info" style="font-size: 1.25rem;">Rp <?= number_format($total_honor, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List of Honor Records -->
            <div class="card card-default my-shadow">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="detailTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="unpaid-tab" data-toggle="pill" href="#unpaid-pane" role="tab" aria-controls="unpaid-pane" aria-selected="true">
                                <i class="fas fa-clock mr-1 text-danger"></i> Rincian Belum Dibayar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="paid-tab" data-toggle="pill" href="#paid-pane" role="tab" aria-controls="paid-pane" aria-selected="false">
                                <i class="fas fa-check-circle mr-1 text-success"></i> Riwayat Pembayaran (Paid)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="payout-tab" data-toggle="pill" href="#payout-pane" role="tab" aria-controls="payout-pane" aria-selected="false">
                                <i class="fas fa-history mr-1 text-primary"></i> Riwayat Pencairan Dana
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="detailTabsContent">
                        <!-- Pane Belum Dibayar -->
                        <div class="tab-pane fade show active" id="unpaid-pane" role="tabpanel" aria-labelledby="unpaid-tab">
                            
                            <!-- Bulk Action Panel -->
                            <div class="d-flex align-items-center mb-3 bg-light p-2 border rounded">
                                <select id="bulk-action" class="form-control form-control-sm w-auto mr-2">
                                    <option value="">-- Pilih Aksi Massal --</option>
                                    <option value="approved">Approve Terpilih</option>
                                    <option value="rejected">Reject Terpilih</option>
                                    <option value="delete">Hapus Terpilih</option>
                                </select>
                                <input type="text" id="bulk-notes" class="form-control form-control-sm w-50 mr-2" placeholder="Catatan Massal (Opsional)">
                                <button type="button" id="btn-bulk-execute" class="btn btn-sm btn-primary font-weight-bold shadow-sm">
                                    <i class="fas fa-check mr-1"></i> Eksekusi
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="unpaidTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 40px;"><input type="checkbox" id="check-all" class="cursor-pointer"></th>
                                            <th style="width: 50px;">No</th>
                                            <th>Aktivitas</th>
                                            <th>Mapel & Kelas</th>
                                            <th>Sesi/Qty</th>
                                            <th>Tarif</th>
                                            <th>Nominal Awal</th>
                                            <th>Nominal Koreksi</th>
                                            <th>Status</th>
                                            <th>Catatan Admin</th>
                                            <th>Tanggal Sesi</th>
                                            <th style="width: 120px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $all_unpaid = array_merge($pending_records, $approved_records);
                                        if (!empty($all_unpaid)) :
                                            $no = 1;
                                            foreach ($all_unpaid as $rec) :
                                        ?>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="check-item cursor-pointer" name="honor_ids[]" value="<?= $rec->id_honor_record ?>">
                                                </td>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($type_names[$rec->type]) ?></td>
                                                <td>
                                                    <?= !empty($rec->nama_mapel) ? htmlspecialchars($rec->nama_mapel) : '-' ?>
                                                    <br>
                                                    <small class="text-muted"><?= !empty($rec->nama_kelas) ? htmlspecialchars($rec->nama_kelas) : '-' ?></small>
                                                </td>
                                                <td class="text-center"><?= $rec->qty ?> sesi / unit</td>
                                                <td class="text-right">Rp <?= number_format($rec->rate, 0, ',', '.') ?></td>
                                                <td class="text-right text-muted">Rp <?= number_format($rec->amount, 0, ',', '.') ?></td>
                                                <td class="text-right font-weight-bold text-danger">
                                                    <?= ($rec->adjusted_amount !== null && floatval($rec->adjusted_amount) > 0) ? 'Rp ' . number_format($rec->adjusted_amount, 0, ',', '.') : '-' ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($rec->status == 'approved') : ?>
                                                        <span class="badge badge-primary">Approved</span>
                                                    <?php elseif ($rec->status == 'rejected') : ?>
                                                        <span class="badge badge-danger">Rejected</span>
                                                    <?php else : ?>
                                                        <span class="badge badge-warning">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($rec->admin_notes ?? '-') ?></td>
                                                <td class="text-center"><?= date('d-m-Y', strtotime($rec->created_at)) ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-xs btn-info btn-adjust mr-1"
                                                        data-id="<?= $rec->id_honor_record ?>"
                                                        data-type="<?= $rec->type ?>"
                                                        data-amount="<?= $rec->amount ?>"
                                                        data-adjusted="<?= $rec->adjusted_amount ?>"
                                                        data-status="<?= $rec->status ?>"
                                                        data-notes="<?= htmlspecialchars($rec->admin_notes ?? '') ?>">
                                                        <i class="fas fa-edit mr-1"></i> Koreksi
                                                    </button>
                                                     <a href="<?= base_url('honor/hapus_record/' . $rec->id_honor_record . '?start_date=' . urlencode($start_date) . '&end_date=' . urlencode($end_date)) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Yakin hapus permanen?');">
                                                         <i class="fas fa-trash mr-1"></i> Hapus
                                                     </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pane Sudah Dibayar -->
                        <div class="tab-pane fade" id="paid-pane" role="tabpanel" aria-labelledby="paid-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="paidTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 50px;">No</th>
                                            <th>Aktivitas</th>
                                            <th>Mapel & Kelas</th>
                                            <th>Sesi/Qty</th>
                                            <th>Tarif</th>
                                            <th>Nominal Dibayar</th>
                                            <th>Catatan Perubahan</th>
                                            <th>Bukti/ID Mutasi</th>
                                            <th>Tanggal Sesi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($paid_records)) :
                                            $no = 1;
                                            foreach ($paid_records as $rec) :
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($type_names[$rec->type]) ?></td>
                                                <td>
                                                    <?= !empty($rec->nama_mapel) ? htmlspecialchars($rec->nama_mapel) : '-' ?>
                                                    <br>
                                                    <small class="text-muted"><?= !empty($rec->nama_kelas) ? htmlspecialchars($rec->nama_kelas) : '-' ?></small>
                                                </td>
                                                <td class="text-center"><?= $rec->qty ?> sesi / unit</td>
                                                <td class="text-right">Rp <?= number_format($rec->rate, 0, ',', '.') ?></td>
                                                <td class="text-right font-weight-bold text-success">
                                                    Rp <?= number_format(($rec->adjusted_amount !== null && floatval($rec->adjusted_amount) > 0) ? $rec->adjusted_amount : $rec->amount, 0, ',', '.') ?>
                                                </td>
                                                <td><?= htmlspecialchars($rec->admin_notes ?? '-') ?></td>
                                                <td class="text-center">
                                                    <span class="badge badge-success">MUT-<?= $rec->mutation_id ?></span>
                                                </td>
                                                <td class="text-center"><?= date('d-m-Y', strtotime($rec->created_at)) ?></td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Pane Riwayat Pencairan Dana -->
                        <div class="tab-pane fade" id="payout-pane" role="tabpanel" aria-labelledby="payout-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="payoutTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 50px;">No</th>
                                            <th>Periode Mengajar</th>
                                            <th>Jumlah Pencairan</th>
                                            <th>Tanggal Transfer</th>
                                            <th>Keterangan / Bukti</th>
                                            <th>Status Konfirmasi Tutor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($mutations)) : ?>
                                            <?php $no = 1; foreach ($mutations as $mut) : ?>
                                                 <?php
                                                 $periode = '-';
                                                 $bukti = '-';
                                                 if (preg_match('/^Honorarium Periode:\s*([^\(]+)(?:\((.*)\))?$/i', $mut->notes, $matches)) {
                                                     $periode = trim($matches[1]);
                                                     $bukti = isset($matches[2]) ? trim($matches[2]) : '-';
                                                 } else {
                                                     $periode = $mut->notes;
                                                 }
                                                 ?>
                                                 <tr>
                                                     <td class="text-center"><?= $no++ ?></td>
                                                     <td><strong><?= strtoupper($periode) ?></strong></td>
                                                     <td class="text-right text-success font-weight-bold">Rp <?= number_format($mut->amount, 0, ',', '.') ?></td>
                                                     <td class="text-center"><?= date('d-m-Y H:i', strtotime($mut->transaction_date)) ?></td>
                                                     <td><?= htmlspecialchars($bukti) ?></td>
                                                    <td class="text-center">
                                                        <?php if ($mut->status_konfirmasi_tutor == 1) : ?>
                                                            <span class="badge badge-success"><i class="fas fa-check-double mr-1"></i> Diterima & Dikonfirmasi</span>
                                                        <?php else : ?>
                                                            <span class="badge badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i> Menunggu Konfirmasi Tutor</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                 <td colspan="6" class="text-center text-muted">Belum ada riwayat mutasi transfer/pembayaran untuk tutor ini.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Adjustment -->
<div class="modal fade" id="adjustModal" tabindex="-1" role="dialog" aria-labelledby="adjustModalLabel" aria-hidden="true">
    <div class="modal-dialog text-dark" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustModalLabel">Koreksi Gaji / Honorarium</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('honor/update_adjustment') ?>
            <input type="hidden" name="id_honor_record" id="id_honor_record_input">
            <input type="hidden" name="tutor_id" value="<?= $tutor->id_guru ?>">
            <input type="hidden" name="start_date" value="<?= $start_date ?>">
            <input type="hidden" name="end_date" value="<?= $end_date ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label>Jenis Kegiatan</label>
                    <select name="type" id="adjust_activity" class="form-control" required>
                        <option value="offline">Tatap Muka Offline</option>
                        <option value="online">Tatap Muka Online</option>
                        <option value="check_task">Tugas</option>
                        <option value="create_cbt">Soal Ujian Modul</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nominal Awal Sistem (Rp)</label>
                    <input type="text" id="adjust_original_amount" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="adjust_amount_input">Nominal Baru Koreksi (Rp)</label>
                    <input type="number" step="0.01" name="adjusted_amount" id="adjust_amount_input" class="form-control" placeholder="Kosongkan jika ingin kembali ke nominal awal">
                    <small class="form-text text-muted">Mengisi nilai ini akan menimpa nominal awal sistem saat pencairan dan slip gaji dicetak.</small>
                </div>
                <div class="form-group">
                    <label for="adjust_status">Ubah Status</label>
                    <select name="status" id="adjust_status" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="adjust_notes_input">Catatan Alasan Koreksi</label>
                    <textarea name="admin_notes" id="adjust_notes_input" class="form-control" rows="3" placeholder="Misalnya: Koreksi kelebihan pertemuan / penyesuaian kas daerah"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-info"><i class="fas fa-check-circle mr-1"></i> Simpan Penyesuaian</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<script>
    var masterTarif = <?= json_encode($master_tarif) ?>;
</script>

<script>
    $(document).ready(function() {

        // === 1. Inisialisasi DataTable (dengan try-catch proteksi) ===
        try {
            if ($.fn.DataTable) {
                var dtLang = {
                    "emptyTable"     : "Tidak ada data yang tersedia",
                    "info"           : "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
                    "infoEmpty"      : "Menampilkan 0 hingga 0 dari 0 entri",
                    "infoFiltered"   : "(disaring dari _MAX_ total entri)",
                    "lengthMenu"     : "Tampilkan _MENU_ entri",
                    "loadingRecords" : "Memuat...",
                    "processing"     : "Memproses...",
                    "search"         : "Cari:",
                    "zeroRecords"    : "Tidak ditemukan data yang sesuai",
                    "paginate"       : { "first": "Pertama", "last": "Terakhir", "next": "Berikut", "previous": "Sebelum" }
                };

                if ($.fn.DataTable.isDataTable('#unpaidTable')) {
                    $('#unpaidTable').DataTable().destroy();
                }
                $('#unpaidTable').DataTable({
                    "paging"    : true,
                    "ordering"  : true,
                    "info"      : true,
                    "searching" : true,
                    "retrieve"  : true,
                    "columnDefs": [
                        { "orderable": false, "targets": [0, -1] }
                    ],
                    "language"  : dtLang
                });

                if ($.fn.DataTable.isDataTable('#paidTable')) {
                    $('#paidTable').DataTable().destroy();
                }
                $('#paidTable').DataTable({
                    "paging"    : true,
                    "ordering"  : true,
                    "info"      : true,
                    "searching" : true,
                    "retrieve"  : true,
                    "language"  : dtLang
                });
            }
        } catch (err) {
            console.warn('[DataTable Init Error]', err);
        }

        // === 2. Event Delegation untuk tombol Koreksi ===
        $(document).on('click', '.btn-adjust', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $btn     = $(this);
            var id       = $btn.attr('data-id');
            var type     = $btn.attr('data-type');
            var amount   = parseFloat($btn.attr('data-amount')) || 0;
            var adjusted = $btn.attr('data-adjusted');
            var status   = $btn.attr('data-status');
            var notes    = $btn.attr('data-notes');

            console.log('[KoreksiModal] id =', id, '| type =', type, '| status =', status);

            $('#id_honor_record_input').val(id);
            $('#adjust_activity').val(type);
            $('#adjust_original_amount').val('Rp ' + amount.toLocaleString('id-ID'));
            $('#adjust_amount_input').val((adjusted && adjusted !== 'null' && adjusted !== '') ? adjusted : '');
            $('#adjust_status').val(status);
            $('#adjust_notes_input').val((notes && notes !== 'null') ? notes : '');

            $('#adjustModal').modal('show');
        }); // <<< PENUTUP btn-adjust

        // === 3. Event Listener untuk Perubahan Jenis Kegiatan ===
        $('#adjust_activity').on('change', function() {
            var selectedType = $(this).val();
            var newTariff = masterTarif[selectedType] || 0;

            $('#adjust_original_amount').val('Rp ' + newTariff.toLocaleString('id-ID'));

            var $input = $('#adjust_original_amount');
            $input.css('background-color', '#e8f0fe');
            setTimeout(function() {
                $input.css('background-color', '');
            }, 400);
        }); // <<< PENUTUP adjust_activity change

        // === 4. Fitur Bulk Action - Checkbox (Event Delegation) ===
        $(document).on('change', '#check-all', function() {
            var isChecked = $(this).is(':checked');
            $('.check-item').prop('checked', isChecked);
            console.log('[Bulk] Check-all toggled:', isChecked);
        }); // <<< PENUTUP check-all

        $(document).on('change', '.check-item', function() {
            var total = $('.check-item').length;
            var checked = $('.check-item:checked').length;
            $('#check-all').prop('checked', (total === checked));
        }); // <<< PENUTUP check-item

        // === 5. Fitur Bulk Action - Tombol Eksekusi (Event Delegation) ===
        $(document).on('click', '#btn-bulk-execute', function(e) {
            e.preventDefault();
            console.log('[Bulk] Tombol Eksekusi Massal diklik!');

            var action = $('#bulk-action').val();
            var catatan = $('#bulk-notes').val();

            var selectedIds = $('.check-item:checked').map(function() {
                return this.value;
            }).get();

            console.log('[Bulk] Action:', action, '| IDs:', selectedIds);

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal 1 data terlebih dahulu!'
                });
                return;
            }

            if (!action) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih aksi massal yang ingin dilakukan!'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Eksekusi',
                text: 'Apakah Anda yakin ingin memproses ' + selectedIds.length + ' data terpilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Eksekusi!',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.value || result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });

                    var csrfData = {};
                    csrfData['honor_ids'] = selectedIds.join(',');
                    csrfData['action'] = action;
                    csrfData['catatan'] = catatan;
                    csrfData['<?= $this->security->get_csrf_token_name() ?>'] = '<?= $this->security->get_csrf_hash() ?>';

                    $.ajax({
                        url: '<?= base_url("honor/bulk_action") ?>',
                        type: 'POST',
                        data: csrfData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    title: "Sukses",
                                    text: response.message,
                                    icon: "success",
                                    showConfirmButton: true
                                }).then(function() {
                                    // Gunakan function() biasa, hindari arrow function =>
                                    window.location.reload();
                                });
                                
                                // Failsafe: Jika then() diblokir oleh bug versi SweetAlert lama, refresh otomatis setelah 2 detik
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                                
                            } else {
                                Swal.fire("Gagal", response.message, "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('[Bulk AJAX Error]', xhr.responseText || error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Sistem',
                                text: 'Terjadi kesalahan saat memproses data. Silakan cek console (F12).'
                            });
                        }
                    });
                }
            });
        }); // <<< PENUTUP btn-bulk-execute

    }); // <<< PENUTUP document.ready
</script>
