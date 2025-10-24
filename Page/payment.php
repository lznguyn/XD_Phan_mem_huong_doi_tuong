<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$show_success_modal = false;
// Xử lý đặt hàng
if (isset($_POST['order_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $number = $_POST['customer_phone'];
    $email = mysqli_real_escape_string($conn, $_POST['customer_email']);
    $method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $placed_on = date('d-M-Y');

    $cart_total = 0;
    $cart_products = [];

    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id='$user_id'") or die('query failed');

    if (mysqli_num_rows($cart_query) > 0) {
        while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ')';
            $cart_total += $cart_item['price'] * $cart_item['quantity'];
        }
    }

    $total_products = implode(', ', $cart_products);

    if ($cart_total == 0) {
        $_SESSION['cart_message'] = 'Giỏ hàng của bạn đang trống!';
    } else {
        $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name='$name' AND number='$number' AND email='$email' AND method='$method' AND total_products='$total_products' AND total_price='$cart_total'") or die('query failed');

        if (mysqli_num_rows($order_query) > 0) {
            $_SESSION['cart_message'] = 'Đơn hàng đã được đặt trước đó!';
        } else {
            mysqli_query($conn, "INSERT INTO `orders`(user_id,name,number,email,method,total_products,total_price,placed_on) VALUES('$user_id','$name','$number','$email','$method','$total_products','$cart_total','$placed_on')") or die('query failed');
            mysqli_query($conn, "DELETE FROM `cart` WHERE user_id='$user_id'") or die('query failed');
            $_SESSION['cart_message'] = 'Đơn hàng đã được đặt thành công!';
            $show_success_modal = true;
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
$cart_message = $_SESSION['cart_message'] ?? '';
unset($_SESSION['cart_message']);

$show_success_modal = $_SESSION['show_success_modal'] ?? false;
unset($_SESSION['show_success_modal']);
?>
<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Thanh toán - MuTraPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: "#1e40af",
              secondary: "#f59e0b",
              accent: "#10b981",
              danger: "#dc2626",
            },
          },
        },
      };
    </script>
  </head>
<body class="bg-gray-50">
    <!-- Header -->
<?php include 'header.php';?>
    <!-- Breadcrumb -->
    <!-- Payment Section -->
  <section class="max-w-7xl mx-auto px-4 py-8 grid lg:grid-cols-3 gap-8">
    <!-- Form Thanh toán -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
      <h2 class="text-2xl font-bold mb-4">Thông tin thanh toán</h2>
      <form method="post" class="space-y-4">
        <div>
          <label class="block mb-1 font-semibold">Họ và tên</label>
          <input type="text" name="customer_name" required class="w-full border rounded-lg px-3 py-2"/>
        </div>
        <div>
          <label class="block mb-1 font-semibold">Số điện thoại</label>
          <input type="tel" name="customer_phone" required class="w-full border rounded-lg px-3 py-2"/>
        </div>
        <div>
          <label class="block mb-1 font-semibold">Email</label>
          <input type="email" name="customer_email" required class="w-full border rounded-lg px-3 py-2"/>
        </div>
        <div>
          <label class="block mb-1 font-semibold">Phương thức thanh toán</label>
          <select name="payment_method" class="w-full border rounded-lg px-3 py-2">
            <option value="Thanh toán khi giao hàng">Thanh toán khi giao hàng</option>
            <option value="ATM">Chuyển khoản ngân hàng</option>
            <option value="Momo">Thanh toán qua Momo</option>
          </select>
        </div>
        <div>
          <label class="block mb-1 font-semibold">Địa chỉ</label>
          <input type="text" name="address" class="w-full border rounded-lg px-3 py-2" placeholder="Số nhà, đường, thành phố, quốc gia"/>
        </div>
        <button type="submit" name="order_btn" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
          <i class="fas fa-lock mr-2"></i>Thanh toán an toàn
        </button>
      </form>
    </div>

    <!-- Order Summary -->
  <!-- Chi tiết đơn hàng -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <h3 class="text-xl font-bold mb-4">Chi tiết đơn hàng</h3>
      <?php
      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id='$user_id'");
      if(mysqli_num_rows($select_cart) > 0){
        while($item = mysqli_fetch_assoc($select_cart)){
          $sub_total = $item['price'] * $item['quantity'];
          $grand_total += $sub_total;
          echo '<div class="flex justify-between mb-2">';
          echo '<span>'.$item['name'].' x '.$item['quantity'].'</span>';
          echo '<span>'.number_format($sub_total,0,',','.').' VNĐ</span>';
          echo '</div>';
        }
      } else {
        echo '<p class="text-red-500">Giỏ hàng của bạn đang trống!</p>';
      }
      ?>
      <div class="border-t mt-2 pt-2 font-bold flex justify-between">
        <span>Tổng cộng:</span>
        <span><?= number_format($grand_total,0,',','.'); ?> VNĐ</span>
      </div>
    </div>
  <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl scale-90 opacity-0">
      <h2 class="text-xl font-bold mb-2">Đặt hàng thành công!</h2>
      <p>Cảm ơn bạn đã đặt hàng. Đơn hàng sẽ được xử lý sớm nhất.</p>
     <button onclick="closeSuccessModal()" class="mt-4 bg-primary text-white py-2 px-4 rounded-lg">Đóng</button>
    </div>
  </div>
</section>
<?php include 'footer.php'; ?>

  <script>
  function showSuccessModal() {
    const modal = document.getElementById('successModal');
    const content = modal.querySelector('div');

    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.classList.add('flex');
      content.classList.remove('scale-90', 'opacity-0');
      content.classList.add('scale-100', 'opacity-100');
    }, 10);

    // Tự động đóng sau 5 giây
    setTimeout(closeSuccessModal, 5000);
  }

  function closeSuccessModal() {
    const modal = document.getElementById('successModal');
    const content = modal.querySelector('div');

    content.classList.add('scale-90', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    modal.classList.remove('flex');
    setTimeout(() => modal.classList.add('hidden'), 500);
  }

  // Hiển thị modal nếu đặt hàng thành công
  <?php if($show_success_modal): ?>
  showSuccessModal();
  <?php endif; ?>
  </script>

  <style>
  /* Scale và opacity chuyển mượt hơn */
  #successModal div {
    transition: transform 0.5s ease, opacity 0.5s ease;
  }
  </style>
</body>
</html>
