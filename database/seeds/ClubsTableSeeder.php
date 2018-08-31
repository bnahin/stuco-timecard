<?php

use Illuminate\Database\Seeder;

class ClubsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->line('Adding Student Council Club');
        App\Club::truncate();
        App\ActivityLog::truncate();

        //This is needed for queries for charts
        App\Club::create([
                'id'         => 1,
                'join_code' => 'BANBAN',
                'club_name'    => 'Student Council',
                'public'  => 1
            ]
        )->settings()->save(new \App\Setting(['club_id' => 1]));
    }
}
