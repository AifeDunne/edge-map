<?php
include_once 'connectDB.php';

if (empty($_FILES['file']['tmp_name'];)) {
$source = $_FILES['file']['tmp_name'];
$getCount = $_GET['imgNum'];
$destination = "../images/loc".$getCount.".jpg";
$maxsize = 200;

$size = getimagesize($source);
$height_orig = $size[0];
$width_orig = $size[1];
unset($size);
$width = $maxsize+1;
$height = $maxsize;
while($width > $maxsize){
    $width = round($height*$width_orig/$height_orig);
    $height = ($width > $maxsize)?--$height:$height;
}
unset($height_orig,$width_orig,$maxsize);
$images_orig    = imagecreatefromstring( file_get_contents($source) );
$photoX         = imagesx($images_orig);
$photoY         = imagesy($images_orig);
$images_fin     = imagecreatetruecolor($height,$width);
imagesavealpha($images_fin,true);
$trans_colour   = imagecolorallocatealpha($images_fin,0,0,0,127);
imagefill($images_fin,0,0,$trans_colour);
unset($trans_colour);
ImageCopyResampled($images_fin,$images_orig,0,0,0,0,$height+1,$width+1,$photoX,$photoY);
unset($photoX,$photoY,$height,$width);
imagepng($images_fin,$destination);
unset($destination);
ImageDestroy($images_orig);

$addPic = $mysqli->query("UPDATE locCount SET hasPicture = 1 WHERE locName = 'RoofEntry".$getCount."'");
}
?>