<?php
include 'config.php';

session_start();

$message = []; // Khởi tạo biến $message để tránh lỗi

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');

    if (mysqli_num_rows($select_users) > 0) {
        $row = mysqli_fetch_assoc($select_users);

        if (password_verify($pass, $row['password'])) {
            if ($row['user_type'] == 'admin') {
                $_SESSION['admin_name'] = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_id'] = $row['id'];
                header('Location: admin_page.php');
                exit();
            } elseif ($row['user_type'] == 'user') {
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_id'] = $row['id'];
                header('Location: home.php');
                exit();
            }
        } else {
            $message[] = 'Thông tin tài khoản hoặc mật khẩu không đúng!';
        }
    } else {
        $message[] = 'Thông tin tài khoản hoặc mật khẩu không đúng!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: darkcyan;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-left: -50px;
        }

        .form-container h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .box {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 10px;
            background: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background: #4cae4c;
        }

        .btn-secondary {
            background: #007bff;
        }

        .btn-secondary:hover {
            background: #0056b3;
        }

        .message {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: #f2dede;
            color: #a94442;
            padding: 10px;
            margin: 0;
            border: 1px solid #ebccd1;
            border-radius: 4px;
            text-align: left;
            z-index: 9999;
            min-width: 300px;
        }

        .message i {
            float: right;
            cursor: pointer;
        }

        p a {
            color: #007bff;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <?php
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo '
            <div class="message">
                <span>' . $msg . '</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i> 
            </div>
            ';
        }
    }
    ?>

    <div class="form-container">
        <form action="" method="post">
            <h3>Đăng Nhập</h3>
            <input type="email" name="email" placeholder="Nhập email" required class="box">
            <input type="password" name="password" placeholder="Nhập Mật Khẩu" required class="box">
            <div class="btn-group">
                <input type="submit" name="submit" value="Đăng Nhập" class="btn">
                <button type="button" onclick="window.location.href='home.php'" class="btn btn-secondary">Trang Chủ</button>
            </div>
            <p>Bạn Đã Có Tài Khoản Chưa? <a href="register.php">Đăng Ký</a></p>
            <p><a href="forgot_pass.php">Quên Mật Khẩu?</a></p>
        </form>
    </div>

</body>

</html>