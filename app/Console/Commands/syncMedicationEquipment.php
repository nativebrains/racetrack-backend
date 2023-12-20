<?php

namespace App\Console\Commands;

use App\Models\MedicationEquipment;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class syncMedicationEquipment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:medication-equipment';

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
            ['symbol' => 'I', 'name' => 'Turf'],
            ['symbol' => '0', 'name' => 'No equipment'],
            ['symbol' => '2', 'name' => 'Screens'],
            ['symbol' => '3', 'name' => 'Shields'],
            ['symbol' => 'a', 'name' => 'Aluminum pads'],
            ['symbol' => 'b', 'name' => 'Blinkers'],
            ['symbol' => 'c', 'name' => 'Mud calks'],
            ['symbol' => 'f', 'name' => 'Front bandages'],
            ['symbol' => 'g', 'name' => 'Goggles'],
            ['symbol' => 'h', 'name' => 'Hood'],
            ['symbol' => 'i', 'name' => 'Cornell Collar'],
            ['symbol' => 'j', 'name' => 'Cornell Collar Off'],
            ['symbol' => 'k', 'name' => 'Flipping halter'],
            ['symbol' => 'n', 'name' => 'No whip'],
            ['symbol' => 'o', 'name' => 'Blinkers off'],
            ['symbol' => 'p', 'name' => 'Aluminum Pad'],
            ['symbol' => 'q', 'name' => 'Nasal Strip off'],
            ['symbol' => 'r', 'name' => 'Bar shoe'],
            ['symbol' => 's', 'name' => 'Nasal Strip'],
            ['symbol' => 'v', 'name' => 'Cheek Piece'],
            ['symbol' => 'W', 'name' => 'No Hind Shoes'],
            ['symbol' => 'x', 'name' => 'Cheek Piece Off'],
            ['symbol' => 'y', 'name' => 'No shoes'],
            ['symbol' => 'z', 'name' => 'Tongue tie'],
            ['symbol' => 'l', 'name' => 'Bar Shoes'],
            ['symbol' => 'A', 'name' => 'Adjunct Medication'],
            ['symbol' => 'B', 'name' => 'Bute'],
            ['symbol' => 'C', 'name' => '1st Time Bute'],
            ['symbol' => 'L', 'name' => 'Lasix'],
            ['symbol' => 'M', 'name' => '1st Time Lasix'],
        ];

        foreach ($data as $item) {
            $item['slug'] = Str::slug($item['name']);
            MedicationEquipment::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }

    }
}
