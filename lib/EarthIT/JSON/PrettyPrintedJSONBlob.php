<?php

class EarthIT_JSON_PrettyPrintedJSONBlob
{
	protected $value;
	
	public function __construct( $value ) {
		$this->value = $value;
	}
	
	/**
	 * Returns the value that's JSON-encoded by this blob.
	 */
	public function getValue() {
		return $this->value;
	}
	
	public function getLength() { return null; }
	
	public function writeTo( $writer ) {
		EarthIT_JSON::prettyPrint($this->value, $writer);
	}
	
	public function __toString() {
		return EarthIT_JSON::prettyEncode($this->value);
	}
}
