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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Psy\Sudo;

class HomeController extends Controller
{
    public function projectMetaData(Request $request)
    {
        return response()->json($this->prepareFilters(), 200 );
    }

    public function fetchRaceData(Request $request){

        $horses = collect();

        $recentRaceFilters =$request->recentRaceFilters;
        $recentRaceData =  $this->fetRaceData($recentRaceFilters);

        $previousRaceFilters = $request->previousRaceFilters;
        $recentStartDate = $recentRaceFilters['date']['start'] ?? null;
        $startDate = Race::where('date', '<', $recentStartDate)->get()->last()->date;

        $previousRaceFilters['date']['start'] = $startDate;
        $previousRaceFilters['date']['end'] = $recentRaceFilters['date']['end'] ?? null;

        $previousRaceData =  $this->fetRaceData($previousRaceFilters);

        $horses = $recentRaceData->merge($previousRaceData);

        $horse = $horses->last();

        $winPercent = $this->calculateWinPercentage($horse->race);
        $inMoney = $this->calculateInMoney($horse->race);

        $averages = $this->calculateAverages($horses);

        return response()->json([
             'winPercent' => $winPercent,
             'inMoney' => $inMoney,
             'roi' => $averages['roi'],
             'averagePayout' => $averages['averagePayout'],
             'averagePayoutCount' => $averages['averagePayoutCount'],
             'previousRaceData' => $previousRaceData,
             'recentRaceData' => $recentRaceData,
             'races' => $horses,
             'race' => $horses->last(),
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

        /*$startDate = Carbon::parse(Horse::min('date'));*/
        $startDate = Carbon::parse(Horse::min('date'));
        $startDateDiff = $startDate->diffInDays(Carbon::now());
        $endDate = 0;

        return [
            'age' => $age,
            'surface' => $surface,
            'track' => $track,
            'trainers' => $trainers,
            'jockey' => $jockey,
            'sex' => $sex,
            'race_track' => $raceTrace,
            'race_type' => $raceType,
            'date' => [
                'start' => $startDateDiff,
                'end' => $endDate,
            ],
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
        return Horse::with('race')
            ->whereHas('race', function($query) use ($filters) {
                return $query->when($filters['surface'], function ($query) use ($filters){
                        $query->where('surface_id', $filters['surface']);
                    })
                    ->when($filters['race_type'], function ($query) use ($filters){
                        $query->where('type', $filters['race_type']);
                    })
                    ->when($filters['distance'], function ($query) use ($filters){
                        $distance = $filters['distance'];
                        if ($distance['min'] && $distance['max']){
                            $query->distance()->whereBetween('distance', $distance);
                        }
                    })
                    ;
            })
            ->when($filters['date'], function ($query) use ($filters){
                $date = $filters['date'];
                if (isset($date) && $date['start'] && $date['end']){
                    $query->whereBetween('date',[$date['start'].' 00:00:00',$date['end'].' 23:59:59']);
                }
            })
            ->when($filters['trainer'], function ($query) use ($filters) {
                $query->where('trainer', $filters['trainer']);
            })
            ->when($filters['jockey'], function ($query) use ($filters){
                $query->where('jockey', $filters['jockey']);
            })
            ->when($filters['track'], function ($query) use ($filters) {
                $query->where('track_lookup_id', $filters['track']);
            })
            ->when($filters['race_track'], function ($query) use ($filters){
                $query->where('track_name', $filters['race_track']);
            })
            ->when($filters['age'], function ($query) use ($filters){
                $query->where('age_id', $filters['age']);
            })
            ->when($filters['sex'], function ($query) use ($filters){
                $query->where('gender', $filters['sex']);
            })
            ->when($filters['odds'], function ($query) use ($filters){
                $odds = $filters['odds'];
                if ($odds['min'] && $odds['max']){
                    $query->whereBetween('win_odds', [$odds['min'], $odds['max']]);
                }
            })
            ->orderBy('date', 'desc')
            ->get();
    }

    private function calculateAverages($horses){
        $roi = [
            'roi' => 0,
            'averagePayout' => 0,
            'totalStarts' => 0,
        ];
        /*
         * ((# of wins * Average Winning Odds) - # of Starts) / Total Number of Starts
        */

        $numberOfWins = $horses->where('finish_position', 1);
        $averageWinOdds = $horses->avg('win_odds');
        $numberOfStarts = $horses->count();
        $totalNumberOfStarts = 0;
        $averagePayout = 0;
        foreach ($horses as $horse){
            $totalNumberOfStarts += $horse->race->horses()->count();
            $averagePayout += $horse->race->horses()->sum('win_odds');
        }
        $roi = (($numberOfWins * $averageWinOdds) - $numberOfStarts) / $totalNumberOfStarts;
        $roi['roi'] = $roi;
        $roi['averagePayout'] = $averagePayout / 10;
        $roi['totalStarts'] = $totalNumberOfStarts;

        return $roi;
    }

    public function calculateWinPercentage($race)
    {
        $percent = $race->horses()->count() > 0 ? (1 / $race->horses()->count()) * 100 : 0;

        return round($percent, 2);
    }

    public function calculateInMoney($race)
    {
        $percent = $race->horses()->count() > 0 ? (3 / $race->horses()->count()) * 100 : 0;

        return round($percent, 2);
    }
}
