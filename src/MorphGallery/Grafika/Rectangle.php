<?php
namespace MorphGallery\Grafika;

/**
 * Class Rectangle
 * @package Grafika
 */
class Rectangle {

    /**
     * Image width in pixels
     * @var int
     */
    protected $width;

    /**
     * Image height in pixels
     * @var int
     */
    protected $height;

    /**
     * @var int
     */
    protected $borderSize;

    /**
     * @var Color
     */
    protected $fillColor;

    /**
     * @var Color
     */
    protected $borderColor;


    /**
     * Rectangle constructor.
     *
     * @param $width
     * @param $height
     * @param null $borderSize
     * @param null $fillColor
     * @param null $borderColor
     */
    public function __construct( $width, $height, $borderSize = null, $fillColor = null, $borderColor = null  ){

        $this->width = $width;
        $this->height = $height;
        $this->borderSize = (null===$borderSize) ? 0 : $borderSize;
        $this->fillColor = (null===$fillColor) ? new Color('#ffffff') : $fillColor;
        $this->borderColor = (null===$borderColor) ? new Color('#000000') : $borderColor;

    }

    /**
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width) {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height) {
        $this->height = $height;
    }

    /**
     * @return Color
     */
    public function getFillColor() {
        return $this->fillColor;
    }

    /**
     * @param Color $fillColor
     */
    public function setFillColor($fillColor) {
        $this->fillColor = $fillColor;
    }

    /**
     * @return int
     */
    public function getBorderSize() {
        return $this->borderSize;
    }

    /**
     * @param int $borderSize
     */
    public function setBorderSize($borderSize) {
        $this->borderSize = $borderSize;
    }

    /**
     * @return Color
     */
    public function getBorderColor() {
        return $this->borderColor;
    }

    /**
     * @param Color $borderColor
     */
    public function setBorderColor($borderColor) {
        $this->borderColor = $borderColor;
    }

}