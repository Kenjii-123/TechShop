<?php
ob_start();
require "init.php";
if (!isset($_GET["id"])) {
  echo '404';
  return;
}
$imagesUploades = "data/uploads/items/";
$sellerData = getSellerId($db, $_GET['id'])[0];

if (!isset($sellerData)) {
  echo 'Seller not found';
}
$sellerMobiles = getSellerMobiles($_GET['id'], $db);
$forSaleItems = getSellerForSaleItems($_GET['id'], $db);
$soldItems = getSellerSoldOutItems($_GET['id'], $db);
$deletedItems = getSellerDeletedItems($_GET['id'], $db);

?>


<div class="container p-3 position-static">
  <div
    class="row shadow rounded p-3 m-5 text-lg-start text-md-center text-sm-center border-start border-5 border-success">
    <div class="col-lg-3 m-auto">
      <h1 class="card-title">
        <?= $sellerData["userName"] ?>
      </h1>
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
        <h5 class=" mb-2 text-muted d-inline-block">
          <?= $sellerData["joinDate"] ?>
        </h5>
      </div>
    </div>

  </div>