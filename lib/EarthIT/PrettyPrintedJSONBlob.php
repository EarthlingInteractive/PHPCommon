<?php

// TODO: Replace this with a JSONUtil class with static methods
class EarthIT_JSONPrettyPrinter
{
	public function __construct( $writer ) {
		$this->writer = $writer;
	}
	
	protected static function isList( array $a ) {
		$len = count($a);
		for( $i=0; $i<$len; ++$i ) {
			if( !array_key_exists($i, $a) ) return false;
		}
		return true;
	}
	
	protected function emit( $text ) {
		call_user_func( $this->writer, $text );
	}
	
	public function prettyPrint( $value, $separator="\n", $separatorDelta="\t" ) {
		if( is_array($value) ) {
			if( count($value) == 0 ) {
				$this->emit('[]');
			} else {
				$isList = self::isList($value);
				$this->emit($isList ? '[' : '{');
				$subSeparator = $separator . $separatorDelta;
				$first = true;
				foreach( $value as $key => $subValue ) {
					if( !$first ) $this->emit(',');
					$this->emit($subSeparator);
					if( !$isList ) {
						$this->emit(json_encode($key));
						$this->emit(': ');
					}
					$this->prettyPrint( $subValue, $subSeparator, $separatorDelta );
					$first = false;
				}
				$this->emit($separator);
				$this->emit($isList ? ']' : '}');
			}
		} else {
			$this->emit( json_encode($value) );
		}
	}
}

class EarthIT_PrettyPrintedJSONBlob implements Nife_Blob
{
	protected $value;
	
	public function __construct( $value ) {
		$this->value = $value;
	}
	
	public function getLength() { return null; }
	
	public function writeTo( $writer ) {
		$pp = new EarthIT_JSONPrettyPrinter($writer);
		$pp->prettyPrint($this->value);
	}
}
