<?php
include 'config.php';
session_start();

// Kiểm tra đăng nhập
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}
// Xử lý hủy lịch
if (isset($_POST['cancel_booking'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    mysqli_query($conn, "DELETE FROM `bookings` WHERE id='$booking_id' AND user_id='$user_id'") or die('query failed');
    $_SESSION['cancel_message'] = 'Hủy lịch thành công!';
    header('Location: ' . $_SERVER['PHP_SELF']);
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
            <div class="mt-2 mb-2">
              <?php 
                $status = $b['status'] ?? 'pending';
                $color = $status == 'completed' ? 'bg-green-500' : ($status == 'cancelled' ? 'bg-red-500' : 'bg-yellow-500');
                $label = $status == 'completed' ? 'Đã xác nhận' : ($status == 'cancelled' ? 'Đã hủy' : 'Đang chờ');
              ?>
              <span class="px-3 py-1 text-white text-sm rounded-full <?php echo $color; ?>">
                <?php echo $label; ?>
              </span>
            </div>
              <?php if ($b['status'] == 'pending'): ?>
                <form method="post" onsubmit="return confirmCancel();">
                  <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                  <button type="submit" name="cancel_booking" 
                          class="bg-danger hover:bg-red-700 text-white py-2 px-4 rounded-lg font-semibold">
                    <i class="fas fa-times-circle mr-1"></i> Hủy lịch
                  </button>
                </form>
              <?php elseif ($b['status'] == 'confirmed'): ?>
                <p class="text-green-600 font-semibold mt-2">Lịch đã được xác nhận</p>
              <?php elseif ($b['status'] == 'cancelled'): ?>
                <p class="text-red-600 font-semibold mt-2">Lịch đã bị hủy</p>
              <?php endif; ?>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'footer.php'; ?>
<script>
function confirmCancel(){
  return confirm("Bạn có chắc muốn hủy lịch này không?");
}
function showMessage(message){
  const modal = document.getElementById('messageModal');
  const text = document.getElementById('messageText');
  text.textContent = message;
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}
function closeModal(){
  document.getElementById('messageModal').classList.add('hidden');
  document.getElementById('messageModal').classList.remove('flex');
}

<?php if(isset($_SESSION['cancel_message'])): ?>
showMessage("<?php echo $_SESSION['cancel_message']; ?>");
<?php unset($_SESSION['cancel_message']); endif; ?>
</script>
</body>
</html>
