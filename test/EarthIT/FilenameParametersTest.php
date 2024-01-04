<?php

use PHPUnit\Framework\TestCase;

class EarthIT_FilenameParametersTest extends TestCase
{
	public function testEncodeL3() {
		$l2 = array(
			'',
			'foo',
			array('bar','baz'),
			array(),
			array('bestBeers','are',array('boxer','blatz','schlitz')),
			'_- %:.+'
		);
		$this->assertEquals(
			'foo.bar-baz.bestBeers-are-boxer+blatz+schlitz._5F_2D_20_25_3A_2E_2B',
			EarthIT_FilenameParameters::encodeL3($l2)
		);
	}
	
	public function testDecodeL3() {
		$this->assertEquals(
			array(
				array(array('foo')),
				array(array('bar'),array('baz')),
				array(array('bestBeers'),array('are'),array('boxer','blatz','schlitz')),
				array(array('_- %:.+')),
				array(array(''),array('',''),array('')),
			),
			EarthIT_FilenameParameters::decodeL3('..foo..bar-baz.bestBeers-are-boxer+blatz+schlitz.._5F_2D_20_25_3A_2E_2B.-+-')
		);
	}
	
	public function testDecodeL3WithArrayAlias() {
		$this->assertEquals(
			array(
				array(array('thing'),array('foo','bar of_soap')),
				array(array('baz'))
			),
			EarthIT_FilenameParameters::decodeL3('..yukyuk.baz', array(
				'yukyuk' => array(array('thing'),array('foo','bar of_soap'))
			))
		);
	}
	
	public function testDecodeL3WithStringAlias() {
		$this->assertEquals(
			array(
				array(array('thing'),array('foo','bar of_soap')),
				array(array('baz'))
			),
			EarthIT_FilenameParameters::decodeL3('..ignoreme.yukyuk.baz', array(
				'yukyuk' => 'thing-foo+bar_20of_5Fsoap',
				'ignoreme' => '',
			))
		);
	}
}
