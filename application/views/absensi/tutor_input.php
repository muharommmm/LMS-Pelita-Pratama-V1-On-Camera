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
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="class_select">Kelas Utama</label>
                                <select id="class_select" class="form-control">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($classes as $id_kelas => $nama_kelas) : ?>
                                        <option value="<?= $id_kelas ?>"><?= htmlspecialchars($nama_kelas) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="class_select_2">Kelas Kedua (Opsional)</label>
                                <select id="class_select_2" class="form-control">
                                    <option value="">-- Tidak Ada --</option>
                                    <?php foreach ($classes as $id_kelas => $nama_kelas) : ?>
                                        <option value="<?= $id_kelas ?>"><?= htmlspecialchars($nama_kelas) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="mapel_select">Mata Pelajaran</label>
                                <select id="mapel_select" class="form-control">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    <?php foreach ($mapels as $id_mapel => $nama_mapel) : ?>
                                        <option value="<?= $id_mapel ?>"><?= htmlspecialchars($nama_mapel) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_input">Tanggal</label>
                                <input type="date" id="date_input" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="jenis_kegiatan_select">Jenis Kegiatan</label>
                                <select id="jenis_kegiatan_select" class="form-control" required>
                                    <option value="offline">Tatap Muka Offline</option>
                                    <option value="online">Tatap Muka Online</option>
                                    <option value="check_task">Tugas</option>
                                    <option value="create_cbt">Soal Ujian Modul</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="btn_load" class="btn btn-primary btn-block mb-3">
                                <i class="fas fa-sync mr-1"></i> Tampilkan
                            </button>
                        </div>
                    </div>

                    <?= form_open('absensi/save_tutor_attendance', ['id' => 'attendanceForm']) ?>
                    <input type="hidden" name="class_id" id="hidden_class_id">
                    <input type="hidden" name="class_id_2" id="hidden_class_id_2">
                    <input type="hidden" name="mapel_id" id="hidden_mapel_id">
                    <input type="hidden" name="date" id="hidden_date">
                    <input type="hidden" name="jenis_kegiatan" id="hidden_jenis_kegiatan">
                    <input type="hidden" name="session_time" id="hidden_session_time">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="studentTable">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 120px;">Kelas</th>
                                    <th style="width: 150px;">NIS</th>
                                    <th>Nama Siswa</th>
                                    <th style="width: 300px;">Status Kehadiran</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="student_list_container">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Silakan pilih kelas, mata pelajaran, dan tanggal terlebih dahulu, lalu klik tombol "Tampilkan".</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-right" id="submit_container" style="display:none;">
                        <button type="button" id="btnSimpanAbsensi" class="btn btn-success btn-lg">
                            <i class="fas fa-save mr-1"></i> Simpan Kehadiran
                        </button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
            
            <!-- CARD 2: RIWAYAT PENGISIAN ABSENSI GURU -->
            <div class="card card-default my-shadow mb-4 border-left border-info" style="border-left-width: 4px !important;">
                <div class="card-header bg-light">
                    <h5 class="card-title text-info font-weight-bold"><i class="fas fa-history mr-2"></i> RIWAYAT PENGISIAN ABSENSI</h5>
                </div>
                <div class="card-body">
                    <!-- Row Filter Riwayat -->
                    <div class="row mb-4 align-items-end">
                        <div class="col-md-4 col-sm-12">
                            <label for="filter_month_history" class="font-weight-bold text-secondary text-sm">Filter Bulan</label>
                            <select id="filter_month_history" class="form-control form-control-sm">
                                <option value="">-- Default (7 Hari Terakhir) --</option>
                                <option value="all">Semua Bulan</option>
                                <?php
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                foreach ($months as $num => $name) : ?>
                                    <option value="<?= $num ?>"><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label for="filter_class_history" class="font-weight-bold text-secondary text-sm">Filter Kelas</label>
                            <select id="filter_class_history" class="form-control form-control-sm">
                                <option value="">Semua Kelas</option>
                                <?php foreach ($classes as $id_kelas => $nama_kelas) : ?>
                                    <option value="<?= $id_kelas ?>"><?= htmlspecialchars($nama_kelas) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <button type="button" id="btn_filter_history" class="btn btn-info btn-sm btn-block text-white font-weight-bold">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover text-sm">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th>Hari, Tanggal & Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Metode KBM</th>
                                    <th style="width: 120px;">Jumlah Siswa</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="history_list_container">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat riwayat absensi...</td>
                                </tr>
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
        // Prevent duplicate class selection
        $('#class_select').on('change', function() {
            let selectedClass = $(this).val();
            
            // Re-enable all options first
            $('#class_select_2 option').prop('disabled', false).show();
            
            if (selectedClass) {
                // Disable the option that matches the selected primary class
                $('#class_select_2 option[value="' + selectedClass + '"]').prop('disabled', true).hide();
                
                // If the second dropdown is currently selecting the newly disabled option, reset it
                if ($('#class_select_2').val() === selectedClass) {
                    $('#class_select_2').val('');
                }
            }
        });

        // Perbaiki Bug Radio Button Dinamis Bootstrap
        $('#student_list_container').on('click', '.btn-group-toggle label.btn', function() {
            // Hapus class active dari semua label di grup yang sama
            $(this).siblings().removeClass('active');
            // Tambahkan class active ke label yang diklik
            $(this).addClass('active');
            // Centang radio button di dalamnya
            $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
        });

        // Reset session time lock if user changes filters manually
        $('#class_select, #class_select_2, #mapel_select, #date_input, #jenis_kegiatan_select').on('change', function() {
            $('#hidden_session_time').val('');
        });

        $('#btn_load').on('click', function() {
            let classId = $('#class_select').val();
            let classId2 = $('#class_select_2').val();
            let mapelId = $('#mapel_select').val();
            let date = $('#date_input').val();
            let jenisKegiatan = $('#jenis_kegiatan_select').val();
            let sessionTime = $('#hidden_session_time').val();
 
            if (!classId || !mapelId || !date || !jenisKegiatan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan lengkapi semua pilihan termasuk Jenis Kegiatan.'
                });
                return;
            }
 
            // Sync hidden inputs
            $('#hidden_class_id').val(classId);
            $('#hidden_class_id_2').val(classId2);
            $('#hidden_mapel_id').val(mapelId);
            $('#hidden_date').val(date);
            $('#hidden_jenis_kegiatan').val(jenisKegiatan);
 
            $('#student_list_container').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat data...</td></tr>');
            $('#submit_container').hide();
 
            $.ajax({
                url: '<?= base_url("absensi/load_students_tutor") ?>',
                type: 'POST',
                data: {
                    class_id: classId,
                    class_id_2: classId2,
                    mapel_id: mapelId,
                    date: date,
                    jenis_kegiatan: jenisKegiatan,
                    session_time: sessionTime,
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status) {
                        $('#student_list_container').html(response.html);
                        $('#submit_container').show();
                        
                        // Store day and method comparison variables
                        window.isHariBerbeda = response.is_hari_berbeda;
                        window.hariSeharusnya = response.hari_seharusnya;
                        window.hariDipilih = response.hari_dipilih;
                        window.isMetodeBerbeda = response.is_metode_berbeda;
                        window.metodeSeharusnya = response.metode_seharusnya;
                        window.metodeSeharusnyaRaw = response.metode_seharusnya_raw;
                        window.metodeDipilih = response.metode_dipilih;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                        $('#student_list_container').html('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>');
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan sistem.'
                    });
                    $('#student_list_container').html('<tr><td colspan="6" class="text-center text-danger">Terjadi kesalahan sistem.</td></tr>');
                }
            });
        });

        // Function to load attendance history dynamically via AJAX
        function loadHistory(month = '', classId = '') {
            $('#history_list_container').html('<tr><td colspan="7" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat riwayat absensi...</td></tr>');
            
            $.ajax({
                url: '<?= base_url("absensi/load_history_tutor") ?>',
                type: 'POST',
                data: {
                    month: month,
                    class_id: classId,
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status) {
                        $('#history_list_container').html(response.html);
                    } else {
                        $('#history_list_container').html('<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat riwayat absensi.</td></tr>');
                    }
                },
                error: function() {
                    $('#history_list_container').html('<tr><td colspan="7" class="text-center text-danger py-4">Terjadi kesalahan koneksi saat memuat riwayat.</td></tr>');
                }
            });
        }

        // Load default history (last 7 days) on page load
        loadHistory();

        // Filter button click handler
        $('#btn_filter_history').on('click', function() {
            let month = $('#filter_month_history').val();
            let classId = $('#filter_class_history').val();
            loadHistory(month, classId);
        });

        // Edit button click handler (using delegation for dynamically loaded rows)
        $(document).on('click', '.btn-edit-absensi', function() {
            // Use attr to get raw string representation instead of jQuery auto-cast
            let classIdsRaw = $(this).attr('data-classids') || '';
            let classIds = classIdsRaw.toString().split(',');
            let mapelId = $(this).attr('data-mapel');
            let date = $(this).attr('data-date');
            let jenis = $(this).attr('data-jenis');
            let time = $(this).attr('data-time');

            // Populate form
            $('#class_select').val(classIds[0]).trigger('change');
            if (classIds.length > 1) {
                // Wait slightly for primary select change event to complete
                setTimeout(function() {
                    $('#class_select_2').val(classIds[1]).trigger('change');
                }, 150);
            } else {
                $('#class_select_2').val('').trigger('change');
            }

            $('#mapel_select').val(mapelId);
            $('#date_input').val(date);
            $('#jenis_kegiatan_select').val(jenis);
            
            // Set session time
            $('#hidden_session_time').val(time);

            // Scroll up to form smoothly
            $('html, body').animate({
                scrollTop: $(".card-title").first().offset().top - 20
            }, 500);

            // Trigger load after dropdowns populate
            setTimeout(function() {
                $('#btn_load').click();
            }, 300);
        });

        // Intercept Simpan Kehadiran button click
        $('#btnSimpanAbsensi').on('click', function(e) {
            e.preventDefault();

            // Update hidden jenis_kegiatan terlebih dahulu
            let currentMetodeVal = $('#jenis_kegiatan_select').val() || 'offline';
            let metodeTeks = $('#jenis_kegiatan_select option:selected').text() || '';
            $('#hidden_jenis_kegiatan').val(currentMetodeVal);

            // ================================================================
            // SNAPSHOT DATA SEBELUM SWAL DIBUKA
            // Kumpulkan postData SEKARANG agar tidak terpengaruh SweetAlert
            // ================================================================
            let parts = [];

            // 1. Ambil semua hidden field & CSRF
            $('#attendanceForm').find('input[type="hidden"]').each(function() {
                parts.push(encodeURIComponent($(this).attr('name')) + '=' + encodeURIComponent($(this).val()));
            });

            // 2. Ambil status dari class 'active' (tidak bergantung pada prop 'checked')
            $('#student_list_container .btn-group-toggle').each(function() {
                let activeRadio = $(this).find('label.active input[type="radio"]');
                if (activeRadio.length) {
                    parts.push(encodeURIComponent(activeRadio.attr('name')) + '=' + encodeURIComponent(activeRadio.attr('value')));
                } else {
                    // Fallback: cek prop checked
                    let checkedRadio = $(this).find('input[type="radio"]:checked');
                    if (checkedRadio.length) {
                        parts.push(encodeURIComponent(checkedRadio.attr('name')) + '=' + encodeURIComponent(checkedRadio.val()));
                    }
                }
            });

            let snapshotData = parts.join('&');
            console.log('SNAPSHOT POST DATA:', snapshotData);

            // ================================================================
            // CEK KETIDAKSESUAIAN JADWAL
            // ================================================================
            let selectedDateStr = $('#date_input').val() || '';
            let dateObj = new Date(selectedDateStr);
            let namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            let hariDipilihReal = (!isNaN(dateObj.getDay())) ? namaHari[dateObj.getDay()] : 'Tidak Diketahui';

            let metodeRaw = window.metodeSeharusnyaRaw || '';
            let isHariBerbedaSafe = window.isHariBerbeda === true;
            let isMetodeMismatch = (metodeRaw !== '') ? (currentMetodeVal !== metodeRaw) : (window.isMetodeBerbeda === true);

            // Fungsi kirim AJAX menggunakan snapshot data yang sudah dikumpulkan
            function kirimAbsensi(postData) {
                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.ajax({
                    url: '<?= base_url("absensi/save_tutor_attendance") ?>',
                    type: 'POST',
                    data: postData,
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire('Berhasil!', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', response.message || 'Data tidak valid.', 'warning');
                        }
                    },
                    error: function(xhr) {
                        console.error('RAW RESPONSE:', xhr.responseText);
                        Swal.fire('Server Error', 'HTTP ' + xhr.status + '<br><small>' + (xhr.responseText || '').substring(0, 300) + '</small>', 'error');
                    }
                });
            }

            if (isHariBerbedaSafe || isMetodeMismatch) {
                let pesanHtml = 'Ditemukan ketidaksesuaian dengan jadwal asli Anda:<br><br>';
                if (isHariBerbedaSafe) {
                    pesanHtml += '📅 Hari seharusnya: <b>' + (window.hariSeharusnya || '-') + '</b><br>';
                    pesanHtml += 'Anda memilih hari: <b class="text-danger">' + hariDipilihReal + '</b><br><br>';
                }
                if (isMetodeMismatch) {
                    pesanHtml += '💻 Metode seharusnya: <b>' + (window.metodeSeharusnya || '-') + '</b><br>';
                    pesanHtml += 'Anda memilih metode: <b class="text-danger">' + metodeTeks + '</b><br><br>';
                }
                pesanHtml += 'Klik <b>Ya, Yakin!</b> jika Anda sedang melakukan penyesuaian (misal: Kelas Pengganti, Jadwal Geser, Pindah Mode).';

                Swal.fire({
                    title: 'Peringatan Ketidaksesuaian!',
                    html: pesanHtml,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Yakin!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    // Gunakan result.value untuk kompatibilitas Swal2 versi <10
                    if (result.isConfirmed || result.value) {
                        // Gunakan snapshotData yang sudah dikumpulkan sebelum Swal
                        kirimAbsensi(snapshotData);
                    }
                });
            } else {
                kirimAbsensi(snapshotData);
            }
        });
    });
</script>
