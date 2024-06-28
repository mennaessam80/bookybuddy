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
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);

    $select_calc_name = mysqli_query($conn, "SELECT name FROM `calc` WHERE name = '$name'") or die('query failed');

    if (mysqli_num_rows($select_calc_name) > 0) {
        $message[] = 'Calculator name already exists';
    } else {

        $add_calc_query = mysqli_query($conn, "INSERT INTO `calc` (name, price, image, quantity, brand) VALUES ('$name', '$price', '$image', '$quantity', '$brand')") or die('query failed');
    }
}


if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_image_query = mysqli_query($conn, "SELECT image FROM `calc` WHERE id = '$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
    unlink('uploaded_img/' . $fetch_delete_image['image']);
    mysqli_query($conn, "DELETE FROM `calc` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_productscalc.php');
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_price = $_POST['update_price'];
    $update_quantity = $_POST['update_quantity'];

    mysqli_query($conn, "UPDATE `calc` SET name = '$update_name', price = '$update_price', quantity = '$update_quantity' WHERE id = '$update_p_id'") or die('query failed');

    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = 'uploaded_img/' . $update_image;
    $update_old_image = $_POST['update_old_image'];

    if (!empty($update_image)) {
        mysqli_query($conn, "UPDATE `calc` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
        move_uploaded_file($update_image_tmp_name, $update_folder);
        unlink('uploaded_img/' . $update_old_image);
    }

    header('location:admin_productscalc.php');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculators</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php include 'admin_header.php'; ?>
    <?php include 'secadmhed.php'; ?>

    <section class="add-products">
        <h1 class="title">Calculators</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Add Calculator</h3>
            <input type="text" name="name" class="box" placeholder="Enter calculator name" required>
            <input type="number" min="0" name="price" class="box" placeholder="Enter calculator price" required>
            <input type="number" min="0" name="quantity" class="box" placeholder="Enter quantity" required>
            <input type="text" name="brand" class="box" placeholder="Enter calculator brand" required>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
            <input type="submit" value="Add Calculator" name="add_product" class="btn">
        </form>
    </section>


    <section class="show-products">
        <div class="box-container">
            <?php
            $select_calculators = mysqli_query($conn, "SELECT * FROM `calc`") or die('query failed');
            if (mysqli_num_rows($select_calculators) > 0) {
                while ($fetch_calculators = mysqli_fetch_assoc($select_calculators)) {
                    ?>
            <div class="box">
                <img style="width: 250px;" src="uploaded_img/<?php echo $fetch_calculators['image']; ?>" alt="">
                <div class="name"><?php echo $fetch_calculators['name']; ?></div>
                <div class="brand"><?php echo $fetch_calculators['brand']; ?></div>
                <div class="price"><?php echo htmlspecialchars($fetch_calculators['price']); ?> EGP/-</div>

                <div class="quantity">Quantity: <?php echo $fetch_calculators['quantity']; ?></div>
                <a href="admin_productscalc.php?update=<?php echo $fetch_calculators['id']; ?>"
                    class="option-btn">Update</a>
                <a href="admin_productscalc.php?delete=<?php echo $fetch_calculators['id']; ?>" class="delete-btn"
                    onclick="return confirm('Delete this calculator?');">Delete</a>
            </div>
            <?php
                }
            } else {
                echo '<p class="empty">No calculators added yet!</p>';
            }
            ?>
        </div>
    </section>

    <section class="edit-product-form">
        <?php
        if (isset($_GET['update'])) {
            $update_id = $_GET['update'];
            $update_query = mysqli_query($conn, "SELECT * FROM `calc` WHERE id = '$update_id'") or die('query failed');
            if (mysqli_num_rows($update_query) > 0) {
                while ($fetch_update = mysqli_fetch_assoc($update_query)) {
                    ?>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
            <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
            <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
            <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required
                placeholder="Enter calculator name">
            <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box"
                required placeholder="Enter calculator price">
            <input type="number" name="update_quantity" value="<?php echo $fetch_update['quantity']; ?>" min="0"
                class="box" required placeholder="Enter quantity">
            <input type="text" name="update_brand" value="<?php echo $fetch_update['brand']; ?>" class="box" required
                placeholder="Enter calculator brand">
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
        window.location.href = 'aadmin_productscalc.php';

    }
    </script>

</body>

</html>