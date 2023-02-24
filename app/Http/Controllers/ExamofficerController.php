<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Fos;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Course;
use App\Models\StudentResult;
use App\Models\RegisterCourse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ERSDownloadExport;
use App\Exports\ERSDownLoadGssExport;
use App\Exports\ERSDownLoadGssExportII;
use App\Exports\ResultLoadGssExport;
use App\Models\UpdateResult;
use Barryvdh\DomPDF\Facade as PDF;

use App\Http\Traits\MyTrait;
class ExamofficerController extends Controller
{
	use MyTrait;
  Const MEDICINE = 14;
  const DENTISTRY = 10;
  Const Role =3;

   public function __construct()
    {
        $this->middleware('auth');
    }

     public function index()
    {
     $p =$this->getp();

  return view('examofficer.index')->with('p',$p)->with('med',self::MEDICINE)->with('den',Self::DENTISTRY);
    }
//-----------------------------------------------------------------------------------------------------------------

    public function eo_assign_courses(Request $request)
    {
  $this->validate($request,array('programme'=>'required','session'=>'required','level'=>'required','semester'=>'required',));
  $p =$request->input('programme');
  $l =$request->input('level');
  $semester =$request->input('semester');
  $session =$request->input('session');
  $d =Auth::user()->department_id;
  $f =Auth::user()->faculty_id;
  $id=Auth::user()->id;
  $f_id = $this->get_fos_exams_officer_and_hod($id,$d,$p);
  
  //$course = $this->getRegisterAssign_courses($id,$l,$semester,$session,$d,$f,$f_id);
  //$this->getRegisterAssign_courses_examsOfficer($id,$l,$semester,$session,$d,$f,$f_id);
  $course = $this->getRegisterAssign_courses($id,$l,$semester,$session,$d,$f,$f_id);
  //dd($course);
 return view('examofficer.eo_assign_courses')->with('c',$course)->with('sm',$semester)->with('s',$session)->with('l',$l)->with('fos',$f_id)->with('p',$p)->with('med',Self::MEDICINE)->with('den',Self::DENTISTRY);

    }
//---------------------------------------- get student  by course -----------------------------------
  public  function eo_result_c(Request $request)
    {  
      $id =$request->input('id');
      $result_type =$request->input('result_type'); 
      $l =$request->input('level');   
      $sm =$request->input('semester'); 
      $s =$request->input('session'); 
  $period =$request->input('period'); 
  $registercourse = DB::table('register_courses')->find($id);
  
  $d =$registercourse->department_id;
  $f =$registercourse->faculty_id;
  $department=Department::find($d);
$faculty=Faculty::find($f);
  $p =$request->input('programme_id'); 

  //$prob_user_id = $this->getprobationStudents($p,$d,$f,$l,$s);
  
  if($request->excel =='excel')
  {
      /*$user = DB::connection('mysql2')->table('users')
          ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
          ->where('course_regs.registercourse_id', $id)
          ->where('course_regs.period', $period)
          ->whereNotIn('users.id', $prob_user_id)
          ->orderBy('users.matric_number', 'ASC')
          ->select('course_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.entry_year')
          ->get();*/
        
      return view('examofficer.excelUpload.index')->with('f',$f)->with('c',$registercourse)->with('rt',$result_type)->with('med',self::MEDICINE)->with('period',$period);

  }elseif($request->excel =='download')
  {
    $fos=Fos::find($registercourse->fos_id);
    $fosname=str_replace('/','',$fos->fos_name);
    $title=str_replace(' ', '',$fosname).'_'.$registercourse->reg_course_code;
   
    return Excel::download(new ERSDownloadExport($request->all(),$registercourse,$department->department_name,$faculty->faculty_name), $title.'.xlsx');
  }

  $studentWithResult = $this->student_with_result($registercourse->course_id,$registercourse->fos_id,$s,$sm,$period);
//dd($studentWithResult);
  if($result_type == "Omitted")
    {
     /* $user_with_no_result =array();
      $coursereg =CourseReg::where([['registercourse_id',$id],['period',$period]])->get();
      foreach ($coursereg as $key => $value) {
        $result =DB::connection('mysql2')->table('student_results')
                         ->where('coursereg_id',$value->id)
                         ->first();
        if($result == null){
       $user_with_no_result [] = $value->user_id;
       }               

      }*/
      $user = DB::connection('mysql2')->table('users')
        ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
        ->where([['course_regs.registercourse_id',$id],['level_id',$l],['semester_id',$sm],['session',$s],['period',$period]])
        ->whereNotIn('users.id',$studentWithResult)
       // ->whereNotIn('users.id',$prob_user_id)
        ->orderBy('users.matric_number','ASC')
        ->select('course_regs.*', 'users.firstname', 'users.surname','users.othername','users.matric_number','users.entry_year')
        ->get();
      }else{

      $user = DB::connection('mysql2')->table('users')
        ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
        ->where([['course_regs.registercourse_id',$id],['level_id',$l],['semester_id',$sm],['session',$s],['period',$period]])
        ->whereNotIn('users.id',$studentWithResult)
    //  ->whereNotIn('users.id',$prob_user_id)
        ->orderBy('users.matric_number','ASC')
        ->select('course_regs.*', 'users.firstname', 'users.surname','users.othername','users.matric_number','users.entry_year')
        ->get();

        
 }
    
  //Get current page form url e.g. &page=6
        $url ="eo_result_c?id=".$id."&level=".$l."&semester=".$sm."&session=".$s."&period=".$period."&result_type=".$result_type;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        //Create a new Laravel collection from the array data
        $collection = new Collection($user);

        //Define how many items we want to be visible in each page
        $perPage = 20;

        //Slice the collection to get the items to display in current page
        $currentPageSearchResults = $collection->slice(($currentPage- 1) * $perPage, $perPage)->all();

        //Create our paginator and pass it to the view
        $paginatedSearchResults= new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);

       // return view('search', ['results' => $paginatedSearchResults]);
               
      return view('examofficer.eo_result_c')->with('l',$l)->with('u',$paginatedSearchResults)->with('url',$url)->with('c',$registercourse)->with('rt',$result_type)->with('med',self::MEDICINE)->with('den',Self::DENTISTRY)->with('f',$f);
    }  

//=========================result insert for student percourse ==========================

    public function eo_insert_result(Request $request)
    {
        //$this->validate($request,array('id'=>'required',));
        if(Auth::user()->resultRight != 1){
          Session::flash('warning', "You need result upload right to upload or update any result. Click Result Upload Right button");
          return back();
      }
        $flag = $request->input('flag');
       // dd($flag);
         $faculty_id = $request->input('faculty_id');
        $date = date("Y/m/d H:i:s");
        $url =$request->input('url');

        $id =$request->input('id');
        if($id == null)
{
Session::flash('warning',"the result you want to submit must be checked by your right hand.");
return back();
}

        foreach ($id as $key => $value) {
        $coursereg_id =$request->input('coursereg_id')[$value];
        $user_id =$request->input('user_id')[$value];
        $mat_no =$request->input('matric_number')[$value];
        $course_id =$request->input('course_id')[$value];
        $cu =$request->input('cu')[$value];
        $session=$request->input('session')[$value];
        $semester =$request->input('semester')[$value];
        $l_id =$request->input('level_id')[$value];
        $season =$request->input('season')[$value];
         $ca =$request->input('ca')[$value];
         $scriptNo =$request->input('scriptNo')[$value];
        $exam=$request->input('exams')[$value];
        //$total=$request->input('total')[$value];
        $total=$ca + $exam;
        $entry_year=$request->input('entry_year')[$value];
        if($faculty_id == Self::MEDICINE || $faculty_id == Self::DENTISTRY){
          $grade_value =$this->get_grade_medicine($total,$season,$l_id);
          }else{
        $grade_value =$this->get_grade($total);
         }
        
        $grade = $grade_value['grade'];
        $cp = $this->mm($grade, $cu);

        if($ca ==''){$ca=0;}
          if($exam ==''){$exam=0;}
          if($total ==''){$total=0;}
     

      //$check_result = StudentResult::where([['level_id', $l_id], ['session', $session], ['course_id', $course_id], ['coursereg_id', $coursereg_id],['flag',$flag]])->first();
      
      $check_result = StudentResult::where([['user_id',$user_id],['matric_number',$mat_no],['level_id', $l_id], ['session', $session], ['course_id', $course_id], ['coursereg_id', $coursereg_id]])->first();
     
      if ($check_result != null) {
        $result_id =$request->input('result_id')[$value];


      
$update = StudentResult::find($result_id);
if($update->approved != 2){ //  check againt sbc
  $ur = new UpdateResult;
  $ur->ca =$update->ca;
  $ur->exam =$update->exam;
  $ur->total =$update->total;
  $ur->user_id = $update->user_id;
  $ur->student_results_id= $update->id;
  $ur->former_deskofficer_id =$update->examofficer;
 $ur->posted = $update->post_date;
  $ur->save();

$update->scriptNo = $scriptNo;
$update->ca = $ca;
            $update->exam = $exam;
            $update->total = $total;
            $update->grade = $grade;
            $update->cp = $cp['cp'];
            $update->examofficer = Auth::user()->id;
            $update->save();
}
         }else{

          $insert_data[] = ['user_id'=>$user_id,'matric_number'=>$mat_no,'scriptNo'=>$scriptNo,'course_id'=>$course_id,'coursereg_id'=>$coursereg_id,'ca'=>$ca,'exam'=>$exam,'total'=>$total,'grade'=> $grade,'cu'=>$cu,'cp'=>$cp['cp'],'level_id'=>$l_id,
                            'session'=>$session,'semester'=>$semester,'status'=>0,'season'=>$season,'flag'=>$flag,'examofficer'=>Auth::user()->id,'post_date'=>$date,'approved'=>0];
                    }


                  
       

        }
                  if(isset($insert_data))
        {
        if(count($insert_data) > 0)
        {
         DB::connection('mysql2')->table('student_results')->insert($insert_data);
        }
    }
        Session::flash('success',"SUCCESSFUL.");
         return back();
      //  return redirect($url);
    }



//--------------------------------------------view result --------------------------------------------------
    public function v_result()
    {
   $p =$this->getp();
return view('examofficer.view_result')->with('p',$p);
    }

//-----------------------------------------------------------------------------------------------------------------

    public function post_v_result(Request $request)
    {
      $pp =$this->getp();
  $this->validate($request,array('programme'=>'required','session'=>'required','level'=>'required','semester'=>'required',));
  $p =$request->input('programme');
  $l =$request->input('level');
  $semester =$request->input('semester');
  $session =$request->input('session');
  $d =Auth::user()->department_id;
  $f =Auth::user()->faculty_id;
  $id=Auth::user()->id;
  $f_id = $this->get_fos_exams_officer_and_hod($id,$d,$p);
   $course = $this->getRegisterAssign_courses($id,$l,$semester,$session,$d,$f,$f_id);
  return view('examofficer.view_result')->with('c',$course)->with('sm',$semester)->with('s',$session)->with('l',$l)->with('p',$pp);

    }    

//-----------------------------------------display result ----------------------------------------------------
 
 public function display_result(Request $request)
 {
  
  $c_id =$request->input('id');
  $xc = explode('~', $c_id);
  $id = $xc[0];
  $f_id = $xc[1];
  $course_code = $xc[2]; 
  $p =$request->input('programme');
  $l =$request->input('level');
  $sm =$request->input('semester');
  $s =$request->input('session');
  $period =$request->input('period');
  $regCourse=RegisterCourse::find($id);

$user= $this->registerStudentWithResultOrNot($regCourse,$sm,$s,$period);

if($request->sbc != null){
  $seal=array(); $updateValue =2;$date =date('Y-m-d');
  foreach($user as $items){
 foreach($items as $v)
 {
  if($v->id != null)
  {
    $seal[]=[$v->id];
  }
}
}

  if(!empty($seal))
  {
    DB::connection('mysql2')
    ->table('student_results')
    ->whereIn('id',$seal)
    ->update(['approved' => $updateValue,'approved_date'=>$date]);
  }
 
  $data = ['u'=>$user,'l'=>$l,'s'=>$s,'sm'=>$sm,'course_code'=>$course_code,'f_id'=>$f_id,'reg'=>$regCourse];
  $pdf = PDF::loadview('examofficer.sbc.display_result',$data);
  return $pdf->setPaper('a4', 'landscape')->stream('examofficer.sbc.display_result.pdf');
}else{
  return view('examofficer.display_result')->with('u',$user)->with('sm',$sm)->with('s',$s)->with('l',$l)->with('f_id',$f_id)->with('course_code',$course_code);
 }
}

 //--------------------------------------------view result --------------------------------------------------
    public function r_student()
    {
         $p =$this->getp();
return view('examofficer.r_student')->with('p',$p);
    }

//-----------------------------------------------------------------------------------------------------------------

    public function post_r_student(Request $request)
    {
      $pp =$this->getp();
  $this->validate($request,array('programme'=>'required','session'=>'required','level'=>'required','semester'=>'required',));
  $p =$request->input('programme');
  $l =$request->input('level');
  $semester =$request->input('semester');
  $session =$request->input('session');
  $d =Auth::user()->department_id;
  $f =Auth::user()->faculty_id;
  $id=Auth::user()->id;
  
  $f_id = $this->get_fos_exams_officer_and_hod($id,$d,$p);
  
$course = $this->getRegisterAssign_courses($id,$l,$semester,$session,$d,$f,$f_id);
  

          return view('examofficer.r_student')->with('c',$course)->with('sm',$semester)->with('s',$session)->with('l',$l)->with('p',$pp);

    }

 //-----------------------------------------display result ----------------------------------------------------
 
 public function d_student(Request $request)
 {
   $c_id =$request->input('id');
     $xc = explode('~', $c_id);
    $id = $xc[0];
     $f_id = $xc[1];
      $course_code = $xc[2]; 
   $p =$request->input('programme');
  $l =$request->input('level');
  $sm =$request->input('semester');
  $s =$request->input('session');
  $period =$request->input('period');
$user= $this->getRegisterStudent($id,$l,$sm,$s,$period);

 
  return view('examofficer.d_student')->with('u',$user)->with('sm',$sm)->with('s',$s)->with('l',$l)->with('f_id',$f_id)->with('course_code',$course_code);
 }
 //---------------------------------- delete result -------------------------------------------
 public function eo_delete_result()
 {
  $p =$this->getp();

  return view('examofficer.delete_result.index')->with('p',$p); 
 } 

 public function post_eo_delete_result(Request $request)
 {
  $p =$this->getp();
  $this->validate($request,array('programme'=>'required','session'=>'required','level'=>'required','semester'=>'required',));
  $pp =$request->input('programme');
  $l =$request->input('level');
  $semester =$request->input('semester');
  $session =$request->input('session');
  $d =Auth::user()->department_id;
  $f =Auth::user()->faculty_id;
  $id=Auth::user()->id;
  $f_id = $this->get_fos_exams_officer_and_hod($id,$d,$pp);
 // $fos_id =Fos::where([['department_id',$d],['programme_id',$pp]])->get();
 $course = $this->getRegisterAssign_courses($id,$l,$semester,$session,$d,$f,$f_id);
 
 //$course2getRegisterAssign_courses_examsOfficer($id,$l,$semester,$session,$d,$f,$f_id)

return view('examofficer.delete_result.index')->with('c',$course)->with('sm',$semester)->with('s',$session)->with('l',$l)->with('fos',$f_id)->with('p',$p)->with('pp',$pp);

 } 

 public function eo_delete_result_detail(Request $request)
 {
   $reg_id =$request->input('id');
  $l =$request->input('level');
  $sm =$request->input('semester');
  $s =$request->input('session');
  $period =$request->input('period');
  $registercourse = DB::table('register_courses')->find($reg_id);
 $user = DB::connection('mysql2')->table('users')
        ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
        ->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
        ->where([['course_regs.registercourse_id',$reg_id],['course_regs.level_id',$l],['semester_id',$sm],['course_regs.session',$s],['period',$period]])
        ->orderBy('users.matric_number','ASC')
        ->select('users.firstname', 'users.surname','users.othername','users.matric_number','student_results.id','student_results.ca','student_results.exam','student_results.total')
        ->get();
       
  return view('examofficer.delete_result.detail')->with('u',$user)->with('sm',$sm)->with('s',$s)->with('l',$l)->with('c',$registercourse);
 }
 

 function eo_delete_desk_result($id)
{

//$reg =StudentResult::destroy($id);
$d =DB::connection('mysql2')->table('student_results')->where([['id',$id],['approved','!=',2]])->delete();
if($d == 0){
  Session::flash('warning',"you can not delete SBC approved result.");
}else{
Session::flash('success',"successful.");
}
return back();
}

function eo_delete_desk_multiple_result(Request $request)
{
 $variable = $request->input('id');
  if($variable == null)
{Session::flash('warning',"you have not select any result.");
   return back();
}
$d =DB::connection('mysql2')->table('student_results')->where('approved','!=',2)
->whereIn('id',$variable)->delete();
if($d == 0){
  Session::flash('warning',"you can not delete SBC approved result.");
}else{
Session::flash('success',"successful.");
}
return back();
}
 
 //----------------------------------------------------------------------------------------------------------
 public function getRegisterAssign_courses_examsOfficer($id,$l,$semester,$session,$d,$f,$f_id)
 {
  
    $course =DB::table('register_courses')->where([['semester_id',$semester],['level_id',$l],['session',$session]])
    ->wherein('fos_id',$f_id)
    ->get()
    ->orderBy('register_courses.reg_course_status','ASC')
    ->orderBy('register_courses.reg_course_code','ASC')
    ->groupBy('fos_id');
    return $course;
 }
 public function getRegisterAssign_courses($id,$l,$semester,$session,$d,$f,$f_id)
 {
 $role = session('key');//$this->g_rolename(Auth::user()->id);
 
 if($role->name =='examsofficer' && $f == Self::MEDICINE || $role->name =='examsofficer' && $f ==Self::DENTISTRY || $role->name =='examsofficer' && $session < 2020 || $role->name =='HOD' && $session < 2020)
 {
  $first = DB::table('register_courses')
  ->where([['level_id',$l],['semester_id',$semester],['session',$session],['department_id',$d],['faculty_id',$f]])
  ->wherein('fos_id',$f_id)
  ->orderBy('register_courses.reg_course_status','ASC')
  ->get()->groupBy('fos_id');
  $course = DB::table('assign_courses')
    ->join('register_courses', 'register_courses.id', '=', 'assign_courses.registercourse_id')
    ->where([['assign_courses.user_id',$id],['assign_courses.level_id',$l],['assign_courses.semester_id',$semester],['assign_courses.session',$session]])
    //->orderBy('register_courses.reg_course_code','ASC')
    ->orderBy('register_courses.reg_course_status','ASC')
    
    
  ->get()->groupBy('fos_id');
  $course =$course->merge($first);
  $course =$course->flatten()->groupBy('fos_id');
 }else{

    $course = DB::table('assign_courses')
    ->join('register_courses', 'register_courses.id', '=', 'assign_courses.registercourse_id')
    ->where([['assign_courses.user_id',$id],['assign_courses.level_id',$l],['assign_courses.semester_id',$semester],['assign_courses.session',$session]])
    //->wherein('assign_courses.fos_id',$f_id)
    ->orderBy('register_courses.reg_course_status','ASC')
    ->orderBy('register_courses.reg_course_code','ASC')
    ->get()->groupBy('fos_id');
 }
        return $course;
 }

 
 //---------------------------------------------------------------------------------------------------
 public function getRegisterStudent($id,$l,$sm,$s,$period)
 {
  $user = DB::connection('mysql2')->table('users')
        ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
        ->where([['course_regs.registercourse_id',$id],['level_id',$l],['semester_id',$sm],['session',$s],['period',$period]])
        ->orderBy('users.matric_number','ASC')
        ->select('course_regs.*', 'users.firstname', 'users.surname','users.othername','users.matric_number','users.image_url')
        ->get();
        return $user;
 }
 public function registerStudentWithResultOrNot($reg,$sm,$s,$period)
 {
  $register_course =DB::table('register_courses')
        ->where([['course_id',$reg->course_id],['department_id',$reg->department_id],['session',$reg->session],['fos_id',$reg->fos_id]])
        ->get();
        $register_course_id=array();
        foreach($register_course as $v)
        {
        $register_course_id [] =$v->id;
        }
  $user = DB::connection('mysql2')->table('users')
  ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
  ->Leftjoin('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
  ->where([['semester_id',$sm],['course_regs.session',$s],['period',$period]])
  ->whereIn('course_regs.registercourse_id',$register_course_id)
  ->orderBy('course_regs.level_id','ASC')
  ->orderBy('users.matric_number','ASC')
  ->select('users.firstname','course_regs.level_id','users.surname','users.othername','users.matric_number','student_results.id','student_results.ca','student_results.exam','student_results.grade','student_results.total','student_results.scriptNo')
  ->get()->groupBy('level_id');
  return $user;
 }

 public function registerStudentWithResultOrNotGss($reg_id,$sm,$s,$period,$d)
 {
  $user = DB::connection('mysql2')->table('users')
  ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
  ->Leftjoin('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
  ->where([['course_regs.course_id',$reg_id],['semester_id',$sm],['course_regs.session',$s],['period',$period],['department_id',$d]])
  ->orderBy('users.matric_number','ASC')
  ->select('users.firstname', 'users.surname','users.othername','users.matric_number','student_results.id','student_results.ca','student_results.exam','student_results.grade','student_results.total')
  ->get();
  return $user;
 }
 public function getlevel($id)
{
$level =DB::table('levels')->where('programme_id',$id)->get();
   return response()->json($level);
}
public function getsemester($id)
{
   $semester =DB::table('semesters')->where('programme_id',$id)->get();
   return response()->json($semester);

} 

protected function p()
{
	$p =DB::table('programmes')->get();
return $p;
}
//=====================without pds ==================

public function getFos_hod($id)
    {
     // $role =$this->getrolename(Auth::user()->id);
      $role = session('key');
   if($role->name == "examsofficer")
   {
    $f_id =array();
  $fos_id =DB::table('deskoffice_fos')->where('user_id',Auth::user()->id)->get();
  foreach($fos_id as $v){
    $f_id [] =$v->fos_id;
  }
  $d =DB::table('fos')->whereIn('id',$f_id)
  ->where([['department_id', Auth::user()->department_id],['programme_id',$id]])
  ->get();
   }else{
     $d =DB::table('fos')->where([['department_id', Auth::user()->department_id],['programme_id',$id]])->get();
   }
    return response()->json($d);
    }


//========================== get fos assign to exams officer and hod fos ==================
public function get_fos_exams_officer_and_hod($id,$d,$p)
{
 // $role =$this->getrolename($id);
  $role = session('key');
  $f_id =array();
  if($role->name == "examsofficer")
  {
 $fos_id =DB::table('deskoffice_fos')->where('user_id',$id)->get();
 if(count($fos_id) == 0)
 {
   Session::flash('warning',"no field of study. contact system admin");
   return back();
 }
 foreach ($fos_id as $key => $value) {
 $f_id[] =$value->fos_id;
 }
//}elseif($role->name =='HOD'){

  }else{
 $fos_id =DB::table('fos')->where([['department_id',$d],['programme_id',$p]])->get();

 if(count($fos_id) == 0)
 {
   //Session::flash('warning',"no field of study. contact system admin");
   return back();
 }
 foreach ($fos_id as $key => $value) {
 $f_id[] =$value->id;
 }
  }
  return $f_id;
}
//=============================== exams officer code for faculty of gss====
public function lecturer_gss()
{
 $p =$this->getp();
return view('examofficer.gss.index')->with('p',$p);
}


public function post_lecturer_gss(Request $request)
{
$this->validate($request,array('programme'=>'required','session'=>'required','semester'=>'required',));
$p =$request->input('programme');
$semester =$request->input('semester');
$session =$request->input('session');
$d =Auth::user()->department_id;

//$course=Course::where([['department_id',$d],['semester',$semester]])->get();
if($request->cpd == 1){
$course = DB::table('courses')
->join('register_courses', 'register_courses.course_id', '=', 'courses.id')
->join('departments', 'register_courses.department_id', '=', 'departments.id')
->where([['courses.department_id',$d],['register_courses.semester_id',$semester],['register_courses.session',$session]])
->orderBy('departments.department_name','ASC')
->select('register_courses.course_id','register_courses.department_id','register_courses.reg_course_code','departments.department_name')
->distinct('register_courses.course_id')
->get();
return view('examofficer.gss.eo_assign_courses')->with('c',$course)->with('sm',$semester)->with('s',$session)->with('p',$p);
}elseif($request->cpc == 1){
  $course = DB::table('courses')
  ->join('register_courses', 'register_courses.course_id', '=', 'courses.id')
->where([['courses.department_id',$d],['register_courses.semester_id',$semester],['register_courses.session',$session]])
  ->orderBy('reg_course_code','ASC')
  ->select('register_courses.course_id','register_courses.reg_course_code')
  ->distinct('register_courses.course_id')
  ->get();
  return view('examofficer.gss.eo_assign_coursesII')->with('c',$course)->with('sm',$semester)->with('s',$session)->with('p',$p);

}
}

public  function eo_result_c_gss(Request $request)
{  
  $id =$request->input('id');
   $ex_id =explode('-',$id);
  $result_type =$request->input('result_type');  
  $s =$request->input('session'); 
$period =$request->input('period'); 
$course_id=$ex_id[0];
$d =$ex_id[1];
$course =Course::find($course_id);
$department=Department::find($d);
$faculty=Faculty::find($department->faculty_id);
if($request->excel =='excel')
{

  return view('examofficer.gss.excelUpload')->with('s',$s)->with('d',$department)->with('c',$course)->with('rt',$result_type)->with('period',$period);

}elseif($request->excel =='download')
{

$title=str_replace(' ', '',$department->department_name).'_'.$course->course_code;

return Excel::download(new ERSDownLoadGssExport($request->all(),$course_id,$d,$department->department_name,$faculty->faculty_name), $title.'.xlsx');
}


}  


public  function eo_result_c_gssII(Request $request)
{  
$course_id =$request->input('id');
$result_type =$request->input('result_type');  
$s =$request->input('session'); 
$period =$request->input('period'); 
$course =Course::find($course_id);
if($request->excel =='excel')
{
return view('examofficer.gss.excelUploadII')->with('s',$s)->with('c',$course)->with('rt',$result_type)->with('period',$period);
}elseif($request->excel =='download')
{

$title=str_replace(' ', '',$course->course_code);

return Excel::download(new ERSDownLoadGssExportII($request->all(), $course_id), $title.'.xlsx');
}


} 
//--------------------------------------------view result --------------------------------------------------
public function v_result_gss()
{
$p =$this->getp();
return view('examofficer.gss.view_result')->with('p',$p);
}

//-----------------------------------------------------------------------------------------------------------------

public function post_v_result_gss(Request $request)
{
  $pp =$this->getp();
$this->validate($request,array('programme'=>'required','session'=>'required','semester'=>'required',));
$p =$request->input('programme');
$semester =$request->input('semester');
$session =$request->input('session');
$d =Auth::user()->department_id;

//$course=Course::where([['department_id',$d],['semester',$semester]])->get();
$course = DB::table('courses')
->join('register_courses', 'register_courses.course_id', '=', 'courses.id')
->join('departments', 'register_courses.department_id', '=', 'departments.id')
->where([['courses.department_id',$d],['register_courses.semester_id',$semester],['register_courses.session',$session]])
->orderBy('departments.department_name','ASC')
->select('register_courses.course_id','register_courses.department_id','register_courses.reg_course_code','departments.department_name')
->distinct('register_courses.course_id')
->get();

return view('examofficer.gss.view_result')->with('c',$course)->with('sm',$semester)->with('s',$session)->with('p',$pp);

}  

public function display_result_gss(Request $request)
{
  $id =$request->input('id');
  $ex_id =explode('-',$id);
  $s =$request->input('session'); 
$period =$request->input('period'); 
$course_id=$ex_id[0];
$d =$ex_id[1];
$course =Course::find($course_id);
$department=Department::find($d);
 
    $f = $department->faculty_id;
     $course_code = $course->course_code; 
     $course_title = $course->course_title; 
 // $p =$request->input('programme');
 
 $sm =$request->input('semester');
 $s =$request->input('session');
 $period =$request->input('period');
 if($request->excel != null)
 {
  return Excel::download(new ResultLoadGssExport($request->all(),$course_id,$d), $course_id.'.xlsx');
 }

$user= $this->registerStudentWithResultOrNotGss($course_id,$sm,$s,$period,$d);
 return view('examofficer.gss.display_result')->with('course_title',$course_title)->with('d',$d)->with('u',$user)->with('sm',$sm)->with('s',$s)->with('f',$f)->with('course_code',$course_code);
}
//==============================end =============================




    
}
