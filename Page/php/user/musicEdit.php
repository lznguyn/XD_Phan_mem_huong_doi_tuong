<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}
// Khi nhấn Thanh toán, thêm vào giỏ và chuyển qua trang thanh toán
if (isset($_POST['go_to_checkout'])) {
    $submission_id = intval($_POST['submission_id']);
    $music = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `music_submissions` WHERE id='$submission_id' AND user_id='$user_id'"));
    if ($music) {
        $name = mysqli_real_escape_string($conn, $music['title']);
        $price = 500000; // hoặc lấy từ cấu hình
        $quantity = 1;

        // Kiểm tra trùng
        $exists = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id='$user_id' AND name='$name'");
        if (mysqli_num_rows($exists) == 0) {
            mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image)
                VALUES('$user_id', '$name', '$price', '$quantity', '')") or die('query failed');
        }

        header('Location: payment.php'); // hoặc payment.php
        exit();
    }
}

// Xử lý yêu cầu chỉnh sửa
if (isset($_POST['request_edit'])) {
    $submission_id = mysqli_real_escape_string($conn, $_POST['submission_id']);
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    $file_name = '';

    if (isset($_FILES['edit_file']) && $_FILES['edit_file']['error'] == 0) {
        $file_tmp = $_FILES['edit_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['edit_file']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['mp3', 'wav', 'midi', 'flac', 'txt'];
        if (in_array($file_ext, $allowed_ext)) {
            $dir = 'uploaded_edits/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $file_name = uniqid('edit_', true) . '.' . $file_ext;
            move_uploaded_file($file_tmp, $dir . $file_name);
        }
    }

    mysqli_query($conn, "INSERT INTO `edit_requests`(submission_id, user_id, feedback, file_name, created_at)
        VALUES('$submission_id', '$user_id', '$feedback', '$file_name', NOW())") or die('query failed');

    mysqli_query($conn, "UPDATE `music_submissions` SET status='pending_edit' WHERE id='$submission_id' AND user_id='$user_id'");

    $_SESSION['msg'] = 'Yêu cầu chỉnh sửa đã được gửi thành công!';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Xử lý phê duyệt
if (isset($_POST['approve_final'])) {
    $submission_id = mysqli_real_escape_string($conn, $_POST['submission_id']);
    mysqli_query($conn, "UPDATE `music_submissions` SET status='approved' WHERE id='$submission_id' AND user_id='$user_id'");
    $_SESSION['msg'] = 'Bạn đã phê duyệt sản phẩm cuối cùng!';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Lấy danh sách bản nhạc đã hoàn thành
$completed = mysqli_query($conn, "
    SELECT * FROM `music_submissions`
    WHERE user_id='$user_id' AND status IN ('completed','pending_edit','approved')
    ORDER BY created_at DESC
") or die('query failed');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Phê duyệt & Chỉnh sửa - MuTraPro</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: { extend: { colors: { primary: '#1e40af', accent: '#10b981', danger: '#dc2626' } } }
    }
  </script>
</head>
<body class="bg-gray-50">
<?php include 'header.php'; ?>

<section class="py-16">
  <div class="max-w-5xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-8">PHÊ DUYỆT / CHỈNH SỬA SẢN PHẨM CUỐI</h2>

    <?php if(isset($_SESSION['msg'])): ?>
      <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-6 text-center">
        <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
      </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($completed) == 0): ?>
      <div class="text-gray-600 text-center">Hiện chưa có sản phẩm nào hoàn thành để phê duyệt.</div>
    <?php else: ?>
      <div class="space-y-6">
        <?php while($c = mysqli_fetch_assoc($completed)): ?>
          <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex justify-between items-center">
              <div>
                <h3 class="font-bold text-xl mb-1"><?php echo htmlspecialchars($c['title']); ?></h3>
                <p class="text-sm text-gray-600 mb-2">
                  <i class="fas fa-calendar-alt text-primary mr-1"></i>
                  <?php echo date('d/m/Y H:i', strtotime($c['created_at'])); ?>
                </p>
              </div>
              <div>
                <?php
                  $status = $c['status'];
                  $label = $status == 'completed' ? 'Hoàn thành' : ($status == 'pending_edit' ? 'Đang chỉnh sửa' : 'Đã phê duyệt');
                  $color = $status == 'completed' ? 'bg-green-500' : ($status == 'pending_edit' ? 'bg-yellow-500' : 'bg-blue-500');
                ?>
                <span class="px-3 py-1 text-white rounded-full text-sm <?php echo $color; ?>"><?php echo $label; ?></span>
              </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
              <?php
                $file_ext = strtolower(pathinfo($c['file_name'], PATHINFO_EXTENSION));
                if (in_array($file_ext, ['mp3', 'wav', 'flac', 'ogg'])) {
                    // 🔊 Nếu là file âm thanh → hiện nút Nghe
                    echo '<a href="uploaded_music/' . htmlspecialchars($c['file_name']) . '" target="_blank"
                            class="text-primary underline text-sm">
                            <i class="fas fa-play mr-1"></i> Nghe sản phẩm
                          </a>';
                } elseif ($file_ext == 'mid' || $file_ext == 'midi') {
                    // 🎵 Nếu là file MIDI → hiện nút Tải về
                    echo '<a href="uploaded_music/' . htmlspecialchars($c['file_name']) . '" download
                            class="text-green-600 underline text-sm">
                            <i class="fas fa-download mr-1"></i> Tải file MIDI
                          </a>';
                } else {
                    // 📄 Trường hợp khác → hiện nút xem/tải chung
                    echo '<a href="uploaded_music/' . htmlspecialchars($c['file_name']) . '" target="_blank"
                            class="text-gray-600 underline text-sm">
                            <i class="fas fa-file mr-1"></i> Xem file
                          </a>';
                }
                ?>

            </div>

            <?php if ($c['status'] == 'completed'): ?>
              <div class="mt-6 flex flex-wrap gap-4">
                <button onclick="openEditModal('<?php echo $c['id']; ?>')"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                  <i class="fas fa-edit mr-1"></i> Gửi yêu cầu chỉnh sửa
                </button>

                <form method="post" onsubmit="return confirm('Xác nhận phê duyệt sản phẩm này?');">
                  <input type="hidden" name="submission_id" value="<?php echo $c['id']; ?>">
                  <button type="submit" name="approve_final"
                          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-check-circle mr-1"></i> Phê duyệt sản phẩm
                  </button>
                </form>
                <form method="post" onsubmit="return confirm('Xác nhận thanh toán sản phẩm này?');">
                    <input type="hidden" name="submission_id" value="<?php echo $c['id']; ?>">
                    <button type="submit" name="go_to_checkout"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-credit-card mr-1"></i> Thanh toán ngay
                    </button>
                </form>
                
              </div>
            <?php elseif ($c['status'] == 'approved'): ?>
              <p class="text-green-700 font-semibold mt-4">Bạn đã phê duyệt sản phẩm này.</p>
            <?php elseif ($c['status'] == 'pending_edit'): ?>
              <p class="text-yellow-600 font-semibold mt-4">Đang xử lý yêu cầu chỉnh sửa của bạn...</p>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      </div>
      
    <?php endif; ?>
  </div>
</section>

<!-- Modal gửi yêu cầu chỉnh sửa -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
  <div class="bg-white rounded-xl p-6 w-96 relative">
    <h3 class="text-xl font-semibold mb-4">Yêu cầu chỉnh sửa</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="submission_id" id="edit_submission_id">
      <textarea name="feedback" rows="3" placeholder="Mô tả chi tiết phần cần chỉnh sửa..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-3"></textarea>
      <label class="block text-sm font-semibold mb-1">File minh họa (tùy chọn)</label>
      <input type="file" name="edit_file" accept=".mp3,.wav,.midi,.flac,.txt"
             class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4">
      <div class="flex justify-end gap-3">
        <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Hủy</button>
        <button type="submit" name="request_edit" class="px-4 py-2 bg-primary text-white rounded-lg">
          Gửi yêu cầu
        </button>
      </div>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
function openEditModal(id){
  document.getElementById('edit_submission_id').value = id;
  document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal(){
  document.getElementById('editModal').classList.add('hidden');
}
</script>

</body>
</html>
