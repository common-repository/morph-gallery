<?php
namespace MorphGallery\Grafika;

/**
 * Interface EditorInterface
 * @package Grafika
 */
interface EditorInterface {

    /**
     * @return bool
     */
    public function isAvailable();

    /**
     * @param $file
     *
     * @return ImageInterface
     */
    public function open( $file );

    /**
     * @param string $overlay
     * @param string $xPos
     * @param string $yPos
     * @param null $width
     * @param null $height
     *
     * @return EditorInterface
     */
    public function overlay( $overlay, $xPos = 'center', $yPos = 'center', $width = null, $height = null );

    /**
     * @param $width
     * @param $height
     *
     * @return EditorInterface
     */
    public function blank( $width, $height );

    /**
     * @param $width
     * @param $height
     * @param string $mode
     *
     * @return EditorInterface
     */
    public function resize( $width, $height, $mode='fit' );

    /**
     * @param $width
     * @param $height
     *
     * @return EditorInterface
     */
    public function resizeExact( $width, $height );

    /**
     * @param $width
     * @param $height
     *
     * @return EditorInterface
     */
    public function resizeFit( $width, $height );

    /**
     * @param $width
     *
     * @return EditorInterface
     */
    public function resizeExactWidth( $width );

    /**
     * @param $height
     *
     * @return EditorInterface
     */
    public function resizeExactHeight( $height );

    /**
     * @param $width
     * @param $height
     *
     * @return EditorInterface
     */
    public function resizeFill( $width, $height );

    /**
     * @param $cropWidth
     * @param $cropHeight
     * @param string $cropX
     * @param string $cropY
     *
     * @return EditorInterface
     */
    public function crop( $cropWidth, $cropHeight, $cropX='center', $cropY='center');

    /**
     * @param $color
     * @param int $x
     * @param int $y
     *
     * @return EditorInterface
     */
    public function fill( $color, $x = 0, $y = 0 );

    /**
     * @param $rectangle
     * @param int $x
     * @param int $y
     *
     * @return EditorInterface
     */
    public function rectangle( $rectangle, $x = 0, $y = 0 );

    /**
     * @param $file
     * @param null $type
     * @param null $quality
     *
     * @return EditorInterface
     */
    public function save( $file, $type = null, $quality = null );

    /**
     * @return ImageInterface
     */
    public function getImage();

    /**
     * @param $image
     */
    public function setImage( $image );

    /**
     * @param $opacity
     *
     * @return EditorInterface
     */
    public function opacity( $opacity );

}