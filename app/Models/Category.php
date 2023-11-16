<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
	use HasFactory;
	use SoftDeletes;

	protected $guarded = [];


	/**
	 * 	CRUD methods
	 * */
	public static function createIncomeCategory(array $request)
	{
		return self::create(array_merge($request, [
			'type'	=> 'Income'
		]));
	}

	public static function createExpenseCategory(array $request)
	{
		return self::create(array_merge($request, [
			'type'	=> 'Expense'
		]));
	}

	public function updateCategory(array $request)
	{
		$this->update($request);
		return $this;
	}

	public function deleteCategory()
	{
		return $this->delete();
	}



	/**
	 * 	Helper methods
	 * */
	public function isTypeIncome()
	{
		return $this->type == 'Income';
	}

	public function isTypeExpense()
	{
		return $this->type == 'Expense';
	}




	/**
	 *  Static method
	 * */
	public static function incomeDataTable($request)
	{
		return self::dataTable($request, 'Income');
	}

	public static function expenseDataTable($request)
    {
        return self::dataTable($request, 'Expense');
    }

	public static function dataTable($request, $type)
	{
		$data = self::where('type', $type);

		return datatables()->eloquent($data)
			->addColumn('action', function ($data) {
				$action = '
					<div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item edit" href="javascript:void(0);" data-edit-href="'.route('income_category.update', $data->id).'" data-get-href="'.route('income_category.get', $data->id).'">
								<i class="fas fa-pencil-alt mr-1"></i> Edit
							</a>
							<a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong>'.$data->category_name.'</strong>?" data-delete-href="'.route('income_category.destroy', $data->id).'">
								<i class="fas fa-trash mr-1"></i> Hapus
							</a>
						</div>
					</div>';
				return $action;
			})
			->rawColumns(['action'])
			->addIndexColumn()
			->make(true);
	}

	public static function importIncomeCategoryFromExcel($request)
	{
		return self::importFromExcel($request, 'Income');
	}

	public static function importExpenseCategoryFromExcel($request)
	{
		return self::importFromExcel($request, 'Expense');
	}

	public static function importFromExcel($request, $type)
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
						$category = self::where('category_name', $row[0])
										->where('type', $type)
										->first();

						if(!$category) {
							\DB::beginTransaction();
							try {
								self::create([
									'category_name'	=> $row[0],
									'type'			=> $type,
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

	public function transaction()
	{
		return $this->hasMany(Transactions::class);
	}
}
