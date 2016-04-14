<?php

namespace Stream;

class DataStoreDecorator extends PersistentStorage implements \Countable, \Iterator {

    private $_data = NULL;

    private $_idx = 0;

    public function __construct(\PDO $pdo = NULL) {
        
        parent::__construct($pdo);
    
    }

    public function count() {
    
        if(is_array($this->_data)) { return count($this->data); }
        if(is_object($this->_data)) { return 1; }
        
        return 0;
    
    }

    public function next() { $this->_idx++; }
 
    public function valid() {
    
        if($this->_idx === 0 && is_object($this->_data)) { return true; }
        if($this->_idx < count($this->_data)) { return true; }
        return false;
    
    }

    public function key() { return $this->_idx; }

    public function rewind() { $this->_idx = 0; return true; }

    /**
     * @return \stdClass
     */
    public function current() {
    
        if(is_object($this->_data)) { return $this->_data; }
    
        if(is_array($this->_data) && !empty($this->_data)) { return $this->_data[$this->_idx]; };
    
        return NULL;
    
    }

    /**
     * @return \Stream\DataStoreDecorator
     */
    public function search($data = [], $_ = NULL) {
        $this->_data = parent::search($data, $_);
        return $this;
    }

    public function filter($filter) {
        $this->_data = parent::filter($filter);
        return $this;
    }

    public function read($id = NULL, $uid = NULL) {
        $this->_data = parent::read($id);
        if(is_array($this->_data)) {
            return $this->_data;
        }
        return $this;
    }

    public function create($data) {
        
        parent::create($data);
        
        return $this;
    }

    public function update($id, $data) {

        if($id === NULL) {
            $id = $this->id;
        }
        
        if($id === NULL) { throw new \Exception; }

        parent::update($id, $data);

        return $this;
    
    }

    public function delete($id, $uid = NULL) {
        $this->_data = parent::delete($id);
        return $this;
    }

    public function __set($key, $val) {

        if($key === 'id') { $this->read($val); }
        
        $this->_data->{$key} = $val;
    
    }

    public function __get($key) {

        $out = NULL;
        
        if(isset($this->_data->{$key})) {
            return $this->_data->{$key};
        }

        return $out;
    
    }

}
