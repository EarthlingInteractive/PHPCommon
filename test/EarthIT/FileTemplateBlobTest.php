<?php

class EarthIT_FileTemplateBlobTest extends PHPUnit_Framework_TestCase
{
	protected $blob;
	protected function setUp() {
		$this->blob = new EarthIT_FileTemplateBlob(__DIR__.'/hello.php', array('name'=>'World'));
		$this->expectedOutput = "Hello, World!\n";
	}
	
	public function testDirectOutput() {
		ob_start();
		$this->blob->writeTo(Nife_Util::getEchoFunction());
		$rez = ob_get_clean();
		
		$this->assertEquals($this->expectedOutput, $rez);
	}
	
	public function testToString() {
		$this->assertEquals($this->expectedOutput, (string)$this->blob);
	}
	
	public function testCallbackOutput() {
		$collector = new Nife_Collector();
		$this->blob->writeTo($collector);
		$this->assertEquals($this->expectedOutput, (string)$collector);
	}
}
