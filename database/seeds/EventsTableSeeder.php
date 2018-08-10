<?php

use Illuminate\Database\Seeder;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Event::truncate();
        //This is needed for queries for charts
        App\Event::insert([
                'id'         => 0,
                'event_name' => 'Out of Classroom',
                'club_id'    => 1,
                'is_active'  => 1
            ]
        );
        factory(App\Event::class, 5)->create();
    }
}
