<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if(!$admin_id){
   header('location:login.php');
   exit();
}
$message = [];

// ===== THÊM CHUYÊN GIA =====
if(isset($_POST['add_expert'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;

    $select_expert_email = mysqli_query($conn, "SELECT email FROM `experts` WHERE name = '$name'") or die('query failed');
    if(mysqli_num_rows($select_expert_email) > 0){
        $message[] = 'Email chuyên gia đã tồn tại!';
    } else {
        $add_expert_query = mysqli_query($conn, "INSERT INTO `experts`(name, role, price, image) VALUES('$name', '$role', '$price', '$image')") or die('query failed');
        if($add_expert_query){
            if($image_size > 2000000){
                $message[] = 'Kích thước ảnh quá lớn!';
            } else {
                move_uploaded_file($image_tmp_name, $image_folder);
                $message[] = 'Thêm chuyên gia thành công!';
            }
        } else {
            $message[] = 'Không thể thêm chuyên gia!';
        }
    }
}
// ===== XÓA DỊCH VỤ =====
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_image_query = mysqli_query($conn, "SELECT image FROM `experts` WHERE id = '$delete_id'") or die('query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   if(isset($fetch_delete_image['image'])){
      @unlink('uploaded_img/'.$fetch_delete_image['image']);
   }
   mysqli_query($conn, "DELETE FROM `experts` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_expert.php');
   exit();
}

// ===== CẬP NHẬT DỊCH VỤ =====
if(isset($_POST['update_product'])){
    $update_p_id = $_POST['update_p_id'];
    $update_name = $_POST['update_name'];
    $update_price = $_POST['update_price'];
    $update_role = $_POST['update_role'];


   mysqli_query($conn, "UPDATE `experts` SET name = '$update_name', price = '$update_price', role = '$update_role' WHERE id = '$update_p_id'") or die('query failed');

   $update_image = $_FILES['update_image']['name'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_folder = 'uploaded_img/'.$update_image;
   $update_old_image = $_POST['update_old_image'];

   if(!empty($update_image)){
      if($update_image_size > 2000000){
         $message[] = 'Kích thước ảnh quá lớn!';
      }else{
         mysqli_query($conn, "UPDATE `experts` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
         move_uploaded_file($update_image_tmp_name, $update_folder);
         @unlink('uploaded_img/'.$update_old_image);
      }
   }

   header('location:admin_expert.php');
   exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý dịch vụ - MuTraPro Admin</title>
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
                        danger: '#dc2626',
                        success: '#059669',
                        warning: '#d97706'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    
    <?php include 'admin_header.php'; ?>

    <!-- Thông báo -->
    <?php if(!empty($message)): ?>
        <div id="messageContainer" class="fixed top-4 right-4 z-50 space-y-2">
            <?php foreach($message as $msg): ?>
                <div class="bg-white border-l-4 border-primary shadow-lg rounded-lg p-4 max-w-sm transform transition-all duration-300 translate-x-full opacity-0 message-item">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-primary mr-3"></i>
                            <span class="text-gray-800"><?php echo $msg; ?></span>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="min-h-screen pt-20">
        <div class="bg-gradient-to-r from-primary to-blue-600 text-white">
            <div class="max-w-7xl mx-auto px-4 py-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Quản lý chuyên gia âm nhạc</h1>
                    <p class="text-blue-100">Thêm, chỉnh sửa và quản lý các chuyên gia của MuTraPro</p>
                </div>
                <div class="hidden md:block bg-white bg-opacity-20 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold"><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM experts")); ?></div>
                    <div class="text-sm">Chuyên gia</div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Form thêm chuyên gia -->
            <div class="bg-white rounded-2xl shadow-sm border p-6 mb-8">
                <div class="flex items-center mb-6">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-xl mr-4">
                        <i class="fas fa-user-plus text-primary text-xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Thêm chuyên gia mới</h2>
                </div>

                <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tên chuyên gia</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chuyên môn</label>
                            <input type="text" name="specialty" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh</label>
                            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" required class="w-full px-4 py-3 border border-gray-300 rounded-xl file:bg-primary file:text-white file:rounded-lg file:cursor-pointer hover:file:bg-blue-700">
                            <p class="text-sm text-gray-500 mt-2">Chấp nhận: JPG, JPEG, PNG. Tối đa 2MB</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả chuyên gia</label>
                        <textarea name="description" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="add_expert" class="bg-primary text-white px-8 py-3 rounded-xl hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>Thêm chuyên gia
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danh sách chuyên gia -->
            <div class="bg-white rounded-2xl shadow-sm border p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center">
                        <div class="bg-accent bg-opacity-10 p-3 rounded-xl mr-4">
                            <i class="fas fa-user text-accent text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Danh sách chuyên gia</h2>
                    </div>
                    <div class="text-sm text-gray-500">
                        <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM experts")); ?> chuyên gia
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php
                    $select_experts = mysqli_query($conn, "SELECT * FROM `experts`") or die('query failed');
                    if(mysqli_num_rows($select_experts) > 0){
                        while($fetch_experts = mysqli_fetch_assoc($select_experts)){
                    ?>
                    <div class="bg-gray-50 rounded-2xl p-4 hover:shadow-lg transition-all">
                        <div class="relative mb-4">
                            <img src="uploaded_img/<?php echo $fetch_experts['image']; ?>" alt="<?php echo $fetch_experts['name']; ?>" class="w-full h-48 object-cover rounded-xl">
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2"><?php echo $fetch_experts['name']; ?></h3>
                        <span class="text-sm text-gray-500 mb-2 block"><?php echo $fetch_experts['price']; ?></span>
                        <p class="text-gray-600 text-sm mb-2"><?php echo $fetch_experts['role']; ?></p>
                        <div class="flex space-x-2">
                            <a href="admin_expert_edit.php?id=<?php echo $fetch_experts['id']; ?>" class="flex-1 bg-warning text-white py-2 rounded-lg hover:bg-yellow-600 text-center text-sm font-medium"><i class="fas fa-edit mr-1"></i>Sửa</a>
                            <a href="admin_expert.php?delete=<?php echo $fetch_experts['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa chuyên gia này?');" class="flex-1 bg-danger text-white py-2 rounded-lg hover:bg-red-700 text-center text-sm font-medium"><i class="fas fa-trash mr-1"></i>Xóa</a>
                        </div>
                    </div>
                    <?php } } else { ?>
                        <div class="col-span-full text-center py-12 text-gray-500">Chưa có chuyên gia nào</div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const messages = document.querySelectorAll('.message-item');
        messages.forEach((m, i) => {
            setTimeout(() => {
                m.classList.remove('translate-x-full', 'opacity-0');
                m.classList.add('translate-x-0', 'opacity-100');
            }, i * 200);
            setTimeout(() => {
                m.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => m.remove(), 300);
            }, 5000 + i * 200);
        });
    });
    </script>
</body>
</html>