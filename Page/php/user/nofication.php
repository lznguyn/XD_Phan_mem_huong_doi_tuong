<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('location:login.php');
    exit();
}

// Lấy danh sách thông báo của người dùng hiện tại
$notifications = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY created_at DESC") or die('Query failed');
?>

<!DOCTYPE html>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo của bạn</title>
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
                        danger: '#dc2626'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 min-h-screen">
    <?php include 'header.php'?>
    <div class="max-w-3xl mx-auto bg-white mt-10 p-6 rounded-xl shadow-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            🔔 Thông báo của bạn
        </h1>
    <?php if (mysqli_num_rows($notifications) > 0): ?>
        <div class="space-y-4">
            <?php while ($n = mysqli_fetch_assoc($notifications)): ?>
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-500"><?php echo date('H:i d/m/Y', strtotime($n['created_at'])); ?></span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?php echo $n['status'] == 'unread' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'; ?>">
                            <?php echo $n['status'] == 'unread' ? 'Chưa đọc' : 'Đã đọc'; ?>
                        </span>
                    </div>
                    <p class="text-gray-800 font-medium mb-1"><?php echo $n['title']; ?></p>
                    <p class="text-gray-600 text-sm"><?php echo $n['message']; ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600 text-center">Bạn chưa có thông báo nào.</p>
    <?php endif; ?>
</div>
```

</body>
</html>
