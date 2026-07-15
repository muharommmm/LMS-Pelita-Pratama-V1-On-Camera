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
                    <h5 class="card-title"><?= $subjudul ?></h5>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <label for="filter_kelas" class="font-weight-bold text-muted">Filter Kelas</label>
                            <select id="filter_kelas" class="form-control select2">
                                <option value="">Semua Kelas</option>
                                <?php foreach ($classes as $id_kelas => $nama_kelas) : ?>
                                    <option value="<?= $id_kelas ?>"><?= htmlspecialchars($nama_kelas) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="filter_mapel" class="font-weight-bold text-muted">Filter Mata Pelajaran</label>
                            <select id="filter_mapel" class="form-control select2">
                                <option value="">Semua Mapel</option>
                                <?php foreach ($mapels as $id_mapel => $nama_mapel) : ?>
                                    <option value="<?= $id_mapel ?>"><?= htmlspecialchars($nama_mapel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="filter_kategori" class="font-weight-bold text-muted">Filter Kategori</label>
                            <select id="filter_kategori" class="form-control select2">
                                <option value="">Semua Kategori</option>
                                <option value="mapel">Mata Pelajaran (Mapel)</option>
                                <option value="ekskul">Ekstrakurikuler (Ekskul)</option>
                                <option value="lainnya">Catatan Khusus (Kustom)</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="ebookTable">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th>Judul E-Book</th>
                                    <th>Kategori / Klasifikasi</th>
                                    <th>Diunggah Oleh</th>
                                    <th>Tanggal Unggah</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ebooks)) : ?>
                                    <?php $no = 1; foreach ($ebooks as $ebook) : ?>
                                        <tr class="ebook-row" 
                                            data-class="<?= $ebook->class_id ?>" 
                                            data-mapel="<?= $ebook->mapel_id ?>" 
                                            data-ekstra="<?= $ebook->ekstra_id ?>" 
                                            data-category="<?= $ebook->mapel_id ? 'mapel' : ($ebook->ekstra_id ? 'ekskul' : ($ebook->custom_category ? 'lainnya' : '')) ?>">
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($ebook->title) ?></td>
                                            <td class="text-center">
                                                <div class="mb-1">
                                                    <span class="badge badge-primary"><?= $ebook->class_id ? 'Kelas: ' . htmlspecialchars($ebook->nama_kelas) : 'Semua Kelas' ?></span>
                                                </div>
                                                <div>
                                                    <?php if ($ebook->mapel_id) : ?>
                                                        <span class="badge badge-success">Mapel: <?= htmlspecialchars($ebook->nama_mapel) ?></span>
                                                    <?php elseif ($ebook->ekstra_id) : ?>
                                                        <span class="badge badge-info">Ekskul: <?= htmlspecialchars($ebook->nama_ekstra) ?></span>
                                                    <?php elseif ($ebook->custom_category) : ?>
                                                        <span class="badge badge-warning"><?= htmlspecialchars($ebook->custom_category) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($ebook->first_name . ' ' . $ebook->last_name) ?></td>
                                            <td class="text-center"><?= date('d-m-Y H:i', strtotime($ebook->created_at)) ?></td>
                                            <td class="text-center">
                                                <div class="btn-group-vertical btn-block">
                                                    <?php if (pathinfo($ebook->file_path, PATHINFO_EXTENSION) === 'pdf') : ?>
                                                        <a href="<?= base_url('ebooks/view/' . $ebook->id_ebook) ?>" target="_blank" class="btn btn-xs btn-info mb-1" title="Lihat Langsung">
                                                            <i class="fas fa-eye"></i> Lihat Langsung
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url($ebook->file_path) ?>" target="_blank" class="btn btn-xs btn-success" title="Download">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada e-book yang tersedia.</td>
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
        var table = null;
        if ($.fn.DataTable) {
            table = $('#ebookTable').DataTable({
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

            // Custom DataTable search filtering logic
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    if (settings.nTable.id !== 'ebookTable') return true;
                    
                    var row = $(table.row(dataIndex).node());
                    var rowClass = row.data('class');
                    var rowMapel = row.data('mapel');
                    var rowCategory = row.data('category');

                    var selectedClass = $('#filter_kelas').val();
                    var selectedMapel = $('#filter_mapel').val();
                    var selectedCategory = $('#filter_kategori').val();

                    var matchClass = !selectedClass || rowClass == selectedClass;
                    var matchMapel = !selectedMapel || rowMapel == selectedMapel;
                    var matchCategory = !selectedCategory || rowCategory == selectedCategory;

                    return matchClass && matchMapel && matchCategory;
                }
            );

            // Redraw table on dropdown change
            $('#filter_kelas, #filter_mapel, #filter_kategori').on('change', function() {
                table.draw();
            });
        }
    });
</script>
