<?php


namespace MorphGallery\AlertSystem;


class Alerts {

    /**
     * @var StorageInterface
     */
    protected $storage;
    protected $errors;
    protected $messages;
    protected $success;

    protected $alerts;
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';

    public function __construct( StorageInterface $storage ) {
        $this->storage = $storage;
    }

    public function add_alert($type, $group, $message, $key=null) {
        $this->check_type($type);
        $this->alerts = $this->storage->get('alerts');
        if(!$this->alerts){
            $this->alerts = array(
                self::SUCCESS => array(),
                self::INFO => array(),
                self::WARNING => array(),
                self::ERROR => array()
            );
        }
        if($key===null){
            $this->alerts[$group][$type][] = $message;
        } else {
            $this->alerts[$group][$type][$key] = $message;
        }

        $this->storage->set('alerts', $this->alerts);
    }

    public function get_alerts($group, $delete = true){

        $alerts = $this->storage->get('alerts');
        if(isset($alerts[$group])){
            $group_alert = $alerts[$group];
            if($delete){
                unset($alerts[$group]); // Delete from storage after we fetch an alert
            }
            $this->storage->set('alerts', $alerts);
            return $group_alert;
        }
        return array();
    }

    public function get_errors($group, $delete = true){
        $alerts = $this->storage->get('alerts');
        if(isset($alerts[$group][self::ERROR])){
            $errors = $alerts[$group][self::ERROR];
            if($delete){
                unset($alerts[$group][self::ERROR]); // Delete from storage after we fetch an alert
            }
            $this->storage->set('alerts', $alerts);
            return $errors;
        }
        return array();
    }

    public function get_successes($group, $delete = true){
        $alerts = $this->storage->get('alerts');
        if(isset($alerts[$group][self::SUCCESS])){
            $successes = $alerts[$group][self::SUCCESS];
            if($delete){
                unset($alerts[$group][self::SUCCESS]); // Delete from storage after we fetch an alert
            }
            $this->storage->set('alerts', $alerts);
            return $successes;
        }
        return array();
    }

    public function get_info($group, $delete = true){
        $alerts = $this->storage->get('alerts');
        if(isset($alerts[$group][self::INFO])){
            $info = $alerts[$group][self::INFO];
            if($delete){
                unset($alerts[$group][self::INFO]); // Delete from storage after we fetch an alert
            }
            $this->storage->set('alerts', $alerts);
            return $info;
        }
        return array();
    }

    public function get_warnings($group, $delete = true){
        $alerts = $this->storage->get('alerts');
        if(isset($alerts[$group][self::WARNING])){
            $warnings = $alerts[$group][self::WARNING];
            if($delete){
                unset($alerts[$group][self::WARNING]); // Delete from storage after we fetch an alert
            }
            $this->storage->set('alerts', $alerts);
            return $warnings;
        }
        return array();
    }

    public function add_success($group, $message, $key=null) {
        $this->add_alert(self::SUCCESS, $group, $message, $key);
    }

    public function add_info($group, $message, $key=null) {
        $this->add_alert(self::INFO, $group, $message, $key);
    }

    public function add_warning($group, $message, $key=null) {
        $this->add_alert(self::WARNING, $group, $message, $key);
    }

    public function add_error($group, $message, $key=null) {
        $this->add_alert(self::ERROR, $group, $message, $key);
    }

    protected function check_type($type){
        if(self::SUCCESS !== $type AND self::INFO !== $type AND self::WARNING !== $type AND self::ERROR !== $type ){
            trigger_error("Invalid alert type.", E_USER_ERROR);
        }
    }
}