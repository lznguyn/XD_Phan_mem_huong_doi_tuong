<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:login.php');
    exit();
}

// ✅ Xử lý xác nhận thanh toán
if (isset($_GET['confirm'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['confirm']);
    mysqli_query($conn, "UPDATE `orders` SET payment_status = 'completed' WHERE id = '$order_id'") or die('Lỗi khi xác nhận thanh toán!');
    $_SESSION['toast_message'] = "✅ Đơn hàng #$order_id đã được xác nhận thanh toán!";
    header('location:admin_orders.php');
    exit();
}

// ✅ Xử lý xóa đơn hàng
if (isset($_GET['delete'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$order_id'") or die('Lỗi khi xóa đơn hàng!');
    $_SESSION['toast_message'] = "🗑️ Đã xóa đơn hàng thành công!";
    header('location:admin_orders.php');
    exit();
}
?>

<!DOCTYPE html>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - MuTraPro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
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
        <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="bg-primary bg-opacity-10 p-3 rounded-xl">
                    <i class="fas fa-shopping-cart text-primary text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý đơn hàng</h1>
                    <p class="text-gray-600 mt-1">Xem và xác nhận thanh toán cho các đơn hàng</p>
                </div>
            </div>
        </div>
    </div>
<!-- Danh sách đơn hàng -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $orders = mysqli_query($conn, "SELECT o.*, u.name AS user_name, u.email AS user_email FROM `orders` o 
                                       JOIN `users` u ON o.user_id = u.id 
                                       ORDER BY o.id DESC") or die('Lỗi truy vấn đơn hàng!');

        if (mysqli_num_rows($orders) == 0):
        ?>
            <div class="col-span-full text-center text-gray-500">Chưa có đơn hàng nào.</div>
        <?php
        else:
            while ($order = mysqli_fetch_assoc($orders)):
                $isPaid = $order['payment_status'] === 'completed';
        ?>
            <div class="bg-white border-2 rounded-xl shadow-sm p-6 hover:shadow-lg transition">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-bold text-lg text-gray-900">Đơn hàng #<?php echo $order['id']; ?></h2>
                    <span class="text-sm px-3 py-1 rounded-full 
                        <?php echo $isPaid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                        <?php echo $isPaid ? 'Đã thanh toán' : 'Chưa thanh toán'; ?>
                    </span>
                </div>

                <p class="text-gray-700"><strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                <p class="text-gray-700"><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
                <p class="text-gray-700"><strong>Tổng tiền:</strong> <?php echo number_format($order['total_price'], 0, ',', '.'); ?>₫</p>
                <p class="text-gray-700"><strong>Ngày đặt:</strong> <?php echo htmlspecialchars($order['placed_on']); ?></p>

                <div class="mt-5 flex flex-col gap-2">
                    <?php if (!$isPaid): ?>
                    <a href="?confirm=<?php echo $order['id']; ?>"
                       onclick="return confirm('Xác nhận đơn hàng này đã thanh toán?');"
                       class="bg-green-50 hover:bg-green-100 text-green-700 py-2 rounded-lg font-medium text-center transition">
                        <i class="fas fa-check mr-2"></i>Xác nhận thanh toán
                    </a>
                    <?php endif; ?>

                    <a href="?delete=<?php echo $order['id']; ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này không?');"
                       class="bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-lg font-medium text-center transition">
                        <i class="fas fa-trash mr-2"></i>Xóa đơn hàng
                    </a>
                </div>
            </div>
        <?php
            endwhile;
        endif;
        ?>
    </div>
</div>
```

</div>

<!-- Toast thông báo -->

<script>
function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.textContent = message;
    toast.className = `fixed bottom-6 right-6 px-4 py-3 rounded-lg text-white shadow-lg z-50 ${type === "success" ? "bg-green-600" : "bg-red-600"}`;
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
