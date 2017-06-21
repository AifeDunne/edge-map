<?php
include_once 'connectDB.php';

$Lat = $_POST['Lat'];
$Lng = $_POST['Lng'];
$locType = $_POST['rType'];
$locAddr = $_POST['Addr'];
$locDesc = $_POST['Desc'];
if ($getCount = $mysqli->query("SELECT crntCount FROM addCount")) {
$nowCount = $getCount->fetch_array();
$crntCount = $nowCount['crntCount'];
$crntCount++;
$newName = "RoofEntry".$crntCount;
$insertThis = "INSERT INTO locCount VALUES (".$crntCount.", '".$Lat."', '".$Lng."', '".$locAddr."', '".$newName."', ".$locType.", '".$locDesc."', 0)";
if ($insertEntry = $mysqli->query($insertThis)) {
$updateCol = "UPDATE addCount SET crntCount = ".$crntCount;
$updateCount = $mysqli->query($updateCol);
echo $crntCount;
	} else { printf ($mysqli->error); }
}
?>