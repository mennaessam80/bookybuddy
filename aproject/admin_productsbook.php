<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}
;

if (isset($_POST['add_product'])) {
    // Retrieve form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;
    $grade = $_POST['grade'];
    $type = $_POST['type'];
    $quantity = $_POST['quantity'];

    // Check if the book name already exists
    $check_query = "SELECT name FROM `books` WHERE name = '$name'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Book name already exists
        $message = 'Book name already exists';
    } else {
        // Proceed with inserting the new book
        // Move uploaded file to the designated folder
        if (move_uploaded_file($image_tmp_name, $image_folder)) {
            // Insert new book into database
            $insert_query = "INSERT INTO `books` (name, price, image, grade, type, quantity) 
            VALUES ('$name', '$price', '$image', '$grade', '$type', '$quantity')";
            $insert_result = mysqli_query($conn, $insert_query);

            if ($insert_result) {
                // Redirect or display success message
                header('Location: admin_productsbook.php');
                exit();
            } else {
                die('Failed to insert book into database.');
            }
        } else {
            die('Failed to move uploaded file.');
        }
    }
}



if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_image_query = mysqli_query($conn, "SELECT image FROM `books` WHERE id = '$delete_id'") or die('query failed');
    $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
    unlink('uploaded_img/' . $fetch_delete_image['image']);
    mysqli_query($conn, "DELETE FROM `books` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_productsbook.php');
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_price = $_POST['update_price'];
    $update_grade = $_POST['update_grade'];
    $update_type = $_POST['update_type'];
    $update_quantity = $_POST['update_quantity'];

    mysqli_query($conn, "UPDATE `books` SET name = '$update_name', price = '$update_price', grade = '$update_grade', type = '$update_type', quantity = '$update_quantity' WHERE id = '$update_p_id'") or die('query failed');

    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = 'uploaded_img/' . $update_image;
    $update_old_image = $_POST['update_old_image'];

    if (!empty($update_image)) {
        mysqli_query($conn, "UPDATE `books` SET image = '$update_image' WHERE id = '$update_p_id'") or die('query failed');
        move_uploaded_file($update_image_tmp_name, $update_folder);
        unlink('uploaded_img/' . $update_old_image);

    }

    header('location:admin_productsbook.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php include 'admin_header.php'; ?>
    <?php include 'secadmhed.php'; ?>

    <section class="add-products">
        <h1 class="title">Books</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <h3>Add Book</h3>
            <input type="text" name="name" class="box" placeholder="Enter book name" required>
            <input type="number" min="0" name="price" class="box" placeholder="Enter book price" required>
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
            <input type="number" min="0" name="quantity" class="box" placeholder="Enter quantity" required>
            <select name="grade" class="box">
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
            <select name="type" class="box">
                <option value="SelahEltelmeez">سلاح التلميذ</option>
                <option value="Elamthan">الإمتحان</option>
                <option value="Aladwaa">الأضواء</option>
                <option value="Elmoasser">المعاصر</option>
                <option value="Bravo">برافو</option>
                <option value="Other">اخري</option>
            </select>
            <input type="submit" value="Add Book" name="add_product" class="btn">
        </form>
    </section>

    <section class="show-products">
        <div class="box-container">
            <?php
            $select_books = mysqli_query($conn, "SELECT * FROM `books`") or die('query failed');
            if (mysqli_num_rows($select_books) > 0) {
                while ($fetch_books = mysqli_fetch_assoc($select_books)) {
                    ?>
                    <div class="box">
                        <img style=" width: 250px;" src="uploaded_img/<?php echo $fetch_books['image']; ?>" alt="">
                        <div class="name"><?php echo $fetch_books['name']; ?></div>
                        <div class="price"><?php echo htmlspecialchars($fetch_books['price'], 2); ?> EGP</div>
                        <div class="grade"><?php echo $fetch_books['grade']; ?></div>
                        <div class="type"><?php echo $fetch_books['type']; ?></div>
                        <div class="quantity">Quantity: <?php echo $fetch_books['quantity']; ?></div>
                        <a href="admin_productsbook.php?update=<?php echo $fetch_books['id']; ?>" class="option-btn">Update</a>
                        <a href="admin_productsbook.php?delete=<?php echo $fetch_books['id']; ?>" class="delete-btn"
                            onclick="return confirm('Delete this book?');">Delete</a>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="empty">No books added yet!</p>';
            }
            ?>
        </div>
    </section>

    <section class="edit-product-form">
        <?php
        if (isset($_GET['update'])) {
            $update_id = $_GET['update'];
            $update_query = mysqli_query($conn, "SELECT * FROM `books` WHERE id = '$update_id'") or die('query failed');
            if (mysqli_num_rows($update_query) > 0) {
                while ($fetch_update = mysqli_fetch_assoc($update_query)) {
                    ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                        <input type="hidden" name="update_old_image" value=" <?php echo $fetch_update['image']; ?>">
                        <img style=" width:22%; height:10%;" src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                        <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required
                            placeholder="Enter book name">
                        <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box"
                            required placeholder="Enter book price">
                        <input type="number" name="update_quantity" value="<?php echo $fetch_update['quantity']; ?>" min="0"
                            class="box" required placeholder="Enter quantity">
                        <select name="update_grade" class="box">
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
                        <select name="update_type" class="box">
                            <option value="SelahEltelmeez">سلاح التلميذ</option>
                            <option value="Elamthan">الإمتحان</option>
                            <option value="Aladwaa">الأضواء</option>
                            <option value="Elmoasser">المعاصر</option>
                            <option value="Bravo">برافو</option>
                            <option value="Other">اخري</option>
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
            window.location.href = 'admin_productsbook.php';

        }
    </script>

</body>

</html>