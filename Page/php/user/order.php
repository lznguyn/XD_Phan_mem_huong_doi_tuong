<?php
include 'config.php';
session_start();

// Kiểm tra đăng nhập
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Lấy danh sách đơn hàng của người dùng
$orders = mysqli_query($conn, "
    SELECT * FROM `orders` 
    WHERE user_id = '$user_id'
    ORDER BY id DESC
") or die('query failed');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Lịch sử giao dịch - MuTraPro</title>
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
<main class="flex-grow">
<section class="py-16">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-8">LỊCH SỬ GIAO DỊCH</h2>

    <?php if (mysqli_num_rows($orders) == 0): ?>
      <div class="text-center text-gray-600">Bạn chưa có đơn hàng nào.</div>
    <?php else: ?>
      <div class="overflow-x-auto bg-white rounded-2xl shadow-lg">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-primary text-white">
            <tr>
              <th class="py-3 px-4">Mã đơn</th>
              <th class="py-3 px-4">Ngày đặt</th>
              <th class="py-3 px-4">Sản phẩm</th>
              <th class="py-3 px-4">Tổng tiền</th>
              <th class="py-3 px-4">Thanh toán</th>
              <th class="py-3 px-4 text-center">Trạng thái</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php while($o = mysqli_fetch_assoc($orders)): ?>
            <tr class="hover:bg-gray-50">
              <td class="py-3 px-4 font-semibold text-primary">#<?= $o['id']; ?></td>
              <td class="py-3 px-4"><?= $o['placed_on']; ?></td>
              <td class="py-3 px-4 text-gray-700"><?= $o['total_products']; ?></td>
              <td class="py-3 px-4 font-semibold text-accent"><?= number_format($o['total_price'],0,',','.'); ?> VNĐ</td>
              <td class="py-3 px-4"><?= $o['method']; ?></td>
              <td class="py-3 px-4 text-center">
                <span class="px-3 py-1 text-white rounded-full 
                  <?php 
                    if($o['payment_status'] == 'completed') echo 'bg-accent';
                    elseif($o['payment_status'] == 'pending') echo 'bg-danger';
                    else echo 'bg-secondary';
                  ?>">
                  <?= $o['payment_status'] ?? 'Chờ xác nhận'; ?>
                </span>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
