<?php if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="fixed top-5 right-5 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center justify-between gap-4 animate-fade-in-down z-50">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times cursor-pointer hover:text-gray-200" onclick="this.parentElement.remove();"></i>
        </div>';
    }
} ?>

<header class="fixed top-0 left-0 w-full bg-indigo-700 text-white shadow-lg z-40">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <!-- Logo -->
    <a href="service_requests.php" class="flex items-center space-x-3">
      <img src="../images/logo.png" alt="Logo" class="h-9 w-9 bg-white rounded-full p-1 shadow">
      <h1 class="text-xl font-bold tracking-wide">Điều phối viên <span class="text-yellow-300">Dịch vụ</span></h1>
    </a>


<!-- Menu -->
<nav class="hidden md:flex items-center space-x-6 text-sm font-medium">
  <a href="coordinator_page.php" class="hover:text-yellow-300 transition">Yêu cầu</a>
  <a href="coordinator_message.php" class="hover:text-yellow-300 transition">Gửi tin nhắn</a>
  <a href="reports.php" class="hover:text-yellow-300 transition">Báo cáo</a>
</nav>

<!-- User -->
<div class="flex items-center gap-3">
  <button id="user-btn" class="flex items-center space-x-2 focus:outline-none">
    <i class="fas fa-user-circle text-2xl"></i>
    <span class="hidden sm:block text-sm">
      <?php echo $_SESSION['coordinator_name'] ?? 'Điều phối viên'; ?>
    </span>
  </button>
  <button id="menu-btn" class="text-2xl md:hidden">
    <i class="fas fa-bars"></i>
  </button>
</div>


  </div>

  <!-- Dropdown tài khoản -->

  <div id="account-box"
       class="hidden absolute right-5 top-20 bg-white text-gray-700 border border-gray-200 rounded-xl shadow-xl p-4 w-72 transition-all duration-200">
    <p class="font-medium mb-1">
      👤 Tên: <span class="text-indigo-700 font-semibold"><?php echo $_SESSION['coordinator_name'] ?? 'Điều phối viên'; ?></span>
    </p>
    <p class="mb-3">📧 Email: <span><?php echo $_SESSION['coordinator_email'] ?? 'coordinator@example.com'; ?></span></p>
    <a href="coordinator_logout.php"
       class="block text-center bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition mb-2">
      <i class="fas fa-sign-out-alt mr-1"></i>Đăng xuất
    </a>
  </div>
</header>

<!-- Mobile menu -->

<div id="mobile-nav" class="hidden fixed inset-0 bg-black bg-opacity-50 z-30">
  <div class="absolute right-0 top-0 h-full w-64 bg-white shadow-xl flex flex-col p-6 space-y-4 text-gray-700">
    <button id="close-menu" class="self-end text-xl mb-4">
      <i class="fas fa-times"></i>
    </button>
    <a href="coordinator_page.php" class="hover:text-indigo-700">Yêu cầu</a>
    <a href="experts.php" class="hover:text-indigo-700">Chuyên gia</a>
    <a href="reports.php" class="hover:text-indigo-700">Báo cáo</a>
    <a href="coordinator_logout.php" class="text-red-600 font-medium mt-2">Đăng xuất</a>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const userBtn = document.getElementById("user-btn");
  const accountBox = document.getElementById("account-box");
  const menuBtn = document.getElementById("menu-btn");
  const mobileNav = document.getElementById("mobile-nav");
  const closeMenu = document.getElementById("close-menu");

  userBtn?.addEventListener("click", () => accountBox.classList.toggle("hidden"));
  menuBtn?.addEventListener("click", () => mobileNav.classList.remove("hidden"));
  closeMenu?.addEventListener("click", () => mobileNav.classList.add("hidden"));

  document.addEventListener("click", (e) => {
    if (!accountBox.contains(e.target) && !userBtn.contains(e.target)) {
      accountBox.classList.add("hidden");
    }
  });
});
</script>

<style>
@keyframes fade-in-down {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down { animation: fade-in-down 0.3s ease-in-out; }
</style>
