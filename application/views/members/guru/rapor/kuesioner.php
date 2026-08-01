<div class="content-wrapper bg-white">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 animate__animated animate__fadeIn">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold text-dark"><i class="fas fa-clipboard-list text-teal mr-2"></i><?= $judul ?></h1>
                    <p class="text-muted text-sm"><?= $subjudul ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Info Box Anonim -->
            <div class="alert alert-info border-left-info shadow-sm p-4 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <i class="fas fa-user-secret fa-2x text-info"></i>
                    </div>
                    <div>
                        <h5 class="alert-heading font-weight-bold mb-1">Informasi Privasi & Keamanan Siswa</h5>
                        <p class="mb-0 text-sm">
                            Semua evaluasi dan masukan yang dikirimkan oleh siswa bersifat **Anonim (Confidential)** untuk menjaga kenyamanan belajar mengajar. Nama siswa, NIS, atau kelas pelapor tidak ditampilkan di sistem demi menjaga objektivitas masukan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detail Rekap Evaluasi -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-teal card-outline card-outline-tabs my-shadow">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="evaluasiTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active font-weight-bold py-3 px-4" id="choices-tab" data-toggle="pill" href="#choices-pane" role="tab" aria-controls="choices-pane" aria-selected="true">
                                        <i class="fas fa-check-double mr-2"></i>Penilaian Pilihan Ganda
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold py-3 px-4" id="comments-tab" data-toggle="pill" href="#comments-pane" role="tab" aria-controls="comments-pane" aria-selected="false">
                                        <i class="fas fa-comment-dots mr-2"></i>Saran & Kritik Masukan (Teks)
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="evaluasiTabContent">
                                
                                <!-- Tab 1: Pilihan Ganda -->
                                <div class="tab-pane fade show active" id="choices-pane" role="tabpanel" aria-labelledby="choices-tab">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="info-box bg-light border my-shadow-sm">
                                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text text-muted text-uppercase text-xs font-weight-bold">Total Pengisi Rapor</span>
                                                    <span class="info-box-number text-dark h4 mb-0"><?= $evaluasi['total_responses'] ?> Siswa</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover text-sm" id="tbl-choices">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width: 5%">No</th>
                                                    <th>Pertanyaan Evaluasi</th>
                                                    <th style="width: 25%">Jawaban/Penilaian Siswa</th>
                                                    <th style="width: 20%">Tanggal Pembelajaran</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($evaluasi['choices'])): ?>
                                                    <?php $no = 1; foreach($evaluasi['choices'] as $choice): ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><strong><?= htmlspecialchars($choice->pertanyaan) ?></strong></td>
                                                            <td>
                                                                <span class="badge badge-pill badge-primary font-weight-semibold text-xs px-2.5 py-1.5">
                                                                    <?= htmlspecialchars($choice->jawaban) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <i class="far fa-calendar-alt text-muted mr-1"></i>
                                                                <?= !empty($choice->tanggal_evaluasi) ? date('d-m-Y', strtotime($choice->tanggal_evaluasi)) : '-' ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data penilaian pilihan ganda dari siswa.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tab 2: Saran & Kritik Teks -->
                                <div class="tab-pane fade" id="comments-pane" role="tabpanel" aria-labelledby="comments-tab">
                                    <div class="list-group list-group-flush">
                                        <?php if(!empty($evaluasi['comments'])): ?>
                                            <?php foreach($evaluasi['comments'] as $comment): ?>
                                                <div class="list-group-item bg-white p-4 mb-3 border rounded shadow-sm">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="bg-gray-100 rounded-circle w-8 h-8 d-flex align-items-center justify-content-center text-muted mr-2">
                                                            <i class="fas fa-quote-left text-xs"></i>
                                                        </div>
                                                        <span class="text-xs text-muted font-weight-bold">TOPIK: <?= htmlspecialchars($comment->pertanyaan) ?></span>
                                                    </div>
                                                    <p class="mb-2 text-dark font-weight-semibold" style="font-size: 0.95rem; line-height: 1.6;">
                                                        "<?= htmlspecialchars($comment->jawaban) ?>"
                                                    </p>
                                                    <div class="d-flex justify-content-between text-[11px] text-muted border-top pt-2">
                                                        <span><i class="far fa-calendar-alt mr-1"></i>Tanggal KBM: <?= !empty($comment->tanggal_evaluasi) ? date('d-m-Y', strtotime($comment->tanggal_evaluasi)) : '-' ?></span>
                                                        <span><i class="far fa-clock mr-1"></i>Dikirim: <?= date('d-m-Y H:i', strtotime($comment->tanggal)) ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-5">
                                                <i class="far fa-comment-dots fa-3x mb-3 text-gray-300"></i>
                                                <p class="mb-0 small">Belum ada kritik, saran, atau aduan tertulis dari siswa untuk Anda.</p>
                                            </div>
                                        <?php endif; ?>
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
    $(document).ready(function() {
        if ($('#tbl-choices tbody tr').length > 1) {
            $('#tbl-choices').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "url": "<?= base_url() ?>/assets/plugins/datatables/i18n/Indonesian.json"
                }
            });
        }
    });
</script>
