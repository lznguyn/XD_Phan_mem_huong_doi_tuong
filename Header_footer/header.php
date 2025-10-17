<?php 
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./Css/header.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Document</title>
</head>
<body>
    <nav>
        <div class="logo">
            <a href="header.php"><img src="" alt=""></a>
        </div>
        <div class="links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Booking</a></li>
            <li><a href="#">Expert</a></li>
            <li><a href="#">Payment</a></li>
            <li><a href="#">UpLoad</a></li>
        </div>
        <div class="sign">
            <?php
                if(isset($_SESSION["username"]))
                {
                    echo "<a href='../login/register.php'>Profile</a>"
                    echo "<a href='../login/logout.php'>Log out</a>"
                }
                else{
                    echo "<a href='../login/register.php'>Register</a>";
                    echo "<a href='../login/logout.php' class='Bbtn'>Login</a>";
                }
            ?>
        </div>
    </nav>
</body>
<script>
    var icon = document.querySelector(".icon_toggle");
    icon.addEventListener("click", ()=>{
        document.body.classList.toggle("dark_theme");
    })
</script>
</html>