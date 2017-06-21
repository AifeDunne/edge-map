<?php
/*
Template Name: Show Map
*/
include_once 'wp-content/themes/onetone/EdgeMap/connect/connectDB.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Edge Map</title>
    <style>
      html { height: 100%; margin: 0px; padding: 0px; }
	  body { position:absolute; top:0; left:0; margin: 0px; padding: 0px; }
	  #map-canvas { float:none; margin-left:440px; height: 560px; width:720px; margin-top:80px; }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <script>
	var overlay, map, marker, markPosition, infowindow;
function initialize() {
  var myLatlng = new google.maps.LatLng(35.549931, -97.515507);
  var mapOptions = {
    zoom: 10,
    center: myLatlng
  }
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
   overlay = new google.maps.OverlayView();
   overlay.draw = function () {};
   overlay.setMap(map);
   setMarkers(map, allLocations);
  }
  <?php
  $markArr = 'var allLocations = [';
  $comArr = 'var commentArr = [';
  $retrieveLoc = "SELECT locID, Lat, Longit, locAddr, locName, roofType, locDesc, hasPicture FROM locCount";
  $getDesc = 'var getDesc = [';
  if ($retLoc = $mysqli->prepare($retrieveLoc)) {
		$retLoc->execute();
		$retLoc->bind_result($locID, $Lat, $Long, $locAddr, $locName, $rType, $locDesc, $hPic);
		while ($retLoc->fetch()) {
		$formatLoc = "['".$locName."', ".$Lat.", ".$Long.", ".$locID."], ";
		$getDesc.= "'".$locDesc."', ";
		$markArr.= $formatLoc;
		if ($hPic === 1) { $comArr.= '"'."<div id='content-wrapper' style='min-width:440px; max-height:250px;'><div style='float:left; width:220px; padding-left:2%;'><img src='/wp-content/themes/onetone/EdgeMap/images/loc".$locID.".jpg'/></div>";	} else { $comArr.= '"<div '."id='content-wrapper' ".'style='."'min-width:430px; max-height:250px;'>"; }
		$formatAddr = explode(',',$locAddr);
		$firstLine = $formatAddr[0];
		$secondLine = $formatAddr[1];
		$thirdLine = $formatAddr[2];
		$ini1 = $thirdLine[1]; $ini2 = $thirdLine[2];
		$secondLine = $secondLine.', '.$ini1.$ini2;
		$thisAddr = $firstLine."<br>".$secondLine;
		$middleEntry = "<div style='float:right; width:200px; padding-left:2%; border-left:1px solid black;margin-bottom:20px;'><b style='float:left;font-size:1.2vw;'>".$thisAddr."</b><br><img src='/wp-content/themes/onetone/EdgeMap/assets/Material".$rType.".jpg' style='vertical-align:bottom; float:left; margin-top:15px;'/></div>";
		if (!empty($locDesc)) {
		$thirdEntry = "<div style='float:left; clear:both; font-size:1vw; border-top:1px solid black;'><b>Description: </b>".$locDesc.'</div></div>", ';
		} else { $thirdEntry = '</div>", '; }
		$comArr.= $middleEntry.$thirdEntry;
		}
		$retLoc->close();
		$markArr = substr($markArr,0,-2);
		$comArr = substr($comArr,0,-2);
		$getDesc = substr($getDesc,0,-2);
		$markArr.= '];';
		$comArr.= '];';
		$getDesc.= '];';
		echo $markArr;
		echo $comArr;
		echo $getDesc;
		} else { printf ($mysqli->error); }
  ?>

  function setMarkers(map, locations) {
  	infowindow = new google.maps.InfoWindow({maxWidth: 440});
	var image = {
    url: '/wp-content/themes/onetone/EdgeMap/assets/HouseX3.png',
    size: new google.maps.Size(40, 40),
    origin: new google.maps.Point(0,0),
    anchor: new google.maps.Point(20, 20)
  };
  var shape = {
      coords: [21, 4, 33, 21, 32, 39, 11, 39, 11, 18, 5, 18],
      type: 'poly'
  };
    for (var i = 0; i < locations.length; i++) {
    var markerLoc = locations[i];
    var myLatLng = new google.maps.LatLng(markerLoc[1], markerLoc[2]);
    marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
		icon: image,
        shape: shape,
        title: markerLoc[0],
        zIndex: markerLoc[3]
    });
	 google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(commentArr[i]);
		  infowindow.open(map, marker);
        }
      })(marker, i));
	}
}
google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
  </body>
</html>