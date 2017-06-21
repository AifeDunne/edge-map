<?php 
/*
Template Name: Full System
*/
include_once 'connect/connectDB.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edge Map</title>
	 <style>
      html { height: 100%; margin: 0px; padding: 0px; }
	  body { position:absolute; top:0; left:0; height: 100%; width:100%; margin: 0px; padding: 0px; background:url(/wp-content/themes/TESSERACT-Master_Branch/EdgeMap/assets/EdgeBg.jpg);background-size: cover;background-repeat: no-repeat;background-attachment: fixed;background-position: center;}
	  #navBar { width:100%; height:60px; background:#000; }
	  #logo { float:left; width:255px; height:60px; margin-left:15vw; background:url(/wp-content/themes/TESSERACT-Master_Branch/EdgeMap/assets/TheEdgeW.png); }
	  #linkHolder {float:left; height:auto; width:auto; margin-left:3vw; margin-top:20px;}
	  .links { font-family: 'Roboto', sans-serif; font-size: 16px; color:white;  text-decoration:none !important; margin-right:2vw; }
	  .links:hover { text-decoration:underline !important; }
	  #addEntry { float:none; margin-left:auto; margin-right:auto; width:500px; height:500px; margin-top:5%; border:1px solid white; padding:20px; }
	  .subRow { float:left; height:auto; width:100%; margin-top:1%; clear:right; }
	  .formTitle { float:left; font-family: 'Roboto', sans-serif; font-size:25px; font-weight:bold; margin-right:1vw; }
	  #boxTitle { font-size:2.5vw; font-weight:bold; margin-top:2vh; margin-bottom:3vh; clear:both; font-family: 'Roboto', sans-serif; }
	  label { font-family: 'Roboto', sans-serif; }
	  #divLeft { width:40%; height:520px; float:left; margin-top:5%; margin-left:3.5%; padding:20px; border:1px solid white; background:rgba(0,0,0,0.7); color:#FFF; }
	  #RightDiv { float:right; width:50%; }
	  #map-canvas { float:none; height: 92vh; width:49.7vw; border:1px solid white;}
    </style>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	
	<script>
		var autocomplete, place, getAdd, getType, geoLoc, getDesc, geocoder, formPic;
		var overlay, map, marker, markPosition, infowindow;
		function PostPic(imgNum) {
		var form = $('#imageInput').prop("files")[0];
		if (form != 'undefined') {
		var formData = new FormData();
		formData.append("file", form);
			$.ajax({
				url: '/wp-content/themes/TESSERACT-Master_Branch/EdgeMap/connect/formatImage.php?imgNum='+imgNum,
				dataType: 'text',
				cache: false,
				contentType: false,
				processData: false,
				data: formData,                       
				type: 'post',
				success: function() { window.location.reload(); }
				})
			}
		}
		
function initialize() {
autocomplete = new google.maps.places.Autocomplete(
		(document.getElementById('autocomplete')),
			  { types: ['geocode'] });
		  google.maps.event.addListener(autocomplete, 'place_changed', function() {
		  place = autocomplete.getPlace();
		  });
		geocoder = new google.maps.Geocoder();
		$("#getGeo").on("click", function() {
		getAdd = $("#autocomplete").val();
		getType = $("[name='material']").val();
		getDesc = $("#descript").val();
		geocoder.geocode({ 'address': getAdd}, function(results) {
		geoLoc = results[0].geometry.location;
		if (geoLoc != 'undefined') {
		$.ajax({
            type: "POST",
            url: "/wp-content/themes/TESSERACT-Master_Branch/EdgeMap/connect/addEntry.php",
			data: { Lat: geoLoc['j'], Lng: geoLoc['C'], Addr: getAdd, rType: getType, Desc: getDesc },
            success: function(data) { var getCount = data; PostPic(getCount); }
			});
		} else { alert("That is not a valid address"); }
				})
			});
			
  var myLatlng = new google.maps.LatLng(35.571716, -97.515507);
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
		if ($hPic === 1) { $comArr.= '"'."<div id='content-wrapper' style='min-width:440px; max-height:250px;'><div style='float:left; width:220px; padding-left:2%;'><img src='/wp-content/themes/TESSERACT-Master_Branch/EdgeMap/images/loc".$locID.".jpg'/></div>";	} else { $comArr.= '"<div '."id='content-wrapper' ".'style='."'min-width:430px; max-height:250px;'>"; }
		$formatAddr = explode(',',$locAddr);
		$firstLine = $formatAddr[0];
		$secondLine = $formatAddr[1];
		$thirdLine = $formatAddr[2];
		$ini1 = $thirdLine[1]; $ini2 = $thirdLine[2];
		$secondLine = $secondLine.', '.$ini1.$ini2;
		$thisAddr = $firstLine."<br>".$secondLine;
		$middleEntry = "<div style='float:right; width:200px; padding-left:2%; border-left:1px solid black;margin-bottom:20px;'><b style='float:left;font-size:1.2vw;'>".$thisAddr."</b><br><img src='/wp-content/themes/TESSERACT-Master_Branch/EdgeMap/assets/Material".$rType.".jpg' style='vertical-align:bottom; float:left; margin-top:15px;'/></div>";
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
  	infowindow = new google.maps.InfoWindow({maxWidth: 440, disableAutoPan: true});
	var image = {
    url: '/wp-content/themes/TESSERACT-Master_Branch/EdgeMap/assets/HouseX3.png',
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

$(document).ready(initialize);
	</script>
	</head>
	<body>
	<div id="navBar"><div id="logo"></div><div id="linkHolder"><a class="links" href="/">Home</a><a class="links" href="/about-us/">About Us</a><a class="links" href="/services/">Services</a><a class="links" href="/contact-2/">Contact</a></div></div>
	<div id="divLeft">
	<div id="boxTitle">Add A New Entry</div>
	<div class="subRow"><span class="formTitle">Address: </span><input id="autocomplete" type="autocomplete" type="text" style="float:left; width:70%; height:3vh;" /></div>
	<div style="width:90%; background:white; height:1px; float:left; margin-left:auto; margin-right:auto; margin-top:20px; margin-bottom:20px;"></div>
	<div class="subRow"><span class="formTitle">Roofing Material: </span><div id="buttonHold" style="margin-top:5px;"><label for="type1">Asphalt</label><input id="type1" name="material" type="radio" value="1" style="margin-right:20px;" checked="checked"/><label for="type2">Tile</label><input id="type2" name="material" type="radio" value="2" style="margin-right:20px;"/><label for="type3">Wood</label><input id="type3" name="material" type="radio" style="margin-right:20px;" value="3"/><label for="type4">Metal</label><input id="type4" name="material" type="radio" value="4"/></div></div>
	<div style="width:90%; background:white; height:1px; float:left; margin-left:auto; margin-right:auto; margin-top:20px; margin-bottom:20px;"></div>
	<div class="subRow"><span class="formTitle">Description: </span><textarea id="descript" type="descript" style="float:left; width:65%; height:9vh;"></textarea></div>
	<div style="width:90%; background:white; height:1px; float:left; margin-left:auto; margin-right:auto; margin-top:25px; margin-bottom:10px;"></div>
	<div class="subRow" style="margin-bottom:2%;">
			<input name="imageInput" id="imageInput" type="file" />
	</div>
	<button id="getGeo" name="getGeo" style="float:left; clear:left; margin-top:3vh; width:200px; height:50px;">Submit Entry</button>
	</div>
	<div id="RightDiv">
	<div id="map-canvas"></div>
	</div>
	</body>
	</html>