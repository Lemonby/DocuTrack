<?php
// File: src/views/pdf/lpj_template.php
// Template HTML untuk PDF LPJ

$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$kode_mak = $kode_mak ?? '';
$realisasi_data = $realisasi_data ?? [];
$grand_total_realisasi = $grand_total_realisasi ?? 0;
$tanggal_mulai = $kegiatan_data['tanggal_mulai'] ?? date('Y-m-d');
$tanggal_selesai = $kegiatan_data['tanggal_selesai'] ?? '';

// Ambil tahun dari tanggal mulai untuk Tahun Anggaran
$tahun_anggaran = (int) date('Y');

if (! function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        return 'Rp '.number_format($angka ?? 0, 0, ',', '.');
    }
}

if (! function_exists('formatTanggal')) {
    function formatTanggal($date)
    {
        if (empty($date) || $date === '0000-00-00') {
            return '-';
        }

        return date('d F Y', strtotime($date));
    }
}

$grand_total_rab = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LAPORAN PERTANGGUNGJAWABAN - <?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'N/A'); ?></title>
    <style>
        @page {
            margin: 2.5cm 2.5cm 2.5cm 2.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }

        /* STYLE KHUSUS HALAMAN COVER */
        .cover-page-container {
            text-align: center;
            padding-top: 1cm;
            height: 100%;
        }

        .cover-logo {
            width: 130px;
            height: auto;
            margin-bottom: 25px;
        }

        .cover-main-title {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .cover-sub-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 45px;
        }

        .cover-section {
            margin-bottom: 30px;
        }

        .cover-label {
            font-size: 12pt;
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        .cover-value {
            font-size: 14pt;
            font-weight: bold;
        }

        .cover-footer {
            margin-top: 80px;
            font-size: 12pt;
        }
        
        .cover-footer .footer-year {
            font-weight: bold;
            font-size: 14pt;
            margin-top: 20px;
        }

        /* Section Headers */
        .section-header {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .subsection-header {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border: none;
        }

        .info-table td {
            padding: 4px 5px;
            border: none;
            vertical-align: top;
        }

        .info-table .label {
            width: 35%;
            font-weight: normal;
        }

        .info-table .separator {
            width: 3%;
        }

        .info-table .value {
            width: 62%;
        }

        /* Content Paragraph */
        .content-text {
            text-align: justify;
            margin-bottom: 15px;
            line-height: 1.8;
        }

        /* IKU List */
        .iku-list {
            margin: 10px 0 10px 20px;
            line-height: 1.8;
        }

        .iku-list li {
            margin-bottom: 5px;
        }

        /* Table - Indikator */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
        }

        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 8px 5px;
            border: 1px solid #000;
            text-align: center;
        }

        .data-table td {
            padding: 6px 5px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .data-table .text-center {
            text-align: center;
        }

        .data-table .text-right {
            text-align: right;
        }

        /* RAB & Realisasi Table */
        .rab-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
            font-size: 9pt;
        }

        .rab-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 6px 4px;
            border: 1px solid #000;
            text-align: center;
            font-size: 8pt;
        }

        .rab-table td {
            padding: 5px 4px;
            border: 1px solid #000;
            vertical-align: middle;
        }

        .rab-table .subtotal-row {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        .rab-table .grand-total-row {
            background-color: #d0d0d0;
            font-weight: bold;
            font-size: 11pt;
        }

        /* Checkbox */
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Bukti / Lampiran Styles */
        .bukti-item {
            margin-bottom: 30px;
            page-break-inside: avoid;
            text-align: center;
            border: 1px solid #000;
            padding: 15px;
            background: #ffffff;
        }

        .bukti-title {
            font-weight: bold;
            margin-bottom: 12px;
            text-align: left;
            font-size: 11pt;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .bukti-image {
            max-width: 100%;
            max-height: 400px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="cover-page-container">
    <?php
    // Path logo yang benar
    $logoPath = __DIR__.'/../../../public/assets/images/logo/logoPnj.jpeg';

// Gunakan base64 encoding jika file ada, atau skip jika tidak ada
if (file_exists($logoPath)) {
    $imageData = base64_encode(file_get_contents($logoPath));
    $src = 'data:image/jpeg;base64,'.$imageData;
} else {
    // Fallback: gunakan URL atau skip
    $src = '/docutrack/public/assets/images/logo/logoPnj.jpeg';
}
?>
    <img src="<?= $src; ?>" class="cover-logo" alt="Logo PNJ" style="width: 130px;">

    <div class="cover-main-title">
        LAPORAN PERTANGGUNGJAWABAN (LPJ)
    </div>
    
    <div class="cover-sub-title">
        TAHUN ANGGARAN <?= $tahun_anggaran; ?>
    </div>

    <div class="cover-section">
        <span class="cover-label">Kegiatan :</span>
        <div class="cover-value">
            <?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'Belum ada nama kegiatan'); ?>
        </div>
    </div>

    <div class="cover-section">
        <span class="cover-label">Unit Kerja:</span>
        <div class="cover-value">
            Dosen Jurusan <?= htmlspecialchars($kegiatan_data['jurusan'] ?? '-'); ?>
        </div>
    </div>

    <div class="cover-footer">
        <p>Politeknik Negeri Jakarta</p>
        <div class="footer-year">
            Tahun <?= $tahun_anggaran; ?>
        </div>
    </div>
</div>
<div class="page-break"></div>

<div class="section-header">I. Kerangka Acuan Kerja (KAK)</div>

<table class="info-table">
    <tr>
        <td class="label">Nama Pengusul</td>
        <td class="separator">:</td>
        <td class="value"><?= htmlspecialchars($kegiatan_data['nama_pengusul'] ?? '-'); ?></td>
    </tr>
    <tr>
        <td class="label">NIM/NIP Pengusul</td>
        <td class="separator">:</td>
        <td class="value"><?= htmlspecialchars($kegiatan_data['nim_pengusul'] ?? '-'); ?></td>
    </tr>
    <tr>
        <td class="label">Jurusan</td>
        <td class="separator">:</td>
        <td class="value"><?= htmlspecialchars($kegiatan_data['jurusan'] ?? '-'); ?></td>
    </tr>
    <tr>
        <td class="label">Prodi</td>
        <td class="separator">:</td>
        <td class="value"><?= htmlspecialchars($kegiatan_data['prodi'] ?? '-'); ?></td>
    </tr>
    <tr>
        <td class="label">Nama Kegiatan</td>
        <td class="separator">:</td>
        <td class="value"><?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '-'); ?></td>
    </tr>
</table>

<div class="subsection-header">Gambaran Umum :</div>
<div class="content-text">
    <?= nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'] ?? '-')); ?>
</div>

<div class="subsection-header">Penerima Manfaat :</div>
<div class="content-text">
    <?= nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? '-')); ?>
</div>

<div class="subsection-header">Strategi Pencapaian Keluaran</div>

<div class="subsection-header">Metode Pelaksanaan :</div>
<div class="content-text">
    <?= nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'] ?? '-')); ?>
</div>

<div class="subsection-header">Tahapan Pelaksanaan :</div>
<div class="content-text">
    <?= nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'] ?? '-')); ?>
</div>

<div class="subsection-header">Indikator Kinerja</div>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 10%;">NO</th>
            <th style="width: 20%;">Bulan</th>
            <th style="width: 50%;">Indikator Keberhasilan</th>
            <th style="width: 20%;">Target</th>
        </tr>
    </thead>
    <tbody>
        <?php if (! empty($indikator_data)) { ?>
            <?php $no = 1;
            foreach ($indikator_data as $item) { ?>
            <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td class="text-center"><?= htmlspecialchars($item['bulan'] ?? '-'); ?></td>
                <td><?= htmlspecialchars($item['nama'] ?? $item['indikatorKeberhasilan'] ?? '-'); ?></td>
                <td class="text-center"><?= htmlspecialchars($item['target'] ?? $item['targetPersen'] ?? '0'); ?>%</td>
            </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4" class="text-center" style="font-style: italic;">Tidak ada data indikator</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<div class="subsection-header">Indikator Kinerja Utama :</div>
<ul class="iku-list">
    <?php if (! empty($iku_data)) { ?>
        <?php foreach ($iku_data as $iku_item) { ?>
            <li><?= htmlspecialchars($iku_item); ?></li>
        <?php } ?>
    <?php } else { ?>
        <li style="font-style: italic;">Tidak ada IKU yang dipilih</li>
    <?php } ?>
</ul>

<div class="page-break"></div>

<div class="section-header">II. Rincian Anggaran Biaya (RAB)</div>

<?php if (! empty($rab_data)) { ?>
    <?php foreach ($rab_data as $kategori => $items) { ?>
        <?php if (empty($items)) {
            continue;
        } ?>
        <?php $subtotal = 0; ?>
        
        <div class="subsection-header"><?= htmlspecialchars($kategori); ?></div>
        
        <table class="rab-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 20%;">Uraian</th>
                    <th rowspan="2" style="width: 20%;">Rincian</th>
                    <th colspan="4" style="width: 20%;">Satuan</th>
                    <th rowspan="2" style="width: 20%;">Total</th>
                </tr>
                <tr>
                    <th style="width: 8%;">Vol 1</th>
                    <th style="width: 7%;">Sat 1</th>
                    <th style="width: 8%;">Vol 2</th>
                    <th style="width: 7%;">Sat 2</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) { ?>
                    <?php
                    $vol1 = $item['vol1'] ?? 0;
                    $vol2 = $item['vol2'] ?? 1;
                    $sat1 = $item['sat1'] ?? '';
                    $sat2 = $item['sat2'] ?? '';
                    $harga = $item['harga'] ?? $item['harga_satuan'] ?? 0;
                    $total_item = $vol1 * $vol2 * $harga;
                    $subtotal += $total_item;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['uraian'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($item['rincian'] ?? ''); ?></td>
                        <td class="text-center"><?= htmlspecialchars($vol1); ?></td>
                        <td class="text-center"><?= htmlspecialchars($sat1); ?></td>
                        <td class="text-center"><?= htmlspecialchars($vol2); ?></td>
                        <td class="text-center"><?= htmlspecialchars($sat2); ?></td>
                        <td class="text-right"><?= formatRupiah($total_item); ?></td>
                    </tr>
                <?php } ?>
                <tr class="subtotal-row">
                    <td colspan="6" class="text-right">Sub Total</td>
                    <td class="text-right"><?= formatRupiah($subtotal); ?></td>
                </tr>
            </tbody>
        </table>
        
        <?php $grand_total_rab += $subtotal; ?>
    <?php } ?>
    
    <table class="rab-table">
        <tr class="grand-total-row">
            <td class="text-right" style="padding: 10px;">Grand Total : <?= formatRupiah($grand_total_rab); ?></td>
        </tr>
    </table>
<?php } else { ?>
    <div class="content-text" style="font-style: italic;">Tidak ada data RAB</div>
<?php } ?>

<div class="section-header">III. Jumlah Dana yang Dicairkan</div>

<table class="info-table">
    <tr>
        <td class="label">Nominal Uang</td>
        <td class="separator">:</td>
        <td class="value" style="font-weight: bold; font-size: 12pt;">
            <?= formatRupiah($grand_total_rab); ?>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="padding-top: 15px;">
            <span class="checkbox" style="<?= ($kegiatan_data['wadir_tujuan'] ?? '') ? 'background-color: #000;' : ''; ?>"></span> Uang Muka
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="checkbox" style="<?= ! ($kegiatan_data['wadir_tujuan'] ?? '') ? 'background-color: #000;' : ''; ?>"></span> Dana Penuh
        </td>
    </tr>
</table>

<div class="page-break"></div>

<div class="section-header">IV. Laporan Realisasi Anggaran (LPJ)</div>

<?php if (! empty($realisasi_data)) { ?>
    <?php $total_realisasi_kegiatan = 0; ?>
    <?php foreach ($realisasi_data as $kategori => $items) { ?>
        <?php if (empty($items)) {
            continue;
        } ?>
        <?php $subtotal_realisasi = 0; ?>
        
        <div class="subsection-header"><?= htmlspecialchars($kategori); ?></div>
        
        <table class="rab-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 20%;">Uraian</th>
                    <th rowspan="2" style="width: 20%;">Rincian</th>
                    <th colspan="4" style="width: 20%;">Satuan</th>
                    <th rowspan="2" style="width: 20%;">Total Realisasi</th>
                </tr>
                <tr>
                    <th style="width: 8%;">Vol 1</th>
                    <th style="width: 7%;">Sat 1</th>
                    <th style="width: 8%;">Vol 2</th>
                    <th style="width: 7%;">Sat 2</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) { ?>
                    <?php
                    $vol1 = $item['vol1'] ?? 0;
                    $vol2 = $item['vol2'] ?? 1;
                    $sat1 = $item['sat1'] ?? '';
                    $sat2 = $item['sat2'] ?? '';
                    $realisasi = $item['realisasi'] ?? 0;
                    $subtotal_realisasi += $realisasi;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['uraian'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($item['rincian'] ?? ''); ?></td>
                        <td class="text-center"><?= htmlspecialchars($vol1); ?></td>
                        <td class="text-center"><?= htmlspecialchars($sat1); ?></td>
                        <td class="text-center"><?= htmlspecialchars($vol2); ?></td>
                        <td class="text-center"><?= htmlspecialchars($sat2); ?></td>
                        <td class="text-right"><?= formatRupiah($realisasi); ?></td>
                    </tr>
                <?php } ?>
                <tr class="subtotal-row">
                    <td colspan="6" class="text-right">Sub Total Realisasi</td>
                    <td class="text-right"><?= formatRupiah($subtotal_realisasi); ?></td>
                </tr>
            </tbody>
        </table>
        
        <?php $total_realisasi_kegiatan += $subtotal_realisasi; ?>
    <?php } ?>
    
    <table class="rab-table">
        <tr class="grand-total-row">
            <td class="text-right" style="padding: 10px;">Grand Total Realisasi : <?= formatRupiah($total_realisasi_kegiatan); ?></td>
        </tr>
    </table>
<?php } else { ?>
    <div class="content-text" style="font-style: italic;">Tidak ada data Realisasi</div>
<?php } ?>

<?php
// Cek apakah ada bukti pembayaran yang diunggah
$hasImages = false;
if (! empty($realisasi_data)) {
    foreach ($realisasi_data as $kategori => $items) {
        foreach ($items as $item) {
            if (! empty($item['file_bukti'])) {
                $hasImages = true;
                break 2;
            }
        }
    }
}
?>

<?php if ($hasImages) { ?>
    <div class="page-break"></div>
    <div class="section-header">V. Lampiran Bukti Pertanggungjawaban (Dokumentasi)</div>
    
    <div style="margin-top: 15px;">
        <?php foreach ($realisasi_data as $kategori => $items) { ?>
            <?php foreach ($items as $item) { ?>
                <?php
                if (empty($item['file_bukti'])) {
                    continue;
                }
                $buktiPath = storage_path('app/public/'.$item['file_bukti']);
                $src = null;
                if (file_exists($buktiPath)) {
                    try {
                        $imageData = base64_encode(file_get_contents($buktiPath));
                        $mime = mime_content_type($buktiPath) ?: 'image/jpeg';
                        $src = 'data:'.$mime.';base64,'.$imageData;
                    } catch (Exception $e) {
                        $src = null;
                    }
                }
                ?>
                <div class="bukti-item">
                    <div class="bukti-title"><?= htmlspecialchars($kategori); ?> - Uraian: <?= htmlspecialchars($item['uraian'] ?? '-'); ?></div>
                    <?php if ($src) { ?>
                        <img src="<?= $src; ?>" class="bukti-image" alt="Bukti Pembayaran">
                    <?php } else { ?>
                        <div style="padding: 20px; border: 1px dashed #ccc; color: #999; font-style: italic; font-size: 10pt;">
                            Berkas bukti pembayaran tidak ditemukan atau tidak dapat dimuat (<?= htmlspecialchars($item['file_bukti']); ?>)
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>

</body>
</html>
