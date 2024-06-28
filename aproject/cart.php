<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $new_cart_quantity = $_POST['cart_quantity'];

    // Get the current cart quantity
    $select_cart_item = mysqli_query($conn, "SELECT * FROM `cart` WHERE id = '$cart_id' AND user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select_cart_item) > 0) {
        $fetch_cart_item = mysqli_fetch_assoc($select_cart_item);
        $old_cart_quantity = $fetch_cart_item['quantity'];
        $product_name = $fetch_cart_item['name'];

        // Calculate the difference
        $quantity_difference = $new_cart_quantity - $old_cart_quantity;

        // Update the product quantity
        function update_product_quantity($product_name, $quantity_difference)
        {
            global $conn;
            $tables = ['calc', 'books', 'bags', 'school_sub'];
            foreach ($tables as $table) {
                $select_product = mysqli_query($conn, "SELECT quantity FROM `$table` WHERE name = '$product_name'") or die('query failed');
                if (mysqli_num_rows($select_product) > 0) {
                    $fetch_product = mysqli_fetch_assoc($select_product);
                    $available_quantity = $fetch_product['quantity'];
                    $new_quantity = $available_quantity - $quantity_difference;
                    mysqli_query($conn, "UPDATE `$table` SET quantity = '$new_quantity' WHERE name = '$product_name'") or die('query failed');
                }
            }
        }

        // Update the product quantity in the appropriate table
        update_product_quantity($product_name, $quantity_difference);

        // Update the cart quantity
        mysqli_query($conn, "UPDATE `cart` SET quantity = '$new_cart_quantity' WHERE id = '$cart_id'") or die('query failed');
        $message[] = 'cart quantity updated!';
    }
}

function restore_quantity($product_name, $product_quantity)
{
    global $conn;
    $tables = ['calc', 'books', 'bags', 'school_sub'];
    foreach ($tables as $table) {
        $select_product = mysqli_query($conn, "SELECT quantity FROM `$table` WHERE name = '$product_name'") or die('query failed');
        if (mysqli_num_rows($select_product) > 0) {
            $fetch_product = mysqli_fetch_assoc($select_product);
            $available_quantity = $fetch_product['quantity'];
            $new_quantity = $available_quantity + $product_quantity;
            mysqli_query($conn, "UPDATE `$table` SET quantity = '$new_quantity' WHERE name = '$product_name'") or die('query failed');
        }
    }
}

if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    $select_cart_item = mysqli_query($conn, "SELECT * FROM `cart` WHERE id = '$remove_id' AND user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select_cart_item) > 0) {
        $fetch_cart_item = mysqli_fetch_assoc($select_cart_item);
        $product_name = $fetch_cart_item['name'];
        $product_quantity = $fetch_cart_item['quantity'];

        // Restore the quantity in the appropriate table
        restore_quantity($product_name, $product_quantity);

        // Remove the item from the cart
        mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id' AND user_id = '$user_id'") or die('query failed');
        header('location:cart.php');
    }
}

if (isset($_GET['delete_all'])) {
    $select_cart_items = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($select_cart_items) > 0) {
        while ($fetch_cart_item = mysqli_fetch_assoc($select_cart_items)) {
            $product_name = $fetch_cart_item['name'];
            $product_quantity = $fetch_cart_item['quantity'];
            // Restore the quantity in the appropriate table
            restore_quantity($product_name, $product_quantity);
        }
    }
    // Delete all items from the cart
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    header('location:cart.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cart</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/aa.css">

</head>

<body>

    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>shopping cart</h3>
        <p> <a href="home.php">home</a> / cart </p>
    </div>

    <section class="shopping-cart">

        <h1 class="title">products added</h1>

        <div class="box-container">
            <?php
            $grand_total = 0;
            $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            if (mysqli_num_rows($select_cart) > 0) {
                while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                    ?>
            <div class="box">
                <a href="cart.php?remove=<?php echo $fetch_cart['id']; ?>" class="fas fa-times"
                    onclick="return confirm('delete this from cart?');"></a>
                <img style="width:250px;" src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="">
                <div class="name"><?php echo $fetch_cart['name']; ?></div>
                <div class="price"><?php echo htmlspecialchars($fetch_cart['price']); ?> EGP/-</div>

                <form action="" method="post">
                    <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                    <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                    <input type="submit" name="update_cart" value="update" class="option-btn">
                </form>
                <div class="sub-total"> sub total :
                    <span><?php echo htmlspecialchars($sub_total = ($fetch_cart['quantity'] * $fetch_cart['price'])); ?>
                        EGP/-</span>

                </div>
            </div>
            <?php
                    $grand_total += $sub_total;
                }
            } else {
                echo '<p class="empty">your cart is empty</p>';
            }
            ?>
        </div>

        <div style="margin-top: 2rem; text-align:center;">
            <a href="cart.php?delete_all" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>"
                onclick="return confirm('delete all from cart?');">delete all</a>
        </div>

        <div class="cart-total">
            <p>grand total : <span><?php echo htmlspecialchars($grand_total); ?> EGP/-</span></p>

            <div class="flex">
                <a href="shop.php" class="option-btn">continue shopping</a>
                <a href="checkout.php" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">proceed to
                    checkout</a>
            </div>
        </div>

    </section>

    <?php include 'footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>