<?php

class EarthIT_FilenameParametersTest extends PHPUnit_Framework_TestCase
{
	public function testEncodeL2() {
		$l2 = array(
			'',
			'foo',
			array('bar','baz'),
			array(),
			array('boxer','blatz','schlitz'),
			'_- %:.+'
		);
		$this->assertEquals(
			'foo.bar-baz.boxer-blatz-schlitz._5F_2D_20_25_3A_2E_2B',
			EarthIT_FilenameParameters::encodeL2($l2)
		);
	}
	
	public function testDecodeL2() {
		$this->assertEquals(
			array(
				array('foo'),
				array('bar','baz'),
				array('boxer','blatz','schlitz'),
				array('_- %:.+'),
				array('','xyz',''),
			),
			EarthIT_FilenameParameters::decodeL2('..foo..bar-baz.boxer-blatz-schlitz.._5F_2D_20_25_3A_2E_2B.-xyz-')
		);
	}
}
