let navbar = document.querySelector('.header .navbar');
let accountBox = document.querySelector('.header .account-box');
// lma aft fe shkl osyr aw 3l mobil 22dr a5le al nav bar tro7 ta7t w ttl3 le fo2
document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   accountBox.classList.remove('active');
}
//hana by7ml al user botton nafs al klam
document.querySelector('#user-btn').onclick = () =>{
   accountBox.classList.toggle('active');
   navbar.classList.remove('active');
}

window.onscroll = () =>{
   navbar.classList.remove('active');
   accountBox.classList.remove('active');
}

// document.querySelector('#close-update').onclick = () =>{
//    document.querySelector('.edit-product-form').style.display = 'none';
//    window.location.href = 'admin_products.php';
// }