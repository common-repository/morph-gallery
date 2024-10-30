<?php


namespace MorphGallery;


class Events {

    private $registry;

    public function __construct() {
    }

    public function on($eventName, callable $callback) {
        $this->registry[$eventName][] = $callback;
    }

    public function trigger( $eventName ){
        $args = func_get_args();
        array_shift($args); // Remove el[0]
        $eventGroup = $this->getGroup( $eventName );

        foreach($eventGroup as $index=>$event){
            forward_static_call_array( $event, $args );
        }
    }

    public function getGroup($groupName){
        foreach($this->registry as $eventGroupName=>$eventGroup){
            if($eventGroupName === $groupName) {
                return $eventGroup;
            }
        }
        return array();
    }
}