<?php

/**
 * CREATE A TEMPORARY IMAGE FILE
 * =============================
 * This has grown a lt and really should be elsewhere, perhaps the model.
 * The image data is stored in the database record as a file name for the
 * backend, where it is initially loaded, but it also holds the actual image
 * data for use at the front end.
 */
//
//    // -----------------------------------------------------------------------
//    // Creating the image using the raw data is intensive and needs a lot of ram
//    // -----------------------------------------------------------------------
//    ini_set("memory_limit","256M");
//
//    // -----------------------------------------------------------------------
//    // Find the ttf font to use for the text output. The same font should ideally
//    // be used in the editor at the back end
//    // -----------------------------------------------------------------------
//    if (!defined('TTF_DIR')) {
//        if (!array_key_exists('fontLocation', Yii::$app->params)) {
//            die('You must define params[\'fontLocation\'') ;
//
//        } else {
//            DEFINE("TTF_DIR", Yii::$app->params['fontLocation']);
//        }
//
//        if (!array_key_exists('fontFile', Yii::$app->params)) {
//            die('You must define params[\'fontFile\'') ;
//
//        } else {
//            DEFINE("TTF_FONTFILE", Yii::$app->params['fontFile']);
//        }
//    }
//
//    // -----------------------------------------------------------------------
//    // Get the gd object, based on the uploaded image type
//    // -----------------------------------------------------------------------
//    switch ($product['leaflet']['image_type']) {
//        case 'image/jpeg':
//            $image = imagecreatefromjpeg($product['leaflet']['image']);
//            break ;
//        case 'image/png':
//            $image = imagecreatefrompng($product['leaflet']['image']);
//            break ;
//        case 'image/gif':
//            $image = imagecreatefromgif($product['leaflet']['image']);
//            break ;
//        default:
//            return ;
//    }
//
//    // -----------------------------------------------------------------------
//    // Create a text box with the desired font and size and output the key
//    // -----------------------------------------------------------------------
//    $fontFile = TTF_DIR . TTF_FONTFILE ;
//    $fontSize = 55 ;
//    $angle = 0 ;
//    //$text = 'YF3CN-3WXXY-7XXM2-YXX86-2XXMF'; // Debug / Demo
//    $text = $product['key'] ;
//
//    $dimensions = imagettfbbox($fontSize, $angle, $fontFile, $text ) ;
//
//    // -----------------------------------------------------------------------
//    // Using the output from the above, we can calculate the actual width and
//    // height of the text in pixels.
//    // -----------------------------------------------------------------------
//    $dimensionsOfText = new stdClass();
//    $dimensionsOfText->width = abs($dimensions[2] - $dimensions[0]);
//    $dimensionsOfText->height = abs($dimensions[1] - $dimensions[7]);
//
//    // -----------------------------------------------------------------------
//    // Now do the same for the area allocated to the key, and the difference
//    // gives the offsets to output the text so that it is both vertically and
//    // horizontally centered in the defined area.
//    // -----------------------------------------------------------------------
//    $dimensionsOfSpace = new stdClass();
//    $dimensionsOfSpace->width = $product['leaflet']['key_box_width'] ;
//    $dimensionsOfSpace->height = $product['leaflet']['key_box_height'] ;
//
//    $xoffset = abs($dimensionsOfSpace->width - $dimensionsOfText->width)/2;
//    $yoffset = abs($dimensionsOfSpace->height - $dimensionsOfText->height)/2;
//
//    // -----------------------------------------------------------------------
//    // Can now write the text on to the image, including the set x and y coords
//    // -----------------------------------------------------------------------
//    $black = ImageColorAllocate($image, 0, 0, 0);
//    $xpos = $product['leaflet']['key_xcoord'] ;
//    $ypos = $product['leaflet']['key_ycoord'] - $dimensions[5] ;
//
//    imagettftext($image, $fontSize, $angle, $xpos+$xoffset, $ypos+$yoffset, $black, $fontFile, $text);
//
//
//    // -----------------------------------------------------------------------
//    // Add the account logo if the space was allocated
//    // -----------------------------------------------------------------------
////    if ($product['leaflet']['logo_box_width'] && $product['leaflet']['logo_box_height'] && $accountLogo) {
////        $new = imagecreate($new_width, $new_height);
////        imagecopy($new, $top, 0, 0, 0, 0, $top_width, $top_height);
////        imagecopy($new, $bottom, 0, $top_height + 1, 0, 0, $bottom_width, $bottom_height);
////
////    }
//
//    // -----------------------------------------------------------------------
//    // Allocate the temporary file and write the image to it
//    // -----------------------------------------------------------------------
//
//    $workImg = tempnam(Yii::getAlias('@frontend') . '/runtime/tmp', 'LEF') . '.jpg';
//
//    imagejpeg ($image, $workImg) ;

?>
<body>

    <div class="page" style="background-image: url(<?= $workImg ?>);  "></div>
    <div class="product-key" style="left:900px; top:500px; border:2px solid red;">

    </div>

</body>
