<?php
class Entity
{
	public $type;
	protected $data;
	protected $_structure;
	protected $_children;
	
	public function __construct($data){
		$this->data = $data;
	}
	public function __get($key){
		if(!is_array($this->data))
			return $this->$key;
		if(array_key_exists($key,$this->data))
			return $this->data[$key];
		return $this->$key;
	}
	public function __set($key, $value){
		if(array_key_exists($key,$this->data))
			$this->data[$key] = $value;
		else
			$this->$key = $value;
	}
}