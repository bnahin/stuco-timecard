<?php

namespace App\Console\Commands;

use App\Club;
use Illuminate\Console\Command;

class ChangeClubCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'club:code 
                            {name : Club name }
                            {code? : New code }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View or change a club\'s join code.';

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
        $clubName = $this->argument('name');
        $newCode = $this->argument('code');

        $club = Club::where('club_name', $clubName);
        if (!$club->exists()) {
            $this->error("Could not find club with name \"$clubName\"");
            die(126);
        }

        $club = $club->first();
        $this->info("Found club with code {$club->join_code}");
        if ($newCode) {
            if (strlen($newCode) !== 6 || !ctype_alpha($newCode)) {
                $this->error("Invalid code \"$newCode\"");
                die(128);
            }

            $club->join_code = $newCode;
            $club->saveOrFail();
            $this->info("Changed code to \"$newCode\"");
        }
    }
}