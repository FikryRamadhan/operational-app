<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionGroupAccess extends Model
{
	use HasFactory;

	protected $fillable = [ 'id_transaction_group', 'id_user' ];


	/**
	 * 	Relationship methods
	 * */
	public function transactionGroup()
	{
		return $this->belongsTo('App\Models\TransactionGroup', 'id_transaction_group');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User', 'id_user');
	}



	/**
	 * 	CRUD methods
	 * */
	public static function createTransactionGroupAccess(array $request)
	{
		$accesses = self::create($request);
		return $accesses;
	}

	public function updateTransactionGroupAccess(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteTransactionGroupAccess()
	{
		return $this->delete();
	}



	/**
	 * 	Helper methods
	 * */
	public function transactionGroupName()
	{
		return $this->transactionGroup->transaction_group_name ?? '-';
	}

	public function userName()
	{
		return $this->user->name ?? '-';
	}



	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = User::select([ 'users.*' ])
					->where('role', User::ROLE_STAFF)
					->with([ 'transactionGroupAccesses' ]);

		return datatables()->eloquent($data)
			->addColumn('transaction_group_access', function ($data) {
				$html = '';

				if(count($data->transactionGroupAccesses) > 0) {
					foreach($data->transactionGroupAccesses as $access) {
						$html .= '
						<div> '. $access->transactionGroupName() .'
							<a href="javascript:void(0);" class="delete" data-delete-href="'.route('transaction_group_access.destroy', $access->id).'" title="Hapus Akses" data-delete-message="Yakin ingin hapus akses?">
								<i class="fas fa-trash"></i>
							</a>  
						</div>';
					}
				} else {
					$html = '<span class="text-danger"> Tidak Punya Akses </span>';
				}

				return $html;
			})
			->rawColumns(['transaction_group_access'])
			->addIndexColumn()
			->make(true);
	}
}
