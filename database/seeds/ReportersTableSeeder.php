<?php

use App\Reporter;
use Illuminate\Database\Seeder;

class ReportersTableSeeder extends Seeder {
    public function run() {
        $reporter_names = ['test1', 'test2', 'test3', 'test4', 'test5', 'test6', 'test7', 'test8', 'test9', 'test10'];

        foreach ($reporter_names as $name) {
            Reporter::create(['reporter_name' => $name]);
        }
    }
}
