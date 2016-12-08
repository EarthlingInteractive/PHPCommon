<?php

class EarthIT_JSON_PerformanceTest
{
	protected $someBigOldStructure;
	
	public function __construct() {
		$s = array();
		for( $i=0; $i<1000; ++$i ) {
			$t = array();
			for( $j=0; $j<10; ++$j ) {
				$u = array();
				for( $k=0; $k<5; ++$k ) {
					$u[] = "abc".$k;
				}
				$t["j".$j] = $u;
			}
			$s["i".$i] = $t;
		}
		$this->someBigOldStructure = $s;
		$this->someBigOldStructureEncoded = json_encode($s);
	}
	
	public function ptest( $encoder, $name ) {
		$startTime = microtime(true);
		$encoded = call_user_func($encoder, $this->someBigOldStructure);
		$endTime = microtime(true);

		$reencoded = json_encode(json_decode($encoded));
		if( $reencoded != $this->someBigOldStructureEncoded ) {
			throw new Error("$name failed to encode the original structure");
		}
		
		return $endTime - $startTime;
	}
	
	public function __invoke() {
		$encoders = array(
			'json_encode with pretty-print' => function($v) { return json_encode($v, JSON_PRETTY_PRINT)."\n"; },
			'EarthIT_JSON::prettyEncode' => function($v) { return EarthIT_JSON::prettyEncode($v); },
			'EarthIT_JSON_HybridJSONBlob(0)' => function($v) {
				$b = new EarthIT_JSON_HybridJSONBlob($v, 0);
				return (string)$b;
			},
			'EarthIT_JSON_HybridJSONBlob(1)' => function($v) {
				$b = new EarthIT_JSON_HybridJSONBlob($v, 1);
				return (string)$b;
			},
			'EarthIT_JSON_HybridJSONBlob(2)' => function($v) {
				$b = new EarthIT_JSON_HybridJSONBlob($v, 2);
				return (string)$b;
			},
		);
		
		$times = array();
		foreach( $encoders as $name => $encoder ) {
			$times[$name] = $this->ptest($encoder, $name);
		}
		
		foreach( $times as $name => $time ) {
			printf("% 40s % 4.8f\n", $name, $time);
		}
	}
}

require_once __DIR__.'/../../vendor/autoload.php';

$t = new EarthIT_JSON_PerformanceTest();
$t->__invoke();
