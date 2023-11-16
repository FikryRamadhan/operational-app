<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validations;
use DB;

class SettingController extends Controller
{
	public function changePassword()
	{
		return view('setting.change_password', [
			'title'			=> 'Ganti Password',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Setting',
					'link'	=> 'javascript:void(0);'
				],
				[
					'title'	=> 'Ganti Password',
					'link'	=> route('setting.change_password')
				]
			]
		]);
	}

	public function savePassword(Request $request)
	{
		Validations::validateChangePassword($request, auth()->user()->id);
		DB::beginTransaction();

		try {
			auth()->user()->setPassword($request->new_password);
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}

	public function profile()
	{
		return view('setting.profile', [
			'title'			=> 'Edit Profil',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Setting',
					'link'	=> 'javascript:void(0);'
				],
				[
					'title'	=> 'Edit Profil',
					'link'	=> route('setting.profile')
				]
			]
		]);
	}

	public function saveProfile(Request $request)
	{
		Validations::validateProfileSave($request, auth()->user()->id);
		DB::beginTransaction();

		try {
			auth()->user()->update($request->all());
			auth()->user()->setAvatar($request);
			DB::commit();

			return \Res::success();
		} catch (\Exception $e) {
			DB::rollback();

			return \Res::error($e);
		}
	}
}
