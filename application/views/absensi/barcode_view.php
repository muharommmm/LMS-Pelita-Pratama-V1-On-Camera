<div class="content-wrapper bg-white pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between mb-2">
                <h1><?= $judul ?></h1>
                <a href="<?= base_url('absensi') ?>" class="btn btn-sm btn-danger">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card card-teal card-outline my-shadow mb-4">
                        <div class="card-header text-center">
                            <h3 class="card-title font-weight-bold float-none"><?= htmlspecialchars($barcode->location_name) ?></h3>
                        </div>
                        <div class="card-body text-center">
                            <p class="text-muted mb-4">Siswa kelas <strong><?= htmlspecialchars($class->nama_kelas) ?></strong> silakan memindai QR Code di bawah menggunakan ponsel mereka untuk presensi mandiri.</p>
                            
                            <?php
                            $scan_url = base_url('absensi/scan/' . $barcode->barcode_code);
                            $qr_api = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($scan_url);
                            ?>
                            <div class="p-4 border rounded bg-light d-inline-block shadow-sm">
                                <img src="<?= $qr_api ?>" alt="QR Code Absensi" style="width: 250px; height: 250px;">
                            </div>
                            
                            <div class="mt-4">
                                <span class="text-xs text-muted">Link Absensi Manual (Bila Scan Bermasalah):</span><br>
                                <a href="<?= $scan_url ?>" target="_blank" class="text-teal font-weight-bold"><?= $scan_url ?></a>
                            </div>
                        </div>
                        <div class="card-footer text-center bg-light">
                            <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> QR Code ini bersifat unik untuk kelas <?= htmlspecialchars($class->nama_kelas) ?>.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
