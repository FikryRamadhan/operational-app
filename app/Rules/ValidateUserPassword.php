<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class ValidateUserPassword implements Rule
{
	public $user;
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct($userId)
	{
		$this->user = User::find($userId);
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
		return $this->user->comparePassword($value);
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Password salah.';
	}
}
