<?php 
/*
Template Name: Enter Info
*/
?>

<?php if ( !is_user_logged_in() ) {
nocache_headers();
header("HTTP/1.1 302 Moved Temporarily");
header('Location: ' . get_settings('siteurl') . '/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
header("Status: 302 Moved Temporarily");
exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edge Map</title>
	 <style>
      html { height: 100%; margin: 0px; padding: 0px; }
	  body { position:absolute; top:0; left:0; height: 100%; width:100%; margin: 0px; padding: 0px; }
	  #addEntry { float:none; margin-left:auto; margin-right:auto; width:500px; height:500px; margin-top:5%; border:1px solid black; padding:20px; }
	  .subRow { float:left; height:auto; width:100%; margin-top:1%; clear:right; }
	  .formTitle { float:left; font-size:1.5vw; font-weight:bold; margin-right:1vw; }
    </style>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script>
		var autocomplete, place, getAdd, getType, geoLoc, getDesc, geocoder, formPic;
		
		function PostPic(imgNum) {
		var form = $('#imageInput').prop("files")[0];
		if (form != 'undefined') {
		var formData = new FormData();
		formData.append("file", form);
			$.ajax({
				url: '/wp-content/themes/onetone/EdgeMap/connect/formatImage.php?imgNum='+imgNum,
				dataType: 'text',
				cache: false,
				contentType: false,
				processData: false,
				data: formData,                       
				type: 'post',
				success: function() { window.location.href = "http://systemoverflow.com/show-map/"; }
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
		$.ajax({
            type: "POST",
            url: "/wp-content/themes/onetone/EdgeMap/connect/addEntry.php",
			data: { Lat: geoLoc['A'], Lng: geoLoc['F'], Addr: getAdd, rType: getType, Desc: getDesc },
            success: function(data) { var getCount = data; PostPic(getCount); }
			});
				})

			})
		}

		$(document).ready(initialize);
	</script>
	</head>
	<body>
	<div style="width:40%; height:500px; float:none; margin-left:auto; margin-right:auto; margin-top:5%; padding:20px; border:1px solid black;">
	<div class="subRow"><span class="formTitle">Address: </span><input id="autocomplete" type="autocomplete" type="text" style="float:left; width:70%; height:3vh;" /></div>
	<div class="subRow"><span class="formTitle">Roofing Material: </span><label for="type1">Asphalt</label><input id="type1" name="material" type="radio" value="1" style="margin-right:20px;" checked="checked"/><label for="type2">Tile</label><input id="type2" name="material" type="radio" value="2" style="margin-right:20px;"/><label for="type3">Wood</label><input id="type3" name="material" type="radio" style="margin-right:20px;" value="3"/><label for="type4">Metal</label><input id="type4" name="material" type="radio" value="4"/></div>
	<div class="subRow"><span class="formTitle">Description: </span><input id="descript" type="descript" type="text" style="float:left; width:70%; height:3vh;" /></div>
	<div class="subRow" style="margin-bottom:2%;">
			<input name="imageInput" id="imageInput" type="file" />
	</div>
	<button id="getGeo" name="getGeo">Submit Entry</button>
	</div>
	</body>
	</html>