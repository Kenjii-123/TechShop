<?php
ob_start();
//get the name of the item to update the header
$itemName = isset($_GET['itemName']) ? $_GET['itemName'] : 0;
if (!$itemName) {
  header("Location: index.php");
}
$pageTitle = $itemName;
include 'init.php';

if (isset($_SESSION['typeOfUser']) && $_SESSION['typeOfUser'] == "admin") {
  header("Location: admin/index.php");
}

$itemId = isset($_GET['itemId']) && is_numeric($_GET['itemId']) ? intval($_GET['itemId']) : 0;
if (!$itemId) {
  header("Location: index.php");
}

$do = isset($_GET['do'])? $_GET['do'] : 'Manage';

$item = GetItemByID($itemId, $db)[0];
$images = GetImagesByID($itemId, $db);

if ($do == 'Manage') {
?>

<?php if(isset($_GET['keyword'])): ?>
					<?php if ($inputSearchError) :?>
					<p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%">Enter a valid value!</p>
					<?php elseif(($noItemsSearch)): ?>
					<p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%">No items match this word
						<?php echo " " .$_GET['keyword']; ?> </p>
					<?php elseif($noItems): ?>
					<p class="alert-danger ms-auto me-auto pt-5 pb-5" style="width:50%">No items in this Category</p>
					<?php else: ?>
						<?php header("Location: searchItem.php?keyword=".$_GET['keyword']); ?>
					<?php endif ?>
          <?php endif ?>


<div class="container mt-5 mb-5" style = "box-shadow: rgba(0, 0, 0, 0.4) 0px 0px 10px; border-radius: 10px">
  <section class="review-item" >
    <div class="gallery">
      <!-- the main image -->
      <div id="screen">
        <?php 
          if (!empty($images)) {
            echo '<img src="' . $dataimages . $images[0]['image'] . '" alt="primary">';
          } else {
            echo '<img src="' . $dataimages . 'default.png" alt="primary">';
          }
        ?>
      </div>
      <div class="thumbnails">
        <?php 
          for($i = 0; $i < count($images); $i++) {
            echo '<img src="' . $dataimages . $images[$i]['image'] . '" alt="primary">';
          }
        ?>
      </div>
    </div>
    <div class="product" >
      <a class="seller-name" style = "font-family:candara;font-weight:bold;font-size:16px; text-decoration:none; color:black;"
        href="seller.php?id=<?php echo $item['sellerId'] ?>">By:
        <?php echo $item['fName'] . ' ' . $item['lName']; ?></a>
      <hr>
      <span class="date-of-item" >Added in : <?php echo $item['addDate']; ?></span>
      <?php 
        if ($item['quantity'] == 0) {
          echo '<span class="sold-out">Sold Out</span>';
        }
      ?>
      <p class="item-name" ><?php echo $itemName; ?></p>
      <p class="description" style = "font-family:candara;font-size:18px;"><?php echo $item['description']; ?></p>
      <div class="price">
        <?php 
          if ($item['discount'] == 0) {
            echo '<div class="new-price">';
            echo $item['price'] . " $";
            echo '</div>';
          }else {
            echo '<div class="new-price" >';
            echo $item['price'] - ($item['price'] * ($item['discount']/100)) . " $";
            echo '</div>';
            echo '<div class="discount">';
            echo $item['discount'] . "%";
            echo '</div>';
            echo '<div class="old-price" style="font-size: 18px;" >';
            echo $item['price'] . " $";
            echo '</div>';
          }
        ?>
      </div>
      <?php
        if (isset($_SESSION['typeOfUser']) && $_SESSION['typeOfUser'] == 'buyer' && $item['quantity'] != 0) {
          $cartID = GetCartIDFromBuyer($_SESSION['id'], $db)[0]['cartId'];
          if (CheckBuyerAndItem($cartID, $itemId, $db)) {
            echo '
            <form action="?do=Update&itemId=' . $itemId . '&itemName=' . $itemName . '" class="order-section" method="POST">
            <div class="counter">
              <span class="left-btn" onclick="ereasing()"><i class="fas fa-minus"></i></span>
              <input type="number" id="amount" min="1" name="quan">
              <span class="right-btn" onclick="adding(' . $item['quantity'] . ')"><i class="fas fa-plus"></i></span>
              </div>
              <button class="add-to-cart-btn">
                <i class="fas fa-shopping-cart"></i>
                <span style = "font-family:candara;font-weight:bold;font-size:20px;">Edit The Cart</span>
              </button>
            </form>';
          } elseif ($item['quantity'] != 0) {
      ?>
      <form action="?do=Confirm&itemId=<?php echo $itemId ?>&itemName=<?php echo $itemName; ?>" class="order-section"
        method="POST">
        <div class="counter">
          <span class="left-btn" onclick="ereasing()"><i class="fas fa-minus"></i></span>
          <input type="number" id="amount" min="1" name="quan" value="1">
          <span class="right-btn" onclick="adding(<?php echo $item['quantity']; ?>)"><i class="fas fa-plus"></i></span>
        </div>
        <button class="add-to-cart-btn" >
          <i class="fas fa-shopping-cart"></i>
          <span style = "font-family:candara;font-weight:bold;font-size:20px;">Add to cart</span>
        </button>
      </form>
      <?php 
          if (isset($_SESSION['trueQuantity'])) {
            echo '<div class="alert-danger p-3 mt-2" style="width: fit-content; border-radius: 15px;">';
            echo 'Max Quantity is ' . $_SESSION['trueQuantity'];
            unset($_SESSION['trueQuantity']);
            echo '</div>';
          }
        ?>
      <?php } } ?>
    </div>
  </section>
</div>


<?php 
} elseif ($do == 'Confirm' && isset($_SESSION['id']) && $_SESSION['typeOfUser'] == "buyer") {
  $num = $_POST['quan'];
  $trueQuantity = QuantityOfItem($item['itemId'], $db)[0]['quantity'];
  if ($num > $trueQuantity) {
    $_SESSION['trueQuantity'] = $trueQuantity;
    header("Location: ?do=Manage&itemId=" . $_GET['itemId'] . "&itemName=" . $_GET['itemName'] . "");
  }
  if (isset($_POST['submit'])) {
    //add this item to the cart of that buyer(session_id)
    $num = $_GET['quantity'];
    //get the cart id from the buyer info
    $cartID = GetCartIDFromBuyer($_SESSION['id'], $db)[0]['cartId'];
    //update the itemCount and payment in cart
    $price = $num * ($item['price'] - ($item['price'] * ($item['discount']/100)));
    UpdateItemCount($cartID, $price, $db);
    //insert into the cart item table
    InsertCartItem($cartID, $item['itemId'], $num, $db);
    header("Location: cart.php");
  } else {
  $num = $_POST['quan'];
  if ($num <= 0) {
    header("Location: reviewitem.php?do=Manage&itemId=" . $itemId . "&itemName=" . $itemName);
  } else {
?>

<div class="container shadow add-to-cart" style="line-height: 2.5;">
  <h1 class="text-center">Add to Cart</h1>
  <div class="info-section" >
    <div class="item-name" style="font-size: 20px"><b style="font-size: 20px">Item:</b> <?php echo $item['title']; ?></div>
    <div class="final-price" style="font-size: 20px"><b style="font-size: 20px">Price:</b> <?php echo $item['price'] - ($item['price'] * ($item['discount']/100)); ?> $
    </div>
    
    <!-- <div class="quantity"><b>Quantity:</b> <//?php echo $num; ?></div> -->
    <div class="quantity" style="font-size: 20px"><b style="font-size: 20px">Quantity:</b> <?php echo $num; ?></div>
    <div class="total-price" style="font-size: 20px"><b style="font-size: 20px">Total Price:</b>
      <?php echo $num * ($item['price'] - ($item['price'] * ($item['discount']/100))); ?> $</div>
    <div class="location" style="font-size: 20px"><b style="font-size: 20px">Location:</b> <?php echo $item['homeNumber'] . ', ' .
                                  $item['street'] . ' ' . $item['city'] . ' ' . $item['country'];?></div>
  </div>
  <form action="?do=Confirm&itemId=<?php echo $itemId ?>&itemName=<?php echo $itemName; ?>&quantity=<?php echo $num; ?>"
    method="POST" class="text-center" method="POST">
    <button type="submit" name="submit" class="btn btn-success" style = "font-family:candara;letter-spacing: 0.05em;">Confirm</button>
    <a class="btn btn-danger" href="<?php echo '?do=Manage&itemId=' . $itemId . '&itemName=' . $itemName; ?>" style = "font-family:candara;letter-spacing: 0.05em;">Go Back</a>
  </form>
</div>

<?php
    }
  }
} elseif ($do == 'Update' && isset($_SESSION['id']) && $_SESSION['typeOfUser'] == "buyer") {
  if (isset($_POST['submit'])) {
    $cartID = GetCartIDFromBuyer($_SESSION['id'], $db)[0]['cartId'];
    //we need to delete the tuple with cartID and itemID
    $num = $_GET['quantity'];
    $oldnum = SelectQuantityOfItem($cartID, $itemId, $db)[0]['quantity'];
    $newPrice = $num * ($item['price'] - ($item['price'] * ($item['discount']/100)));
    $oldPrice = $oldnum * ($item['price'] - ($item['price'] * ($item['discount']/100)));
    UpdateItemCountPrice($cartID, $oldPrice, $newPrice, $db);
    UpdateCartItem($cartID, $itemId, $num, $db);
    header("Location: cart.php");
  } else {
  $num = $_POST['quan'];
  if ($num <= 0) {
    header("Location: reviewitem.php?do=Manage&itemId=" . $itemId . "&itemName=" . $itemName);
  } else {
?>

<div class="container shadow add-to-cart" style="line-height: 2.5;">
  <h1 class="text-center">Edit The Cart</h1>
  <div class="info-section">
    <div class="item-name" style="font-size: 20px"><b style="font-size: 20px">Item:</b> <?php echo $item['title']; ?></div>
    <div class="final-price"style="font-size: 20px"><b style="font-size: 20px">Price:</b> <?php echo $item['price'] - ($item['price'] * ($item['discount']/100)); ?> $
    </div>
    <div class="quantity"style="font-size: 20px"><b style="font-size: 20px">Quantity:</b> <?php echo $num; ?></div>
    <div class="total-price"style="font-size: 20px"><b style="font-size: 20px">Total Price:</b>
      <?php echo $num * ($item['price'] - ($item['price'] * ($item['discount']/100))); ?> $</div>
    <div class="location"style="font-size: 20px"><b style="font-size: 20px">Location:</b> <?php echo $item['homeNumber'] . ', ' .
                                  $item['street'] . ' ' . $item['city'] . ' ' . $item['country'];?></div>
  </div>
  <form action="?do=Update&itemId=<?php echo $itemId ?>&itemName=<?php echo $itemName; ?>&quantity=<?php echo $num; ?>"
    method="POST" class="text-center" method="POST">
    <button type="submit" name="submit" class="btn btn-success" style = "font-family:candara;letter-spacing: 0.05em;">Confirm</button>
    <a class="btn btn-danger" href="<?php echo '?do=Manage&itemId=' . $itemId . '&itemName=' . $itemName; ?>" style = "font-family:candara;letter-spacing: 0.05em;">Go
      Back</a>
  </form>
</div>

<?php
    }
  }
}

include $tpl . 'footer.php';
ob_end_flush();
?>