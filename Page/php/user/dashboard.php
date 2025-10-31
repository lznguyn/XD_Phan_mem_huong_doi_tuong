<?php
include 'config.php';
session_start();

// Kiểm tra người dùng đã đăng nhập 
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (isset($_POST['add_to_cart'])) {
    if (!$user_id) {
        header('Location: login.php');
        exit();
    }

    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
    $service_price = mysqli_real_escape_string($conn, $_POST['service_price']);
    $service_image = mysqli_real_escape_string($conn, $_POST['service_image']);
    $service_quantity = (int)$_POST['service_quantity'];

    $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name='$service_name' AND user_id='$user_id'") or die('query failed');
    if (mysqli_num_rows($check_cart) > 0) {
        $_SESSION['cart_message'] = 'Dịch vụ đã được thêm trước đó!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id','$service_name','$service_price','$service_quantity','$service_image')") or die('query failed');
        $_SESSION['cart_message'] = 'Dịch vụ đã được thêm vào yêu cầu của bạn!';
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
    <title>MuTraPro - Hệ thống ký âm và sản xuất âm nhạc theo yêu cầu</title>
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
    <section class="relative h-[600px] bg-gradient-to-r from-blue-900 to-purple-900 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="Music Studio" class="absolute inset-0 w-full h-full object-cover">
        <div class="relative z-10 text-center text-white px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <h2 class="text-3xl sm:text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                Chuyển ý tưởng âm nhạc của bạn thành hiện thực
            </h2>
            <p class="text-lg sm:text-xl lg:text-2xl mb-8 opacity-90 leading-relaxed">
                MuTraPro cung cấp dịch vụ ký âm, phối khí và thu âm chuyên nghiệp theo yêu cầu – từ file âm thanh đến bản nhạc hoàn chỉnh và bản thu chất lượng cao.
            </p>
            <a href="#" class="inline-block bg-white text-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors shadow-lg">
                TÌM HIỂU THÊM
            </a>
        </div>
    </section>

   
<!-- Services Section -->
<section class="py-16 sm:py-20 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 lg:mb-16">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">DỊCH VỤ ÂM NHẠC NỔI BẬT</h2>
            <div class="w-24 h-1 bg-primary mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <?php
            $select_services = mysqli_query($conn, "SELECT * FROM `services` LIMIT 6") or die('query failed');
            if (mysqli_num_rows($select_services) > 0) {
                while ($fetch_service = mysqli_fetch_assoc($select_services)) {
            ?>
            <form method="post" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col">
                <img src="uploaded_img/<?php echo $fetch_service['image']; ?>" alt="<?php echo $fetch_service['name']; ?>" class="w-full h-64 object-cover">
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo $fetch_service['name']; ?></h3>
                    <p class="text-2xl font-bold text-primary mb-4"><?php echo number_format($fetch_service['price'], 0, ',', '.'); ?> VNĐ</p>
                    <input type="number" min="1" name="service_quantity" value="1" class="w-20 px-2 py-1 border border-gray-300 rounded text-center mb-4">
                    <input type="hidden" name="service_name" value="<?php echo $fetch_service['name']; ?>">
                    <input type="hidden" name="service_price" value="<?php echo $fetch_service['price']; ?>">
                    <input type="hidden" name="service_image" value="<?php echo $fetch_service['image']; ?>">
                    <button type="submit" name="add_to_cart" class="mt-auto bg-primary text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Đặt dịch vụ
                    </button>
                </div>
            </form>
            <?php
                }
            } else {
                echo '<p class="text-center text-gray-500 col-span-3">Chưa có dịch vụ nào được đăng!</p>';
            }
            ?>
        </div>
        <div class="text-center mt-12">
            <a href="services.php" class="inline-block bg-secondary text-white px-8 py-3 rounded-lg font-semibold hover:bg-yellow-600 transition-colors">
                Xem thêm
            </a>
        </div>
    </div>
</section>

    <!-- About Section -->
    <section class="py-16 sm:py-20 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="order-2 lg:order-1">
                    <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Về MuTraPro" class="w-full h-96 object-cover rounded-xl shadow-lg">
                </div>
                <div class="order-1 lg:order-2">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                        VỀ MUTRAPRO
                    </h2>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        MuTraPro là nền tảng tích hợp giúp khách hàng yêu cầu ký âm, phối khí và sản xuất âm nhạc. Quy trình làm việc minh bạch, kết nối trực tiếp giữa khách hàng và đội ngũ chuyên gia: Ký âm, Phối khí, và Nghệ sĩ thu âm.
                    </p>
                    <a href="#" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        TÌM HIỂU CHI TIẾT
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA Section -->
    <section class="py-16 sm:py-20 lg:py-24 bg-gradient-to-r from-primary to-blue-800 text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6">
                BẠN CẦN DỊCH VỤ ÂM NHẠC CHUYÊN NGHIỆP?
            </h2>
            <p class="text-lg sm:text-xl mb-8 opacity-90 leading-relaxed">
                Liên hệ ngay với MuTraPro để được tư vấn và đặt dịch vụ ký âm, phối khí hoặc thu âm với các nghệ sĩ chuyên nghiệp trong phòng thu đạt chuẩn.
            </p>
            <a href="#" class="inline-block bg-white text-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors shadow-lg">
                LIÊN HỆ NGAY
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
     <!-- Cart message -->
    <?php if (isset($_SESSION['cart_message'])): ?>
    <script>
        alert("<?php echo $_SESSION['cart_message']; ?>");
    </script>
    <?php unset($_SESSION['cart_message']); endif; ?>       

    <script>    
        // Background slideshow simulation
        const heroImages = [
            'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
            'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80',
            'https://images.unsplash.com/photo-1516280440614-37939bbacd81?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'
        ];
        
        let currentImageIndex = 0;
        const heroSection = document.querySelector('section img');
        
        function changeHeroImage() {
            currentImageIndex = (currentImageIndex + 1) % heroImages.length;
            if (heroSection) {
                heroSection.src = heroImages[currentImageIndex];
            }
        }
        
        setInterval(changeHeroImage, 4000);
        
        // Cart message simulation
        function showCartMessage(message) {
            alert(message);
        }
    </script>

</body>
</html>