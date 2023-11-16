<?php 

namespace App\MyClass;

class Template
{

	public static function required()
	{
		return "<span class='text-danger'> * </span>";
	}


	public static function requiredBanner()
	{
		return "<div class='banner banner-info'>
					Kolom bertanda ".self::required()." wajib diisi.
				</div>";
	}

}
