<?php

namespace App\Console\Commands\InitApp;

use Illuminate\Console\Command;

class InitApp extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:init';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Inisialisasi Aplikasi';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$this->info('Sedang membuat user..');
		\Artisan::call('app:create_user');
		$this->info('[v] Berhasil membuat user');
	}
}
