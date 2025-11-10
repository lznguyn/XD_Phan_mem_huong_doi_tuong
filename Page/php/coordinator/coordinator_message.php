<?php
include '../user/config.php';
session_start();

$coordinator_id = $_SESSION['coordinator_id'] ?? null;
if (!$coordinator_id) {
    header('location:login.php');
    exit();
}

// Khi admin gửi cập nhật
if (isset($_POST['update_status'])) {
    $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if (str_starts_with($item_id, 'booking-'))
    {
      $id = intval(substr($item_id, 8));
      // Cập nhật bookings
      mysqli_query($conn, "UPDATE bookings SET status='$status' WHERE id='$id'") or die('Lỗi cập nhật bookings!');
      
      // Lấy thông tin user & expert
      $info_query = mysqli_query($conn, "SELECT user_id, expert_id FROM bookings WHERE id='$id'");
      $info = mysqli_fetch_assoc($info_query);
      
      // Kiểm tra tồn tại
      $user_id = $info['user_id'] ?? null;
      $expert_id = $info['expert_id'] ?? null;
    } elseif (str_starts_with($item_id, 'submission-')) {
      $id = intval(substr($item_id, 11));
      // Cập nhật music_submissions
      mysqli_query($conn, "UPDATE music_submissions SET status='$status' WHERE id='$id'") or die('Lỗi cập nhật submissions!');
      
      // Lấy thông tin user (chỉ cần thông báo cho khách hàng)
      $info_query = mysqli_query($conn, "SELECT user_id FROM music_submissions WHERE id='$id'");
      $info = mysqli_fetch_assoc($info_query);
      
      // Kiểm tra tồn tại
      $user_id = $info['user_id'] ?? null;
      $expert_id = null;
    } else {
      die ('Muc khong ton tai!');
    }

     // Tiêu đề thông báo
    $title = "Cập nhật yêu cầu dịch vụ #$item_id";

   // Gửi thông báo đến khách hàng
if (!empty($user_id)) {
    mysqli_query($conn, "
        INSERT INTO notifications (user_id, title, message, status, created_at)
        VALUES ('$user_id', '$title', '$message', 'unread', NOW())
    ") or die('Lỗi gửi thông báo user!');
}

    // Gửi thông báo đến chuyên gia
if (!empty($expert_id)) {
    mysqli_query($conn, "
        INSERT INTO notifications (user_id, title, message, status, created_at)
        VALUES ('$expert_id', '$title', '$message', 'unread', NOW())
    ") or die('Lỗi gửi thông báo expert!');
}

    $_SESSION['toast_message'] = "✅ Đã cập nhật trạng thái và gửi thông báo thành công!";
    header('location:coordinator_message.php');
    exit();
}

// Lấy danh sách yêu cầu
$requests = mysqli_query($conn, "
  SELECT sr.*, u.name AS user_name, e.name AS expert_name
  FROM bookings sr
  JOIN users u ON sr.user_id = u.id
  JOIN experts e ON sr.expert_id = e.id
  ORDER BY sr.date DESC
") or die('Lỗi lấy danh sách yêu cầu!');
$music_requests = mysqli_query($conn, "
  SELECT ms.*, u.name AS customer_name
  FROM music_submissions ms
  JOIN users u ON ms.user_id = u.id
  ORDER BY ms.created_at DESC") or die('Lỗi lấy danh sách yêu cầu âm nhạc!');
?>

<!DOCTYPE html>

<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Gửi thông báo & Cập nhật - Điều phối viên</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<?php include 'coordinator_header.php'?>
<body class="bg-gray-50 mt-10">
  <div class="max-w-6xl mx-auto py-10 px-6">
    <h1 class="text-3xl font-bold text-indigo-700 mb-6">Gửi thông báo & Cập nhật</h1>

<div class="bg-white p-6 rounded-xl shadow">
  <form method="POST" class="space-y-6">
    <div>
      <label class="block text-gray-700 font-medium mb-2">Chọn yêu cầu dịch vụ</label>
      <select name="item_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
        <option value="">-- Chọn mục --</option>

        <optgroup label="Yêu cầu dịch vụ (Bookings)">
          <?php while ($b = mysqli_fetch_assoc($requests)): ?>
            <option value="booking-<?php echo $b['id']; ?>">
              #<?php echo $b['id']; ?> - <?php echo htmlspecialchars($b['user_name']); ?> (<?php echo htmlspecialchars($b['expert_name']); ?>)
            </option>
          <?php endwhile; ?>
        </optgroup>

        <optgroup label="Bài nhạc (Music Submissions)">
          <?php while ($s = mysqli_fetch_assoc($music_requests)): ?>
            <option value="submission-<?php echo $s['id']; ?>">
              #<?php echo $s['id']; ?> - <?php echo htmlspecialchars($s['title']); ?> (<?php echo htmlspecialchars($s['customer_name']); ?>)
            </option>
          <?php endwhile; ?>
        </optgroup>
      </select>

    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-2">Cập nhật trạng thái</label>
      <select name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
        <option value="pending">Chờ xử lý</option>
        <option value="in_progress">Đang xử lý</option>
        <option value="completed">Hoàn tất</option>
      </select>
    </div>

    <div>
      <label class="block text-gray-700 font-medium mb-2">Nội dung thông báo</label>
      <textarea name="message" rows="4" required placeholder="Nhập nội dung thông báo..." class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
    </div>

    <button type="submit" name="update_status" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold shadow">
      Gửi thông báo & Cập nhật
    </button>
  </form>
</div>

<?php if (isset($_SESSION['toast_message'])): ?>
  <p class="mt-6 text-green-600 font-semibold"><?php echo $_SESSION['toast_message']; unset($_SESSION['toast_message']); ?></p>
<?php endif; ?>


  </div>
</body>
</html>

