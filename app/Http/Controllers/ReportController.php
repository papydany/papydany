<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\CourseReg;
use App\Models\Fos;
use App\Models\Faculty;
use App\Models\Department;
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

    public function registeredCoursesToStudentsII(){
        $fc = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('report.registeredCoursesToStudentsII.index')->with('fc',$fc);
 }
    public function postRegisteredCoursesToStudentsII(Request $request){

$semester =$request->semester; 
$s=$request->session; 
$p=$request->programme;
$d=$request->department;
$f=Faculty::find($request->faculty_id);
$dpt=Department::find($d);

$course = DB::table('courses')->where('department_id',$d)->get();
$data =array();$id=array();$fosDataId=array(); $id2=array();$data2=array();
foreach($course as $c)
{
$id[]=$c->id;
$data[$c->id] =['code'=>$c->course_code];
$courseReg = CourseReg::where([['session',$s],['semester_id',$semester]])
->where('course_id',$c->id)
->select('fos_id', (DB::raw('COUNT(fos_id) as  bat')))
->groupBy('fos_id')->get();

if(count($courseReg) != 0)
{
foreach($courseReg as $v)
{
    $data1[$c->id.'-'.$v->fos_id]=['number'=>$v->bat];
    $id2[] =$c->id;
    $fosDataId[]=$v->fos_id;
}
}
}
//dd($data1);
$regCourse = DB::table('register_courses')->where([['session',$s],['semester_id',$semester]])
->join('fos', 'register_courses.fos_id', '=', 'fos.id')
->where([['register_courses.programme_id',$p]])
->whereIntegerInRaw('course_id',$id2)
->whereIntegerInRaw('fos_id',$fosDataId)
->select('register_courses.department_id','register_courses.id','course_id','fos_name','fos_id')
->get()->groupBy('course_id');

    foreach($regCourse as $k => $cid) 
    {
 /*  $courseReg = CourseReg::where([['session',$s],['semester_id',$semester]])
   ->where('course_id',$k)
   ->select('fos_id', (DB::raw('COUNT(fos_id) as  bat')))
   ->groupBy('fos_id')
   ->get();
   if(count($courseReg) != 0)
   {
   foreach($courseReg as $v)
   {
       $data3[$v->fos_id] =['number'=>$v->bat];
       $id2 [] =$k;
    }*/
   $data2[$k] =[$cid];
//}

}
//dd($data2);
   /* $courseReg = CourseReg::where([['session',$s],['semester_id',$semester]])
    ->whereIn('course_id',$rgCourseId)
    ->select('course_id', (DB::raw('COUNT(course_id) as  bat')))
    ->groupBy('course_id')
    ->get();
    foreach($courseReg as $v)
    {
        $data1[$v->course_id] =['number'=>$v->bat];
        $id2 [] =$v->course_id;
    }*/

   // dd($data2);                                                                             
    $course = Course::whereIntegerInRaw('id',$id2)->orderBy('course_code','asc')->get();

   // dd($courseReg);//->groupBy('course_code')->sortBy('course_code'));
  return view('report.registeredCoursesToStudentsII.view')->with('cr',$course)->with('data1',$data1)->with('data2',$data2)->with('data',$data)->with('s',$s)->with('sm',$semester)->with('f',$f)->with('dpt',$dpt);
   
    }

    public function postRegisteredCoursesToStudentsIII(Request $request){
        $s=$request->session; 
        $semester=$request->semester; 
        $code =trim($request->code);
        $code=$code.'%';
        $course = DB::table('courses')->where('course_code','Like',$code)->get();
        $data =array();$id=array();$fosDataId=array(); $id2=array();$data2=array();
        foreach($course as $c)
        {
        $id[]=$c->id;
        $data[$c->id] =['code'=>$c->course_code];
        $courseReg = CourseReg::where([['session',$s],['semester_id',$semester]])
        ->where('course_id',$c->id)
        ->select('fos_id', (DB::raw('COUNT(fos_id) as  bat')))
        ->groupBy('fos_id')->get();
        
        if(count($courseReg) != 0)
        {
        foreach($courseReg as $v)
        {
            $data1[$c->id.'-'.$v->fos_id]=['number'=>$v->bat];
            $id2[] =$c->id;
            $fosDataId[]=$v->fos_id;
        }
        }
        }
        //dd($data1);
        $regCourse = DB::table('register_courses')->where([['session',$s],['semester_id',$semester]])
        ->join('fos', 'register_courses.fos_id', '=', 'fos.id')
        ->whereIntegerInRaw('course_id',$id2)
        ->whereIntegerInRaw('fos_id',$fosDataId)
        ->select('register_courses.department_id','register_courses.id','course_id','fos_name','fos_id')
        ->get()->groupBy('course_id');
        
            foreach($regCourse as $k => $cid) 
            {
        
           $data2[$k] =[$cid];
     
        
        }
                                                                             
            $course = Course::whereIntegerInRaw('id',$id2)->orderBy('course_code','asc')->get();
        
          
          return view('report.registeredCoursesToStudentsII.view')->with('cr',$course)->with('data1',$data1)->with('data2',$data2)->with('data',$data)->with('s',$s)->with('sm',$semester);
           
            }
}
