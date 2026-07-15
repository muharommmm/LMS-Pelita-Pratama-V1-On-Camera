<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1><?= $judul ?></h1>
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

            <div class="card card-default my-shadow mb-4">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><?= $subjudul ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="classTable">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Kelas</th>
                                    <th>Nama Barcode Lokasi</th>
                                    <th>Kode Token Barcode</th>
                                    <th style="width: 250px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($classes)) : ?>
                                    <?php $no = 1; foreach ($classes as $id_kelas => $nama_kelas) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center font-weight-bold"><?= htmlspecialchars($nama_kelas) ?></td>
                                            <td><?= isset($barcodes[$id_kelas]) ? htmlspecialchars($barcodes[$id_kelas]->location_name) : '<span class="text-muted">Belum disetting</span>' ?></td>
                                            <td class="text-center text-monospace small"><?= isset($barcodes[$id_kelas]) ? htmlspecialchars($barcodes[$id_kelas]->barcode_code) : '-' ?></td>
                                            <td class="text-center">
                                                <a href="<?= base_url('absensi/generate_barcode/' . $id_kelas) ?>" class="btn btn-xs btn-primary">
                                                    <i class="fas fa-qrcode mr-1"></i> Barcode Lokasi
                                                </a>
                                                <a href="<?= base_url('absensi/recap/' . $id_kelas) ?>" class="btn btn-xs btn-success">
                                                    <i class="fas fa-chart-bar mr-1"></i> Rekap Absensi
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data kelas pelajaran.</td>
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
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#classTable').DataTable({
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
