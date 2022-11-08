<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\RegisterCourse;
use App\Models\Department;
use App\Http\Traits\MyTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ERSMopUpDownloadExport;
use App\Exports\ERSMopUpDownloadExport2;
class MoppedUpController extends Controller
{
    use MyTrait;
    public function index(){
        $p = $this->getp();
    $fc = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('moppedUp.index')->with('fc',$fc)->with('p',$p);
    }

    public function postMoppedUp(Request $request){
        $result= session('key');
        if($result->name =="HOD" || $result->name =="examsofficer")
        {
        $faculty_id=Auth::user()->faculty_id;
        $department=Auth::user()->department_id; 
        $programme=$request->programme;
        }elseif($result->name =="Deskofficer")
        {
        $faculty_id=Auth::user()->faculty_id;
        $department=Auth::user()->department_id; 
        $programme=Auth::user()->programme_id;
        }else{
        $programme=$request->programme;
        $faculty_id=$request->faculty_id;
        $department=$request->department;
        }
        $srArray =array();
        $cArray =array();
        if($request->ers != null){
            $c=array();
            
            $sr = DB::connection('mysql2')->table('student_regs')
            ->where([['programme_id',$programme],['faculty_id',$faculty_id],['department_id',$department],['moppedUp',1]]) 
            ->orderBy('session','ASC')->get();
            if($sr->count() != 0){
            foreach($sr as $v){
$srArray[]=$v->id;
            }
            $cReg = DB::connection('mysql2')->table('course_regs')
            ->whereIn('studentreg_id',$srArray) 
            ->select('course_id')->distinct('course_id')->get();
            foreach($cReg as $cv){
$cArray[]=$cv->course_id;
            }
            $c = DB::table('courses')->whereIn('id',$cArray)->get();
        }
        return view('moppedUp.index')->with('c',$c)->with('d',$department);

        }{
        $user = DB::connection('mysql2')->table('users')
        ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
        ->where([['student_regs.programme_id',$programme],['student_regs.faculty_id',$faculty_id],['student_regs.department_id',$department],['moppedUp',1],['semester',1]]) 
        ->orderBy('session','ASC')
        ->orderBy('level_id','ASC')
        ->select('users.*','level_id','session')->get();
        $group =$user->groupBy('level_id');

        $course = DB::connection('mysql2')->table('course_regs')
        ->join('student_regs', 'course_regs.studentreg_id', '=', 'student_regs.id')
        ->where([['student_regs.programme_id',$programme],['student_regs.faculty_id',$faculty_id],['student_regs.department_id',$department],['moppedUp',1],['semester',1]]) 
        ->orderBy('course_code','ASC')
        ->select('course_regs.*')->get();
        $c_group =$course->groupBy('user_id');
        $courseArray=array();

        foreach($c_group as $k =>$value)
        {
           $courseArray[$k] =['value'=>$value];
           
        }

    }

        return view('moppedUp.view')->with('f',$faculty_id)->with('d',$department)->with('gsr',$group)->with('cA',$courseArray);

    }

       //========================== classAttendance=================
       public function mopUpClassAttendance($id,$s,$d,$fos,$l,$semester)
       {
           $reg = RegisterCourse::where([['id', $id], ['session', $s]])->first();
           $courseReg = DB::connection('mysql2')->table('users')
           ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
           ->join('course_regs', 'student_regs.id', '=', 'course_regs.studentreg_id')
           ->where([['users.fos_id', $fos], ['users.department_id', $d], ['course_regs.semester_id', $semester]
            ,['course_regs.course_id', $reg->course_id],['student_regs.moppedUp',1]])
            ->select('course_regs.*','student_regs.session', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number','users.image_url')
            ->orderBy('level_id','ASC')->orderBy('users.matric_number','ASC')
            ->get()->groupBy('level_id');
            return view('moppedUp.classAttendance')->with('reg',$reg)->with('item',$courseReg)->with('d',$d)->with('l',$l)->with('s',$s)->with('sm',$semester)->with('fos',$fos);
   
       }

       public function mopUpERS($id,$d)
       {
        $department =Department::find($d);
        $faculty =Faculty::find($department->faculty_id);
        $course=RegisterCourse::where([['course_id',$id],['department_id',$d]])->first();
    $cTitle=str_replace('/','',$course->reg_course_title);
    $title=str_replace(' ', '',$cTitle).'_'.$course->reg_course_code;
  
   
    return Excel::download(new ERSMopUpDownloadExport($id,$d,$course->reg_course_code,$course->reg_course_title,$course->semester_id,$department->department_name,$faculty->faculty_name), $title.'.xlsx');
       }

       public function mopUpERS2($id)
       {
       $course=RegisterCourse::where([['course_id',$id]])->first();
    $cTitle=str_replace('/','',$course->reg_course_title);
    $title=str_replace(' ', '',$cTitle).'_'.$course->reg_course_code;
  return Excel::download(new ERSMopUpDownloadExport2($id,$course->reg_course_code,$course->reg_course_title,$course->semester_id), $title.'.xlsx');
       }
}
