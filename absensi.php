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

// Proses tambah/edit absensi
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        if($_POST['action'] == 'tambah') {
            $tanggal = $_POST['tanggal'];
            $kelas_id = $_POST['kelas_id'];
            $siswa_status = $_POST['status'];
            $keterangan = $_POST['keterangan'];
            
            // Hapus absensi yang sudah ada untuk tanggal dan kelas tersebut
            $stmt = $pdo->prepare("DELETE FROM absensi WHERE siswa_id IN (SELECT id FROM siswa WHERE kelas_id = ?) AND DATE(tanggal) = ?");
            $stmt->execute([$kelas_id, $tanggal]);
            
            // Insert absensi baru
            $stmt = $pdo->prepare("INSERT INTO absensi (siswa_id, tanggal, status, keterangan, created_by) VALUES (?, ?, ?, ?, ?)");
            
            foreach($siswa_status as $siswa_id => $status) {
                $stmt->execute([
                    $siswa_id,
                    $tanggal,
                    $status,
                    $keterangan[$siswa_id] ?? '',
                    $_SESSION['user_id']
                ]);
            }
            
            header("Location: absensi.php?success=1");
            exit;
        }
    }
}

// Ambil daftar kelas
$stmt = $pdo->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_list = $stmt->fetchAll();

// Ambil data siswa jika kelas dipilih
$siswa_list = [];
if(isset($_GET['kelas_id']) && !empty($_GET['kelas_id'])) {
    $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
    
    $query = "
        SELECT 
            s.*,
            COALESCE(a.status, '') as status,
            COALESCE(a.keterangan, '') as keterangan
        FROM siswa s
        LEFT JOIN absensi a ON s.id = a.siswa_id AND DATE(a.tanggal) = ?
        WHERE s.kelas_id = ?
        ORDER BY s.nama
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$tanggal, $_GET['kelas_id']]);
    $siswa_list = $stmt->fetchAll();
}
?>

<!-- Main Content -->
<div class="md:pl-64 pb-safe">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Input Absensi Siswa</h1>
            <p class="text-gray-600">Silakan pilih kelas dan tanggal untuk melakukan absensi</p>
        </div>

        <?php if(isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">Data absensi berhasil disimpan!</span>
        </div>
        <?php endif; ?>

        <!-- Form Pilih Kelas dan Tanggal -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="kelas_id">
                        Pilih Kelas
                    </label>
                    <select name="kelas_id" id="kelas_id" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                            onchange="this.form.submit()">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach($kelas_list as $kelas): ?>
                            <option value="<?= $kelas['id'] ?>" <?= (isset($_GET['kelas_id']) && $_GET['kelas_id'] == $kelas['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kelas['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="tanggal">
                        Pilih Tanggal
                    </label>
                    <input type="date" name="tanggal" id="tanggal" required
                           value="<?= isset($_GET['tanggal']) ? htmlspecialchars($_GET['tanggal']) : date('Y-m-d') ?>"
                           class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                           onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <?php if(!empty($siswa_list)): ?>
        <!-- Form Absensi -->
        <form method="POST" class="bg-white rounded-lg shadow-md overflow-hidden pb-20 md:pb-0">
            <input type="hidden" name="action" value="tambah">
            <input type="hidden" name="kelas_id" value="<?= htmlspecialchars($_GET['kelas_id']) ?>">
            <input type="hidden" name="tanggal" value="<?= isset($_GET['tanggal']) ? htmlspecialchars($_GET['tanggal']) : date('Y-m-d') ?>">
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Daftar Siswa</h2>
            </div>
            
            <div class="table-responsive">
                <table class="min-w-full divide-y divide-gray-200 table-responsive-stack">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach($siswa_list as $index => $siswa): ?>
                            <tr>
                                <td data-label="No" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $index + 1 ?>
                                </td>
                                <td data-label="Nama Siswa" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($siswa['nama']) ?>
                                </td>
                                <td data-label="Status" class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status[<?= $siswa['id'] ?>]" value="hadir" 
                                                   <?= $siswa['status'] == 'hadir' ? 'checked' : '' ?> required
                                                   class="form-radio text-green-600">
                                            <span class="ml-2 text-sm text-gray-700">Hadir</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status[<?= $siswa['id'] ?>]" value="sakit"
                                                   <?= $siswa['status'] == 'sakit' ? 'checked' : '' ?> required
                                                   class="form-radio text-yellow-600">
                                            <span class="ml-2 text-sm text-gray-700">Sakit</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status[<?= $siswa['id'] ?>]" value="izin"
                                                   <?= $siswa['status'] == 'izin' ? 'checked' : '' ?> required
                                                   class="form-radio text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">Izin</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status[<?= $siswa['id'] ?>]" value="alpha"
                                                   <?= $siswa['status'] == 'alpha' ? 'checked' : '' ?> required
                                                   class="form-radio text-red-600">
                                            <span class="ml-2 text-sm text-gray-700">Alpha</span>
                                        </label>
                                    </div>
                                </td>
                                <td data-label="Keterangan" class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" name="keterangan[<?= $siswa['id'] ?>]" 
                                           value="<?= htmlspecialchars($siswa['keterangan']) ?>"
                                           class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                           placeholder="Tambahkan keterangan (opsional)">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Desktop Submit Button -->
            <div class="hidden md:block px-6 py-4 bg-gray-50 text-right">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                    <i class="fas fa-save mr-2"></i>Simpan Absensi
                </button>
            </div>

            <!-- Mobile Fixed Submit Button -->
            <div class="md:hidden fixed bottom-16 inset-x-0 p-4 bg-white border-t border-gray-200 shadow-lg z-50">
                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 flex items-center justify-center text-lg font-medium">
                    <i class="fas fa-save mr-2"></i>Simpan Absensi
                </button>
            </div>
        </form>
        <?php elseif(isset($_GET['kelas_id'])): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">Tidak ada data siswa di kelas ini.</span>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
@media (max-width: 768px) {
    /* Tambah padding bottom pada form untuk ruang tombol fixed */
    .table-responsive {
        margin-bottom: 4rem;
    }

    /* Style untuk tombol fixed */
    .fixed.bottom-16 {
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1), 0 -2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Animasi hover untuk tombol */
    .fixed.bottom-16 button:active {
        transform: scale(0.98);
    }
}
</style>

<?php require_once 'partials/footer.php'; ?> 