<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\MyTrait;
use App\Models\RegisterCourse;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    use MyTrait;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function courseCode(Request $request)
    {
        $f = $this->get_faculty();
return view('audit.courseCode')->with('fc',$f);
    }

    public function getCourseCode(Request $request)
    {
    $f=$request->faculty_id;
    $d=$request->department_id;
    $fos=$request->fos;
    $s=$request->session;
    $code =str_replace(' ','',$request->code);

$registeredCourse =RegisterCourse::where([['fos_id',$fos],['session',$s],['reg_course_code',$code]])
->get();
$reg =RegisterCourse::where([['fos_id',$fos],['session',$s],['reg_course_code',$code]])->orderBy('level_id','asc')
->first();

$id =array();
foreach($registeredCourse as $v)
{
$id [] =$v->id;
}

$ccd = DB::connection('mysql2')->table('course_regs')
            ->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
            ->join('users', 'users.id', '=', 'student_results.user_id')
            ->whereIn('course_regs.registercourse_id',$id)
            ->select('users.matric_number','users.surname','users.firstname','users.othername','student_results.*')
            ->orderBy('student_results.post_date','asc')->get()->groupBy('level_id');
           // dd($ccd);
return view('audit.courseCodeDetail')->with('cd',$ccd)->with('d',$d)->with('f',$f)->with('fos',$fos)->with('s',$s)->with('reg',$reg);
    }

    public function courseCodeOld()
    {
        $f = $this->get_faculty_old();
return view('audit.courseCodeOld')->with('fc',$f);
    }

    public function getCourseCodeOld(Request $request)
    {
    $f=$request->faculty_id;
    $d=$request->department_id;
    $fos=$request->fos;
    $s=$request->session;
    $code =str_replace(' ','',$request->code);

$registeredCourse =DB::connection('oldporta')->table('all_courses')
->where([['course_custom2',$fos],['course_custom5',$s],['course_code',$code]])
->get();
//dd($registeredCourse);
$reg =DB::connection('oldporta')->table('all_courses')
->where([['course_custom2',$fos],['course_custom5',$s],['course_code',$code]])
->orderBy('level_id','asc')->first();

$id =array();
foreach($registeredCourse as $v)
{
$id [] =$v->thecourse_id;
}

$ccd = DB::connection('oldporta')->table('course_reg')
            ->join('students_results', 'course_reg.thecourse_id', '=', 'students_results.stdcourse_id')
            ->join('students_profile', 'students_profile.std_id', '=', 'students_results.std_id')
            ->join('exam_officers', 'exam_officers.examofficer_id', '=', 'students_results.examofficer')
            ->where('course_reg.thecourse_id',$id)
            ->where([['course_reg.cyearsession',$s],['students_results.std_mark_custom2',$s]])
            ->select('exam_officers.eo_firstname','students_profile.matric_no','students_profile.surname','students_profile.firstname','students_profile.othernames','students_results.*')
            ->orderBy('students_results.date_posted','asc')
            ->distinct('students_results.stdresult_id')
            ->get()->groupBy('students_results.level_id');
            //dd($ccd);
return view('audit.courseCodeDetailOld')->with('cd',$ccd)->with('d',$d)->with('f',$f)->with('fos',$fos)->with('s',$s)->with('reg',$reg);
    }

   
}
