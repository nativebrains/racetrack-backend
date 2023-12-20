<?php

namespace App\Http\Controllers;

use App\Imports\RaceDataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    public function index(){
        $files = Storage::files('imports');
        foreach ($files as $file){
            $data = Excel::queueImport(new RaceDataImport(), $file );
            /*$data = Excel::toCollection(new RaceDataImport(), $file );
            dd($data[0][1]);*/
        }

    }
}
