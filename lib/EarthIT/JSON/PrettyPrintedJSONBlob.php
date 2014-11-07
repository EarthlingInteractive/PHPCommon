<?php

class EarthIT_JSON_PrettyPrintedJSONBlob implements Nife_Blob
{
	protected $value;
	
	public function __construct( $value ) {
		$this->value = $value;
	}
	
	public function getLength() { return null; }
	
	public function writeTo( $writer ) {
		EarthIT_JSON::prettyPrint($this->value, $writer);
	}
	
	public function __toString() {
		return EarthIT_JSON::prettyEncode($this->value);
	}
}
