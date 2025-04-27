<?php
session_start();
require_once 'config/koneksi.php';
require_once 'partials/header.php';

// Set filter default
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

// Ambil daftar kelas untuk filter
$stmt = $pdo->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_list = $stmt->fetchAll();

// Query untuk mendapatkan data absensi
$query = "
    SELECT 
        s.nis,
        s.nama as nama_siswa,
        s.jenis_kelamin,
        k.nama_kelas,
        a.status,
        a.keterangan,
        g.nama as guru_pengajar
    FROM siswa s
    JOIN kelas k ON s.kelas_id = k.id
    LEFT JOIN absensi a ON s.id = a.siswa_id AND DATE(a.tanggal) = ?
    LEFT JOIN guru g ON a.created_by = g.id
    WHERE 1=1
";

$params = [$tanggal];

if (!empty($kelas_id)) {
    $query .= " AND s.kelas_id = ?";
    $params[] = $kelas_id;
}

$query .= " ORDER BY k.nama_kelas, s.nama";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$siswa_list = $stmt->fetchAll();

// Hitung statistik
$total_hadir = 0;
$total_sakit = 0;
$total_izin = 0;
$total_alpha = 0;

foreach ($siswa_list as $siswa) {
    switch ($siswa['status']) {
        case 'hadir': $total_hadir++; break;
        case 'sakit': $total_sakit++; break;
        case 'izin': $total_izin++; break;
        case 'alpha': $total_alpha++; break;
    }
}
?>

<div class="min-h-screen bg-gradient-to-br from-orange-50 to-white py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-600 to-orange-500 rounded-lg shadow-sm mb-6 p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Monitoring Absensi Siswa</h1>
                        <p class="text-orange-100">SDN 203 BONTOMACINNA</p>
                    </div>
                    <a href="index.php" class="inline-flex items-center px-4 py-2 bg-white text-orange-600 rounded-lg hover:bg-orange-50 transition duration-200">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
                        <select name="kelas_id" class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            <?php foreach($kelas_list as $kelas): ?>
                                <option value="<?= $kelas['id'] ?>" <?= $kelas_id == $kelas['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kelas['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tanggal</label>
                        <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" 
                               class="w-full rounded-lg border-gray-300 focus:ring-orange-500 focus:border-orange-500"
                               onchange="this.form.submit()">
                    </div>
                </form>
            </div>

            <!-- Statistik -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100">
                            <i class="fas fa-check text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm">Hadir</h3>
                            <p class="text-xl md:text-2xl font-semibold text-orange-600"><?= $total_hadir ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100">
                            <i class="fas fa-hospital text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm">Sakit</h3>
                            <p class="text-xl md:text-2xl font-semibold text-orange-600"><?= $total_sakit ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100">
                            <i class="fas fa-envelope text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm">Izin</h3>
                            <p class="text-xl md:text-2xl font-semibold text-orange-600"><?= $total_izin ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100">
                            <i class="fas fa-times text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm">Alpha</h3>
                            <p class="text-xl md:text-2xl font-semibold text-orange-600"><?= $total_alpha ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Absensi -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-600 to-orange-500">
                    <h2 class="text-lg font-semibold text-white">Data Absensi Siswa</h2>
                    <p class="text-sm text-orange-100">Tanggal: <?= date('d F Y', strtotime($tanggal)) ?></p>
                </div>
                
                <!-- Panduan Scroll Mobile -->
                <div class="md:hidden bg-blue-50 p-4 flex items-center justify-center text-blue-600 border-b">
                    <i class="fas fa-hand-point-right animate-bounce mr-2"></i>
                    <span class="text-sm">Geser ke kanan untuk melihat data lengkap</span>
                    <i class="fas fa-hand-point-left animate-bounce ml-2"></i>
                </div>
                
                <?php if(empty($siswa_list)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-info-circle text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500">Tidak ada data absensi untuk tanggal ini</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <!-- Indikator Scroll -->
                        <div class="md:hidden absolute right-0 top-1/2 transform -translate-y-1/2 bg-orange-500 text-white p-2 rounded-l-lg shadow-lg opacity-50">
                            <i class="fas fa-arrows-left-right"></i>
                        </div>
                        <div class="inline-block min-w-full">
                            <div class="overflow-hidden">
                                <table class="min-w-full">
                                    <thead class="bg-orange-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-orange-600 uppercase tracking-wider">No</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-orange-600 uppercase tracking-wider">Kelas</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-orange-600 uppercase tracking-wider">NIS</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-orange-600 uppercase tracking-wider">Nama Siswa</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-orange-600 uppercase tracking-wider">JK</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-orange-600 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-orange-600 uppercase tracking-wider">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $no = 1; foreach($siswa_list as $siswa): ?>
                                            <tr class="hover:bg-orange-50 transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <div class="flex items-center">
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900"><?= $no++ ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?= htmlspecialchars($siswa['nama_kelas']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?= htmlspecialchars($siswa['nis']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?= htmlspecialchars($siswa['nama_siswa']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?= $siswa['jenis_kelamin'] ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-3 py-1 text-xs rounded-full font-medium
                                                        <?php
                                                        switch($siswa['status']) {
                                                            case 'hadir':
                                                                echo 'bg-green-100 text-green-800';
                                                                break;
                                                            case 'sakit':
                                                                echo 'bg-yellow-100 text-yellow-800';
                                                                break;
                                                            case 'izin':
                                                                echo 'bg-blue-100 text-blue-800';
                                                                break;
                                                            case 'alpha':
                                                                echo 'bg-red-100 text-red-800';
                                                                break;
                                                            default:
                                                                echo 'bg-gray-100 text-gray-800';
                                                        }
                                                        ?>">
                                                        <?= $siswa['status'] ? ucfirst($siswa['status']) : 'Belum Absen' ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?= $siswa['keterangan'] ? htmlspecialchars($siswa['keterangan']) : '-' ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Responsif table styles */
@media (max-width: 768px) {
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
        position: relative; /* Untuk positioning indikator scroll */
    }
    
    table {
        display: block;
        width: 100%;
        overflow-x: auto;
    }
    
    th, td {
        min-width: 120px;
    }
    
    /* Membuat kolom nomor dan status lebih kecil */
    th:first-child, td:first-child,
    th:nth-child(5), td:nth-child(5) {
        min-width: 60px;
        position: sticky;
        left: 0;
        background: white;
        z-index: 1;
    }
    
    /* Membuat kolom status sedikit lebih lebar */
    th:nth-child(6), td:nth-child(6) {
        min-width: 100px;
    }
    
    /* Membuat kolom keterangan lebih lebar */
    th:last-child, td:last-child {
        min-width: 150px;
    }

    /* Animasi untuk panduan scroll */
    @keyframes slideRight {
        0% { transform: translateX(0); }
        50% { transform: translateX(10px); }
        100% { transform: translateX(0); }
    }

    .animate-bounce {
        animation: bounce 1s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(-25%); }
        50% { transform: translateY(0); }
    }
}

/* Animasi hover untuk baris tabel */
tr {
    transition: all 0.2s ease-in-out;
}

tr:hover {
    transform: translateX(5px);
}

/* Style untuk status badges */
.status-badge {
    transition: all 0.3s ease;
}

.status-badge:hover {
    transform: scale(1.1);
}
</style>

<?php require_once 'partials/footer.php'; ?> 