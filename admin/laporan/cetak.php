<?php
require('../../config/koneksi.php');
require('../../vendor/autoload.php'); // Plugin to PDF pakek composer require dompdf/dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';

$query = "SELECT p.*, u.nama AS nama_user, b.judul AS judul_buku 
        FROM peminjaman p 
        JOIN user u ON p.id_user = u.id_user 
        JOIN buku b ON p.id_buku = b.buku_id";

if ($tgl_awal && $tgl_akhir) {
    $query .= " WHERE tanggal_pinjam BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$query .= " ORDER BY p.id_peminjaman DESC";
$data = mysqli_query($koneksi, $query);

// === TEMPLATE LAPORAN SEMI FORMAL ===
$html = '
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
h2, h3 { text-align: center; margin: 0; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #000; padding: 6px; text-align: left; }
th { background: #f2f2f2; }
.header { text-align: center; margin-bottom: 20px; }
.ttd { margin-top: 50px; text-align: right; }
</style>

<div class="header">
    <h2>LAPORAN PEMINJAMAN BUKU</h2>
    <h3>Perpustakaan Digital Sekolah</h3>
    <p>Periode: ' . ($tgl_awal ?: '-') . ' s/d ' . ($tgl_akhir ?: '-') . '</p>
</div>

<table>
<thead>
<tr>
    <th>No</th>
    <th>Nama Anggota</th>
    <th>Judul Buku</th>
    <th>Tgl Pinjam</th>
    <th>Tgl Kembali</th>
    <th>Status</th>
    <th>Denda (Rp)</th>
</tr>
</thead>
<tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($data)) {
    $html .= '<tr>
        <td>' . $no++ . '</td>
        <td>' . htmlspecialchars($row['nama_user']) . '</td>
        <td>' . htmlspecialchars($row['judul_buku']) . '</td>
        <td>' . $row['tanggal_pinjam'] . '</td>
        <td>' . ($row['tanggal_kembali'] ?: '-') . '</td>
        <td>' . ucfirst($row['status_peminjaman']) . '</td>
        <td>' . number_format($row['denda'], 2, ',', '.') . '</td>
    </tr>';
}

$html .= '</tbody></table>
<div class="ttd">
    <p>Diketahui,</p>
    <br><br><br>
    <p><b>Admin Perpustakaan</b></p>
</div>';

// === Generate PDF ===
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Laporan_Peminjaman_' . date('Ymd') . '.pdf', ['Attachment' => false]);
exit;
