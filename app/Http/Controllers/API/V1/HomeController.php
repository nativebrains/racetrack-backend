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

    public function fetchRaceData(Request $request){
        $recentRaceFilters =$request->recentRaceFilters;
        $recentRaceData =  $this->fetRaceData($recentRaceFilters);;

        $previousRaceFilters = $request->previousRaceFilters;
        $previousRaceData =  $this->fetRaceData($previousRaceFilters);
        return response()->json([
             'previousRaceData' => $previousRaceData,
             'recentRaceData' => $recentRaceData,
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

    private function fetRaceData($filters){
        return Race::with(['horses', 'age','surface', 'track', 'distance'])
            /*->whereHas('horses', function ($query) use ($filters) {

            })*/
            ->when($filters['trainer'], function ($query) use ($filters) {
                $query->horses()->where('trainer', $filters['trainer']);
            })
            ->when($filters['jockey'], function ($query) use ($filters){
                $query->horses()->where('jockey', $filters['jockey']);
            })
            ->when($filters['track'], function ($query) use ($filters) {
                $query->where('track_lookup_id', $filters['track']);
            })
            ->when($filters['race_track'], function ($query) use ($filters){
                $query->where('track_name', $filters['race_track']);
            })
            ->when($filters['age'], function ($query) use ($filters){
                $query->horses()->where('age_id', $filters['age']);
            })
            ->when($filters['sex'], function ($query) use ($filters){
                $query->horses()->where('gender', $filters['sex']);
            })
            ->when($filters['surface'], function ($query) use ($filters){
                $query->where('surface_id', $filters['surface']);
            })
            ->when($filters['race_type'], function ($query) use ($filters){
                $query->where('type', $filters['race_type']);
            })
            ->when($filters['distance'], function ($query) use ($filters){
                $distance = $filters['distance'];
                if ($distance->min && $distance->max){
                    $query->distance()->whereBetween('distance', [$distance->min, $distance->max]);
                }
            })
            ->when($filters['date'], function ($query) use ($filters){
                $date = $filters['date'];
                if ($date->start && $date->end){
                    $query->whereBetween('date',[$date->start.' 00:00:00',$date->end.' 23:59:59']);
                }
            })
            ->when($filters['odds'], function ($query) use ($filters){
                $odds = $filters['odds'];
                if ($odds->min && $odds->max){
                    $query->horses()->whereBetween('win_odds', [$odds->min, $odds->max]);
                }
            })
            ->get();
    }
}
