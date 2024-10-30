<?php


namespace MorphGallery;


class Logger {

    private $logs;

    public function __construct() {
        $this->logs = array();
    }

    public function log( $text ){
        $this->logs[] = $text;
    }

    public function get_logs(){
        return $this->logs;
    }
}