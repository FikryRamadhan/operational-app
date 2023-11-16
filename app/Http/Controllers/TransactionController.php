<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionGroup;
use Validations;
use DB;

class TransactionController extends Controller
{

	public function index(Request $request)
	{
		if($request->ajax()) {
			return Transaction::dataTable($request);
		}
		
		return view('transaction.index', [
			'title'			=> 'Transaksi',
			'transactionGroups' => TransactionGroup::all(),
			'breadcrumbs'	=> [
				[
					'title'	=> 'Transaksi',
					'link'	=> route('transaction')
				]
			],
			'categories' 	=> Category::all()
		]);
	}


	public function create()
	{
		return view('transaction.create', [
			'title'			=> 'Tambah Transaksi',
			'transactionGroups' => TransactionGroup::all(),
			'breadcrumbs'	=> [
				[
					'title'	=> 'Transaksi',
					'link'	=> route('transaction')
				],
				[
					'title'	=> 'Tambah Transaksi',
					'link'	=> route('transaction.create')
				]
			]
		]);
	}

	public function store(Request $request)
	{
		Validations::validateTransaction($request);

		try {
			DB::beginTransaction();
			Transaction::createTransaction($request);
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function edit(Transaction $transaction)
	{
		return view('transaction.edit', [
			'title'			=> 'Edit Transaksi',
			'transactionGroups' => TransactionGroup::all(),
			'transaction'	=> $transaction,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Transaksi',
					'link'	=> route('transaction')
				],
				[
					'title'	=> 'Edit Transaksi',
					'link'	=> route('transaction.create')
				]
			]
		]);
	}

	public function update(Request $request, Transaction $transaction)
	{
		Validations::validateTransaction($request);

		try {
			DB::beginTransaction();
			$transaction->updateTransaction($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function destroy(Transaction $transaction)
	{
		try {
			DB::beginTransaction();
			$transaction->deleteTransaction();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function verify(Transaction $transaction)
	{
		try {
			DB::beginTransaction();
			$transaction->verify();
			DB::commit();

			return \Res::success([
				'message' => 'Berhasil diverifikasi'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function verifySelected(Request $request)
	{
		try {
			$transactions = Transaction::whereIn('id', $request->ids)
									   ->where('is_verified', 'no')
									   ->get();
			foreach($transactions as $transaction)
			{
				DB::beginTransaction();
				$transaction->verify();
				DB::commit();
			}

			return \Res::success([
				'message' => 'Berhasil diverifikasi'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function verifyAll(Request $request)
	{
		try {
			$transactions = Transaction::where('is_verified', 'no')
									   ->get();
			foreach($transactions as $transaction)
			{
				DB::beginTransaction();
				$transaction->verify();
				DB::commit();
			}

			return \Res::success([
				'message' => 'Berhasil diverifikasi'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function import(Request $request)
	{
		Validations::validateImportTransaction($request);
		try {
			Transaction::importFromExcel($request);
			return \Res::success();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
