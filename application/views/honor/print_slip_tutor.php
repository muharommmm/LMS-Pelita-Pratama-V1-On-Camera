<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Honorarium Tutor - <?= htmlspecialchars($tutor->nama_guru) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px double #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 12px;
        }
        .title-doc {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 120px;
        }
        .info-table td.colon {
            width: 15px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
            text-transform: uppercase;
            font-size: 11px;
        }
        .data-table td.number {
            text-align: right;
        }
        .data-table td.center {
            text-align: center;
        }
        .footer-signatures {
            width: 100%;
            margin-top: 50px;
        }
        .footer-signatures td {
            text-align: center;
            width: 50%;
        }
        .signature-space {
            height: 75px;
        }
        .btn-print-container {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 12px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-print:hover {
            background-color: #0056b3;
        }
        @media print {
            .btn-print-container {
                display: none;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="btn-print-container">
        <button class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> Cetak Dokumen</button>
    </div>

    <div class="header">
        <h2><?= htmlspecialchars($setting->nama_aplikasi) ?></h2>
        <p>PKBM PELITA PRATAMA - PENDIDIKAN KESETARAAN PAKET A, B, DAN C</p>
        <p>Alamat: <?= htmlspecialchars($setting->alamat ?? 'Bandung') ?> | Telp: <?= htmlspecialchars($setting->telp ?? '-') ?></p>
    </div>

    <div class="title-doc">SLIP HONORARIUM TUTOR</div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Guru / Tutor</td>
            <td class="colon">:</td>
            <td><strong><?= htmlspecialchars($tutor->nama_guru) ?></strong></td>
            <td class="label">Periode Laporan</td>
            <td class="colon">:</td>
            <td>
                <?php if ($start_date && $end_date) : ?>
                    <?= date('d M Y', strtotime($start_date)) ?> s.d. <?= date('d M Y', strtotime($end_date)) ?>
                <?php else : ?>
                    Semua Periode
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="label">NIP</td>
            <td class="colon">:</td>
            <td><?= htmlspecialchars($tutor->nip ?? '-') ?></td>
            <td class="label">Tanggal Cetak</td>
            <td class="colon">:</td>
            <td><?= date('d-m-Y H:i') ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Mata Pelajaran</th>
                <th style="width: 20%;">Kelas / Rombel</th>
                <th style="width: 30%;">Rincian Kuantitas Kegiatan</th>
                <th style="width: 15%;">Total Honor</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            $total_honor_all = 0.00;
            if (!empty($slip_data)) : 
            ?>
                <?php foreach ($slip_data as $row) : ?>
                    <?php 
                    $total_honor_all += $row['total_amount']; 
                    $details = [];
                    if ($row['qty_offline'] > 0) $details[] = $row['qty_offline'] . 'x Offline';
                    if ($row['qty_online'] > 0) $details[] = $row['qty_online'] . 'x Online';
                    if ($row['qty_check_task'] > 0) $details[] = $row['qty_check_task'] . 'x Periksa Tugas';
                    if ($row['qty_create_cbt'] > 0) $details[] = $row['qty_create_cbt'] . 'x Buat CBT';
                    $details_str = implode(', ', $details);
                    ?>
                    <tr>
                        <td class="center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['mapel_name']) ?></td>
                        <td><?= htmlspecialchars($row['class_name']) ?></td>
                        <td><?= $details_str ?></td>
                        <td class="number">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="font-weight: bold; background-color: #f9f9f9;">
                    <td colspan="4" style="text-align: right; text-transform: uppercase;">Jumlah Diterima :</td>
                    <td class="number" style="color: #d9534f; font-size: 13px;">Rp <?= number_format($total_honor_all, 0, ',', '.') ?></td>
                </tr>
            <?php else : ?>
                <tr>
                    <td colspan="5" class="center" style="color: #777;">Tidak ada catatan kegiatan pengajaran/tugas untuk periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="footer-signatures">
        <tr>
            <td>
                <p>Penerima / Tutor,</p>
                <div class="signature-space"></div>
                <p><strong>( <?= htmlspecialchars($tutor->nama_guru) ?> )</strong></p>
            </td>
            <td>
                <p>Bandung, <?= date('d M Y') ?></p>
                <p>Kepala PKBM Pelita Pratama,</p>
                <div class="signature-space"></div>
                <p><strong>( ......................................... )</strong></p>
            </td>
        </tr>
    </table>

</body>
</html>
