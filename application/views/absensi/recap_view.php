<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1><?= $judul ?></h1>
                <a href="<?= base_url('absensi') ?>" class="btn btn-sm btn-danger">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-default my-shadow mb-4">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><?= $subjudul ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="recapTable">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 150px;">NIS</th>
                                    <th>Nama Siswa</th>
                                    <th style="width: 100px;" class="text-success">Hadir (H)</th>
                                    <th style="width: 100px;" class="text-warning">Sakit (S)</th>
                                    <th style="width: 100px;" class="text-info">Izin (I)</th>
                                    <th style="width: 100px;" class="text-danger">Alpha (A)</th>
                                    <th style="width: 120px;">Total Hari</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recap)) : ?>
                                    <?php $no = 1; foreach ($recap as $row) : ?>
                                        <?php 
                                        $total = intval($row->hadir) + intval($row->sakit) + intval($row->izin) + intval($row->alpha); 
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row->nis) ?></td>
                                            <td><?= htmlspecialchars($row->nama) ?></td>
                                            <td class="text-center font-weight-bold text-success"><?= intval($row->hadir) ?></td>
                                            <td class="text-center font-weight-bold text-warning"><?= intval($row->sakit) ?></td>
                                            <td class="text-center font-weight-bold text-info"><?= intval($row->izin) ?></td>
                                            <td class="text-center font-weight-bold text-danger"><?= intval($row->alpha) ?></td>
                                            <td class="text-center font-weight-bold"><?= $total ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Belum ada data absensi untuk kelas ini.</td>
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
            $('#recapTable').DataTable({
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
