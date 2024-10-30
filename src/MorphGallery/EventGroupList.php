<?php


namespace MorphGallery;


/**
 * Class EventGroupList holds the list of events
 * @package MorphGallery
 */
class EventGroupList implements \Iterator, \Countable {

    /**
     * @var int $index Position of event
     */
    private $index;

    /**
     * @var int $count Number of events
     */
    private $count;

    /**
     * @var array
     */
    private $groups;

    public function __construct() {
        $this->index = 0;
        $this->count = 0;
        $this->groups = array();
    }

    public function rewind() {
        $this->index = 0;
    }

    public function valid() {
        return isset($this->groups[$this->index]);
    }

    public function current() {
        return $this->groups[$this->index];
    }

    public function key() {
        return $this->index;
    }

    public function next() {
        ++$this->index;
    }

    public function count() {
        return $this->count;
    }

    public function addEventGroup( $group ){
        $this->groups[ $this->count ] =  $group ;
        $this->count++;
    }
}