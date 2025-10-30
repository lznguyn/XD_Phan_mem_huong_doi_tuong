<?php if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="fixed top-5 right-5 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center justify-between gap-4 animate-fade-in-down z-50">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times cursor-pointer hover:text-gray-200" onclick="this.parentElement.remove();"></i>
        </div>';
    }
} ?>
<header class="bg-indigo-600 text-white shadow-md">
    <div class="max-w-6xl mx-auto flex justify-between items-center py-4 px-6">
        <div class="flex items-center gap-3">
            <i class="fas fa-music text-2xl"></i>
            <h1 class="text-2xl font-bold">Chuyên gia Hòa âm</h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm">Xin chào, <?php echo htmlspecialchars($_SESSION['arragement_name'] ?? 'Chuyên gia'); ?></span>
            <a href="../user/logout.php" class="bg-white text-indigo-600 px-3 py-1 rounded hover:bg-gray-100 transition text-sm">
                Đăng xuất
            </a>
        </div>
    </div>
</header>
