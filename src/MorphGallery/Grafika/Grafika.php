<?php

namespace MorphGallery\Grafika;

/**
 * Contains factory methods for detecting editors, creating editors and images.
 * @package Grafika
 */
class Grafika {

    const DIR = __DIR__; // Grafika directory

    public static function fontsDir(){
        $ds = DIRECTORY_SEPARATOR;
        return realpath(self::DIR.$ds.'..'.$ds.'..').$ds.'fonts';
    }

    /**
     * @param array $editorList Array of editor list names. Use this to change the order of evaluation for editors. Default order of evaluation is Imagick then GD.
     *
     * @return string Name of available editor.
     * @throws \Exception Throws exception if there are no supported editors.
     */
    public static function detectAvailableEditor( $editorList = array('Imagick', 'Gd') ){

        /* Get first supported editor instance. Order of editorList matter. */
        foreach($editorList as $editorName){
            $className = '\\MorphGallery\\Grafika\\'.$editorName.'\\Editor';
            $editorInstance = new $className();
            /** @var EditorInterface $editorInstance */
            if( true === $editorInstance->isAvailable()){
                return $editorName;
            }
        }

        throw new \Exception('No supported editor.');
    }

    /**
     * Creates the first available editor.
     * @return EditorInterface
     * @throws \Exception
     */
    public static function createEditor(){
        $editorName = self::detectAvailableEditor();
        $className = '\\MorphGallery\\Grafika\\'.$editorName.'\\Editor';
        return new $className();
    }

    /**
     * Create an image.
     * @param string $imageFile Path to image file.
     *
     * @return ImageInterface
     * @throws \Exception
     */
    public static function createImage( $imageFile ){
        $editorName = self::detectAvailableEditor();
        /** @var ImageInterface $className */
        $className = '\MorphGallery\\Grafika\\' . $editorName . '\\Image';
        return $className::createFromFile( $imageFile );
    }


    /**
     * Create a blank image.
     *
     * @param int $width Width of image in pixels.
     * @param int $height Height of image in pixels.
     *
     * @return ImageInterface
     * @throws \Exception
     */
    public static function createBlankImage( $width = 1, $height = 1 ){
        $editorName = self::detectAvailableEditor();
        /** @var ImageInterface $className */
        $className = '\MorphGallery\\Grafika\\'.$editorName.'\\Image';
        return $className::createBlank( $width, $height );
    }

}