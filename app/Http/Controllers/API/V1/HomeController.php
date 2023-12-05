<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Age;
use App\Models\FurlongLookup;
use App\Models\Horse;
use App\Models\Race;
use App\Models\Surface;
use App\Models\TrackLookup;
use App\Models\YardLookup;
use Illuminate\Http\Request;
use Psy\Sudo;

class HomeController extends Controller
{
    public function projectMetaData(Request $request)
    {
        return response()->json($this->prepareFilters(), 200 );
    }

    public function fetchRaceData(){
        $races = Race::get();
        return response()->json([
             'races' => $races,
        ], 200 );
    }

    private function prepareFilters(){
        $age = Age::all()->map(function ($item){
            return [
                'key' => $item->id,
                'label' => $item->value,
            ];
        });
        $surface = Surface::all()->map(function ($item){
            return [
                'key' => $item->id,
                'label' => $item->symbol,
            ];
        });
        $track = TrackLookup::all()->map(function ($item){
            return [
                'key' => $item->id,
                'label' => $item->symbol,
            ];
        });
        $trainers = Horse::distinct()->pluck('trainer')->map(function ($item){
            return [
                'key' => $item,
                'label' => $item,
            ];
        });
        $jockey = Horse::distinct()->pluck('jockey')->map(function ($item){
            return  [
                'key' => $item,
                'label' => $item,
            ];
        });
        $raceTrace = Race::distinct()->pluck('track_name')->map(function ($item){
            return  [
                'key' => $item,
                'label' => $item,
            ];
        });
        $raceType = Race::distinct()->pluck('type')->map(function ($item){
            return  [
                'key' => $item,
                'label' => $item,
            ];
        });
        $sex = [
            [
                'key' => 'F',
                'label' => 'Female',
            ],
            [
                'key' => 'M',
                'label' => 'Male',
            ]
        ];
        $minDistance = YardLookup::min('distance') > FurlongLookup::min('distance') ? YardLookup::min('distance') : FurlongLookup::min('distance');
        $maxDistance = YardLookup::max('distance') > FurlongLookup::max('distance') ? YardLookup::max('distance') : FurlongLookup::max('distance');

        return [
            'age' => $age,
            'surface' => $surface,
            'track' => $track,
            'trainers' => $trainers,
            'jockey' => $jockey,
            'sex' => $sex,
            'race_track' => $raceTrace,
            'race_type' => $raceType,
            'odds' => [
                'min' => Horse::min('win_odds'),
                'max' => Horse::max('win_odds'),
            ],
            'distance' => [
                'min' => $minDistance,
                'max' => $maxDistance,
            ],
        ];
    }
}
