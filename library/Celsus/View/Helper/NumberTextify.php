<?php

class Celsus_View_Helper_NumberTextify {

	protected $_view;

    public function setView(Zend_View_Interface $view) {
        $this->_view = $view;
    }

    protected static $_units = array(
    	1 => "One",
    	2 => "Two",
    	3 => "Three",
    	4 => "Four",
    	5 => "Five",
    	6 => "Six",
    	7 => "Seven",
    	8 => "Eight",
    	9 => "Nine",
    );

    protected static $_teens = array(
    	0 => "Ten",
    	1 => "Eleven",
    	2 => "Twelve",
    	3 => "Thirteen",
    	4 => "Fourteen",
    	5 => "Fifteen",
    	6 => "Sixteen",
    	7 => "Seventeen",
    	8 => "Eighteen",
    	9 => "Nineteen",
    );

    protected static $_tens = array(
    	2 => "Twenty",
    	3 => "Thirty",
    	4 => "Forty",
    	5 => "Fifty",
    	6 => "Sixty",
    	7 => "Seventy",
    	8 => "Eighty",
    	9 => "Ninety"
    );

    protected static $_orders = array(
    	3 => "Hundred",
    	4 => "Thousand",
    	7 => "Million",
    	10 => "Billion",
    	13 => "Trillion"
    );

    /**
     * Turns an integer into a text representation of the same.
     *
     * @param string $resource
     * @return string
     */
    public function numberTextify($number) {
    	if (!$number) {
    		return "Zero";
    	}

    	$return = array();
    	$number = "$number";
    	if (!ctype_digit($number)) {
    		throw new Celsus_Exception("Must supply digits only to number textify.");
    	}
    	$magnitude = strlen($number);

    	for ($i = 0; $i < $magnitude; $i++) {
    		$unit = $number[$i];
    		if (!$unit) {
    			// No value at this position.
    			continue;
    		}
    		$order = $magnitude - $i;
    		if ($order == 1) {
    			// Units.
    			if ($magnitude > 2 && ($number[$magnitude - 2] == "0")) {
    				// This is part of a larger number, and there are no tens.
    				$return[] = "And";
    			}
    			$return[] = self::$_units[$unit];
    		} elseif ($order == 2) {
    			// Tens
    			if ($magnitude > 2) {
    				// This is part of a larger number.
    				$return[] = "And";
    			}
    			if ($unit == 1) {
    				// Fifteen
    				$return[] = self::$_teens[intval($number[$i + 1])];

    				// We've already parsed the units, so skip the next number.
    				$i++;
    			} else {
    				// Twenty
    				$return[] = self::$_tens[$unit];
    			}
    		} else {
    			if (array_key_exists($order, self::$_orders)) {
    				// e.g. One Million.
    				$return[] = self::$_units[$unit] . ' ' . self::$_orders[$order];
    			} else {
    				if ($order % 3 == 0) {
    					// e.g. Five Hundred Thousand
    					$interim = ($number[$i + 1] || $number[$i + 2]) ? '' : (' ' . self::$_orders[$order - 2]);
    					$return[] = self::$_units[$unit] . ' ' . self::$_orders[3] . $interim;
    				} elseif ($order % 3 == 2) {
    					// Fifty Thousand
		    			if ($magnitude > $order && ($number[$i - 1] != "0")) {
		    				// This is part of a larger number.
		    				$return[] = "And";
		    			}
		    			if ($unit == 1) {
		    				// Fifteen
		    				$return[] = self::$_teens[intval($number[$i + 1])] . ' ' . self::$_orders[$order - 1];

		    				// We've already parsed the units, so skip the next number.
		    				$i++;
		    			} else {
		    				// Twenty
		    				$interim = ($number[$i + 1]) ? '' : (' ' . self::$_orders[$order - 1]);
		    				$return[] = self::$_tens[$unit]  . $interim;
		    			}
    				} elseif ($order % 3 == 1) {
    					// One Hundred And Five Thousand
		    			if ($magnitude > $order) {
		    				// This is part of a larger number.
		    				$return[] = "And";
		    			}
		    			$return[] = self::$_units[$unit];
    				}
    			}
    		}
    	}
    	return implode(" ", $return);
    }
}
?>