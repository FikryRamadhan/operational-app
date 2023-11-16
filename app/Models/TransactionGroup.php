<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionGroup extends Model
{
	use HasFactory;
	use SoftDeletes;

	protected $guarded = [];

	/**
	 * 	Relationship methods
	 * */
	public function transactions()
	{
		return $this->belongsTo('App\Models\Transaction', 'id_transaction_group');
	}

	public function incomeTransactions()
	{
		return $this->hasMany('App\Models\Transaction', 'id_transaction_group')
					->where('type', 'Income');
	}

	public function expenseTransactions()
	{
		return $this->hasMany('App\Models\Transaction', 'id_transaction_group')
					->where('type', 'Expense');
	}



	/**
	 * 	CRUD methods
	 * */
	public static function createTransactionGroup(array $request)
	{
		return self::create($request);
	}

	public function updateTransactionGroup(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteTransactionGroup()
	{
		return $this->delete();
	}



	/**
	 * 	Helper methods
	 * */
	public function isActive()
	{
		return $this->is_active == 'yes';
	}

	public function isActiveText()
	{
		return $this->isActive() ? 'Aktif' : 'Nonaktif';
	}

	public function isActiveHtml()
	{
		return $this->isActive() ? '<span class="text-success"> Aktif </span>' : '<span class="text-danger"> Nonaktif </span>';
	}

	public function totalIncome()
	{
		$total = 0;
		foreach($this->incomeTransactions as $transaction) {
			$total += $transaction->nominal;
		}

		return $total;
	}

	public function totalExpense()
	{
		$total = 0;
		foreach($this->expenseTransactions as $transaction) {
			$total += $transaction->nominal * -1;
		}

		return $total;
	}

	public function totalIncomeFormatted()
	{
		return 'Rp. '.number_format($this->totalIncome());
	}

	public function totalExpenseFormatted()
	{
		return 'Rp. '.number_format($this->totalExpense());
	}

	public function totalBalance()
	{
		return $this->totalIncome() - $this->totalExpense();
	}

	public function totalBalanceFormatted()
	{
		$total = $this->totalBalance();
		if($total >= 0) {
			return 'Rp. '.number_format($total);
		} else {
			return '- Rp. '.number_format($total * -1);
		}
	}

	public function totalBalanceFormattedHtml()
	{
		$total = $this->totalBalance();
		if($total >= 0) {
			return 'Rp. '.number_format($total);
		} else {
			return '- Rp. '.number_format($total * -1);
		}
	}




	/**
	 *	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'transaction_groups.*' ]);

		return \Datatables::eloquent($data)
			->editColumn('is_active', function($data){
				return $data->isActiveHtml();
			})
			->addColumn('action', function ($data) {
				$action = '
					<div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item edit" href="javascript:void(0);" data-edit-href="'.route('transaction_group.update', $data->id).'" data-get-href="'.route('transaction_group.get', $data->id).'">
								<i class="fas fa-pencil-alt mr-1"></i> Edit
							</a>
							<a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong>'.$data->transaction_group_name.'</strong>?" data-delete-href="'.route('transaction_group.destroy', $data->id).'">
								<i class="fas fa-trash mr-1"></i> Hapus
							</a>
						</div>
					</div>';
				return $action;
			})
			->rawColumns([ 'is_active', 'action' ])
			->make(true);
	}


	public static function importFromExcel($request)
	{
		$amount = 0;

		if(!empty($request->file_excel))
		{
			$file = $request->file('file_excel');
			$filename = date('YmdHis_').rand(100,999).'.'.$file->getClientOriginalExtension();
			$file->move(storage_path('app/public/temp_files'), $filename);
			$path = storage_path('app/public/temp_files/'.$filename);
			$parseData = \App\MyClass\SimpleXLSX::parse($path);

			if($parseData)
			{
				$iter = 0;
				foreach($parseData->rows() as $row)
				{
					$iter++;
					if($iter == 1) continue;

					if(!empty($row[0])) {
						$transactionGroup = self::where('transaction_group_name', $row[0])->first();

						if(!$transactionGroup) {
							\DB::beginTransaction();
							try {
								self::create([
									'transaction_group_name'	=> $row[0],
								]);
								$amount++;
								\DB::commit();
							} catch (\Exception $e) {
								\DB::rollback();
							}
						}
					}

				}
			}

			\File::delete($path);
		}

		return $amount;
	}
	
}
