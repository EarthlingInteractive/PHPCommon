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
	 * @param array $lists a list of lists to be encoded
	 * @return string
	 */
	public static function encodeL2( array $lists ) {
		$parts = array();
		foreach( $lists as $l ) {
			if( $l === '' ) continue;
			if( is_scalar($l) ) $l = array($l);
			if( !is_array($l) ) throw new Error("Can't encode non-array, non-scalar");
			if( count($l) == 0 ) continue;
			$sectionParts = array();
			foreach( $l as $i ) {
				$sectionParts[] = self::encodeComponent($i);
			}
			$parts[] = implode('-', $sectionParts);
		}
		return implode('.', $parts);
	}

	public static function decodeL2( $paramString, array $aliases=array() ) {
		if( strlen($paramString) == 0 ) return array();
		$parts = explode('.', $paramString);
		$l2s = array();
		foreach( $parts as $p ) {
			if( strlen($p) == 0 ) continue;
			if( isset($aliases[$p]) ) {
				$l2s = array_merge($aliases[$p], $l2s);
			} else {
				$encodedComponents = explode('-', $p);
				$decodedComponents = array();
				foreach( $encodedComponents as $e ) {
					$decodedComponents[] = self::decodeComponent($e);
				}
				$l2s[] = $decodedComponents;
			}
		}
		return $l2s;
	}
}
