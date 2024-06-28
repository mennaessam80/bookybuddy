<style>
.head0 {
    background-color: var(--blue);
    text-align: center;
    color: var(--white);
    font-size: 1.6rem;
    padding: 2px;
}

.header {
    position: sticky;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background-color: var(--white);
    box-shadow: var(--box-shadow);
}

.header .flex {
    display: flex;
    align-items: center;
    padding: 2rem;
    justify-content: space-between;
    position: relative;
    max-width: 1200px;
    height: 83px;
    margin: 0 auto;
}

.header .flex .logo {
    position: absolute;
    top: 3px;
    margin: 20px;
    font-size: 2.5rem;
    color: var(--black);
}

.premium-btn {
    display: inline-block;
    margin-top: 1rem;
    padding: 1rem 3rem;
    cursor: pointer;
    color: var(--white);
    font-size: 1.8rem;
    border-radius: 0.5rem;
    text-transform: capitalize;
}

.premium-btn.normal {
    background-color: gray;
}

.premium-btn.premium {
    background-color: gold;
}

.premium-btn.normal:hover,
.premium-btn.premium:hover {
    background-color: black;
}
</style>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">
    <div class="head0">
        <p>Complete your order of 500 EGP and get free delivery</p>
    </div>

    <div class="header-2">
        <div class="flex">
            <div>
                <img src="logore.png" alt="Registration Image" style="float: left; max-width: 82px; margin: 0px;">
                <a href="home.php" class="logo">booky <span>buddy</span></a>
            </div>

            <nav class="navbar" style="padding: 0px 0px 0px 88px;">
                <a href="home.php">home</a>
                <a href="about.php">about</a>
                <a href="shop.php">shop</a>
                <a href="exa.php">exams</a>
                <a href="contact.php">contact</a>
                <a href="orders.php">orders</a>
            </nav>

            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <a href="search_page.php" class="fas fa-search"></a>
                <div id="user-btn" class="fas fa-user"></div>
                <?php
                    $select_cart_number = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
                    $cart_rows_number = mysqli_num_rows($select_cart_number); 
                ?>
                <a href="cart.php"> <i class="fas fa-shopping-cart"></i>
                    <span>(<?php echo $cart_rows_number; ?>)</span>
                </a>
            </div>

            <div class="user-box">
                <p>username : <span><?php echo $_SESSION['user_name']; ?></span></p>
                <p>email : <span><?php echo $_SESSION['user_email']; ?></span></p>
                <a href="logout.php" class="delete-btn">logout</a>
                <br>

                <?php
                    if(isset($_SESSION['user_id'])){
                        $user_id = $_SESSION['user_id'];
                        $result = mysqli_query($conn, "SELECT payment_status FROM subscriptions WHERE user_id = '$user_id' AND payment_status = 'completed'");
                        if(mysqli_num_rows($result) > 0){
                            echo '<a href="exa.php" class="premium-btn premium">You are Premium</a>';
                        } else {
                            echo '<a href="subscription_checkout.php" class="premium-btn normal">Be Premium</a>';
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</header>