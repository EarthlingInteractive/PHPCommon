<?php

class EarthIT_FileTemplateBlob
{
	/**
	 * @var string the path to the template file
	 */
	protected $templateFile;
	/**
	 * @var array the variables to be used in the template
	 */
	protected $vars;
	
	public function __construct( string $templateFile, array $vars=array() ) {
		$this->templateFile = $templateFile;
		$this->vars = $vars;
	}
	
	public function getLength() {
		// No way to know without evaluating the template
		return null;
	}
	
	protected function outputDirectly() {
		extract($this->vars);
		require $this->templateFile;
	}
	
	public function __toString() {
		ob_start();
		try {
			$this->outputDirectly();
		} catch( Exception $e ) {
			ob_end_clean();
			throw $e;
		}
		return ob_get_clean();
	}
	
	public function writeTo( $outputter ) {
		if( EarthIT_Util::isEchoFunction($outputter) ) {
			$this->outputDirectly();
		} else {
			call_user_func( $outputter, $this->__toString() );
		}
	}
}
