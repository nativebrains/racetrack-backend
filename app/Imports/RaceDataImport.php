<?php

namespace App\Imports;

use App\Models\Age;
use App\Models\FurlongLookup;
use App\Models\Horse;
use App\Models\Race;
use App\Models\Surface;
use App\Models\TrackLookup;
use App\Models\YardLookup;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class RaceDataImport implements ToModel, SkipsEmptyRows , WithHeadingRow, ShouldQueue, WithChunkReading, WithEvents
{
    use RemembersRowNumber;
    use RegistersEventListeners;
    use RemembersChunkOffset;

    public mixed $heading =  0;
    public string $delimiter = ',';

    public string $exceptionFileName =  '';
    protected mixed $currentRace =  null;


    public function  __construct( ){

    }


    /**
     * @param array $row
     */
    public function model(array $row){
        $row_type = $row[0];
        if($row_type == 'R'){
            $this->handleRace($row);
            \Log::info('Race Data Imported -> ' . json_encode($this->currentRace));
        }elseif($row_type == 'H'){
            $this->handleHorse($row);
        }
    }

    /*
    * Define the row number containing all the headings, if no heading found than the value will be 0.
    */
    public function headingRow(): int {
        return $this->heading;
    }


    /*
     * Dividing the Whole file into chunks and for the sole purpose of better Server resource management and defining the chunk size
    */
    public function chunkSize(): int {
        return 15000;
    }

    /*
    it will be executed before an import is started
    Contribution entry status will be updated to show that import is being processed.
    */
    public   function beforeImport(BeforeImport $event): void
    {
        \Log::info('beforeImport');
    }
    /*
     it will be executed after an import is finished
     Exception file will be uploaded to s3 and will be attached to contribution entry
    */
    public   function afterImport(AfterImport $event): void
    {
        \Log::info('afterImport');

        $errorFileName = $event->getConcernable()->exceptionFileName;




    }

    private function handleRace($row){
        $track_name = $row['2'];
        $date = $row['3'];
        $number_of_races = $row['4'];
        $type = $row['7'];
        $ageData = $row['12'];
        $distance_type = $row['14'];
        $distance = $row['13'];
        $surface = $row['16'];
        $track = $row['18'];
        $data = $row;

        $age = Age::whereSymbol(str_replace('0', '', substr($ageData, 0, 2)))->first();
        $status = str_contains('F', $ageData);
        $dataArray = [
            'track_name' => $track_name,
            'date' => Carbon::createFromFormat('Ymd',$date)->format('Y-m-d'),
            'number_of_races' => $number_of_races,
            'type' => $type,
            'age_id' => $age?->id,
            'status' => $status ? 'Filles/Mares Only' : 'Open',
            'distance_type' => ($distance_type == 'F' ? FurlongLookup::class : YardLookup::class),
            'distance_id' => ($distance_type == 'F' ? FurlongLookup::whereDistance($distance)->first()->id : YardLookup::whereDistance($distance)->first()->id),
            'surface_id' => Surface::whereSymbol($surface)->first()->id,
            'track_lookup_id' => TrackLookup::whereSymbol($track)->first()->id,
            'data' => $data
        ];
        /*\Log::info($dataArray);*/
        $this->currentRace = Race::create($dataArray);
    }
    private function handleHorse($row){
        $race_id = $this->currentRace?->id;
        $track_name = $this->currentRace?->track_name;
        $date = $row['2'];
        $previous_race_date= substr($row['5'], 0 , 7);
        $name = $row['7'];
        $weight_carried = $row['8'];
        $age = $row['9'];
        $gender = $row['10'];
        /*$equipment_id = $row['2'];*/
        $jockey = $row['12'];
        $win_odds = $row['13'];
        $claiming_price = $row['16'];
        $finish_position = $row['30'];
        $trainer = $row['33'];
        $owner = $row['34'];
        $data = $row;

        $dataArray = [
            'race_id' => $race_id,
            'track_name' => $track_name,
            'date' => Carbon::createFromFormat('Ymd',$date)->format('Y-m-d'),
            'previous_race' => $previous_race_date,
            'name' => $name,
            'weight_carried' => $weight_carried,
            'age' => $age,
            'gender' => $gender,
            /*'equipment_id' => $equipment_id,*/
            'jockey' => $jockey,
            'win_odds' => $win_odds,
            'claiming_price' => $claiming_price,
            'finish_position' => $finish_position,
            'trainer' => $trainer,
            'owner' => $owner,
            'data' => $data
        ];
        Horse::create($dataArray);
    }
}
