<?php


namespace MorphGallery\FilterSystem;


class Filter {

    private $filterName;
    private $action;
    private $actionName;
    private $priority;

    /**
     * Filter constructor.
     *
     * @param $filterName
     * @param $action
     * @param $actionName
     * @param $priority
     */
    public function __construct( $filterName, $action, $actionName, $priority ) {
        $this->filterName = $filterName;
        $this->action     = $action;
        $this->actionName = $actionName;
        $this->priority   = $priority;
    }

    /**
     * @return mixed
     */
    public function getFilterName() {
        return $this->filterName;
    }

    /**
     * @return mixed
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function getActionName() {
        return $this->actionName;
    }

    /**
     * @return mixed
     */
    public function getPriority() {
        return $this->priority;
    }

}