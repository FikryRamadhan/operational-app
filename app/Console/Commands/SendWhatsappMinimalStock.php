<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SendWhatsappMinimalStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:stock_barang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Untuk  Mengirim Pesan Stock Barang Yang Kurang Dari Minimal Stock Melalui Whatsapp';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Product::sendNotificationStock();
    }
}