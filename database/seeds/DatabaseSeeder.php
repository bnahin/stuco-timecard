<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (App::isLocal()) {
            /** Development Seeding */
            $this->call([
                StudentInfoTableSeeder::class, //Import Students
                AdminTableSeeder::class, //Seed Admin (BN)
                EventsTableSeeder::class, //Random events
                //HoursTableSeeder::class, //Random hours
                //Hours now handled by UsersTableSeeder
                ClubsTableSeeder::class, //Seed club (StuCo)
                UsersTableSeeder::class, //Seed Users,
                BlockedUsersSeeder::class //Block Random Student
            ]);
        } else {
            /** Development Seeding */
            $this->call([
                StudentInfoTableSeeder::class,
                AdminTableSeeder::class,
                ClubsTableSeeder::class
            ]);
        }
    }
}
