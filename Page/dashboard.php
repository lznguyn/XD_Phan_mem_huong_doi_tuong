<?php
include 'config.php';
session_start();

// Kiểm tra người dùng đã đăng nhập
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (isset($_POST['add_to_cart'])) {

    if ($user_id == null) {
        header('Location: login.php');
        exit();
    }

    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
    $service_price = mysqli_real_escape_string($conn, $_POST['service_price']);
    $service_image = mysqli_real_escape_string($conn, $_POST['service_image']);
    $service_quantity = (int)$_POST['service_quantity'];

    $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$service_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($check_cart) > 0) {
        $_SESSION['cart_message'] = 'Dịch vụ đã được thêm trước đó!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$service_name', '$service_price', '$service_quantity', '$service_image')") or die('query failed');
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

```
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">

<style>
    .home {
        min-height: 81vh;
        background: linear-gradient(rgba(0,0,0,.5), rgba(0,0,0,.5)), url('images/mutrapro_banner1.jpg') center/cover no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-image 1s ease-in-out;
    }
    .services .box-container .box .image {
        height: 22rem;
        width: 22rem;
    }
</style>
```

</head>

<body>

<?php include 'header.php'; ?>

<section class="home">
    <div class="content">
        <h3>Chuyển ý tưởng âm nhạc của bạn thành hiện thực</h3>
        <p>MuTraPro cung cấp dịch vụ ký âm, phối khí và thu âm chuyên nghiệp theo yêu cầu – từ file âm thanh đến bản nhạc hoàn chỉnh và bản thu chất lượng cao.</p>
        <a href="about.php" class="white-btn">TÌM HIỂU THÊM</a>
    </div>
</section>

<section class="services">
    <h1 class="title">DỊCH VỤ ÂM NHẠC NỔI BẬT</h1>

```
<div class="box-container">
    <?php
    $select_services = mysqli_query($conn, "SELECT * FROM `services` LIMIT 6") or die('query failed');
    if (mysqli_num_rows($select_services) > 0) {
        while ($fetch_service = mysqli_fetch_assoc($select_services)) {
    ?>
    <form action="" method="post" class="box">
        <img class="image" src="uploaded_img/<?php echo $fetch_service['image']; ?>" alt="">
        <div class="name"><?php echo $fetch_service['name']; ?></div>
        <div class="price"><?php echo number_format($fetch_service['price'], 0, ',', '.'); ?> VNĐ</div>
        <input type="number" min="1" name="service_quantity" value="1" class="qty">
        <input type="hidden" name="service_name" value="<?php echo $fetch_service['name']; ?>">
        <input type="hidden" name="service_price" value="<?php echo $fetch_service['price']; ?>">
        <input type="hidden" name="service_image" value="<?php echo $fetch_service['image']; ?>">
        <input type="submit" value="Đặt dịch vụ" name="add_to_cart" class="btn">
    </form>
    <?php
        }
    } else {
        echo '<p class="empty">Chưa có dịch vụ nào được đăng!</p>';
    }
    ?>
</div>

<div class="load-more" style="margin-top: 2rem; text-align:center">
    <a href="services.php" class="option-btn">Xem thêm</a>
</div>
```

</section>

<section class="about">
    <div class="flex">
        <div class="image">
            <img src="images/about_mutrapro.jpg" alt="">
        </div>
        <div class="content">
            <h3>VỀ MUTRAPRO</h3>
            <p>MuTraPro là nền tảng tích hợp giúp khách hàng yêu cầu ký âm, phối khí và sản xuất âm nhạc. Quy trình làm việc minh bạch, kết nối trực tiếp giữa khách hàng và đội ngũ chuyên gia: Ký âm, Phối khí, và Nghệ sĩ thu âm.</p>
            <a href="about.php" class="btn">TÌM HIỂU CHI TIẾT</a>
        </div>
    </div>
</section>

<section class="home-contact">
    <div class="content">
        <h3>BẠN CẦN DỊCH VỤ ÂM NHẠC CHUYÊN NGHIỆP?</h3>
        <p>Liên hệ ngay với MuTraPro để được tư vấn và đặt dịch vụ ký âm, phối khí hoặc thu âm với các nghệ sĩ chuyên nghiệp trong phòng thu đạt chuẩn.</p>
        <a href="contact.php" class="white-btn">LIÊN HỆ NGAY</a>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

<script>
<?php if (isset($_SESSION['cart_message'])): ?>
    alert("<?php echo $_SESSION['cart_message']; ?>");
    <?php unset($_SESSION['cart_message']); ?>
<?php endif; ?>
</script>

<script>
    const backgroundImages = [
        'images/mutrapro_banner1.jpg',
        'images/mutrapro_banner2.jpg',
        'images/mutrapro_banner3.jpg'
    ];
    let currentImageIndex = 0;
    const homeSection = document.querySelector('.home');
    function changeBackgroundImage() {
        currentImageIndex = (currentImageIndex + 1) % backgroundImages.length;
        homeSection.style.backgroundImage = `linear-gradient(rgba(0,0,0,.5), rgba(0,0,0,.5)), url('${backgroundImages[currentImageIndex]}')`;
    }
    setInterval(changeBackgroundImage, 4000);
</script>

</body>
</html>
