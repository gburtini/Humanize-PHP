<?php
	require_once "Humanize.php";

	for($i=0;$i<100;$i++) {
		echo HumanizePHP::ordinal($i) . "\t" . HumanizePHP::apnumber($i) . "\t" . HumanizePHP::intword($i*22000000) . "\n";
	}


	echo HumanizePHP::naturalday(strtotime("-3 hours")) . "\n";
	echo HumanizePHP::naturalday(strtotime("+24 hours")) . "\n";
	echo HumanizePHP::naturalday(strtotime("-25 hours")) . "\n";
	echo HumanizePHP::naturalday(strtotime("-3 weeks")) . "\n";	
?>
