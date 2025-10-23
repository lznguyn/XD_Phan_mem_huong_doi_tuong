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

```
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="css/style.css">

<style>
    .experts {
        padding: 2rem;
        background: #fff;
    }
    .experts .box-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
        gap: 1.5rem;
    }
    .experts .box {
        background: #f8f8f8;
        border-radius: 1rem;
        text-align: center;
        padding: 1.5rem;
        transition: 0.3s;
    }
    .experts .box:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    .experts .box img {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 50%;
    }
    .experts .box .name {
        font-size: 1.8rem;
        margin: 0.5rem 0;
    }
    .experts .box .role {
        color: #666;
        font-size: 1.3rem;
    }
    .experts .box .price {
        font-weight: bold;
        color: #d43c3c;
        margin: 0.7rem 0;
    }
</style>
```

</head>

<body>

<?php include 'header.php'; ?>

<section class="home" style="background:linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)),url('images/expert_banner.jpg') center/cover no-repeat; height:60vh; display:flex; align-items:center; justify-content:center;">
    <div class="content" style="color:#fff; text-align:center;">
        <h3>ĐẶT CHUYÊN GIA ÂM NHẠC CỦA BẠN</h3>
        <p>Chọn chuyên gia phù hợp để được hỗ trợ ký âm, phối khí hoặc thu âm cùng đội ngũ chuyên nghiệp của MuTraPro.</p>
    </div>
</section>

<section class="experts">
    <h1 class="title">CHUYÊN GIA HÀNG ĐẦU</h1>

```
<div class="box-container">
    <?php
    $select_experts = mysqli_query($conn, "SELECT * FROM `experts`") or die('query failed');
    if (mysqli_num_rows($select_experts) > 0) {
        while ($fetch_expert = mysqli_fetch_assoc($select_experts)) {
    ?>
    <form action="" method="post" class="box">
        <img src="uploaded_img/<?php echo $fetch_expert['image']; ?>" alt="">
        <div class="name"><?php echo $fetch_expert['name']; ?></div>
        <div class="role"><?php echo $fetch_expert['role']; ?></div>
        <div class="price"><?php echo number_format($fetch_expert['price'], 0, ',', '.'); ?> VNĐ / giờ</div>
        <input type="hidden" name="expert_name" value="<?php echo $fetch_expert['name']; ?>">
        <input type="hidden" name="expert_role" value="<?php echo $fetch_expert['role']; ?>">
        <input type="hidden" name="expert_price" value="<?php echo $fetch_expert['price']; ?>">
        <input type="hidden" name="expert_image" value="<?php echo $fetch_expert['image']; ?>">
        <input type="submit" value="Đặt chuyên gia" name="book_expert" class="btn">
    </form>
    <?php
        }
    } else {
        echo '<p class="empty">Hiện chưa có chuyên gia nào được đăng!</p>';
    }
    ?>
</div>
```

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

<script>
<?php if (isset($_SESSION['cart_message'])): ?>
    alert("<?php echo $_SESSION['cart_message']; ?>");
    <?php unset($_SESSION['cart_message']); ?>
<?php endif; ?>
</script>

</body>
</html>
