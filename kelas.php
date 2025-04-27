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

// Proses tambah/edit kelas
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        if($_POST['action'] == 'tambah') {
            $stmt = $pdo->prepare("INSERT INTO kelas (nama_kelas, guru_id) VALUES (?, ?)");
            $stmt->execute([
                $_POST['nama_kelas'],
                $_POST['guru_id']
            ]);
            $_SESSION['success'] = "Data kelas berhasil ditambahkan!";
        } elseif($_POST['action'] == 'edit') {
            $stmt = $pdo->prepare("UPDATE kelas SET nama_kelas = ?, guru_id = ? WHERE id = ?");
            $stmt->execute([
                $_POST['nama_kelas'],
                $_POST['guru_id'],
                $_POST['id']
            ]);
            $_SESSION['success'] = "Data kelas berhasil diperbarui!";
        } elseif($_POST['action'] == 'hapus') {
            $stmt = $pdo->prepare("DELETE FROM kelas WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $_SESSION['success'] = "Data kelas berhasil dihapus!";
        }
        header("Location: kelas.php");
        exit;
    }
}

// Setelah semua logika, baru include header dan navbar
require_once 'partials/header.php';
require_once 'partials/navbar.php';

// Ambil data guru untuk dropdown
$stmt = $pdo->query("SELECT id, nama FROM guru WHERE role = 'guru' ORDER BY nama");
$guru_list = $stmt->fetchAll();

// Filter pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$guru_filter = isset($_GET['guru_filter']) ? $_GET['guru_filter'] : '';

// Query untuk mendapatkan data kelas dengan filter
$query = "
    SELECT k.*, g.nama as nama_guru,
    (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id = k.id) as total_siswa 
    FROM kelas k 
    LEFT JOIN guru g ON k.guru_id = g.id 
    WHERE 1=1
";

$params = [];

if (!empty($search)) {
    $query .= " AND k.nama_kelas LIKE ?";
    $params[] = "%$search%";
}

if (!empty($guru_filter)) {
    $query .= " AND k.guru_id = ?";
    $params[] = $guru_filter;
}

$query .= " ORDER BY k.nama_kelas";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$kelas_list = $stmt->fetchAll();

?>

<!-- Main Content -->
<div class="md:pl-64 pb-safe">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Kelas</h1>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>Tambah Kelas
            </button>
        </div>

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
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Kelas</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Masukkan nama kelas..."
                           class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Wali Kelas</label>
                    <select name="guru_filter" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Wali Kelas</option>
                        <?php foreach($guru_list as $guru): ?>
                            <option value="<?= $guru['id'] ?>" <?= $guru_filter == $guru['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($guru['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    <a href="kelas.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Tabel Kelas -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="table-responsive">
                <table class="min-w-full divide-y divide-gray-200 table-responsive-stack">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wali Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(empty($kelas_list)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data kelas yang ditemukan
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($kelas_list as $kelas): ?>
                                <tr>
                                    <td data-label="Nama Kelas" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($kelas['nama_kelas']) ?>
                                    </td>
                                    <td data-label="Wali Kelas" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($kelas['nama_guru'] ?? '-') ?>
                                    </td>
                                    <td data-label="Total Siswa" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                            <?= $kelas['total_siswa'] ?> Siswa
                                        </span>
                                    </td>
                                    <td data-label="Aksi" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="editKelas(<?= htmlspecialchars(json_encode($kelas)) ?>)" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button onclick="hapusKelas(<?= $kelas['id'] ?>, '<?= htmlspecialchars($kelas['nama_kelas']) ?>')" 
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

<!-- Modal Tambah Kelas -->
<div id="modalTambah" class="fixed z-50 inset-0 hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST">
                <input type="hidden" name="action" value="tambah">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Kelas Baru</h3>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_kelas">
                            Nama Kelas
                        </label>
                        <input type="text" name="nama_kelas" id="nama_kelas" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="guru_id">
                            Wali Kelas
                        </label>
                        <select name="guru_id" id="guru_id" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Pilih Wali Kelas</option>
                            <?php foreach($guru_list as $guru): ?>
                                <option value="<?= $guru['id'] ?>"><?= htmlspecialchars($guru['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
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

<!-- Modal Edit Kelas -->
<div id="modalEdit" class="fixed z-50 inset-0 hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Kelas</h3>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_nama_kelas">
                            Nama Kelas
                        </label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit_guru_id">
                            Wali Kelas
                        </label>
                        <select name="guru_id" id="edit_guru_id" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Pilih Wali Kelas</option>
                            <?php foreach($guru_list as $guru): ?>
                                <option value="<?= $guru['id'] ?>"><?= htmlspecialchars($guru['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
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

<script>
function editKelas(kelas) {
    document.getElementById('edit_id').value = kelas.id;
    document.getElementById('edit_nama_kelas').value = kelas.nama_kelas;
    document.getElementById('edit_guru_id').value = kelas.guru_id || '';
    document.getElementById('modalEdit').classList.remove('hidden');
}

function hapusKelas(id, nama) {
    if(confirm('Apakah Anda yakin ingin menghapus kelas "' + nama + '"?')) {
        document.getElementById('hapus_id').value = id;
        document.getElementById('formHapus').submit();
    }
}
</script>

<?php require_once 'partials/footer.php'; ?> 