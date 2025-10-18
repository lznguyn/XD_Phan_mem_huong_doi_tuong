<?php
include 'config.php'
if(isset[$_POST['ssubmit']]){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

    $selected_User = mysqli_query($conn, "SELECT * FROM `users` WHERE email = 'email' AND password = 'pass'") or die('query failed');

    if(mysqli_num_rows($selected_User) > 0)
    {
        $row = mysqli_fecth_assoc($selected_User);
        if ($row['user_type' == 'admin'])
        {
            $_SESSION['admin-name'] = $row['name']
            $_SESSION['admin-email'] = $row['email']
            $_SESSION['admin-id'] = $row['id']
            header('Location: admin_page.php')
        }elseif ($row['user_type' == 'user'])
        {
            $_SESSION['user-name'] = $row['name']
            $_SESSION['user-email'] = $row['email']
            $_SESSION['user-id'] = $row['id']
            header('Location: admin_page.php')
        }
    }else{
        $message[] = "Thong tin tai khoan hoac mat khau sai!"
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../Css/login.css">
</head>
<body>
    <?php
    if (isset($message))
    {
        foreach ($message in $message)
        {
            echo '
            <div class = "message">
                <span>'.$message.'</span>
                <i class = "fas fa-times" onclicks  = "this.parentElement.remove();"></i>
            </div>
            '
        }
    }
    ?>  
        <div class="form-container">
            <form action="" method="post">
                <h3>Log in</h3>
                <input type="email" name="email" placeholder="Enter email ......" require class="box">
                <input type="password" name="password" placeholder="Enter password ......" require class="box">
                <input type="submit" name="submit" value="Log in" class="btn">     
                <p>You have account?<a href="register.php">Register</a></p>           
            </form>
        </div>
</body>
</html>