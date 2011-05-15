<?php
	class HumanizePHP {
		function apnumber($number) {
			$replace = array(0=>"zero", 1=>"one", 2=>"two", 3=>"three",
					4=>"four", 5=>"five", 6=>"six", 7=>"seven",
					8=>"eight", 9=>"nine");

			$num = intval($number);		// warning, intval returns 0 on non-integers.
			if($num == 0) {
				if(!strstr('0', $number)) return $number;	// not a zero
			}
			
			if(array_key_exists($num, $replace)) {
				return $replace[$num];
			} else {
				return $number;
			}
		}

		function intcomma($number, $decimals=0, $decimal='.', $separator=',') {
			return number_format($number, $decimals, $decimal, $separator);
		}


		// smallestAccepted is set to 1 million by default for
		// adherence to the Django Humanize API.
		function intword($number, $smallestAccepted=1000000, $decimals = 1) {
			$number = intval($number);
			if($number < $smallestAccepted) return $number;
			
			if($number < 100) {
				return HumanizePHP::intcomma($number, $decimals);
			}

			if($number < 1000) {
				$newValue = $number / 100;
				return HumanizePHP::intcomma($newValue, $decimals) . " hundred";
			}

			if($number < 100000) {
				$newValue = $number / 1000.0;
				return HumanizePHP::intcomma($newValue, $decimals) . " thousand";
			}

			if($number < 1000000) {
				$newValue = $number / 100000.0;
				return HumanizePHP::intcomma($newValue, $decimals) . " hundred thousand";
			}

			if($number < 1000000000) {
				$newValue = $number / 1000000.0;
				return HumanizePHP::intcomma($newValue, $decimals) . " million";
			}

			// senseless on a 32 bit system probably.
			if($number < 1000000000000) {
				$newValue = $number / 1000000000.0;
				return HumanizePHP::intcomma($newValue, $decimals) . " billion";
			}

			if($number < 1000000000000000) {
				$newValue = $number / 1000000000000.0;
				return HumanizePHP::intcomma($newValue, $decimals) . " trillion";
			} 

			return $number;	// too big.			
		}

		function naturalday($timestamp, $format='F j, Y') {
			$end_tomorrow = strtotime("+2 days 12:01am")-60;
			$tomorrow = strtotime("tomorrow 12:01am")-60;
			$yesterday = strtotime("yesterday 12:01am")-60;
			$today = strtotime("today 12:01am")-60;
			
			if($timestamp > $yesterday && $timestamp < $today) return "yesterday";
			if($timestamp > $today && $timestamp < $tomorrow) return "today";
			if($timestamp > $tomorrow && $timestamp < $end_tomorrow) return "tomorrow";
			
			return date($format, $timestamp);			
		}

		function ordinal($value) {
			$number = intval($value);
			if($number == 0) return $value; 	// could be a bad string or just a 0.

			$specialCheck = $number % 100;
			if($specialCheck == 11 || $specialCheck == 12 || $specialCheck == 13) { return $number . "th"; }
		
			$leastSignificant = $number % 10;
			switch($leastSignificant) {
				case 1:
					$end = "st";
				break;
				case 2: 
					$end = "nd";
				break;
				case 3:
					$end = "rd";
				break;
				default:
					$end = "th";
				break;
			}	
			return $number . $end;
		}

		// not part of the Django API.
		// takes a number and turns it in to a string viable for writing on a cheque
		// 124 -> one hundred and twenty four.
		// 65535 -> sixty five thousand, five hundred and thirty five
		function checkize($number) {
			$singles = array(0=>"zero", 1=>"one", 2=>"two", 
					 3=>"three", 4=>"four", 5=>"five",
					 6=>"six", 7=>"seven", 8=>"eight",
					 9=>"nine");
			$ten_singles = array(0=>"ten", 1=>"eleven", 2=>"twelve",
				      3=>"thirteen", 4=>"fourteen", 5=>"fifteen",
				      6=>"sixteen", 7=>"seventeen", 8=>"eighteen",
				      9=>"nineteen");	// special case.
			$tens = array(2=>"twenty", 3=>"thirty", 4=>"fourty", 5=>"fifty", 6=>"seventy",
				      8=>"eighty", 9=>"ninety");
			$thousands = array("thousand", "million", "billion", "trillion", "quadrillion");

			$number = strval(intval($number));
			$parts = array();

			// check the special "teens" case.
			$specialCheck = $number % 100;
			if($specialCheck <= 19 && $specialCheck >= 10) {
				$parts[] = $ten_singles[$number[strlen($number)-1]];
			} else {
				$parts[] = $singles[$number[strlen($number)-1]];
				$parts[] = $tens[$number[strlen($number)-2]];
			}

			// need to handle "$singles hundred"
			// need to handle thousands.
			
			return implode(" ", array_reverse($parts));	
		}

	}
?>
