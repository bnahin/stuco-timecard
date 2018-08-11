<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Admin::truncate();
        $this->command->line('Adding Admin account (BN)');
        DB::table('admins')->insert([
            'id'             => 1,
            'google_id'      => '102261834875964430786',
            'first_name'     => 'Blake',
            'last_name'      => 'Nahin',
            'email'          => '115602@ecrchs.org',
            'remember_token' => str_random(10)
        ]);
        App\Admin::find(1)->clubs()->attach(1);
    }
}
