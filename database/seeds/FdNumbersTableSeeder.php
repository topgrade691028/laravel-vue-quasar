<?php

use App\FdNumber;
use Illuminate\Database\Seeder;

class FdNumbersTableSeeder extends Seeder {
    public function run() {
        $numbers = ['FD21618', 'FD24542', 'FD24631', 'FD31837', 'FD31856', 'FD33735', 'FD36252', 'FD39180', 'FD39181', 'FD8415'];

        foreach ($numbers as $number) {
            FdNumber::create(['fd_number' => $number]);
        }
    }
}
