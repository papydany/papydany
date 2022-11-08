<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class Graduate implements FromView ,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $u;
    private $y;

    public function __construct(array $request)
    {
        $this->d =$request['department']; 
        $this->s=$request['start'];
        $this->e=$request['end'];
        $this->y =substr($this->e,-4);
        $this->u =DB::connection('mysql2')->table('users')->where([['department_id',$this->d],['graduation_status',1]])
        ->whereBetween('date_of_graduation', [$this->s, $this->e])->orderBy('matric_number','Asc')->get();
    

    }
        
    public function view(): View
    {
      
     
        return view('support.graduate.excel', ['u'=>$this->u,'e'=>$this->e,'s'=>$this->s,'d'=>$this->d,'y'=>$this->y]);
       }
    
}
