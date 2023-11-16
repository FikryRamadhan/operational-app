<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\MyClass\Response;
use App\MyClass\Validations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReminderController extends Controller
{
	
	public function index(Request $request)
	{
		if($request->ajax()) {
			return Reminder::dataTable($request);
		}

		return view('reminder.index', [
			'title'			=> 'Reminder',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Reminder',
					'link'	=> route('reminder')
				]
			]
		]);
	}

	public function create()
	{
		return view('reminder.create', [
			'title'			=> 'Tambah Reminder',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Reminder',
					'link'	=> route('reminder')
				],
				[
					'title'	=> 'Tambah Reminder',
					'link'	=> route('reminder.create')
				]
			]
		]);
	}

	public function store(Request $request)
	{
		Validations::validateReminder($request);
		DB::beginTransaction();

		try {
			Reminder::createReminder($request);
			DB::commit();

			return \Res::save();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function detail(Reminder $reminder)
	{
		return view('reminder.detail', [
			'title'			=> 'Detail Reminder',
			'reminder'		=> $reminder,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Reminder',
					'link'	=> route('reminder')
				],
				[
					'title'	=> 'Detail Reminder',
					'link'	=> route('reminder.detail', $reminder->id)
				]
			]
		]);
	}

	public function edit(Reminder $reminder)
	{
		return view('reminder.edit', [
			'title'			=> 'Edit Reminder',
			'reminder'		=> $reminder,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Reminder',
					'link'	=> route('reminder')
				],
				[
					'title'	=> 'Edit Reminder',
					'link'	=> route('reminder.edit', $reminder->id)
				]
			]
		]);
	}

	public function update(Request $request, Reminder $reminder)
	{
		Validations::validateReminder($request);
		DB::beginTransaction();

		try {
			$reminder->updateReminder($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function destroy(Reminder $reminder)
	{
		DB::beginTransaction();

		try {
			$reminder->deleteReminder();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function check(Reminder $reminder)
	{
		try {
			DB::beginTransaction();
			$reminder->status();
			DB::commit();

			return Response::success([
				'message' => 'Sudah Di Selesaikan'
			]);
		} catch (\Exception $e) {
			DB::rollback();

			return Response::error($e);
		}
	}
}
