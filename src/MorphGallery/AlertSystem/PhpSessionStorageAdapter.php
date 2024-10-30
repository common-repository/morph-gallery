<?php


namespace MorphGallery\AlertSystem;


class PhpSessionStorageAdapter implements StorageInterface {

    /**
     * @var string
     */
    protected $session_name;

    /**
     * @var array
     */
    protected $data;

    public function __construct( $session_name ) {
        $this->session_name = $session_name;
    }

    public function set( $key, $value ) {
        $_SESSION[ $this->session_name ][ $key ] = $value;
    }

    public function get( $key ) {

        if ( isset( $_SESSION[ $this->session_name ][ $key ] ) ) {
            return $_SESSION[ $this->session_name ][ $key ];
        }

        return false;
    }
}