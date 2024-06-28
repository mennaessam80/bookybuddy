<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

// Delete user
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
    mysqli_query($conn, "DELETE FROM `subscriptions` WHERE user_id = '$delete_id'") or die('query failed');
    header('location:admin_users.php');
    exit();
}

// Block user
if (isset($_GET['block'])) {
    $block_id = $_GET['block'];
    mysqli_query($conn, "UPDATE `users` SET status = 'blocked' WHERE id = '$block_id'") or die('query failed');
    header('location:admin_users.php');
    exit();
}

// Unblock user
if (isset($_GET['unblock'])) {
    $unblock_id = $_GET['unblock'];
    mysqli_query($conn, "UPDATE `users` SET status = 'unblocked' WHERE id = '$unblock_id'") or die('query failed');
    header('location:admin_users.php');
    exit();
}
// Add approve and reject functionalities
if (isset($_GET['approve'])) {
    $approve_id = $_GET['approve'];
    mysqli_query($conn, "UPDATE users SET access = 'accepted' WHERE id = '$approve_id' AND user_type = 'admin'") or die('query failed');
    header('location:admin_users.php');
    exit();
}

if (isset($_GET['reject'])) {
    $reject_id = $_GET['reject'];
    mysqli_query($conn, "UPDATE users SET access = 'rejected' WHERE id = '$reject_id' AND user_type = 'admin'") or die('query failed');
    header('location:admin_users.php');
    exit();
}


// Toggle user subscription
if (isset($_GET['toggle_subscription'])) {
    $user_id = $_GET['toggle_subscription'];
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_type FROM `users` WHERE id = '$user_id'"));
    if ($user['user_type'] == 'user') { // Ensure the user is not an admin
        $subscription = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM `subscriptions` WHERE user_id = '$user_id'"));
        if ($subscription) {
            // If user has a subscription, delete it (downgrade to normal)
            mysqli_query($conn, "DELETE FROM `subscriptions` WHERE user_id = '$user_id'") or die('query failed');
        } else {
            // If user doesn't have a subscription, create it (upgrade to premium)
            $start_date = date('Y-m-d H:i:s');
            $end_date = date('Y-m-d H:i:s', strtotime('+30 days'));
            mysqli_query($conn, "INSERT INTO `subscriptions` (user_id, name, number, email, method, total_price, payment_status, start_date, end_date) VALUES ('$user_id', '', '', '', '', 0, 'completed', '$start_date', '$end_date')") or die('query failed');
        }
    }
    header('location:admin_users.php');
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
    <title>Users</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
    .l {
        font-size: 20px;
    }
    </style>
</head>

<body>

    <?php include 'admin_header.php'; ?>

    <section class="users">

        <h1 class="title">User Accounts</h1>

        <div class="box-container">
            <?php
$select_users = mysqli_query($conn, "SELECT * FROM users") or die('query failed');
while ($fetch_users = mysqli_fetch_assoc($select_users)) {
    $subscription_query = mysqli_query($conn, "SELECT * FROM subscriptions WHERE user_id = '{$fetch_users['id']}'");
    $subscription = mysqli_fetch_assoc($subscription_query);
    $subscription_status = $subscription ? 'premium' : 'normal';
    $start_date = $subscription ? $subscription['start_date'] : '';
    $end_date = $subscription ? $subscription['end_date'] : '';
?>

            <div class="box">
                <p> User ID : <span><?php echo $fetch_users['id']; ?></span> </p>
                <p> Username : <span><?php echo $fetch_users['name']; ?></span> </p>
                <p> Email : <span><?php echo $fetch_users['email']; ?></span> </p>
                <p> User Type : <span><?php echo $fetch_users['user_type']; ?></span> </p>
                <p> Subscription : <span
                        style="color:<?php echo ($subscription_status == 'premium') ? 'var(--orange)' : ''; ?>">
                        <?php echo $subscription_status; ?></span>
                </p>
                <?php if ($subscription_status == 'premium') { ?>
                <p style=" font-size: 15px;"> Start Date : <span><?php echo $start_date; ?></span> </p>
                <p style=" font-size: 15px;"> End Date : <span><?php echo $end_date; ?></span> </p>
                <?php } ?>
                <p> Status : <span style="color:<?php echo ($fetch_users['status'] == 'blocked') ? 'red' : 'green'; ?>">
                        <?php echo $fetch_users['status']; ?></span>
                </p>
                <p> Access : <span><?php echo $fetch_users['access']; ?></span> </p>
                <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>"
                    onclick="return confirm('Delete this user?');" class="delete-btn">Delete User</a>
                <?php if ($fetch_users['status'] == 'unblocked') { ?>
                <a href="admin_users.php?block=<?php echo $fetch_users['id']; ?>"
                    onclick="return confirm('Block this user?');" class="option-btn">Block User</a>
                <?php } else { ?>
                <a href="admin_users.php?unblock=<?php echo $fetch_users['id']; ?>"
                    onclick="return confirm('Unblock this user?');" class="option-btn">Unblock User</a>
                <?php } ?>
                <?php if ($fetch_users['user_type'] != 'admin') { ?>
                <a href="admin_users.php?toggle_subscription=<?php echo $fetch_users['id']; ?>"
                    onclick="return confirm('Change subscription?');" class="option-btn">
                    <?php echo ($subscription_status == 'normal') ? 'Make Premium' : 'Make Normal'; ?>
                </a>
                <?php } ?>
                <?php if ($fetch_users['user_type'] == 'admin') { ?>
                <?php if ($fetch_users['access'] == 'rejected') { ?>
                <a href="admin_users.php?approve=<?php echo $fetch_users['id']; ?>"
                    onclick="return confirm('Approve this admin?');" class="option-btn">Approve Admin</a>
                <?php } ?>
                <?php if ($fetch_users['access'] == 'accepted') { ?>
                <a href="admin_users.php?reject=<?php echo $fetch_users['id']; ?>"
                    onclick="return confirm('Reject this admin?');" class="option-btn">Reject Admin</a>
                <?php } ?>
                <?php } ?>
            </div>
            <?php
            }
            ?>
        </div>

    </section>

    <!-- custom admin js file link  -->
    <script src="js/admin_script.js"></script>

</body>

</html>