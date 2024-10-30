<?php


namespace MorphGallery\AlertSystem;


interface StorageInterface {

    public function set($key, $value);

    public function get($key);
}