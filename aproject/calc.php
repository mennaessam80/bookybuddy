<?php include 'config.php';

session_start();

$user_id=$_SESSION['user_id'];

if ( !isset($user_id)) {
    header('location:login.php');
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $product_name=$_POST['product_name'];
    $product_brand=$_POST['product_brand'];
    $product_price=$_POST['product_price'];
    $product_image=$_POST['product_image'];
    $product_quantity=$_POST['product_quantity'];
    $available_quantity=$_POST['product_available_quantity'];

    if ($product_quantity > $available_quantity) {
        $message[]='Requested quantity exceeds available stock!';
    }

    else {
        $check_cart_numbers=mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND brand = '$product_brand' AND user_id = '$user_id'") or die('query failed');

        if (mysqli_num_rows($check_cart_numbers) > 0) {
            $message[]='Already added to cart!';
        }

        else {
            mysqli_query($conn, "INSERT INTO `cart`(user_id, name, brand, price, quantity, image) VALUES('$user_id', '$product_name', '$product_brand', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
            $message[]='Product added to cart!';

            // Update the quantity in the calc table
            $new_quantity=$available_quantity - $product_quantity;
            mysqli_query($conn, "UPDATE `calc` SET quantity = '$new_quantity' WHERE name = '$product_name' AND brand = '$product_brand'") or die('query failed');
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
    <title>Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/aa.css">
    <style>
    .nnavbar .c {
        background-color: #DDDAD1;
    }

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
        <h3>Our Shop</h3>
        <p><a href="home.php">Home</a> / Shop / Calculators </p>
    </div>

    <div class="sidebar">
        <form method="GET" action="calc.php">
            <div class="filter-group">
                <h3>Filter by price</h3>
                <select name="price" class="box">
                    <option value="">All prices</option>
                    <option value="low">Low to High</option>
                    <option value="high">High to Low</option>
                </select>
            </div>
            <div class="filter-group">
                <h3>Filter by brand</h3>
                <select name="brand" class="box">
                    <option value="">All brands</option>
                    <option value="casio">Casio</option>
                    <option value="casine">Casine</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <button type="submit" class="btn">Filter</button>
            <a href="calc.php" class="btn" style="background: #DDDAD1; width:70%;">Reset Filter</a>
        </form>
    </div>

    <section class="products">
        <div class="box-container">
            <?php
            $query = "SELECT * FROM `calc`";
            $price_filter = isset($_GET['price']) ? $_GET['price'] : '';
            $brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';

            if ($brand_filter != '') {
                $query .= " WHERE brand = '$brand_filter'";
            }

            if ($price_filter == 'low') {
                $query .= ($brand_filter != '') ? " AND" : " WHERE";
                $query .= " ORDER BY price ASC";
            } elseif ($price_filter == 'high') {
                $query .= ($brand_filter != '') ? " AND" : " WHERE";
                $query .= " ORDER BY price DESC";
            }

            $select_products = mysqli_query($conn, $query) or die('query failed');
            if (mysqli_num_rows($select_products) > 0) {
                while ($fetch_products = mysqli_fetch_assoc($select_products)) {
                    $image_path = 'uploaded_img/' . $fetch_products['image'];
                    $image_exists = file_exists($image_path);
                    ?>
            <form action="" method="post" class="box">
                <?php if ($image_exists) { ?>
                <img class="image" src="<?php echo $image_path; ?>" alt="">
                <?php } else { ?>
                <img class="image" src="uploaded_img/default.png" alt="Image not available">
                <?php } ?>
                <div class="name"><?php echo $fetch_products['name']; ?></div>
                <div class="brand">Brand: <?php echo $fetch_products['brand']; ?></div>
                <div class="price"><?php echo htmlspecialchars($fetch_products['price'], 2); ?> EGP</div>

                <div class="quantity">Available: <?php echo $fetch_products['quantity']; ?></div>
                <input type="number" min="1" max="<?php echo $fetch_products['quantity']; ?>" name="product_quantity"
                    value="1" class="qty">
                <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                <input type="hidden" name="product_brand" value="<?php echo $fetch_products['brand']; ?>">
                <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                <input type="hidden" name="product_available_quantity"
                    value="<?php echo $fetch_products['quantity']; ?>">
                <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
            </form>
            <?php
                }
            } else {
                echo '<p class="empty">No products added yet!</p>';
            }
            ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="js/script.js"></script>
</body>

</html>