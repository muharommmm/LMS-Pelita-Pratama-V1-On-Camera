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
            <div class="card card-default my-shadow mb-4">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><?= $subjudul ?> (Kelas: <?= htmlspecialchars($siswa->nama_kelas) ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="siswaScheduleTable">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th>Hari</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru Pengajar</th>
                                    <th>Waktu Kelas</th>
                                    <th>Link Kelas / Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($schedules)) : ?>
                                    <?php $no = 1; foreach ($schedules as $row) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center font-weight-bold"><?= $day_names[$row->day] ?></td>
                                            <td><?= htmlspecialchars($row->nama_mapel) ?> (<?= htmlspecialchars($row->kode) ?>)</td>
                                            <td class="font-weight-bold text-dark"><?= htmlspecialchars($row->nama_guru) ?></td>
                                            <td class="text-center font-weight-bold text-teal">
                                                <?= date('H:i', strtotime($row->start_time)) ?> - <?= date('H:i', strtotime($row->end_time)) ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($row->learning_link)) : ?>
                                                    <a href="<?= htmlspecialchars($row->learning_link) ?>" target="_blank" class="btn btn-xs btn-success font-weight-bold">
                                                        <i class="fas fa-external-link-alt mr-1"></i> Masuk Kelas (Link)
                                                    </a>
                                                <?php else : ?>
                                                    <span class="badge badge-secondary"><i class="fas fa-building mr-1"></i> Kelas Offline</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada jadwal terdaftar untuk kelas Anda periode ini.</td>
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
            $('#siswaScheduleTable').DataTable({
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
