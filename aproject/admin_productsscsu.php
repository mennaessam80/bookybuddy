<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}
;

if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    $select_product_name = mysqli_query($conn, "SELECT name FROM `school_sub` WHERE name = '$name'") or die('query failed');

    if (mysqli_num_rows($select_product_name) > 0) {
        $message[] = 'Product name already exists';
    } else {
        move_uploaded_file($image_tmp_name, $image_folder);
        $add_product_query = mysqli_query($conn, "INSERT INTO `school_sub` (name, price, quantity, color, image) VALUES ('$name', '$price', '$quantity', '$color', '$image')") or die('query failed');
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_image_query = mysqli_query($conn, "SELECT image FROM `school_sub` WHERE id = '$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
    unlink('uploaded_img/' . $fetch_delete_image['image']);
    mysqli_query($conn, "DELETE FROM `school_sub` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_productsscsu.php');
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_price = $_POST['update_price'];
    $update_quantity = $_POST['update_quantity'];
    $update_color = mysqli_real_escape_string($conn, $_POST['update_color']);

    mysqli_query($conn, "UPDATE `school_sub` SET name = '$update_name', price = '$update_price', quantity = '$update_quantity', color = '$update_color' WHERE id = '$update_p_id'") or die('query failed');

    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_folder = 'uploaded_img/' . $update_image;
    $update_old_image = $_POST['update_old_image'];

    if (!empty($update_image)) {
        mysqli_query($conn, "UPDATE `school_sub` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
        move_uploaded_file($update_image_tmp_name, $update_folder);
        unlink('uploaded_img/' . $update_old_image);
    }

    header('location:admin_productsscsu.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Supplies</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php include 'admin_header.php'; ?>
    <?php include 'secadmhed.php'; ?>

    <section class="add-products">
        <h1 class="title">School Supplies</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Add School Supply</h3>
            <input type="text" name="name" class="box" placeholder="Enter product name" required>
            <input type="number" min="0" name="price" class="box" placeholder="Enter product price" required>
            <input type="number" min="0" name="quantity" class="box" placeholder="Enter product quantity" required>
            <select name="color" class="box" required>
                <option value="" disabled selected>Select color</option>
                <option value="Red">Red</option>
                <option value="Blue">Blue</option>
                <option value="Green">Green</option>
                <option value="Yellow">Yellow</option>
                <option value="Black">Black</option>
                <option value="White">White</option>
            </select>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
            <input type="submit" value="Add School Supply" name="add_product" class="btn">
        </form>
    </section>

    <section class="show-products">
        <div class="box-container">
            <?php
            $select_products = mysqli_query($conn, "SELECT * FROM `school_sub`") or die('query failed');
            if (mysqli_num_rows($select_products) > 0) {
                while ($fetch_products = mysqli_fetch_assoc($select_products)) {
                    ?>
            <div class="box">
                <img style="width: 250px;" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                <div class="name"><?php echo $fetch_products['name']; ?></div>
                <div class="price"><?php echo htmlspecialchars($fetch_products['price'], 2); ?> EGP</div>

                <div class="quantity">Quantity: <?php echo $fetch_products['quantity']; ?></div>
                <div class="color">Color: <?php echo $fetch_products['color']; ?></div>
                <a href="admin_productsscsu.php?update=<?php echo $fetch_products['id']; ?>"
                    class="option-btn">Update</a>
                <a href="admin_productsscsu.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn"
                    onclick="return confirm('Delete this product?');">Delete</a>
            </div>
            <?php
                }
            } else {
                echo '<p class="empty">No school supplies added yet!</p>';
            }
            ?>
        </div>
    </section>

    <section class="edit-product-form">
        <?php
        if (isset($_GET['update'])) {
            $update_id = $_GET['update'];
            $update_query = mysqli_query($conn, "SELECT * FROM `school_sub` WHERE id = '$update_id'") or die('query failed');
            if (mysqli_num_rows($update_query) > 0) {
                while ($fetch_update = mysqli_fetch_assoc($update_query)) {
                    ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
            <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
            <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
            <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required
                placeholder="Enter product name">
            <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box"
                required placeholder="Enter product price">
            <input type="number" name="update_quantity" value="<?php echo $fetch_update['quantity']; ?>" min="0"
                class="box" required placeholder="Enter product quantity">
            <select name="update_color" class="box" required>
                <option value="" disabled selected>Select color</option>
                <option value="Red" <?php if ($fetch_update['color'] == 'Red')
                                echo 'selected'; ?>>Red</option>
                <option value="Blue" <?php if ($fetch_update['color'] == 'Blue')
                                echo 'selected'; ?>>Blue</option>
                <option value="Green" <?php if ($fetch_update['color'] == 'Green')
                                echo 'selected'; ?>>Green</option>
                <option value="Yellow" <?php if ($fetch_update['color'] == 'Yellow')
                                echo 'selected'; ?>>Yellow</option>
                <option value="Black" <?php if ($fetch_update['color'] == 'Black')
                                echo 'selected'; ?>>Black</option>
                <option value="White" <?php if ($fetch_update['color'] == 'White')
                                echo 'selected'; ?>>White</option>
            </select>
            <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
            <input type="submit" value="Update" name="update_product" class="btn">
            <input type="reset" value="Cancel" id="close-update" class="option-btn">
        </form>
        <?php
                }
            }
        } else {
            echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
        }
        ?>
    </section>

    <script src="js/admin_script.js"></script>
    <script>
    document.querySelector('#close-update').onclick = () => {
        document.querySelector('.edit-product-form').style.display = 'none';
        window.location.href = 'admin_productsscsu.php';
    }
    </script>

</body>

</html>