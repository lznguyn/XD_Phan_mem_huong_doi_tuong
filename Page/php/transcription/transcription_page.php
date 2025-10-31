<?php
include '../user/config.php';
session_start();

// Kiểm tra đăng nhập
$expert_id = $_SESSION['transcription_id'] ?? null;
if (!$expert_id) {
    header('location:login.php');
    exit();
}

// Xử lý upload bản phối cuối cùng
$upload_message = "";
if (isset($_POST['upload_mix'])) {
    $request_id = mysqli_real_escape_string($conn, $_POST['request_id']);

    if (isset($_FILES['mix_file']) && $_FILES['mix_file']['error'] == 0) {
        $file_name = $_FILES['mix_file']['name'];
        $file_tmp = $_FILES['mix_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['mp3','wav','flac'];
        if (in_array($file_ext, $allowed_ext)) {
            $upload_dir = 'uploaded_mixes/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $new_name = uniqid('mix_', true) . '.' . $file_ext;
            move_uploaded_file($file_tmp, $upload_dir . $new_name);

            mysqli_query($conn, "UPDATE music_submissions 
                SET title='$new_name', status='completed' 
                WHERE id='$request_id' AND target_role='arrangement'") 
                or die('Lỗi cập nhật bản phối!');   

            $upload_message = "✅ Upload bản phối thành công!";
        } else {
            $upload_message = "Định dạng file không hợp lệ. Chỉ mp3, wav, flac.";
        }
    } else {
        $upload_message = "Vui lòng chọn file để upload.";
    }
}

// Lấy danh sách bài nhạc gán cho chuyên gia
$submissions = mysqli_query($conn, "
    SELECT ms.*, u.name AS customer_name 
    FROM music_submissions ms
    JOIN users u ON ms.user_id = u.id
    WHERE ms.target_role='transcription'
    ORDER BY ms.created_at DESC
") or die('Query failed');

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chuyên gia Hòa âm - Quản lý bản nhạc</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<?php include 'transcription_header.php'; ?>
<body class="bg-gray-50 mt-10">

<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-indigo-700 mb-6">Danh sách bản nhạc cần xử lý</h1>

    <?php if ($upload_message != ""): ?>
        <div class="mb-4 text-center text-green-700 font-semibold"><?php echo $upload_message; ?></div>
    <?php endif; ?>

    <div class="space-y-4">
        <?php while($s = mysqli_fetch_assoc($submissions)): ?>
            <div class="bg-white rounded-xl shadow p-4 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold"><?php echo htmlspecialchars($s['title']); ?></h3>
                    <p class="text-gray-600 text-sm mb-1">Khách hàng: <?php echo htmlspecialchars($s['customer_name']); ?></p>
                    <p class="text-sm text-gray-600 mb-1">
                        <i class="fas fa-calendar-alt mr-1"></i> <?php echo $s['created_at']; ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        Trạng thái: 
                        <?php 
                        $status = $s['status'];
                        $color = $status=='completed' ? 'bg-green-500' : ($status=='pending' ? 'bg-blue-500' : 'bg-yellow-500');
                        $label = $status=='completed' ? 'Hoàn tất' : ($status=='pending' ? 'Đang xử lý' : 'Chờ xác nhận');
                        ?>
                        <span class="px-2 py-1 text-white text-xs rounded <?php echo $color; ?>"><?php echo $label; ?></span>
                    </p>
                    <?php if($s['file_name']): ?>
                        <a href="uploaded_mixes/<?php echo $s['file_name']; ?>" target="_blank" class="text-indigo-600 text-sm underline mt-1 block">
                            <i class="fas fa-play mr-1"></i> Nghe bản phối
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Form upload bản phối -->
                <form method="POST" enctype="multipart/form-data" class="flex flex-col items-end gap-2">
                    <input type="hidden" name="request_id" value="<?php echo $s['id']; ?>">
                    <input type="file" name="mix_file" accept=".mp3,.wav,.flac" required>
                    <button type="submit" name="upload_mix" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">
                        Upload bản phối
                    </button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
