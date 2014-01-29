<?php

abstract class EarthIT_Component
{
	protected $registry;
	
	public function __construct( EarthIT_Registry $registry ) {
		$this->registry = $registry;
	}
}
