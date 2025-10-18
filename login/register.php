<?php
include 'config.php';

if(isset($_POST['submit'])){
   // Lấy thông tin người dùng và bảo mật đầu vào
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, $_POST['password']);
   $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);
   $user_type = $_POST['user_type'];
   
   // Kiểm tra mã xác nhận cho tài khoản admin
   $admin_code = mysqli_real_escape_string($conn, $_POST['admin_code']);

   // Kiểm tra email đã tồn tại chưa
   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');

   if(mysqli_num_rows($select_users) > 0){
      $messages[] = 'Email Đã Tồn Tại!';
   } else {
      if($pass != $cpass){
         $messages[] = 'Mật khẩu xác nhận không trùng khớp!';
      } elseif(strlen($pass) < 8) {
         // Kiểm tra độ dài mật khẩu
         $messages[] = 'Mật khẩu phải có ít nhất 8 ký tự!';
      } else {
         // Kiểm tra mã xác nhận cho Admin
         if($user_type == 'admin' && $admin_code != 'admin123'){
            $messages[] = 'Mã xác nhận Admin không đúng!';
         } else {
            // Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            // Lưu thông tin người dùng vào cơ sở dữ liệu
            mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$hashed_pass', '$user_type')") or die('query failed');
            $messages[] = 'Đăng ký thành công!  Chúc Mừng Bạn Đăng Ký Tài Khoản Thành Công!';
            
            // Tạm dừng để hiển thị câu chúc mừng, sau đó chuyển hướng
            echo '<meta http-equiv="refresh" content="2;url=login.php">';
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS File -->
    
    <style>
        body {
            background-color: darkcyan;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .message {
            background-color: #f9c2c2;
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: red;
        }

        .message i {
            cursor: pointer;
            color: #333;
        }

        .form-container {
            background-color: #fff;
            width: 400px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .box {
            width: 95%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        .box:focus {
            outline: none;
            border-color: #4CAF50;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .cancel-btn {
            background-color: #f44336;
            margin-top: 10px;
            margin-left:-2px ;
        }

        .cancel-btn:hover {
            background-color: #d32f2f;
        }

        p {
            text-align: center;
            margin-top: 15px;
        }

        p a {
            color: #4CAF50;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        select.box {
            background-color: #fff;
        }

        #admin-code-container {
            display: none;
        }
    </style>
</head>
<body>

<?php
// Hiển thị thông báo lỗi hoặc thành công
if(isset($messages)){
   foreach($messages as $msg){
      echo '
      <div class="message">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<div class="form-container">
    <form action="" method="post">
        <h3>ĐĂNG KÝ TÀI KHOẢN</h3>
        <input type="text" name="name" placeholder="Nhập Tên ..." required class="box">
        <input type="email" name="email" placeholder="Nhập Email..." required class="box">
        <input type="password" name="password" placeholder="Nhập Mật Khẩu..." required class="box" minlength="8">
        <input type="password" name="cpassword" placeholder="Nhập Lại Mật khẩu" required class="box" minlength="8">
        
        <div id="admin-code-container">
            <input type="text" name="admin_code" placeholder="Nhập Mã Xác Thực Của Admin" class="box">
        </div>
        
        <select name="user_type" class="box" onchange="toggleAdminCodeField(this)">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        
        <input type="submit" name="submit" value="Đăng ký" class="btn">
        <input type="button" value="Cancel" class="btn cancel-btn" onclick="window.location.reload();">
        
        <p>Bạn Đã Có Tài Khoản? <a href="login.php">Đăng Nhập</a></p>
    </form>
</div>

<script>
    // Hàm hiển thị hoặc ẩn trường mã xác nhận khi chọn loại người dùng
    function toggleAdminCodeField(select) {
        if (select.value === 'admin') {
            document.getElementById('admin-code-container').style.display = 'block';
        } else {
            document.getElementById('admin-code-container').style.display = 'none';
        }
    }
</script>

</body>
</html>