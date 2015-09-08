<?php
	namespace gburtini;
	if(!function_exists("__")) {	// allows you to define your own translation architecture.
		function __($string) { 
			return $string;
		}
	}


	class HumanizePHP {
		static function naturaltime($timestamp, $depth=2, $time=null, $wrap_string=true) {
			// humanizes a timestamp, for example as a result from strtotime. naturaltime(time() + 60) returns "in 1 minute."
			// depth indicates how many subscales to break the result down to, for example naturaltime(time() + 61) returns "in 1 minute", but naturaltime(time() + 61, 2) returns "in 1 minute and 1 second"
			if($time === null)
				$time = time();
			$age = $time - $timestamp;
			if ($age == 0 && $wrap_string)
				return __("just now");
			$original = $age;
		
			// credit to Rich Remer for this data: http://stackoverflow.com/questions/8629788/php-strtotime-reverse
			$scales = array( 
				array('second', 'seconds', 60),
				array('minute', 'minutes', 60),
				array('hour', 'hours', 24),
				array('day', 'days', 30),	// this would be better if we actually figured out the months... like strtotime does
				array('month', 'months', 12),
				array('year', 'years', 10),
				array('decade', 'decades', 10),
				array('century', 'centuries', 1000),
				array('millenium', 'millenia', PHP_INT_MAX)
		    	);
		
			$totalfactor = 1;
			foreach ($scales as $item) {
				$singular = $item[0];
				$plural = $item[1];
				$factor = intval($item[2]);
		
				$age = abs($age);
				
				// TODO: optionally combine with apnumber to change numbers to words.
				if($age == 0) return;
				if ($age == 1)
					$response = sprintf(__("%d $singular"), 1);
				else if($age < $factor)
					$response = sprintf(__("%d $plural"), $age);
		
		
				if(!empty($response)) {
					break;
				}
				$totalfactor *= $factor;
				$age = (int)($age / $factor);
			}
		
			if($depth > 1) {
				// recurse in if we wish to fill out the remainder
				$next = (HumanizePHP::naturaltime(
					// get a time in seconds relative to zero for whatever is leftover after what we've rendered so far
					(($original < 0) ? (1) : (-1)) * ($original % (abs($age) * $totalfactor)), 
					$depth-1, 
					0, 	// reference time of zero
					false	// don't wrap with "in" and "ago" as we're still building the string.
				));
		
				if($next == null) // we return null if there were none of this subelement. I think this should probably recurse one deeper (we may have to skip units), but rounding here is clean sometimes too (you probably don't want to say "1 decade and 4 seconds.")
				return $response;
		
		
				if($depth > 2) // deal with commas. there's probably a better (read: more language agnostic) way to do this (produce a list of $nexts and join them).
					$response = $response . __(", ") . $next;
				else
					$response = $response . __(" and ") . $next;
		
			} 
		
			if($wrap_string) {
				if($original < 0) { $response = sprintf(__("in %s"), $response); }
				else { $response = sprintf(__("%s ago"), $response); }
			}
		
			return $response;
		}


		static function apnumber($number) {
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

		static function intcomma($number, $decimals=0, $decimal='.', $separator=',') {
			return number_format($number, $decimals, $decimal, $separator);
		}


		// smallestAccepted is set to 1 million by default for
		// adherence to the Django Humanize API.
		static function intword($number, $smallestAccepted=1000000, $decimals = 1) {
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

		static function naturalday($timestamp, $format='F j, Y') {
			// this -60 deals with a bug in strtotime on (some?) PHP builds.
			$end_tomorrow = strtotime("+2 days 12:01am")-60;
			$tomorrow = strtotime("tomorrow 12:01am")-60;
			$yesterday = strtotime("yesterday 12:01am")-60;
			$today = strtotime("today 12:01am")-60;
			
			if($timestamp > $yesterday && $timestamp < $today) return "yesterday";
			if($timestamp > $today && $timestamp < $tomorrow) return "today";
			if($timestamp > $tomorrow && $timestamp < $end_tomorrow) return "tomorrow";
			
			return date($format, $timestamp);			
		}

		static function ordinal($value) {
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
		static function checkize($number) {
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
				if($number > 10) {
					$parts[] = $tens[$number[strlen($number)-2]] . " -";
				}
			}

			// special hundreds case (not a multiple of 3).
			if($number > pow(10, 2)) {
				$hundredsCount = $number[strlen($number)-3];
				if($hundredsCount != 0) {
					$parts[] = $singles[$hundredsCount] . " hundred";
				}
			}

			$offset = 3;
			foreach($thousands as $frag) {
				if($number < pow(10,$offset+1)) break;
				$part = substr($number, strlen($number)-$offset-3, 3);
				$parts[] = HumanizePHP::checkize($part) . " {$frag},";
				$offset+=3;
			}
			
			return str_replace(" - ", "-", implode(" ", array_reverse($parts)));	
		}

	}
?>
