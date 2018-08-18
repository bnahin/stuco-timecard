<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BlockedUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\BlockedUser::truncate();

        $user = App\User::inRandomOrder()->first();
        $clubid = 1;
        /*DB::table('blocked_users')->insert([
            'user_id'    => $user->id,
            'club_id'    => $clubid,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);*/
        $user->blocks()->create(['club_id' => $clubid]);

    }
}
