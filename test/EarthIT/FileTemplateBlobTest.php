<?php

use PHPUnit\Framework\TestCase;

class EarthIT_FileTemplateBlobTest extends TestCase
{
	protected $blob;
	protected function setUp() : void {
		$this->blob = new EarthIT_FileTemplateBlob(__DIR__.'/hello.php', array('name'=>'World'));
		$this->expectedOutput = "Hello, World!\n";
	}
	
	public function testDirectOutput() {
		ob_start();
		$this->blob->writeTo(EarthIT_Util::getEchoFunction());
		$rez = ob_get_clean();
		
		$this->assertEquals($this->expectedOutput, $rez);
	}
	
	public function testToString() {
		$this->assertEquals($this->expectedOutput, (string)$this->blob);
	}
	
	public function testCallbackOutput() {
		$collector = new EarthIT_Collector();
		$this->blob->writeTo($collector);
		$this->assertEquals($this->expectedOutput, (string)$collector);
	}
}
