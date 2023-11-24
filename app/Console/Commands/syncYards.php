<?php

namespace App\Console\Commands;

use App\Models\YardLookup;
use Illuminate\Console\Command;

class syncYards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:yards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = [
            ['distance' => 220, 'type' => 'Sprint', 'value' => 1],
            ['distance' => 330, 'type' => 'Sprint', 'value' => 1.5],
            ['distance' => 400, 'type' => 'Sprint', 'value' => 1.82],
            ['distance' => 770, 'type' => 'Sprint', 'value' => 3.5],
            ['distance' => 870, 'type' => 'Sprint', 'value' => 3.95],
            ['distance' => 440, 'type' => 'Sprint', 'value' => 2],
        ];
        foreach ($data as $item) {
            YardLookup::updateOrCreate(
                ['distance' => $item['distance']],
                $item
            );
        }
    }
}
