<?php
session_start();
require_once 'config/koneksi.php';
require_once 'partials/header.php';
require_once 'partials/navbar.php';

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil daftar kelas untuk filter
$stmt = $pdo->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_list = $stmt->fetchAll();

// Set filter default
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
?>

<!-- Main Content -->
<div class="md:pl-64 pb-safe">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-4">Laporan Absensi</h1>
                
                <!-- Filter Form -->
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select name="kelas_id" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Kelas</option>
                            <?php foreach($kelas_list as $kelas): ?>
                                <option value="<?= $kelas['id'] ?>" <?= $kelas_id == $kelas['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kelas['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" value="<?= htmlspecialchars($tanggal_awal) ?>" 
                               class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" value="<?= htmlspecialchars($tanggal_akhir) ?>" 
                               class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-3 flex flex-col md:flex-row justify-between items-center gap-4">
                        <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-search mr-2"></i>Tampilkan
                        </button>
                        <?php if(!empty($data_absensi)): ?>
                        <a href="export_absensi.php?kelas_id=<?= htmlspecialchars($kelas_id) ?>&tanggal_awal=<?= htmlspecialchars($tanggal_awal) ?>&tanggal_akhir=<?= htmlspecialchars($tanggal_akhir) ?>" 
                           class="w-full md:w-auto bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200 text-center">
                            <i class="fas fa-file-excel mr-2"></i>Export Excel
                        </a>
                        <?php endif; ?>
                    </div>
                </form>

                <?php if(empty($data_absensi)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-info-circle text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500">Tidak ada data absensi untuk filter yang dipilih</p>
                    </div>
                <?php else: ?>
                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200 table-responsive-stack">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">JK</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $no = 1;
                                foreach($data_absensi as $data): 
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td data-label="No" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                                    <td data-label="Tanggal" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('d/m/Y', strtotime($data['tanggal'])) ?>
                                    </td>
                                    <td data-label="Kelas" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($data['nama_kelas']) ?>
                                    </td>
                                    <td data-label="NIS" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($data['nis']) ?>
                                    </td>
                                    <td data-label="Nama" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($data['nama_siswa']) ?>
                                    </td>
                                    <td data-label="JK" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $data['jenis_kelamin'] ?>
                                    </td>
                                    <td data-label="Status" class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            <?php
                                            switch($data['status']) {
                                                case 'hadir':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'sakit':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'izin':
                                                    echo 'bg-blue-100 text-blue-800';
                                                    break;
                                                default:
                                                    echo 'bg-red-100 text-red-800';
                                            }
                                            ?>">
                                            <?= ucfirst($data['status']) ?>
                                        </span>
                                    </td>
                                    <td data-label="Keterangan" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $data['keterangan'] ? htmlspecialchars($data['keterangan']) : '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?> 