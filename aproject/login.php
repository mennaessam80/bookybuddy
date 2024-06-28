<?php

include 'config.php';
session_start();


if (isset($_POST['submit'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

    if (mysqli_num_rows($select_users) > 0) {

        $row = mysqli_fetch_assoc($select_users);
        
        if ($row['status'] == 'blocked') {
            $message[] = 'Your account is blocked. Please contact the administrator.';
        } else {
            if ($row['user_type'] == 'admin' && $row['access'] == 'rejected') {
                $message[] = 'Your account is pending approval.';
                
               } elseif ($row['user_type'] == 'admin' && $row['access'] == 'accepted') {

                $_SESSION['admin_name'] = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_id'] = $row['id'];
                header('Location: admin_page.php');
                exit(); // Ensure no further code is executed after redirection
            } elseif ($row['user_type'] == 'user') {
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_id'] = $row['id'];
                header('Location: home.php');
                exit(); // Ensure no further code is executed after redirection
            }
        }

    } else {
        $message[] = 'Incorrect email or password!';
    }

}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/aa.css">

</head>

<body>

    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '
            <div class="message">
               <span>' . $message . '</span>
               <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
            ';
        }
    }
    ?>

    <div class="form-container">

        <form action="" method="post">
            <div style="text-align: center;">
                <img src="logooo.jpg" alt="Registration Image" style="max-width: 30%; height: auto;">
            </div>
            <h3>Login Now</h3>
            <input type="email" name="email" placeholder="Enter your email" required class="box">
            <input type="password" name="password" placeholder="Enter your password" required class="box">
            <input type="submit" name="submit" value="Login Now" class="btn">
            <p>Don't have an account? <a href="register.php">Register Now</a></p>
        </form>

    </div>

</body>

</html>