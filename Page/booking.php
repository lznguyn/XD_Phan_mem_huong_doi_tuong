<?php
include 'config.php';
session_start();

// Kiểm tra người dùng đã đăng nhập
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Xử lý đặt lịch
if (isset($_POST['book_session'])) {
    if ($user_id == null) {
        header('Location: login.php');
        exit();
    }

    $expert_id = mysqli_real_escape_string($conn, $_POST['expert_id']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $time_slot = mysqli_real_escape_string($conn, $_POST['time_slot']);

    // Kiểm tra trùng lịch
    $check_booking = mysqli_query($conn, "
        SELECT * FROM `bookings` 
        WHERE expert_id='$expert_id' AND date='$booking_date' AND time_slot='$time_slot'
    ") or die('query failed');

    if (mysqli_num_rows($check_booking) > 0) {
        $_SESSION['booking_message'] = 'Khung giờ này đã có người đặt!';
    } else {
        mysqli_query($conn, "
            INSERT INTO `bookings`(user_id, expert_id, date, time_slot)
            VALUES ('$user_id', '$expert_id', '$booking_date', '$time_slot')
        ") or die('query failed');
        $_SESSION['booking_message'] = 'Đặt lịch thành công!';
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
    <title>Đặt lịch thu âm trực tiếp - MuTraPro</title>

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
        .experts .box label {
            display: block;
            margin: 0.5rem 0 0.2rem 0;
            font-weight: bold;
        }
        .experts .box input[type="date"],
        .experts .box select {
            width: 90%;
            padding: 0.5rem;
            margin-bottom: 0.7rem;
            border-radius: 0.5rem;
            border: 1px solid #ccc;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #d43c3c;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #b73232;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="home" style="background:linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)),url('images/expert_banner.jpg') center/cover no-repeat; height:60vh; display:flex; align-items:center; justify-content:center;">
    <div class="content" style="color:#fff; text-align:center;">
        <h3>ĐẶT LỊCH THU ÂM TRỰC TIẾP</h3>
        <p>Chọn chuyên gia và khung giờ phù hợp để được hỗ trợ thu âm cùng đội ngũ chuyên nghiệp của MuTraPro.</p>
    </div>
</section>

<section class="experts">
    <h1 class="title">CHUYÊN GIA HÀNG ĐẦU</h1>

    <div class="box-container">
        <?php
        $select_experts = mysqli_query($conn, "SELECT * FROM `experts`") or die('query failed');
        if (mysqli_num_rows($select_experts) > 0) {
            while ($expert = mysqli_fetch_assoc($select_experts)) {
        ?>
        <form action="" method="post" class="box">
            <img src="uploaded_img/<?php echo $expert['image']; ?>" alt="">
            <div class="name"><?php echo $expert['name']; ?></div>
            <div class="role"><?php echo $expert['role']; ?></div>
            <div class="price"><?php echo number_format($expert['price'], 0, ',', '.'); ?> VNĐ / giờ</div>

            <input type="hidden" name="expert_id" value="<?php echo $expert['id']; ?>">

            <label>Chọn ngày:</label>
            <input type="date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">

            <label>Chọn khung giờ:</label>
            <select name="time_slot" required>
                <option value="09:00-10:00">09:00-10:00</option>
                <option value="10:00-11:00">10:00-11:00</option>
                <option value="14:00-15:00">14:00-15:00</option>
                <option value="15:00-16:00">15:00-16:00</option>
            </select>

            <input type="submit" name="book_session" value="Đặt lịch" class="btn">
        </form>
        <?php
            }
        } else {
            echo '<p class="empty">Hiện chưa có chuyên gia nào!</p>';
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

<script>
<?php if (isset($_SESSION['booking_message'])): ?>
    alert("<?php echo $_SESSION['booking_message']; ?>");
    <?php unset($_SESSION['booking_message']); ?>
<?php endif; ?>
</script>

</body>
</html>
