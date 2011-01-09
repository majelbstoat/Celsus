<?php

class Celsus_Data_Formatter_CSV implements Celsus_Data_Formatter_Interface {

	/**
	 * Returns the data formatted as CSV data.
	 *
	 * Uses output buffering to capture data written to the output
	 * file handle using and PHP's native fputcsv().
	 *
	 * @param Celsus_Data_Interface $object
	 * @return string
	 */
	public static function format(Celsus_Data_Interface $object) {
		ob_start();
		$output = fopen('php://output', 'w');
		$data = $object->toArray();
		$headers = array_keys($data);
		fputcsv($output, $headers);
		fputcsv($output, $data);
		fclose($output);
		return ob_get_clean();
	}
}