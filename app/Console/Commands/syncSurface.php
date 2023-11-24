<?php

namespace App\Console\Commands;

use App\Models\Surface;
use Illuminate\Console\Command;

class syncSurface extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:surface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Surface data to system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = [
            ['symbol' => 'I', 'type' => 'Turf'],
            ['symbol' => 'T', 'type' => 'Turf'],
            ['symbol' => 'E', 'type' => 'Synthetic'],
            ['symbol' => 'D', 'type' => 'Dirt'],
            ['symbol' => 'O', 'type' => 'Turf'],
            ['symbol' => 'M', 'type' => 'Turf'],
        ];
        foreach ($data as $item) {
            Surface::updateOrCreate(
                ['symbol' => $item['symbol']],
                $item
            );
        }
    }
}
