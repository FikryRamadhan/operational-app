<?php 

namespace App\MyClass;

class Helper
{

	public static function validFloat($number)
	{
		return str_replace(',', '', number_format($number));
	}


	public static function validNumber($number)
	{
		$number = (string) $number;
		$arrNumber = explode('.', $number);
		$result = '';

		$result = str_replace(',', '.', number_format($arrNumber[0]));
		if(count($arrNumber) == 2) {
			$result .= ','.$arrNumber[1];
		}

		return $result;
	}


	public static function tempPath($filename = '')
	{
		return storage_path('docs/temps/'.$filename);
	}


	public static function idPhoneNumberFormat($phone)
	{
		$output = $phone;
		$output = substr($output, 0, 1) == '0'? "62".substr($output, 1) : $output;
		$output = substr($output, 0, 3) == '+62'? substr($output, 1) : $output;
		$output = substr($output, 0, 2) != '62'? "62".$output : $output;

		return $output;
	}
}