<html>
<head>
    <title>Hotels Under</title>
    <link rel="stylesheet" type="text/css" media="all" data-expandcss="true" href="//www.priceline.com/hotels-in/content/css/style.css?v=s13">
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="//www.priceline.com/hotels-in/content/js/date.js"></script>
    <script src="/keyboardless-hotels/content/js/index.js"></script>
    
    <style>
    input[type="text"] {
        width: 440px;
        font-size: 22px;
        margin: 10px 0 0 10px;
        padding: 2px 6px 4px 40px;
        border: 1px solid #999;
        color: #222;
        font-weight: normal;
        height: 44px;
        -webkit-border-radius: 0;
        border-radius: 0;
        background: #fff url(//www.priceline.com/hotels-in/content/graphics/search-icon.png) no-repeat 12px 48%;
    }
    
    button {
        margin: 10px;
    }
    
    #recoStatus {
        font-size: 25px;
        margin: 10px;
    }
    </style>
</head>
<body>

<?php

$page = preg_replace("/\s/", "%20", $_SERVER["PATH_INFO"]);

if (isset($page) && $page != "") {
?>
<input type="text" name="search" id="search" placeholder="hotel attribute" x-webkit-speech="" onwebkitspeechchange="$.fn.speechChangeHandler();" autocomplete="off">
<br/><br/>

<?php
} else {
?>
<button onclick="reco.toggleStartStop(); document.getElementById('recoStatus').innerHTML='Where would you like to go?'; return false;">
    Speak!
</button>
<div id="recoStatus">
</div>
<script>
    var listing = {};
</script>
<?php
}
?>
<br/><br/>
<div class="hotelListings">
<?php

if (isset($page) && $page != "") {
    exec("curl https://www.priceline.com/api/hotelretail/listing/v3".$page."/900", $array);
    $jsonString = $array[0];
    
    //echo "https://www.priceline.com/api/hotelretail/listing/v3".$page."/900";
    echo "<script>";
    echo "var listing = " . $jsonString . ";";
    echo "</script>";
    
    $listings = json_decode($jsonString, true);
    
    for ($i=0; $i < count($listings["hmiSorted"]); $i++) {
        $hotelID = $listings["hmiSorted"][$i];
        $hotel = $listings["hotels"][$hotelID];
        $amenities = @implode(", ", $hotel["amenities"]);
        $hotelName = $hotel["hotelName"];
        $brandName = $hotel["brandName"];
        $neighborhood = $hotel["neighborhood"];
        $uberAttribute = strtolower($amenities . "|" . $hotelName . "|" . $brandName . "|" . $neighborhood);
        $merchPrice = round($hotel["merchPrice"], 0);
        $address = $hotel["address"]["cityName"];
        
        $proximity = round($hotel["proximity"], 1);
        
        $searchCity = $listings["city"]["cityName"];
        $proximityFrom = $searchCity . " (city center)";
        
        if (isset($listings["city"]["searchedLocation"])) {
            $searchType = $listings["city"]["searchedLocation"]["type"];
            if ($searchType == "LATLON" || $searchType == "POI") {
                if (!isset($listings["city"]["searchedLocation"]["seType"]) || $listings["city"]["searchedLocation"]["seType"] != "PopulatedPlace") {
                    $proximityFrom = $listings["city"]["searchedLocation"]["itemName"];
                }
            }
        }
        
        $img = substr($hotelID, 0, strlen($hotelID)-3) . "/" . $hotelID;
        
        $url = $page . "/h" . $hotelID;
        ?>
        <a href="https://www.priceline.com/hotels-in/area-of#<?=$url?>">
            <div data-role="retailListItem" class="hotelListItem" hotelID="<?=$hotelID?>" merchprice="<?=$merchPrice?>" starrating="2" guestrating="4.710280418395996" uber="<?=$uberAttribute?>" style="display: none;">
                <div class="hotel-thumb"><img src="//mobileimg.priceline.com/htlimg/<?=$img?>/thumbnail-200-landscape.jpg"></div>
                <div class="listedHotel group">
                    <div class="hotelInfo">
                        <strong class="hotelName"><?=$hotelName?></strong>
                        <div class="star-reviews group">
                            <div class="star-rating stars2"><span class="visuallyhidden">2 stars</span></div>
                            <span class="_hotelListItemReviews review-score">Guest Score <span class="ratingpointpoint-in"></span><span class="ratingpointpoint-out"></span><strong>4.7</strong><span> / 10</span></span>
                            <span class="review-text"> 71 reviews</span>
                        </div>
                        <div class="address"><?=$address?></div>
                        <div class="proxmity"><?=$proximity?> mi from <?=$proximityFrom?></div>
                    </div>
                    <div class="hotellistprice">
                        <div class="price">
                            <button class="dollar button primary large">$<?=$merchPrice?><span class="post-price"> / night</span></button>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </a>
        <?php
    }
?>
</div>

<br/><br/>
<button id="guestcheckout">Checkout</button>

<form id="payment" hidden action="" method="post">
  <input autocomplete="cc-name" name="myname">
  <input autocomplete="cc-number" name="ccnumber">
  <input autocomplete="cc-exp" name="ccexp">
  <input autocomplete="cc-csc" name="cccvc">
  <input autocomplete="billing address-line1" name="billaddress">
  <input autocomplete="billing locality" name="billtown">
  <input autocomplete="billing region" name="billstate">
  <input autocomplete="billing postal-code" name="billzip">
</form>
<br/><br/><br/>
<?php
}
?>
</body>
</html>
