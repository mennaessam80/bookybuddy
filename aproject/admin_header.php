<?php

if(isset($message)){
   if(is_array($message)) {
      foreach($message as $msg){
         echo '
         <div class="message">
            <span>'.$msg.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   } else {
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
    <div class="flex">
        <div>
            <img src=" logore.png" alt="Registration Image" style=" float: left; max-width: 82px; margin: 0px;  ">
            <a href=" admin_page.php" class="logo">Admin<span>Panel</span></a>
        </div>

        <nav class="navbar" ">
            <a href=" admin_page.php">home</a>
            <a href="admin_productsbook.php">products</a>
            <a href="admin_productsexa.php">exams</a>
            <a href="admin_orders.php">orders</a>
            <a href="admin_users.php">users</a>
            <a href="admin_contacts.php">messages</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div> <!-- bas d han3mlo display none-->
            <div id="user-btn" class="fas fa-user"></div>
        </div>

        <div class="account-box">
            <p>username : <span><?php echo $_SESSION['admin_name']; ?></span></p>
            <p>email : <span><?php echo $_SESSION['admin_email']; ?></span></p>
            <a href="logout.php" class="delete-btn">logout</a>
            <div>new <a href="login.php">login</a> | <a href="register.php">register</a></div>
        </div>

    </div>

</header>