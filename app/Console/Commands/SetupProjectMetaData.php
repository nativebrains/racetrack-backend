<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupProjectMetaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:setup-project-meta-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call all the metatable commands to populate the tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('sync:ages');
        Artisan::call('sync:furlongs');
        Artisan::call('sync:yards');
        Artisan::call('sync:surface');
        Artisan::call('sync:track-condition');
    }
}
