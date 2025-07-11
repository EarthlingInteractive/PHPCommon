<?php

class EarthIT_Registry
{
	protected $configDir;
	protected $configs = array();
	protected $components = array();
	protected $dbAdapter;
	
	public function __construct( $configDir ) {
		$this->configDir = $configDir;
	}
	
	public function getConfig( $name ) {
		$parts = explode('/', $name);
		$file = array_shift($parts);
		if( isset($this->configs[$file]) ) {
			$c = $this->configs[$file];
		} else {
			$cf = "{$this->configDir}/{$file}.json";
			if( !file_exists($cf) ) return null;
			$c = json_decode(file_get_contents($cf), true);
			if( $c === null ) {
				throw new Exception("Failed to load config from '{$cf}'");
			}
			$this->configs[$file] = $c;
		}
		foreach( $parts as $p ) {
			if( isset($c[$p]) ) {
				$c = $c[$p];
			} else {
				return null;
			}
		}
		return $c;
	}
	
	public function getComponent( $name, $nullIfDoesntExist=false ) {
		if( !isset($this->components[$name]) ) {
			if( $nullIfDoesntExist ) {
				if( class_exists($name, true) ) {
					$this->components[$name] = new $name($this);
				} else {
					return null;
				}
			} else {
				$this->components[$name] = new $name($this);
			}
		}
		return $this->components[$name];
	}
}
