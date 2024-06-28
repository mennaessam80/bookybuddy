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

    $check_stock_query = mysqli_query($conn, "SELECT quantity FROM `books` WHERE name = '$product_name'") or die('query failed');
    $stock_row = mysqli_fetch_assoc($check_stock_query);
    $available_quantity = $stock_row['quantity'];

    if ($product_quantity > $available_quantity) {
        $message[] = 'Not enough stock available';
    } elseif (mysqli_num_rows($check_cart_numbers) > 0) {
        $message[] = 'Already added to cart';
    } else {
        mysqli_query($conn, "INSERT INTO `cart` (user_id, name, price, quantity, image) VALUES ('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
        mysqli_query($conn, "UPDATE `books` SET quantity = quantity - $product_quantity WHERE name = '$product_name'") or die('query failed');
        $message[] = 'Book added to cart';
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
        <p> <a href="home.php">home</a> / shop / books</p>
    </div>

    <div class="sidebar">
        <form method="GET" action="shop.php">
            <div class="filter-group">
                <h3>Filter by Grade</h3>
                <select name="grade" class="box">
                    <option value="">All Grades</option>
                    <option value="kg1">كي جي 1</option>
                    <option value="kg2">كي جي 2</option>
                    <option value="1prim">1ابتدائي</option>
                    <option value="2prim">2ابتدائي</option>
                    <option value="3prim">3ابتدائي</option>
                    <option value="4prim">4ابتدائي</option>
                    <option value="5prim">5ابتدائي</option>
                    <option value="6prim">6ابتدائي</option>
                    <option value="1prep">1اعدادي</option>
                    <option value="2prep">2اعدادي</option>
                    <option value="3prep">3اعدادي</option>
                    <option value="1sec">1ثانوي</option>
                    <option value="2sec">2ثانوي</option>
                    <option value="3sec">3ثانوي</option>
                    <option value="other">اخري</option>
                </select>
            </div>
            <div class="filter-group">
                <h3>Filter by type of book</h3>
                <select name="type" class="box">
                    <option value="">All types</option>
                    <option value="SelahEltelmeez">سلاح التلميذ</option>
                    <option value="Elamthan">الإمتحان</option>
                    <option value="Aladwaa">الأضواء</option>
                    <option value="Elmoasser">المعاصر</option>
                    <option value="Bravo">برافو</option>
                    <option value="Other">اخري</option>
                </select>
            </div>
            <button type="submit" class="btn">Filter</button>
            <a href="shop.php" class="btn" style=" background: #DDDAD1; width:70%;">Reset Filter</a>
        </form>
    </div>

    <section class="products">
        <div class="box-container">
            <?php
            $query = "SELECT * FROM `books`";
            $conditions = [];

            if (isset($_GET['type']) && !empty($_GET['type'])) {
                $type = mysqli_real_escape_string($conn, $_GET['type']);
                $conditions[] = "type='$type'";
            }

            if (isset($_GET['grade']) && !empty($_GET['grade'])) {
                $grade = mysqli_real_escape_string($conn, $_GET['grade']);
                $conditions[] = "grade='$grade'";
            }

            if (count($conditions) > 0) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }

            $select_products = mysqli_query($conn, $query) or die('query failed');
            if (mysqli_num_rows($select_products) > 0) {
                while ($fetch_products = mysqli_fetch_assoc($select_products)) {
                    ?>
            <form action="" method="post" class="box">
                <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                <div class="name"><?php echo $fetch_products['name']; ?></div>
                <div class="price"><?php echo htmlspecialchars($fetch_products['price']); ?>EGP/-</div>

                <div class="grade"><?php echo $fetch_products['grade']; ?></div>
                <div class="type"><?php echo $fetch_products['type']; ?></div>
                <div class="quantity">Available: <?php echo $fetch_products['quantity']; ?></div>

                <input type="number" min="1" name="product_quantity" value="1" class="qty">
                <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                <input type="submit" value="add to cart" name="add_to_cart" class="btn">
            </form>
            <?php
                }
            } else {
                echo '<p class="empty">No books available!</p>';
            }
            ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>