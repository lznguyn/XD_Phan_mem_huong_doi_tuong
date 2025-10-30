<?php
include '../user/config.php';
session_start();

// Kiểm tra quyền đăng nhập admin
$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điều khiển Admin - MuTraPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#f59e0b',
                        accent: '#10b981',
                        danger: '#dc2626',
                        success: '#059669',
                        warning: '#d97706',
                        info: '#0284c7'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50">

<?php include 'admin_header.php'; ?>

<div class="min-h-screen pt-20">
    <!-- Chào mừng -->
    <div class="bg-gradient-to-r from-primary to-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">Chào mừng trở lại, Admin!</h1>
                <p class="text-blue-100">Tổng quan hoạt động hệ thống MuTraPro hôm nay</p>
            </div>
            <div class="hidden md:block bg-white bg-opacity-20 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold"><?php echo date('d'); ?></div>
                <div class="text-sm"><?php echo date('M Y'); ?></div>
            </div>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Lấy dữ liệu thống kê -->
        <?php
        $total_pendings = $total_completed = 0;
        $select_pending = mysqli_query($conn, "SELECT total_price FROM orders WHERE payment_status = 'pending'");
        while ($row = mysqli_fetch_assoc($select_pending)) $total_pendings += $row['total_price'];
        
        $select_completed = mysqli_query($conn, "SELECT total_price FROM orders WHERE payment_status = 'completed'");
        while ($row = mysqli_fetch_assoc($select_completed)) $total_completed += $row['total_price'];

        $orders_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM orders"));
        $products_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM services"));
        $pending_orders_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bookings WHERE status = 'pending'"));
        $completed_orders_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bookings WHERE status = 'completed'"));
        $experts_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM experts"));
        $musicsub_pending_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM music_submissions WHERE status = 'pending'"));
        $musicsub_completed_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM music_submissions WHERE status = 'completed'"));

        $users_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE user_type='user'"));
        $admins_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE user_type='admin'"));
        ?>

        <!-- Các thẻ thống kê -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between mb-4">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-xl">
                        <i class="fas fa-clock text-warning text-xl"></i>
                    </div>
                    <span class="text-warning font-medium">Đang xử lý</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo number_format($total_pendings, 0, ',', '.'); ?> VNĐ</h3>
                <p class="text-gray-600 text-sm mt-1">Tổng tiền chờ xử lý</p>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between mb-4">
                    <div class="bg-success bg-opacity-10 p-3 rounded-xl">
                        <i class="fas fa-check-circle text-success text-xl"></i>
                    </div>
                    <span class="text-success font-medium">Hoàn thành</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo number_format($total_completed, 0, ',', '.'); ?> VNĐ</h3>
                <p class="text-gray-600 text-sm mt-1">Tổng tiền đã thanh toán</p>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between mb-4">
                    <div class="bg-info bg-opacity-10 p-3 rounded-xl">
                        <i class="fas fa-shopping-cart text-info text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo $orders_count; ?></h3>
                <p class="text-gray-600 text-sm mt-1">Tổng đơn hàng</p>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <i class="fas fa-music text-purple-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo $products_count; ?></h3>
                <p class="text-gray-600 text-sm mt-1">Dịch vụ âm nhạc</p>
            </div>
            
            <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <i class="fas fa-music text-purple-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo $musicsub_pending_count; ?></h3>
                <p class="text-gray-600 text-sm mt-1">Yêu cầu chỉnh sửa nhạc chưa hoàn tất</p>
            </div>
              <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <i class="fas fa-music text-purple-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo $musicsub_completed_count; ?></h3>
                <p class="text-gray-600 text-sm mt-1">Yêu cầu chỉnh sửa nhạc đã hoàn tất</p>
            </div>

             <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo $experts_count; ?></h3>
                <p class="text-gray-600 text-sm mt-1">Chuyên gia công ty</p>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between mb-4">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-xl">
                        <i class="fas fa-clock text-warning text-xl"></i>
                    </div>
                    <span class="text-warning font-medium">Đang xử lý</span>
                </div>
                <p class="text-gray-600 text-sm mt-1"><?php echo $pending_orders_count; ?> booking đang chờ xử lý</p>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <div class="flex justify-between mb-4">
                    <div class="bg-success bg-opacity-10 p-3 rounded-xl">
                        <i class="fas fa-check-circle text-success text-xl"></i>
                    </div>
                    <span class="text-success font-medium">Hoàn thành</span>
                </div>
                <p class="text-gray-600 text-sm mt-1"><?php echo $completed_orders_count; ?> booking đã thanh toán</p>
            </div>

        </div>
        

        <!-- Người dùng, admin, tin nhắn -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow p-6">
                <div class="flex justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-xl">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo $users_count; ?></h3>
                <p class="text-gray-600 text-sm">Người dùng</p>
            </div>

            <div class="bg-white rounded-2xl shadow p-6">
                <div class="flex justify-between mb-4">
                    <div class="bg-red-100 p-3 rounded-xl">
                        <i class="fas fa-user-shield text-red-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo $admins_count; ?></h3>
                <p class="text-gray-600 text-sm">Quản trị viên</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
