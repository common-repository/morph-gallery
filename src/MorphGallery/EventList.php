<?php


namespace MorphGallery;


class EventList implements \Iterator, \Countable {

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
    private $events;

    public function __construct() {
        $this->index = 0;
        $this->count = 0;
        $this->events = array();
    }

    public function rewind() {
        $this->index = 0;
    }

    public function valid() {
        return isset($this->events[$this->index]);
    }

    public function current() {
        return $this->events[$this->index];
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

    public function addEvent($event){
        $this->events[$this->count] = $event;
        $this->count++;
    }
}