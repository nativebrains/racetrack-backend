<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;

class RaceDataImport implements ToModel, SkipsEmptyRows , WithHeadingRow, ShouldQueue, WithChunkReading, WithEvents
{
    use RemembersRowNumber;
    use RegistersEventListeners;
    use RemembersChunkOffset;

    public mixed $heading =  0;
    public string $delimiter = ',';

    public string $exceptionFileName =  '';


    public function  __construct( ){

    }


    /**
     * @param array $row
     */
    public function model(array $row){
        //
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


        \Log::build([
            'driver' => 'single',
            'path' => Storage::disk('logs')->path('IMIE File Finished.log'),
        ])->info($errorFileName . ' is completed');

    }
}
