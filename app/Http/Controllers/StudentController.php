<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\StudentReg;
use App\Models\Faculty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Traits\MyTrait;

class StudentController extends Controller
{
    use MyTrait;
    public function studentsWithOnlyFirstSemester()
    {
        $data1=array();$data2=array();
    
        $uu =DB::connection('mysql2')->table('users')
        ->where('department_id',Auth::user()->department_id)->get();
    
        $r2 =DB::connection('mysql2')->table('student_regs')->where([['department_id',Auth::user()->department_id]])
        ->whereIn('semester',[2])
        ->select('user_id')->distinct()->get();
        $r1 =DB::connection('mysql2')->table('student_regs')->where([['department_id',Auth::user()->department_id]])
        ->whereIn('semester',[1])
        ->select('user_id')->distinct()->get();
      
   
          foreach($r1 as $v)
       {
        
$data1[]=$v->user_id;
       }
       foreach($r2 as $v)
       {
        
$data2[]=$v->user_id;
       }
       $r=array_diff($data1,$data2);
    
       
        
      
      
         $u =DB::connection('mysql2')->table('users')->whereIn('id',$r)->orderBy('entry_year','desc')->get(); 
      
         $f = Faculty::where('id',Auth::user()->faculty_id)->orderBy('faculty_name', 'ASC')->first();
        return view('student.studentsWithOnlyFirstSemester.index')->with('u',$u)->with('f',$f);

    }
    public function postStudentsWithOnlyFirstSemester(Request $request)
    {
       
        $variable = $request->id;
      
        if ($variable == null) {
            Session::flash('warning', "please select students.");
            return back();
        }
 
        foreach ($variable as $v) {
           
           $l = 1;
            $courseReg2 = '';
            $secondSemester = 2;
            $us = DB::connection('mysql2')->table('users')->find($v);
            $s=$us->entry_year;
            
            //check if student has registered for second semester
            $check2 = $this->registrationStatus($v, $secondSemester, $s);

       
            if ($check2 == null) {
                $studentRegId = $this->studentReg($v,$us->faculty_id,$us->department_id,$us->programme_id, $l,$s, $secondSemester, 'NORMAL');
                $registeredCourses = $this->getRegisteredCourses1($l, $s, $secondSemester, $us->fos_id);
                $courseReg2 = $this->studentCourseReg($v, $studentRegId, $registeredCourses, $l, $s, $secondSemester, 'NORMAL');
            }

        }
        
        if ($courseReg2 == 1) {
            Session::flash('success', "successful.");
        } else {
            Session::flash('warning', "was not success.");
        }

        return back();

    }
}
