<?php
include '../user/config.php';
session_start();

$coordinator_id = $_SESSION['coordinator_id'] ?? null;
if (!$coordinator_id) {
    header('location:login.php');
    exit();
}

// --- Lấy giá trị bộ lọc từ GET ---
$filter_priority = $_GET['priority'] ?? '';
$filter_status = $_GET['status'] ?? '';

// --- Xây dựng câu truy vấn có điều kiện ---
$query = "SELECT b.*, u.name AS customer_name 
          FROM bookings b
          LEFT JOIN users u ON b.user_id = u.id
          WHERE 1";if ($filter_priority != '') {
    $query .= " AND priority = '" . mysqli_real_escape_string($conn, $filter_priority) . "'";
}
if ($filter_status != '') {
    $query .= " AND status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
}
$query .= " ORDER BY date DESC";

$result = mysqli_query($conn, $query) or die('Query failed' . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý yêu cầu dịch vụ</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<?php include 'coordinator_header.php'?>
<body class="bg-gray-50">
  <div class="max-w-6xl mx-auto py-8 px-4 mt-10">
    <h1 class="text-2xl font-bold mb-6">Quản lý yêu cầu dịch vụ</h1>

    <!-- Bộ lọc -->
    <form method="GET" class="mb-6 flex flex-wrap gap-3 items-end bg-white p-4 rounded-lg shadow">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Lọc theo ưu tiên</label>
        <select name="priority" class="border rounded-lg px-3 py-2 w-48">
          <option value="">Tất cả</option>
          <option value="Thấp" <?php if($filter_priority=='Thấp') echo 'selected'; ?>>Thấp</option>
          <option value="Trung bình" <?php if($filter_priority=='Trung bình') echo 'selected'; ?>>Trung bình</option>
          <option value="Cao" <?php if($filter_priority=='Cao') echo 'selected'; ?>>Cao</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Lọc theo trạng thái</label>
        <select name="status" class="border rounded-lg px-3 py-2 w-48">
          <option value="">Tất cả</option>
          <option value="pending" <?php if($filter_status=='pending') echo 'selected'; ?>>Chờ xác nhận</option>
          <option value="in_progress" <?php if($filter_status=='in_progress') echo 'selected'; ?>>Đang xử lý</option>
          <option value="completed" <?php if($filter_status=='completed') echo 'selected'; ?>>Hoàn tất</option>
        </select>
      </div>

      <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
        Áp dụng
      </button>

      <a href="service_requests.php" class="ml-2 text-gray-600 hover:underline">Đặt lại</a>
    </form>

    <!-- Bảng danh sách -->
    <table class="min-w-full bg-white rounded-lg shadow">
      <thead class="bg-gray-200">
        <tr>
          <th class="py-3 px-4 text-left">Mã</th>
          <th class="py-3 px-4 text-left">Khách hàng</th>
          <th class="py-3 px-4 text-left">Ngày tạo</th>
          <th class="py-3 px-4 text-left">Trạng thái</th>
          <th class="py-3 px-4 text-left">Ưu tiên</th>
          <th class="py-3 px-4 text-center">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($req = mysqli_fetch_assoc($result)): ?>
            <tr class="border-b hover:bg-gray-50">
              <td class="py-3 px-4"><?php echo $req['id']; ?></td>
              <td class="py-3 px-4"><?php echo $req['customer_name']; ?></td>
              <td class="py-3 px-4"><?php echo $req['date']; ?></td>
              <td class="py-3 px-4">
                <?php 
                  $status = $req['status'];
                  $color = $status == 'completed' ? 'bg-green-500' : ($status == 'in_progress' ? 'bg-blue-500' : 'bg-yellow-500');
                  $label = $status == 'completed' ? 'Hoàn tất' : ($status == 'in_progress' ? 'Đang xử lý' : 'Chờ xác nhận');
                ?>
                <span class="px-3 py-1 text-white text-sm rounded-full <?php echo $color; ?>">
                  <?php echo $label; ?>
                </span>
              </td>
              <td class="py-3 px-4">
                <form action="update_priority.php" method="POST" class="flex items-center space-x-2">
                  <input type="hidden" name="id" value="<?php echo $req['id']; ?>">
                  <select name="priority" class="border rounded px-2 py-1 text-sm">
                    <option value="Thấp" <?php if($req['priority']=='Thấp') echo 'selected'; ?>>Thấp</option>
                    <option value="Trung bình" <?php if($req['priority']=='Trung bình') echo 'selected'; ?>>Trung bình</option>
                    <option value="Cao" <?php if($req['priority']=='Cao') echo 'selected'; ?>>Cao</option>
                  </select>
                  <button type="submit" class="bg-indigo-500 text-white px-3 py-1 rounded text-sm">Lưu</button>
                </form>
              </td>
              <td class="py-3 px-4 text-center">
                <a href="service_detail.php?id=<?php echo $req['id']; ?>" class="text-blue-600 font-medium hover:underline">Chi tiết</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="py-6 text-center text-gray-500">Không có yêu cầu nào phù hợp.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
