<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportReceiver;
use DB;

class ReportReceiverController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return ReportReceiver::dataTable($request);
		}

		return view('report_receiver.index', [
			'title'         => 'CC Pengiriman Laporan',
			'breadcrumbs'   => [
				[
					'title' => 'CC Pengiriman Laporan',
					'link'  => route('report_receiver')
				]
			]
		]);
	}

	public function create()
	{
		return view('report_receiver.create', [
			'title'         => 'Tambah CC Pengiriman Laporan',
			'breadcrumbs'   => [
				[
					'title' => 'CC Pengiriman Laporan',
					'link'  => route('report_receiver')
				],
				[
					'title' => 'Tambah',
					'link'  => route('report_receiver.create')
				]
			]
		]);
	}

	public function store(Request $request)
	{
		DB::beginTransaction();

		try {
			ReportReceiver::createReportReceiver($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function edit(ReportReceiver $reportReceiver)
	{
		return view('report_receiver.edit', [
			'title'         => 'Edit CC Pengiriman Laporan',
			'reportReceiver' => $reportReceiver,
			'breadcrumbs'   => [
				[
					'title' => 'CC Pengiriman Laporan',
					'link'  => route('report_receiver')
				],
				[
					'title' => 'Edit',
					'link'  => route('report_receiver.edit', $reportReceiver->id)
				]
			]
		]);
	}

	public function update(Request $request, ReportReceiver $reportReceiver)
	{
		DB::beginTransaction();

		try {
			$reportReceiver->updateReportReceiver($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function destroy(ReportReceiver $reportReceiver)
	{
		DB::beginTransaction();

		try {
			$reportReceiver->deleteReportReceiver();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
