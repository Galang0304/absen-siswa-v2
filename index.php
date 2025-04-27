<?php
session_start();
require_once 'config/koneksi.php';

// Jika sudah login, redirect ke dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

require_once 'partials/header.php';

// Ambil data statistik
$stmt = $pdo->query("SELECT COUNT(*) FROM siswa");
$total_siswa = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM kelas");
$total_kelas = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM guru WHERE role = 'guru'");
$total_guru = $stmt->fetchColumn();
?>

<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-orange-500 to-orange-600 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-grid-white/[0.1] bg-[length:16px_16px]"></div>
    
    <!-- Hero Content -->
    <div class="relative container mx-auto px-4 py-12 md:py-24">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <!-- Text Content -->
            <div class="text-white space-y-6">
                <div class="flex items-center space-x-4 mb-6">
                    <img src="assets/images/logo-sekolah.png" alt="Logo Sekolah" class="w-20 h-20">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold">SDN 203 BONTOMACINNA</h1>
                        <p class="text-orange-100">Kec. Gantarang, Kab. Bulukumba</p>
                    </div>
                </div>
                <p class="text-xl text-orange-50">Selamat datang di Sistem Informasi Absensi Siswa</p>
                <p class="text-orange-100">Sistem informasi ini dirancang untuk memudahkan pengelolaan absensi siswa secara efektif dan efisien.</p>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-8">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <div class="text-3xl font-bold"><?= $total_siswa ?></div>
                        <div class="text-orange-100">Total Siswa</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <div class="text-3xl font-bold"><?= $total_kelas ?></div>
                        <div class="text-orange-100">Total Kelas</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <div class="text-3xl font-bold"><?= $total_guru ?></div>
                        <div class="text-orange-100">Total Guru</div>
                    </div>
                </div>

                <!-- Login Buttons -->
                <div class="mt-8 space-y-4">
                    <a href="login.php" class="inline-flex items-center px-6 py-3 bg-white text-orange-600 rounded-lg hover:bg-orange-50 transition duration-300">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk ke Sistem
                    </a>
                    <a href="monitoring.php" class="inline-flex items-center px-6 py-3 bg-orange-700 text-white rounded-lg hover:bg-orange-800 transition duration-300">
                        <i class="fas fa-chart-line mr-2"></i>
                        Monitoring Orang Tua
                    </a>
                </div>
            </div>
            
            <!-- Image Gallery -->
            <div class="relative hidden md:block">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <img src="assets/images/foto-gedung-sekolah.jpg" alt="Gedung Sekolah" class="w-full h-48 object-cover rounded-lg shadow-lg transform hover:scale-105 transition duration-300">
                        <img src="assets/images/logo-sekolah.png" alt="Logo" class="w-full h-48 object-contain bg-white/10 backdrop-blur-sm rounded-lg shadow-lg p-4 transform hover:scale-105 transition duration-300">
                    </div>
                    <div class="mt-8">
                        <img src="assets/images/foto-guru.jpg" alt="Foto Guru" class="w-full h-[400px] object-cover rounded-lg shadow-lg transform hover:scale-105 transition duration-300">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tentang Sekolah Section -->
<div class="bg-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Tentang Sekolah</h2>
            <p class="text-gray-600">SDN 203 Bontomacinna adalah sekolah dasar negeri yang berkomitmen untuk memberikan pendidikan berkualitas dan membentuk karakter siswa yang unggul. Dengan akreditasi B, sekolah kami terus berinovasi dalam mengembangkan metode pembelajaran yang efektif.</p>
        </div>

        <!-- Keunggulan -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
            <div class="text-center p-6 bg-orange-50 rounded-lg">
                <i class="fas fa-award text-3xl text-orange-600 mb-3"></i>
                <h3 class="font-semibold">Akreditasi B</h3>
                <p class="text-sm text-gray-600">Terakreditasi Unggul</p>
            </div>
            <div class="text-center p-6 bg-orange-50 rounded-lg">
                <i class="fas fa-chalkboard-teacher text-3xl text-orange-600 mb-3"></i>
                <h3 class="font-semibold">Guru Berkualitas</h3>
                <p class="text-sm text-gray-600">Pendidik Profesional</p>
            </div>
            <div class="text-center p-6 bg-orange-50 rounded-lg">
                <i class="fas fa-book text-3xl text-orange-600 mb-3"></i>
                <h3 class="font-semibold">Kurikulum Merdeka</h3>
                <p class="text-sm text-gray-600">Pembelajaran Inovatif</p>
            </div>
            <div class="text-center p-6 bg-orange-50 rounded-lg">
                <i class="fas fa-trophy text-3xl text-orange-600 mb-3"></i>
                <h3 class="font-semibold">Prestasi</h3>
                <p class="text-sm text-gray-600">Akademik & Non-Akademik</p>
            </div>
        </div>

        <!-- Visi & Misi -->
        <div class="grid md:grid-cols-2 gap-8 mb-16">
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">VISI</h3>
                <p class="text-gray-600">Terbinanya murid yang Berkarakter, Selamat, Bahagia, Berprestasi, dan Bermanfaat untuk Masyarakat (BERSAHABAT)</p>
            </div>
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">MISI</h3>
                <ul class="text-gray-600 space-y-3 list-disc pl-5">
                    <li>Menata lingkungan sekolah yang membentuk murid yang memiliki akhlak mulia melalui rutinitas kegiatan keagamaan.</li>
                    <li>Membangun lingkungan sekolah yang bertoleransi dalam kebhinekaan global.</li>
                    <li>Merancang pembelajaran yang menarik dan menyenangkan.</li>
                    <li>Mengembangkan dan memfasilitasi peningkatan prestasi murid.</li>
                    <li>Mengembangkan kemandirian, nalar kritis dan kreativitas.</li>
                </ul>
            </div>
        </div>

        <!-- Kontak -->
        <div class="bg-orange-50 rounded-lg p-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Kontak Kami</h3>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center">
                    <i class="fas fa-map-marker-alt text-3xl text-orange-600 mb-3"></i>
                    <h4 class="font-semibold mb-2">Alamat</h4>
                    <p class="text-gray-600">Desa Bontomacinna, Kecamatan Gantarang<br>Kabupaten Bulukumba, Sulawesi Selatan 92561</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-phone text-3xl text-orange-600 mb-3"></i>
                    <h4 class="font-semibold mb-2">Telepon</h4>
                    <p class="text-gray-600">+62 852-9923-5494<br>+62 852-5570-7086</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-envelope text-3xl text-orange-600 mb-3"></i>
                    <h4 class="font-semibold mb-2">Email</h4>
                    <p class="text-gray-600">sdn203bontomacinna@gmail.com</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <!-- Features Section -->
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-orange-600 text-3xl mb-4">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <h3 class="text-xl font-semibold mb-2">Absensi Digital</h3>
            <p class="text-gray-600">Sistem absensi digital yang memudahkan pencatatan kehadiran siswa secara efisien.</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-orange-600 text-3xl mb-4">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3 class="text-xl font-semibold mb-2">Laporan Real-time</h3>
            <p class="text-gray-600">Pantau kehadiran siswa secara real-time dan dapatkan laporan detail dengan mudah.</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-orange-600 text-3xl mb-4">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-xl font-semibold mb-2">Monitoring Orang Tua</h3>
            <p class="text-gray-600">Orang tua dapat memantau kehadiran dan aktivitas anak secara langsung melalui sistem.</p>
        </div>
    </div>

    <!-- Monitoring Section -->
    <div class="mt-12">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Cara Monitoring untuk Orang Tua</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Langkah-langkah Monitoring:</h3>
                    <ol class="list-decimal list-inside space-y-3 text-gray-600">
                        <li>Klik tombol "Monitoring Orang Tua" di atas</li>
                        <li>Masukkan NIS (Nomor Induk Siswa) anak Anda</li>
                        <li>Masukkan Tanggal Lahir anak sebagai verifikasi</li>
                        <li>Lihat informasi kehadiran dan aktivitas anak Anda</li>
                    </ol>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Fitur Monitoring:</h3>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Riwayat kehadiran harian
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Rekap absensi bulanan
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Notifikasi ketidakhadiran
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Laporan aktivitas sekolah
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?> 