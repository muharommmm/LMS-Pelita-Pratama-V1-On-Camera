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
            <?php if (!empty($info_jadwal)) : ?>
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-lg mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wide">Informasi Pola Jadwal</h3>
                        <div class="mt-1 text-sm text-blue-700">
                            <?= htmlspecialchars($info_jadwal); ?> 
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card card-default my-shadow mb-4">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><?= $subjudul ?></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label for="filterKelas" class="font-weight-bold"><i class="fas fa-filter mr-1 text-primary"></i> Filter Kelas:</label>
                                <select id="filterKelas" class="form-control form-control-sm">
                                    <option value="">-- Semua Kelas --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="tutorScheduleTable">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th>Hari</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Waktu</th>
                                    <th>Pola Mingguan</th>
                                    <th>Link Kelas / Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($schedules)) : ?>
                                    <?php $no = 1; foreach ($schedules as $row) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center font-weight-bold"><?= $day_names[$row->day] ?></td>
                                            <td class="text-center font-weight-bold text-dark"><?= htmlspecialchars($row->nama_kelas) ?></td>
                                            <td><?= htmlspecialchars($row->nama_mapel) ?> (<?= htmlspecialchars($row->kode) ?>)</td>
                                            <td class="text-center font-weight-bold text-teal">
                                                <?= date('H:i', strtotime($row->start_time)) ?> - <?= date('H:i', strtotime($row->end_time)) ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $pola = strtolower($row->pola_mingguan ?? '');
                                                if ($pola === 'ganjil' || $row->pola_mingguan == 2) : ?>
                                                    <span class="badge badge-warning text-xs px-2 py-1 rounded">Minggu Ganjil</span>
                                                <?php elseif ($pola === 'genap' || $row->pola_mingguan == 3) : ?>
                                                    <span class="badge badge-info text-xs px-2 py-1 rounded">Minggu Genap</span>
                                                <?php else : ?>
                                                    <span class="badge badge-success text-xs px-2 py-1 rounded">Setiap Minggu</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $jenis = strtolower($row->jenis_kegiatan ?? '');
                                                if ($jenis === 'tugas') : 
                                                ?>
                                                    <span class="badge badge-warning"><i class="fas fa-pencil-alt mr-1"></i> Tugas Mandiri</span>
                                                <?php elseif ($jenis === 'online' || !empty($row->learning_link)) : ?>
                                                    <?php if (!empty($row->learning_link)) : ?>
                                                        <a href="<?= htmlspecialchars($row->learning_link) ?>" target="_blank" class="btn btn-xs btn-success font-weight-bold">
                                                            <i class="fas fa-video mr-1"></i> Mulai Kelas (Link)
                                                        </a>
                                                    <?php else : ?>
                                                        <span class="badge badge-info"><i class="fas fa-globe mr-1"></i> Tatap Muka Online</span>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <span class="badge badge-secondary"><i class="fas fa-building mr-1"></i> Kelas Offline</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Anda tidak memiliki jadwal mengajar terdaftar untuk periode ini.</td>
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
            var table = $('#tutorScheduleTable').DataTable({
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

            // Populate Class Filter dynamically
            var uniqueClasses = [];
            table.column(2).data().each(function(val) {
                var cleanVal = $('<div>').html(val).text().trim();
                if (cleanVal && $.inArray(cleanVal, uniqueClasses) === -1) {
                    uniqueClasses.push(cleanVal);
                }
            });
            uniqueClasses.sort();
            $.each(uniqueClasses, function(i, val) {
                $('#filterKelas').append('<option value="' + val + '">' + val + '</option>');
            });

            // Handle filter change
            $('#filterKelas').on('change', function() {
                var val = $(this).val();
                if (val) {
                    table.column(2).search('^' + $.fn.dataTable.util.escapeRegex(val) + '$', true, false).draw();
                } else {
                    table.column(2).search('').draw();
                }
            });
        }
    });
</script>
