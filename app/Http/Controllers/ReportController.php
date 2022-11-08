<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\CourseReg;
use App\Models\Fos;
use App\Models\RegisterCourse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function registeredCourseToStudent(){
        return view('report.index');
 
    }
    public function postRegisteredCourseToStudent(Request $request){
$l=$request->level;
$semester =$request->semester; 
$s=$request->session; 
$p=$request->programme;
  
//$course=Course::where([['level',$l],['semester',$semester]])->get();
$fos =Fos::where('programme_id',$p)->select('department_id')->distinct()->get();
foreach($fos as $v){
$dept [] =$v->department_id;
}

$course = DB::table('courses')
            ->join('departments', 'courses.department_id', '=', 'departments.id')
            ->where([['level',$l],['semester',$semester]])
            ->whereIn('departments.id',$dept)
            ->select('courses.*', 'departments.department_name')
            ->get();
         
$data =array();$id=array();
foreach($course as $c)
{
    $id[]=$c->id;
    $data[$c->id] =['code'=>$c->course_code,'unit'=>$c->course_unit,'dept'=>$c->department_name];
}

    $courseReg = CourseReg::where([['session',$s],['semester_id',$semester]])
    ->whereIn('course_id',$id)
    ->select('course_id', (DB::raw('COUNT(course_id) as  bat')))
    ->groupBy('course_id')
    ->get();
    foreach($courseReg as $v)
    {
        $data1[$v->course_id] =['number'=>$v->bat];
        $id2 [] =$v->course_id;
    }
    $regCourse = DB::table('register_courses')->where([['session',$s],['semester_id',$semester]])
    ->join('departments', 'register_courses.department_id', '=', 'departments.id')
    ->whereIn('course_id',$id2)
    ->select('department_id','course_id','department_name')
    ->distinct('department_id')
    //->groupBy('course_id')
    ->get()->groupBy('course_id');
    foreach($regCourse as $k => $cid) 
    {
$data2[$k] =[$cid];
    }  
   // dd($data2);                                                                             
    $course = Course::whereIn('id',$id2)->orderBy('course_code','asc')->get();

   // dd($courseReg);//->groupBy('course_code')->sortBy('course_code'));
  return view('report.rcStudent')->with('cr',$course)->with('data1',$data1)->with('data2',$data2)->with('data',$data)->with('l',$l)->with('s',$s)->with('sm',$semester);
   
    }
}
