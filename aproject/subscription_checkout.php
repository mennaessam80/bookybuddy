<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

if (isset($_POST['subscribe_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = $_POST['number'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    // $placed_on = date('d-M-Y');

    // Assuming the subscription cost is fixed
    $subscription_cost = 100;

    $subscription_query = mysqli_query($conn, "SELECT * FROM `subscriptions` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($subscription_query) > 0) {
        $message[] = 'You already have an active subscription!';
    } else {
        mysqli_query($conn, "INSERT INTO `subscriptions` (user_id, name, number, email, method, total_price, payment_status) VALUES ('$user_id', '$name', '$number', '$email', '$method', '$subscription_cost', 'pending')") or die('query failed');
        
        // // Insert corresponding order into orders table
        // mysqli_query($conn, "INSERT INTO `orders` (user_id, name, number, email, address, method, total_products, total_price, placed_on, payment_status) VALUES ('$user_id', '$name', '$number', '$email', 'N/A', '$method', 'Subscription', '$subscription_cost', '$placed_on', 'pending')") or die('query failed');

        $message[] = 'Subscription initiated successfully! Awaiting admin approval.';

        // Update user's subscription status to premium
        // mysqli_query($conn, "UPDATE `users` SET `subscription` = 'premium', `premium_start_date` = NOW() WHERE `id` = '$user_id'") or die('query failed');
        
        // Redirect to orders page
        header('location:orders.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Checkout</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/aa.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>Subscription Checkout</h3>
        <p> <a href="home.php">home</a> / subscription checkout </p>
    </div>

    <section class="checkout">

        <form action="" method="post">
            <h3>Become a Premium Member</h3>
            <div class="flex">
                <div class="inputBox">
                    <span>Your Name :</span>
                    <input type="text" name="name" required placeholder="Enter your name">
                </div>
                <div class="inputBox">
                    <span>Your Number :</span>
                    <input type="number" name="number" required placeholder="Enter your number">
                </div>
                <div class="inputBox">
                    <span>Your Email :</span>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="inputBox">
                    <span>Payment Method :</span>
                    <select name="method">
                        <option value="credit card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="paytm">Paytm</option>
                    </select>
                </div>
            </div>
            <input type="submit" value="Subscribe Now" class="btn" name="subscribe_btn">
        </form>

    </section>

    <?php include 'footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>