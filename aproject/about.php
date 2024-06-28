<style>
.w {
    width: 30px;
    align-items: center;
    text-align: center;
}
</style><?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>about</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/aa.css">

</head>

<body>

    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>about us</h3>
        <p> <a href="home.php">home</a> / about </p>
    </div>

    <section class="about">

        <div class="flex">

            <div class="content">
                <div>
                    <img src="images/ai.jpg" alt=""
                        style="   display: block; margin-left: auto; margin-right: auto;width:45%;">
                </div>

                <h3>why choose us?</h3>
                <p>As we approach the year 2024, the importance of external school books as a key tool for expanding
                    knowledge and enhancing learning is becoming increasingly evident.


                    <b>Booky Buddy is the first website to offer the convenience of purchasing external school
                        books
                        online,</b> giving readers easy access to rich and diverse content. By using the Booky Buddy
                    website,
                    students can search for external books and other school supplies.
                </p>
                <div>
                    <img src="images/aiiii.jpg" alt=""
                        style="   display: block; margin-left: auto; margin-right: auto;width:45%;">
                </div>
                <p>
                    We also offer <b>exams for different educational stages,</b> from KG1 to the higher grades. Our
                    comprehensive range of exams is designed to cater to various academic levels, ensuring that
                    students have access to the appropriate materials to excel in their studies. Our stores and
                    libraries provide <b>a wide selection of school supplies at affordable prices for all ages and
                        tastes.</b> For young girls and boys, there is a delightful range of cute and trendy school
                    supplies that meet their diverse needs. </p>
                <div>
                    <img src="images/aii.jpg" alt=""
                        style="   display: block; margin-left: auto; margin-right: auto;width:45%;">
                </div>
                <p> Booky Buddy will <b>launch a special initiative at the start of the first week and the last
                        week
                        of
                        the
                        education term</b> to help students sell their old external school books and earn money.
                    This
                    initiative
                    provides a platform for students to list their used external school books, making it easy
                    for others
                    to
                    purchase them at affordable prices. By participating, students can not only declutter their
                    shelves
                    but
                    also make some extra cash, promoting a sustainable and economical way to access educational
                    materials.
                    Join us in this exciting venture and make the most of your external school books with Booky
                    Buddy!
                </p>
                <div>
                    <img src="images/aiii.jpg" alt=""
                        style="   display: block; margin-left: auto; margin-right: auto;width:45%;">
                </div>
                <p> At Booky Buddy, we partner with the <b>best delivery companies to ensure the safety and
                        timely
                        arrival
                        of your orders.</b> Our collaboration with reputable carriers guarantees that your books
                    and
                    school
                    supplies are handled with care and delivered right to your doorstep. We prioritize the
                    security of
                    your purchases, so you can shop with confidence, knowing that your items will reach you in
                    excellent
                    condition. Enjoy a hassle-free shopping experience with Booky Buddy, where your satisfaction
                    is our
                    top priority. </p>
                <a href="contact.php" class="btn"
                    style="   display: block; margin-left: auto; margin-right: auto;text-align: center;">contact
                    us</a>
            </div>

        </div>

    </section>




    <?php include 'footer.php'; ?>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

</body>

</html>