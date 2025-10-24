<?php
include 'config.php';
session_start();

// Kiểm tra đăng nhập
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Lấy danh sách lịch đã đặt của user
$bookings = mysqli_query($conn, "
    SELECT b.*, e.name AS expert_name, e.image AS expert_image, e.role AS expert_role
    FROM bookings b
    JOIN experts e ON b.expert_id = e.id
    WHERE b.user_id = '$user_id'
    ORDER BY b.date DESC, b.time_slot ASC
") or die('query failed');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Lịch của tôi - MuTraPro</title>
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
<body class="bg-gray-50">
<?php include 'header.php'; ?>

<section class="py-16">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-8">LỊCH CỦA TÔI</h2>

    <?php if (mysqli_num_rows($bookings) == 0): ?>
      <div class="text-center text-gray-600">Bạn chưa có lịch đặt nào.</div>
    <?php else: ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php while($b = mysqli_fetch_assoc($bookings)): ?>
          <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center">
            <img src="uploaded_img/<?php echo $b['expert_image']; ?>" 
                 class="w-28 h-28 rounded-full border-4 border-primary object-cover mb-3">
            <h3 class="font-bold text-lg mb-1"><?php echo $b['expert_name']; ?></h3>
            <p class="text-gray-500 text-sm mb-2"><?php echo $b['expert_role']; ?></p>
            <div class="text-sm text-gray-700 mb-2">
              <i class="fas fa-calendar-alt text-primary mr-2"></i>
              <?php echo date('d/m/Y', strtotime($b['date'])); ?>
            </div>
            <div class="text-sm text-gray-700 mb-4">
              <i class="fas fa-clock text-primary mr-2"></i>
              <?php echo $b['time_slot']; ?>
            </div>
            <div class="text-accent font-semibold">Đã xác nhận</div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
