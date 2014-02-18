<?php

class EarthIT_Collector
{
	public $collection = array();
	
	public function __construct( array $initialCollection=array() ) {
		$this->collection = $initialCollection;
	}
	
	public function __invoke( $item ) {
		$this->collection[] = $item;
	}
	
	public function __toString() {
		return implode('',$this->collection);
	}
}
