<?php

namespace App\Console\Commands;

use App\Admin;
use App\Club;
use App\StudentInfo;
use Illuminate\Console\Command;

class AddAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'club:adminadd 
                            {ident : Club identifier (name or code) }
                            {fname : First name }
                            {lname : Last name }
                            {email : Email address }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set user as admin';

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
        $fname = $this->argument('fname');
        $lname = $this->argument('lname');

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
        if (str_contains($email, ['ecrchs.org']) && !StudentInfo::where('email', $email)->exists()) {
            $this->error("The email specified is the student domain but they are not a member of the enrolled student database.");
            die(128);
        }

        $club = $club->first();
        $this->info("Found club \"{$club->club_name}\" [{$club->join_code}]");

        $admin = Admin::where('email', $email);
        if (!$admin->exists()) {
            //Create Admin model & attach
            Admin::create([
                'first_name'     => $fname,
                'last_name'      => $lname,
                'email'          => $email,
                'remember_token' => str_random(10)
            ])->clubs()->attach($club->id);
        } else {
            $admin->first()->clubs()->attach($club->id);
        }

        $this->info("Successfully set $fname $lname as an admin.");
    }
}
