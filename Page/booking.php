<?php
include 'config.php';
session_start();
// Kiểm tra người dùng đã đăng nhập
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
// Xử lý đặt lịch
if (isset($_POST['book_session'])) {
    if ($user_id == null) {
        header('Location: login.php');
        exit();
    }

      $expert_id = mysqli_real_escape_string($conn, $_POST['expert_id']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $time_slot = mysqli_real_escape_string($conn, $_POST['time_slot']);

    // Kiểm tra trùng lịch
    $check_booking = mysqli_query($conn, "
        SELECT * FROM `bookings` 
        WHERE expert_id='$expert_id' AND date='$booking_date' AND time_slot='$time_slot'
    ") or die('query failed');

    if (mysqli_num_rows($check_booking) > 0) {
        $_SESSION['booking_message'] = 'Khung giờ này đã có người đặt!';
        $_SESSION['booking_success'] = false;
    } else {
      mysqli_query($conn, "
          INSERT INTO bookings (user_id, expert_id, date, time_slot, status)
          VALUES ('$user_id', '$expert_id', '$date', '$time_slot', 'pending')
          ") or die('query failed');

        $_SESSION['booking_message'] = 'Đặt lịch thành công!';
        $_SESSION['booking_success'] = true;
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!-- Success/Error Message Modal -->
<!-- Modal thông báo -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 max-w-sm mx-4 text-center">
    <i id="messageIcon" class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
    <h3 id="messageTitle" class="text-lg font-semibold mb-2">Đặt lịch thành công!</h3>
    <p id="messageText" class="text-gray-600 mb-4">Chúng tôi sẽ liên hệ với bạn để xác nhận lịch hẹn.</p>
    <button onclick="closeModal()" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700">Đóng</button>
  </div>
</div>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lịch thu âm trực tiếp - MuTraPro</title>
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
<!-- Hero -->
<section class="relative h-80 bg-gradient-to-r from-purple-900 to-blue-900 flex items-center justify-center">
  <div class="absolute inset-0 bg-black bg-opacity-60"></div>
  <img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" class="absolute inset-0 w-full h-full object-cover" alt="">
  <div class="relative z-10 text-center text-white px-4">
    <h2 class="text-4xl font-bold mb-4">ĐẶT LỊCH CHUYÊN GIA ÂM NHẠC</h2>
    <p>Chọn chuyên gia và khung giờ phù hợp để được hỗ trợ thu âm cùng đội ngũ chuyên nghiệp của MuTraPro.</p>
  </div>
</section>
<!-- Experts -->
<section class="py-16">
  <div class="max-w-7xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-12">CHUYÊN GIA HÀNG ĐẦU</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php
      $experts = mysqli_query($conn, "SELECT * FROM experts") or die('query failed');
      while ($e = mysqli_fetch_assoc($experts)):
        // Lấy số slot trống của chuyên gia
        $total_slots = 8;
        $booked_slots = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as booked FROM bookings WHERE expert_id='".$e['id']."'"))['booked'];
        $available_slots = $total_slots - $booked_slots;
        $status_class = $available_slots == 0 ? 'bg-danger' : 'bg-accent';
        $status_text = $available_slots == 0 ? 'Full' : 'Online';
      ?>
      <form method="post" class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center">
        <div class="relative mb-4">
          <img src="uploaded_img/<?php echo $e['image']; ?>" class="w-32 h-32 rounded-full border-4 border-primary object-cover">
          <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 <?php echo $status_class ?> text-white px-2 py-1 rounded-full text-xs font-semibold flex items-center">
            <i class="fas fa-circle mr-1 text-xs"></i><?php echo $status_text ?>
          </div>
        </div>
        <div class="text-xl font-bold mb-1"><?php echo $e['name']; ?></div>
        <div class="text-gray-600 mb-2"><?php echo $e['role']; ?></div>
        <div class="text-2xl font-bold text-primary mb-4"><?php echo number_format($e['price'],0,',','.'); ?> VNĐ / giờ</div>

        <input type="hidden" name="expert_id" value="<?php echo $e['id']; ?>">
        <input type="date" name="booking_date" required class="border p-2 rounded mb-2 w-full" min="<?php echo date('Y-m-d'); ?>">
        <select name="time_slot" required class="border p-2 rounded mb-2 w-full">
          <option value="">-- Chọn khung giờ --</option>
          <option value="09:00-10:00">09:00-10:00</option>
          <option value="10:00-11:00">10:00-11:00</option>
          <option value="14:00-15:00">14:00-15:00</option>
          <option value="15:00-16:00">15:00-16:00</option>
        </select>
        <div class="bg-gray-50 rounded-lg p-2 w-full mb-2 text-center <?php echo $available_slots==0?'text-danger':'text-accent'; ?>">
          <?php echo $available_slots.'/'.$total_slots ?> slot còn trống
        </div>
        <button type="submit" name="book_session" class="w-full py-2 rounded-lg text-white font-semibold <?php echo $available_slots==0?'bg-gray-400 cursor-not-allowed':'bg-primary hover:bg-blue-700'; ?>" <?php echo $available_slots==0?'disabled':''; ?>>
          <i class="fas fa-calendar-check mr-2"></i>Đặt lịch ngay
        </button>
      </form>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<!-- Footer -->
<?php include 'footer.php'; ?>

<script>
function showMessage(message, success=true){
  const modal = document.getElementById('messageModal');
  const icon = document.getElementById('messageIcon');
  const title = document.getElementById('messageTitle');
  const text = document.getElementById('messageText');

  if(success){
    icon.className = 'fas fa-check-circle text-4xl text-green-500 mb-4';
    title.textContent = 'Đặt lịch thành công!';
  } else {
    icon.className = 'fas fa-exclamation-circle text-4xl text-red-500 mb-4';
    title.textContent = 'Thông báo!';
  }

  text.textContent = message;
  modal.classList.remove('hidden'); modal.classList.add('flex');
}
function closeModal(){ const modal = document.getElementById('messageModal'); modal.classList.add('hidden'); modal.classList.remove('flex'); }

<?php if(isset($_SESSION['booking_message'])): ?>
showMessage("<?php echo $_SESSION['booking_message']; ?>", <?php echo strpos($_SESSION['booking_message'],'thành công')!==false?'true':'false'; ?>);
<?php unset($_SESSION['booking_message']); endif; ?>
</script>

</body>
</html>
