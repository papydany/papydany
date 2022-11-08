<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class PinsExport implements FromView
{
    private $reg;
    /**
    * @return \Illuminate\Support\Collection
    */
   

   
    public function view(): View
    {
        $pin = DB::table('pins')->where('id','>=','161188')->orderBy('id','ASC')->get();
return view('support.pin.export', ['pin' => $pin]);
       }
}
