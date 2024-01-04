<?php

class EarthIT_JSON_HybridJSONBlob
{
	protected $value;
	protected $nativeEncodeDepth;
	protected $separator = "";
	protected $separatorDelta = "\t";
	
	public function __construct( $value, $nativeEncodeDepth=INF ) {
		$this->value = $value;
		$this->nativeEncodeDepth = $nativeEncodeDepth;
	}
	
	/**
	 * Returns the value that's JSON-encoded by this blob.
	 */
	public function getValue() {
		return $this->value;
	}
	
	public function getLength() { return null; }
	
	protected function _write2( $value, $callback, $depth, $separator, $separatorDelta ) {
		if( $depth == $this->nativeEncodeDepth || is_scalar($value) || $value === null ) {
			call_user_func( $callback, EarthIT_JSON::encode($value) );
			return;
		}

		if( !is_array($value) && !is_object($value) ) {
			throw new Exception("Don't know how to JSONify non-scalar, non-array, non-object: ".gettype($value));
		}
		
		if( is_object($value) ) {
			$isList = false;
		} else {
			$isList = EarthIT_JSON::isList($value);
			unset($value[EarthIT_JSON::JSON_TYPE]); // We don't want to include that!
		}
		
		$isEmpty = true;
		foreach( $value as $k=>$v ) {
			$isEmpty = false; break;
		}
		
		if( $isEmpty ) {
			call_user_func($callback, $isList ? '[]' : '{}');
		} else {
			call_user_func($callback, $isList ? '[' : '{');
			$subSeparator = $separator . $separatorDelta;
			$first = true;
			foreach( $value as $key => $subValue ) {
				if( !$first ) call_user_func($callback, ',');
				call_user_func($callback, $subSeparator);
				if( !$isList ) {
					call_user_func($callback, EarthIT_JSON::encode((string)$key));
					call_user_func($callback, ': ');
				}
				$this->_write2($subValue, $callback, $depth+1, $subSeparator, $separatorDelta);
				$first = false;
			}
			call_user_func($callback, $separator);
			call_user_func($callback, $isList ? ']' : '}');
		}
	}
	
	public function writeTo( $callback ) {
		$this->_write2( $this->value, $callback, 0, $this->separator, $this->separatorDelta );
	}

	public function __toString() { 
		$c = new EarthIT_Collector();
		$this->writeTo($c);
		return (string)$c;
	}
}
