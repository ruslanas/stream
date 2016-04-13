<?php

/**
 * Experimental validator
 * DataStore checks if data valid before each operation
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

const FIXED = 1;
const REQUIRED = 2;
const EMAIL = 4;
const URL = 8;
const BOOL = 16;
const USER = 32;

class Validator extends Util\Injectable {

    private $_map = [
    
        FIXED => ['fixed', "Can't change value"],
        REQUIRED => ['required', 'Required'],
        EMAIL => ['email', 'Invalid email address'],
        URL => ['url', 'URL invalid'],
        BOOL => ['logic', 'Expected true|false'],
        USER => ['user', "User can't change value"]
    
    ];
    
    private $_errors = [];
    
    private $dsl = [];

    static public function init() { }

    public function __construct($dsl) {
        $this->dsl = $dsl;
    }

    public function validate($data, $dsl = NULL) {

        $dsl = $dsl !== NULL ? $dsl : $this->dsl;
        
        reset($dsl);
        
        $class_name = current($dsl);
        
        $this->model = new $class_name($this->PDO);

        if(!empty($dsl[1])) {
            $parts = explode('.', $dsl[1]);
            $this->model->read($data[end($parts)]);
        } else {
            $this->model->read($data['id']);
        }

        $dsl = $dsl[0];

        for($i=1;$i<count($dsl);$i++) {
            
            if(is_array($dsl[$i][0])) {
                return $this->validate($data, $dsl[$i]);
            }

            $val = isset($data[$dsl[$i][0]]) ? $data[$dsl[$i][0]] : NULL;

            $this->valid($val, $dsl[$i][1], $dsl[$i][0]);
        
        }

        return $this->_errors;
    }

    private function valid($arg, $rules, $name = '_') {
        
        foreach($this->_map as $validator => $v) {
            
            if($arg === NULL && !($rules & REQUIRED)) {
                continue;
            }

            if($rules & $validator) {
                
                if(method_exists($this, $this->_map[$validator][0])) {

                    if(!$this->{$this->_map[$validator][0]}($arg, $name)) {
                        $this->_errors[$name] = $this->_map[$validator][1];
                    }
                
                } else {
                    throw new Exception;
                }
            }
        
        }
        
        return !!count($this->_errors);
    
    }

    private function fixed($arg, $name) {

        if($this->model->current() === NULL) { return true ; }

        return ($this->model->current()->{$name} == $arg);

    }

    private function required($arg) {
        return !($arg === NULL);
    }

    private function email($arg) {
        return !(filter_var($arg, FILTER_VALIDATE_EMAIL) === FALSE);
    }

    private function url($arg) {
        return !(filter_var($arg, FILTER_VALIDATE_URL) === FALSE);
    }

    private function logic($arg) {
        return is_bool($arg);
    }

    private function user($arg) {
    
        if($this->Session->get('uid') === NULL) { return false ; }

        $this->User->read($this->Session->get('uid'));

        if(empty($this->User->current())) { return false; }

        return ($this->model->current()->user_id == $this->User->current()->id);
    
    }

}
