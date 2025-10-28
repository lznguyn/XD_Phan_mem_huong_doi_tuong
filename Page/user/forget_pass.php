<?php
include 'config.php';

$message = '';
$step = 1; // Biến để điều khiển giao diện (1: Quên mật khẩu, 2: Đặt lại mật khẩu)

if (isset($_POST['show_encrypted_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Kiểm tra email trong cơ sở dữ liệu
    $query = mysqli_query($conn, "SELECT * FROM `users` WHERE email='$email'") or die('Query failed');

    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $encrypted_password = $row['password']; // Lấy mật khẩu mã hóa từ DB
        $step = 2; // Chuyển sang bước đặt lại mật khẩu
    } else {
        $message = "Email không tồn tại trong cơ sở dữ liệu.";
    }
}

if (isset($_POST['reset_password'])) {
    $encrypted_password = mysqli_real_escape_string($conn, $_POST['encrypted_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Kiểm tra mật khẩu mã hóa
    $query = mysqli_query($conn, "SELECT * FROM `users` WHERE password='$encrypted_password'") or die('Query failed');
    if (mysqli_num_rows($query) > 0) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE `users` SET password='$hashed_password' WHERE password='$encrypted_password'") or die('Query failed');
            $message = "Mật khẩu đã được đặt lại thành công.";
            $step = 1; // Quay lại bước nhập email
        } else {
            $message = "Mật khẩu mới không khớp.";
            $step = 2; // Quay lại bước đặt lại mật khẩu
        }
    } else {
        $message = "Mật khẩu mã hóa không hợp lệ.";
        $step = 2; // Quay lại bước đặt lại mật khẩu
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - MuTraPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 flex items-center justify-center p-4">
    
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black bg-opacity-30"></div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white bg-opacity-20 rounded-full mb-4 backdrop-blur-sm">
                <i class="fas fa-music text-2xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">MuTraPro</h1>
            <p class="text-blue-200">Khôi phục tài khoản của bạn</p>
        </div>
        
        <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white border-opacity-20">

            <?php if ($step == 1): ?>
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-500 bg-opacity-20 rounded-full mb-4">
                        <i class="fas fa-key text-blue-300 text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Quên mật khẩu?</h3>
                    <p class="text-blue-200 text-sm">Nhập email để lấy lại mật khẩu của bạn</p>
                </div>

                <form action="" method="post" class="space-y-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-blue-300"></i>
                        </div>
                        <input type="email" 
                               name="email" 
                               placeholder="Nhập địa chỉ email của bạn" 
                               required 
                               class="w-full pl-12 pr-4 py-4 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white placeholder-blue-200 focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all">
                    </div>
                    <button type="submit" 
                            name="show_encrypted_password" 
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 rounded-xl font-semibold text-lg hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105">
                        <i class="fas fa-search mr-2"></i>Kiểm tra email
                    </button>
                </form>

            <?php elseif ($step == 2): ?>
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-500 bg-opacity-20 rounded-full mb-4">
                        <i class="fas fa-shield-alt text-green-300 text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Đặt lại mật khẩu</h3>
                    <p class="text-blue-200 text-sm">Nhập mật khẩu mới để hoàn tất quá trình khôi phục</p>
                </div>

                <div class="bg-yellow-500 bg-opacity-10 border border-yellow-400 border-opacity-30 rounded-xl p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                        <div>
                            <h4 class="text-yellow-200 font-semibold mb-2">Mật khẩu mã hóa của bạn:</h4>
                            <div class="bg-black bg-opacity-30 rounded-lg p-3 font-mono text-xs text-yellow-100 break-all">
                                <?php echo htmlspecialchars($encrypted_password); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="" method="post" class="space-y-6">
                    <input type="hidden" name="encrypted_password" value="<?php echo htmlspecialchars($encrypted_password); ?>">

                    <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required 
                           class="w-full px-4 py-4 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white placeholder-blue-200 focus:ring-2 focus:ring-blue-400">
                    <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required 
                           class="w-full px-4 py-4 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-xl text-white placeholder-blue-200 focus:ring-2 focus:ring-blue-400">
                    <button type="submit" name="reset_password"
                            class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-4 rounded-xl font-semibold text-lg hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105">
                        <i class="fas fa-shield-alt mr-2"></i>Đặt lại mật khẩu
                    </button>
                </form>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="mt-6 p-4 rounded-xl <?php echo (strpos($message, 'thành công') !== false) ? 'bg-green-500 bg-opacity-20 border border-green-400 border-opacity-30' : 'bg-red-500 bg-opacity-20 border border-red-400 border-opacity-30'; ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fas <?php echo (strpos($message, 'thành công') !== false) ? 'fa-check-circle text-green-300' : 'fa-exclamation-circle text-red-300'; ?>"></i>
                        <span class="<?php echo (strpos($message, 'thành công') !== false) ? 'text-green-200' : 'text-red-200'; ?> text-sm">
                            <?php echo htmlspecialchars($message); ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-8 pt-6 border-t border-white border-opacity-20 space-y-4">
                <a href="login.php" class="block text-center w-full py-3 bg-white bg-opacity-10 text-white rounded-xl font-semibold border border-white border-opacity-20 hover:bg-opacity-20 transition-all">
                    <i class="fas fa-sign-in-alt mr-2"></i>Quay lại đăng nhập
                </a>
                <div class="text-center">
                    <span class="text-blue-200 text-sm">Chưa có tài khoản? </span>
                    <a href="register.php" class="text-white font-semibold hover:text-blue-300 transition-colors hover:underline">
                        Đăng ký ngay
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-8 text-blue-200 text-sm">
            <p>&copy; 2024 MuTraPro. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>

    <?php if (strpos($message, 'thành công') !== false): ?>
    <script>
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 3000);
    </script>
    <?php endif; ?>
</body>
</html>
