<?php
namespace App\Exports;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class ERSMopUpDownloadExport3 implements FromView,ShouldAutoSize

{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $user;
    private $group;
    public function __construct()
    {
      
       
       $course_id=array();
       $c=Course::where('department_id',139)->get();
       foreach($c as $v)
       {
        $course_id[] =$v->id;
       }
       
         $this->user = DB::connection('mysql2')->table('users')
         ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
         ->join('course_regs', 'student_regs.id', '=', 'course_regs.studentreg_id')
         ->where([['moppedUp',1],['semester',1]]) 
         ->whereIn('course_id',$course_id)
       ->orderBy('matric_number','ASC')
       ->distinct()
         ->select('matric_number','surname','firstname','othername','users.id','users.department_id')->get();
         $this->group =$this->user->groupBy('department_id');
 
         $course = DB::connection('mysql2')->table('course_regs')
         ->join('student_regs', 'course_regs.studentreg_id', '=', 'student_regs.id')
         ->where('moppedUp',1)
         ->whereIn('course_id',$course_id)
         ->orderBy('course_code','ASC')
         ->select('course_regs.*')->get();
         $c_group =$course->groupBy('user_id');
         $this->courseArray=array();
 
         foreach($c_group as $k =>$value)
         {
            $this->courseArray[$k] =['value'=>$value];
            
         }
       
       
    }
    
   
    public function view(): View
    {
        
return view('moppedUp.ers_excel3', ['user' => $this->group,'cA'=>$this->courseArray]);
       }
}
