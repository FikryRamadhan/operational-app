<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionGroupAccess;
use App\MyClass\Validations;
use DB;

class TransactionGroupAccessController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return TransactionGroupAccess::dataTable($request);
		}

		return view('transaction_group_access.index', [
			'title'			=> 'Akses Grup Transaksi',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Akses Grup Transaksi',
					'link'	=> route('transaction_group_access')
				]
			]
		]);
	}


	public function create()
	{
		return view('transaction_group_access.create', [
			'title'			=> 'Tambah Akses Grup Transaksi',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Akses Grup Transaksi',
					'link'	=> route('transaction_group_access')
				],
				[
					'title'	=> 'Tambah',
					'link'	=> route('transaction_group_access.create')
				]
			]
		]);
	}


	public function store(Request $request)
	{
		Validations::validateTransactionGroupAccess($request);
		DB::beginTransaction();

		try {
			TransactionGroupAccess::createTransactionGroupAccess($request->all());
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}


	public function destroy(TransactionGroupAccess $transactionGroupAccess)
	{
		DB::beginTransaction();

		try {
			$transactionGroupAccess->deleteTransactionGroupAccess();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
