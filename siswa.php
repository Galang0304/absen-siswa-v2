<?php
session_start();

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah user adalah superadmin
if($_SESSION['role'] != 'superadmin') {
    header("Location: dashboard.php");
    exit;
}

require_once 'config/koneksi.php';

// Proses tambah/edit siswa
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        if($_POST['action'] == 'tambah') {
            try {
                // Cek apakah NIS atau NISN sudah ada
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE nis = ? OR nisn = ?");
                $stmt->execute([$_POST['nis'], $_POST['nisn']]);
                $count = $stmt->fetchColumn();
                
                if($count > 0) {
                    $_SESSION['error'] = "NIS atau NISN sudah terdaftar. Silakan periksa kembali.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO siswa (nis, nisn, nama, jenis_kelamin, kelas_id) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['nis'],
                        $_POST['nisn'],
                        $_POST['nama'],
                        $_POST['jenis_kelamin'],
                        $_POST['kelas_id']
                    ]);
                    $_SESSION['success'] = "Data siswa berhasil ditambahkan!";
                }
            } catch(PDOException $e) {
                $_SESSION['error'] = "Gagal menambahkan data siswa. Silakan coba lagi.";
            }
        } elseif($_POST['action'] == 'edit') {
            try {
                // Cek apakah NIS atau NISN sudah ada (kecuali untuk siswa yang sedang diedit)
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE (nis = ? OR nisn = ?) AND id != ?");
                $stmt->execute([$_POST['nis'], $_POST['nisn'], $_POST['id']]);
                $count = $stmt->fetchColumn();
                
                if($count > 0) {
                    $_SESSION['error'] = "NIS atau NISN sudah terdaftar. Silakan periksa kembali.";
                } else {
                    $stmt = $pdo->prepare("UPDATE siswa SET nis = ?, nisn = ?, nama = ?, jenis_kelamin = ?, kelas_id = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['nis'],
                        $_POST['nisn'],
                        $_POST['nama'],
                        $_POST['jenis_kelamin'],
                        $_POST['kelas_id'],
                        $_POST['id']
                    ]);
                    $_SESSION['success'] = "Data siswa berhasil diperbarui!";
                }
            } catch(PDOException $e) {
                $_SESSION['error'] = "Gagal memperbarui data siswa. Silakan coba lagi.";
            }
        } elseif($_POST['action'] == 'hapus') {
            try {
                $stmt = $pdo->prepare("DELETE FROM siswa WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $_SESSION['success'] = "Data siswa berhasil dihapus!";
            } catch(PDOException $e) {
                $_SESSION['error'] = "Gagal menghapus data siswa. Silakan coba lagi.";
            }
        }
        header("Location: siswa.php");
        exit;
    }
}

// Setelah semua logika, baru include header dan navbar
require_once 'partials/header.php';
require_once 'partials/navbar.php';

// Ambil data kelas untuk dropdown
$stmt = $pdo->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
$kelas_list = $stmt->fetchAll();

// Filter pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$kelas_filter = isset($_GET['kelas_filter']) ? $_GET['kelas_filter'] : '';
$jk_filter = isset($_GET['jk_filter']) ? $_GET['jk_filter'] : '';

// Query untuk mendapatkan data siswa dengan filter
$query = "
    SELECT s.*, k.nama_kelas 
    FROM siswa s 
    JOIN kelas k ON s.kelas_id = k.id 
    WHERE 1=1
";

$params = [];

if (!empty($search)) {
    $query .= " AND (s.nama LIKE ? OR s.nis LIKE ? OR s.nisn LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($kelas_filter)) {
    $query .= " AND s.kelas_id = ?";
    $params[] = $kelas_filter;
}

if (!empty($jk_filter)) {
    $query .= " AND s.jenis_kelamin = ?";
    $params[] = $jk_filter;
}

$query .= " ORDER BY k.nama_kelas, s.nama";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$siswa_list = $stmt->fetchAll();
?>

<!-- Main Content -->
<div class="md:pl-64 pb-safe">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Siswa</h1>
            <div class="flex space-x-2">
                <button onclick="document.getElementById('modalImport').classList.remove('hidden')" 
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-file-import mr-2"></i>Import Excel
                </button>
                <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i>Tambah Siswa
                </button>
            </div>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
            <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php 
            unset($_SESSION['error']);
        endif; 
        ?>
        
        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?= $_SESSION['success'] ?></span>
            <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php 
            unset($_SESSION['success']);
        endif; 
        ?>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Siswa</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Cari nama, NIS, atau NISN..."
                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kelas</label>
                    <select name="kelas_filter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kelas</option>
                        <?php foreach($kelas_list as $kelas): ?>
                            <option value="<?= $kelas['id'] ?>" <?= $kelas_filter == $kelas['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kelas['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                    <select name="jk_filter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua</option>
                        <option value="L" <?= $jk_filter === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $jk_filter === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="md:col-span-3 flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    <a href="siswa.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Tabel Siswa -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="table-responsive">
                <table class="min-w-full divide-y divide-gray-200 table-responsive-stack">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(empty($siswa_list)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data siswa yang ditemukan
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($siswa_list as $siswa): ?>
                                <tr>
                                    <td data-label="NIS" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($siswa['nis']) ?>
                                    </td>
                                    <td data-label="NISN" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($siswa['nisn']) ?>
                                    </td>
                                    <td data-label="Nama" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($siswa['nama']) ?>
                                    </td>
                                    <td data-label="JK" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $siswa['jenis_kelamin'] ?>
                                    </td>
                                    <td data-label="Kelas" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($siswa['nama_kelas']) ?>
                                    </td>
                                    <td data-label="Aksi" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="editSiswa(<?= htmlspecialchars(json_encode($siswa)) ?>)" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button onclick="hapusSiswa(<?= $siswa['id'] ?>, '<?= htmlspecialchars($siswa['nama']) ?>')" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div id="modalTambah" class="fixed z-50 inset-0 hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Siswa Baru</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="nis">NIS</label>
                            <input type="text" name="nis" id="nis" required
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="nisn">NISN</label>
                            <input type="text" name="nisn" id="nisn" required
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nama">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="jenis_kelamin" value="L" required class="form-radio">
                                    <span class="ml-2">Laki-laki</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="jenis_kelamin" value="P" required class="form-radio">
                                    <span class="ml-2">Perempuan</span>
                                </label>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="kelas_id">Kelas</label>
                            <select name="kelas_id" id="kelas_id" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Pilih Kelas</option>
                                <?php foreach($kelas_list as $kelas): ?>
                                    <option value="<?= $kelas['id'] ?>"><?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Siswa -->
<div id="modalEdit" class="fixed z-50 inset-0 hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Siswa</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_nis">NIS</label>
                            <input type="text" name="nis" id="edit_nis" required
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_nisn">NISN</label>
                            <input type="text" name="nisn" id="edit_nisn" required
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_nama">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_nama" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kelamin</label>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="jenis_kelamin" value="L" id="edit_jk_l" required class="form-radio">
                                    <span class="ml-2">Laki-laki</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="jenis_kelamin" value="P" id="edit_jk_p" required class="form-radio">
                                    <span class="ml-2">Perempuan</span>
                                </label>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_kelas_id">Kelas</label>
                            <select name="kelas_id" id="edit_kelas_id" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Pilih Kelas</option>
                                <?php foreach($kelas_list as $kelas): ?>
                                    <option value="<?= $kelas['id'] ?>"><?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update
                    </button>
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hapus (Hidden) -->
<form id="formHapus" method="POST" class="hidden">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" id="hapus_id">
</form>

<!-- Modal Import -->
<div id="modalImport" class="fixed z-50 inset-0 hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST" action="import_siswa.php" enctype="multipart/form-data">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Import Data Siswa</h3>
                    
                    <!-- Panduan Import -->
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-medium text-blue-800 mb-2">Panduan Import:</h4>
                        <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                            <li>Download template Excel yang sudah disediakan</li>
                            <li>Isi data sesuai format yang ada di template</li>
                            <li>Pastikan kolom NIS dan NISN bersifat unik</li>
                            <li>Jenis kelamin diisi dengan "L" atau "P"</li>
                            <li>Upload file yang sudah diisi</li>
                        </ol>
                    </div>
                    
                    <!-- Download Template -->
                    <div class="mb-4">
                        <a href="template_import_siswa.php" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Download Template Excel
                        </a>
                    </div>
                    
                    <!-- File Input -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Pilih File Excel (.xlsx)
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-upload mr-2"></i>Import Data
                    </button>
                    <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSiswa(siswa) {
    document.getElementById('edit_id').value = siswa.id;
    document.getElementById('edit_nis').value = siswa.nis;
    document.getElementById('edit_nisn').value = siswa.nisn;
    document.getElementById('edit_nama').value = siswa.nama;
    document.getElementById('edit_kelas_id').value = siswa.kelas_id;
    
    if(siswa.jenis_kelamin == 'L') {
        document.getElementById('edit_jk_l').checked = true;
    } else {
        document.getElementById('edit_jk_p').checked = true;
    }
    
    document.getElementById('modalEdit').classList.remove('hidden');
}

function hapusSiswa(id, nama) {
    if(confirm('Apakah Anda yakin ingin menghapus data siswa "' + nama + '"?')) {
        document.getElementById('hapus_id').value = id;
        document.getElementById('formHapus').submit();
    }
}
</script>

<?php require_once 'partials/footer.php'; ?> 