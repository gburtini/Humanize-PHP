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

		function intword($number) {
			$number = intval($number);
			if($number < 1000000) return $number;

			if($number < 1000000000) {
				$newValue = $number / 1000000.0;
				return HumanizePHP::intcomma($newValue, 1) . " million";
			}

			// senseless on a 32 bit system probably.
			if($number < 1000000000000) {
				$newValue = $number / 1000000000.0;
				return HumanizePHP::intcomma($newValue, 1) . " billion";
			}

			if($number < 1000000000000000) {
				$newValue = $number / 1000000000000.0;
				return HumanizePHP::intcomma($newValue, 1) . " trillion";
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
	}
?>
