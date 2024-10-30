<?php
namespace MorphGallery\FileSystem;

/**
 * Simple class for doing file system operations
 */
class FileSystemHelper {

    /**
     * Constructor
     *
     */
    public function __construct() {
    }

    public function getFile( $path ){
        return new File( $path );
    }

    public function copy( $source, $target, $overwrite = false, $permission = 0755 ){
        if( file_exists( $target ) and false === $overwrite ){
            throw new \Exception( 'File already exists' );
        }

        $targetDir = dirname( $target ); // $target's directory
        if( false === is_dir( $targetDir ) ){ // Check if $target's directory exist
            @mkdir( $targetDir, $permission, true  ); // Default perms 0755. See https://codex.wordpress.org/Changing_File_Permissions and http://php.net/manual/en/function.mkdir.php
        }

        if (!@copy($source, $target)) {
            throw new \Exception( sprintf('Cannot copy "%s" to "%s".', $source, $target) );
        }

        return $this;
    }

    public function move( $source, $target, $overwrite = false ){
        if( !file_exists( $source ) ){
            throw new \Exception( sprintf('Cannot find source file "%s".', $source) );
        }

        if( file_exists( $target ) and false === $overwrite ){
            throw new \Exception( sprintf('File "%s" already exists. Set overwrite flag to true to enable file overwrite.', $target) );
        }

        if (!@rename($source, $target)) {
            $error = error_get_last();
            throw new \Exception( sprintf('Cannot move "%s" to "%s". %s', $source, $target, $error['message']) );
        }

        return $this;
    }

    public function delete( $path ) {
        if ( @is_file( $path ) ) {
            $this->deleteFile( $path );
        } else {
            $this->deleteDir( $path );
        }
    }

    public function deleteFile( $path ){

        if(!@is_file( $path)){
            throw new \Exception( sprintf('Path "%s" is not a file or does not exist.', $path ) );
        }

        if (!@unlink( $path )) {
            $error = error_get_last();
            throw new \Exception( sprintf('Cannot delete "%s". %s', $path, $error['message']) );
        }

        return $this;
    }

    /**
     * Delete directory and all its contents
     *
     * @param $path
     *
     * @throws \Exception
     */
    public function deleteDir( $path ){
        if(!@is_dir( $path)){
            throw new \Exception( sprintf('Path "%s" is not a directory.', $path ) );
        }
        $items = @scandir( $path );
        if(false === $items){
            throw new \Exception( sprintf('scandir failed on "%s".', $path ) );
        }
        foreach($items as $item) {
            if ('.' !== $item and '..' !== $item) {
                $target = "$path/$item";
                if ( @is_dir( $target ) ) {
                    $this->deleteDir( $target );
                } else {
                    if(!@unlink( $target )){
                        throw new \Exception( sprintf('unlink failed on "%s".', $target ) );
                    }
                }
            }
        }
        if(!@rmdir($path)){
            throw new \Exception( sprintf('rmdir failed on "%s".', $path ) );
        }
    }

    /**
     * Delete all directory contents but not the directory.
     *
     * @param $path
     *
     * @throws \Exception
     */
    public function clearDir( $path ) {

        if(!@is_dir( $path)){
            throw new \Exception( sprintf('Path "%s" is not a directory.', $path ) );
        }
        $items = @scandir( $path );
        if(false === $items){
            throw new \Exception( sprintf('scandir failed on "%s".', $path ) );
        }
        foreach($items as $item) {
            if ('.' !== $item and '..' !== $item) {
                $target = "$path/$item";
                if ( @is_dir( $target ) ) {
                    $this->deleteDir( $target );
                } else {
                    if(!@unlink( $target )){
                        throw new \Exception( sprintf('unlink failed on "%s".', $target ) );
                    }
                }
            }
        }
    }
}