<?php

namespace App\Console\Commands;

use App\Club;
use Illuminate\Console\Command;

class ChangeClubNameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'club:name 
                            {ident : Club identifier (name or join code) }
                            {name : New name }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the name of a club';

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
        $ident = $this->argument('ident');
        $newName = $this->argument('name');

        $club = Club::where('club_name', $ident)->orWhere('join_code', $ident);
        if (!$club->exists()) {
            $this->error("Could not find club with identifier \"$ident\"");
            die(126);
        }

        $club = $club->first();
        $this->info("Found club \"{$club->club_name}\" [{$club->join_code}]");
        if ($newName) {
            $club->club_name = $newName;
            $club->saveOrFail();

            $this->info("Changed club name (\"{$club->club_name}\") to \"$newName\"");
        } else {
            $this->error("The club's new name is required.");
            abort(128);
        }
    }
}
