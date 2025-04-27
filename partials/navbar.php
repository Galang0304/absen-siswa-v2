<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Redirect ke login jika belum login
if(!isset($_SESSION['user_id']) && $current_page != 'login.php') {
    header("Location: login.php");
    exit;
}
?>

<!-- Desktop Sidebar -->
<nav class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:left-0 md:w-64 md:bg-white md:shadow-lg">
    <div class="flex flex-col h-full">
        <!-- Logo/Brand -->
        <div class="flex flex-col items-center justify-center h-auto px-4 py-6 bg-orange-600">
            <img src="assets/images/logo-sekolah.png" alt="Logo Sekolah" class="w-20 h-20 mb-3">
            <div class="text-center">
                <div class="text-xl font-bold text-white mb-1">SDN 203</div>
                <div class="text-sm font-medium text-orange-100">BONTOMACINNA</div>
            </div>
        </div>

        <!-- Menu Items -->
        <div class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="dashboard.php" 
               class="flex items-center px-4 py-3 rounded-lg <?= $current_page == 'dashboard.php' ? 'bg-orange-100 text-orange-600' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600' ?>">
                <i class="fas fa-home w-6"></i>
                <span class="ml-3">Dashboard</span>
            </a>

            <a href="absensi.php" 
               class="flex items-center px-4 py-3 rounded-lg <?= $current_page == 'absensi.php' ? 'bg-orange-100 text-orange-600' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600' ?>">
                <i class="fas fa-clipboard-check w-6"></i>
                <span class="ml-3">Absensi</span>
            </a>
            
            <a href="laporan.php" 
               class="flex items-center px-4 py-3 rounded-lg <?= $current_page == 'laporan.php' ? 'bg-orange-100 text-orange-600' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600' ?>">
                <i class="fas fa-file-alt w-6"></i>
                <span class="ml-3">Laporan</span>
            </a>
            
            <?php if($_SESSION['role'] == 'superadmin'): ?>
            <div class="pt-4 mb-2">
                <div class="text-xs uppercase tracking-wide font-semibold text-gray-500 px-4">Admin Menu</div>
            </div>

            <a href="guru.php" 
               class="flex items-center px-4 py-3 rounded-lg <?= $current_page == 'guru.php' ? 'bg-orange-100 text-orange-600' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600' ?>">
                <i class="fas fa-chalkboard-teacher w-6"></i>
                <span class="ml-3">Guru</span>
            </a>
            
            <a href="kelas.php" 
               class="flex items-center px-4 py-3 rounded-lg <?= $current_page == 'kelas.php' ? 'bg-orange-100 text-orange-600' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600' ?>">
                <i class="fas fa-school w-6"></i>
                <span class="ml-3">Kelas</span>
            </a>
            
            <a href="siswa.php" 
               class="flex items-center px-4 py-3 rounded-lg <?= $current_page == 'siswa.php' ? 'bg-orange-100 text-orange-600' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600' ?>">
                <i class="fas fa-user-graduate w-6"></i>
                <span class="ml-3">Siswa</span>
            </a>
            <?php endif; ?>
        </div>

        <!-- User Profile -->
        <div class="border-t border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-circle text-2xl text-orange-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($_SESSION['nama']) ?></p>
                    <a href="logout.php" class="text-xs text-orange-600 hover:text-orange-800">
                        <i class="fas fa-sign-out-alt mr-1"></i>Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Header -->
<div class="md:hidden fixed top-0 left-0 right-0 bg-orange-600 shadow-lg z-50">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center space-x-3">
            <img src="assets/images/logo-sekolah.png" alt="Logo Sekolah" class="w-10 h-10">
            <div>
                <div class="text-white font-bold text-lg">SDN 203</div>
                <div class="text-orange-100 text-xs">BONTOMACINNA</div>
            </div>
        </div>
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="flex items-center space-x-3">
                <span class="text-white text-sm"><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <a href="logout.php" class="bg-white text-orange-600 px-3 py-1 rounded-lg text-sm hover:bg-orange-50 transition duration-200">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content Padding for Mobile -->
<div class="md:hidden pt-16 pb-20">
    <!-- Content akan berada di sini -->
</div>

<!-- Mobile Bottom Navigation -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t border-gray-200 z-50">
    <div class="grid grid-cols-4 h-16">
        <a href="dashboard.php" 
           class="flex flex-col items-center justify-center <?= $current_page == 'dashboard.php' ? 'text-orange-600' : 'text-gray-600' ?>">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs mt-1">Dashboard</span>
        </a>

        <a href="absensi.php" 
           class="flex flex-col items-center justify-center <?= $current_page == 'absensi.php' ? 'text-orange-600' : 'text-gray-600' ?>">
            <i class="fas fa-clipboard-check text-xl"></i>
            <span class="text-xs mt-1">Absensi</span>
        </a>

        <a href="laporan.php" 
           class="flex flex-col items-center justify-center <?= $current_page == 'laporan.php' ? 'text-orange-600' : 'text-gray-600' ?>">
            <i class="fas fa-file-alt text-xl"></i>
            <span class="text-xs mt-1">Laporan</span>
        </a>

        <?php if($_SESSION['role'] == 'superadmin'): ?>
        <a href="#" onclick="toggleMenu(event)" 
           class="flex flex-col items-center justify-center <?= in_array($current_page, ['guru.php', 'kelas.php', 'siswa.php']) ? 'text-orange-600' : 'text-gray-600' ?>">
            <i class="fas fa-bars text-xl"></i>
            <span class="text-xs mt-1">Menu</span>
        </a>
        <?php endif; ?>
    </div>
</nav>

<!-- Mobile Menu Dropdown -->
<div id="mobileMenu" class="md:hidden fixed bottom-16 right-0 mb-2 mr-2 bg-white rounded-lg shadow-xl transform scale-0 transition-transform duration-200 origin-bottom-right">
    <div class="p-2 space-y-1">
        <?php if($_SESSION['role'] == 'superadmin'): ?>
        <div class="px-4 py-2 mb-2">
            <div class="text-xs uppercase tracking-wide font-semibold text-gray-500">Admin Menu</div>
        </div>
        
        <a href="guru.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-lg <?= $current_page == 'guru.php' ? 'bg-orange-100 text-orange-600' : '' ?>">
            <i class="fas fa-chalkboard-teacher w-6"></i>
            <span class="ml-3">Guru</span>
        </a>
        
        <a href="kelas.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-lg <?= $current_page == 'kelas.php' ? 'bg-orange-100 text-orange-600' : '' ?>">
            <i class="fas fa-school w-6"></i>
            <span class="ml-3">Kelas</span>
        </a>
        
        <a href="siswa.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-lg <?= $current_page == 'siswa.php' ? 'bg-orange-100 text-orange-600' : '' ?>">
            <i class="fas fa-user-graduate w-6"></i>
            <span class="ml-3">Siswa</span>
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content Padding -->
<div class="md:pl-64">
    <!-- Content akan berada di sini -->
</div>

<script>
function toggleMenu(e) {
    e.preventDefault();
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('scale-0');
    menu.classList.toggle('scale-100');
}

// Menutup menu saat mengklik di luar menu
document.addEventListener('click', function(e) {
    const menu = document.getElementById('mobileMenu');
    const menuButton = e.target.closest('a[onclick="toggleMenu(event)"]');
    const menuContent = e.target.closest('#mobileMenu');
    
    if (!menuButton && !menuContent) {
        menu.classList.add('scale-0');
        menu.classList.remove('scale-100');
    }
});
</script> 