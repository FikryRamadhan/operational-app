<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Category;

class UniqueIncomeCategoryName implements Rule
{
	private $exceptId;
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct($exceptId = null)
	{
		$this->exceptId = $exceptId;
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		$category = Category::where('type', 'Income')
							->where('category_name', $value)
							->where('id', '!=', $this->exceptId)
							->first();
		if(!$category) return true;
		return false;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Nama kategori tersebut sudah ada.';
	}
}
