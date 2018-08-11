<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            StudentInfoTableSeeder::class, //Import Students
            AdminTableSeeder::class, //Seed Admin (BN)
            EventsTableSeeder::class, //Random events
            //HoursTableSeeder::class, //Random hours
            //Hours now handled by UsersTableSeeder
            ClubsTableSeeder::class, //Seed club (StuCo)
            UsersTableSeeder::class, //Seed Users
        ]);
    }
}
