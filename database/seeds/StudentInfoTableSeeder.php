<?php

use Illuminate\Database\Seeder;
use App\Common\Bnahin\EcrchsServices;

class StudentInfoTableSeeder extends Seeder
{
    private $auth;

    public function __construct(EcrchsServices $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->line('Importing student database, this will take time!!');
        if (App\StudentInfo::all()->count() < 2000) {
            $count = $this->auth->importEnrolled(true);
            if ($count) {
                $this->command->line("Done! Imported " . number_format($count) . " students.");
            } else {
                $this->command->error('Error! No student data file found.');
            }
        } else {
            \DB::table('student_info')->update(['user_id' => null]);
            $this->command->line("Nevermind, there's already data here. ;)");
        }
    }
}
