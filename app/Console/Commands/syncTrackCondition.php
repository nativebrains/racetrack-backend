<?php

namespace App\Console\Commands;

use App\Models\TrackLookup;
use Illuminate\Console\Command;

class syncTrackCondition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:track-condition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Track Condition to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = [
            ['symbol' => 'FM', 'condition' => 'Firm', 'type' => 'Dry, Track'],
            ['symbol' => 'FT', 'condition' => 'Fast', 'type' => 'Dry, Track'],
            ['symbol' => 'GD', 'condition' => 'Good', 'type' => 'Wet, Track'],
            ['symbol' => 'MY', 'condition' => 'Muddy', 'type' => 'Wet, Track'],
            ['symbol' => 'SY', 'condition' => 'Sloppy', 'type' => 'Wet, Track'],
            ['symbol' => 'WF', 'condition' => 'Wet Fast', 'type' => 'Wet, Track'],
            ['symbol' => 'YL', 'condition' => 'Yielding', 'type' => 'Wet, Track'],
            ['symbol' => 'HY', 'condition' => 'Heavy', 'type' => 'Wet, Track'],
            ['symbol' => 'SF', 'condition' => 'Soft', 'type' => 'Wet, Track'],
        ];

        foreach ($data as $item) {
            TrackLookup::updateOrCreate(
                ['symbol' => $item['symbol']],
                $item
            );
        }
    }
}
