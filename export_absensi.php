<?php
session_start();
require_once 'config/koneksi.php';

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Set filter dari parameter URL
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-d');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

// Query untuk mendapatkan data absensi berdasarkan filter
$where_conditions = [];
$params = [];

if(!empty($kelas_id)) {
    $where_conditions[] = "s.kelas_id = ?";
    $params[] = $kelas_id;
}

$where_conditions[] = "DATE(a.tanggal) BETWEEN ? AND ?";
$params[] = $tanggal_awal;
$params[] = $tanggal_akhir;

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "
    SELECT 
        k.nama_kelas,
        s.nis,
        s.nama as nama_siswa,
        s.jenis_kelamin,
        DATE(a.tanggal) as tanggal,
        a.status,
        a.keterangan,
        g.nama as guru_pengajar
    FROM siswa s
    JOIN kelas k ON s.kelas_id = k.id
    LEFT JOIN absensi a ON s.id = a.siswa_id
    LEFT JOIN guru g ON a.created_by = g.id
    $where_clause
    ORDER BY k.nama_kelas, s.nama, a.tanggal
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data_absensi = $stmt->fetchAll();

// Set header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Laporan_Absensi_' . date('d-m-Y', strtotime($tanggal_awal)) . '_sd_' . date('d-m-Y', strtotime($tanggal_akhir)) . '.xls"');
header('Cache-Control: max-age=0');

// Output Excel content
?>
<table border="1">
    <thead>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 14pt; font-weight: bold;">
                Laporan Absensi Siswa
            </th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">
                Periode: <?= date('d F Y', strtotime($tanggal_awal)) ?> s/d <?= date('d F Y', strtotime($tanggal_akhir)) ?>
            </th>
        </tr>
        <tr>
            <th style="background-color: #f0f0f0; font-weight: bold;">No</th>
            <th style="background-color: #f0f0f0; font-weight: bold;">Tanggal</th>
            <th style="background-color: #f0f0f0; font-weight: bold;">Kelas</th>
            <th style="background-color: #f0f0f0; font-weight: bold;">NIS</th>
            <th style="background-color: #f0f0f0; font-weight: bold;">Nama Siswa</th>
            <th style="background-color: #f0f0f0; font-weight: bold;">JK</th>
            <th style="background-color: #f0f0f0; font-weight: bold;">Status</th>
            <th style="background-color: #f0f0f0; font-weight: bold;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        $current_kelas = '';
        foreach($data_absensi as $row): 
            // Add empty row between classes
            if($current_kelas != '' && $current_kelas != $row['nama_kelas']):
        ?>
            <tr>
                <td colspan="8">&nbsp;</td>
            </tr>
        <?php 
            endif;
            $current_kelas = $row['nama_kelas'];
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                <td><?= $row['nama_kelas'] ?></td>
                <td><?= $row['nis'] ?></td>
                <td><?= $row['nama_siswa'] ?></td>
                <td><?= $row['jenis_kelamin'] ?></td>
                <td style="<?= getStatusStyle($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
                <td><?= $row['keterangan'] ?? '-' ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: right;">
                Dicetak pada: <?= date('d/m/Y H:i:s') ?>
            </td>
        </tr>
    </tfoot>
</table>

<?php
function getStatusStyle($status) {
    switch($status) {
        case 'hadir':
            return 'background-color: #dcfce7; color: #166534;'; // Green
        case 'sakit':
            return 'background-color: #fef9c3; color: #854d0e;'; // Yellow
        case 'izin':
            return 'background-color: #dbeafe; color: #1e40af;'; // Blue
        case 'alpha':
            return 'background-color: #fee2e2; color: #991b1b;'; // Red
        default:
            return '';
    }
} 