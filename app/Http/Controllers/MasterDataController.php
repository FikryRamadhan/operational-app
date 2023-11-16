<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionGroup;
use App\Models\Category;
use App\MyClass\Validations;
use Illuminate\Support\Facades\DB;

class MasterDataController extends Controller
{
	/**
	 * 	Transaction Group
	 * */
	public function transactionGroupIndex(Request $request)
	{
		if($request->ajax()) {
			return TransactionGroup::dataTable($request);
		}

		return view('transaction_group.index', [
			'title'			=> 'Grup Transaksi',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Grup Transaksi',
					'link'	=> route('transaction_group')
				]
			]
		]);
	}

	public function transactionGroupStore(Request $request)
	{
		Validations::validateTransactionGroup($request);
		DB::beginTransaction();

		try {
			TransactionGroup::createTransactionGroup($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function transactionGroupGet(TransactionGroup $transactionGroup)
	{
		try {
			return \Res::success([
				'transactionGroup' => $transactionGroup
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}

	public function transactionGroupUpdate(Request $request, TransactionGroup $transactionGroup)
	{
		Validations::validateTransactionGroup($request, $transactionGroup->id);
		DB::beginTransaction();

		try {
			$transactionGroup->updateTransactionGroup($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function transactionGroupDestroy(TransactionGroup $transactionGroup)
	{
		DB::beginTransaction();

		try {
			$transactionGroup->deleteTransactionGroup();
			DB::commit();
			
			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();
			
			return \Res::error($e);
		}
	}

	public function transactionGroupImport(Request $request)
	{
		Validations::validateImport($request);
		try {
			TransactionGroup::importFromExcel($request);
			return \Res::success();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	} 

	/**
	 * 	Income Category
	 * */
	public function incomeCategoryIndex(Request $request)
	{
		if($request->ajax()) {
			return Category::incomeDataTable($request);
		}

		return view('income_category.index', [
			'title'			=> 'Kategori Pemasukan',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kategori Pemasukan',
					'link'	=> route('income_category')
				]
			]
		]);
	}

	public function incomeCategoryStore(Request $request)
	{
		Validations::validateIncomeCategory($request);
		DB::beginTransaction();

		try {
			Category::createIncomeCategory($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function incomeCategoryGet(Category $category)
	{
		try {
			return \Res::success([
				'category' => $category
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}

	public function incomeCategoryUpdate(Request $request, Category $category)
	{
		Validations::validateIncomeCategory($request, $category->id);
		DB::beginTransaction();

		try {
			$category->updateCategory($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function incomeCategoryDestroy(Category $category)
	{
		DB::beginTransaction();

		try {
			$category->deleteCategory();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function incomeCategoryImport(Request $request)
	{
		Validations::validateImport($request);
		try {
			Category::importIncomeCategoryFromExcel($request);
			return \Res::success();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}



	/**
	 * 	Expense Category
	 * */
	public function expenseCategoryIndex(Request $request)
	{
		if($request->ajax()) {
			return Category::expenseDataTable($request);
		}

		return view('expense_category.index', [
			'title'			=> 'Kategori Pengeluaran',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Kategori Pengeluaran',
					'link'	=> route('expense_category')
				]
			]
		]);
	}

	public function expenseCategoryStore(Request $request)
	{
		Validations::validateExpenseCategory($request);
		DB::beginTransaction();

		try {
			Category::createExpenseCategory($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function expenseCategoryGet(Category $category)
	{
		try {
			return \Res::success([
				'category' => $category
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}

	public function expenseCategoryUpdate(Request $request, Category $category)
	{
		Validations::validateExpenseCategory($request, $category->id);
		DB::beginTransaction();

		try {
			$category->updateCategory($request->all());
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function expenseCategoryDestroy(Category $category)
	{
		DB::beginTransaction();

		try {
			$category->deleteCategory();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function expenseCategoryImport(Request $request)
	{
		try {
			Validations::validateImport($request);
			Category::importExpenseCategoryFromExcel($request);
			return \Res::success();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	/**
	 * 	Category
	 * */
	public function getCategory(Request $request)
	{
		try {
			$categories = new Category();
			
			if($request->type) {
				$categories = $categories->where('type', $request->type);
			}

			$categories = $categories->orderBy('category_name', 'asc')
									 ->get();

			return \Res::success([
				'categories' => $categories
			]);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
