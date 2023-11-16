<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTransactionReport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:send_transaction_report';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Kirim laporan transaksi';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		if(date('N') == 7 && date('H:i') == '23:00') {
			\App\Models\Transaction::sendWeeklyReports();
		}

		sleep(5);

		if(date('d') == date('t') && date('H:i') == '23:00') {
			\App\Models\Transaction::sendMonthlyReports();
		}
	}
}
