<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'] ?? null;

// if(!isset($user_id)){
//    header('location:login.php');
// };

if(isset($_POST['add_to_wishlist'])){

   $pid = $_POST['pid'];
   $pid = filter_var($pid, FILTER_SANITIZE_STRING);
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);

   $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
   $check_wishlist_numbers->execute([$p_name, $user_id]);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_wishlist_numbers->rowCount() > 0){
      $message[] = 'Already added to wishlist!';
   }elseif($check_cart_numbers->rowCount() > 0){
      $message[] = 'Already added to cart!';
   }else{
      $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
      $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
      $message[] = 'Added to wishlist!';
   }

}

if(isset($_POST['add_to_cart'])){
   $pid = $_POST['pid'];
   $pid = filter_var($pid, FILTER_SANITIZE_STRING);
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name, FILTER_SANITIZE_STRING);
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price, FILTER_SANITIZE_STRING);
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image, FILTER_SANITIZE_STRING);
   // $p_qty = $_POST['p_qty'];
   // $p_qty = filter_var($p_qty, FILTER_SANITIZE_STRING);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_cart_numbers->rowCount() > 0){
      $message[] = 'Already added to cart!';
   }else{

      $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
      $check_wishlist_numbers->execute([$p_name, $user_id]);

      if($check_wishlist_numbers->rowCount() > 0){
         $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
         $delete_wishlist->execute([$p_name, $user_id]);
      }

      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
      $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
      $message[] = 'Added to cart!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home Page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="home-bg">


   <section class="home">

      <div class="content">
         <span>Tradition in Every Touch,<br>
             Craftsmanship in Every Piece....</span>
         <h3>Experience Sri Lanka’s heritage through exquisite handcrafted creations.</h3>
         <p>Sri Lanka’s handmade crafts showcase the island’s rich heritage and skilled artistry. Each piece, from woven palmyrah items to carved wooden treasures, tells a story of tradition and culture.</p>
         <a href="about.php" class="btn">About us</a>
      </div>

   </section>

</div>

<section class="home-category">

   <h1 class="title">Select by Category</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/palmyra.jpg" alt="">
         <h3>Palmyrah & Cane Items</h3>
         <p>Eco-friendly handwoven creations such as baskets, mats, bags, and storage boxes that blend tradition with everyday use.</p>
         <a href="category.php?category=palmyrah" class="btn">Select</a>
      </div>

      <div class="box">
         <img src="images\wood.jpeg" alt="">
         <h3>Wooden Crafts & Carvings</h3>
         <p>Authentic Sri Lankan wooden masks, statues, kitchenware, and furniture crafted with skill and cultural artistry.</p>
         <a href="category.php?category=wood" class="btn">Select</a>
      </div>

      <div class="box">
         <img src="images\Batik_Wear_in_Sri_Lanka.jpg"alt="">
         <h3>Cloth & Handloom Products</h3>
         <p>Handloom and batik textiles including sarees, sarongs, scarves, and bags, celebrating vibrant island heritage.</p>
         <a href="category.php?category=cloth" class="btn">Select</a>
      </div>

      <div class="box">
         <img src="images\Sri-Lanka-Pottery.jpg" alt="Sri-Lanka-Pottery">
         <h3>Clay & Pottery</h3>
         <p>Traditional clay pots, vases, tableware, and kitchenware designed for natural living and timeless style.</p>
         <a href="category.php?category=clay" class="btn">Select</a>
      </div>

   </div>

</section>

<section class="products">

   <h1 class="title">Latest products</h1>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 3");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" class="box" method="POST">
      <div class="price">$<span><?= $fetch_products['price']; ?></span>/-</div>
      <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
      <input type="submit" value="add to wishlist" class="option-btn" name="add_to_wishlist">
      <input type="submit" value="add to cart" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products added yet!</p>';
   }
   ?>

   </div>

</section>







<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>