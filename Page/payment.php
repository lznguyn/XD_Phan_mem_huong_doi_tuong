<?php
include 'config.php';
session_start();

// Kiểm tra người dùng đã đăng nhập
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}
if (isset($_POST['update_cart'])) {
    foreach($_POST['quantities'] as $cart_id => $qty) {
        $qty = (int)$qty;
        if ($qty > 0) {
            mysqli_query($conn, "UPDATE `cart` SET quantity = '$qty' WHERE id = '$cart_id' AND user_id = '$user_id'");
        }
    }
    $_SESSION['cart_message'] = "Cập nhật giỏ hàng thành công!";
    header('Location: checkout.php');
    exit();
}
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id' AND user_id = '$user_id'");
    $_SESSION['cart_message'] = "Dịch vụ đã được xóa khỏi giỏ hàng!";
    header('Location: checkout.php');
    exit();
}
// Xử lý thanh toán
if (isset($_POST['checkout'])) {
    $cart_items = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($cart_items) > 0) {
        $total_amount = 0;
        while ($item = mysqli_fetch_assoc($cart_items)) {
            $total_amount += $item['price'] * $item['quantity'];
            // Lưu vào bảng transactions
            mysqli_query($conn, "INSERT INTO `transactions`(user_id, service_name, price, quantity, image, transaction_date) 
            VALUES('$user_id', '{$item['name']}', '{$item['price']}', '{$item['quantity']}', '{$item['image']}', NOW())") or die('query failed');
        }
        // Xóa giỏ hàng sau khi thanh toán
        mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        $_SESSION['checkout_message'] = "Thanh toán thành công! Tổng: ".number_format($total_amount,0,',','.')." VNĐ";
        header('Location: checkout.php');
        exit();
    } else {
        $_SESSION['checkout_message'] = "Giỏ hàng trống!";
        header('Location: checkout.php');
        exit();
    }
}

// Lấy danh sách giỏ hàng
$cart_items = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');

// Lấy lịch sử giao dịch
$transactions = mysqli_query($conn, "SELECT * FROM `transactions` WHERE user_id = '$user_id' ORDER BY transaction_date DESC") or die('query failed');

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MuTraPro - Giỏ hàng & Lịch sử</title>
<link rel="stylesheet" href="css/style.css">
<style>
.checkout-container { max-width: 900px; margin: 2rem auto; padding: 1rem; }
.cart-item, .transaction-item { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; }
.cart-item img, .transaction-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; }
.qty-input { width: 60px; padding: 0.3rem; text-align: center; }
.btn { padding: 0.5rem 1rem; background: #000; color: #fff; border: none; cursor: pointer; border-radius: 5px; margin-left: 0.5rem; }
.empty { text-align: center; padding: 2rem 0; color: #555; }
.actions { display: flex; align-items: center; }
.total { font-weight: bold; text-align: right; margin-top: 1rem; }
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="checkout-container">
    <h2>Giỏ hàng của bạn</h2>

    <?php if(mysqli_num_rows($cart_items) > 0): ?>
    <form method="post">
        <?php $total=0; while($item = mysqli_fetch_assoc($cart_items)):
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
        <div class="cart-item">
            <img src="uploaded_img/<?php echo $item['image']; ?>" alt="">
            <div>
                <div><?php echo $item['name']; ?></div>
                <div><?php echo number_format($item['price'],0,',','.'); ?> VNĐ</div>
            </div>
            <div class="actions">
                <input type="number" min="1" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" class="qty-input">
                <a href="?remove=<?php echo $item['id']; ?>" class="btn" style="background:#e74c3c;">Xóa</a>
            </div>
            <div><?php echo number_format($subtotal,0,',','.'); ?> VNĐ</div>
        </div>
        <?php endwhile; ?>
        <div class="total">Tổng: <?php echo number_format($total,0,',','.'); ?> VNĐ</div>
        <input type="submit" name="update_cart" value="Cập nhật giỏ hàng" class="btn">
        <input type="submit" name="checkout" value="Thanh toán ngay" class="btn">
    </form>
    <?php else: ?>
        <p class="empty">Giỏ hàng trống!</p>
    <?php endif; ?>

    <h2 style="margin-top:3rem">Lịch sử giao dịch</h2>
    <?php if(mysqli_num_rows($transactions) > 0): ?>
        <?php while($tran = mysqli_fetch_assoc($transactions)): ?>
        <div class="transaction-item">
            <img src="uploaded_img/<?php echo $tran['image']; ?>" alt="">
            <div>
                <div><?php echo $tran['service_name']; ?></div>
                <div><?php echo number_format($tran['price'],0,',','.'); ?> VNĐ x <?php echo $tran['quantity']; ?></div>
            </div>
            <div><?php echo date('d/m/Y H:i', strtotime($tran['transaction_date'])); ?></div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="empty">Chưa có giao dịch nào!</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<?php if (isset($_SESSION['cart_message'])): ?>
<script>alert("<?php echo $_SESSION['cart_message']; ?>");</script>
<?php unset($_SESSION['cart_message']); endif; ?>
<?php if (isset($_SESSION['checkout_message'])): ?>
<script>alert("<?php echo $_SESSION['checkout_message']; ?>");</script>
<?php unset($_SESSION['checkout_message']); endif; ?>

</body>
</html>

