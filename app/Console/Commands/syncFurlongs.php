<?php

namespace App\Console\Commands;

use App\Models\FurlongLookup;
use Illuminate\Console\Command;

class syncFurlongs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:furlongs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Furlongs data into database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = [
            ['distance' => 200, 'type' => 'Sprint', 'value' => 2],
            ['distance' => 250, 'type' => 'Sprint', 'value' => 2.5],
            ['distance' => 300, 'type' => 'Sprint', 'value' => 3],
            ['distance' => 350, 'type' => 'Sprint', 'value' => 3.5],
            ['distance' => 400, 'type' => 'Sprint', 'value' => 4],
            ['distance' => 450, 'type' => 'Sprint', 'value' => 4.5],
            ['distance' => 500, 'type' => 'Sprint', 'value' => 5],
            ['distance' => 550, 'type' => 'Sprint', 'value' => 5.5],
            ['distance' => 600, 'type' => 'Sprint', 'value' => 6],
            ['distance' => 650, 'type' => 'Sprint', 'value' => 6.5],
            ['distance' => 700, 'type' => 'Sprint', 'value' => 7],
            ['distance' => 750, 'type' => 'Sprint', 'value' => 7.5],
            ['distance' => 800, 'type' => 'Route', 'value' => 8],
            ['distance' => 832, 'type' => 'Route', 'value' => 8.32],
            ['distance' => 850, 'type' => 'Route', 'value' => 8.5],
            ['distance' => 900, 'type' => 'Route', 'value' => 9],
            ['distance' => 950, 'type' => 'Route', 'value' => 9.5],
            ['distance' => 1000, 'type' => 'Route', 'value' => 10],
            ['distance' => 1050, 'type' => 'Route', 'value' => 10.5],
            ['distance' => 1100, 'type' => 'Route', 'value' => 11],
            ['distance' => 1150, 'type' => 'Route', 'value' => 11.5],
            ['distance' => 1200, 'type' => 'Route', 'value' => 12],
            ['distance' => 1250, 'type' => 'Route', 'value' => 12.5],
            ['distance' => 1300, 'type' => 'Route', 'value' => 13],
            ['distance' => 1350, 'type' => 'Route', 'value' => 13.5],
            ['distance' => 1400, 'type' => 'Route', 'value' => 14],
            ['distance' => 1450, 'type' => 'Route', 'value' => 14.5],
            ['distance' => 1500, 'type' => 'Route', 'value' => 15],
            ['distance' => 1550, 'type' => 'Route', 'value' => 15.5],
            ['distance' => 1600, 'type' => 'Route', 'value' => 16],
        ];
        foreach ($data as $item) {
            FurlongLookup::updateOrCreate(
                ['distance' => $item['distance']],
                $item
            );
        }
    }
}
/*
 * 200,Sprint,2
250,Sprint,2.5
300,Sprint,3
350,Sprint,3.5
400,Sprint,4
450,Sprint,4.5
500,Sprint,5
550,Sprint,5.5
600,Sprint,6
650,Sprint,6.5
700,Sprint,7
750,Sprint,7.5
800,Route,8
832,Route,8.32
850,Route,8.5
900,Route,9
950,Route,9.5
1000,Route,10
1050,Route,10.5
1100,Route,11
1150,Route,11.5
1200,Route,12
1250,Route,12.5
1300,Route,13
1350,Route,13.5
1400,Route,14
1450,Route,14.5
1500,Route,15
1550,Route,15.5
1600,Route,16
 *  */
