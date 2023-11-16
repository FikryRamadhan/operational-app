<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class ReportController extends Controller
{
	public function index()
	{
		return view('report.index', [
			'title'         => 'Laporan',
			'breadcrumbs'   => [
				[
					'title' => 'Laporan',
					'link'  => route('report')
				]
			]
		]);
	}


	/**
	 *  Transaction
	 * */
	public function transactionGenerate(Request $request)
	{
		try {
			$action = $request->action;

			if ($action == 'pdf_stream') {
				return Transaction::streamPdfReport($request);
			} elseif ($action == 'pdf_download') {
				return Transaction::downloadPdfReport($request);
			} elseif ($action == 'xlsx_download') {
				$path = Transaction::downloadExcelReport($request);

				return response()->download($path)->deleteFileAfterSend();
			} else {
				return Transaction::streamPdfReport($request);
			}
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	/**
	 *  Transaction Per Category
	 * */
	public function transactionPerCategoryGenerate(Request $request)
	{
		try {
			$action = $request->action;

			if ($action == 'pdf_stream') {
				return Transaction::transactionPerCategoryStreamPdfReport($request);
			} elseif ($action == 'pdf_download') {
				return Transaction::transactionPerCategoryDownloadPdfReport($request);
			} elseif ($action == 'xlsx_download') {
				$path = Transaction::transactionPerCategoryDownloadExcelReport($request);

				return response()->download($path)->deleteFileAfterSend();
			} else {
				return Transaction::transactionPerCategoryStreamPdfReport($request);
			}
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	/**
	 *  Income
	 * */
	public function incomeGenerate(Request $request)
	{
		try {
			$action = $request->action;

			if ($action == 'pdf_stream') {
				return Transaction::incomeStreamPdfReport($request);
			} elseif ($action == 'pdf_download') {
				return Transaction::incomeDownloadPdfReport($request);
			} elseif ($action == 'xlsx_download') {
				$path = Transaction::incomeDownloadExcelReport($request);

				return response()->download($path)->deleteFileAfterSend();
			} else {
				return Transaction::incomeStreamPdfReport($request);
			}
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}


	/**
	 *  Expense
	 * */
	public function expenseGenerate(Request $request)
	{
		try {
			$action = $request->action;

			if ($action == 'pdf_stream') {
				return Transaction::expenseStreamPdfReport($request);
			} elseif ($action == 'pdf_download') {
				return Transaction::expenseDownloadPdfReport($request);
			} elseif ($action == 'xlsx_download') {
				$path = Transaction::expenseDownloadExcelReport($request);

				return response()->download($path)->deleteFileAfterSend();
			} else {
				return Transaction::expenseStreamPdfReport($request);
			}
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
