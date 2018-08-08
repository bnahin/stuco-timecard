<?php

use Illuminate\Database\Seeder;
use App\User;

class HoursTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Hour::truncate();
        factory(App\Hour::class, 80)->create();
    }
}
