<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1><?= $judul ?></h1>
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#uploadEbookModal">
                    <i class="fas fa-upload mr-1"></i> Unggah E-Book
                </button>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Alert Messages -->
            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-check"></i> <?= $this->session->flashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="icon fas fa-ban"></i> <?= $this->session->flashdata('error') ?>
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
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <label for="filter_kelas" class="font-weight-bold text-muted">Filter Kelas</label>
                            <select id="filter_kelas" class="form-control select2">
                                <option value="">Semua Kelas</option>
                                <?php foreach ($kelas as $id_kelas => $nama_kelas) : ?>
                                    <option value="<?= $id_kelas ?>"><?= htmlspecialchars($nama_kelas) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="filter_mapel" class="font-weight-bold text-muted">Filter Mata Pelajaran</label>
                            <select id="filter_mapel" class="form-control select2">
                                <option value="">Semua Mapel</option>
                                <?php foreach ($mapels as $mapel) : ?>
                                    <option value="<?= $mapel->id_mapel ?>"><?= htmlspecialchars($mapel->nama_mapel) ?></option>
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
                                    <th>Target Kelas</th>
                                    <th>Tipe / Kategori</th>
                                    <th>Diunggah Oleh</th>
                                    <th>Tanggal Unggah</th>
                                    <th style="width: 180px;">Aksi</th>
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
                                                <span class="badge badge-primary"><?= $ebook->class_id ? (strpos($ebook->class_id, ',') !== false ? 'Multi Kelas' : 'Kelas: ' . htmlspecialchars($ebook->nama_kelas)) : 'Semua Kelas' ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($ebook->mapel_id) : ?>
                                                    <span class="badge badge-success">Mapel: <?= htmlspecialchars($ebook->nama_mapel) ?></span>
                                                <?php elseif ($ebook->ekstra_id) : ?>
                                                    <span class="badge badge-info">Ekskul: <?= htmlspecialchars($ebook->nama_ekstra) ?></span>
                                                <?php elseif ($ebook->custom_category) : ?>
                                                    <span class="badge badge-warning"><?= htmlspecialchars($ebook->custom_category) ?></span>
                                                <?php else : ?>
                                                    <span class="badge badge-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($ebook->first_name . ' ' . $ebook->last_name) ?></td>
                                            <td class="text-center"><?= date('d-m-Y H:i', strtotime($ebook->created_at)) ?></td>
                                            <td class="text-center">
                                                <?php if (pathinfo($ebook->file_path, PATHINFO_EXTENSION) === 'pdf') : ?>
                                                    <a href="<?= base_url('ebooks/view/' . $ebook->id_ebook) ?>" target="_blank" class="btn btn-xs btn-info" title="Lihat Langsung">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= base_url($ebook->file_path) ?>" target="_blank" class="btn btn-xs btn-success" title="Download">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <button type="button" class="btn btn-xs btn-warning btn-edit" 
                                                    data-id="<?= $ebook->id_ebook ?>"
                                                    data-title="<?= htmlspecialchars($ebook->title) ?>"
                                                    data-class_id="<?= $ebook->class_id ?>"
                                                    data-mapel_id="<?= $ebook->mapel_id ?>"
                                                    data-ekstra_id="<?= $ebook->ekstra_id ?>"
                                                    data-custom="<?= htmlspecialchars($ebook->custom_category) ?>"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <a href="<?= base_url('ebooks/delete/' . $ebook->id_ebook) ?>" class="btn btn-xs btn-danger btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus e-book ini?');" title="Hapus">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada e-book yang diunggah.</td>
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

<!-- Edit Ebook Modal -->
<div class="modal fade" id="editEbookModal" tabindex="-1" role="dialog" aria-labelledby="editEbookModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEbookModalLabel">Edit E-Book</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open_multipart('ebooks/update', ['id' => 'formEditEbook']) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_title">Judul E-Book <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_class_id">Target Kelas <span class="text-danger">*</span></label>
                    <select name="class_id[]" id="edit_class_id" class="form-control select2" multiple="multiple" data-placeholder="Pilih Kelas (Bisa lebih dari satu)" required>
                        <option value="0">Semua Kelas</option>
                        <?php foreach ($kelas as $id_kelas => $nama_kelas) : ?>
                            <option value="<?= $id_kelas ?>"><?= htmlspecialchars($nama_kelas) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_sub_category_type">Sub-Kategori / Tipe <span class="text-danger">*</span></label>
                    <select name="sub_category_type" id="edit_sub_category_type" class="form-control" required>
                        <option value="mapel">Mata Pelajaran (Mapel)</option>
                        <option value="ekskul">Ekstrakurikuler (Ekskul)</option>
                        <option value="lainnya">Catatan Khusus (Kustom)</option>
                    </select>
                </div>
                
                <div class="form-group edit-sub-category-group" id="edit_group_mapel">
                    <label for="edit_mapel_id">Target Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="mapel_id" id="edit_mapel_id" class="form-control">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($mapels as $mapel) : ?>
                            <option value="<?= $mapel->id_mapel ?>"><?= htmlspecialchars($mapel->nama_mapel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group edit-sub-category-group d-none" id="edit_group_ekskul">
                    <label for="edit_ekstra_id">Target Ekstrakurikuler <span class="text-danger">*</span></label>
                    <select name="ekstra_id" id="edit_ekstra_id" class="form-control">
                        <option value="">-- Pilih Ekstrakurikuler --</option>
                        <?php foreach ($ekskuls as $ekskul) : ?>
                            <option value="<?= $ekskul->id_ekstra ?>"><?= htmlspecialchars($ekskul->nama_ekstra) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group edit-sub-category-group d-none" id="edit_group_lainnya">
                    <label for="edit_custom_category">Kategori Kustom (Catatan) <span class="text-danger">*</span></label>
                    <input type="text" name="custom_category" id="edit_custom_category" class="form-control">
                </div>

                <div class="form-group">
                    <label for="edit_ebook_file">Ganti Berkas E-Book (Opsional)</label>
                    <div class="custom-file">
                        <input type="file" name="ebook_file" id="edit_ebook_file" class="custom-file-input" accept=".pdf,.epub,.mobi">
                        <label class="custom-file-label" for="edit_ebook_file">Pilih berkas baru...</label>
                    </div>
                    <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengganti file.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Upload Ebook Modal -->
<div class="modal fade" id="uploadEbookModal" tabindex="-1" role="dialog" aria-labelledby="uploadEbookModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadEbookModalLabel">Unggah E-Book Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open_multipart('ebooks/upload') ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="title">Judul E-Book <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="Masukkan judul e-book" required>
                </div>
                
                <div class="form-group">
                    <label for="class_id">Target Kelas <span class="text-danger">*</span></label>
                    <select name="class_id[]" id="class_id" class="form-control select2" multiple="multiple" data-placeholder="Pilih Kelas (Bisa lebih dari satu)" required>
                        <option value="0">Semua Kelas</option>
                        <?php foreach ($kelas as $id_kelas => $nama_kelas) : ?>
                            <option value="<?= $id_kelas ?>"><?= htmlspecialchars($nama_kelas) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sub_category_type">Sub-Kategori / Tipe <span class="text-danger">*</span></label>
                    <select name="sub_category_type" id="sub_category_type" class="form-control" required>
                        <option value="mapel">Mata Pelajaran (Mapel)</option>
                        <option value="ekskul">Ekstrakurikuler (Ekskul)</option>
                        <option value="lainnya">Catatan Khusus (Kustom)</option>
                    </select>
                </div>
                
                <div class="form-group sub-category-group" id="group_mapel">
                    <label for="mapel_id">Target Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="mapel_id" id="mapel_id" class="form-control">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($mapels as $mapel) : ?>
                            <option value="<?= $mapel->id_mapel ?>"><?= htmlspecialchars($mapel->nama_mapel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group sub-category-group d-none" id="group_ekskul">
                    <label for="ekstra_id">Target Ekstrakurikuler <span class="text-danger">*</span></label>
                    <select name="ekstra_id" id="ekstra_id" class="form-control">
                        <option value="">-- Pilih Ekstrakurikuler --</option>
                        <?php foreach ($ekskuls as $ekskul) : ?>
                            <option value="<?= $ekskul->id_ekstra ?>"><?= htmlspecialchars($ekskul->nama_ekstra) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group sub-category-group d-none" id="group_lainnya">
                    <label for="custom_category">Kategori Kustom (Catatan) <span class="text-danger">*</span></label>
                    <input type="text" name="custom_category" id="custom_category" class="form-control" placeholder="Contoh: Modul E-Book Khusus Lamar Kerja">
                </div>

                <div class="form-group">
                    <label for="ebook_file">Berkas E-Book <span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <input type="file" name="ebook_file" id="ebook_file" class="custom-file-input" accept=".pdf,.epub,.mobi" required>
                        <label class="custom-file-label" for="ebook_file">Pilih berkas (.pdf, .epub, .mobi)</label>
                    </div>
                    <small class="form-text text-muted">Ukuran maksimal file: 50 MB.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Toggle input groups based on sub-category selector
        $('#sub_category_type').on('change', function() {
            var type = $(this).val();
            $('.sub-category-group').addClass('d-none');
            $('.sub-category-group select, .sub-category-group input').removeAttr('required');

            if (type === 'mapel') {
                $('#group_mapel').removeClass('d-none');
                $('#mapel_id').attr('required', 'required');
            } else if (type === 'ekskul') {
                $('#group_ekskul').removeClass('d-none');
                $('#ekstra_id').attr('required', 'required');
            } else if (type === 'lainnya') {
                $('#group_lainnya').removeClass('d-none');
                $('#custom_category').attr('required', 'required');
            }
        });

                // Initialize Select2
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        }

        // Edit button click
        $('.btn-edit').on('click', function() {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var class_id = String($(this).data('class_id')).split(',');
            var mapel_id = $(this).data('mapel_id');
            var ekstra_id = $(this).data('ekstra_id');
            var custom = $(this).data('custom');
            
            $('#formEditEbook').attr('action', '<?= base_url("ebooks/update/") ?>' + id);
            $('#edit_title').val(title);
            
            if (class_id == 'null' || class_id == '') {
                $('#edit_class_id').val(['0']).trigger('change');
            } else {
                $('#edit_class_id').val(class_id).trigger('change');
            }

            if (mapel_id) {
                $('#edit_sub_category_type').val('mapel').trigger('change');
                $('#edit_mapel_id').val(mapel_id);
            } else if (ekstra_id) {
                $('#edit_sub_category_type').val('ekskul').trigger('change');
                $('#edit_ekstra_id').val(ekstra_id);
            } else if (custom) {
                $('#edit_sub_category_type').val('lainnya').trigger('change');
                $('#edit_custom_category').val(custom);
            }
            
            $('#editEbookModal').modal('show');
        });

        // Toggle edit input groups based on sub-category selector
        $('#edit_sub_category_type').on('change', function() {
            var type = $(this).val();
            $('.edit-sub-category-group').addClass('d-none');
            $('.edit-sub-category-group select, .edit-sub-category-group input').removeAttr('required');

            if (type === 'mapel') {
                $('#edit_group_mapel').removeClass('d-none');
                $('#edit_mapel_id').attr('required', 'required');
            } else if (type === 'ekskul') {
                $('#edit_group_ekskul').removeClass('d-none');
                $('#edit_ekstra_id').attr('required', 'required');
            } else if (type === 'lainnya') {
                $('#edit_group_lainnya').removeClass('d-none');
                $('#edit_custom_category').attr('required', 'required');
            }
        });

        // Trigger change initially to set correct state
        $('#sub_category_type').trigger('change');

        // Set dynamic filename in file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Initialize DataTable if library is loaded
        if ($.fn.DataTable) {
            var table = $('#ebookTable').DataTable({
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
                    var rowClass = String(row.data('class'));
                    var rowMapel = row.data('mapel');
                    var rowCategory = row.data('category');

                    var selectedKelas = $('#filter_kelas').val();
                    var selectedMapel = $('#filter_mapel').val();
                    var selectedCategory = $('#filter_kategori').val();

                    var matchKelas = false;
                    if (!selectedKelas) {
                        matchKelas = true;
                    } else if (rowClass && rowClass !== 'undefined' && rowClass !== 'null') {
                        var arrClass = rowClass.split(',');
                        if (arrClass.includes(String(selectedKelas))) {
                            matchKelas = true;
                        }
                    }

                    var matchMapel = !selectedMapel || rowMapel == selectedMapel;
                    var matchCategory = !selectedCategory || rowCategory == selectedCategory;

                    return matchKelas && matchMapel && matchCategory;
                }
            );

            // Redraw table on dropdown change
            $('#filter_kelas, #filter_mapel, #filter_kategori').on('change', function() {
                table.draw();
            });
        }
    });
</script>
