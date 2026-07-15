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

            <!-- Info Boxes -->
            <div class="row">
                <div class="col-md-6 col-sm-12 col-12">
                    <div class="info-box bg-gradient-danger my-shadow">
                        <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Menunggu Pencairan</span>
                            <span class="info-box-number text-lg">Rp <?= number_format($total_menunggu_pencairan, 0, ',', '.') ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                Menunggu pencairan dana oleh Admin
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 col-12">
                    <div class="info-box bg-gradient-success my-shadow">
                        <span class="info-box-icon"><i class="fas fa-check-double"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Honor Sudah Dibayar</span>
                            <span class="info-box-number text-lg">Rp <?= number_format($total_sudah_dibayar, 0, ',', '.') ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                Dana berhasil masuk / cair
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pendapatan Tahun Ajaran Aktif -->
            <div class="card card-default my-shadow mb-4">
                <div class="card-header bg-light">
                    <h5 class="card-title font-weight-bold"><i class="fas fa-chart-line mr-1 text-primary"></i> Ringkasan Pendapatan Tahun Ajaran Aktif (<?= htmlspecialchars($tp_active->tahun) ?>)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped m-0">
                            <thead>
                                <tr class="text-center">
                                    <th>Kategori Aktivitas</th>
                                    <th>Telah Dibayar (Lunas)</th>
                                    <th>Belum Dibayar</th>
                                    <th>Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_paid = 0;
                                $total_unpaid = 0;
                                $total_yearly = 0;
                                $types = [
                                    'offline' => 'Tatap Muka Offline',
                                    'online' => 'Tatap Muka Online',
                                    'check_task' => 'Pemeriksaan Tugas',
                                    'create_cbt' => 'Pembuatan Bank Soal CBT'
                                ];
                                foreach ($yearly_summary as $summary) : 
                                    $total_paid += $summary->paid_amount;
                                    $total_unpaid += $summary->unpaid_amount;
                                    $total_yearly += $summary->total_amount;
                                ?>
                                    <tr>
                                        <td class="font-weight-bold pl-3"><?= htmlspecialchars($types[$summary->type] ?? $summary->type) ?></td>
                                        <td class="text-right text-success pr-3">Rp <?= number_format($summary->paid_amount, 0, ',', '.') ?></td>
                                        <td class="text-right text-danger pr-3">Rp <?= number_format($summary->unpaid_amount, 0, ',', '.') ?></td>
                                        <td class="text-right font-weight-bold pr-3">Rp <?= number_format($summary->total_amount, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="bg-light font-weight-bold" style="border-top: 2px solid #ddd;">
                                    <td class="pl-3">TOTAL KESELURUHAN</td>
                                    <td class="text-right text-success pr-3">Rp <?= number_format($total_paid, 0, ',', '.') ?></td>
                                    <td class="text-right text-danger pr-3">Rp <?= number_format($total_unpaid, 0, ',', '.') ?></td>
                                    <td class="text-right text-primary pr-3">Rp <?= number_format($total_yearly, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12 text-right">
                    <a href="<?= base_url('absensi') ?>" class="btn btn-primary shadow-sm">
                        <i class="fas fa-edit mr-1"></i> Input Absensi Sekarang
                    </a>
                </div>
            </div>

            <!-- Detailed Views Tabs -->
            <div class="card card-default card-tabs my-shadow mb-4">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="honorTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="unpaid-tab" data-toggle="pill" href="#unpaid-pane" role="tab" aria-controls="unpaid-pane" aria-selected="true">
                                <i class="fas fa-clock mr-1"></i> Rincian Belum Dibayar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="payout-tab" data-toggle="pill" href="#payout-pane" role="tab" aria-controls="payout-pane" aria-selected="false">
                                <i class="fas fa-history mr-1"></i> Riwayat Pencairan Dana
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="honorTabsContent">
                        <!-- Pane Unpaid -->
                        <div class="tab-pane fade show active" id="unpaid-pane" role="tabpanel" aria-labelledby="unpaid-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="unpaidTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 50px;">No</th>
                                            <th>Aktivitas</th>
                                            <th>Mapel & Kelas</th>
                                            <th>Sesi / Kuantitas</th>
                                            <th>Tarif</th>
                                            <th>Nominal Awal</th>
                                            <th>Nominal Koreksi</th>
                                            <th>Catatan Admin</th>
                                            <th>Status</th>
                                            <th>Tanggal Aktivitas</th>
                                            <th style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $all_unpaid = array_merge($pending_records, $approved_records);
                                        if (!empty($all_unpaid)) : 
                                        ?>
                                            <?php $no = 1; foreach ($all_unpaid as $rec) : ?>
                                                <tr>
                                                    <td class="text-center"><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($type_names[$rec->type]) ?></td>
                                                    <td>
                                                         <?= !empty($rec->nama_mapel) ? htmlspecialchars($rec->nama_mapel) : '-' ?>
                                                         <br>
                                                         <small class="text-muted"><?= !empty($rec->nama_kelas) ? htmlspecialchars($rec->nama_kelas) : '-' ?></small>
                                                    </td>
                                                    <td class="text-center"><?= $rec->qty ?> unit / sesi</td>
                                                    <td class="text-right">Rp <?= number_format($rec->rate, 0, ',', '.') ?></td>
                                                    <td class="text-right text-muted">Rp <?= number_format($rec->amount, 0, ',', '.') ?></td>
                                                    <td class="text-right font-weight-bold text-danger">
                                                        <?php if ($rec->adjusted_amount !== null && floatval($rec->adjusted_amount) > 0) : ?>
                                                            Rp <?= number_format($rec->adjusted_amount, 0, ',', '.') ?>
                                                        <?php else : ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($rec->admin_notes)) : ?>
                                                            <span class="text-danger font-weight-bold"><i class="fas fa-info-circle mr-1"></i><?= htmlspecialchars($rec->admin_notes) ?></span>
                                                        <?php else : ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($rec->status === 'approved') : ?>
                                                            <span class="badge badge-primary">Approved</span>
                                                        <?php elseif ($rec->status === 'rejected') : ?>
                                                            <span class="badge badge-danger">Rejected</span>
                                                        <?php else : ?>
                                                            <span class="badge badge-warning">Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center"><?= date('d-m-Y H:i', strtotime($rec->created_at)) ?></td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('honor/delete_record/' . $rec->id_honor_record) ?>" class="btn btn-xs btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus catatan honorarium ini?');">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="10" class="text-center text-muted">Seluruh honorarium Anda telah lunas dibayarkan!</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pane Payout History -->
                        <div class="tab-pane fade" id="payout-pane" role="tabpanel" aria-labelledby="payout-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="payoutTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 50px;">No</th>
                                            <th>Periode Mengajar</th>
                                            <th>Jumlah Pencairan</th>
                                            <th>Tanggal Transfer</th>
                                            <th>Keterangan / Bukti</th>
                                            <th>Status Konfirmasi Anda</th>
                                            <th style="width: 150px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($mutations)) : ?>
                                            <?php $no = 1; foreach ($mutations as $mut) : ?>
                                                 <?php
                                                 $periode = '-';
                                                 $bukti = '-';
                                                 if (preg_match('/^Honorarium Periode:\s*([^\(]+)(?:\((.*)\))?$/i', $mut->notes, $matches)) {
                                                     $periode = trim($matches[1]);
                                                     $bukti = isset($matches[2]) ? trim($matches[2]) : '-';
                                                 } else {
                                                     $periode = $mut->notes;
                                                 }
                                                 ?>
                                                 <tr>
                                                     <td class="text-center"><?= $no++ ?></td>
                                                     <td><strong><?= strtoupper($periode) ?></strong></td>
                                                     <td class="text-right text-success font-weight-bold">Rp <?= number_format($mut->amount, 0, ',', '.') ?></td>
                                                     <td class="text-center"><?= date('d-m-Y H:i', strtotime($mut->transaction_date)) ?></td>
                                                     <td><?= htmlspecialchars($bukti) ?></td>
                                                    <td class="text-center">
                                                        <?php if ($mut->status_konfirmasi_tutor == 1) : ?>
                                                            <span class="badge badge-success"><i class="fas fa-check-double mr-1"></i> Diterima & Dikonfirmasi</span>
                                                        <?php else : ?>
                                                            <span class="badge badge-warning"><i class="fas fa-exclamation-triangle mr-1"></i> Menunggu Konfirmasi Anda</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($mut->status_konfirmasi_tutor == 0) : ?>
                                                            <a href="<?= base_url('honor/confirm_payout/' . $mut->id_mutation) ?>" class="btn btn-xs btn-success btn-block" onclick="return confirm('Apakah Anda yakin sudah menerima dana transfer ini?');">
                                                                <i class="fas fa-check mr-1"></i> Konfirmasi Terima
                                                            </a>
                                                        <?php else : ?>
                                                            <button class="btn btn-xs btn-outline-secondary btn-block" disabled><i class="fas fa-check-double mr-1"></i> Selesai</button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Belum ada riwayat mutasi transfer/pembayaran dari Admin.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
        if ($.fn.DataTable) {
            $('#unpaidTable').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "searching": true,
                "language": {
                    "url": "<?= base_url('assets/plugins/datatables/i18n/Indonesian.json') ?>"
                }
            });
            $('#payoutTable').DataTable({
                "paging": true,
                "ordering": true,
                "info": true,
                "searching": true,
                "language": {
                    "url": "<?= base_url('assets/plugins/datatables/i18n/Indonesian.json') ?>"
                }
            });
        }
    });
</script>
