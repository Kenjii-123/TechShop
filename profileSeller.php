<?php
ob_start();
$pageTitle = 'Profile';
$imagesUploades = "data/uploads/items/";
include "init.php";
// if not signed in redirect to sign-in page
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    return;
}
if(!isset($_GET['id'])){
if (isset($_GET['delete_id'])) {
    shallowDeleteItem($_GET['delete_id'], $db);
    $Name_childcategoryID = getChildCategoryId($_GET['delete_id'], $db);
    deleteTotalItems($Name_childcategoryID, 0,$_GET['delete_id'], $db);
    header("Location: profileSeller.php");
    return;
}
if (isset($_GET['retrieve_id'])) {
    retrieveItem($_GET['retrieve_id'], $db);
    $Name_childcategoryID = getChildCategoryId($_GET['retrieve_id'], $db);
    $quantity = getItemQuantity($_GET['retrieve_id'], $db);
    retriveTotalItems($Name_childcategoryID, $quantity, $_GET['retrieve_id'], $db);
    header("Location: profileSeller.php");
    return;
}
if (isset($_GET['permanentlyDelete_id'])) {
    permanentlyDeleteItem($_GET['permanentlyDelete_id'], $db);
    header("Location: profileSeller.php");
    return;
}
$sellerData = getSeller($db, $_SESSION['username'])[0];
$sellerMobiles = getSellerMobiles($_SESSION['id'], $db);
$forSaleItems = getSellerForSaleItems($_SESSION['id'], $db);
$soldItems = getSellerSoldOutItems($_SESSION['id'], $db);
$deletedItems = getSellerDeletedItems($_SESSION['id'], $db);


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


<div class="container p-3 position-static">
  <div
    class="row shadow rounded p-3 m-5 text-lg-start text-md-center text-sm-center border-start border-5 border-success">
    <div class="col-lg-3 m-auto">
      <h1 class="card-title" style = "font-family:candara;letter-spacing: 0.05em;font-weight:bold;"><?= $sellerData["userName"] ?></h1>
    </div>
    <div class="col-lg-7 m-auto">
      <div class="m-2">
        <h4 class="d-inline-block" style = "font-family:candara;letter-spacing: 0.05em; font-weight:bold;">Email: </h4>
        <a href="mailto:<?= $sellerData["email"] ?>" class="mb-2 link-dark fa-1x ">
          <h5 class="text-muted d-inline-block" style = "font-family:candara;letter-spacing: 0.05em;">
            <?= $sellerData["email"] ?>
          </h5>
        </a>
      </div>
      
      <div class="m-2">
        <h4 class="d-inline-block" style = "font-family:candara;letter-spacing: 0.05em; font-weight:bold;">Balance: </h4>
        <a href="mailto:<?= $sellerData["Amount"] ?>" class="mb-2 link-dark fa-1x ">
          <h5 class="text-muted d-inline-block" style = "font-family:candara;letter-spacing: 0.05em;">
            <?= $sellerData["Amount"] ?>$
          </h5>
        </a>
      </div>
      <div class="m-2">
        <h4 class="d-inline-block" style = "font-family:candara;letter-spacing: 0.05em; font-weight:bold;">Mobile: </h4>
        <h5 class="mb-2 text-muted d-inline-block " style = "font-family:candara;letter-spacing: 0.05em;">
          <ul class="list-group list-group-flush profile_scroll" style="max-height: 120px;overflow: auto">
            <?php
                            foreach ($sellerMobiles as $mobile) {
                                echo "<li class='list-group-item'> $mobile->phoneNo</li>";
                            }
                            ?>

          </ul>
        </h5>
      </div>
      <div class="m-2">
        <h4 class="d-inline-block" style = "font-family:candara;letter-spacing: 0.05em; font-weight:bold;">Join date: </h4>
        <h5 class=" mb-2 text-muted d-inline-block" style = "font-family:candara;letter-spacing: 0.05em;"><?= $sellerData["joinDate"] ?></h5>
      </div>
    </div>
    <div class="col-lg-2 m-auto">
      <a href="EditProfile.php" class="link-dark"><i class="bi bi-pencil fa-3x"></i></a>
    </div>
  </div>



  <!------------------------------------------------>
  <!--   Dashboard   -->
  <!------------------------------------------------>
  <div class="row justify-content-around m-3">
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="header-title mt-0 mb-4">Total Reviews</h4>
          <div class="row flex-row flex-nowrap justify-content-evenly">
            <div style="width: fit-content">
              <i class="bi bi-people-fill fa-4x"></i>
            </div>
            <div class="text-end" style="width: fit-content">
              <h2 class="fw-normal pt-2 mb-1 text-center"> <?= $sellerData["likes"] + $sellerData["disLikes"] ?> </h2>
              <p class="text-muted mb-1 text-center" style = "font-family:candara;letter-spacing: 0.05em;">Review</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="header-title mt-0 mb-4">Total likes</h4>
          <div class="row flex-row flex-nowrap justify-content-evenly">
            <div style="width: fit-content">
              <i class="bi bi-hand-thumbs-up fa-4x"></i>
            </div>
            <div class="text-end" style="width: fit-content">
              <h2 class="fw-normal pt-2 mb-1 text-center"> <?= $sellerData["likes"] ?> </h2>
              <p class="text-muted mb-1 text-center" style = "font-family:candara;letter-spacing: 0.05em;">Like</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="header-title mt-0 mb-4">Total dislikes</h4>
          <div class="row flex-row flex-nowrap justify-content-evenly">
            <div style="width: fit-content">
              <i class="bi bi-hand-thumbs-down fa-4x"></i>
            </div>
            <div class="text-end" style="width: fit-content">
              <h2 class="fw-normal pt-2 mb-1 text-center"> <?= $sellerData["disLikes"] ?> </h2>
              <p class="text-muted mb-1 text-center" style = "font-family:candara;letter-spacing: 0.05em;">Dislike</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="header-title mt-0 mb-4">Total transactions</h4>
          <div class="row flex-row flex-nowrap justify-content-evenly">
            <div style="width: fit-content">
              <i class="bi bi-cash-coin fa-4x"></i>
            </div>
            <div class="text-end" style="width: fit-content">
              <h2 class="fw-normal pt-2 mb-1 text-center"> <?= $sellerData["transactions"] ?> </h2>
              <p class="text-muted mb-1 text-center" style = "font-family:candara;letter-spacing: 0.05em;">Transaction</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!------------------------------------------------>
  <!--   For sale   -->
  <!------------------------------------------------>
  <div class="row justify-content-around" id="forSale">
    <div class="btn-group" role="group" aria-label="Basic example">
      <a href="#forSale" class="btn btn-success m-2 rounded-pill btn-lg" style = "font-family:candara;letter-spacing: 0.05em;">Items for sale</a>
      <a href="#sold" class="btn btn-success m-2 rounded-pill btn-lg" style = "font-family:candara;letter-spacing: 0.05em;">Sold Items</a>
      <a href="#deleted" class="btn btn-success m-2 rounded-pill btn-lg" style = "font-family:candara;letter-spacing: 0.05em;">Deleted Items</a>
    </div>

    <div class="row justify-content-between m-3">
      <div class="col-lg-8 jumbotron m-3">
        <div class="container">
          <h1 class="display-4" style = "font-family:candara;letter-spacing: 0.05em;font-weight:bold;">For sale</h1>
          <hr class="my-4">

          <p class="lead" style = "font-family:candara;letter-spacing: 0.05em;">List of all items that are offered for sale.</p>
        </div>
      </div>
      <div class="col-lg-3 m-0" style="position: sticky; right: 0;">
        <a href="add_item.php" class="link-dark">
          <div class="card m-md-auto shadow" style="width: 18rem;">
            <div class="m-auto">
              <i class="bi bi-plus-circle fa-6x"></i>
            </div>
            <div class="card-body">
              <h5 class="card-title text-center" style = "font-family:candara;letter-spacing: 0.05em;">Add Item</h5>
            </div>
          </div>
        </a>
      </div>
    </div>
    <div class="col-sm-12">

      <section class="row flex-row flex-nowrap p-3 overflow-auto profile_scroll rounded position-static "
        style="gap: 60px;">
        <?php
                    foreach ($forSaleItems

                    as $forSaleItem) {
                    $imageName = getImageOfAnItem($forSaleItem->itemId,$db);
                    $childcategory = getChildCategory($forSaleItem->childcategoryId, $db)[0];
                    $countOrders = getPendingOrdersCount($forSaleItem->itemId,$db);
                    echo '
                    <div class="col-lg-3 m-0 text-center">
                        <div class="card m-md-auto shadow" style="width: 18rem;">
                             <a style = "font-family:candara;letter-spacing: 0.05em;"href="orders.php?itemid='.$forSaleItem->itemId.'" class="btn btn-danger rounded-pill position-absolute"
                               style="width: fit-content; top: 0;right: 0">
                                <span class="badge">'.$countOrders.'</span></a>
                                '; ?>

        <a href="reviewItem.php?do=Manage&itemId=<?= $forSaleItem->itemId?>&itemName=<?=$forSaleItem->title?>"
          style = "font-family:candara;letter-spacing: 0.05em;">
          <?php
                        if($imageName){
                            echo'<img src="'.$imagesUploades. $imageName[0]->image .' " class="card-img-top" alt="Item">';
                        } else {
                            echo'<img src="'.$imagesUploades. 'default.png" class="card-img-top" alt="Item">';
                        }
                        ?>
          <?php echo '       
                    <div class="card-body">
                                <h5 class="card-title" >' . $forSaleItem->title . '</h5>
                                
                                <h6 class="card-title">' . $childcategory->childcategoryName . '</h6>
                                <p class="card-text" style = "font-family:candara;letter-spacing: 0.05em;">' . $forSaleItem->description . '</p>
                                <h4 class="card-title">' . $forSaleItem->price . '$</h4>
                                <h6 class="card-title">' . $forSaleItem->addDate . '</h6>
                                <div class="card-body">
                                    <a href="editItem.php?id=' . $forSaleItem->itemId . '" class="btn btn-success" style = "font-family:candara;letter-spacing: 0.05em;">Edit</a>
                                    <a href="profileSeller.php?delete_id=' . $forSaleItem->itemId . '" class="btn btn-danger" style = "font-family:candara;letter-spacing: 0.05em;">Delete</a>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                    ';
                        } ?>
      </section>
    </div>
  </div>


  <!------------------------------------------------>
  <!--   Sold  out  -->
  <!------------------------------------------------>
  <div class="row justify-content-around" id="sold">
    <div class="jumbotron jumbotron-fluid m-3">
      <div class="container">
        <h1 class="display-4" style = "font-family:candara;letter-spacing: 0.05em;font-weight:bold;">Sold Out</h1>
        <hr class="my-4">
        <p class="lead" style = "font-family:candara;letter-spacing: 0.05em;">List of all Sold items.</p>
      </div>
    </div>
    <div class="col-sm-12 ">
      <section class="row flex-row flex-nowrap p-3 overflow-auto profile_scroll rounded position-static "
        style="gap: 60px;">
        <?php
                    foreach ($soldItems

                    as $soldItem) {
                    $imageName = getImageOfAnItem($soldItem->itemId,$db);
                    $childcategory = getChildCategory($soldItem->childcategoryId, $db)[0];
                    $countOrders = getPendingOrdersCount($soldItem->itemId,$db);
                    echo '
                    <div class="col-lg-3 m-0 text-center">
                        <div class="card m-md-auto shadow" style="width: 18rem;">
                        <a href="orders.php?itemid='.$soldItem->itemId.'" style="z-index: 12" class="btn btn-danger rounded-pill position-absolute"
                        style = "font-family:candara;letter-spacing: 0.05em;">
                                <span class="badge">'.$countOrders.'</span></a>
                                '; ?>
        <a href="reviewItem.php?do=Manage&itemId=<?= $soldItem->itemId?>&itemName=<?=$soldItem->title?>"
          style="text-decoration: none;color: black;filter:grayscale(100%)">

          <?php
                        if($imageName){
                            echo'<img src="'.$imagesUploades. $imageName[0]->image .' " class="card-img-top" alt="Item">';
                        } else {
                            echo'<img src="'.$imagesUploades. 'default.png" class="card-img-top" alt="Item">';
                        }
                        ?>
          <?php echo '       
                    <div class="card-body">
                                <h5 class="card-title">' . $soldItem->title . '</h5>
                                <h6 class="card-title">' . $childcategory->childcategoryName . '</h6>
                                <p class="card-text" style = "font-family:candara;letter-spacing: 0.05em;">' . $soldItem->description . '</p>
                                <h4 class="card-title">' . $soldItem->price . '$</h4>
                                <h6 class="card-title">' . $soldItem->addDate . '</h6>
                                <div class="card-body">
                                    <a href="editItem.php?id=' . $soldItem->itemId . '" class="btn btn-success" style = "font-family:candara;letter-spacing: 0.05em;">Edit</a>
                                    <a href="profileSeller.php?delete_id=' . $soldItem->itemId . '" class="btn btn-danger" style = "font-family:candara;letter-spacing: 0.05em;">Delete</a>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                    ';
                        } ?>

      </section>
    </div>
  </div>


  <!------------------------------------------------>
  <!--   Deleted   -->
  <!------------------------------------------------>
  <div class="row justify-content-around" id="deleted">
    <div class="col-sm-12">
      <div class="jumbotron jumbotron-fluid m-3">
        <div class="container">
          <h1 class="display-4" style = "font-family:candara;letter-spacing: 0.05em;font-weight:bold;">Deleted</h1>
          <hr class="my-4">
          <p class="lead" style = "font-family:candara;letter-spacing: 0.05em;">List of all deleted items.</p>
        </div>
      </div>
      <section class="row flex-row flex-nowrap p-3 overflow-auto profile_scroll rounded position-static"
        style="gap: 60px;">

        <?php
                    foreach ($deletedItems

                    as $deletedItem) {
                    $imageName = getImageOfAnItem($deletedItem->itemId,$db);
                    $childcategory = getChildCategory($deletedItem->childcategoryId, $db);
                    echo '
                    <div class="col-lg-3 m-0 text-center">
                        <div class="card m-md-auto shadow" style="width: 18rem;">
                                '; ?>
        <a href="reviewItem.php?do=Manage&itemId=<?= $deletedItem->itemId?>&itemName=<?=$deletedItem->title?>"
          style="text-decoration: none;color: black;filter:grayscale(70%)">

          <?php
                        if($imageName){
                            echo'<img src="'.$imagesUploades. $imageName[0]->image .' " class="card-img-top" alt="Item">';
                        } else {
                            echo'<img src="'.$imagesUploades. 'default.png" class="card-img-top" alt="Item">';
                        }
                        ?>
          <?php echo '       
                    <div class="card-body">
                                <h5 class="card-title">' . $deletedItem->title . '</h5>
                                <p class="card-text" style = "font-family:candara;letter-spacing: 0.05em;">' . $deletedItem->description . '</p>
                                <h4 class="card-title">' . $deletedItem->price . '$</h4>
                                <h6 class="card-title">' . $deletedItem->addDate . '</h6>
                                <div class="card-body">
                                    <a href="profileSeller.php?retrieve_id=' . $deletedItem->itemId . '" class="btn btn-success" style = "font-family:candara;letter-spacing: 0.05em;">Retrieve</a>
                                    <a href="profileSeller.php?permanentlyDelete_id=' . $deletedItem->itemId . '"  id="stopRedirect" class="btn btn-danger" onclick="return permanentlyDeleteItem()" style = "font-family:candara;letter-spacing: 0.05em;">Delete</a>
                                </div>
                            </div>
                    </a>
                        </div>
                    </div>
                    ';
                        } ?>

      </section>
    </div>
  </div>

</div>
<?php
}
else{

$sellerData = getSellerId($db, $_GET['id'])[0];
$sellerMobiles = getSellerMobiles($_GET['id'], $db);
$forSaleItems = getSellerForSaleItems($_GET['id'], $db);
$soldItems = getSellerSoldOutItems($_GET['id'], $db);
$deletedItems = getSellerDeletedItems($_GET['id'], $db);


?>


<div class="container p-3 position-static">
  <div
    class="row shadow rounded p-3 m-5 text-lg-start text-md-center text-sm-center border-start border-5 border-success">
    <div class="col-lg-3 m-auto">
      <h1 class="card-title"><?= $sellerData["userName"] ?></h1>
    </div>
    <div class="col-lg-7 m-auto">
      <div class="m-2">
        <h4 class="d-inline-block">Email: </h4>
        <a href="mailto:<?= $sellerData["email"] ?>" class="mb-2 link-dark fa-1x ">
          <h5 class="text-muted d-inline-block">
            <?= $sellerData["email"] ?>
          </h5>
        </a>
      </div>
      <div class="m-2">
        <h4 class="d-inline-block">Mobile: </h4>
        <h5 class="mb-2 text-muted d-inline-block ">
          <ul class="list-group list-group-flush profile_scroll" style="max-height: 120px;overflow: auto">
            <?php
                            foreach ($sellerMobiles as $mobile) {
                                echo "<li class='list-group-item'> $mobile->phoneNo</li>";
                            }
                            ?>

          </ul>
        </h5>
      </div>
      <div class="m-2">
        <h4 class="d-inline-block">Join date: </h4>
        <h5 class=" mb-2 text-muted d-inline-block"><?= $sellerData["joinDate"] ?></h5>
      </div>
    </div>

  </div>


  <!------------------------------------------------>
  <!--   Dashboard   -->
  <!------------------------------------------------>
  <div class="row justify-content-around m-3">
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="header-title mt-0 mb-4">Total Reviews</h4>
          <div class="row flex-row flex-nowrap justify-content-evenly">
            <div style="width: fit-content">
              <i class="bi bi-people-fill fa-4x"></i>
            </div>
            <div class="text-end" style="width: fit-content">
              <h2 class="fw-normal pt-2 mb-1 text-center"> <?= $sellerData["likes"] + $sellerData["disLikes"] ?> </h2>
              <p class="text-muted mb-1 text-center">Review</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="header-title mt-0 mb-4">Total likes</h4>
          <div class="row flex-row flex-nowrap justify-content-evenly">
            <div style="width: fit-content">
              <i class="bi bi-hand-thumbs-up fa-4x"></i>
            </div>
            <div class="text-end" style="width: fit-content">
              <h2 class="fw-normal pt-2 mb-1 text-center"> <?= $sellerData["likes"] ?> </h2>
              <p class="text-muted mb-1 text-center">Like</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="header-title mt-0 mb-4">Total dislikes</h4>
          <div class="row flex-row flex-nowrap justify-content-evenly">
            <div style="width: fit-content">
              <i class="bi bi-hand-thumbs-down fa-4x"></i>
            </div>
            <div class="text-end" style="width: fit-content">
              <h2 class="fw-normal pt-2 mb-1 text-center"> <?= $sellerData["disLikes"] ?> </h2>
              <p class="text-muted mb-1 text-center">Dislike</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card">
        <div class="card-body">
          <h4 class="header-title mt-0 mb-4">Total transactions</h4>
          <div class="row flex-row flex-nowrap justify-content-evenly">
            <div style="width: fit-content">
              <i class="bi bi-cash-coin fa-4x"></i>
            </div>
            <div class="text-end" style="width: fit-content">
              <h2 class="fw-normal pt-2 mb-1 text-center"> <?= $sellerData["transactions"] ?> </h2>
              <p class="text-muted mb-1 text-center">Transaction</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!------------------------------------------------>
  <!--   For sale   -->
  <!------------------------------------------------>
  <div class="row justify-content-around" id="forSale">
    <div class="btn-group" role="group" aria-label="Basic example">
      <a href="#forSale" class="btn btn-success m-2 rounded-pill btn-lg">Items for sale</a>
      <a href="#sold" class="btn btn-success m-2 rounded-pill btn-lg">Sold Items</a>
      <!-- <a href="#deleted" class="btn btn-success m-2 rounded-pill btn-lg">Deleted Items</a> -->
    </div>

    <div class="row justify-content-between m-3">
      <div class="col-lg-8 jumbotron m-3">
        <div class="container">
          <h1 class="display-4">For sale</h1>
          <hr class="my-4">

          <p class="lead">List of all items that are offered for sale.</p>
        </div>
      </div>
    </div>
    <div class="col-sm-12">

      <section class="row flex-row flex-nowrap p-3 overflow-auto profile_scroll rounded position-static "
        style="gap: 60px;">
        <?php
                    foreach ($forSaleItems

                    as $forSaleItem) {
                    $imageName = getImageOfAnItem($forSaleItem->itemId,$db);
                    $childcategory = getchildcategory($forSaleItem->childcategoryId, $db)[0];
                    $countOrders = getPendingOrdersCount($forSaleItem->itemId,$db);
                    echo '
                    <div class="col-lg-3 m-0 text-center">
                        <div class="card m-md-auto shadow" style="width: 18rem;">
                                '; ?>

        <a href="reviewItem.php?do=Manage&itemId=<?= $forSaleItem->itemId?>&itemName=<?=$forSaleItem->title?>"
          style="text-decoration: none;color: black">
          <?php
                        if($imageName){
                            echo'<img src="'.$imagesUploades. $imageName[0]->image .' " class="card-img-top" alt="Item">';
                        } else {
                            echo'<img src="'.$imagesUploades. 'default.png" class="card-img-top" alt="Item">';
                        }
                        ?>
          <?php echo '       
                    <div class="card-body">
                                <h5 class="card-title">' . $forSaleItem->title . '</h5>
                                
                                <h6 class="card-title">' . $childcategory->childcategoryName . '</h6>
                                <p class="card-text">' . $forSaleItem->description . '</p>
                                <h4 class="card-title">' . $forSaleItem->price . '$</h4>
                                <h6 class="card-title">' . $forSaleItem->addDate . '</h6>
                                <div class="card-body">
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                    ';
                        } ?>
      </section>
    </div>
  </div>


  <!------------------------------------------------>
  <!--   Sold  out  -->
  <!------------------------------------------------>
  <div class="row justify-content-around" id="sold">
    <div class="jumbotron jumbotron-fluid m-3">
      <div class="container">
        <h1 class="display-4">Sold Out</h1>
        <hr class="my-4">
        <p class="lead">List of all Sold items.</p>
      </div>
    </div>
    <div class="col-sm-12 ">
      <section class="row flex-row flex-nowrap p-3 overflow-auto profile_scroll rounded position-static "
        style="gap: 60px;">
        <?php
                    foreach ($soldItems

                    as $soldItem) {
                    $imageName = getImageOfAnItem($soldItem->itemId,$db);
                    $childcategory = getchildcategory($soldItem->childcategoryId, $db)[0];
                    $countOrders = getPendingOrdersCount($soldItem->itemId,$db);
                    echo '
                    <div class="col-lg-3 m-0 text-center">
                        <div class="card m-md-auto shadow" style="width: 18rem;">
                                '; ?>
        <a href="reviewItem.php?do=Manage&itemId=<?= $soldItem->itemId?>&itemName=<?=$soldItem->title?>"
          style="text-decoration: none;color: black;filter:grayscale(100%)">

          <?php
                        if($imageName){
                            echo'<img src="'.$imagesUploades. $imageName[0]->image .' " class="card-img-top" alt="Item">';
                        } else {
                            echo'<img src="'.$imagesUploades. 'default.png" class="card-img-top" alt="Item">';
                        }
                        ?>
          <?php echo '       
                    <div class="card-body">
                                <h5 class="card-title">' . $soldItem->title . '</h5>
                                <h6 class="card-title">' . $childcategory->childcategoryName . '</h6>
                                <p class="card-text" style = "font-family:candara;letter-spacing: 0.05em;">' . $soldItem->description . '</p>
                                <h4 class="card-title">' . $soldItem->price . '$</h4>
                                <h6 class="card-title">' . $soldItem->addDate . '</h6>
                                <div class="card-body">
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                    ';
                        } ?>

      </section>
    </div>
  </div>


  <!------------------------------------------------>
  <!--   Deleted   -->
  <!------------------------------------------------>


</div>

<?php
}
include $tpl . "footer.php";ob_end_flush(); ?>