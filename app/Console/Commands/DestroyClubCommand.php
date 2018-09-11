<?php

namespace App\Console\Commands;

use App\Club;
use Illuminate\Console\Command;

class DestroyClubCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'club:destroy 
                            {ident : The club\'s name or code }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroy club and its hours and assignments';

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
     */
    public function handle()
    {
        $ident = $this->argument('ident');
        $club = Club::where('club_name', $ident)->orWhere('join_code', $ident);
        if (!$club->exists()) {
            $this->error('Could not find club with identifer "' . $ident . '".');
            die(128);
        }
        $club = $club->first();

        $this->line("Found club \"{$club->club_name}\" [{$club->join_code}]");

        if ($this->confirm('Are you sure you want to destroy the club "' . $club->club_name . '"? This will obliterate it from existance. Proceed with caution.')) {
            if (strtoupper($this->ask('Please retype the club\'s join code to proceed with deletion')) == $club->join_code) {
                $this->info('Continuing with deletion.');
                try {
                    $club->fullDestroy();
                    $this->info('The club has been successfully destroyed.');
                    die(0);
                } catch (\Exception $e) {
                    $this->error('Unable to destroy club.');
                    die(126);
                }
            } else {
                $this->info('Incorrect join code entered. Aborting deletion.');
                die(126);
            }
        } else {
            $this->info("Aborting deletion.");
            die(126);
        }
    }
}
