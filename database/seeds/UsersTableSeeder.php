<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('last_name', 'Nahin')->count()) {
            DB::table('users')->insert([
                'google_id'      => '102261834875964430786',
                'student_id'     => 115602,
                'first_name'     => 'Blake',
                'last_name'      => 'Nahin',
                'email'          => '115602@ecrchs.org',
                'domain'         => 'ecrchs.org',
                'grade'          => '12',
                'remember_token' => str_random(10),
                'is_admin'       => 1
            ]);
        }
    }
}
