<?php
$f = 'C:\xampp\htdocs\garuda_cbt\application\views\ebooks\admin_list.php';
$c = file_get_contents($f);

// 1. Add Edit button in the table
$old_table_actions = <<<EOT
                                                <a href="<?= base_url('ebooks/delete/' . \$ebook->id_ebook) ?>" class="btn btn-xs btn-danger btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus e-book ini?');" title="Hapus">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
EOT;

$new_table_actions = <<<EOT
                                                <button type="button" class="btn btn-xs btn-warning btn-edit" 
                                                    data-id="<?= \$ebook->id_ebook ?>"
                                                    data-title="<?= htmlspecialchars(\$ebook->title) ?>"
                                                    data-class_id="<?= \$ebook->class_id ?>"
                                                    data-mapel_id="<?= \$ebook->mapel_id ?>"
                                                    data-ekstra_id="<?= \$ebook->ekstra_id ?>"
                                                    data-custom="<?= htmlspecialchars(\$ebook->custom_category) ?>"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <a href="<?= base_url('ebooks/delete/' . \$ebook->id_ebook) ?>" class="btn btn-xs btn-danger btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus e-book ini?');" title="Hapus">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
EOT;

$c = str_replace($old_table_actions, $new_table_actions, $c);

// 2. Change class_id select in upload modal
$old_select = <<<EOT
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="0">Semua Kelas</option>
                        <?php foreach (\$kelas as \$id_kelas => \$nama_kelas) : ?>
                            <option value="<?= \$id_kelas ?>"><?= htmlspecialchars(\$nama_kelas) ?></option>
                        <?php endforeach; ?>
                    </select>
EOT;

$new_select = <<<EOT
                    <select name="class_id[]" id="class_id" class="form-control select2" multiple="multiple" data-placeholder="Pilih Kelas (Bisa lebih dari satu)" required>
                        <option value="0">Semua Kelas</option>
                        <?php foreach (\$kelas as \$id_kelas => \$nama_kelas) : ?>
                            <option value="<?= \$id_kelas ?>"><?= htmlspecialchars(\$nama_kelas) ?></option>
                        <?php endforeach; ?>
                    </select>
EOT;

$c = str_replace($old_select, $new_select, $c);

// 3. Add Edit Modal
$edit_modal = <<<EOT
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
                        <?php foreach (\$kelas as \$id_kelas => \$nama_kelas) : ?>
                            <option value="<?= \$id_kelas ?>"><?= htmlspecialchars(\$nama_kelas) ?></option>
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
                        <?php foreach (\$mapels as \$mapel) : ?>
                            <option value="<?= \$mapel->id_mapel ?>"><?= htmlspecialchars(\$mapel->nama_mapel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group edit-sub-category-group d-none" id="edit_group_ekskul">
                    <label for="edit_ekstra_id">Target Ekstrakurikuler <span class="text-danger">*</span></label>
                    <select name="ekstra_id" id="edit_ekstra_id" class="form-control">
                        <option value="">-- Pilih Ekstrakurikuler --</option>
                        <?php foreach (\$ekskuls as \$ekskul) : ?>
                            <option value="<?= \$ekskul->id_ekstra ?>"><?= htmlspecialchars(\$ekskul->nama_ekstra) ?></option>
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
EOT;

$c = str_replace('<!-- Upload Ebook Modal -->', $edit_modal . "\n\n<!-- Upload Ebook Modal -->", $c);

$js_addition = <<<EOT
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
EOT;

$c = str_replace('// Trigger change initially to set correct state', $js_addition . "\n\n        // Trigger change initially to set correct state", $c);

file_put_contents($f, $c);
echo "Updated admin_list.php\n";
