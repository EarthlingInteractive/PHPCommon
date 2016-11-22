<?php

/**
 * Encodes arbitrary data in a format that is 'filename friendly'.
 * 
 * Not indended to be especially pretty, but to provide a way to encode arbitrary parameters into a filename
 * when no other method is suitable.
 *
 * e.g.
 *
 *   users.nameLike-Fred_5F.orderBy-username+emailAddress.json
 *
 * Characters allowed unescaped in values: A-Z, a-z, 0-9
 * Any other characters must be escaped.
 * Dot, dash, underscore, and plus (., -, _, +) have special meaning,
 * similar to &, =, %, and comma (for comma-separated lists) in query
 * strings.
 */
class EarthIT_FilenameParameters
{
	public static function encodeComponent( $str ) {
		$excaped = '';
		$len = strlen($str);
		for( $i=0; $i<$len; ++$i ) {
			$ord = ord($str[$i]);
			if( $ord >= 48 && $ord <= 57 or $ord >= 64 && $ord <= 90 or $ord >= 97 && $ord <= 122 ) {
				$excaped .= $str[$i];
			} else {
				$excaped .= sprintf('_%02X', ord($str[$i]));
			}
		}
		return $excaped;
	}
	
	public static function decodeCallback( $bif ) {
		return chr(hexdec($bif[1]));
	}
	
	public static function decodeComponent( $str ) {
		return preg_replace_callback( '/_([0-9A-Fa-f]{2})/', array('EarthIT_FilenameParameters','decodeCallback'), $str );
	}

	/**
	 * @param array $lists a list of lists of lists to be encoded
	 * @return string
	 */
	public static function encodeL3( array $lists ) {
		$parts = array();
		foreach( $lists as $l ) {
			if( $l === '' ) continue;
			if( is_scalar($l) ) $l = array($l);
			if( !is_array($l) ) throw new Error("Can't encode non-array, non-scalar");
			if( count($l) == 0 ) continue;
			
			$parts1 = array();
			foreach( $l as $c ) {
				if( is_scalar($c) ) $c = array($c);
				
				$parts2 = array();
				foreach( $c as $d ) {
					$parts2[] = self::encodeComponent($d);
				}
				$parts1[] = implode('+', $parts2);
			}
			$parts[] = implode('-', $parts1);
		}
		return implode('.', $parts);
	}

	public static function decodeL3( $paramString, array $aliases=array() ) {
		if( strlen($paramString) == 0 ) return array();
		$parts = explode('.', $paramString);
		$l0 = array();
		foreach( $parts as $p ) {
			if( strlen($p) == 0 ) continue;
			if( isset($aliases[$p]) ) $p = $aliases[$p];
			
			if( is_array($p) ) {
				// Alias is already decoded
				$l1 = $p;
			} else {
				$parts1 = explode('-',$p);
				$l1 = array();
				foreach( $parts1 as $p1 ) {
					$parts2 = explode('+', $p1);
					$l2 = array();
					foreach( $parts2 as $p2 ) {
						$l2[] = self::decodeComponent($p2);
					}
					$l1[] = $l2;
				}
			}
			$l0[] = $l1;
		}
		return $l0;
	}
}
