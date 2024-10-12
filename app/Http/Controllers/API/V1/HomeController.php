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
use Illuminate\Support\Number;

class HomeController extends Controller
{
    public function projectMetaData(Request $request)
    {
        return response()->json($this->prepareFilters(), 200);
    }

    public function fetchRaceData(Request $request)
    {

        $horses = collect();

        $recentRaceFilters  = $request->recentRaceFilters;
        $recentRaceData     =  $this->fetRaceData($recentRaceFilters);

        $previousRaceFilters    = $request->previousRaceFilters;
        $recentStartDate        = $recentRaceFilters['date']['start'];

        $startDate          = Race::where('date', '<', $recentStartDate)->get()->last()?->date;

        $previousRaceFilters['date']['start']   = $startDate;
        $previousRaceFilters['date']['end']     = $recentRaceFilters['date']['end'];

        $previousRaceData   =  $this->fetRaceData($previousRaceFilters);

        $horses             = $recentRaceData->merge($previousRaceData);

        $horse              = $horses->last();

        $winPercent         = $horse ? $this->calculateWinPercentage($horse->race) : 1;
        $inMoney            = $horse ? $this->calculateInMoney($horse->race) : 1;

        $averages           = $this->calculateAverages($horses);

        return response()->json([
            'winPercent' => $winPercent,
            'inMoney' => $inMoney,
            'roi' => round($averages['roi'], 2),
            'averagePayout' => Number::currency($averages['averagePayout']),
            'averagePayoutCount' => Number::currency($averages['averagePayoutCount']),
            'totalStarts' => Number::abbreviate($averages['totalStarts']),
            'previousRaceData' => $previousRaceData,
            'recentRaceData' => $recentRaceData,
            'races' => $horses,
            'race' => $horses->last(),
        ], 200);
    }

    private function prepareFilters()
    {
        $age = Age::all()->map(function ($item) {
            return [
                'value  ' => $item->id,
                'label' => $item->value,
            ];
        });

        $surface = Surface::all()->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => "{$item->type} ($item->symbol)",
            ];
        });

        $track = TrackLookup::all()->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->condition,
            ];
        });

        $trainers = Horse::distinct()->pluck('trainer')
            ->filter(fn ($item) => !blank($item))
            ->map(function ($item) {
                return [
                    'value' => $item,
                    'label' => $item,
                ];
            })
            ->values();

        $jockey = Horse::distinct()->pluck('jockey')
            ->filter(fn ($item) => !blank($item))
            ->map(function ($item) {
                return  [
                    'value' => $item,
                    'label' => $item,
                ];
            })
            ->values();

        $raceTrace = Race::distinct()->pluck('track_name')
            ->filter(fn ($item) => !blank($item))
            ->map(function ($item) {
                return  [
                    'value' => $item,
                    'label' => $item,
                ];
            })
            ->values();

        $raceType = Race::distinct()->pluck('type')
            ->filter(fn ($item) => !blank($item))
            ->map(function ($item) {
                return  [
                    'value' => $item,
                    'label' => $item,
                ];
            })
            ->values();

        $sex = [
            [
                'value' => 'F',
                'label' => 'Female',
            ],
            [
                'value' => 'M',
                'label' => 'Male',
            ]
        ];
        $minDistance = YardLookup::min('distance') > FurlongLookup::min('distance') ? YardLookup::min('distance') : FurlongLookup::min('distance');
        $maxDistance = YardLookup::max('distance') > FurlongLookup::max('distance') ? YardLookup::max('distance') : FurlongLookup::max('distance');

        $startDate = Carbon::parse(Horse::min('date'));

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
                'start' => $startDate->format('Y-m-d'),
                'end' => now()->format('Y-m-d'),
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

    // private function fetRaceData($filters)
    // {
    //     // dd($filters['distance']);
    //     return Horse::with('race')
    //         ->whereHas('race', function ($query) use ($filters) {
    //             return $query->when(isset($filters['surface']), function ($query) use ($filters) {
    //                 $query->where('surface_id', $filters['surface']);
    //             })
    //                 ->when(isset($filters['race_type']), function ($query) use ($filters) {
    //                     $query->where('type', $filters['race_type']);
    //                 })
    //                 ->when(isset($filters['track']), function ($query) use ($filters) {
    //                     $query->where('track_lookup_id', (int)$filters['track']);
    //                 })
    //                 ->when(isset($filters['distance']), function ($query) use ($filters) {
    //                     $distance = $filters['distance'];
    //                     if ($distance['min'] && $distance['max']) {
    //                         // $query->whereHas('distance', function ($query) use ($distance) {
    //                         //     $query->whereBetween('distance', $distance);
    //                         // });
    //                         $query->whereHas('race.distance', function ($query) use ($distance) {
    //                             $query->whereBetween('distance', $distance);
    //                         });
    //                     }
    //                 })
    //                 ->when(isset($filters['age']), function ($query) use ($filters) {
    //                     $query->where('age_id', $filters['age']);
    //                 });
    //         })
    //         ->when(isset($filters['date']), function ($query) use ($filters) {
    //             $date = $filters['date'];
    //             if (isset($date) && $date['start'] && $date['end']) {
    //                 $query->whereBetween('date', [$date['start'] . ' 00:00:00', $date['end'] . ' 23:59:59']);
    //             }
    //         })
    //         ->when(isset($filters['trainer']), function ($query) use ($filters) {
    //             $query->where('trainer', $filters['trainer']);
    //         })
    //         ->when(isset($filters['jockey']), function ($query) use ($filters) {
    //             $query->where('jockey', $filters['jockey']);
    //         })
    //         ->when(isset($filters['race_track']), function ($query) use ($filters) {
    //             $query->where('track_name', $filters['race_track']);
    //         })
    //         ->when(isset($filters['sex']), function ($query) use ($filters) {
    //             $query->where('gender', $filters['sex']);
    //         })
    //         ->when(isset($filters['odds']), function ($query) use ($filters) {
    //             $odds = $filters['odds'];
    //             if ($odds['min'] && $odds['max']) {
    //                 $query->whereBetween('win_odds', [$odds['min'], $odds['max']]);
    //             }
    //         })
    //         ->orderBy('date', 'desc')
    //         ->get();
    // }

    private function fetRaceData($filters)
    {
        return Horse::with('race')
            ->whereHas('race', function ($query) use ($filters) {
                $this->applyRaceFilters($query, $filters);
            })
            ->when(isset($filters['date']), function ($query) use ($filters) {
                $date = $filters['date'];
                if (!empty($date['start']) && !empty($date['end'])) {
                    $query->whereBetween('date', [$date['start'] . ' 00:00:00', $date['end'] . ' 23:59:59']);
                }
            })
            ->when(isset($filters['trainer']), function ($query) use ($filters) {
                $query->where('trainer', $filters['trainer']);
            })
            ->when(isset($filters['jockey']), function ($query) use ($filters) {
                $query->where('jockey', $filters['jockey']);
            })
            ->when(isset($filters['race_track']), function ($query) use ($filters) {
                $query->where('track_name', $filters['race_track']);
            })
            ->when(isset($filters['sex']), function ($query) use ($filters) {
                $query->where('gender', $filters['sex']);
            })
            ->when(isset($filters['odds']), function ($query) use ($filters) {
                $odds = $filters['odds'];
                if (!empty($odds['min']) && !empty($odds['max'])) {
                    $query->whereBetween('win_odds', [$odds['min'], $odds['max']]);
                }
            })
            ->orderBy('date', 'desc')
            ->get();
    }

    private function applyRaceFilters($query, $filters)
    {
        $query->when(isset($filters['surface']), function ($query) use ($filters) {
            $query->where('surface_id', $filters['surface']);
        })
        ->when(isset($filters['race_type']), function ($query) use ($filters) {
            $query->where('type', $filters['race_type']);
        })
        ->when(isset($filters['track']), function ($query) use ($filters) {
            $query->where('track_lookup_id', (int)$filters['track']);
        })
        ->when(isset($filters['distance']), function ($query) use ($filters) {
            $distance = $filters['distance'];
            if (!empty($distance['min']) && !empty($distance['max'])) {
                // $query->whereHas('distance', function ($query) use ($distance) {
                //     $query->whereBetween('distance', [$distance['min'], $distance['max']]);
                // });
            }
        })
        ->when(isset($filters['age']), function ($query) use ($filters) {
            $query->where('age_id', $filters['age']);
        });
    }


    private function calculateAverages($horses)
    {
        $data = [
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
        $roi = 0;
        $averagePayout = 0;
        foreach ($horses as $horse) {
            $totalNumberOfStarts += $horse->race->horses()->count();
            $averagePayout += $horse->race->horses()->sum('win_odds');
        }
        if ($totalNumberOfStarts) {
            $roi = ((($numberOfWins->count() * $averageWinOdds) - $numberOfStarts) / $totalNumberOfStarts);
        }

        $data['roi'] = $roi;
        $data['averagePayout'] = $averagePayout;
        $data['averagePayoutCount'] = $averagePayout / 10;
        $data['totalStarts'] = $totalNumberOfStarts;

        return $data;
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

    public function getRaces() {
        return FurlongLookup::with('races')->get();
    }
}
