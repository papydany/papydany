<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;



class ResultLoadGssExport implements FromView,ShouldAutoSize
{
      /**
    * @return \Illuminate\Support\Collection
    */
    
    private $user;
        
    public function __construct(array $request,$course_id,$d)
    {
        //$this->id = $request['id'];
    $this->s = $request['session'];
    $this->season=$request['period'];
    $s =$this->s;
    $ss =$this->season;
    $this->course_id =$course_id;
    $this->d=$d;
    $c =DB::table('courses')->find($course_id);
    $this->title =$c->course_title;
    $this->code =$c->course_code;

        $this->user  = DB::connection('mysql2')->table('users')
        ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
        ->Leftjoin('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
        ->where([['course_regs.course_id',$course_id],['semester_id',$c->semester],['course_regs.session',$s],['period',$ss],['department_id',$d]])
        ->orderBy('users.matric_number','ASC')
        ->select('users.firstname', 'users.surname','users.othername','users.matric_number','student_results.id','student_results.ca','student_results.exam','student_results.grade','student_results.total')
        ->get();
       
    }
   
    public function view(): View
    {
       
return view('examofficer.gss.excelResult', [
            'user' => $this->user,'title'=>$this->title,'code'=>$this->code
        ]);
       }
}
