<?php


namespace MorphGallery\FilterSystem;


class FilterGroup extends \SplPriorityQueue {

    private $done = false;
    private $queueOrder = PHP_INT_MAX;

    /**
     * Fix order for items with the same priority: http://stackoverflow.com/questions/25522897/why-splpriorityqueue-class-is-a-queue-conceptual
     *
     * @param mixed $item
     * @param mixed $priority
     */
    public function insert( $item, $priority ) {
        if ( is_int( $priority ) ) {
            $priority = array( $priority, $this->queueOrder-- );
        }
        parent::insert( $item, $priority );
    }

    /**
     * Mark filter group as done so as not to run it again.
     * @param boolean $done
     */
    public function setDone( $done ) {
        $this->done = $done;
    }

    /**
     * Check if filter group has ran.
     * @return boolean
     */
    public function isDone() {
        return $this->done;
    }

}