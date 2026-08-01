<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-primary font-weight-bold"><i class="fas fa-binoculars mr-2"></i> <?= $judul ?></h1>
                    <p class="text-muted text-sm"><?= $subjudul ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div id="accordionMonitoring">
                
                <!-- ACCORDION 1: PEMANTAUAN SISWA -->
                <div class="card card-default my-shadow mb-3 border-left border-primary" style="border-left-width: 4px !important;">
                    <div class="card-header bg-light" id="headingSiswa">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-primary font-weight-bold text-left w-100 d-flex justify-content-between align-items-center" data-toggle="collapse" data-target="#collapseSiswa" aria-expanded="true" aria-controls="collapseSiswa" style="text-decoration: none;">
                                <span><i class="fas fa-user-graduate mr-2"></i> PEMANTAUAN SISWA (MONITORING PERKEMBANGAN)</span>
                                <i class="fas fa-chevron-down fold-icon"></i>
                            </button>
                        </h5>
                    </div>

                    <div id="collapseSiswa" class="collapse show" aria-labelledby="headingSiswa" data-parent="#accordionMonitoring">
                        <div class="card-body">
                            <!-- Filter Siswa -->
                            <div class="row mb-4 align-items-end">
                                <div class="col-md-6 col-sm-12">
                                    <label for="select-siswa" class="font-weight-bold text-secondary">Pilih Nama / NIS Siswa</label>
                                    <select id="select-siswa" class="form-control select2" style="width: 100%;">
                                        <option value="">-- Ketik Nama atau NIS Siswa --</option>
                                        <?php foreach ($siswa_list as $s): ?>
                                            <option value="<?= $s->id_siswa ?>"><?= htmlspecialchars($s->nama) ?> (NIS: <?= $s->nis ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6 mt-2">
                                    <button type="button" id="btn-print-siswa" class="btn btn-success btn-block btn-lg d-none">
                                        <i class="fas fa-print mr-2"></i> Cetak Laporan Siswa
                                    </button>
                                </div>
                            </div>

                            <!-- Loading Spinner -->
                            <div id="loading-siswa" class="text-center d-none my-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Menarik seluruh data riwayat siswa...</p>
                            </div>

                            <!-- Area Tampilan Data Siswa -->
                            <div id="siswa-result-container" class="d-none">
                                <div id="print-siswa-section">
                                    <!-- Profil Header -->
                                    <div class="row mb-4 p-3 bg-light rounded shadow-sm border">
                                        <div class="col-md-6">
                                            <h4 class="font-weight-bold text-primary mb-1" id="lbl-siswa-nama">-</h4>
                                            <p class="text-muted mb-0">NIS: <span id="lbl-siswa-nis">-</span></p>
                                        </div>
                                        <div class="col-md-6 text-md-right mt-2 mt-md-0">
                                            <h5 class="text-secondary font-weight-bold mb-0">Kelas: <span id="lbl-siswa-kelas" class="badge badge-primary px-3 py-2">-</span></h5>
                                        </div>
                                    </div>

                                    <!-- Row 1: Kehadiran & Jadwal -->
                                    <div class="row">
                                        <!-- Kehadiran -->
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-user-check mr-2 text-success"></i> Ringkasan Kehadiran (Semester Ini)</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row text-center mb-3">
                                                        <div class="col-3">
                                                            <div class="border rounded p-2 bg-success text-white">
                                                                <h4 class="font-weight-bold mb-0" id="abs-hadir">0</h4>
                                                                <span class="text-xs">Hadir</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="border rounded p-2 bg-primary text-white">
                                                                <h4 class="font-weight-bold mb-0" id="abs-sakit">0</h4>
                                                                <span class="text-xs">Sakit</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="border rounded p-2 bg-warning text-dark">
                                                                <h4 class="font-weight-bold mb-0" id="abs-izin">0</h4>
                                                                <span class="text-xs">Izin</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="border rounded p-2 bg-danger text-white">
                                                                <h4 class="font-weight-bold mb-0" id="abs-alpha">0</h4>
                                                                <span class="text-xs">Alpha</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <h6 class="font-weight-bold text-muted">Persentase Kehadiran Fisik & KBM:</h6>
                                                        <div class="progress" style="height: 25px;">
                                                            <div id="progress-kehadiran" class="progress-bar progress-bar-striped progress-bar-animated bg-success font-weight-bold" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Jadwal Fleksibel -->
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-calendar-alt mr-2 text-primary"></i> Jadwal Pelajaran Aktif (Jadwal Fleksibel)</h6>
                                                </div>
                                                <div class="card-body p-0 table-responsive" style="max-height: 230px; overflow-y: auto;">
                                                    <table class="table table-sm table-striped mb-0 text-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Hari</th>
                                                                <th>Jam / Waktu</th>
                                                                <th>Mata Pelajaran</th>
                                                                <th>Pola</th>
                                                                <th>Jenis Kegiatan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbl-siswa-jadwal">
                                                            <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada jadwal terdaftar</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Row 2: Tugas & Materi -->
                                    <div class="row">
                                        <!-- Tugas -->
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-tasks mr-2 text-danger"></i> Monitoring Pengerjaan Tugas</h6>
                                                </div>
                                                <div class="card-body p-0 table-responsive" style="max-height: 300px; overflow-y: auto;">
                                                    <table class="table table-sm table-striped mb-0 text-xs">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th>Tugas</th>
                                                                <th>Mapel / Guru</th>
                                                                <th>Status</th>
                                                                <th>Nilai</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbl-siswa-tugas">
                                                            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada tugas terdaftar</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Materi -->
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-book-open mr-2 text-info"></i> Riwayat Pembelajaran Materi</h6>
                                                </div>
                                                <div class="card-body p-0 table-responsive" style="max-height: 300px; overflow-y: auto;">
                                                    <table class="table table-sm table-striped mb-0 text-xs">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th>Materi</th>
                                                                <th>Mapel / Guru</th>
                                                                <th>Status</th>
                                                                <th>Dibaca Pada</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbl-siswa-materi">
                                                            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada materi terdaftar</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Row 3: CBT Ujian -->
                                    <div class="row">
                                        <!-- Hasil Ujian -->
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-graduation-cap mr-2 text-purple"></i> Hasil Ujian CBT (Ujian Selesai)</h6>
                                                </div>
                                                <div class="card-body p-0 table-responsive" style="max-height: 250px; overflow-y: auto;">
                                                    <table class="table table-sm table-striped mb-0 text-xs">
                                                        <thead>
                                                            <tr>
                                                                <th>Ujian</th>
                                                                <th>Waktu Submit</th>
                                                                <th>Nilai Total</th>
                                                                <th>Koreksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbl-siswa-cbt-done">
                                                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada ujian diselesaikan</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ujian Belum Dikerjakan -->
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-exclamation-triangle mr-2 text-warning"></i> Ujian CBT Aktif (Belum Dikerjakan)</h6>
                                                </div>
                                                <div class="card-body p-0 table-responsive" style="max-height: 250px; overflow-y: auto;">
                                                    <table class="table table-sm table-striped mb-0 text-xs">
                                                        <thead>
                                                            <tr>
                                                                <th>Ujian</th>
                                                                <th>Durasi</th>
                                                                <th>Batas Waktu</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="tbl-siswa-cbt-pending">
                                                            <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada ujian pending</td></tr>
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

                <!-- ACCORDION 2: PEMANTAUAN TUTOR / GURU -->
                <div class="card card-default my-shadow mb-3 border-left border-success" style="border-left-width: 4px !important;">
                    <div class="card-header bg-light" id="headingTutor">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-success collapsed font-weight-bold text-left w-100 d-flex justify-content-between align-items-center" data-toggle="collapse" data-target="#collapseTutor" aria-expanded="false" aria-controls="collapseTutor" style="text-decoration: none;">
                                <span><i class="fas fa-chalkboard-teacher mr-2"></i> PEMANTAUAN TUTOR / GURU (KINERJA & HONORARIUM)</span>
                                <i class="fas fa-chevron-right fold-icon"></i>
                            </button>
                        </h5>
                    </div>

                    <div id="collapseTutor" class="collapse" aria-labelledby="headingTutor" data-parent="#accordionMonitoring">
                        <div class="card-body">
                            <!-- Filter Tutor -->
                            <div class="row mb-4 align-items-end">
                                <div class="col-md-4 col-sm-12">
                                    <label for="select-tutor" class="font-weight-bold text-secondary">Pilih Nama Guru / Tutor</label>
                                    <select id="select-tutor" class="form-control select2" style="width: 100%;">
                                        <option value="">-- Pilih Guru / Tutor --</option>
                                        <?php foreach ($tutor_list as $t): ?>
                                            <option value="<?= $t->id_guru ?>"><?= htmlspecialchars($t->nama_guru) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label for="tutor-start-date" class="font-weight-bold text-secondary">Mulai Tanggal</label>
                                    <input type="date" id="tutor-start-date" class="form-control" value="<?= date('Y-m-01') ?>">
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <label for="tutor-end-date" class="font-weight-bold text-secondary">Hingga Tanggal</label>
                                    <input type="date" id="tutor-end-date" class="form-control" value="<?= date('Y-m-t') ?>">
                                </div>
                                <div class="col-md-2 col-sm-12 mt-2">
                                    <button type="button" id="btn-cari-tutor" class="btn btn-success btn-block btn-lg">
                                        <i class="fas fa-search mr-2"></i> Filter
                                    </button>
                                </div>
                            </div>

                            <!-- Loading Spinner -->
                            <div id="loading-tutor" class="text-center d-none my-5">
                                <div class="spinner-border text-success" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Menghitung aktivitas, penilaian, dan rekap honorarium tutor...</p>
                            </div>

                            <!-- Area Tampilan Data Tutor -->
                            <div id="tutor-result-container" class="d-none">
                                <!-- Ringkasan Profil & Aktivitas KBM -->
                                <div class="row mb-4 p-3 bg-light rounded shadow-sm border align-items-center">
                                    <div class="col-md-6">
                                        <h4 class="font-weight-bold text-success mb-1" id="lbl-tutor-nama">-</h4>
                                        <p class="text-muted mb-0">NIP: <span id="lbl-tutor-nip">-</span></p>
                                    </div>
                                    <div class="col-md-6 text-md-right mt-2 mt-md-0">
                                        <span class="badge badge-info px-3 py-2 mr-2">Materi Terbuat: <strong id="lbl-total-materi">0</strong></span>
                                        <span class="badge badge-danger px-3 py-2">Tugas Terbuat: <strong id="lbl-total-tugas">0</strong></span>
                                    </div>
                                </div>

                                <!-- Row 1: Honorarium & Status Tugas -->
                                <div class="row">
                                    <!-- Rekap Keuangan Honorarium -->
                                    <div class="col-md-7 mb-4">
                                        <div class="card h-100 border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-hand-holding-usd mr-2 text-success"></i> Rekap Penyaluran Honorarium Tutor (Rentang Waktu)</h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <table class="table table-striped table-bordered text-sm mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Kategori Kegiatan</th>
                                                            <th class="text-right">Sudah Cair (Paid)</th>
                                                            <th class="text-right">Belum Cair (Pending)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Tatap Muka Offline</td>
                                                            <td class="text-right text-success font-weight-bold" id="honor-paid-offline">Rp 0</td>
                                                            <td class="text-right text-danger font-weight-bold" id="honor-pending-offline">Rp 0</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tatap Muka Online</td>
                                                            <td class="text-right text-success font-weight-bold" id="honor-paid-online">Rp 0</td>
                                                            <td class="text-right text-danger font-weight-bold" id="honor-pending-online">Rp 0</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Pemeriksaan Tugas</td>
                                                            <td class="text-right text-success font-weight-bold" id="honor-paid-task">Rp 0</td>
                                                            <td class="text-right text-danger font-weight-bold" id="honor-pending-task">Rp 0</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Pembuatan Bank Soal CBT</td>
                                                            <td class="text-right text-success font-weight-bold" id="honor-paid-cbt">Rp 0</td>
                                                            <td class="text-right text-danger font-weight-bold" id="honor-pending-cbt">Rp 0</td>
                                                        </tr>
                                                        <tr class="bg-light">
                                                            <th class="text-primary">TOTAL HONORARIUM</th>
                                                            <th class="text-right text-success font-weight-bold" id="honor-paid-total">Rp 0</th>
                                                            <th class="text-right text-danger font-weight-bold" id="honor-pending-total">Rp 0</th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Penilaian Tugas -->
                                    <div class="col-md-5 mb-4">
                                        <div class="card h-100 border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-check-double mr-2 text-danger"></i> Rasio Penilaian Tugas Siswa</h6>
                                            </div>
                                            <div class="card-body p-0 table-responsive" style="max-height: 250px; overflow-y: auto;">
                                                <table class="table table-sm table-striped mb-0 text-xs">
                                                    <thead>
                                                        <tr>
                                                            <th>Judul Tugas</th>
                                                            <th>Kelas</th>
                                                            <th>Dikumpul</th>
                                                            <th>Dinilai</th>
                                                            <th>Belum Nilai</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbl-tutor-tugas-stats">
                                                        <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada tugas terdaftar</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 2: Chat Terakhir & CBT Essay Pending -->
                                <div class="row">
                                    <!-- Chat Terakhir -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-comments mr-2 text-primary"></i> Aktivitas Chat & Diskusi Tutor</h6>
                                            </div>
                                            <div class="card-body p-0 table-responsive" style="max-height: 250px; overflow-y: auto;">
                                                <table class="table table-sm table-striped mb-0 text-xs">
                                                    <thead>
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Topik Postingan</th>
                                                            <th>Isi Komentar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbl-tutor-chat">
                                                        <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada aktivitas chat terdeteksi</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CBT Pending Koreksi Esai -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="font-weight-bold text-secondary mb-0"><i class="fas fa-edit mr-2 text-warning"></i> Ujian CBT Butuh Koreksi Esai Manual</h6>
                                            </div>
                                            <div class="card-body p-0 table-responsive" style="max-height: 250px; overflow-y: auto;">
                                                <table class="table table-sm table-striped mb-0 text-xs">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Siswa</th>
                                                            <th>Nama Ujian</th>
                                                            <th>Waktu Submit</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbl-tutor-cbt-pending">
                                                        <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada ujian pending koreksi</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 2.5: Jadwal Mengajar Tutor -->
                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <div class="card my-shadow border border-success">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="font-weight-bold text-success mb-0"><i class="fas fa-calendar-alt mr-2 text-success"></i> Jadwal Mengajar Tutor (Jadwal Fleksibel Terkonsolidasi)</h6>
                                            </div>
                                            <div class="card-body p-0 table-responsive" style="max-height: 250px; overflow-y: auto;">
                                                <table class="table table-sm table-striped mb-0 text-xs">
                                                    <thead>
                                                        <tr>
                                                            <th>Hari</th>
                                                            <th>Jam / Waktu</th>
                                                            <th>Kelas</th>
                                                            <th>Mata Pelajaran</th>
                                                            <th>Pola & Kegiatan (Metode Ajar)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbl-tutor-jadwal">
                                                        <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada jadwal mengajar terdaftar</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 3: Rapor Evaluasi & Pengaduan Siswa -->
                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <div class="card my-shadow border border-danger">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="font-weight-bold text-danger mb-0">
                                                    <i class="fas fa-exclamation-triangle mr-2 text-danger"></i> Rapor Evaluasi & Masukan Laporan Siswa terhadap Tutor
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <!-- Kolom Kuesioner Pilihan Ganda -->
                                                    <div class="col-md-6 border-right">
                                                        <h6 class="font-weight-bold text-xs text-muted uppercase tracking-wider mb-3"><i class="fas fa-list-ol mr-1"></i> Rekapitulasi Penilaian Pilihan Ganda</h6>
                                                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                                            <table class="table table-sm table-striped mb-0 text-xs">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Pertanyaan</th>
                                                                        <th>Jawaban Siswa</th>
                                                                        <th>Tanggal Kelas</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="tbl-tutor-eval-choices">
                                                                    <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data kuesioner</td></tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Kolom Kritik & Saran Teks -->
                                                    <div class="col-md-6">
                                                        <h6 class="font-weight-bold text-xs text-muted uppercase tracking-wider mb-3"><i class="fas fa-comment-dots mr-1"></i> Saran & Kritik Aduan Siswa (Teks)</h6>
                                                        <div class="list-group list-group-flush" id="list-tutor-eval-comments" style="max-height: 250px; overflow-y: auto;">
                                                            <div class="text-center text-muted py-3 small">Belum ada kritik & saran yang dilaporkan.</div>
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
                </div>

            </div>
        </div>
    </section>
</div>

<script>
    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return 'Rp ' + ribuan;
    }

    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "-- Cari atau Pilih --",
            allowClear: true
        });

        // Accordion Icon Toggles
        $('#accordionMonitoring').on('show.bs.collapse', function (e) {
            $(e.target).prev('.card-header').find('.fold-icon').removeClass('fa-chevron-right').addClass('fa-chevron-down');
        });
        $('#accordionMonitoring').on('hide.bs.collapse', function (e) {
            $(e.target).prev('.card-header').find('.fold-icon').removeClass('fa-chevron-down').addClass('fa-chevron-right');
        });

        // ------------------ SISWA AJAX ------------------
        $('#select-siswa').on('change', function() {
            var id_siswa = $(this).val();
            if(id_siswa === '') {
                $('#siswa-result-container').addClass('d-none');
                $('#btn-print-siswa').addClass('d-none');
                return;
            }

            $('#loading-siswa').removeClass('d-none');
            $('#siswa-result-container').addClass('d-none');
            $('#btn-print-siswa').addClass('d-none');

            ajaxcsrf();
            $.ajax({
                url: base_url + 'monitoring/get_siswa_data',
                type: 'POST',
                data: { id_siswa: id_siswa },
                dataType: 'json',
                success: function(res) {
                    $('#loading-siswa').addClass('d-none');
                    if(res.status) {
                        // Render Profile
                        $('#lbl-siswa-nama').text(res.siswa.nama);
                        $('#lbl-siswa-nis').text(res.siswa.nis);
                        $('#lbl-siswa-kelas').text(res.siswa.kelas);

                        // Render Kehadiran
                        $('#abs-hadir').text(res.kehadiran.detail.H);
                        $('#abs-sakit').text(res.kehadiran.detail.S);
                        $('#abs-izin').text(res.kehadiran.detail.I);
                        $('#abs-alpha').text(res.kehadiran.detail.A);

                        var totalAbsen = res.kehadiran.total;
                        var pct = 0;
                        if(totalAbsen > 0) {
                            pct = Math.round((res.kehadiran.detail.H / totalAbsen) * 100);
                        }
                        $('#progress-kehadiran').css('width', pct + '%').text(pct + '%').attr('aria-valuenow', pct);
                        if(pct < 50) {
                            $('#progress-kehadiran').removeClass('bg-success bg-warning').addClass('bg-danger');
                        } else if(pct < 75) {
                            $('#progress-kehadiran').removeClass('bg-success bg-danger').addClass('bg-warning');
                        } else {
                            $('#progress-kehadiran').removeClass('bg-warning bg-danger').addClass('bg-success');
                        }

                        // Render Jadwal
                        var htmlJadwal = '';
                        if(res.jadwal.length > 0) {
                            $.each(res.jadwal, function(i, v) {
                                var kgtClass = 'badge-secondary';
                                var kgtLower = (v.kegiatan || '').toLowerCase();
                                if(kgtLower === 'offline') {
                                    kgtClass = 'badge-success';
                                } else if(kgtLower === 'online') {
                                    kgtClass = 'badge-info';
                                } else if(kgtLower === 'tugas') {
                                    kgtClass = 'badge-warning';
                                }
                                
                                htmlJadwal += '<tr>' +
                                    '<td class="font-weight-bold">' + v.hari + '</td>' +
                                    '<td>' + v.waktu + '</td>' +
                                    '<td>' + v.mapel + '</td>' +
                                    '<td><span class="badge badge-pill badge-secondary">' + v.pola + '</span></td>' +
                                    '<td><span class="badge badge-pill ' + kgtClass + '">' + v.kegiatan + '</span></td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlJadwal = '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada jadwal KBM fleksibel untuk kelas siswa ini</td></tr>';
                        }
                        $('#tbl-siswa-jadwal').html(htmlJadwal);

                        // Render Tugas
                        var htmlTugas = '';
                        if(res.tugas.length > 0) {
                            $.each(res.tugas, function(i, v) {
                                var badgeClass = v.status === 'Sudah Dikerjakan' ? 'badge-success' : 'badge-danger';
                                htmlTugas += '<tr>' +
                                    '<td class="font-weight-bold">' + v.judul + '</td>' +
                                    '<td>' + v.mapel + '<br><small class="text-muted">' + v.guru + '</small></td>' +
                                    '<td><span class="badge ' + badgeClass + '">' + v.status + '</span><br><small class="text-muted">' + v.waktu + '</small></td>' +
                                    '<td class="font-weight-bold text-center text-primary">' + v.nilai + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlTugas = '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada tugas terdaftar</td></tr>';
                        }
                        $('#tbl-siswa-tugas').html(htmlTugas);

                        // Render Materi
                        var htmlMateri = '';
                        if(res.materi.length > 0) {
                            $.each(res.materi, function(i, v) {
                                var badgeClass = v.status === 'Sudah Dibaca' ? 'badge-success' : 'badge-danger';
                                htmlMateri += '<tr>' +
                                    '<td class="font-weight-bold">' + v.judul + '</td>' +
                                    '<td>' + v.mapel + '<br><small class="text-muted">' + v.guru + '</small></td>' +
                                    '<td><span class="badge ' + badgeClass + '">' + v.status + '</span></td>' +
                                    '<td>' + v.waktu + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlMateri = '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada materi terdaftar</td></tr>';
                        }
                        $('#tbl-siswa-materi').html(htmlMateri);

                        // Render CBT Done
                        var htmlCbtDone = '';
                        if(res.cbt.completed.length > 0) {
                            $.each(res.cbt.completed, function(i, v) {
                                var koreksiBadge = v.dikoreksi === 'Sudah' ? 'badge-success' : 'badge-warning';
                                htmlCbtDone += '<tr>' +
                                    '<td class="font-weight-bold">' + v.nama + '</td>' +
                                    '<td>' + v.waktu + '</td>' +
                                    '<td class="font-weight-bold text-success text-center">' + v.nilai + '</td>' +
                                    '<td><span class="badge ' + koreksiBadge + '">' + v.dikoreksi + '</span></td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlCbtDone = '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada ujian diselesaikan</td></tr>';
                        }
                        $('#tbl-siswa-cbt-done').html(htmlCbtDone);

                        // Render CBT Pending
                        var htmlCbtPending = '';
                        if(res.cbt.pending.length > 0) {
                            $.each(res.cbt.pending, function(i, v) {
                                htmlCbtPending += '<tr>' +
                                    '<td class="font-weight-bold">' + v.nama + '</td>' +
                                    '<td>' + v.durasi + '</td>' +
                                    '<td class="text-danger">' + v.tgl_selesai + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlCbtPending = '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada ujian pending</td></tr>';
                        }
                        $('#tbl-siswa-cbt-pending').html(htmlCbtPending);

                        $('#siswa-result-container').removeClass('d-none');
                        $('#btn-print-siswa').removeClass('d-none');
                    } else {
                        swal.fire({
                            title: "Gagal",
                            text: res.message,
                            icon: "error"
                        });
                    }
                },
                error: function() {
                    $('#loading-siswa').addClass('d-none');
                    swal.fire({
                        title: "Error",
                        text: "Gagal memproses data siswa ke server.",
                        icon: "error"
                    });
                }
            });
        });

        // Print Siswa Summary handler
        $('#btn-print-siswa').on('click', function() {
            var printContents = document.getElementById('print-siswa-section').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = '<html><head><title>Laporan Perkembangan Siswa</title>' +
                '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">' +
                '<style>@media print { body { padding: 20px; } .card { border: 1px solid #ccc !important; } }</style>' +
                '</head><body>' + printContents + '</body></html>';

            window.print();
            document.body.innerHTML = originalContents;
            // Re-bind scripts after restoring body content
            window.location.reload();
        });

        // ------------------ TUTOR AJAX ------------------
        $('#btn-cari-tutor').on('click', function() {
            var id_guru = $('#select-tutor').val();
            var start = $('#tutor-start-date').val();
            var end = $('#tutor-end-date').val();

            if(id_guru === '') {
                swal.fire({
                    title: "Peringatan",
                    text: "Silakan pilih salah satu guru/tutor terlebih dahulu.",
                    icon: "warning"
                });
                return;
            }

            $('#loading-tutor').removeClass('d-none');
            $('#tutor-result-container').addClass('d-none');

            ajaxcsrf();
            $.ajax({
                url: base_url + 'monitoring/get_tutor_data',
                type: 'POST',
                data: {
                    id_guru: id_guru,
                    start_date: start,
                    end_date: end
                },
                dataType: 'json',
                success: function(res) {
                    $('#loading-tutor').addClass('d-none');
                    if(res.status) {
                        // Profil & Summary
                        $('#lbl-tutor-nama').text(res.guru.nama);
                        $('#lbl-tutor-nip').text(res.guru.nip ? res.guru.nip : '-');
                        $('#lbl-total-materi').text(res.kbm_summary.materi_total);
                        $('#lbl-total-tugas').text(res.kbm_summary.tugas_total);

                        // Honorarium
                        $('#honor-paid-offline').text(formatRupiah(res.honor.paid.offline));
                        $('#honor-pending-offline').text(formatRupiah(res.honor.pending.offline));
                        
                        $('#honor-paid-online').text(formatRupiah(res.honor.paid.online));
                        $('#honor-pending-online').text(formatRupiah(res.honor.pending.online));
                        
                        $('#honor-paid-task').text(formatRupiah(res.honor.paid.check_task));
                        $('#honor-pending-task').text(formatRupiah(res.honor.pending.check_task));
                        
                        $('#honor-paid-cbt').text(formatRupiah(res.honor.paid.create_cbt));
                        $('#honor-pending-cbt').text(formatRupiah(res.honor.pending.create_cbt));

                        $('#honor-paid-total').text(formatRupiah(res.honor.paid.total));
                        $('#honor-pending-total').text(formatRupiah(res.honor.pending.total));

                        // Status Penilaian Tugas
                        var htmlTugas = '';
                        if(res.tugas_details.length > 0) {
                            $.each(res.tugas_details, function(i, v) {
                                var pendingBadge = v.pending > 0 ? 'badge-danger' : 'badge-success';
                                htmlTugas += '<tr>' +
                                    '<td class="font-weight-bold">' + v.judul + '<br><small class="text-muted">' + v.mapel + ' (' + v.created_on + ')</small></td>' +
                                    '<td><span class="badge badge-info">' + v.kelas + '</span></td>' +
                                    '<td class="text-center font-weight-bold">' + v.total + '</td>' +
                                    '<td class="text-center text-success font-weight-bold">' + v.graded + '</td>' +
                                    '<td class="text-center"><span class="badge px-2 py-1 ' + pendingBadge + '">' + v.pending + '</span></td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlTugas = '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada tugas dibuat dalam periode ini</td></tr>';
                        }
                        $('#tbl-tutor-tugas-stats').html(htmlTugas);

                        // Chat/Diskusi
                        var htmlChat = '';
                        if(res.chat.length > 0) {
                            $.each(res.chat, function(i, v) {
                                htmlChat += '<tr>' +
                                    '<td style="white-space: nowrap;">' + v.tanggal + '</td>' +
                                    '<td><strong>' + v.topik + '</strong></td>' +
                                    '<td class="text-muted">' + v.komentar + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlChat = '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada riwayat chat diskusi</td></tr>';
                        }
                        $('#tbl-tutor-chat').html(htmlChat);

                        // CBT Pending
                        var htmlCbt = '';
                        if(res.cbt_pending.length > 0) {
                            $.each(res.cbt_pending, function(i, v) {
                                htmlCbt += '<tr>' +
                                    '<td><strong>' + v.siswa + '</strong></td>' +
                                    '<td>' + v.ujian + '</td>' +
                                    '<td class="text-danger">' + v.waktu + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlCbt = '<tr><td colspan="3" class="text-center text-muted py-3">Semua jawaban esai CBT telah dikoreksi</td></tr>';
                        }
                        $('#tbl-tutor-cbt-pending').html(htmlCbt);

                        // Render Evaluasi Pilihan Ganda (Choices)
                        var htmlEvalChoices = '';
                        if(res.evaluasi.choices.length > 0) {
                            $.each(res.evaluasi.choices, function(i, v) {
                                htmlEvalChoices += '<tr>' +
                                    '<td><strong>' + v.pertanyaan + '</strong></td>' +
                                    '<td><span class="badge badge-pill badge-primary font-weight-semibold text-xs px-2 py-1">' + v.jawaban + '</span></td>' +
                                    '<td><small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>' + v.tanggal_evaluasi + '</small></td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlEvalChoices = '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data kuesioner pilihan ganda untuk tutor ini</td></tr>';
                        }
                        $('#tbl-tutor-eval-choices').html(htmlEvalChoices);

                        // Render Evaluasi Kritik & Saran (Comments)
                        var htmlEvalComments = '';
                        if(res.evaluasi.comments.length > 0) {
                            $.each(res.evaluasi.comments, function(i, v) {
                                htmlEvalComments += '<div class="list-group-item bg-white p-3 mb-2 rounded shadow-sm border border-slate-100">' +
                                    '<p class="mb-1 text-sm font-semibold text-dark">"' + v.jawaban + '"</p>' +
                                    '<div class="d-flex justify-content-between text-[10px] text-muted mt-2">' +
                                    '  <span><i class="far fa-question-circle mr-1"></i>Topik: ' + v.pertanyaan + '</span>' +
                                    '  <span><i class="far fa-clock mr-1"></i>Dikirim: ' + v.tanggal_kirim + '</span>' +
                                    '</div>' +
                                    '</div>';
                            });
                        } else {
                            htmlEvalComments = '<div class="text-center text-muted py-3 small">Belum ada saran & kritik tertulis untuk tutor ini.</div>';
                        }
                        $('#list-tutor-eval-comments').html(htmlEvalComments);

                        // Render Jadwal Mengajar Tutor (Terkonsolidasi)
                        var htmlTutorJadwal = '';
                        if(res.jadwal.length > 0) {
                            $.each(res.jadwal, function(i, v) {
                                htmlTutorJadwal += '<tr>' +
                                    '<td class="font-weight-bold">' + v.hari + '</td>' +
                                    '<td>' + v.waktu + '</td>' +
                                    '<td><span class="badge badge-info">' + v.kelas + '</span></td>' +
                                    '<td>' + v.mapel + '</td>' +
                                    '<td><span class="text-secondary font-weight-semibold">' + v.pola_kegiatan + '</span></td>' +
                                    '</tr>';
                            });
                        } else {
                            htmlTutorJadwal = '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada jadwal mengajar fleksibel untuk tutor ini</td></tr>';
                        }
                        $('#tbl-tutor-jadwal').html(htmlTutorJadwal);

                        $('#tutor-result-container').removeClass('d-none');
                    } else {
                        swal.fire({
                            title: "Gagal",
                            text: res.message,
                            icon: "error"
                        });
                    }
                },
                error: function() {
                    $('#loading-tutor').addClass('d-none');
                    swal.fire({
                        title: "Error",
                        text: "Gagal memproses filter data tutor ke server.",
                        icon: "error"
                    });
                }
            });
        });

    });
</script>
