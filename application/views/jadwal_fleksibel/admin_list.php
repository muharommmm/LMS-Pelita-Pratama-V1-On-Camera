<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1><?= $judul ?></h1>
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addScheduleModal">
                    <i class="fas fa-plus mr-1"></i> Tambah Jadwal Fleksibel
                </button>
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
                        <table class="table table-striped table-bordered table-hover" id="scheduleTable">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th>Hari</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Waktu</th>
                                    <th>Guru Pengajar</th>
                                    <th>Pola Mingguan</th>
                                    <th>Jenis Kegiatan</th>
                                    <th>Link Kelas / Status</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($schedules)) : ?>
                                    <?php $no = 1; foreach ($schedules as $row) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center font-weight-bold"><?= $day_names[$row->day] ?></td>
                                            <td class="text-center"><?= htmlspecialchars($row->nama_kelas) ?></td>
                                            <td><?= htmlspecialchars($row->nama_mapel) ?> (<?= htmlspecialchars($row->kode) ?>)</td>
                                            <td class="text-center font-weight-bold text-teal">
                                                <?= date('H:i', strtotime($row->start_time)) ?> - <?= date('H:i', strtotime($row->end_time)) ?>
                                            </td>
                                            <td class="font-weight-bold text-dark"><?= htmlspecialchars($row->nama_guru) ?></td>
                                            <td class="text-center font-weight-bold text-indigo"><?= htmlspecialchars($row->pola_mingguan) ?></td>
                                            <td class="text-center">
                                                <?php if (in_array(strtolower($row->jenis_kegiatan), ['tugas'])) : ?>
                                                    <span class="badge badge-warning"><i class="fas fa-pencil-alt mr-1"></i> Tugas</span>
                                                <?php elseif (in_array(strtolower($row->jenis_kegiatan), ['online'])) : ?>
                                                    <span class="badge badge-info"><i class="fas fa-globe mr-1"></i> Tatap Muka Online</span>
                                                <?php else : ?>
                                                    <span class="badge badge-success"><i class="fas fa-chalkboard-teacher mr-1"></i> Tatap Muka Offline</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!empty($row->learning_link)) : ?>
                                                    <a href="<?= htmlspecialchars($row->learning_link) ?>" target="_blank" class="btn btn-xs btn-teal">
                                                        <i class="fas fa-video mr-1"></i> Buka Link
                                                    </a>
                                                <?php else : ?>
                                                    <span class="badge badge-secondary"><i class="fas fa-building mr-1"></i> Kelas Offline</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('jadwal_fleksibel/copy/' . $row->id_jadwal) ?>" class="btn btn-xs btn-success" onclick="return confirm('Apakah Anda yakin ingin menduplikasi jadwal ini?');" title="Copy Jadwal">
                                                    <i class="fas fa-copy"></i> Copy
                                                </a>
                                                <button class="btn btn-xs btn-warning btn-edit-jadwal" data-id="<?= $row->id_jadwal ?>" title="Edit Jadwal">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <a href="<?= base_url('jadwal_fleksibel/delete/' . $row->id_jadwal) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" role="dialog" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog text-dark" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addScheduleModalLabel">Tambah Jadwal Fleksibel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('jadwal_fleksibel/create', ['id' => 'form-jadwal']) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="tutor_id">Guru / Tutor <span class="text-danger">*</span></label>
                    <select name="tutor_id" id="tutor_id" class="form-control" required>
                        <option value="">-- Pilih Guru/Tutor --</option>
                        <?php foreach ($tutors as $id => $name) : ?>
                            <?php if ($id > 0) : ?>
                                <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="class_id">Target Kelas <span class="text-danger">*</span></label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($classes as $id => $name) : ?>
                            <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="mapel_id">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="mapel_id" id="mapel_id" class="form-control" required>
                        <option value="">-- Pilih Guru/Tutor Terlebih Dahulu --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="day">Hari <span class="text-danger">*</span></label>
                    <select name="day" id="day" class="form-control" required>
                        <option value="">-- Pilih Hari --</option>
                        <option value="1">Senin</option>
                        <option value="2">Selasa</option>
                        <option value="3">Rabu</option>
                        <option value="4">Kamis</option>
                        <option value="5">Jumat</option>
                        <option value="6">Sabtu</option>
                        <option value="7">Minggu</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_time">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="start_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_time">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" id="end_time" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pola_mingguan">Pola Mingguan (Alternating) <span class="text-danger">*</span></label>
                    <select name="pola_mingguan" id="pola_mingguan" class="form-control" required>
                        <option value="Semua">Setiap Minggu</option>
                        <option value="Ganjil">Minggu Ganjil Saja</option>
                        <option value="Genap">Minggu Genap Saja</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="jenis_kegiatan">Jenis Kegiatan <span class="text-danger">*</span></label>
                    <select name="jenis_kegiatan" id="jenis_kegiatan" class="form-control" required>
                        <option value="offline">Tatap Muka Offline</option>
                        <option value="online">Tatap Muka Online</option>
                        <option value="tugas">Pemberian Tugas (Mandiri)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="learning_link">Link Pembelajaran Kelas Online (Zoom/Google Meet)</label>
                    <input type="url" name="learning_link" id="learning_link" class="form-control" placeholder="https://zoom.us/j/...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Jadwal</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog text-dark" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">Edit Jadwal Fleksibel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('jadwal_fleksibel/update', ['id' => 'form-edit-jadwal']) ?>
            <input type="hidden" name="id_jadwal" id="edit_id_jadwal">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_class_id">Target Kelas <span class="text-danger">*</span></label>
                    <select name="class_id" id="edit_class_id" class="form-control select2" style="width: 100%;" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($classes as $id => $name) : ?>
                            <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_mapel_id">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="mapel_id" id="edit_mapel_id" class="form-control select2" style="width: 100%;" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($mapels as $id => $name) : ?>
                            <?php if ($id > 0) : ?>
                                <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_day">Hari <span class="text-danger">*</span></label>
                    <select name="day" id="edit_day" class="form-control" required>
                        <option value="">-- Pilih Hari --</option>
                        <option value="1">Senin</option>
                        <option value="2">Selasa</option>
                        <option value="3">Rabu</option>
                        <option value="4">Kamis</option>
                        <option value="5">Jumat</option>
                        <option value="6">Sabtu</option>
                        <option value="7">Minggu</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_start_time">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_end_time">Jam Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_pola_mingguan">Pola Mingguan (Alternating) <span class="text-danger">*</span></label>
                    <select name="pola_mingguan" id="edit_pola_mingguan" class="form-control" required>
                        <option value="Semua">Setiap Minggu</option>
                        <option value="Ganjil">Minggu Ganjil Saja</option>
                        <option value="Genap">Minggu Genap Saja</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_jenis_kegiatan">Jenis Kegiatan <span class="text-danger">*</span></label>
                    <select name="jenis_kegiatan" id="edit_jenis_kegiatan" class="form-control" required>
                        <option value="offline">Tatap Muka Offline</option>
                        <option value="online">Tatap Muka Online</option>
                        <option value="tugas">Pemberian Tugas (Mandiri)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_learning_link">Link Pembelajaran Kelas Online (Zoom/Google Meet)</label>
                    <input type="url" name="learning_link" id="edit_learning_link" class="form-control" placeholder="https://zoom.us/j/...">
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

<script>
    $(document).ready(function() {

        // === 1. Inisialisasi Select2 (dengan try-catch proteksi) ===
        try {
            if ($.fn.select2) {
                $('#tutor_id, #class_id').select2({ width: '100%', theme: 'bootstrap4' });
            }
        } catch (err) {
            console.warn('Select2 init error:', err);
        }

        // === 2. Inisialisasi DataTable ===
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

            if ($.fn.DataTable.isDataTable('#scheduleTable')) {
                $('#scheduleTable').DataTable().destroy();
            }
            var table = $('#scheduleTable').DataTable({
                "paging"      : true,
                "lengthChange": true,
                "searching"   : true,
                "ordering"    : true,
                "info"        : true,
                "autoWidth"   : false,
                "retrieve"    : true,
                "columnDefs"  : [
                    { "orderable": false, "targets": [9] }
                ],
                "language"    : dtLang
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

            // Restore filter from sessionStorage
            var savedFilter = sessionStorage.getItem('selected_filter_kelas');
            if (savedFilter) {
                $('#filterKelas').val(savedFilter);
                table.column(2).search('^' + $.fn.dataTable.util.escapeRegex(savedFilter) + '$', true, false).draw();
            }

            // Handle filter change
            $('#filterKelas').on('change', function() {
                var val = $(this).val();
                if (val) {
                    sessionStorage.setItem('selected_filter_kelas', val);
                    table.column(2).search('^' + $.fn.dataTable.util.escapeRegex(val) + '$', true, false).draw();
                } else {
                    sessionStorage.removeItem('selected_filter_kelas');
                    table.column(2).search('').draw();
                }
            });
        }

        // === 3. AJAX dynamic Mapel loading based on Tutor assignment ===
        $('#tutor_id').on('change', function() {
            var tutorId = $(this).val();
            var $mapel = $('#mapel_id');
            $mapel.empty();

            if (!tutorId) {
                $mapel.append('<option value="">-- Pilih Guru/Tutor Terlebih Dahulu --</option>');
                return;
            }

            $mapel.append('<option value="">Memuat...</option>');

            $.ajax({
                url: '<?= base_url("jadwal_fleksibel/get_mapel_by_tutor_ajax") ?>',
                type: 'POST',
                data: { 
                    tutor_id: tutorId,
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(data) {
                    var html = '';
                    if (data && data.length > 0) {
                        html += '<option value="">-- Pilih Mata Pelajaran --</option>';
                        for (var i = 0; i < data.length; i++) {
                            html += '<option value="' + data[i].id_mapel + '">' + data[i].nama_mapel + '</option>';
                        }
                    } else {
                        html = '<option value="">-- Tutor ini belum memiliki jadwal mapel --</option>';
                    }
                    $mapel.html(html);
                },
                error: function(xhr, status, error) {
                    console.error('[AJAX Load Mapel Error]', xhr.responseText || error);
                    $mapel.html('<option value="">-- Gagal memuat data --</option>');
                }
            });
        });

        // === 4. AJAX Schedule conflict check with Soft-Warning (Form Tambah) ===
        $('#form-jadwal').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);

            var tutorId = $('#tutor_id').val();
            var day = $('#day').val();
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            var jenisKegiatan = $('#jenis_kegiatan').val();

            $.ajax({
                url: '<?= base_url("jadwal_fleksibel/cek_bentrok_ajax") ?>',
                type: 'POST',
                data: {
                    tutor_id: tutorId,
                    day: day,
                    start_time: startTime,
                    end_time: endTime,
                    jenis_kegiatan: jenisKegiatan,
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'bentrok') {
                        if (confirm('Peringatan: ' + response.pesan + '\n\nApakah Anda tetap ingin menyimpan jadwal ini?')) {
                            $form[0].submit();
                        }
                    } else {
                        $form[0].submit();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('[AJAX Cek Bentrok Error]', xhr.responseText || error);
                    $form[0].submit();
                }
            });
        }); // <<< PENUTUP form-jadwal submit — KRITIS!

        // === 5. AJAX Handler for EDIT Modal (Event Delegation) ===
        $(document).on('click', '.btn-edit-jadwal', function(e) {
            e.preventDefault();
            var btn = $(this);
            var idJadwal = btn.attr('data-id') || btn.data('id');

            console.log('[Edit Jadwal] Tombol diklik, ID:', idJadwal);

            if (!idJadwal) {
                alert('ID Jadwal tidak ditemukan pada tombol.');
                return;
            }

            // Reset form sebelum isi data baru
            $('#form-edit-jadwal')[0].reset();
            $('#edit_id_jadwal').val(idJadwal);

            $.ajax({
                url: '<?= base_url("jadwal_fleksibel/get_detail/") ?>' + idJadwal,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('[Edit Jadwal] Data diterima:', response);

                    if (response && response.id_jadwal) {
                        // Isi semua field form
                        $('#edit_id_jadwal').val(response.id_jadwal);
                        $('#edit_class_id').val(response.class_id);
                        $('#edit_mapel_id').val(response.mapel_id);
                        $('#edit_day').val(response.day);
                        $('#edit_start_time').val(response.start_time);
                        $('#edit_end_time').val(response.end_time);
                        $('#edit_pola_mingguan').val(response.pola_mingguan);
                        $('#edit_jenis_kegiatan').val(response.jenis_kegiatan);
                        $('#edit_learning_link').val(response.learning_link || '');

                        // Re-init Select2 dengan dropdownParent agar tidak nyangkut di belakang modal
                        try {
                            if ($.fn.select2) {
                                $('#edit_class_id').select2({
                                    theme: 'bootstrap4',
                                    width: '100%',
                                    dropdownParent: $('#modal-edit')
                                });
                                $('#edit_mapel_id').select2({
                                    theme: 'bootstrap4',
                                    width: '100%',
                                    dropdownParent: $('#modal-edit')
                                });
                            }
                        } catch(err) {
                            console.warn('[Edit Jadwal] Select2 re-init error:', err);
                        }

                        // Tampilkan modal
                        $('#modal-edit').modal('show');
                    } else {
                        alert('Data jadwal tidak ditemukan.');
                    }
                },
                error: function(xhr) {
                    console.error('[Edit Jadwal] AJAX Error:', xhr.responseText);
                    alert('Gagal mengambil data jadwal dari server.');
                }
            });
        }); // <<< PENUTUP btn-edit-jadwal click

        // === 6. Submit Form Edit via AJAX ===
        $('#form-edit-jadwal').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);

            var formData = $form.serializeArray();
            var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
            var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
            var hasCsrf = false;
            $.each(formData, function(i, field) {
                if (field.name === csrfName) hasCsrf = true;
            });
            if (!hasCsrf) {
                formData.push({ name: csrfName, value: csrfHash });
            }

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $.param(formData),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        $('#modal-edit').modal('hide');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Jadwal berhasil diperbarui!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            alert('Jadwal berhasil diperbarui!');
                            location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal memperbarui jadwal.'
                            });
                        } else {
                            alert(response.message || 'Gagal memperbarui jadwal.');
                        }
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan sistem saat memperbarui jadwal.');
                }
            });
        }); // <<< PENUTUP form-edit-jadwal submit

    }); // <<< PENUTUP document.ready
</script>

