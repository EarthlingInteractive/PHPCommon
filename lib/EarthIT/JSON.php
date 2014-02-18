<?php

class EarthIT_JSON
{
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
	
	public static function decode( $thing ) {
		$thing = trim($thing);
		if( $thing == '' ) throw new EarthIT_Error_JSONDecodeError("Attempted to JSON-decode empty string.");
		if( $thing == 'null' ) return null;
		$value = json_decode($thing, true);
		if( $value === null ) {
			$report_thing = strlen($thing) < 256 ? $thing : substr($thing,0,253)."...";
			$message = function_exists('json_last_error_msg') ? json_last_error_msg() : self::jsonDecodeMessage(json_last_error());
			throw new EarthIT_Error_JSONDecodeError("Error parsing JSON: $message; JSON: $report_thing");
		}
		return $value;
	}
}
