<?php

namespace SmartcatSupport\form;

/**
 * Factory/Builder for creating forms
 *
 * @author ericg
 */
class FormBuilder {
    protected $id;
    private $fields = array();
    private $method;
    private $action;
    
    public function __construct( $id, $method = 'POST', $action = '?' ) {
        $this->id = $id;
        $this->method = $method;
        $this->action = $action;
    }
    
    public function add( $class, $id, $args = array() ) {
        $this->fields[ $id ] = new $class( $id, $args );
        
        return $this;
    }
    
    public function get_form() {
        return new Form( $this->id, $this->fields, $this->method, $this->action );
    }
    
    public function create_constraint( $class, $message = '', $args = [] ) {
        return new $class( $message, $args );
    }

    public function clear_config() {
        $this->fields = array();
    }
}
