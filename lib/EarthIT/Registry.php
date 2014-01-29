<?php

class EarthIT_Registry
{
	protected $configDir;
	protected $configs;
	protected $components;
	protected $dbAdapter;
	
	public function __construct( $configDir ) {
		$this->configDir = $configDir;
		$this->configs = array();
		$this->components = array();
	}
	
	public function getConfig( $name ) {
		$parts = explode('/', $name);
		$file = array_shift($parts);
		if( !isset($this->configs[$file]) ) {
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
	
	public function getComponent( $name ) {
		if( !isset($this->components[$name]) ) {
			$this->components[$name] = new $name($this);
		}
		return $this->components[$name];
	}
}
