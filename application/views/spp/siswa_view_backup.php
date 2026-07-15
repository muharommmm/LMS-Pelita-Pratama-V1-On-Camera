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
                    <h5 class="card-title">Riwayat Pembayaran SPP (Siswa: <?= htmlspecialchars($siswa->nama) ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 50px;">No</th>
                                    <th>Bulan</th>
                                    <th>Nominal Tagihan</th>
                                    <th>Status Pembayaran</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Nomor Invoice</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($billings)) : ?>
                                    <?php $no = 1; foreach ($billings as $bill) : ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center"><b><?= htmlspecialchars($month_names[$bill->month]) ?></b></td>
                                            <td class="text-right">Rp <?= number_format($bill->amount, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <?php if ($bill->status === 'paid') : ?>
                                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> LUNAS</span>
                                                <?php else : ?>
                                                    <span class="badge badge-warning"><i class="fas fa-exclamation-circle mr-1"></i> BELUM BAYAR</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $bill->payment_date ? date('d-m-Y H:i', strtotime($bill->payment_date)) : '-' ?>
                                            </td>
                                            <td class="text-center text-monospace">
                                                <?= $bill->invoice_number ? htmlspecialchars($bill->invoice_number) : '-' ?>
                                            </td>
                                            <td><?= htmlspecialchars($bill->notes) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada riwayat tagihan/pembayaran SPP untuk tahun ajaran aktif ini.</td>
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
