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
        App\Club::truncate();
        App\ActivityLog::truncate();
        App\Setting::truncate();

        $this->command->line('Adding Student Council Club');
        //For Admin Test
        App\Club::create([
            'id'        => 1,
            'join_code' => 'BANBAN',
            'club_name' => 'Student Council',
            'public'    => 1
        ])->settings()->save(new \App\Setting([
            'club_id'   => 1,
            'club_desc' => "Leading the school's ability to succeed."
        ]));


        $this->command->line('Adding Teacher Development Club');

        //For Teacher PDs
        App\Club::create([
                'id'        => 2,
                'join_code' => 'TCHRPD',
                'club_name' => 'Professional Development',
                'public'    => 0
            ]
        )->settings()->save(new \App\Setting([
            'club_id'   => 2,
            'club_desc' => "Track teacher attendance at PD days."
        ]));

        $this->command->line('Adding La Familia Club');
        //For User Test
        App\Club::create([
                'id'        => 3,
                'join_code' => 'AMSAMS',
                'club_name' => 'La Familia',
                'public'    => 1
            ]
        )->settings()->save(new \App\Setting([
            'club_id'   => 3,
            'club_desc' => "Connecting the school's student body with Latino culture. Somos una familia."
        ]));

        $this->command->line('Adding Key Club');
        //For Join Test
        App\Club::create([
            'id'        => 3,
            'join_code' => 'JOINUS',
            'club_name' => 'Key Club',
            'public'    => 1
        ])->settings()->save(new \App\Setting([
            'club_id'   => 3,
            'club_desc' => "Uniting the world's students through community service."
        ]));
    }
}
