<?php

/**
 * Provides functions which assist in generating a shortened representation of a large number.
 *
 * The codebook is set at 64 items because we can do fast calculations with a power of 2.
 *
 * @author majelbstoat
 */
class Celsus_Encoder {

	//const CODEBOOK = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-@';
	const CODEBOOK = '-@0123456789aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ';

	/**
	 * Encodes a number into a base 64 representation.
	 *
	 * After code from WoLpH http://stackoverflow.com/questions/1119722/base-62-conversion-in-python
	 *
	 * @param unknown_type $number
	 */
	public static function encode($number) {

		$return = '';
		$codebook = self::CODEBOOK;
		while ($number != 0) {
			// Modulo 64.
			$return .= $codebook[$number & 63];

			// Divide by 64.
			$number >>= 6;
		}
		return strrev($return);

	}

	public static function decode($string) {
		$lookup = array_flip(str_split(self::CODEBOOK));
		$length = strlen($string);
		$return = 0;

		for ($i = 0; $i < $length; $i++) {
			$return = ($return << 6) + $lookup[$string[$i]];
		}
		return $return;
	}


}
