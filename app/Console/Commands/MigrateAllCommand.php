<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class MigrateAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executar Todas as migrantes';

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

        foreach (Config::get('database.connections') as $name => $details)
        {
            $this->info('Running migration for "' . $name . '"');
            $this->call('migrate', array('--database' => $name, '--path' => 'app/database/migrations/' . $name));
        }
    }

    public function fire()
    {

    }
}
