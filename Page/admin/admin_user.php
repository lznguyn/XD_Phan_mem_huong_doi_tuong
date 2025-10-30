<?php
include '../user/config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    header('location:login.php');
    exit();
}

// Xử lý xóa tài khoản
// Xử lý xóa tài khoản và dữ liệu liên quan
if (isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete']);

    // 1️⃣ Xóa giỏ hàng của user
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$delete_id'") or die('Lỗi khi xóa giỏ hàng!');

    // 2️⃣ Xóa đơn hàng của user
    mysqli_query($conn, "DELETE FROM `orders` WHERE user_id = '$delete_id'") or die('Lỗi khi xóa đơn hàng!');

    // 4️⃣ Xóa nhạc đã gửi lên (nếu có)
    mysqli_query($conn, "DELETE FROM `music_submissions` WHERE user_id = '$delete_id'") or die('Lỗi khi xóa nhạc!');

    // xoa booking neu co
    mysqli_query($conn, "DELETE FROM `bookings` WHERE user_id = '$delete_id'") or die('Lỗi khi xóa booking!');

    // 5️⃣ Cuối cùng, xóa người dùng
    mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('Lỗi khi xóa tài khoản!');

    // 6️⃣ Thông báo
    $_SESSION['toast_message'] = "✅ Đã xóa tài khoản và dữ liệu liên quan thành công!";
    header('location:admin_user.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - MuTraPro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#f59e0b',
                        accent: '#10b981',
                        danger: '#dc2626',
                        admin: '#7c3aed'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">

    <?php include 'admin_header.php'; ?>

    <div class="min-h-screen pt-20">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-xl">
                        <i class="fas fa-users text-primary text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Quản lý người dùng</h1>
                        <p class="text-gray-600 mt-1">Xem và quản lý tất cả tài khoản trong hệ thống</p>
                    </div>
                </div>

                <?php
                $total_users = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE user_type = 'user'"));
                $total_admins = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE user_type = 'admin'"));
                $total_coordinator = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE user_type = 'coordinator'"));
                ?>
                <div class="hidden md:flex items-center space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary"><?php echo $total_users + $total_admins + $total_coordinator; ?></div>
                        <div class="text-sm text-gray-600">Tổng cộng</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-accent"><?php echo $total_users; ?></div>
                        <div class="text-sm text-gray-600">Người dùng</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-admin"><?php echo $total_admins; ?></div>
                        <div class="text-sm text-gray-600">Quản trị viên</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-admin"><?php echo $total_coordinator; ?></div>
                        <div class="text-sm text-gray-600">Điều phối viên</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách người dùng -->
        <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php
            $select_users = mysqli_query($conn, "SELECT * FROM `users` ORDER BY id DESC") or die('query failed');
            while ($user = mysqli_fetch_assoc($select_users)):
                $is_admin = $user['user_type'] === 'admin';
                $is_coordinator = ($user['user_type'] === 'coordinator');
                $badge_color = $is_admin ? 'bg-admin text-white' : 'bg-accent text-white';
            ?>
            <div class="bg-white border-2 <?php echo $is_admin ? 'border-admin' : 'border-gray-200'; ?> rounded-xl shadow-sm p-6 hover:shadow-lg transition-all">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas <?php 
                            if ($is_admin) echo 'fa-user-shield'; 
                            elseif ($is_coordinator) echo 'fa-user-tie'; 
                            else echo 'fa-user'; 
                        ?>  text-white text-xl"></i>
                    </div>
                    <div class="<?php echo $badge_color; ?> rounded-full mt-2 px-2 py-1 text-xs font-semibold inline-block">
                        <?php 
                            if ($is_admin) echo 'Admin';
                            elseif ($is_coordinator) echo 'Coordinator';
                            else echo 'User';
                        ?>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-center text-gray-900"><?php echo htmlspecialchars($user['name']); ?></h3>
                <p class="text-gray-600 text-center text-sm break-words mb-4"><?php echo htmlspecialchars($user['email']); ?></p>

                <div class="flex flex-col gap-2">
                    <?php if ($user['id'] != $admin_id): ?>
                    <a href="?delete=<?php echo $user['id']; ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa tài khoản này không?');"
                       class="bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-lg font-medium text-center transition-all">
                        <i class="fas fa-trash mr-2"></i>Xóa tài khoản
                    </a>
                    <?php else: ?>
                    <div class="bg-gray-50 text-gray-400 py-2 rounded-lg font-medium text-center">
                        <i class="fas fa-lock mr-2"></i>Tài khoản hiện tại
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Toast thông báo -->
    <script>
    function showToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.textContent = message;
        toast.className = `fixed bottom-6 right-6 px-4 py-3 rounded-lg text-white shadow-lg z-50 ${
            type === "success" ? "bg-green-600" : "bg-red-600"
        }`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add("opacity-0", "transition");
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
    </script>

    <?php if (isset($_SESSION['toast_message'])): ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        showToast("<?php echo addslashes($_SESSION['toast_message']); ?>");
    });
    </script>
    <?php unset($_SESSION['toast_message']); endif; ?>

</body>
</html>
