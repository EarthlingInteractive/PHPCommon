<?php

use PHPUnit\Framework\TestCase;

class EarthIT_JSONTest_FunkyObject1 implements JsonSerializable {
	public function jsonSerialize() : object {
		return (object)array(
			'foo' => 'bar',
			'baz' => 'quux'
		);
	}
}

class EarthIT_JSONTest_FunkyObject2 {}

class EarthIT_JSONTest extends TestCase
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
	
	public function testEncodeExplicitEmptyList() {
		$arr = array( EarthIT_JSON::JSON_TYPE => EarthIT_JSON::JT_LIST, 'foo'=>1, 'bar'=>2, 'baz'=>3 );
		$str = EarthIT_JSON::prettyEncode($arr);
		$this->assertEquals( "[\n\t1,\n\t2,\n\t3\n]", $str );
	}
	
	public function testEncodeExplicitListWithElements() {
		$arr = array( EarthIT_JSON::JSON_TYPE => EarthIT_JSON::JT_LIST );
		$str = EarthIT_JSON::prettyEncode($arr);
		$this->assertEquals( '[]', $str );
	}
	
	public function testEncodeExplicitEmptyObject() {
		$arr = array( EarthIT_JSON::JSON_TYPE => EarthIT_JSON::JT_OBJECT );
		$str = EarthIT_JSON::prettyEncode($arr);
		$this->assertEquals( '{}', $str );
	}
	
	public function testEncodeJsonSerializable() {
		$str = EarthIT_JSON::prettyEncode(new EarthIT_JSONTest_FunkyObject1());
		$this->assertEquals("{\n\t\"foo\": \"bar\",\n\t\"baz\": \"quux\"\n}", $str);
	}
	
	public function testEncodeEmptyObject() {
		$str = EarthIT_JSON::prettyEncode(new EarthIT_JSONTest_FunkyObject2());
		$this->assertEquals("{}", $str);
	}
}
