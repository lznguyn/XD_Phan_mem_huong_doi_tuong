<?php
include 'config.php';
session_start();

// Kiểm tra đăng nhập
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Biến thông báo
$upload_message = "";

// Xử lý gửi bản nhạc
if (isset($_POST['send_music_btn'])) {
    $title = mysqli_real_escape_string($conn, $_POST['music_title']);
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $target_role = mysqli_real_escape_string($conn, $_POST['target_role']);

    // DEBUG: Kiểm tra chi tiết file upload
    if (!isset($_FILES['music_file'])) {
        $upload_message = "Không tìm thấy file trong request. Kiểm tra thuộc tính enctype của form.";
    } elseif ($_FILES['music_file']['error'] !== 0) {
        // Giải thích mã lỗi
        $error_messages = [
            1 => "File vượt quá upload_max_filesize trong php.ini (hiện tại: " . ini_get('upload_max_filesize') . ")",
            2 => "File vượt quá MAX_FILE_SIZE trong form HTML",
            3 => "File chỉ được upload một phần",
            4 => "Không có file nào được chọn",
            6 => "Thiếu thư mục tạm để lưu file",
            7 => "Không thể ghi file vào ổ đĩa",
            8 => "Extension PHP đã dừng việc upload"
        ];
        $error_code = $_FILES['music_file']['error'];
        $upload_message = "Lỗi upload: " . ($error_messages[$error_code] ?? "Lỗi không xác định (code: $error_code)");
    } else {
        // File upload thành công, tiếp tục xử lý
        $file_name = $_FILES['music_file']['name'];
        $file_tmp = $_FILES['music_file']['tmp_name'];
        $file_size = $_FILES['music_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['mp3', 'wav', 'midi', 'flac'];
        
        // Kiểm tra định dạng
        if (!in_array($file_ext, $allowed_ext)) {
            $upload_message = "Định dạng file không hợp lệ. Chỉ chấp nhận: " . implode(', ', $allowed_ext);
        } 
        // Kiểm tra kích thước (10MB)
        elseif ($file_size > 10 * 1024 * 1024) {
            $upload_message = "File quá lớn. Kích thước tối đa: 10MB. File của bạn: " . round($file_size / 1024 / 1024, 2) . "MB";
        } 
        else {
            $upload_dir = 'uploaded_music/';
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    $upload_message = "Không thể tạo thư mục upload. Kiểm tra quyền ghi.";
                }
            }
            
            if (empty($upload_message)) {
                $new_name = uniqid('music_', true) . '.' . $file_ext;
                $destination = $upload_dir . $new_name;
                
                if (move_uploaded_file($file_tmp, $destination)) {
                    $query = "INSERT INTO `music_submissions`(user_id, title, file_name, note, status, target_role, created_at)
                              VALUES('$user_id', '$title', '$new_name', '$note', 'pending', '$target_role', NOW())";
                    
                    if (mysqli_query($conn, $query)) {
                        $_SESSION['upload_message'] = 'Gửi bản nhạc thành công! Chúng tôi sẽ liên hệ sớm.';
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit();
                    } else {
                        $upload_message = "Lỗi lưu database: " . mysqli_error($conn);
                        // Xóa file đã upload
                        unlink($destination);
                    }
                } else {
                    $upload_message = "Không thể di chuyển file. Kiểm tra quyền ghi thư mục uploaded_music/";
                }
            }
        }
    }
}

// Lấy danh sách bản nhạc đã gửi
$submissions = mysqli_query($conn, "
    SELECT * FROM `music_submissions`
    WHERE user_id = '$user_id'
    ORDER BY created_at DESC
") or die('query failed');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Gửi bản nhạc - MuTraPro</title>
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
  <div class="max-w-4xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-8">GỬI BẢN NHẠC CHO DỊCH VỤ BIÊN SOẠN</h2>

    <?php if (!empty($upload_message)): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-center">
        <?php echo $upload_message; ?>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bg-white shadow-lg rounded-2xl p-6 mb-10">
      <div class="mb-4">
        <label class="block font-semibold mb-1">Tên bản nhạc</label>
        <input type="text" name="music_title" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
      </div>

      <div class="mb-4">
        <label class="block font-semibold mb-1">Chọn file nhạc</label>
        <input type="file" name="music_file" accept=".mp3,.wav,.midi,.flac" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
      </div>

      <div class="mb-4">
        <label class="block font-semibold mb-1">Ghi chú (tuỳ chọn)</label>
        <textarea name="note" rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                  placeholder="Ví dụ: Mong muốn chỉnh tone, thêm nhạc cụ, làm bản phối mới..."></textarea>
      </div>

      <div class="mb-4">
        <label class="block font-semibold mb-1">Gửi đến</label>
        <select name="target_role" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">-- Chọn người nhận --</option>
            <option value="transcription">Chuyên gia Phiên âm</option>
            <option value="arrangement">Chuyên gia Hòa âm</option>
            <option value="recording_artists">Nghệ sĩ Thu âm</option>
        </select>
      </div>


      <button type="submit" name="send_music_btn"
              class="bg-primary hover:bg-blue-800 text-white px-6 py-3 rounded-lg font-semibold">
        <i class="fas fa-paper-plane mr-2"></i> Gửi bản nhạc
      </button>
    </form>

    <h3 class="text-2xl font-bold mb-4">BẢN NHẠC ĐÃ GỬI</h3>
    <?php if (mysqli_num_rows($submissions) == 0): ?>
      <div class="text-gray-600 text-center">Bạn chưa gửi bản nhạc nào.</div>
    <?php else: ?>
      <div class="space-y-4">
        <?php while($s = mysqli_fetch_assoc($submissions)): ?>
          <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between">
            <div>
              <h4 class="font-semibold text-lg"><?php echo htmlspecialchars($s['title']); ?></h4>
              <p class="text-gray-500 text-sm mb-1"><?php echo htmlspecialchars($s['note']); ?></p>
              <p class="text-sm text-gray-600">
                <i class="fas fa-calendar-alt text-primary mr-1"></i>
                <?php echo date('d/m/Y H:i', strtotime($s['created_at'])); ?>
              </p>
              <p class="text-sm text-gray-600">
                <i class="fas fa-user-tag text-secondary mr-1"></i>
                Gửi đến: <?php echo ucfirst($s['target_role']); ?>
              </p>
            </div>
            <div class="text-right">
              <?php
                $status = $s['status'];
                $color = $status == 'completed' ? 'bg-green-500' : ($status == 'rejected' ? 'bg-red-500' : 'bg-yellow-500');
                $label = $status == 'completed' ? 'Đã hoàn thành' : ($status == 'rejected' ? 'Từ chối' : 'Đang xử lý');
              ?>
              <span class="px-3 py-1 text-white text-sm rounded-full <?php echo $color; ?>">
                <?php echo $label; ?>
              </span>
              <a href="uploaded_music/<?php echo $s['file_name']; ?>" target="_blank"
                 class="block mt-2 text-primary underline text-sm">
                 <i class="fas fa-play mr-1"></i> Nghe bản gốc
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'footer.php'; ?>

<script>
<?php if(isset($_SESSION['upload_message'])): ?>
alert("<?php echo $_SESSION['upload_message']; ?>");
<?php unset($_SESSION['upload_message']); endif; ?>
</script>
</body>
</html>
