<?php

/*
 * Static utility functions to help with MVC thingies
 */
class EarthIT_Util {
	/** @api */
	public static function output( $thing ) {
		if( is_scalar($thing) ) {
			echo $thing;
		} else if( $thing instanceof Nife_Blob ) {
			$thing->writeTo( array('EarthIT_Util','output') );
		} else {
			throw new Exception("Don't know how to write ".var_export($thing,true)." to output.");
		}
	}
	
	/**
	 * Returns an output function that simply echoes whatever is fed to it.
	 * @api
	 */
	public static function getEchoFunction() {
		return array('EarthIT_Util','output');
	}
	
	/**
	 * Returns true if the functon provided is the echo function.
	 * This may be useful when implementing blobs that would
	 * otherwise need to use output buffering to collect output
	 * before sending it elsewhere.
	 * @api
	 */
	public static function isEchoFunction( $f ) {
		return self::getEchoFunction() == $f;
	}
}