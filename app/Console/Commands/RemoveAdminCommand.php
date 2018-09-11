<?php

namespace App\Console\Commands;

use App\Admin;
use App\Club;
use Illuminate\Console\Command;

class RemoveAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'club:admindel
                            {ident : Club identifier (name or code) }
                            {email : Email address }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove user as admin';

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
        $email = $this->argument('email');

        if (!$email) {
            $this->error("There is no email specified.");
            die(128);
        }

        $club = Club::where('join_code', $ident)->orWhere('club_name', $ident);
        if (!$club->exists()) {
            $this->error('Could not find club with identifer "' . $ident . '".');
            die(128);
        }
        if (!str_contains($email, ['ecrchs.net', 'ecrchs.org'])) {
            $this->error('Invalid email "' . $email . '"');
            die(128);
        }

        $club = $club->first();
        $this->info("Found club \"{$club->club_name}\" [{$club->join_code}]");

        $admin = Admin::where('email', $email);
        if (!$admin->exists()) {
            $this->info("The user with email $email is not an admin.");
        } elseif (!$admin->first()->clubs()->where('clubs.id', $club->id)->exists()) {
            $this->info("The user with email $email is not an admin of the club.");
        } else {
            $admin = $admin->first();

            $admin->clubs()->detach($club->id);
            $this->info("Successfully removed $email as an admin.");
        }
    }
}
