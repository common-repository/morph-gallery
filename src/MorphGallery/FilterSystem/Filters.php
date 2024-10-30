<?php


namespace MorphGallery\FilterSystem;


class Filters {

    private $registry;

    public function __construct() {
        $this->registry = array();
    }

    public function add( $filterName, callable $action, $actionName = '', $priority = 0 ) {
        if ( !isset( $this->registry[ $filterName ] ) ) {
            $this->registry[ $filterName ] = new FilterGroup();
        }
        /**
         * @var FilterGroup $filterGroup
         */
        $filterGroup = $this->registry[ $filterName ];
        $filterGroup->insert(
            new Filter($filterName, $action, $actionName, $priority ),
            $priority
        );
    }

    public function filter( $filterName, $value ){
        $args = func_get_args();
        array_shift($args); // Remove el[0]
        array_unshift( $args, $this ); // Add Filters instance at el[0]

        $filterGroup = $this->getGroup( $filterName );

        if($filterGroup->isDone())
            throw new \Exception( 'Cannot filter again. Already filtered.' );

        while($filterGroup->valid()){
            /**
             * @var Filter $filter
             */
            $filter = $filterGroup->current();
            $args[1] = $value;
            $value = forward_static_call_array( $filter->getAction(), $args );

            $filterGroup->next();
        }
        $filterGroup->setDone(true);

        return $value;
    }

    /**
     * @param $groupName
     *
     * @return FilterGroup
     */
    public function getGroup($groupName){
        foreach($this->registry as $filterGroupName=>$filterGroup){
            if($filterGroupName === $groupName) {
                return $filterGroup;
            }
        }
        return array();
    }

}