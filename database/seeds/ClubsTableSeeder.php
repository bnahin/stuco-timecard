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
        App\Setting::truncate();

        //This is needed for queries for charts
        App\Club::create([
            'id'        => 1,
            'join_code' => 'BANBAN',
            'club_name' => 'Student Council',
            'public'    => 1
        ])->settings()->save(new \App\Setting([
            'club_id'   => 1,
            'club_desc' => "Leading the school's ability to succeed."
        ]));

        $this->command->line('Adding La Familia Club');
        App\Club::create([
                'id'        => 2,
                'join_code' => 'AMSAMS',
                'club_name' => 'La Familia',
                'public'    => 1
            ]
        )->settings()->save(new \App\Setting([
            'club_id'   => 2,
            'club_desc' => "Connecting the school's student body with Latino culture. Somos una familia."
        ]));
    }
}
