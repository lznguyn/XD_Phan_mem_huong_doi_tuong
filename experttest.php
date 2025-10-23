<?php
include 'config.php';
session_start();

// Kiểm tra người dùng đã đăng nhập
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (isset($_POST['book_expert'])) {

    if ($user_id == null) {
        header('Location: login.php');
        exit();
    }

    $expert_name = mysqli_real_escape_string($conn, $_POST['expert_name']);
    $expert_price = mysqli_real_escape_string($conn, $_POST['expert_price']);
    $expert_image = mysqli_real_escape_string($conn, $_POST['expert_image']);
    $expert_role = mysqli_real_escape_string($conn, $_POST['expert_role']);

    // kiểm tra nếu đã thêm
    $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$expert_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($check_cart) > 0) {
        $_SESSION['cart_message'] = 'Chuyên gia này đã được thêm trước đó!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$expert_name', '$expert_price', 1, '$expert_image')") or die('query failed');
        $_SESSION['cart_message'] = 'Đã thêm chuyên gia vào yêu cầu của bạn!';
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt chuyên gia - MuTraPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#f59e0b',
                        accent: '#10b981'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Hero Section -->
    <section class="relative h-[400px] bg-gradient-to-r from-purple-900 to-blue-900 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        <img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="Expert Banner" class="absolute inset-0 w-full h-full object-cover">
        <div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                ĐẶT CHUYÊN GIA ÂM NHẠC CỦA BẠN
            </h2>
            <p class="text-lg sm:text-xl opacity-90 leading-relaxed">
                Chọn chuyên gia phù hợp để được hỗ trợ ký âm, phối khí hoặc thu âm cùng đội ngũ chuyên nghiệp của MuTraPro.
            </p>
        </div>
    </section>

    <!-- Experts Section -->
    <section class="py-16 sm:py-20 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                    CHUYÊN GIA HÀNG ĐẦU
                </h2>
                <div class="w-24 h-1 bg-primary mx-auto mb-6"></div>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Đội ngũ chuyên gia âm nhạc giàu kinh nghiệm, sẵn sàng biến ý tưởng của bạn thành hiện thực
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
                <?php
            $select_experts = mysqli_query($conn, "SELECT * FROM `experts`") or die('query failed');
            if(mysqli_num_rows($select_experts) > 0){
                while($fetch_expert = mysqli_fetch_assoc($select_experts)){
            ?>
            <form method="post" class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="relative mb-6">
                    <img src="uploaded_img/<?php echo $fetch_expert['image']; ?>" alt="<?php echo $fetch_expert['name']; ?>" class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-primary">
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-accent text-white px-3 py-1 rounded-full text-xs font-semibold">
                        <i class="fas fa-star mr-1"></i>4.9
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo $fetch_expert['name']; ?></h3>
                <p class="text-gray-600 mb-3 font-medium"><?php echo $fetch_expert['role']; ?></p>
                <p class="text-2xl font-bold text-primary mb-4"><?php echo number_format($fetch_expert['price'],0,',','.'); ?> VNĐ / giờ</p>
                <input type="hidden" name="expert_name" value="<?php echo $fetch_expert['name']; ?>">
                <input type="hidden" name="expert_role" value="<?php echo $fetch_expert['role']; ?>">
                <input type="hidden" name="expert_price" value="<?php echo $fetch_expert['price']; ?>">
                <input type="hidden" name="expert_image" value="<?php echo $fetch_expert['image']; ?>">
                <button type="submit" name="book_expert" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-md">
                    <i class="fas fa-user-plus mr-2"></i>Đặt chuyên gia
                </button>
            </form>
            <?php
                }
            } else {
                echo '<p class="text-center text-gray-500 col-span-full">Hiện chưa có chuyên gia nào được đăng!</p>';
            }
            ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-primary to-blue-800 text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6">
                Không tìm thấy chuyên gia phù hợp?
            </h2>
            <p class="text-lg sm:text-xl mb-8 opacity-90">
                Liên hệ với chúng tôi để được tư vấn và kết nối với chuyên gia phù hợp nhất cho dự án của bạn.
            </p>
            <a href="#" class="inline-block bg-white text-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors shadow-lg">
                <i class="fas fa-phone mr-2"></i>LIÊN HỆ TƯ VẤN
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
    <!-- Success/Error Message Modal -->
    <div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
            <div class="text-center">
                <i id="messageIcon" class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                <h3 id="messageTitle" class="text-lg font-semibold mb-2">Thành công!</h3>
                <p id="messageText" class="text-gray-600 mb-4">Đã thêm chuyên gia vào yêu cầu của bạn!</p>
                <button onclick="closeModal()" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    
<script>
    function showMessage(message, isSuccess = true) {
        const modal = document.getElementById('messageModal');
        const icon = document.getElementById('messageIcon');
        const title = document.getElementById('messageTitle');
        const text = document.getElementById('messageText');
        
        if (isSuccess) {
            icon.className = 'fas fa-check-circle text-4xl text-green-500 mb-4';
            title.textContent = 'Thành công!';
        } else {
            icon.className = 'fas fa-exclamation-circle text-4xl text-red-500 mb-4';
            title.textContent = 'Thông báo!';
        }
        
        text.textContent = message;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('messageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    <?php if(isset($_SESSION['cart_message'])): ?>
        alert("<?php echo $_SESSION['cart_message']; ?>");
        <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>
</script>

</body>
</html>