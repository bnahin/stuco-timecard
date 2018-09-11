<?php

namespace App\Console\Commands;

use App\Club;
use App\Setting;
use Illuminate\Console\Command;

class CreateClubCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'club:create 
                            {name : The club\'s name}
                            {code : The club\'s join code }
                            {--master=on : Allow timepunches };
                            {--allow-mark=yes : Allow hour marking }
                            {--allow-delete=no : Allow user timepunch deletion }
                            {--allow-comments=yes : Allow comments on timepunched }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new club';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        $name = $this->argument('name');
        $code = $this->argument('code');

        $master = in_array(strtolower($this->option('master')), ['on', 'yes', 'y', 'si']);
        $allowMark = in_array(strtolower($this->option('allow-mark')), ['on', 'yes', 'y', 'si']);
        $allowDelete = in_array(strtolower($this->option('allow-delete')), ['on', 'yes', 'y', 'si']);
        $allowComments = in_array(strtolower($this->option('allow-comments')), ['on', 'yes', 'y', 'si']);

        if (strlen($code) !== 6 || !ctype_alpha($code)) {
            $this->error("Invalid code \"$code\"");
            die(128);
        }
        if (Club::where('join_code', $code)->exists()) {
            $this->error("The code is already taken by \"" . Club::where('join_code',
                    $code)->first()->club_name . "\"");
            die(126);
        }

        $club = new Club;
        $club->club_name = $name;
        $club->join_code = $code;
        $club->public = true;

        $settings = new Setting;
        $settings->club_desc = "This club has no description... yet.";
        $settings->master = $master;
        $settings->allow_mark = $allowMark;
        $settings->allow_delete = $allowDelete;
        $settings->allow_comments = $allowComments;

        $club->saveOrFail();
        $club->settings()->save($settings);

        $this->info("Club successfully created. Make sure to add admins using club:admins {$club->join_code} [emails...].");

    }
}
