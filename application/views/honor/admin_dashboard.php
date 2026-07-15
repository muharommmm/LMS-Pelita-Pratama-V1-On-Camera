<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1><?= $judul ?></h1>
                <div>
                    <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#inputMassalModal">
                        <i class="fas fa-plus-circle mr-1"></i> Input Massal Honor
                    </button>
                    <button type="button" class="btn btn-sm btn-danger mr-2" data-toggle="modal" data-target="#printRekapModal">
                        <i class="fas fa-print mr-1"></i> Cetak Rekap Yayasan
                    </button>
                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#rateSettingModal">
                        <i class="fas fa-cog mr-1"></i> Pengaturan Tarif Honor
                    </button>
                </div>
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

            <!-- Filter Form -->
            <div class="card card-default my-shadow mb-4">
                <div class="card-body">
                    <?= form_open('honor', ['method' => 'GET', 'class' => 'form-inline']) ?>
                        <div class="form-group mr-3">
                            <label for="start_date" class="mr-2">Periode Mulai:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="<?= $start_date ?>" required>
                        </div>
                        <div class="form-group mr-3">
                            <label for="end_date" class="mr-2">s/d:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="<?= $end_date ?>" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-filter mr-1"></i> Tampilkan
                        </button>
                    <?= form_close() ?>
                </div>
            </div>

            <!-- Rekapitulasi Honor Tutor -->
            <div class="card card-default my-shadow mb-4">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Rekapitulasi Honorarium Tutor Siap Bayar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="tutorSummaryTable">
                            <thead>
                                <tr class="text-center">
                                     <th style="width: 50px;">No</th>
                                     <th>NIP</th>
                                     <th>Nama Tutor</th>
                                     <th>Periode Mengajar</th>
                                     <th>Honor Pending (Belum Valid)</th>
                                     <th>Honor Siap Bayar (Approved)</th>
                                     <th>Total Honor Dibayar (Lunas)</th>
                                     <th style="width: 150px;">Aksi</th>
                                 </tr>
                             </thead>
                            <tbody>
                                <?php if (!empty($summaries)) : ?>
                                    <?php $no = 1; foreach ($summaries as $row) : ?>
                                        <tr>
                                             <td class="text-center"><?= $no++ ?></td>
                                             <td class="text-center"><?= htmlspecialchars($row->nip) ?></td>
                                             <td class="font-weight-bold"><?= htmlspecialchars($row->nama_guru) ?></td>
                                             <td><strong><?= strtoupper($row->periode_mengajar ?: '-') ?></strong></td>
                                             <td class="text-right text-warning font-weight-bold">Rp <?= number_format($row->total_pending, 0, ',', '.') ?></td>
                                             <td class="text-right text-success font-weight-bold">Rp <?= number_format($row->total_approved, 0, ',', '.') ?></td>
                                             <td class="text-right text-secondary">Rp <?= number_format($row->total_paid, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('honor/detail_tutor/' . $row->id_guru . '?start_date=' . urlencode($start_date) . '&end_date=' . urlencode($end_date)) ?>" class="btn btn-xs btn-info mr-1">
                                                    <i class="fas fa-search-plus mr-1"></i> Rincian
                                                </a>
                                                <?php if ($row->total_approved > 0) : ?>
                                                    <button type="button" class="btn btn-xs btn-danger btn-payout" data-toggle="modal" data-target="#payoutModal" data-id="<?= $row->id_guru ?>" data-name="<?= htmlspecialchars($row->nama_guru) ?>" data-amount="<?= $row->total_approved ?>">
                                                        <i class="fas fa-wallet mr-1"></i> Bayar
                                                    </button>
                                                <?php else : ?>
                                                    <button type="button" class="btn btn-xs btn-secondary" disabled>
                                                        <i class="fas fa-check-circle mr-1"></i> Lunas
                                                    </button>
                                                    <a href="<?= base_url('honor/batal_lunas/' . $row->id_guru . '?start_date=' . urlencode($start_date) . '&end_date=' . urlencode($end_date)) ?>"
                                                       class="btn btn-xs btn-warning ml-1"
                                                       onclick="return confirm('Yakin ingin membatalkan status Lunas untuk <?= htmlspecialchars($row->nama_guru) ?>?\n\nSemua data yang sudah dibayar akan ditarik kembali menjadi belum dibayar dan bisa dikoreksi ulang.');">
                                                        <i class="fas fa-undo mr-1"></i> Batal Lunas
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                         <td colspan="8" class="text-center text-muted">Belum ada data tutor terdaftar.</td>
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

<!-- Modal Payout -->
<div class="modal fade" id="payoutModal" tabindex="-1" role="dialog" aria-labelledby="payoutModalLabel" aria-hidden="true">
    <div class="modal-dialog text-dark" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payoutModalLabel">Proses Bayar Honor Tutor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('honor/payout') ?>
            <input type="hidden" name="tutor_id" id="payout_tutor_id">
            <input type="hidden" name="start_date" value="<?= $start_date ?>">
            <input type="hidden" name="end_date" value="<?= $end_date ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Tutor</label>
                    <input type="text" id="payout_tutor_name" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="payout_amount">Jumlah Nominal Bayar (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="payout_amount" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="payout_notes">Catatan Pembayaran</label>
                    <textarea name="notes" id="payout_notes" class="form-control" rows="3" placeholder="Masukkan detail seperti nomor transfer bank, tanggal transfer, dsb."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger"><i class="fas fa-check-circle mr-1"></i> Selesaikan Pembayaran</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Modal Rates Setting -->
<div class="modal fade" id="rateSettingModal" tabindex="-1" role="dialog" aria-labelledby="rateSettingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg text-dark" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rateSettingModalLabel">Atur Tarif Honorarium</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('honor/save_rate') ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tutor_select">Tutor / Target Guru <span class="text-danger">*</span></label>
                            <select name="tutor_id" id="tutor_select" class="form-control" required>
                                <option value="global">Tarif Global (Default Sekolah)</option>
                                <?php foreach ($tutors as $id => $name) : ?>
                                    <?php if ($id > 0) : ?>
                                        <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Gunakan "Tarif Global" jika ingin mengatur nominal default untuk semua tutor.</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rate_offline">Tarif Tatap Muka Offline (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="rate_offline" id="rate_offline" class="form-control" value="<?= $global_rate->rate_offline ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="rate_online">Tarif Tatap Muka Online (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="rate_online" id="rate_online" class="form-control" value="<?= $global_rate->rate_online ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rate_check_task">Tarif Periksa Tugas (Rp / Siswa) <span class="text-danger">*</span></label>
                            <input type="number" name="rate_check_task" id="rate_check_task" class="form-control" value="<?= $global_rate->rate_check_task ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="rate_create_cbt">Tarif Buat Bank Soal CBT (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="rate_create_cbt" id="rate_create_cbt" class="form-control" value="<?= $global_rate->rate_create_cbt ?>" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Tarif</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#tutorSummaryTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "language": {
                    "url": "<?= base_url('assets/plugins/datatables/i18n/Indonesian.json') ?>"
                }
            });
        }

        // Set payout details dynamically in modal
        $('.btn-payout').on('click', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let amount = $(this).data('amount');

            $('#payout_tutor_id').val(id);
            $('#payout_tutor_name').val(name);
            $('#payout_amount').val(amount);
            $('#payout_amount').attr('max', amount);
        });

        // Auto-fill Tarif Massal
        $('#select-aktivitas').on('change', function() {
            let tarifDefault = $(this).find('option:selected').data('tarif');
            if (tarifDefault !== undefined && tarifDefault !== '') {
                $('#input-tarif').val(tarifDefault);
            } else {
                $('#input-tarif').val('');
            }
        });
    });
</script>

<!-- Modal Print Rekap Yayasan -->
<div class="modal fade" id="printRekapModal" tabindex="-1" role="dialog" aria-labelledby="printRekapModalLabel" aria-hidden="true">
    <div class="modal-dialog text-dark" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printRekapModalLabel">Cetak Rekap Yayasan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('honor/cetak_rekap_yayasan', ['method' => 'GET', 'target' => '_blank']) ?>
            <div class="modal-body">
                <p class="text-muted">Pilih rentang tanggal aktivitas untuk menghasilkan laporan matriks rekapitulasi honorarium bagi yayasan.</p>
                <div class="form-group">
                    <label for="rekap_start">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" name="start" id="rekap_start" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="rekap_end">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" name="end" id="rekap_end" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger"><i class="fas fa-print mr-1"></i> Buka Tampilan Cetak</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Modal Input Massal Honor -->
<div class="modal fade" id="inputMassalModal" tabindex="-1" role="dialog" aria-labelledby="inputMassalModalLabel" aria-hidden="true">
    <div class="modal-dialog text-dark" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inputMassalModalLabel">Input Massal Honor / Absensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('honor/simpan_massal_honor') ?>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Tutor / Guru <span class="text-danger">*</span></label>
                    <div style="max-height: 180px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;" class="mb-2">
                        <?php foreach ($tutors as $id => $name) : ?>
                            <?php if ($id > 0) : ?>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="tutor_ids[]" value="<?= $id ?>" class="custom-control-input" id="tutor_massal_chk_<?= $id ?>">
                                    <label class="custom-control-label" for="tutor_massal_chk_<?= $id ?>"><?= htmlspecialchars($name) ?></label>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <small class="form-text text-muted">Centang tutor yang akan diberikan honor ini.</small>
                </div>

                <div class="form-group">
                    <label>Kategori Aktivitas <span class="text-danger">*</span></label>
                    <select name="aktivitas" id="select-aktivitas" class="form-control" required>
                        <option value="" data-tarif="">-- Pilih Aktivitas --</option>
                        <option value="offline" data-tarif="<?= intval($global_rate->rate_offline) ?>">Tatap Muka Offline</option>
                        <option value="online" data-tarif="<?= intval($global_rate->rate_online) ?>">Tatap Muka Online</option>
                        <option value="create_cbt" data-tarif="<?= intval($global_rate->rate_create_cbt) ?>">Pembuatan Bank Soal CBT</option>
                        <option value="check_task" data-tarif="<?= intval($global_rate->rate_check_task) ?>">Tugas Tambahan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tarif per Kuantitas (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="tarif" id="input-tarif" class="form-control" placeholder="Contoh: 15000 atau 50000" min="0" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Aktivitas <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_aktivitas" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label>Mapel & Kelas <span class="text-danger">*</span></label>
                    <input type="text" name="mapel_kelas" class="form-control" placeholder="Contoh: Matematika - Kelas IX" required>
                </div>

                <div class="form-group">
                    <label>Kuantitas (Sesi / Unit) <span class="text-danger">*</span></label>
                    <input type="number" name="kuantitas" class="form-control" value="1" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-check-circle mr-1"></i> Simpan Massal</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
