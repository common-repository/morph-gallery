<?php

namespace MorphGallery\Grafika\Imagick;

use MorphGallery\Grafika\EditorInterface;
use MorphGallery\Grafika\ImageType;
use MorphGallery\Grafika\Rectangle;
use MorphGallery\Grafika\Color;

/**
 * Imagick Editor class. Uses the PHP Imagick library.
 * @package Grafika\Imagick
 */
final class Editor implements EditorInterface {

    /**
     * @var Image Holds the image instance
     */
    private $image;

    /**
     * Constructor.
     */
    function __construct() {
        $this->image = null;
    }

    /**
     * Checks if the editor is available on the current PHP install.
     *
     * @return bool
     */
    public function isAvailable(){
        // First, test Imagick's extension and classes.
        if ( false === extension_loaded( 'imagick' ) ||
            false === class_exists( 'Imagick' ) ||
            false === class_exists( 'ImagickDraw' ) ||
            false === class_exists( 'ImagickPixel' ) ||
            false === class_exists( 'ImagickPixelIterator' )
        ){
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
        if($width and $height){

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

        // Overlay the image on the original image
        $this->image->getCore()->compositeImage($overlay->getCore(), \Imagick::COMPOSITE_OVER, $x, $y);

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
     * @return self
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

        $this->_resize($newWidth, $newHeight);

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
    public function resizeFit( $newWidth, $newHeight ) {
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
     * @param int|string $cropX The number of pixels from the left of the image. Can also be the words "left", "center", and "right".
     * @param int|string $cropY The number of pixels from the right of the image. Can also be the words "top", "center", and "bottom".
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

        $this->image->getCore()->cropImage( $cropWidth, $cropHeight, $x, $y );

        return $this;
    }

    /**
     * Save the image to an image format.
     *
     * @param string $file File path where to save the image.
     * @param null|string $type Type of image. Can be null, "GIF", "PNG", or "JPEG".
     * @param null|string $quality Quality of image. Applies to JPEG only. Accepts number 0 - 100 where 0 is lowest and 100 is the highest quality. Or null for default.
     * @param bool|false $interlace Set to true for progressive JPEG. Applies to JPEG only.
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
                $type = $this->image->getType(); // Unknown result, use original image type
            }
        }

        $targetDir = dirname( $file ); // $file's directory
        if( false === is_dir( $targetDir ) ){ // Check if $file's directory exist
            // Create and set default perms to 755
            if( !mkdir( $targetDir, $permission, true  ) ){
                throw new \Exception( sprintf('Cannot create %s', $targetDir) );
            }
        }

        switch ( $type ) {
            case ImageType::GIF :
                $this->image->getCore()->writeImages( $file, true ); // Support animated image. Eg. GIF
                break;

            case ImageType::PNG :
                // PNG is lossless and does not need compression
                $this->image->getCore()->setImageFormat($type);
                $this->image->getCore()->writeImage( $file );
                break;

            default: // Defaults to jpeg
                $quality = ( $quality === null ) ? 60 : $quality; // Default to 60 when null given
                $quality = ( $quality > 100 ) ? 100 : $quality;
                $quality = ( $quality < 0 ) ? 0 : $quality;

                if($interlace){
                    $this->image->getCore()->setImageInterlaceScheme( \Imagick::INTERLACE_JPEG );
                }
                $this->image->getCore()->setImageFormat($type);
                $this->image->getCore()->setImageCompressionQuality($quality);
                $this->image->getCore()->writeImage( $file ); // Single frame image. Eg. JPEG
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
    public function rectangle( $rectangle,  $x = 0, $y = 0 ){

        $this->_imageCheck();

        $strokeColor = new \ImagickPixel( $rectangle->getBorderColor()->getHexString() );
        $fillColor = new \ImagickPixel( $rectangle->getFillColor()->getHexString() );

        $draw = new \ImagickDraw();
        $draw->setFillColor($strokeColor);
        $draw->setStrokeOpacity( 0 );

        $x1 = $x;
        $x2 = $x + $rectangle->getWidth();
        $y1 = $y;
        $y2 = $y + $rectangle->getHeight();

        $draw->rectangle( $x1, $y1, $x2, $y2 );

        $borderSize = $rectangle->getBorderSize();
        $innerRect = new \ImagickDraw();
        $innerRect->setStrokeOpacity( 0 );
        $innerRect->setFillColor($fillColor);
        $innerRect->rectangle( $x1 + $borderSize, $y1 + $borderSize, $x2 - $borderSize, $y2 - $borderSize );

        $this->image->getCore()->setImageFormat("png");
        $this->image->getCore()->drawImage($draw);
        $this->image->getCore()->drawImage($innerRect);

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
    public function fill( $color, $x = 0, $y = 0 ){

        $this->_imageCheck();

        $target = $this->image->getCore()->getImagePixelColor($x, $y);
        $this->image->getCore()->floodfillPaintImage( $color->getHexString(), 1, $target, $x, $y, false);

        return $this;
    }


    /**
     * Sets the image to the specified opacity level where 1.0 is fully opaque and 0.0 is fully transparent.
     *
     * @param float $opacity
     *
     * @return self
     * @throws \Exception
     */
    public function opacity( $opacity ){

        $this->_imageCheck();

        // Bounds checks
        $opacity = ($opacity > 1) ? 1 : $opacity;
        $opacity = ($opacity < 0) ? 0 : $opacity;

        $this->image->getCore()->setImageOpacity( $opacity );

        return $this;
    }

    /**
     * Resize helper function.
     *
     * @param int $newWidth
     * @param int $newHeight
     *
     * @return self
     * @throws \Exception
     */
    private function _resize( $newWidth, $newHeight ){
        $this->_imageCheck();

        if ( 'GIF' == $this->image->getType() ) { // Animated image. Eg. GIF

            $imagick = $this->image->getCore()->coalesceImages();

            foreach ($imagick as $frame) {
                $frame->resizeImage($newWidth, $newHeight, \Imagick::FILTER_BOX, 1, false);
                $frame->setImagePage($newWidth, $newHeight, 0, 0);
            }

            // Assign new image with frames
            $this->image = new Image($imagick->deconstructImages(), $this->image->getImageFile(), $newWidth, $newHeight, $this->image->getType());
        } else { // Single frame image. Eg. JPEG, PNG

            $this->image->getCore()->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1, false);
            // Assign new image
            $this->image = new Image($this->image->getCore(), $this->image->getImageFile(), $newWidth, $newHeight, $this->image->getType());
        }

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
    private function _imageCheck(){
        if( null === $this->image ){
            throw new \Exception('No image to edit.');
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