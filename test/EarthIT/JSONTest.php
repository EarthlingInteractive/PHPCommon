<?php

class EarthIT_JSONTest extends PHPUnit_Framework_TestCase
{
	public function testEncode() {
		$this->assertEquals('"hi"', EarthIT_JSON::encode('hi'));
	}
	public function testEncodeError() {
		if( PHP_VERSION_ID < 50500 ) {
			$this->markTestSkipped("PHP < 5.5 doesn't throw errors when json_encode is asked to encode a malformed string.");
			return;
		}
		
		$caught = false;
		try {
			EarthIT_JSON::encode(substr("☹",0,1));
		} catch( Exception $e ) {
			$caught = true;
		}
		$this->assertTrue($caught, "Expected an exception to get thrown when encoding a string with invalid UTF-8 sequences.");
	}
}
