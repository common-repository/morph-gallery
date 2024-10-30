<?php
namespace MorphGallery\FileSystem;

/**
 * Simple class for browsing the file system tree
 */
class File {

    /**
     * @var \SplFileInfo $file
     */
    protected $file;

    public function __construct( $path ) {
        if(is_dir( $path )){
            throw new \Exception('Not a file but a directory.');
        }
        if(!file_exists( $path )){
            throw new \Exception(sprintf('File not found on %s', $path));
        }

        $this->file = new \SplFileInfo( $path );
    }

    /**
     * Get file name only without extension and path
     *
     * @return string
     */
    public function getName(){
        return $this->file->getBasename(); // Not using getFilename because "/file.txt" returns "/file.txt" (with a slash)
    }

    /**
     * Get file extension. Eg. notes.txt = txt, backup.tar.gz = gz
     *
     * @param bool $dot Set to true to include a dot. Eg. notes.txt = .txt
     *
     * @return string
     */
    public function getExtension( $dot=false ){
        $ext = $this->file->getExtension();
        if($dot) return '.'.$ext;

        return $ext;
    }

    /**
     * Get file name suffixes in array
     *
     * @return array
     */
    public function getSuffixes(){
        $name = $this->getName();
        $dot  = strpos( $name, '.' );
        if ( false !== $dot ) {
            $slice = substr( $name, $dot+1 );
            return explode('.', $slice);
        }
        return array();
    }

    /**
     * Get file's directory path (parent path)
     *
     * @return string
     */
    public function getDirectory(){
        return dirname($this->file->getRealPath());
    }

    /**
     * Full path to file
     *
     * @return string
     */
    public function getPath(){
        return $this->file->getRealPath(); // Normalized path
    }
}