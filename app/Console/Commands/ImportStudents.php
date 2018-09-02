<?php

namespace App\Console\Commands;

use App\Common\Bnahin\EcrchsServices;
use Illuminate\Console\Command;

class ImportStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:import 
                            {--f|force : Truncate the table first.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import enrolled student database from uploaded file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * The ECRCHS helper instance.
     * @var \App\Common\Bnahin\EcrchsServices $ecrchs
     */
    protected $ecrchs;

    public function __construct(EcrchsServices $ecrchs)
    {
        parent::__construct();

        $this->ecrchs = $ecrchs;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Importing student database. This will take time.');

        $count = $this->ecrchs->importEnrolled(true);
        if (!$count) {
            $this->error('Imported 0 students. Does the file [' . $this->ecrchs->studentExcelPath . $this->ecrchs->studentExcelFileName . '] exist?');
        }
    }
}
