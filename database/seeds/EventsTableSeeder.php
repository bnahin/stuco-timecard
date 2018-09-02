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
        $this->command->line('Adding Events');
        App\Event::truncate();
        //This is needed for queries for charts
        $clubs = App\Club::all();
        foreach($clubs as $club) {
            App\Event::insert([
                    'event_name' => 'Out of Classroom',
                    'club_id'    => $club->id,
                    'is_active'  => 1,
                    'order'      => 0
                ]
            );
            factory(App\Event::class, 5)->create(['club_id' => $club->id]);
        }
    }
}
