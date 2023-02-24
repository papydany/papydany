<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\RegisterCourse;
use App\Models\Department;
use App\Models\StudentReg;
use App\Models\CourseReg;
use App\Models\StudentResult;
use App\Http\Traits\MyTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ERSMopUpDownloadExport;
use App\Exports\ERSMopUpDownloadExport2;
use App\Exports\ERSMopUpDownloadExport3;
use App\Imports\MoppedUpImport;
use App\Models\AssignCoursesMopUp;
use Illuminate\Support\Facades\Session;

class MoppedUpController extends Controller
{
    use MyTrait;
    public function index(){
        $p = $this->getp();
    $fc = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('moppedUp.index')->with('fc',$fc)->with('p',$p);
    }

    public function postMoppedUp(Request $request){
        
        $p = $this->getp();
        $fc = Faculty::orderBy('faculty_name', 'ASC')->get();
        $dept = Department::orderBy('department_name', 'ASC')->get();
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
        $course_id=array();
        if($request->ers != null){
            $c=array();
            
            $sr = DB::connection('mysql2')->table('student_regs')
            ->where([['programme_id',$programme],['faculty_id',$faculty_id],['department_id',$department],['moppedUp',1]]) 
            ->orderBy('session','ASC')->get();
            if($sr->count() != 0){
                foreach($sr as $v){
                    $srArray[]=$v->id;
                    }
               
    $assign_course =DB::table('assign_courses_mop_ups')->where([['department_id',$department], ['faculty_id', $faculty_id]])
    ->select('course_id')->get();
    if (count($assign_course) > 0) {
    foreach ($assign_course as $key => $value) {
  $course_id[] = $value->course_id;
                        } }     
                // gss and gst faculty
            if(Auth::user()->faculty_id ==29)
            {
                if(Auth::user()->department_id ==139){
$code='GSS%';
                }elseif(Auth::user()->department_id == 140){
                    $code='GST%';
                }


                $cReg = DB::connection('mysql2')->table('course_regs')
                ->whereIn('studentreg_id',$srArray)
                ->where('course_code', 'like',$code) 
                ->select('course_id')->distinct('course_id')->get();
                
                foreach($cReg as $cv){
                $cArray[]=$cv->course_id;
                }
                $c = DB::table('courses')->whereIn('id',$cArray)->where('course_code', 'like',$code)->get();
           
            }else{
           
            $cReg = DB::connection('mysql2')->table('course_regs')
            ->whereIn('studentreg_id',$srArray)
            ->whereNotIn('course_id', $course_id)
            ->select('course_id')->distinct('course_id')->get();
            foreach($cReg as $cv){
           
            $cArray[]=$cv->course_id;
            }
            $c = DB::table('courses')->whereIn('id',$cArray)->get();
        }
        }
        
        return view('moppedUp.index')->with('c',$c)->with('dept',$dept)->with('d',$department)->with('fc',$fc)->with('p',$p)->with('f',$faculty_id);

        }elseif($request->viewAssignCourse != null){
    
        $assign_course =DB::table('assign_courses_mop_ups')
        ->join('courses','courses.id', '=','assign_courses_mop_ups.course_id')
        ->join('users','users.id', '=','assign_courses_mop_ups.user_id')
       
        ->where([['assign_courses_mop_ups.department_id', $department],['assign_courses_mop_ups.faculty_id', $faculty_id],
          ])
       ->select('assign_courses_mop_ups.id as assign_courses_mop_up_id','users.*','courses.course_code')
         ->get();
        
        
        return view('moppedUp.assign_courses_mop_up.view')->with('f_id',$faculty_id)->with('d_id',$department)->with('ac',$assign_course);
        
        }elseif($request->deResult != null){
            if($result->name =="admin" || $result->name =="support" || $result->name =="DVC")
            {
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
            return view('moppedUp.viewAllDepartmentResult')->with('c',$c)->with('dept',$dept)->with('d',$department)->with('fc',$fc)->with('p',$p)->with('f',$faculty_id);

            }else{
            $assign_course =DB::table('assign_courses_mop_ups')
            ->join('courses','courses.id', '=','assign_courses_mop_ups.course_id')
          ->where('assign_courses_mop_ups.user_id',Auth::user()->id)
           ->select('assign_courses_mop_ups.*','courses.course_code')
             ->get();
            }
    return view('moppedUp.deResult')->with('c',$assign_course);
       
        }else
        {
        $user = DB::connection('mysql2')->table('users')
        ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
        ->where([['student_regs.programme_id',$programme],['student_regs.faculty_id',$faculty_id],['student_regs.department_id',$department],['moppedUp',1],['semester',1]]) 
        ->orderBy('session','ASC')
        ->orderBy('level_id','ASC')
        ->select('users.*','level_id','session')->get();
        $group =$user->groupBy('level_id');

        $course = DB::connection('mysql2')->table('course_regs')
        ->join('student_regs', 'course_regs.studentreg_id', '=', 'student_regs.id')
        ->where([['student_regs.programme_id',$programme],['student_regs.faculty_id',$faculty_id],['student_regs.department_id',$department],['moppedUp',1]]) 
        ->orderBy('course_code','ASC')
        ->select('course_regs.*')->get();
        $c_group =$course->groupBy('user_id');
        $courseArray=array();

        foreach($c_group as $k =>$value)
        {
           $courseArray[$k] =['value'=>$value];
           
        }

    }

        return view('moppedUp.view')->with('f',$faculty_id)->with('d',$department)->with('gsr',$group)->with('cA',$courseArray)->with('fc',$fc)->with('p',$p);

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

       public function mopUpERS3()
       {
       // $department =Department::find($d);
  return Excel::download(new ERSMopUpDownloadExport3(),'GSS.xlsx');
       }

       //========================assign mop up courses=========
       public function assignCoursesMopUp(Request $request)
       {
           $id = $request->input('id');
           $this->validate($request, array('lecturer' => 'required'));
           if ($id == null) {
               return back();
           }
           $lecturer = $request->input('lecturer');
           $f = $request->f;
           $d = $request->d;
   // status 1 mean fos is assign and 0 mean not assigned
   $date=date("Y-m-d h:i:s");
           foreach ($id as $key => $value) {
               $v[] = ['course_id' => $value,'created_at'=>$date,'admin'=>auth::user()->id, 'user_id' => $lecturer, 'department_id' => $d, 'faculty_id' => $f];
   
           }
   
    DB::table('assign_courses_mop_ups')->insert($v);
           Session::flash('success', "SUCCESSFUL.");
           return back();
   
       }

//======================== mopped up=================================

public  function uploadMopUpResult($id, $d)
{  
    $c=Course::find($id);
   
return view('moppedUp.excelUpload')->with('id',$id)->with('d',$d)->with('c',$c);

}

public function excelInsertMopUPResult(Request $request)
{
   /* if(Auth::user()->resultRight != 1){
        Session::flash('warning', "You need result upload right to upload or update any result. Click Result Upload Right button");
        return back();
    }*/
    $errors =array();

    
    if($request->file('excel_import_result'))
    {
        
        try {
    $path = $request->file('excel_import_result');

Excel::import(new MoppedUpImport($request->all()),$path);
} catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
    $failures = $e->failures();
    
    foreach ($failures as $failure) {
        $failure->row(); // row that went wrong
        $failure->attribute(); // either heading key (if using heading row concern) or column index
      $errors[] =  $failure->errors(); // Actual error messages from Laravel validator
        $failure->values(); // The values of the row that has failed.
    }
    dd($errors);
}

Session::flash('success', "SUCCESSFUL.");
    return back();
     }
}

//============================ view result===========
public  function viewMopUpResult($id, $d)
{  
    $department =Department::find($d);
    $faculty =Faculty::find($department->faculty_id);
    $course=RegisterCourse::where([['course_id',$id],['department_id',$d]])->first();
    
    
    $user = DB::connection('mysql2')->table('users')
        ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
        ->join('course_regs', 'student_regs.id', '=', 'course_regs.studentreg_id')
        ->leftjoin('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
        ->where([['users.department_id',$d],['course_regs.semester_id', $course->semester_id],['student_regs.semester', $course->semester_id],
        ['student_regs.department_id', $d],['course_regs.course_id',$id],['student_regs.moppedUp',1]])
         ->select('course_regs.*', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number','student_results.total','student_results.grade')
         ->orderBy('users.matric_number','ASC')
         ->get();
     
return view('moppedUp.viewResult')->with('d',$department)->with('f',$faculty)->with('c',$course)->with('u',$user);

}

//======================== remove assign mop result==================
public function removeMopAssignCourse($id)
{
    DB::table('assign_courses_mop_ups')->where('id',$id)->delete();
    Session::flash('success', "SUCCESSFUL.");
    return back();
}

public function upd()
{
$s=StudentReg::where([['semester',2],['moppedUp',1]])->get();
foreach($s as $v)
{
CourseReg::where([['user_id',$v->user_id],['semester_id',$v->semester],['session',$v->session]])
->update(['studentreg_id' => $v->id]);  
}

}

//===============================log====================
public function mopUpWithCALog()
{
    $all=StudentReg::where('moppedUp',1)->get();
    foreach($all as $v){
$sr[]=$v->id;
    }

    $cr=CourseReg::whereIn('studentreg_id',$sr)->get();
    foreach($cr as $v){
        $crId[]=$v->id;
    }
    $srt=StudentResult::whereIn('coursereg_id',$crId)->where('CA','!=',0)->get();
foreach($srt as $v)
{
    $srtID[]=$v->id;
}
//dd($srtID);
StudentResult::whereIn('id',$srtID)->delete();
dd('success');
}

public function upRc($s,$fos){
$u=DB::connection('mysql2')->table('users')->where([['fos_id',$fos],['entry_year',$s]])->get();
foreach($u as $v){
    $rc=RegisterCourse::where([['fos_id',$fos],['session',$s],['level_id',1],['semester_id',1],['reg_course_status','C']])->get();
    foreach($rc as $b){
        CourseReg::where([['user_id',$v->id],['semester_id',$b->semester_id],['session',$s],['course_id',$b->course_id],['course_status',$b->reg_course_status]])
        ->update(['registercourse_id' => $b->id]);     
    }
}
dd('success');
}
}
