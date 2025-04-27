<?php
session_start();
require_once 'config/koneksi.php';

// Cek jika sudah login
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Proses Login
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM guru WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    $guru = $stmt->fetch();
    
    if($guru && password_verify($password, $guru['password'])) {
        $_SESSION['user_id'] = $guru['id'];
        $_SESSION['username'] = $guru['username'];
        $_SESSION['role'] = $guru['role'];
        $_SESSION['nama'] = $guru['nama'];
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}

require_once 'partials/header.php';
?>

<style>
.hero-pattern {
    background-color: #ff7e33;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.image-slider {
    animation: slideShow 20s infinite;
}

@keyframes slideShow {
    0%, 33% {
        opacity: 1;
        transform: scale(1);
    }
    34%, 35% {
        opacity: 0;
        transform: scale(1.1);
    }
    36%, 69% {
        opacity: 1;
        transform: scale(1);
    }
    70%, 71% {
        opacity: 0;
        transform: scale(1.1);
    }
    72%, 100% {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<div class="min-h-screen hero-pattern flex">
    <!-- Bagian Kiri - Gambar dan Informasi -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <!-- Overlay Gelap -->
        <div class="absolute inset-0 bg-black bg-opacity-40 z-10"></div>
        
        <!-- Gambar Slider -->
        <div class="absolute inset-0">
            <img src="assets/images/foto-gedung-sekolah.jpg" class="absolute inset-0 w-full h-full object-cover image-slider" alt="Gedung Sekolah" style="animation-delay: 0s">
            <img src="assets/images/foto-guru.jpg" class="absolute inset-0 w-full h-full object-cover image-slider" alt="Foto Guru" style="animation-delay: -13s">
        </div>
        
        <!-- Konten di atas gambar -->
        <div class="relative z-20 p-12 flex flex-col justify-center text-white">
            <img src="assets/images/logo-sekolah.png" alt="Logo Sekolah" class="w-32 h-32 mb-8">
            <h1 class="text-4xl font-bold mb-4">SDN 203 BONTOMACINNA</h1>
            <p class="text-lg mb-6">Sistem Informasi Absensi Siswa</p>
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-6">
                <p class="text-sm">Kec. Gantarang, Kab. Bulukumba</p>
                <p class="text-sm">Provinsi Sulawesi Selatan</p>
            </div>
        </div>
    </div>

    <!-- Bagian Kanan - Form Login -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="max-w-md w-full">
            <!-- Tombol Kembali -->
            <div class="text-center mb-6">
                <a href="index.php" class="inline-flex items-center px-4 py-2 bg-white text-orange-600 rounded-lg hover:bg-orange-50 transition duration-200 shadow-sm">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-2xl p-8">
                <div class="text-center mb-8">
                    <!-- Logo Mobile -->
                    <div class="lg:hidden mb-6">
                        <img src="assets/images/logo-sekolah.png" alt="Logo Sekolah" class="w-24 h-24 mx-auto">
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Selamat Datang
                    </h2>
                    <p class="text-sm text-gray-600">
                        Silakan masuk untuk mengakses Sistem Absensi Siswa
                    </p>
                </div>
                
                <?php if(isset($error)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700"><?= $error ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form class="space-y-6" method="POST">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">
                            Username
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="username" name="username" type="text" required 
                                   class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent sm:text-sm" 
                                   placeholder="Masukkan username">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required 
                                   class="appearance-none block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent sm:text-sm" 
                                   placeholder="Masukkan password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" onclick="togglePassword()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i id="toggleIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-200">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt text-orange-300 group-hover:text-orange-200"></i>
                            </span>
                            Masuk
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-600">
                        &copy; <?= date('Y') ?> SDN 203 BONTOMACINNA. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

<?php require_once 'partials/footer.php'; ?> 