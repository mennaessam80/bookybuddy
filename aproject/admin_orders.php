<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

if (isset($_POST['update_order'])) {
    $order_update_id = $_POST['order_id'];
    $update_payment = $_POST['update_payment'];
    mysqli_query($conn, "UPDATE `orders` SET payment_status = '$update_payment' WHERE id = '$order_update_id'") or die('query failed');
    $message[] = 'Payment status has been updated!';
}

if (isset($_POST['update_subscription'])) {
    $subscription_id = $_POST['subscription_id'];
    $update_payment = $_POST['update_payment'];
    if ($update_payment == 'completed') {
        $start_date = date('Y-m-d H:i:s');
        $end_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        mysqli_query($conn, "UPDATE `subscriptions` SET payment_status = '$update_payment', start_date = '$start_date', end_date = '$end_date' WHERE id = '$subscription_id'") or die('query failed');
    } else {
        mysqli_query($conn, "UPDATE `subscriptions` SET payment_status = '$update_payment', start_date = NULL, end_date = NULL WHERE id = '$subscription_id'") or die('query failed');
    }
    $message[] = 'Subscription payment status has been updated!';
}

if (isset($_GET['delete_order'])) {
    $delete_id = $_GET['delete_order'];
    mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_orders.php');
    exit();
}

if (isset($_GET['delete_subscription'])) {
    $delete_id = $_GET['delete_subscription'];
    mysqli_query($conn, "DELETE FROM `subscriptions` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_orders.php');
    exit();
}


// Check for expired subscriptions and delete them
mysqli_query($conn, "DELETE FROM `subscriptions` WHERE end_date < NOW()") or die('query failed');

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

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

    <?php include 'admin_header.php'; ?>

    <section class="orders">
        <h1 class="title">Subscriptions</h1>
        <div class="box-container">
            <?php
        $select_subscriptions = mysqli_query($conn, "SELECT * FROM `subscriptions`") or die('query failed');
        if (mysqli_num_rows($select_subscriptions) > 0) {
            while ($fetch_subscriptions = mysqli_fetch_assoc($select_subscriptions)) {
                ?>
            <div class="box">
                <p>User ID : <span><?php echo $fetch_subscriptions['user_id']; ?></span></p>
                <p>Name : <span><?php echo $fetch_subscriptions['name']; ?></span></p>
                <p>Number : <span><?php echo $fetch_subscriptions['number']; ?></span></p>
                <p>Email : <span><?php echo $fetch_subscriptions['email']; ?></span></p>
                <p>Method : <span><?php echo $fetch_subscriptions['method']; ?></span></p>
                <p>Total Price : <span><?php echo htmlspecialchars($fetch_subscriptions['total_price']); ?> EGP</span>
                </p>
                <p>Payment Status : <span><?php echo $fetch_subscriptions['payment_status']; ?></span></p>
                <p>Start Date : <span><?php echo $fetch_subscriptions['start_date']; ?></span></p>
                <p>End Date : <span><?php echo $fetch_subscriptions['end_date']; ?></span></p>
                <form action="" method="post">
                    <input type="hidden" name="subscription_id" value="<?php echo $fetch_subscriptions['id']; ?>">
                    <select name="update_payment">
                        <option value="" selected disabled><?php echo $fetch_subscriptions['payment_status']; ?>
                        </option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                    <input type="submit" value="Update" name="update_subscription" class="option-btn"> <a
                        href="admin_orders.php?delete_subscription=<?php echo $fetch_subscriptions['id']; ?>"
                        onclick="return confirm('delete this subscription?');" class="delete-btn">Delete</a>
                </form>

            </div>
            <?php
            }
        } else {
            echo '<p class="empty">No subscriptions found!</p>';
        }
        ?>
        </div>

        <h1 class="title">Placed Orders</h1>
        <div class="box-container">
            <?php
        $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
        if (mysqli_num_rows($select_orders) > 0) {
            while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
                ?>
            <div class="box">
                <p>User ID : <span><?php echo $fetch_orders['user_id']; ?></span></p>
                <p>Placed on : <span><?php echo $fetch_orders['placed_on']; ?></span></p>
                <p>Name : <span><?php echo $fetch_orders['name']; ?></span></p>
                <p>Number : <span><?php echo $fetch_orders['number']; ?></span></p>
                <p>Email : <span><?php echo $fetch_orders['email']; ?></span></p>
                <p>Address : <span><?php echo $fetch_orders['address']; ?></span></p>
                <p>Total products : <span><?php echo $fetch_orders['total_products']; ?></span></p>
                <p>Total price : <span><?php echo htmlspecialchars($fetch_orders['total_price']); ?> EGP</span></p>
                <p>Payment method : <span><?php echo $fetch_orders['method']; ?></span></p>
                <form action="" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
                    <select name="update_payment">
                        <option value="" selected disabled><?php echo $fetch_orders['payment_status']; ?></option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                    <input type="submit" value="Update" name="update_order" class="option-btn">
                    <a href="admin_orders.php?delete_order=<?php echo $fetch_orders['id']; ?>"
                        onclick="return confirm('delete this order?');" class="delete-btn">Delete</a>
                </form>
            </div>
            <?php
            }
        } else {
            echo '<p class="empty">No orders placed yet!</p>';
        }
        ?>
        </div>
    </section>

    <!-- custom admin js file link  -->
    <script src="js/admin_script.js"></script>

</body>

</html>