<?php

namespace MorphGallery\Grafika\Gd;

use MorphGallery\Grafika\EditorInterface;
use MorphGallery\Grafika\Grafika;
use MorphGallery\Grafika\ImageType;
use MorphGallery\Grafika\Rectangle;
use MorphGallery\Grafika\Color;

/**
 * GD Editor class. Uses the PHP GD library.
 * @package Grafika\Gd
 */
final class Editor implements EditorInterface {

    /**
     * @var Image Holds the image instance.
     */
    private $image;

    /**
     * Constructor
     */
    function __construct() {
        $this->image = null;
    }


    /**
     * @return bool
     */
    public function isAvailable() {
        if ( false === extension_loaded( 'gd' ) || false === function_exists( 'gd_info' ) ) {
            return false;
        }

        // On some setups GD library does not provide imagerotate()
        if ( ! function_exists( 'imagerotate' ) ) {

            return false;
        }

        return true;
    }

    /**
     * Opens an image file for manipulation.
     *
     * @param string $file File path to image.
     *
     * @return self
     */
    public function open( $file ) {
        $this->image = Image::createFromFile( $file );

        return $this;
    }

    /**
     * Overlay an image on top of the current image.
     *
     * @param Image|string $overlay Can be a string containing a file path of the image to overlay or an Image object.
     * @param string|int $xPos Horizontal position of image. Can be 'left','center','right' or integer number. Defaults to 'center'.
     * @param string|int $yPos Vertical position of image. Can be 'top', 'center','bottom' or integer number. Defaults to 'center'.
     *
     * @param null $width
     * @param null $height
     *
     * @return Editor
     * @throws \Exception
     */
    public function overlay( $overlay, $xPos = 'center', $yPos = 'center', $width = null, $height = null ) {

        $this->_imageCheck();

        if ( is_string( $overlay ) ) { // If string passed, turn it into a Image object
            $overlay = Image::createFromFile( $overlay );
        }

        // Resize overlay
        if($width or $height){

            $overlayWidth = $overlay->getWidth();
            $overlayHeight = $overlay->getHeight();

            if(is_numeric($width)){
                $overlayWidth = (int) $width;
            } else {
                $percent  = strpos( $width, '%' );
                if( false !== $percent){
                    $overlayWidth = intval( $width ) / 100 * $this->image->getWidth();
                }
            }

            if(is_numeric($height)){
                $overlayHeight = (int) $height;
            } else {
                $percent  = strpos( $height, '%' );
                if( false !== $percent){
                    $overlayHeight = intval( $height ) / 100 * $this->image->getHeight();
                }
            }

            $editor = new Editor();
            $editor->setImage($overlay);
            $editor->resizeFit( $overlayWidth, $overlayHeight );
            $overlay = $editor->getImage();
        }

        //$x = $y = 0;

        if ( is_string( $xPos ) ) {
            // Compute position from string
            switch ( $xPos ) {
                case 'left':
                    $x = 0;
                    break;

                case 'right':
                    $x = $this->image->getWidth() - $overlay->getWidth();
                    break;

                case 'center':
                default:
                    $x = (int) round( ( $this->image->getWidth() / 2 ) - ( $overlay->getWidth() / 2 ) );
                    break;
            }
        } else {
            $x = $xPos;
        }

        if ( is_string( $yPos ) ) {
            switch ( $yPos ) {
                case 'top':
                    $y = 0;
                    break;

                case 'bottom':
                    $y = $this->image->getHeight() - $overlay->getHeight();
                    break;

                case 'center':
                default:
                    $y = (int) round( ( $this->image->getHeight() / 2 ) - ( $overlay->getHeight() / 2 ) );
                    break;
            }
        } else {
            $y = $yPos;
        }

        imagecopyresampled(
            $this->image->getCore(), // Base image
            $overlay->getCore(), // Overlay
            (int) $x, // Overlay x position
            (int) $y, // Overlay y position
            0,
            0,
            $overlay->getWidth(), // Overlay final width
            $overlay->getHeight(), // Overlay final height
            $overlay->getWidth(), // Overlay source width
            $overlay->getHeight() // Overlay source height
        );

        return $this;

    }

    /**
     * Create a blank image given width and height.
     *
     * @param int $width Width of image in pixels.
     * @param int $height Height of image in pixels.
     *
     * @return self
     */
    public function blank( $width, $height ) {
        $this->image = Image::createBlank( $width, $height );

        return $this;
    }

    /**
     * Wrapper function for the resizeXXX family of functions. Resize image given width, height and mode.
     *
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     * @param string $mode Resize mode. Possible values: "exact", "exactHeight", "exactWidth", "fill", "fit".
     *
     * @return Editor
     * @throws \Exception
     */
    public function resize( $newWidth, $newHeight, $mode='fit' ){

        switch ($mode){
            case 'exact':
                $this->resizeExact( $newWidth, $newHeight );
                break;
            case 'fill':
                $this->resizeFill($newWidth, $newHeight);
                break;
            case 'exactWidth':
                $this->resizeExactWidth( $newWidth );
                break;
            case 'exactHeight':
                $this->resizeExactHeight( $newHeight );
                break;
            case 'fit':
                $this->resizeFit($newWidth, $newHeight);
                break;
            default:
                throw new \Exception( sprintf('Invalid resize mode "%s".', $mode) );
        }

        return $this;
    }

    /**
     * Resize image to exact dimensions ignoring aspect ratio. Useful if you want to force exact width and height.
     *
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return self
     */
    public function resizeExact( $newWidth, $newHeight ){

        $this->_resize( $newWidth, $newHeight );

        return $this;
    }

    /**
     * Resize image to exact width. Height is auto calculated. Useful for creating column of images with the same width.
     *
     * @param int $newWidth Width in pixels.
     *
     * @return self
     */
    public function resizeExactWidth( $newWidth ){

        $width = $this->image->getWidth();
        $height = $this->image->getHeight();
        $ratio = $width / $height;

        $resizeWidth = $newWidth;
        $resizeHeight = round($newWidth / $ratio);

        $this->_resize( $resizeWidth, $resizeHeight );

        return $this;
    }

    /**
     * Resize image to exact height. Width is auto calculated. Useful for creating row of images with the same height.
     *
     * @param int $newHeight Height in pixels.
     *
     * @return self
     */
    public function resizeExactHeight( $newHeight ){

        $width = $this->image->getWidth();
        $height = $this->image->getHeight();
        $ratio = $width / $height;

        $resizeHeight = $newHeight;
        $resizeWidth = $newHeight * $ratio;

        $this->_resize( $resizeWidth, $resizeHeight );

        return $this;
    }

    /**
     * Resize image to fill all the space in the given dimension. Excess parts are cropped.
     *
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return self
     */
    public function resizeFill( $newWidth, $newHeight ){
        $width = $this->image->getWidth();
        $height = $this->image->getHeight();
        $ratio = $width / $height;

        // Base optimum size on new width
        $optimumWidth = $newWidth;
        $optimumHeight = round($newWidth / $ratio);

        if( ($optimumWidth < $newWidth) or ($optimumHeight < $newHeight) ){ // Oops, where trying to fill and there are blank areas
            // So base optimum size on height instead
            $optimumWidth = $newHeight * $ratio;
            $optimumHeight = $newHeight;
        }

        $this->_resize( $optimumWidth, $optimumHeight);
        $this->crop( $newWidth, $newHeight ); // Trim excess parts

        return $this;
    }

    /**
     * Resize image to fit inside the given dimension. No part of the image is lost.
     *
     * @param int $newWidth Width in pixels.
     * @param int $newHeight Height in pixels.
     *
     * @return self
     */
    public function resizeFit( $newWidth, $newHeight ){

        $width = $this->image->getWidth();
        $height = $this->image->getHeight();
        $ratio = $width / $height;

        $resizeWidth = $newWidth;
        $resizeHeight = $newHeight;

        if ( $width > $height ) { // Image is wider (landscape)

            // Just compute height
            $resizeHeight = round($newWidth / $ratio);

        } elseif ( $height > $width ) { // Image is taller (portrait)

            // Just compute width
            $resizeWidth = $newHeight * $ratio;

        } else { // Image is a square

            if ( $newWidth > $newHeight ) { // New image is wider (landscape)

                // Just compute height
                $resizeHeight = round($newWidth / $ratio);

            } else if ( $newHeight > $newWidth ) { // New image taller (portrait)

                // Just compute width
                $resizeWidth = $newHeight * $ratio;

            }
        }

        $this->_resize( $resizeWidth, $resizeHeight );

        return $this;
    }

    /**
     * Crop the image to the given dimension and position.
     *
     * @param int $cropWidth Crop width in pixels.
     * @param int $cropHeight Crop Height in pixels.
     * @param int|string $cropX The number of pixels from the left of the image. This parameter can be a number or any of the words "left", "center", "right".
     * @param int|string $cropY The number of pixels from the top of the image. This parameter can be a number or any of the words "top", "center", "bottom".
     *
     * @return self
     */
    public function crop($cropWidth, $cropHeight, $cropX='center', $cropY='center') {

        if(is_string($cropX)){
            // Compute position from string
            switch ($cropX){
                case 'left':
                    $x = 0;
                    break;

                case 'right':
                    $x = $this->image->getWidth() - $cropWidth;
                    break;

                case 'center':
                default:
                    $x = (int) round( ($this->image->getWidth()/2) - ($cropWidth/2) );
                    break;
            }
        } else {
            $x = $cropX;
        }

        if(is_string($cropY)){
            switch ($cropY){
                case 'top':
                    $y = 0;
                    break;

                case 'bottom':
                    $y = $this->image->getHeight() - $cropHeight;
                    break;

                case 'center':
                default:
                    $y = (int) round( ($this->image->getHeight()/2) - ($cropHeight/2) );
                    break;
            }
        } else {
            $y = $cropY;
        }

        // Create blank image
        $newImageResource = imagecreatetruecolor($cropWidth, $cropHeight);

        // Now crop
        imagecopyresampled(
            $newImageResource, // Target image
            $this->image->getCore(), // Source image
            0, // Target x
            0, // Target y
            $x, // Src x
            $y, // Src y
            $cropWidth, // Target width
            $cropHeight, // Target height
            $cropWidth, // Src width
            $cropHeight // Src height
        );

        // Free memory of old resource
        imagedestroy( $this->image->getCore() );

        // Cropped image instance
        $this->image = new Image($newImageResource, $this->image->getImageFile(), $cropWidth, $cropHeight, $this->image->getType());

        return $this;
    }

    /**
     * Save the image to an image format.
     *
     * @param string $file File path where to save the image.
     * @param null|string $type Type of image. Can be null, "GIF", "PNG", or "JPEG".
     * @param null|string $quality Quality of image. Applies to JPEG only. Accepts number 0 - 100 where 0 is lowest and 100 is the highest quality. Or null for default.
     * @param bool|false $interlace Set to true for progressive JPEG. Applies to JPEG only.
     *
     * @param int $permission
     *
     * @return Editor
     * @throws \Exception
     */
    public function save( $file, $type = null, $quality = null, $interlace = false, $permission = 0755 ){

        $this->_imageCheck();

        if ( null === $type ) {

            $type = $this->_getImageTypeFromFileName( $file ); // Null given, guess type from file extension
            if ( ImageType::UNKNOWN === $type ) {
                $type = $this->image->getType(); // 0 result, use original image type
            }
        }

        $targetDir = dirname( $file ); // $file's directory
        if( false === is_dir( $targetDir ) ){ // Check if $file's directory exist
            // Create and set default perms to 755
            if( !mkdir( $targetDir, $permission, true  ) ){
                throw new \Exception( sprintf('Cannot create %s', $targetDir) );
            }
        }

        switch ( strtoupper($type) ) {
            case ImageType::GIF :
                imagegif( $this->image->getCore(), $file );
                break;

            case ImageType::PNG :
                $quality = ( $quality === null ) ? 0 : $quality;
                $quality = ( $quality > 9 ) ? 9 : $quality;
                $quality = ( $quality < 0 ) ? 0 : $quality;
                imagepng( $this->image->getCore(), $file, $quality );
                break;

            default: // Defaults to jpeg
                $quality = ( $quality === null ) ? 100 : $quality;
                $quality = ( $quality > 100 ) ? 100 : $quality;
                $quality = ( $quality < 0 ) ? 0 : $quality;
                imageinterlace( $this->image->getCore(), $interlace );
                imagejpeg( $this->image->getCore(), $file, $quality );
        }

        return $this;
    }

    /**
     * Creates a rectangle shape.
     *
     * @param Rectangle $rectangle Rectangle object.
     * @param int $x X-coordinate of starting point.
     * @param int $y Y-coordinate of the starting point.
     *
     * @return self
     * @throws \Exception
     */
    public function rectangle( $rectangle, $x = 0, $y = 0 ) {

        $this->_imageCheck();

        list( $r, $g, $b, $alpha ) = $rectangle->getFillColor()->getRgba();

        $fillColorResource = imagecolorallocatealpha( $this->image->getCore(), $r, $g, $b,
            $this->_gdAlpha( $alpha ) );

        $x1 = $x;
        $x2 = $x + $rectangle->getWidth();
        $y1 = $y;
        $y2 = $y + $rectangle->getHeight();

        if ( 0 === $rectangle->getBorderSize() ) { // No borders

            imagefilledrectangle( $this->image->getCore(), $x1, $y1, $x2, $y2, $fillColorResource );
        } else { // With border
            list( $r, $g, $b, $alpha ) = $rectangle->getBorderColor()->getRgba();
            $borderColorResource = imagecolorallocatealpha( $this->image->getCore(), $r, $g, $b,
                $this->_gdAlpha( $alpha ) );
            // Create border by creating two rectangles
            imagefilledrectangle( $this->image->getCore(), $x1, $y1, $x2, $y2,
                $borderColorResource ); // Bigger rect
            imagefilledrectangle( $this->image->getCore(), $x1 + $rectangle->getBorderSize(),
                $y1 + $rectangle->getBorderSize(), $x2 - $rectangle->getBorderSize(), $y2 - $rectangle->getBorderSize(),
                $fillColorResource ); // Smaller rect
        }

        return $this;
    }

    /**
     * Fill entire image with color.
     *
     * @param Color $color Color object
     * @param int $x X-coordinate of start point
     * @param int $y Y-coordinate of start point
     *
     * @return self
     */
    public function fill( $color, $x = 0, $y = 0 ) {

        $this->_imageCheck();

        list( $r, $g, $b, $alpha ) = $color->getRgba();

        $colorResource = imagecolorallocatealpha( $this->image->getCore(), $r, $g, $b,
            $this->_gdAlpha( $alpha ) );
        imagefill( $this->image->getCore(), $x, $y, $colorResource );

        return $this;
    }

    /**
     * Sets the image to the specified opacity level where 1.0 is fully opaque and 0.0 is fully transparent.
     * Warning: This function loops thru each pixel manually which can be slow. Use sparingly.
     *
     * @param float $opacity
     *
     * @return self
     * @throws \Exception
     */
    public function opacity( $opacity ){

        // Bounds checks
        $opacity = ($opacity > 1) ? 1 : $opacity;
        $opacity = ($opacity < 0) ? 0 : $opacity;

        for($y = 0; $y < $this->image->getHeight(); $y++){
            for($x = 0; $x < $this->image->getWidth(); $x++){
                $rgb = imagecolorat($this->image->getCore(), $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $colors = imagecolorsforindex($this->image->getCore(), $rgb);
                $alpha = $colors['alpha'];

                // Reverse alpha values from 127-0 (transparent to opaque) to 0-127 for easy math
                // Previously: 0 = opaque, 127 = transparent.
                // Now: 0 = transparent, 127 = opaque
                $reverse = 127 - $alpha;
                $reverse = round($reverse * $opacity);

                if( $alpha < 127 ) { // Process non transparent pixels only
                    imagesetpixel( $this->image->getCore(), $x, $y, imagecolorallocatealpha( $this->image->getCore(), $r, $g, $b, 127 - $reverse ) );
                }
            }
        }

        return $this;
    }


    /**
     * @param string $text
     * @param int $x
     * @param int $y
     *
     * @param int $size
     * @param Color $color
     * @param string $font
     * @param int $angle
     *
     * @return $this
     */
    public function text( $text, $size = 12, $x = 0, $y = 12, $color = null, $font = '', $angle = 0 ) {
        $color  = ($color !== null) ? $color : new Color('#ffffff');
        $font   = ($font !== '') ? $font : Grafika::fontsDir().DIRECTORY_SEPARATOR.'LiberationSans-Regular.ttf';

        list( $r, $g, $b, $alpha ) = $color->getRgba();

        $colorResource = imagecolorallocatealpha(
            $this->image->getCore(),
            $r, $g, $b,
            $this->_gdAlpha( $alpha )
        );

        imagettftext(
            $this->image->getCore(),
            $size,
            $angle,
            $x,
            $y,
            $colorResource,
            $font,
            $text
        );

        return $this;
    }

    /**
     * Resize helper function.
     *
     * @param int $newWidth
     * @param int $newHeight
     * @param int $targetX
     * @param int $targetY
     * @param int $srcX
     * @param int $srcY
     *
     * @throws \Exception
     */
    private function _resize( $newWidth, $newHeight, $targetX=0, $targetY=0, $srcX=0, $srcY=0 ){

        $this->_imageCheck();

        // Create blank image
        $newImage = Image::createBlank( $newWidth, $newHeight );

        if( ImageType::PNG === $this->image->getType() ){
            // Preserve PNG transparency
            $newImage->fullAlphaMode( true );
        }

        imagecopyresampled(
            $newImage->getCore(),
            $this->image->getCore(),
            $targetX,
            $targetY,
            $srcX,
            $srcY,
            $newWidth,
            $newHeight,
            $this->image->getWidth(),
            $this->image->getHeight()
        );

        // Free memory of old resource
        imagedestroy( $this->image->getCore() );

        // Resize image instance
        $this->image = new Image(
            $newImage->getCore(),
            $this->image->getImageFile(),
            $newWidth,
            $newHeight,
            $this->image->getType()
        );

    }

    /**
     * Convert alpha value of 0 - 1 to GD compatible alpha value of 0 - 127 where 0 is opaque and 127 is transparent
     *
     * @param float $alpha Alpha value of 0 - 1. Example: 0, 0.60, 0.9, 1
     *
     * @return int
     */
    private function _gdAlpha( $alpha ) {

        $scale = round( 127 * $alpha );

        return $invert = 127 - $scale;
    }

    /**
     * Get image type base on file extension.
     *
     * @param int $imageFile File path to image.
     *
     * @return ImageType string Type of image.
     */
    private function _getImageTypeFromFileName( $imageFile ) {
        $ext = strtolower( (string) pathinfo( $imageFile, PATHINFO_EXTENSION ) );

        if ( 'jpg' == $ext or 'jpeg' == $ext ) {
            return ImageType::JPEG;
        } else if ( 'gif' == $ext ) {
            return ImageType::GIF;
        } else if ( 'png' == $ext ) {
            return ImageType::PNG;
        } else {
            return ImageType::UNKNOWN;
        }
    }

    /**
     * Check if editor has already been assigned an image.
     *
     * @throws \Exception
     */
    private function _imageCheck() {
        if ( null === $this->image ) {
            throw new \Exception( 'No image to edit.' );
        }
    }

    /**
     * @return Image
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage( $image ) {
        $this->image = $image;
    }
}