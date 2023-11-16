<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validations;
use DB;

class UserController extends Controller
{
	public function index(Request $request)
	{
		if($request->ajax()) {
			return User::dataTable($request);
		}
		
		return view('user.index', [
			'title'			=> 'User',
			'breadcrumbs'   => [
				[
					'title' => 'User',
					'link'  => route('user')
				]
			]
		]);
	}


	public function create()
	{
		return view('user.create', [
			'title'			=> 'Tambah User',
			'breadcrumbs'   => [
				[
					'title' => 'User',
					'link'  => route('user')
				],
				[
					'title' => 'Tambah User',
					'link'  => route('user.create')
				]
			]
		]);
	}

	public function store(Request $request)
	{
		Validations::validateCreateUser($request);

		try {
			DB::beginTransaction();
			User::createUser($request);
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function edit(User $user)
	{
		return view('user.edit', [
			'title'         => 'Edit User',
			'user'   		=> $user,
			'breadcrumbs'   => [
				[
					'title' => 'User',
					'link'  => route('user')
				],
				[
					'title' => 'Edit User',
					'link'  => route('user.create')
				]
			]
		]);
	}

	public function update(Request $request, User $user)
	{
		Validations::validateEditUser($request, $user->id);

		try {
			DB::beginTransaction();
			$user->updateUser($request);
			DB::commit();

			return \Res::update();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function destroy(User $user)
	{
		try {
			DB::beginTransaction();
			$user->deleteUser();
			DB::commit();

			return \Res::delete();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
