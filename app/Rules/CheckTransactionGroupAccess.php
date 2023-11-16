<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\TransactionGroupAccess;

class CheckTransactionGroupAccess implements Rule
{
	private $userId;
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct($userId)
	{
		$this->userId = $userId;
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
		$check = TransactionGroupAccess::where('id_user', $this->userId)
									   ->where('id_transaction_group', $value)
									   ->count();

		return $check == 0;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Akses ke grup transaksi ini sudah ada.';
	}
}
