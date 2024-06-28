<?php

include 'config.php';

if(isset($_POST['submit'])){
// namozg (bt2kd mn wgod al 7rof wl klam ) tam arsalo mn zrar asmo submit 
   $name = mysqli_real_escape_string($conn, $_POST['name']);//btdef sceep cachter 2abl al 7rof ale hawdeha fl data base(hatro7 b tare2a monzma)tawsl b nafs 7rofha  hatro7 kd /a/l/i 
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));//tashfer al password 
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
   $user_type = $_POST['user_type'];
   $access = ($user_type == 'admin') ? 'rejected' : 'accepted';
   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');//ba7s lw fe most5dm nafs al asm 

   if(mysqli_num_rows($select_users) > 0){
      $message[] = 'user already exist!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         mysqli_query($conn, "INSERT INTO users(name, email, password, user_type, access) VALUES('$name', '$email', '$cpass', '$user_type', '$access')") or die('query failed');
         $message[] = 'registered successfully!';
         header('location:login.php');
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
    <title>register</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/aa.css">

</head>

<body>
    <!-- 
       //60//l2no mesh hytb3 fo2 
      //61//byt2kd an mesg leha kema
      //61//kol mesg hatt5zn fe mot8yr asmo mesg 
      //63//m3mol fl css
         //65//ale hya 3lmt al X
          //65//هذه الوظيفة تستخدم لإزالة العنصر الحالي من DOM (نموذج الكائن للوثيقة). في هذه الحالة، عنصر الأب <div> الذي يحتوي على الرسالة سيتم إزالته عند النقر على الأيقونة.
          //لذا، بمجرد أن ينقر الشخص على الأيقونة "x" (أو أيقونة الإغلاق)، سيتم إزالة الرسالة التي تحتوي عليها هذه الأيقونة من الصفحة. -->


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


    <div class="form-container">

        <form action="" method="post">
            <div style="text-align: center;">
                <img src="logooo.jpg" alt="Registration Image" style="max-width: 30%; height: auto;">
            </div>
            <h3>register now</h3>
            <input type="text" name="name" placeholder="enter your name" required class="box">
            <input type="email" name="email" placeholder="enter your email" required class="box">
            <input type="password" name="password" placeholder="enter your password" required class="box">
            <input type="password" name="cpassword" placeholder="confirm your password" required class="box">
            <select name="user_type" class="box">
                <option value="user">user</option>
                <option value="admin">admin</option>
            </select>
            <input type="submit" name="submit" value="register now" class="btn">
            <p>already have an account? <a href="login.php">login now</a></p>
        </form>

    </div>

</body>

</html>