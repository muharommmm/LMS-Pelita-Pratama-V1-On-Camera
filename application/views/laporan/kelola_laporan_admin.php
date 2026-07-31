<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-3">
                <h1><?= $judul ?></h1>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Flash Alert -->
            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> <?= $this->session->flashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-1"></i> <?= $this->session->flashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Navigation Tabs -->
            <div class="card card-default my-shadow mb-4">
                <div class="card-header p-2">
                    <ul class="nav nav-pills" id="admin-laporan-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tab-insiden" data-toggle="tab"><i class="fas fa-exclamation-circle mr-1"></i> Kotak Laporan Insiden</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab-rekap-tutor" data-toggle="tab"><i class="fas fa-chart-bar mr-1"></i> Rekap Rapor Tutor</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#tab-kuesioner" data-toggle="tab"><i class="fas fa-question-circle mr-1"></i> Pengaturan Kuesioner</a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content">
                        
                        <!-- TAB 1: KOTAK LAPORAN INSIDEN -->
                        <div class="tab-pane active" id="tab-insiden">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="tableInsiden">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 50px;">No</th>
                                            <th>Pelapor</th>
                                            <th>Kategori & Tanggal Kejadian</th>
                                            <th>Deskripsi Kronologi</th>
                                            <th>Bukti Berkas</th>
                                            <th>Status</th>
                                            <th style="width: 150px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($laporan_insiden)) : ?>
                                            <?php $no = 1; foreach ($laporan_insiden as $l) : ?>
                                                <tr>
                                                    <td class="text-center align-middle"><?= $no++ ?></td>
                                                    <td class="align-middle">
                                                        <?php if ($l->is_anonymous == 1) : ?>
                                                            <span class="badge badge-secondary"><i class="fas fa-user-secret"></i> Anonim</span>
                                                        <?php else : ?>
                                                            <strong><?= htmlspecialchars($l->nama_siswa) ?></strong>
                                                            <br><span class="text-xs text-muted">Kelas: <?= htmlspecialchars($l->nama_kelas ?: 'Tidak ada') ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="badge badge-warning"><?= htmlspecialchars($l->kategori) ?></span>
                                                        <br><small class="text-muted"><i class="far fa-calendar-alt"></i> <?= date('d-m-Y', strtotime($l->tanggal_kejadian)) ?></small>
                                                    </td>
                                                    <td class="align-middle">
                                                        <p class="mb-1 text-sm"><?= nl2br(htmlspecialchars($l->deskripsi)) ?></p>
                                                        <?php if (!empty($l->catatan_admin)) : ?>
                                                            <div class="mt-2 p-2 bg-light rounded border-left border-info text-xs">
                                                                <strong>Tindak Lanjut Admin:</strong> <?= nl2br(htmlspecialchars($l->catatan_admin)) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php if ($l->bukti_file) : ?>
                                                            <a href="<?= base_url($l->bukti_file) ?>" target="_blank" class="btn btn-xs btn-outline-primary">
                                                                <i class="fas fa-file-download mr-1"></i> Buka Bukti
                                                            </a>
                                                        <?php else : ?>
                                                            <span class="text-xs text-muted">Tidak ada</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php if ($l->status === 'Pending') : ?>
                                                            <span class="badge badge-danger">Pending</span>
                                                        <?php elseif ($l->status === 'Diproses') : ?>
                                                            <span class="badge badge-primary">Diproses</span>
                                                        <?php else : ?>
                                                            <span class="badge badge-success">Selesai</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button type="button" class="btn btn-xs btn-info btn-block btn-tindak-lanjut" 
                                                                data-id="<?= $l->id_laporan ?>" 
                                                                data-status="<?= $l->status ?>" 
                                                                data-catatan="<?= htmlspecialchars($l->catatan_admin ?: '') ?>">
                                                            <i class="fas fa-gavel"></i> Tindak Lanjut
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: REKAP RAPOR TUTOR -->
                        <div class="tab-pane" id="tab-rekap-tutor">
                            <div class="accordion" id="accordionTutor">
                                <?php if (!empty($rekap_tutor)) : ?>
                                    <?php foreach ($rekap_tutor as $idx => $r) : ?>
                                        <div class="card card-outline card-info my-shadow mb-3">
                                            <div class="card-header cursor-pointer" data-toggle="collapse" data-target="#collapseTutor_<?= $r['id_guru'] ?>">
                                                <div class="d-flex justify-between align-items-center w-100">
                                                    <h6 class="card-title font-weight-bold mb-0 text-primary">
                                                        <i class="fas fa-user-tie mr-2"></i> <?= htmlspecialchars($r['nama_guru']) ?>
                                                        <span class="text-xs text-muted font-weight-normal ml-2">NIP: <?= htmlspecialchars($r['nip'] ?: '-') ?></span>
                                                    </h6>
                                                    <span class="badge badge-success text-xs"><?= $r['total_responses'] ?> Siswa Menilai</span>
                                                </div>
                                            </div>
                                            
                                            <div id="collapseTutor_<?= $r['id_guru'] ?>" class="collapse" data-parent="#accordionTutor">
                                                <div class="card-body bg-light">
                                                    <?php if ($r['total_responses'] > 0) : ?>
                                                        <div class="row">
                                                            <!-- Multiple Choice Charts/Summaries -->
                                                            <div class="col-md-6 border-right">
                                                                <h5 class="font-weight-bold text-xs text-muted uppercase tracking-wider mb-3">Rekapitulasi Kuesioner Pilihan Ganda</h5>
                                                                <div class="space-y-4">
                                                                    <?php 
                                                                    // Group choices by questions
                                                                    $grouped_choices = [];
                                                                    foreach ($r['choices'] as $c) {
                                                                        $grouped_choices[$c->pertanyaan][] = $c;
                                                                    }
                                                                    foreach ($grouped_choices as $q_text => $answers) :
                                                                    ?>
                                                                        <div class="mb-3 p-2 bg-white rounded shadow-sm border">
                                                                            <strong class="text-xs block text-slate-800 mb-2"><?= htmlspecialchars($q_text) ?></strong>
                                                                            <div class="table-responsive">
                                                                                <table class="table table-sm table-borderless text-xs mb-0">
                                                                                    <thead>
                                                                                        <tr class="border-bottom text-muted">
                                                                                            <th>Jawaban</th>
                                                                                            <th>Tanggal Kelas</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <?php foreach ($answers as $ans) : ?>
                                                                                            <tr>
                                                                                                <td class="align-middle">
                                                                                                    <span class="font-weight-semibold text-slate-800"><?= htmlspecialchars($ans->jawaban) ?></span>
                                                                                                </td>
                                                                                                <td class="align-middle text-muted">
                                                                                                    <i class="far fa-calendar-alt mr-1"></i> <?= !empty($ans->tanggal_evaluasi) ? date('d-m-Y', strtotime($ans->tanggal_evaluasi)) : '-' ?>
                                                                                                </td>
                                                                                            </tr>
                                                                                        <?php endforeach; ?>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                            <!-- Custom Feedback Comments -->
                                                            <div class="col-md-6">
                                                                <h5 class="font-weight-bold text-xs text-muted uppercase tracking-wider mb-3">Saran & Kritik Siswa (Teks)</h5>
                                                                <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                                                                    <?php foreach ($r['comments'] as $c) : ?>
                                                                        <div class="list-group-item bg-white p-2.5 mb-2 rounded shadow-sm border border-slate-100">
                                                                            <p class="mb-1 text-sm font-semibold">"<?= htmlspecialchars($c->jawaban) ?>"</p>
                                                                            <div class="d-flex justify-content-between text-[10px] text-muted mt-1">
                                                                                <span><i class="far fa-calendar-alt"></i> Tanggal Kelas: <strong><?= !empty($c->tanggal_evaluasi) ? date('d-m-Y', strtotime($c->tanggal_evaluasi)) : '-' ?></strong></span>
                                                                                <span><i class="far fa-clock"></i> Dikirim: <?= date('d-m-Y H:i', strtotime($c->tanggal)) ?></span>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php else : ?>
                                                        <p class="text-center text-muted text-sm my-3">Belum ada siswa yang menilai tutor ini.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p class="text-center text-muted">Belum ada data rekap tutor.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- TAB 3: PENGATURAN KUESIONER -->
                        <div class="tab-pane" id="tab-kuesioner">
                            <div class="row">
                                <!-- Col Pengaturan Parameter -->
                                <div class="col-md-4">
                                    <div class="card card-primary card-outline shadow-sm mb-4">
                                        <div class="card-header">
                                            <h3 class="card-title font-weight-bold text-xs"><i class="fas fa-cog mr-1"></i> Parameter Input Tanggal</h3>
                                        </div>
                                        <?= form_open('laporan/simpan_pengaturan_lapor', ['class' => 'p-3']) ?>
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="evaluasi_tanggal_tampil" name="evaluasi_tanggal_tampil" value="1" <?= (isset($lapor_settings['evaluasi_tanggal_tampil']) && $lapor_settings['evaluasi_tanggal_tampil'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label font-weight-bold text-xs" for="evaluasi_tanggal_tampil">Tampilkan Input Tanggal</label>
                                                </div>
                                                <small class="text-muted d-block mt-1">Mengizinkan/menyembunyikan input tanggal pembelajaran pada form siswa.</small>
                                            </div>
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="evaluasi_tanggal_wajib" name="evaluasi_tanggal_wajib" value="1" <?= (isset($lapor_settings['evaluasi_tanggal_wajib']) && $lapor_settings['evaluasi_tanggal_wajib'] == '1') ? 'checked' : '' ?>>
                                                    <label class="custom-control-label font-weight-bold text-xs" for="evaluasi_tanggal_wajib">Wajibkan Input Tanggal</label>
                                                </div>
                                                <small class="text-muted d-block mt-1">Jika aktif, siswa harus mengisi tanggal kelas untuk mengirim evaluasi.</small>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fas fa-save mr-1"></i> Simpan Pengaturan</button>
                                        <?= form_close() ?>
                                    </div>
                                </div>
                                
                                <!-- Col List Pertanyaan -->
                                <div class="col-md-8">
                                    <div class="card card-outline card-secondary shadow-sm">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h3 class="card-title font-weight-bold text-xs mb-0 text-slate-800"><i class="fas fa-list mr-1"></i> Pertanyaan Penilaian Kinerja</h3>
                                            <button type="button" class="btn btn-xs btn-primary ml-auto" data-toggle="modal" data-target="#modalTambahPertanyaan">
                                                <i class="fas fa-plus mr-1"></i> Tambah Pertanyaan
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-sm text-xs mb-0" id="tableKuesioner">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th style="width: 40px;">No</th>
                                                            <th>Teks Pertanyaan</th>
                                                            <th>Tipe</th>
                                                            <th>Pilihan Jawaban</th>
                                                            <th>Status</th>
                                                            <th style="width: 130px;">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($pertanyaan)) : ?>
                                                            <?php $no = 1; foreach ($pertanyaan as $q) : ?>
                                                                <tr>
                                                                    <td class="text-center align-middle"><?= $no++ ?></td>
                                                                    <td class="align-middle"><strong><?= htmlspecialchars($q->pertanyaan) ?></strong></td>
                                                                    <td class="text-center align-middle">
                                                                        <span class="badge badge-<?= $q->tipe === 'pilihan' ? 'info' : 'secondary' ?>">
                                                                            <?= $q->tipe === 'pilihan' ? 'Pilihan Ganda' : 'Esai Bebas (Teks)' ?>
                                                                        </span>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <?php if ($q->pilihan_jawaban) : ?>
                                                                            <?php 
                                                                            $opts = explode(',', $q->pilihan_jawaban);
                                                                            foreach ($opts as $opt) :
                                                                            ?>
                                                                                <span class="badge badge-light border mr-1 mb-1"><?= htmlspecialchars(trim($opt)) ?></span>
                                                                            <?php endforeach; ?>
                                                                        <?php else : ?>
                                                                            <span class="text-xs text-muted">Input esai/tulisan siswa</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        <span class="badge badge-<?= $q->is_active == 1 ? 'success' : 'danger' ?>">
                                                                            <?= $q->is_active == 1 ? 'Aktif' : 'Nonaktif' ?>
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        <button type="button" class="btn btn-xs btn-warning btn-edit-pertanyaan" 
                                                                                data-id="<?= $q->id_pertanyaan ?>" 
                                                                                data-pertanyaan="<?= htmlspecialchars($q->pertanyaan) ?>" 
                                                                                data-tipe="<?= $q->tipe ?>" 
                                                                                data-pilihan="<?= htmlspecialchars($q->pilihan_jawaban ?: '') ?>" 
                                                                                data-active="<?= $q->is_active ?>">
                                                                            <i class="fas fa-edit"></i> Edit
                                                                        </button>
                                                                        <a href="<?= base_url('laporan/hapus_pertanyaan/' . $q->id_pertanyaan) ?>" 
                                                                           onclick="return confirm('Hapus pertanyaan ini? Seluruh data jawaban siswa terkait pertanyaan ini juga akan terpengaruh.')" 
                                                                           class="btn btn-xs btn-danger">
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
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tindak Lanjut Insiden -->
<div class="modal fade" id="modalTindakLanjut" tabindex="-1" role="dialog" aria-labelledby="modalTindakLanjutLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTindakLanjutLabel"><i class="fas fa-gavel mr-1"></i> Tindak Lanjut Laporan Insiden</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('', ['id' => 'formTindakLanjut']) ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="status">Perbarui Status Aduan <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="Pending">Pending</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai (Ditutup)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="catatan_admin">Tulis Catatan / Tindak Lanjut dari Sekolah <span class="text-danger">*</span></label>
                    <textarea name="catatan_admin" id="catatan_admin" rows="5" class="form-control" placeholder="Tuliskan keputusan sekolah, tindakan yang telah diambil, atau klarifikasi hasil pertemuan di sini..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i> Simpan Catatan</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Modal Tambah Pertanyaan -->
<div class="modal fade" id="modalTambahPertanyaan" tabindex="-1" role="dialog" aria-labelledby="modalTambahPertanyaanLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahPertanyaanLabel">Tambah Pertanyaan Evaluasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('laporan/tambah_pertanyaan') ?>
            <div class="modal-body space-y-3">
                <div class="form-group">
                    <label for="pertanyaan">Teks Pertanyaan <span class="text-danger">*</span></label>
                    <input type="text" name="pertanyaan" id="pertanyaan" class="form-control" placeholder="Contoh: Bagaimana kerapian mengajar tutor?" required>
                </div>
                <div class="form-group">
                    <label for="tipe">Tipe Jawaban <span class="text-danger">*</span></label>
                    <select name="tipe" id="tipe" class="form-control" required>
                        <option value="teks">Esai Bebas (Teks)</option>
                        <option value="pilihan">Pilihan Ganda</option>
                    </select>
                </div>
                <div class="form-group d-none" id="group_pilihan">
                    <label for="pilihan_jawaban">Pilihan Ganda (Pisahkan dengan tanda koma) <span class="text-danger">*</span></label>
                    <input type="text" name="pilihan_jawaban" id="pilihan_jawaban" class="form-control" placeholder="Contoh: Sangat Baik,Baik,Cukup,Kurang">
                    <small class="text-muted text-xs">Pastikan memisahkan setiap pilihan dengan karakter koma tanpa spasi.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Pertanyaan</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- Modal Edit Pertanyaan -->
<div class="modal fade" id="modalEditPertanyaan" tabindex="-1" role="dialog" aria-labelledby="modalEditPertanyaanLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPertanyaanLabel">Edit Pertanyaan Evaluasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('', ['id' => 'formEditPertanyaan']) ?>
            <div class="modal-body space-y-3">
                <div class="form-group">
                    <label for="edit_pertanyaan">Teks Pertanyaan <span class="text-danger">*</span></label>
                    <input type="text" name="pertanyaan" id="edit_pertanyaan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_tipe">Tipe Jawaban <span class="text-danger">*</span></label>
                    <select name="tipe" id="edit_tipe" class="form-control" required>
                        <option value="teks">Esai Bebas (Teks)</option>
                        <option value="pilihan">Pilihan Ganda</option>
                    </select>
                </div>
                <div class="form-group d-none" id="edit_group_pilihan">
                    <label for="edit_pilihan_jawaban">Pilihan Ganda (Pisahkan dengan tanda koma) <span class="text-danger">*</span></label>
                    <input type="text" name="pilihan_jawaban" id="edit_pilihan_jawaban" class="form-control">
                </div>
                <div class="form-group">
                    <label for="edit_is_active">Status Keaktifan</label>
                    <select name="is_active" id="edit_is_active" class="form-control">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
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

<!-- Scripts -->
<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#tableInsiden').DataTable({
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
            $('#tableKuesioner').DataTable({
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

        // Toggle choices field on Create Question modal
        $('#tipe').on('change', function() {
            if ($(this).val() === 'pilihan') {
                $('#group_pilihan').removeClass('d-none');
                $('#pilihan_jawaban').attr('required', 'required');
            } else {
                $('#group_pilihan').addClass('d-none');
                $('#pilihan_jawaban').removeAttr('required');
            }
        });

        // Toggle choices field on Edit Question modal
        $('#edit_tipe').on('change', function() {
            if ($(this).val() === 'pilihan') {
                $('#edit_group_pilihan').removeClass('d-none');
                $('#edit_pilihan_jawaban').attr('required', 'required');
            } else {
                $('#edit_group_pilihan').addClass('d-none');
                $('#edit_pilihan_jawaban').removeAttr('required');
            }
        });

        // Tindak Lanjut button click
        $('.btn-tindak-lanjut').on('click', function() {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var catatan = $(this).data('catatan');

            $('#formTindakLanjut').attr('action', '<?= base_url("laporan/update_status_insiden/") ?>' + id);
            $('#status').val(status);
            $('#catatan_admin').val(catatan);

            $('#modalTindakLanjut').modal('show');
        });

        // Edit Pertanyaan button click
        $('.btn-edit-pertanyaan').on('click', function() {
            var id = $(this).data('id');
            var pertanyaan = $(this).data('pertanyaan');
            var tipe = $(this).data('tipe');
            var pilihan = $(this).data('pilihan');
            var active = $(this).data('active');

            $('#formEditPertanyaan').attr('action', '<?= base_url("laporan/edit_pertanyaan/") ?>' + id);
            $('#edit_pertanyaan').val(pertanyaan);
            $('#edit_tipe').val(tipe).trigger('change');
            $('#edit_pilihan_jawaban').val(pilihan);
            $('#edit_is_active').val(active);

            $('#modalEditPertanyaan').modal('show');
        });
    });
</script>
