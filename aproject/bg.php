<style>
.nnavbar .d {
    background-color: #DDDAD1;
}
</style>

<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($check_cart_numbers) > 0) {
        $message[] = 'Already added to cart!';
    } else {
        // Fetch available quantity
        $fetch_quantity_query = mysqli_query($conn, "SELECT quantity FROM `bags` WHERE name = '$product_name'") or die('query failed');
        $fetch_quantity = mysqli_fetch_assoc($fetch_quantity_query);

        if ($fetch_quantity['quantity'] < $product_quantity) {
            $message[] = 'Not enough quantity available!';
        } else {
            mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
            mysqli_query($conn, "UPDATE `bags` SET quantity = quantity - $product_quantity WHERE name = '$product_name'") or die('query failed');
            $message[] = 'Product added to cart!';
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
    <title>shop</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/aa.css">
    <!-- Custom CSS file link  -->
    <link rel="stylesheet" href="css/aa.css">
    <style>
    .sidebar {
        width: 250px;
        float: left;
        height: 100%;
        padding: 40px;
    }

    .sidebar h3 {
        margin-bottom: 20px;
        font-size: 1.8rem;
        color: #333;
    }

    .sidebar a {
        display: block;
        color: #555;
        text-decoration: none;
        margin-bottom: 10px;
        padding: 10px 15px;
        background: #fff;
        border-radius: 5px;
        transition: background 0.3s, color 0.3s;
        font-size: 1.5rem;
    }

    .sidebar a:hover {
        background: #e3e3e3;
        color: #000;
    }

    .sidebar .filter-group {
        margin-bottom: 30px;
    }

    .content {
        margin-left: 270px;
        padding: 20px;
    }
    </style>

</head>

<body>

    <?php include 'header.php'; ?>

    <?php include 'seched.php'; ?>


    <div class="heading">
        <h3>our shop</h3>
        <p> <a href="home.php">home</a> / shop / bags & pencil cases</p>
    </div>

    <div class="sidebar">
        <form method="GET" action="bg.php">
            <div class="filter-group">
                <h3>Filter by price</h3>
                <select name="price" class="box">
                    <option value="">All prices</option>
                    <option value="low">Low to High</option>
                    <option value="high">High to Low</option>
                </select>
            </div>
            <div class="filter-group">
                <h3>Filter by size </h3>
                <select name="size" class="box">
                    <option value="">All sizes</option>
                    <option value="Small">Small</option>
                    <option value="Medium">Medium</option>
                    <option value="Large">Large</option>
                    <option value="X-Large">X-Large</option>
                </select>
            </div>
            <div class="filter-group">
                <h3>Filter by color</h3>
                <select name="color" class="box">
                    <option value="">All colors</option>
                    <option value="Red">Red</option>
                    <option value="Blue">Blue</option>
                    <option value="Green">Green</option>
                    <option value="pink">pink</option>
                    <option value="Black">Black</option>
                    <option value="White">White</option>
                </select>
            </div>
            <button type="submit" class="btn">Filter</button>
            <a href="bg.php" class="btn" style="background: #DDDAD1; width:70%;">Reset Filter</a>
        </form>
    </div>

    <section class="products">

        <!-- <h1 class="title">latest products</h1> -->

        <div class="box-container">

            <?php
            $query = "SELECT * FROM `bags`";
            $price_filter = isset($_GET['price']) ? $_GET['price'] : '';
            $size_filter = isset($_GET['size']) ? $_GET['size'] : '';
            $color_filter = isset($_GET['color']) ? $_GET['color'] : '';

            $filters = [];

            if ($size_filter != '') {
                $filters[] = "size = '$size_filter'";
            }
            if ($color_filter != '') {
                $filters[] = "color = '$color_filter'";
            }

            if (!empty($filters)) {
                $query .= " WHERE " . implode(" AND ", $filters);
            }

            if ($price_filter == 'low') {
                $query .= " ORDER BY price ASC";
            } elseif ($price_filter == 'high') {
                $query .= " ORDER BY price DESC";
            }

            $select_products = mysqli_query($conn, $query) or die('query failed');
            if (mysqli_num_rows($select_products) > 0) {
                while ($fetch_products = mysqli_fetch_assoc($select_products)) {
                    ?>
            <form action="" method="post" class="box">
                <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                <div class="name"><?php echo $fetch_products['name']; ?></div>
                <div class="price"><?php echo htmlspecialchars($fetch_products['price']); ?> EGP/-</div>

                <div class="quantity"><?php echo $fetch_products['quantity']; ?> in stock</div>
                <input type="number" min="1" max="<?php echo $fetch_products['quantity']; ?>" name="product_quantity"
                    value="1" class="qty">
                <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                <input type="submit" value="add to cart" name="add_to_cart" class="btn">
            </form>
            <?php
                }
            } else {
                echo '<p class="empty">no products added yet!</p>';
            }
            ?>
        </div>

    </section>

    <?php include 'footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>