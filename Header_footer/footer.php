<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Player</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="../Css/footer.css">
</head>
<body>
    <div class="footer">
        <div class="footer_cont">
            <div class="s1">
                <div class="footer_logo">
                    <a href=""></a>
                </div>
                <div class="ab_foot">
                    <p>ABOUT</p>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">About Developer</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Jobs</a></li>
                </div>
                 <div class="lnk_foot">
                    <P>LINKS</P>
                    <li><a href="#">Help</a></li>
                    <li><a href="#">Support</a></li>
                    <li><a href="#">Legal</a></li>
                    <li><a href="#">Copywright</a></li>
                </div>
                <div class="cont_foot">
                    <P>CONNECT</P>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Google+</a></li>
                </div>  
                <div class="sub_footer">
                    <p>SUBCRIBE</p>
                    <form action="">
                        <label for="">Do you want to subscribe to our newsletter?</label>
                        <input type="email" placeholder="Your Email" required>
                        <input type="submit" value="Subscribe">
                    </form>
                </div>  
            </div>
            <hr>
            <div class="s2">
                <p>cookies</p>
                <p>©<span id="year"></span> Copyright. All rights reserved</p>
            </div>
        </div>
    </div>
</body>
<script>
        document.getElementById("year").innerHTML = new Date().getFullYear();
    </script>
</html>