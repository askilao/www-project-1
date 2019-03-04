<?php

// scale function for thumbnails

function scale($img, $new_width, $new_height) {
    $old_x = imageSX($img);
    $old_y = imageSY($img);

    if($old_x > $old_y) {                     // Image is landscape mode
    $thumb_w = $new_width;
    $thumb_h = $old_y*($new_height/$old_x);
    } else if($old_x < $old_y) {              // Image is portrait mode
    $thumb_w = $old_x*($new_width/$old_y);
    $thumb_h = $new_height;
    } if($old_x == $old_y) {                  // Image is square
    $thumb_w = $new_width;
    $thumb_h = $new_height;
    }

    if ($thumb_w>$old_x) {                    // Don't scale images up
    $thumb_w = $old_x;
    $thumb_h = $old_y;
    }

    $dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);
    imagecopyresampled($dst_img,$img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);

    ob_start();                         // flush/start buffer
    imagepng($dst_img,NULL,9);          // Write image to buffer
    $scaledImage = ob_get_contents();   // Get contents of buffer
    ob_end_clean();                     // Clear buffer
    return $scaledImage;

}
