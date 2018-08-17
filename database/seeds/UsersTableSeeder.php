<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->line('Adding Users and Hours');

        App\User::truncate();
        \DB::table('club_user')->truncate();

        factory(App\User::class, 9)->create()->each(function ($user) {
            //Attach to club
            $user->clubs()->attach(1);

            //Hours
            $stuid = $user->student->student_id;
            factory(App\Hour::class, 80)->make(['student_id' => $stuid])->each(function ($hour) use ($user) {
                $user->hours()->save($hour);
            });
        });
    }
}
