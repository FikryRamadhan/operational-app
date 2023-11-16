<?php

namespace App\Console\Commands\InitApp;

use Illuminate\Console\Command;
use App\Models\User;

class CreateUser extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:create_user';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create User';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$user = User::where('role', User::ROLE_OWNER)->first();
		if(!$user) {
			User::create([
				'name' 		=> 'Owner',
				'email' 	=> 'owner@adiva.co.id',
				'password' 	=> \Hash::make('pass'),
				'role' 		=> User::ROLE_OWNER
			]);
		}

		$user = User::where('role', User::ROLE_STAFF)->first();
		if(!$user) {
			User::create([
				'name' 		=> 'Staff',
				'email' 	=> 'staff@adiva.co.id',
				'password' 	=> \Hash::make('pass'),
				'role' 		=> User::ROLE_STAFF
			]);
		}
	}
}
