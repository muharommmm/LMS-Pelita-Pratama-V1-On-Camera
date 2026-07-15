<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Honor Yayasan - PKBM Pelita Pratama</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 20px;
        }
        .header-container {
            width: 100%;
            margin-bottom: 20px;
            position: relative;
        }
        .header {
            text-align: center;
            border-bottom: 2px double #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 11px;
        }
        .total-box {
            position: absolute;
            top: 0;
            right: 0;
            border: 2px solid #d9534f;
            padding: 8px 12px;
            border-radius: 4px;
            background-color: #fdf7f7;
            text-align: right;
        }
        .total-box span {
            font-size: 10px;
            color: #777;
            text-transform: uppercase;
            display: block;
        }
        .total-box strong {
            font-size: 14px;
            color: #d9534f;
        }
        .title-doc {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .period-info {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin-bottom: 15px;
        }
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .matrix-table th, .matrix-table td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }
        .matrix-table th {
            background-color: #f2f2f2;
            text-align: center;
            text-transform: uppercase;
            font-size: 10px;
        }
        .matrix-table td.number {
            text-align: right;
            font-family: monospace;
        }
        .matrix-table td.center {
            text-align: center;
        }
        .matrix-table td.no-activity {
            background-color: #f8d7da; /* Soft Red Pastel */
            color: #721c24;
            text-align: center;
        }
        .matrix-table td.has-activity {
            background-color: #e2f0d9; /* Soft Green Pastel */
            color: #385723;
        }
        .btn-print-container {
            margin-bottom: 20px;
            text-align: right;
        }
        .btn-print {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 12px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-print:hover {
            background-color: #218838;
        }
        .footer-signatures {
            width: 100%;
            margin-top: 40px;
        }
        .footer-signatures td {
            text-align: center;
            width: 50%;
        }
        .signature-space {
            height: 60px;
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
        <button class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> Cetak Rekap Yayasan</button>
    </div>

    <div class="header-container">
        <div class="header">
            <h2><?= htmlspecialchars($setting->nama_aplikasi) ?></h2>
            <p>PKBM PELITA PRATAMA - PENDIDIKAN KESETARAAN PAKET A, B, DAN C</p>
            <p>Alamat: <?= htmlspecialchars($setting->alamat ?? 'Bandung') ?> | Telp: <?= htmlspecialchars($setting->telp ?? '-') ?></p>
        </div>
        <div class="total-box">
            <span>Total Pengeluaran Honor</span>
            <strong>Rp <?= number_format($total_overall, 0, ',', '.') ?></strong>
        </div>
    </div>

    <div class="title-doc">REKAPITULASI HONORARIUM TUTOR (YAYASAN)</div>
    <div class="period-info">
        Periode Laporan: 
        <?php if ($start_date && $end_date) : ?>
            <strong><?= date('d M Y', strtotime($start_date)) ?> s.d. <?= date('d M Y', strtotime($end_date)) ?></strong>
        <?php else : ?>
            <strong>Semua Periode</strong>
        <?php endif; ?>
    </div>

    <table class="matrix-table">
        <thead>
            <tr>
                <th style="width: 3%;" rowspan="2">No</th>
                <th style="width: 10%;" rowspan="2">NIP</th>
                <th style="width: 18%;" rowspan="2">Nama Tutor / Guru</th>
                <th colspan="9">Tingkatan Kelas / Rombel</th>
                <th style="width: 12%;" rowspan="2">Total Diterima</th>
            </tr>
            <tr>
                <th style="width: 7%;">Kelas 4</th>
                <th style="width: 7%;">Kelas 5</th>
                <th style="width: 7%;">Kelas 6</th>
                <th style="width: 7%;">Kelas 7</th>
                <th style="width: 7%;">Kelas 8</th>
                <th style="width: 7%;">Kelas 9</th>
                <th style="width: 7%;">Kelas 10</th>
                <th style="width: 7%;">Kelas 11</th>
                <th style="width: 7%;">Kelas 12</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            $column_totals = array_fill(4, 9, 0.00);
            if (!empty($tutors_matrix)) : 
            ?>
                <?php foreach ($tutors_matrix as $tutor_id => $row) : ?>
                    <tr>
                        <td class="center"><?= $no++ ?></td>
                        <td class="center"><?= htmlspecialchars($row['nip'] ?? '-') ?></td>
                        <td class="font-weight-bold"><?= htmlspecialchars($row['nama_guru']) ?></td>
                        
                        <?php for ($lvl = 4; $lvl <= 12; $lvl++) : ?>
                            <?php 
                            $val = $row['levels'][$lvl];
                            $column_totals[$lvl] += $val;
                            if ($val > 0) : 
                            ?>
                                <td class="number has-activity">Rp <?= number_format($val, 0, ',', '.') ?></td>
                            <?php else : ?>
                                <td class="no-activity">0</td>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <td class="number" style="font-weight: bold; background-color: #f2f2f2;">Rp <?= number_format($row['total_tutor'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Totals Row -->
                <tr style="font-weight: bold; background-color: #e9ecef;">
                    <td colspan="3" style="text-align: right; text-transform: uppercase;">Total Per Kelas :</td>
                    <?php for ($lvl = 4; $lvl <= 12; $lvl++) : ?>
                        <td class="number">Rp <?= number_format($column_totals[$lvl], 0, ',', '.') ?></td>
                    <?php endfor; ?>
                    <td class="number" style="color: #d9534f; font-size: 12px; background-color: #dee2e6;">Rp <?= number_format($total_overall, 0, ',', '.') ?></td>
                </tr>
            <?php else : ?>
                <tr>
                    <td colspan="13" class="center" style="color: #777; padding: 20px;">Tidak ada data aktivitas mengajar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="footer-signatures">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p>Bendahara PKBM,</p>
                <div class="signature-space"></div>
                <p><strong>( ......................................... )</strong></p>
            </td>
            <td>
                <p>Bandung, <?= date('d M Y') ?></p>
                <p>Ketua PKBM Pelita Pratama,</p>
                <div class="signature-space"></div>
                <p><strong>( ......................................... )</strong></p>
            </td>
        </tr>
    </table>

</body>
</html>
