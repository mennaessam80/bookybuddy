<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/aa.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>Your Orders</h3>
        <p> <a href="home.php">Home</a> / Orders </p>
    </div>

    <section class="placed-orders">

        <h1 class="title">Placed Orders</h1>

        <div class="box-container">

            <?php
            $order_query = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
            $order_query->bind_param("i", $user_id);
            $order_query->execute();
            $result = $order_query->get_result();

            if ($result->num_rows > 0) {
                while ($fetch_orders = $result->fetch_assoc()) {
                    ?>
                    <div class="box">
                        <h3>orders Details</h3>
                        <p> Placed on: <span><?php echo htmlspecialchars($fetch_orders['placed_on']); ?></span> </p>
                        <p> Name: <span><?php echo htmlspecialchars($fetch_orders['name']); ?></span> </p>
                        <p> Number: <span><?php echo htmlspecialchars($fetch_orders['number']); ?></span> </p>
                        <p> Email: <span><?php echo htmlspecialchars($fetch_orders['email']); ?></span> </p>
                        <p> Address: <span><?php echo htmlspecialchars($fetch_orders['address']); ?></span> </p>
                        <p> Payment Method: <span><?php echo htmlspecialchars($fetch_orders['method']); ?></span> </p>
                        <p> Your Orders: <span><?php echo htmlspecialchars($fetch_orders['total_products']); ?></span> </p>
                        <p> Total Price:
                            <span><?php echo htmlspecialchars(htmlspecialchars($fetch_orders['total_price']), 2); ?>
                                EGP</span> </p>

                        <p> Payment Status: <span
                                style="color:<?php echo ($fetch_orders['payment_status'] == 'pending') ? 'red' : 'green'; ?>;"><?php echo htmlspecialchars($fetch_orders['payment_status']); ?></span>
                        </p>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="empty">No orders placed yet!</p>';
            }

            // Fetch subscription details
            $subscription_query = $conn->prepare("SELECT * FROM `subscriptions` WHERE user_id = ?");
            $subscription_query->bind_param("i", $user_id);
            $subscription_query->execute();
            $subscription_result = $subscription_query->get_result();

            if ($subscription_result->num_rows > 0) {
                while ($fetch_subscription = $subscription_result->fetch_assoc()) {
                    ?>
                    <div class="box">
                        <h3>Subscription Details</h3>
                        <p> Name: <span><?php echo htmlspecialchars($fetch_subscription['name']); ?></span> </p>
                        <p> Number: <span><?php echo htmlspecialchars($fetch_subscription['number']); ?></span> </p>
                        <p> Email: <span><?php echo htmlspecialchars($fetch_subscription['email']); ?></span> </p>
                        <p> Payment Method: <span><?php echo htmlspecialchars($fetch_subscription['method']); ?></span> </p>
                        <p> Total Price:
                            <span><?php echo htmlspecialchars(htmlspecialchars($fetch_subscription['total_price']), 2); ?>
                                EGP</span>
                        </p>



                        <p> Payment Status: <span
                                style="color:<?php echo ($fetch_subscription['payment_status'] == 'pending') ? 'red' : 'green'; ?>;"><?php echo htmlspecialchars($fetch_subscription['payment_status']); ?></span>
                        </p>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="empty">No subscriptions found!</p>';
            }
            ?>
        </div>

    </section>

    <?php include 'footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>