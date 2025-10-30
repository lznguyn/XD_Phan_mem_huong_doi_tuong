<?php
include '../user/config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:login.php');
    exit();
}

// ✅ Xác nhận booking
if (isset($_GET['confirm'])) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['confirm']);
    mysqli_query($conn, "UPDATE `bookings` SET status = 'completed' WHERE id = '$booking_id'") or die('Lỗi khi cập nhật trạng thái!');
    $_SESSION['toast_message'] = "✅ Đã xác nhận hoàn thành buổi đặt #$booking_id!";
    header('location:admin_booking.php');
    exit();
}

// ✅ Xóa booking
if (isset($_GET['delete'])) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM `bookings` WHERE id = '$booking_id'") or die('Lỗi khi xóa booking!');
    $_SESSION['toast_message'] = "🗑️ Đã xóa lịch đặt thành công!";
    header('location:admin_booking.php');
    exit();
}
?>

<!DOCTYPE html>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Booking - MuTraPro Admin</title>
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
                    <i class="fas fa-calendar-check text-primary text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý Booking</h1>
                    <p class="text-gray-600 mt-1">Xem và xác nhận các buổi đặt chuyên gia</p>
                </div>
            </div>
        </div>
    </div>

```
<!-- Danh sách Booking -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $bookings = mysqli_query($conn, "
            SELECT b.*, u.name AS user_name, e.name AS expert_name 
            FROM `bookings` b
            JOIN `users` u ON b.user_id = u.id
            JOIN `experts` e ON b.expert_id = e.id
            ORDER BY b.id DESC
        ") or die('Lỗi truy vấn dữ liệu!');

        if (mysqli_num_rows($bookings) == 0):
        ?>
            <div class="col-span-full text-center text-gray-500">Chưa có lịch đặt nào.</div>
        <?php
        else:
            while ($row = mysqli_fetch_assoc($bookings)):
                $isDone = $row['status'] === 'completed';
        ?>
            <div class="bg-white border-2 rounded-xl shadow-sm p-6 hover:shadow-lg transition">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-bold text-lg text-gray-900">Booking #<?php echo $row['id']; ?></h2>
                    <span class="text-sm px-3 py-1 rounded-full 
                        <?php echo $isDone ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                        <?php echo $isDone ? 'Hoàn thành' : 'Đang chờ'; ?>
                    </span>
                </div>

                <p class="text-gray-700"><strong>Người đặt:</strong> <?php echo htmlspecialchars($row['user_name']); ?></p>
                <p class="text-gray-700"><strong>Chuyên gia:</strong> <?php echo htmlspecialchars($row['expert_name']); ?></p>
                <p class="text-gray-700"><strong>Ngày hẹn:</strong> <?php echo htmlspecialchars($row['date']); ?></p>
                <p class="text-gray-700"><strong>Giờ hẹn:</strong> <?php echo htmlspecialchars($row['time_slot']); ?></p>
                <p class="text-gray-700"><strong>Trạng thái:</strong> <?php echo htmlspecialchars($row['status']); ?></p>

                <div class="mt-5 flex flex-col gap-2">
                    <?php if (!$isDone): ?>
                    <a href="?confirm=<?php echo $row['id']; ?>"
                       onclick="return confirm('Xác nhận buổi đặt này đã hoàn thành?');"
                       class="bg-green-50 hover:bg-green-100 text-green-700 py-2 rounded-lg font-medium text-center transition">
                        <i class="fas fa-check mr-2"></i>Xác nhận hoàn thành
                    </a>
                    <?php endif; ?>

                    <a href="?delete=<?php echo $row['id']; ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa booking này không?');"
                       class="bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-lg font-medium text-center transition">
                        <i class="fas fa-trash mr-2"></i>Xóa booking
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
