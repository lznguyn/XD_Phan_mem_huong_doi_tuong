<?php
if (isset($message)) { // Kiểm tra message tồn tại hay không
    foreach ($message as $message) { // Vòng lặp từng phần tử trong message
        echo '
        <div class="message">
            <span>' . $message . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>
<header class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <img src="../project/images/logo.jpg" alt="Logo" class="h-10 w-10 object-cover rounded-full">
                <h1 class="text-2xl font-bold text-primary">MuTraPro</h1>
            </div>

            <!-- Navbar -->
            <nav class="hidden md:flex space-x-6 text-gray-700 font-medium">
                <a href="dashboard.php" class="hover:text-primary transition-colors">TRANG CHỦ</a>
                <a href="booking.php" class="hover:text-primary transition-colors">ĐẶT LỊCH</a>
                <a href="allBooking.php" class="hover:text-primary transition-colors">LỊCH CỦA TÔI</a>
                <a href="upload.php" class="hover:text-primary transition-colors">UPLOAD FILE</a>
                <a href="expert.php" class="hover:text-primary transition-colors">LIÊN HỆ CHUYÊN GIA</a>
                <a href="payment.php" class="hover:text-primary transition-colors">THANH TOÁN</a>
            </nav>

            <!-- Icons / User -->
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <a href="search_page.php" class="text-gray-700 hover:text-primary transition-colors">
                    <i class="fas fa-search text-xl"></i>
                </a>

                <!-- Cart -->
                <?php
                $select_cart_number = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
                $cart_rows_number = mysqli_num_rows($select_cart_number);
                ?>
                <a href="cart.php" class="relative text-gray-700 hover:text-primary transition-colors">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full px-2">
                        <?php echo $cart_rows_number; ?>
                    </span>
                </a>

                <!-- User Info -->
                <div class="relative">
                    <div id="user-btn" class="fas fa-user text-gray-700 cursor-pointer"></div>
                    <?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_email'])): ?>
                    <div class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg p-4 hidden" id="user-box">
                        <p class="text-gray-800 text-sm">Username: <span class="font-semibold"><?php echo $_SESSION['user_name']; ?></span></p>
                        <p class="text-gray-800 text-sm mb-2">Email: <span class="font-semibold"><?php echo $_SESSION['user_email']; ?></span></p>
                        <a href="logout.php" class="block text-center bg-red-500 text-white py-1 rounded hover:bg-red-600 transition-colors">Đăng Xuất</a>
                    </div>
                    <?php else: ?>
                        <div class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg p-3 hidden" id="user-box">
                            <a href="login.php" class="block text-center bg-blue-500 text-white py-1 rounded hover:bg-blue-600 transition-colors">Đăng Nhập</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile menu button -->
                <button id="menu-btn" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</header>
<script>
document.getElementById('user-btn').addEventListener('click', function() {
    const box = document.getElementById('user-box');
    box.classList.toggle('hidden');
});
</script>
