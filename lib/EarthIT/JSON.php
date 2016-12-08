<?php

class EarthIT_JSON
{
	/**
	 * A special key that can be used to influence how arrays are encoded,
	 * in case the default (e.g. "[]" for empty arrays) isn't what you want
	 * (e.g. you wanted "{}").
	 */
	const JSON_TYPE = '$jsonType$kjefh873yhfb4cw34u5yfwjd43ukd4rhf38k278yh234t5rfu7yjwrefjmywgef8i2k7n2c$';
	const JT_LIST = 'list';
	const JT_OBJECT = 'object';

	/**
	 * Strips encoding metadata (such as JSON_TYPE) out of arrays.
	 * 
	 * If we ever start using array-like objects to represent data+metadata to be JSON-encoded,
	 * then I expect that this would also convert those objects to arrays.
	 */
	public static function withoutMetadata( $jsObject, $recurse=false ) {
		if( !is_array($jsObject) ) return $jsObject;
		
		unset($jsObject[self::JSON_TYPE]);
		
		if( $recurse ) foreach( $jsObject as $k=>$v ) {
			if( is_array($v) ) $jsObject[$k] = self::withoutMetadata($v, $recurse);
		}
		
		return $jsObject;
	}
	
	public static function jsonDecodeMessage( $code ) {
		switch( $code ) {
		case JSON_ERROR_NONE             : return "No error";
		case JSON_ERROR_DEPTH            : return "Maximum stack depth exceeded";
		case JSON_ERROR_STATE_MISMATCH   : return "Underflow or the modes mismatch";
		case JSON_ERROR_CTRL_CHAR        : return "Unexpected control character found";
		case JSON_ERROR_SYNTAX           : return "Syntax error, malformed JSON";
		case JSON_ERROR_UTF8             : return "Malformed UTF-8 characters, possibly incorrectly encoded";
		default                          : return "json_last_error() = $code";
		}
	}

	protected static function lastJsonErrorMessage() {
		return function_exists('json_last_error_msg') ?
			json_last_error_msg() :
			self::jsonDecodeMessage(json_last_error());
	}
	
	public static function decode( $thing ) {
		if( $thing instanceof EarthIT_JSON_PrettyPrintedJSONBlob ) {
			return self::withoutMetadata($thing->getValue(), true);
		}
		
		if( $thing instanceof Nife_Blob ) {
			$thing = (string)$thing;
		}
		if( !is_string($thing) ) {
			throw new EarthIT_JSON_JSONDecodeError("Attempted to JSON-decode non-string: ".gettype($thing));
		}
		
		$thing = trim($thing);
		if( $thing == '' ) throw new EarthIT_JSON_JSONDecodeError("Attempted to JSON-decode empty string.");
		if( $thing == 'null' ) return null;
		$value = json_decode($thing, true);
		if( $value === null ) {
			$report_thing = strlen($thing) < 256 ? $thing : substr($thing,0,253)."...";
			$message = self::lastJsonErrorMessage();
			throw new EarthIT_JSON_JSONDecodeError("Error parsing JSON: $message; JSON: $report_thing");
		}
		return $value;
	}
	
	public static function isList( array $a ) {
		if( isset($a[self::JSON_TYPE]) ) {
			switch($a[self::JSON_TYPE]) {
			case self::JT_LIST: return true;
			case self::JT_OBJECT: return false;
			}
		}
		
		$len = count($a);
		for( $i=0; $i<$len; ++$i ) {
			if( !array_key_exists($i, $a) ) return false;
		}
		return true;
	}
	
	
	
	/**
	 * Just like the normal json_encode, but throws an exception instead of
	 * returning false on error.
	 */
	public static function encode( $value ) {
		// Leaving off $options and $depth parameters because:
		// 1) We don't use them.
		// 2) PHP 5.2 compatibility.
		// TODO: Check if recent enough PHP version (>= 5.4) and if so pass JSON_UNESCAPED_SLASHES
		if( defined('JSON_UNESCAPED_SLASHES') ) {
			$json = json_encode($value, JSON_UNESCAPED_SLASHES);
		} else {
			$json = json_encode($value);
		}
		if( $json === false ) {
			throw new Exception("Failed to json-encode ".gettype($value).": ".print_r($value,true).": ".self::lastJsonErrorMessage());
		}
		return $json;
	}
	
	public static function prettyPrint( $value, $callback, $separator="\n", $separatorDelta="\t" ) {
		if( is_array($value) ) {
			$isList = self::isList($value);
			unset($value[self::JSON_TYPE]); // We don't want to include that!
			if( count($value) == 0 ) {
				call_user_func($callback, $isList ? '[]' : '{}');
			} else {
				call_user_func($callback, $isList ? '[' : '{');
				$subSeparator = $separator . $separatorDelta;
				$first = true;
				foreach( $value as $key => $subValue ) {
					if( !$first ) call_user_func($callback, ',');
					call_user_func($callback, $subSeparator);
					if( !$isList ) {
						call_user_func($callback, self::encode((string)$key));
						call_user_func($callback, ': ');
					}
					self::prettyPrint( $subValue, $callback, $subSeparator, $separatorDelta );
					$first = false;
				}
				call_user_func($callback, $separator);
				call_user_func($callback, $isList ? ']' : '}');
			}
		} else if( is_scalar($value) or $value === null ) {
			$json = self::encode($value);
			call_user_func($callback, self::encode($value) );
		} else {
			throw new Exception(
				__CLASS__."#".__FUNCTION__." only works on scalars and arrays.  ".
				gettype($value).(is_object($value) ? " (".get_class($value).")" : '').
				" given.");
		}
	}
	
	public static function prettyEncode( $value, $separator="\n", $separatorDelta="\t" ) {
		$collector = new EarthIT_Collector();
		self::prettyPrint( $value, $collector, $separator, $separatorDelta );
		return (string)$collector;
	}
}
