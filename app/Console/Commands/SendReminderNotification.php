<?php

namespace App\Console\Commands;

use function Termwind\ask;

use App\Models\ReminderDetail;
use Illuminate\Console\Command;

class SendReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send_reminder_notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim Notifikasi Reminder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \App\Models\Reminder::sendNotification();
    }
}
