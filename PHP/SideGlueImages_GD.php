<?php




/**
 * $images_base64 == array with images, base64 encoded
 * returns all images glued side by side (ltr) as base64
 */

function SideGlueImages($images_base64){
  if(!is_array($images_base64)) $images_base64 = array($images_base64);
  $merged_width = 0;
  $merged_height = 0;
  for($x=0 ; $x < count($images_base64);$x++){
      $img[$x] = imagecreatefromstring(base64_decode($images_base64[$x]));
      list($imgwidth[$x], $imgheight[$x]) = [imagesx($img[$x]),imagesy($img[$x])];
      $merged_width += $imgwidth[$x];
      if($imgheight[$x] > $merged_height) $merged_height = $imgheight[$x];
  }
  $merged_image = imagecreatetruecolor($merged_width, $merged_height);
  imagealphablending($merged_image, false);
  imagesavealpha($merged_image, true);
  $xOffset = 0;
  for($x = 0; $x < count($img);$x++){
      $image = $img[$x];
      imagecopy($merged_image, $image, $xOffset, 0, 0, 0, $imgwidth[$x], $imgheight[$x]);
      $xOffset += $imgwidth[$x];
  }

  ob_start ();

  imagejpeg($merged_image);
  $image_data = ob_get_contents ();

  ob_end_clean ();

  return base64_encode($image_data);
}
