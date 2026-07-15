<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1><?= $judul ?></h1>
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#uploadCsvModal">
                    <i class="fas fa-file-csv mr-1"></i> Import SPP (CSV)
                </button>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Under Development Alert -->
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Fitur Dalam Pengembangan!</h5>
                Modul Keuangan SPP saat ini masih dalam tahap pengembangan aktif. Beberapa fitur mungkin belum berfungsi dengan sempurna.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
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

            <div class="card card-default my-shadow mb-4">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><?= $subjudul ?> (Tahun Pelajaran: <?= htmlspecialchars($tp_active->tahun) ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="sppTable">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Bulan</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Tgl Pembayaran</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($billings)) : ?>
                                    <?php $no = 1; foreach ($billings as $bill) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center"><?= htmlspecialchars($bill->nis) ?></td>
                                            <td><?= htmlspecialchars($bill->nama) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($bill->nama_kelas ? $bill->nama_kelas : '-') ?></td>
                                            <td class="text-center"><?= htmlspecialchars($month_names[$bill->month]) ?></td>
                                            <td class="text-right">Rp <?= number_format($bill->amount, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <?php if ($bill->status === 'paid') : ?>
                                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> LUNAS</span>
                                                <?php else : ?>
                                                    <span class="badge badge-warning"><i class="fas fa-exclamation-circle mr-1"></i> BELUM BAYAR</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $bill->payment_date ? date('d-m-Y H:i', strtotime($bill->payment_date)) : '-' ?>
                                            </td>
                                            <td><?= htmlspecialchars($bill->notes) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Belum ada riwayat tagihan/pembayaran SPP untuk periode ini.</td>
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

<!-- Upload CSV Modal -->
<div class="modal fade" id="uploadCsvModal" tabindex="-1" role="dialog" aria-labelledby="uploadCsvModalLabel" aria-hidden="true">
    <div class="modal-dialog text-dark" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadCsvModalLabel">Import Data SPP Masal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open_multipart('spp/import_csv') ?>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle mr-1"></i> Format File CSV:</h6>
                    <p class="mb-1">File CSV harus memiliki kolom berikut tanpa header atau dengan header baris pertama:</p>
                    <code>nis, bulan, nominal, status, tgl_bayar, catatan</code>
                    <br><br>
                    <strong>Contoh Data:</strong><br>
                    <code>10223, 7, 250000, paid, 2026-07-05, Pembayaran Juli</code><br>
                    <code>10223, 8, 250000, unpaid, , </code>
                </div>
                <div class="form-group">
                    <label for="csv_file">Pilih Berkas CSV <span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <input type="file" name="csv_file" id="csv_file" class="custom-file-input" accept=".csv,.txt" required>
                        <label class="custom-file-label" for="csv_file">Pilih berkas .csv</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> Import Sekarang</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        if ($.fn.DataTable) {
            $('#sppTable').DataTable({
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
    });
</script>
