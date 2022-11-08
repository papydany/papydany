<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class Erss implements WithMultipleSheets
{


    protected $request;
   

    public function __construct(array $request)
    {
      $this->request =$request; 
    }
    public function sheets(): array
    {
        $sheets = [];
       
        return $sheets;
    }

}
