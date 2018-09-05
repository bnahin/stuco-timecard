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
        App\Hour::truncate();
        \DB::table('club_user')->truncate();

        //Create as user for testing
        $user = App\User::create([
            'id'              => 1,
            'google_id'       => '102261834875964430786',
            'student_info_id' => App\StudentInfo::where('student_id', 115602)->first()->id,
            'first_name'      => 'Blake',
            'last_name'       => 'Nahin',
            'email'           => '115602@ecrchs.org',
            'domain'          => 'ecrchs.org',
            'remember_token'  => str_random(10)
        ]);
        $user->clubs()->attach(1); //Student Council
        $user->clubs()->attach(2); //Teacher PD

        factory(App\User::class, 9)->create()->each(function ($user) {
            //Attach to club
            $user->clubs()->attach(floor(random_int(1, 2)));

            //Hours
            $stuid = $user->student->student_id;
            factory(App\Hour::class, 80)->make(['student_id' => $stuid])->each(function ($hour) use ($user) {
                $user->hours()->save($hour);
            });
        });
    }
}
