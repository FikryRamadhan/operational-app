<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transactions;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Income',
            'id_category' => '1',
            'description' => 'Bulanan',
            'nominal' => '20000',
            'is_verified' => 'No',
            'id_user' => '2'
        ]);
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Expense',
            'id_category' => '4',
            'description' => 'Bulanan',
            'nominal' => '-20000',
            'is_verified' => 'No',
            'id_user' => '3'
        ]);
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Income',
            'id_category' => '1',
            'description' => 'Bulanan',
            'nominal' => '20000',
            'is_verified' => 'No',
            'id_user' => '3'
        ]);
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Expense',
            'id_category' => '4',
            'description' => 'Bulanan',
            'nominal' => '-20000',
            'is_verified' => 'No',
            'id_user' => '3'
        ]);
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Income',
            'id_category' => '1',
            'description' => 'Bulanan',
            'nominal' => '20000',
            'is_verified' => 'No',
            'id_user' => '2'
        ]);
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Expense',
            'id_category' => '4',
            'description' => 'Bulanan',
            'nominal' => '-20000',
            'is_verified' => 'No',
            'id_user' => '2'
        ]);
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Income',
            'id_category' => '1',
            'description' => 'Bulanan',
            'nominal' => '20000',
            'is_verified' => 'No',
            'id_user' => '3'
        ]);
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Expense',
            'id_category' => '4',
            'description' => 'Bulanan',
            'nominal' => '-20000',
            'is_verified' => 'No',
            'id_user' => '2'
        ]);
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Income',
            'id_category' => '1',
            'description' => 'Bulanan',
            'nominal' => '20000',
            'is_verified' => 'No',
            'id_user' => '3'
        ]);
        Transactions::create([
            'date' => carbon::now(),
            'type' => 'Expense',
            'id_category' => '4',
            'description' => 'Bulanan',
            'nominal' => '-20000',
            'is_verified' => 'No',
            'id_user' => '2'
        ]);
    }
}
