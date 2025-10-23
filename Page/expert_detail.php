<?php
include 'config.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!isset($_GET['id'])) {
    header('Location: book_expert.php');
    exit();
}

$expert_id = intval($_GET['id']);
$select_expert = mysqli_query($conn, "SELECT * FROM `experts` WHERE id = $expert_id") or die('query failed');
if (mysqli_num_rows($select_expert) == 0) {
    echo "<p>Chuyên gia không tồn tại!</p>";
    exit();
}
$expert = mysqli_fetch_assoc($select_expert);

if (isset($_POST['book_expert'])) {
    if ($user_id == null) {
        header('Location: login.php');
        exit();
    }
    $expert_name = mysqli_real_escape_string($conn, $_POST['expert_name']);
    $expert_price = mysqli_real_escape_string($conn, $_POST['expert_price']);
    $expert_image = mysqli_real_escape_string($conn, $_POST['expert_image']);

    $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$expert_name' AND user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($check_cart) > 0) {
        $_SESSION['cart_message'] = 'Chuyên gia này đã có trong giỏ!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$expert_name', '$expert_price', 1, '$expert_image')") or die('query failed');
        $_SESSION['cart_message'] = 'Đã thêm chuyên gia vào giỏ của bạn!';
    }
    header('Location: expert_detail.php?id=' . $expert_id);
    exit();
}
?>

<!DOCTYPE html>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $expert['name']; ?> - Chuyên Gia MuTraPro</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .expert-detail {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            padding: 3rem;
            background: #fff;
        }
        .expert-detail img {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .expert-info {
            flex: 1;
            min-width: 300px;
        }
        .expert-info h2 {
            font-size: 2.4rem;
            margin-bottom: .5rem;
        }
        .expert-info .role {
            color: #666;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }
        .expert-info .price {
            color: #d43c3c;
            font-weight: bold;
            margin-bottom: 1.2rem;
        }
        .expert-info .description {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .reviews {
            padding: 2rem 3rem;
            background: #f9f9f9;
        }
        .reviews h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        .reviews .review {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #ddd;
            padding-bottom: 1rem;
        }
        .video-demo {
            padding: 2rem 3rem;
        }
        iframe {
            width: 100%;
            height: 400px;
            border-radius: 1rem;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="expert-detail">
    <img src="uploaded_img/<?php echo $expert['image']; ?>" alt="<?php echo $expert['name']; ?>">
    <div class="expert-info">
        <h2><?php echo $expert['name']; ?></h2>
        <div class="role"><?php echo $expert['role']; ?></div>
        <div class="price"><?php echo number_format($expert['price'], 0, ',', '.'); ?> VNĐ / giờ</div>
        <div class="description"><?php echo nl2br($expert['description']); ?></div>

```
    <form method="post">
        <input type="hidden" name="expert_name" value="<?php echo $expert['name']; ?>">
        <input type="hidden" name="expert_price" value="<?php echo $expert['price']; ?>">
        <input type="hidden" name="expert_image" value="<?php echo $expert['image']; ?>">
        <input type="submit" value="Đặt chuyên gia ngay" name="book_expert" class="btn">
    </form>
</div>
```

</section>

<?php if (!empty($expert['video_link'])): ?>

<section class="video-demo">
    <h2 style="margin-bottom:1rem;">Video demo</h2>
    <iframe src="<?php echo $expert['video_link']; ?>" allowfullscreen></iframe>
</section>
<?php endif; ?>

<section class="reviews">
    <h3>Đánh giá từ khách hàng</h3>
    <?php
    $reviews = mysqli_query($conn, "SELECT * FROM `reviews` WHERE expert_id = $expert_id") or die('query failed');
    if (mysqli_num_rows($reviews) > 0) {
        while ($rev = mysqli_fetch_assoc($reviews)) {
            echo '<div class="review"><strong>' . htmlspecialchars($rev['customer_name']) . ':</strong><br>' . htmlspecialchars($rev['content']) . '</div>';
        }
    } else {
        echo '<p>Chưa có đánh giá nào cho chuyên gia này.</p>';
    }
    ?>
</section>

<?php include 'footer.php'; ?>

<script>
<?php if (isset($_SESSION['cart_message'])): ?>
    alert("<?php echo $_SESSION['cart_message']; ?>");
    <?php unset($_SESSION['cart_message']); ?>
<?php endif; ?>
</script>

</body>
</html>
