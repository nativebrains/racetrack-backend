<?php

namespace App\Console\Commands;

use App\Models\Age;
use Illuminate\Console\Command;

class syncAges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:ages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command will add the ages to ages table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = [
            ['symbol' => 2, 'value' => '2YO'],
            ['symbol' => 3, 'value' => '3YO'],
            ['symbol' => '3U', 'value' => '3UP'],
            ['symbol' => '4U', 'value' => '4UP'],
            ['symbol' => '35', 'value' => '3UP'],
            ['symbol' => '2U', 'value' => '2+'],
            ['symbol' => '36', 'value' => '3UP'],
        ];
        foreach ($data as $item) {
            Age::updateOrCreate(
                ['symbol' => $item['symbol']],
                $item
            );
        }
    }
}
