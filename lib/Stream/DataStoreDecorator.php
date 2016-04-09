<?php

namespace Stream;

class DataStoreDecorator extends PersistentStorage {

    private $_data = NULL;

    public function __construct(\PDO $pdo = NULL) {
        parent::__construct($pdo);
    }

    public function read($id = NULL, $uid = NULL) {
        $this->_data = parent::read($id);
        if(is_array($this->_data)) {
            return $this->_data;
        }
        return $this;
    }

    public function create($data) {
        $this->_data = parent::create($data);
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
            $out = $this->_data->{$key};
        }

        return $out;
    
    }

}
