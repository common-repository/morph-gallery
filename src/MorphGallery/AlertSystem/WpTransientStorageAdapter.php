<?php


namespace MorphGallery\AlertSystem;


class WpTransientStorageAdapter implements StorageInterface {

    /**
     * @var string
     */
    protected $transient_name;

    /**
     * @var int
     */
    protected $transient_duration;

    /**
     * @var array
     */
    protected $data;

    public function __construct( $transient_name, $transient_duration = 60 ) {
        $this->transient_name = $transient_name;
        $this->transient_duration = $transient_duration;
    }

    public function set( $key, $value, $duration = null ) {
        if($duration === null){
            $duration = $this->transient_duration; // Use global settings
        }
        $transient = get_transient( $this->transient_name );
        if ( false === $transient ) {
            $transient = array();
        }
        $transient[ $key ] = $value;
        set_transient( $this->transient_name, $transient, $duration );
    }

    public function get( $key ) {
        $transient = get_transient( $this->transient_name );
        if ( false !== $transient ) {
            if ( isset( $transient[ $key ] ) ) {
                return $transient[ $key ];
            }
        }

        return false;
    }
}