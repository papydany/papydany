<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;



class ResultDownloadGssExportII implements FromView,ShouldAutoSize
{
      /**
    * @return \Illuminate\Support\Collection
    */
    
    private $result,$title,$code,$s,$season,$course_id;
        
    public function __construct(array $request)
    {
        //$this->id = $request['id'];
    $this->s = $request['session'];
    $this->season=$request['period'];
    $s =$this->s;
    $ss =$this->season;
    $this->course_id =$request['course_id'];
    $c =DB::table('courses')->find($this->course_id);
    $this->title =$c->course_title;
    $this->code =$c->course_code;
    $this->result=DB::connection('mysql2')->table('student_results')
    ->join('users','student_results.user_id','=','users.id')
    ->where([['course_id',$this->course_id],['session',$s],['season',$ss]])->get();
    }
   
    public function view(): View
    {
       
return view('examofficer.gss.excelResult', [
            'user' => $this->result,'title'=>$this->title,'code'=>$this->code
        ]);
       }
}
