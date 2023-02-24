<?php
namespace App\Http\Traits;
use Illuminate\Support\Facades\DB;
use App\Models\Programme;
use App\Models\Faculty;
use App\Models\CourseReg;
use App\Models\StudentReg;
use App\Models\Level;
use App\Models\Fos;
use App\Models\Semester;
use App\Models\Department;
use App\Models\EnableResultUpload;
use App\Models\StudentResult;
use App\Models\RegisterCourse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

trait MyTrait {
  
   public function mm($G,$U) {

        $return = array();
       
        switch( $G ) {
            case 'A':
                $return['cp'] = 5 * $U;
                break;
            case 'B':
                $return['cp'] = 4 * $U;
                break;
            case 'C':
                $return['cp'] = 3 * $U;
                break;
            case 'D':
                $return['cp'] = 2 * $U;
                break;
            case 'E':
                $return['cp'] = 1 * $U;
                break;
            case 'F':
                $return['cp'] = 0 * $U;
                break;
                case 'PD':
                  $return['cp'] = 5 * $U;
                  break;
                  case 'P':
                    $return['cp'] = 4* $U;
                    break;
            }
        
        return $return;
    }
 public function get_grade($total)
 { 
    if($total == 0)
    {
    $total =1; 
}

  switch($total) {
               case $total < 40:
                 $return['grade']  ='F';
                 return $return;
                break; 
                case $total >= 70:
                $return['grade']='A';
               return $return;
                break;
            case $total >= 60:
                $return['grade'] ='B';
                return $return;
                break;
            case $total >= 50:
                 $return['grade'] ='C';
                 return $return;
                break;
            case $total >= 45:
                 $return['grade'] ='D';
                return $return;
                break;
            case $total >= 40:
                 $return['grade'] ='E';
               return $return;
                break; 
              }
            }


 public function get_grade_medicine($total,$season,$l)
 { 
    if($total == 0)
    {
    $total =1; 
}
if($l > 2)

{
  if($season =='VACATION')
  {
    switch($total) {
      case $total < 50:
        $return['grade']  ='F';
        return $return;
       break; 
      
   case $total >= 50:
        $return['grade'] ='P';
        return $return;
       break;
   }
  }else{
  switch($total) {
    case $total < 50:
      $return['grade']  ='F';
      return $return;
     break; 
     case $total >= 70:
     $return['grade']='PD';
    return $return;
     break;
 case $total >= 50:
      $return['grade'] ='P';
      return $return;
     break;
 }
}

}else{
  switch($total) {
               case $total < 40:
                 $return['grade']  ='F';
                 return $return;
                break; 
                case $total >= 70:
                $return['grade']='A';
               return $return;
                break;
            case $total >= 60:
                $return['grade'] ='B';
                return $return;
                break;
            case $total >= 50:
                 $return['grade'] ='C';
                 return $return;
                break;
            case $total >= 45:
                 $return['grade'] ='D';
                return $return;
                break;
            case $total >= 40:
                 $return['grade'] ='E';
               return $return;
                break;
            
            }

          }

 }
      public function g_rolename($id){
        $user = DB::table('roles')
            ->join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id',$id)
            ->first();
            return $user->name;
    }

protected function getp()
{
  $p =Programme::where('id','!=',1)->get();
  
return $p;
}

protected function get_faculty()
{
$sql =Faculty::orderBy('faculty_name','ASC')->get();
return $sql;
}

protected function get_faculty_old()
{
$sql =DB::connection('oldporta')->table('faculties')
->orderBy('faculties_name','ASC')->get();
return $sql;
}

protected function get_fos()
{
    $fos= DB::connection('mysql')->table('fos')
            ->join('deskoffice_fos', 'fos.id', '=', 'deskoffice_fos.fos_id')
            ->where('deskoffice_fos.user_id',Auth::user()->id)
            // ->where('deskoffice_fos.status',1)
            ->orderBy('fos_name','ASC')
            ->select('fos.*')
            ->get();
            return $fos;
}
protected function get_department($id)
{
  
$sql =Department::find($id);
return $sql;
}
//---------------------------- get probation students -----------------------------------
 public function getprobationStudents($p,$d,$f,$l,$s)
 {
    // get student that did probation
  $s1 = $s-1;
  $prob_user_id = array(); $normal=array();
  
$prob_Student_reg =DB::connection('mysql2')->table('student_regs')
->where([['semester',1],['programme_id',$p],['department_id',$d],['faculty_id',$f],['level_id',$l],['session',$s],['moppedUp',null],['season','NORMAL']])->get();
if(count($prob_Student_reg) > 0){
foreach ($prob_Student_reg as $key => $value) {
$normal []=$value->user_id;
}
 }
 $u =DB::connection('mysql2')->table('student_regs')
 ->where([['session','<=',$s1],['level_id',$l],['semester',1]])
 ->whereIn('user_id',$normal)->get();
 if($u->count() > 0){
  foreach ($u as $key => $v) {   
 $prob_user_id [] = $v->user_id;
}
}
return $prob_user_id;
 }




 public function probationStudent($id,$l,$season)
 {
    $studentreg =DB::connection('mysql2')->table('student_regs')
    ->where([['level_id',$l],['user_id',$id],['semester',1],['season',$season]])->count();
    //dd($studentreg);
    if($studentreg > 1)
    {
     return true;   
    }
    return false;
 }
 public function getrolename($id){
    $user = DB::table('roles')
        ->join('user_roles', 'roles.id', '=', 'user_roles.role_id')
        ->where('user_roles.user_id',$id)
        ->first();
        return $user->name;
}

public function student_with_no_result($id,$period)
{
    $user_with_no_result =array();
    $coursereg =CourseReg::where([['registercourse_id',$id],['period',$period]])->get();
    foreach ($coursereg as $key => $value) {
      $result =DB::connection('mysql2')->table('student_results')
                       ->where('coursereg_id',$value->id)
                       ->first();
      if($result == null){
     $user_with_no_result [] = $value->user_id;
     }               

    }
    return $user_with_no_result;
}

public function student_with_result($course_id,$fos,$s,$semester,$period)
{
    $user_with_result =array();
    $result =DB::connection('mysql2')->table('student_results')
    ->join('course_regs', 'course_regs.id', '=', 'student_results.coursereg_id')
    ->join('users', 'users.id', '=', 'course_regs.user_id')
    ->where([['course_regs.course_id',$course_id],['student_results.session',$s],['student_results.semester',$semester],['student_results.season',$period]])
    ->where('users.fos_id',$fos)
    ->get();
    
    if(count($result) > 0){
foreach($result as $v)
{
  $user_with_result [] = $v->user_id;
}
    }

    return $user_with_result;
}
//============================= correctional result =========================================

// get registered Correctional students
public function getRegisteredStudentsWithFlag($p,$d,$f,$fos,$l,$s,$flag,$perpage)
{
 // get student that did probation
 
 $prob_user_id = array(); $correctional_array = array();

$prob_user_id = $this->getprobationStudents($p,$d,$f,$l,$s);

/*$correctional =$this->getStudentsWithFlag($p,$d,$f,$fos,$l,$s,$flag);

if($correctional != null)
{
 foreach ($correctional as $key => $value) {
   $correctional_array [] = $value->id;
 }
}*/
if($perpage == 0){
 $users = DB::connection('mysql2')->table('users')
           ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
           ->join('student_results', 'users.id', '=', 'student_results.user_id')
           ->where([['users.programme_id',$p],['users.department_id',$d],['users.faculty_id',$f],['users.fos_id',$fos],['student_results.level_id',$l],['student_results.session',$s],['student_results.flag',$flag]])
           ->where([['student_regs.programme_id',$p],['student_regs.department_id',$d],['student_regs.faculty_id',$f],['users.fos_id',$fos],['student_regs.level_id',$l],['student_regs.session',$s],['student_regs.moppedUp',null]])
           ->whereNotIn('users.id',$prob_user_id)
         //  ->whereIn('users.id',$correctional_array)
           ->orderBy('users.matric_number','ASC')
           ->distinct()            
           ->select('users.*')
           ->get();
}else{
  $users = DB::connection('mysql2')->table('users')
  ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
  ->join('student_results', 'users.id', '=', 'student_results.user_id')
  ->where([['users.programme_id',$p],['users.department_id',$d],['users.faculty_id',$f],['users.fos_id',$fos],['student_results.level_id',$l],['student_results.session',$s],['student_results.flag',$flag]])
  ->where([['student_regs.programme_id',$p],['student_regs.department_id',$d],['student_regs.faculty_id',$f],['users.fos_id',$fos],['student_regs.level_id',$l],['student_regs.session',$s],['student_regs.moppedUp',null]])
  ->whereNotIn('users.id',$prob_user_id)
//  ->whereIn('users.id',$correctional_array)
  ->orderBy('users.matric_number','ASC')
  ->distinct()            
  ->select('users.*')
  ->paginate($perpage)->withQueryString();
}
  
  return $users;
}
 //------------------------------ get students with flag ---------------------------
 public function getStudentsWithFlag($p,$d,$f,$fos,$l,$s,$flag)
 {
    $users = DB::connection('mysql2')->table('users')
            ->join('student_results', 'users.id', '=', 'student_results.user_id')
            ->where([['users.programme_id',$p],['users.department_id',$d],['users.faculty_id',$f],['users.fos_id',$fos],['student_results.level_id',$l],['student_results.session',$s],['student_results.flag',$flag]])
            ->orderBy('users.matric_number','ASC')
            ->distinct()            
            ->select('users.*')
            ->get();
return $users;
 }

 public function SelectedStudentsWithFlag($array,$l,$s,$flag)
 {
    $users = DB::connection('mysql2')->table('users')
            ->join('student_results', 'users.id', '=', 'student_results.user_id')
            ->where([['student_results.level_id',$l],['student_results.session',$s],['student_results.flag',$flag]])
            ->whereIn('users.id',$array)
            ->orderBy('users.matric_number','ASC')
            ->distinct()            
            ->select('users.*')
            ->get();
return $users;
 }

 //---------------------- get registered students ------------------------
 public function registerdStudents($fos_id,$p,$d,$f,$season,$session,$l_id,$prob_user_id)
 {
  $user = DB::connection('mysql2')->table('student_regs')
  ->distinct('student_regs.matric_number')
      ->join('users', 'student_regs.user_id', '=', 'users.id')
      ->where('users.fos_id',$fos_id)
      ->where([['student_regs.programme_id',$p],['student_regs.department_id',$d],['student_regs.faculty_id',$f],['student_regs.season',$season],
          ['student_regs.session',$session],['student_regs.level_id',$l_id]])
      ->whereNotIn('users.id',$prob_user_id)
      ->orderBy('users.matric_number','ASC')
      ->select('users.id','users.firstname', 'users.surname','users.othername','users.matric_number','users.fos_id','users.entry_year')
      ->get();
      
  return $user;
 }

 // -------------------- get course unit -----------------

 public function getTotalCourseunit($fos_id,$s,$level_id)
 {
      // get course unit set for the programme    
      $course_unit =DB::table('course_units')->where([['fos',$fos_id],['session',$s],['level',$level_id]])->first();
      
      if($course_unit == null)
      {
        $course_unit =DB::table('course_units')->where([['fos',0],['session',$s],['level',0]])->first();
     
      }

      return $course_unit;
 }

 // -------------- get total of unit of courses taken in semster -------
public function getTotalCourseUnitPerSemster($id,$session,$semester,$level,$season)
{
    $courseRegTotal =DB::connection('mysql2')->table('course_regs')
    ->where([['session',$session],['user_id',$id],
    ['semester_id',$semester],['level_id',$level],['period',$season]])->sum('course_unit');
  return $courseRegTotal; 
}
 
//========================== studentreg========================
public function studentReg($id,$f,$d,$p,$l,$s,$s_id,$season,$fos){
  if($s== 2020 || $s==2021){
    $lateReg=1;
  }else{
    $lateReg=0;
  }
  $newStudentReg = New StudentReg;
  $newStudentReg->user_id=$id;
  $newStudentReg->session=$s;
  $newStudentReg->semester=$s_id;
  $newStudentReg->programme_id=$p;
  $newStudentReg->faculty_id=$f;
  $newStudentReg->department_id=$d;
  $newStudentReg->fos_id=$fos;
  $newStudentReg->level_id=$l;
  $newStudentReg->season=$season;
  $newStudentReg->deskofficer=Auth::user()->id;
  $newStudentReg->lateReg=$lateReg;
  $newStudentReg->save();
  return $newStudentReg->id;
}

public function getRegisteredCourses1($l,$s,$s_id,$fos)
{
  $rc =DB::table('register_courses')
  ->where([['session',$s],['fos_id',$fos],['semester_id',$s_id],['level_id',$l],
  ['reg_course_status','C']])->get();
return $rc; 
}

public function studentCourseReg($id,$studentreg_id,$rc,$l,$s,$s_id,$season,$fos)
{$rcId=array(); $grcId=array(); $data=array();
  if($s== 2020 || $s==2021){
    $lateReg=1;
  }else{
    $lateReg=0;
  }
$grc =DB::connection('mysql2')->table('course_regs')
->where([['studentreg_id',$studentreg_id],['session',$s],['user_id',$id],['semester_id',$s_id],['level_id',$l],
['period',$season]])->get();
foreach($grc as $v1){
  $grcId[] =$v1->registercourse_id;

}

foreach($rc as $v)
{
  if(!in_array($v->id,$grcId))
  {
    $data[] =['studentreg_id'=>$studentreg_id,'registercourse_id'=>$v->id,
    'user_id'=>$id,'level_id'=>$l,'fos_id'=>$fos,'semester_id'=>$s_id,'course_id'=>$v->course_id,'period'=>$season,
    'session'=>$s,'course_title'=>$v->reg_course_title,'course_code'=>$v->reg_course_code,
    'course_status'=>$v->reg_course_status,'course_unit'=>$v->reg_course_unit,'lateReg'=>$lateReg];
    
  }
  
}
if(count($data) != 0){
$c =DB::connection('mysql2')->table('course_regs')->insert($data);
return 1;
}
return 2;
}

public function studentCourseRegWithStatus($id,$studentreg_id,$rc,$l,$s,$s_id,$season,$status,$fos)
{$rcId=array(); $grcId=array(); $data=array();
  if($s== 2020 || $s==2021){
    $lateReg=1;
  }else{
    $lateReg=0;
  }
$grc =DB::connection('mysql2')->table('course_regs')
->where([['studentreg_id',$studentreg_id],['session',$s],['user_id',$id],['semester_id',$s_id],['level_id',$l],
['period',$season]])->get();
foreach($grc as $v1){
  $grcId[] =$v1->registercourse_id;

}

foreach($rc as $v)
{
  if(!in_array($v->id,$grcId))
  {
    $data[] =['studentreg_id'=>$studentreg_id,'registercourse_id'=>$v->id,'fos_id'=>$fos,
    'user_id'=>$id,'level_id'=>$l,'semester_id'=>$s_id,'course_id'=>$v->course_id,'period'=>$season,
    'session'=>$s,'course_title'=>$v->reg_course_title,'course_code'=>$v->reg_course_code,
    'course_status'=>$status,'course_unit'=>$v->reg_course_unit,'lateReg'=>$lateReg];
    
  }
  
}
if(count($data) != 0){
$c =DB::connection('mysql2')->table('course_regs')->insert($data);
return 1;
}
return 2;
}
public function registrationStatus($id,$s_id,$s)
{
 return DB::connection('mysql2')->table('student_regs')
     ->where([['user_id',$id],['semester',$s_id],['session',$s]])
     ->first();
}
public function assignCourses($p){

}
protected function get_level()
{
    $level = Level::where('programme_id', Auth::user()->programme_id)->get();
    return $level;
}
protected function get_semester()
    {
        $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
        return $semester;
    }
    public function generateRandomString($length) {
      $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
   }


   /**--------------- failed course unit-------------------------------------------- */
public function failCourseUnit($l,$id,$s,$season)
{
  $courseId =array();
  $courseArray=['GSS','GST'];
$sql2 =StudentResult::where([['level_id',$l],['session',$s],['user_id',$id],['grade','F'],['season',$season]])
->select('course_id')->get();
foreach($sql2 as $v)
{
$sql1 =DB::connection('mysql2')->table('student_results')
->where([['course_id',$v->course_id],['session','<=',$s],['user_id',$id],['grade','F']])
->get()->count();
$reg =CourseReg::where([['course_id',$v->course_id],['session',$s],['user_id',$id]])->first();

if( in_array(substr($reg->course_code,0,3), $courseArray))

{
$courseId []=$v->course_id;
}else{

if($sql1 < 3)
{
  $courseId []=$v->course_id;
}
}
}

$sql =StudentResult::where([['level_id',$l],['session',$s],['user_id',$id],['grade','F'],['season',$season]])
->whereIn('course_id',$courseId )
->get(); 
$tc =$sql->sum('cu');
return $tc;
}


public function passedCourseUnit($l,$id,$s,$season)
{
  $courseId =array();
  $courseArray=['GSS','GST'];


$sql =StudentResult::where([['level_id',$l],['session',$s],['user_id',$id],['grade','!=','F'],['season',$season]])
->get(); 
$tc =$sql->sum('cu');
return $tc;
}

// ----------------- drop course ----------------------------
public function dropCourse($id,$l,$s,$period)
{
  $sql1 = CourseReg::where([['user_id',$id],['level_id',$l],['session',$s],['period',$period]])
  ->whereIn('course_status',['C','E'])
  ->select('course_id')
 ->get();
  $sql2 = DB::table('register_courses')
         ->where([['reg_course_status','C'],['level_id',$l],['session',$s],['fos_id',Auth::user()->fos_id]])
         ->whereNotIn('course_id',$sql1)->orderBy('semester_id','asc')
        ->get();
return $sql2;
}
/**--------------- failed course -------------------------------------------- */
public function failCourse($l,$id,$s,$season)
{
  $courseId =array();
  $courseArray=['GSS','GST'];
$sql =StudentResult::where([['level_id',$l],['session',$s],['user_id',$id],['grade','F'],['season',$season]])
->select('course_id')->get();
foreach($sql as $v)
{

  $sql1 =DB::connection('mysql2')->table('student_results')
  ->where([['course_id',$v->course_id],['session','<=',$s],['user_id',$id],['grade','F']])
->get()->count();
$cosreg =CourseReg::where([['course_id',$v->course_id],['session',$s],['user_id',$id]])->first();
//var_dump(substr($cosreg->course_code,0,3));

if(in_array(substr($cosreg->course_code,0,3), $courseArray))

{
$courseId []=$v->course_id;
}else{

if($sql1 < 3)
{
  $courseId []=$v->course_id;
}
}
}//dd();
$reg =DB::table('register_courses')->whereIn('course_id',$courseId)
->where([['fos_id',Auth::user()->fos_id],['session',$s],['level_id',$l]])
->orderBy('semester_id','asc')->get();
return $reg;
}

/*----------------------- previously failed courses --------------------------*/
public function getPreviouFailedCourses($id,$semester,$previous_session,$season =null)
{
  if($season != null)
  {
  $failed_courses =StudentResult::where([['user_id',$id,],['semester',$semester],['session',$previous_session],['grade','F'],['season',$season]])->get();
 
  }else{
  $failed_courses =StudentResult::where([['user_id',$id,],['semester',$semester],['session',$previous_session],['grade','F']])->get();
  }return $failed_courses;
}

/*------------------------------- number of previous failed courses per course id --------------------*/
public function NumberPreviousFailedCoursePerCourseId($id,$semester,$previous_session,$course_id)
{

 $no_failed_courses =StudentResult::where([['user_id',$id,],['semester',$semester],['session','<=',$previous_session],['grade','F'],['course_id',$course_id]])->get()->count();
 return  $no_failed_courses;
}

public function getCourseCodeType($courseReg_id)
{
  $c =CourseReg::find($courseReg_id);
  return $c;
}

public function GetRegisteredCompulsaryCourses($semester,$session,$level,$fos,$specialization_id)
{
  $specializationId =$this->getSpecializationIdWithLevel($specialization_id,$level);
  if($specializationId > 0)
  {
  
    $regcourse= DB::table('register_courses')
    ->join('register_specializations', 'register_courses.id', '=', 'register_specializations.registercourse_id')
    ->where([['semester_id',$semester],['session',$session],['programme_id',Auth::user()->programme_id],['faculty_id',Auth::user()->faculty_id],['department_id',Auth::user()->department_id],['register_courses.fos_id',$fos],['level_id',$level],['reg_course_status','C']])
    ->where('register_specializations.specialization_id',$specializationId)
    ->select('register_courses.*')
    ->get();

  }else{
  $regcourse=RegisterCourse::where([['semester_id',$semester],['session',$session],['programme_id',Auth::user()->programme_id],['faculty_id',Auth::user()->faculty_id],['department_id',Auth::user()->department_id],['fos_id',$fos],['level_id',$level],['reg_course_status','C']])
  ->whereIn('specialization_id',[0,$specializationId])
  ->get();
  }
  return $regcourse;
}

public function getSpecializationIdWithLevel($specializationId,$level)
 {
   if($specializationId == 0)
   {
     return 0;
   }else{
    // $s =Specialization::find($specializationId);
     $s = DB::table('specializations')->find($specializationId);

     if($level < $s->level)
     {
       return 0;
     }
     return $specializationId;
   }
 }

 /*-------------------------getsingele course reg at a time --------------------------------------*/
public function GetCourseRegSingle($session,$level,$id,$regcourse_id,$course_id,$semester,$period)
{
   $courseReg=CourseReg::where([['session',$session],['level_id',$level],['user_id',$id],['registercourse_id',$regcourse_id],['course_id',$course_id],['semester_id',$semester],['period',$period]])->first();
   return $courseReg; 
}

public function GetRegisteredCoursesWithArrayCourseId($semester,$session,$level,$status,$array_course_id,$fos)
{
   $regcourse =RegisterCourse::where([['semester_id',$semester],['fos_id',$fos],['session',$session],['level_id',$level],['reg_course_status',$status]])->whereIn('course_id',$array_course_id)->get();

   return $regcourse;
}

function get_cgpa($s,$user_id,$season){
  if($season == 'VACATION')
  {
    $takeType =['NORMAL','VACATION'];
  }else if($season == 'RESIT'){
    $takeType =['NORMAL','RESIT'];
  }else{
    $takeType =['NORMAL'];
  }
$tcu = 0; $tgp = 0;
$row = DB::connection('mysql2')->table('student_results')->distinct()
->join('course_regs', 'course_regs.id', '=', 'student_results.coursereg_id')
->where([['student_results.session','<=',$s],['student_results.user_id',$user_id],])
->where([['course_regs.session','<=',$s],['course_regs.user_id',$user_id]])
->whereIn('student_results.season',$takeType)
->get();
if(count($row) > 0)
{
foreach ($row as $key => $value)
{
  // $cu = $this->get_crunit($value->coursereg_id, $value->session, $user_id);
   $cu =$value->course_unit;
   $gp = $this->get_gradepoint($value->grade, $cu);
   $tcu += $cu;
   $tgp += $gp;
}

    @$gpa = $tgp / $tcu ;
    $gpa = number_format ($gpa,2); 
    return $gpa;
  }
  return 0;
}

public  function get_gradepoint($grade,$cu){

  if ($grade == 'A' )
      return 5.0 * $cu;
  else if ($grade == 'B' )
      return 4.0 * $cu;
  else if ($grade == 'C' )
      return 3.0 * $cu;
  else if ($grade == 'D' )
      return 2.0 * $cu;
  else if ($grade == 'E' )
      return 1.0 * $cu;
  else if ($grade == 'F' )
      return 0.0 * $cu ;
  
 }

 protected function Probtion($l,$id,$s,$cgpa,$season,$foss)
{
$return ='';
$fail_cu=$this->get_fail_crunit($l,$id,$s,$season);

$fos =Fos::find($foss);


if( $l >= $fos->duration)
{
  return $return;
}
$s_on_probation =$this->getprobationStudents1($id,$l,$s);


if($s_on_probation == 'true')
{

if($cgpa >=0.00 && $cgpa <=0.99){
            
        $return = 'WITHDRAW';
        }
        elseif($cgpa >=1.00 && $cgpa <=1.49){

        $return = 'WITHDRAW OR CHANGE PROGRAMME';

        }
      
    
        return $return;
}else{

  
   if($fail_cu > 15 && $cgpa < 1.5 || $cgpa >=0.00 && $cgpa <=0.99){
            
        $return = 'WITHDRAW';
        }
        elseif($cgpa >=1.00 && $cgpa <=1.49 || $fail_cu ==15){

            $return = 'PROBATION';

        }elseif($cgpa >=1.5 && $fail_cu >15){
        $return = 'CHANGE PROGRAMME';
        } 
      //new condion for probation for medlap in 300l for DE and 400l for UME
//if they did not write second semester course the professonal exams, they have to repeat d class

        if($fos->id == 143 && $l == 4)
        {
          $sql2 = DB::table('register_courses')
          ->where([['reg_course_status','C'],['level_id',$l],['session',$s],['fos_id',$fos->id],['semester_id',2]])->first();
          $row = DB::connection('mysql2')->table('course_regs')
        ->where([['session',$s],['user_id',$id],['level_id',$l]])
        ->whereIn('course_id',[$sql2->course_id])
        ->get();
        if(count($row) == 0)
        {
          $return = 'PROBATION';
        }
        
        }elseif($fos->id == 144 && $l == 3){
          $sql2 = DB::table('register_courses')
          ->where([['reg_course_status','C'],['level_id',$l],['session',$s],['fos_id',$fos->id],['semester_id',2]])->first();
          
          $row = DB::connection('mysql2')->table('course_regs')
        ->where([['session',$s],['user_id',$id],['level_id',$l]])
        ->whereIn('course_id',[$sql2->course_id])
        ->get();
        
        if(count($row) == 0)
        {
          
          $return = 'PROBATION';
        }
        }

        return $return;
      }

}

//============================== oldportal =============================
function get_cgpa_old($s, $std_id){

  $tcu = 0; $tgp = 0;
    
    $row = DB::connection('oldporta')->table('students_results')
    ->join('course_reg', 'course_reg.thecourse_id', '=', 'students_results.stdcourse_id')
    ->where([['students_results.std_mark_custom2','<=',$s],['students_results.std_id',$std_id],])
    ->where([['course_reg.cyearsession','<=',$s],['course_reg.std_id',$std_id]])
    ->get();

    if(count($row) > 0)
    {
    foreach ($row as $key => $value)
    {
      // $cu = $this->get_crunit($value->coursereg_id, $value->session, $user_id);
       $cu =$value->cu;
       $gp = $this->get_gradepoint($value->std_grade, $cu);
       $tcu += $cu;
       $tgp += $gp;
    }
    
        @$gpa = $tgp / $tcu ;
        $gpa = number_format ($gpa,2); 
        return $gpa;
      }
      return 0;
  }
function getFos_old($id)
{
			$sql = DB::connection('oldporta')->table('dept_options')->where('do_id',$id)->first();
			return 	$sql;
}
function new_Probtion_old($l,$s_id,$s,$cgpa){
	$fail_cu=$this->get_fail_crunit_old($l,$s_id,$s);
	$entry_year =$this->get_entry_sesssion_old($s_id);
		 $return='';
    $new_prob =2012;
	if($entry_year->std_custome2 < $new_prob)
	{
	if( $cgpa < 0.75 ){
		$return =  'WITHDRAW';
	}elseif(( $cgpa >= 0.75) && ($cgpa <= 0.99) ){
	$return =	 'PROBATION';
	}
	}else{
		$fos = $this->getFos_old($entry_year->stdcourse); // get fos
		// check if the level id is more than duration of the course
		
		if($l < $fos->duration){
        if($fail_cu > 15|| $cgpa >=0.00 && $cgpa <=0.99 ){
			
		$return = 'WITHDRAW';
		}
		elseif($cgpa >=1.00 && $cgpa <=1.49 || $fail_cu ==15){

			$return = 'PROBATION';

		}elseif( $cgpa > 1.49 && $cgpa <=1.5 && $fail_cu ==15 ){
		$return = 'WITHDRAW OR CHANGE PROGRAMME';
		} 
	}
		}
		return $return;
}


function get_fail_crunit_old($l,$s_id,$s){
  $sql = DB::connection('oldporta')->table('students_results')->where([['level_id',$l],['std_mark_custom2',$s],['std_id',$s_id],
   ['std_grade','F'],['period','NORMAL']])->get();
$tcu =$sql->sum('cu');
return $tcu;
}

function get_entry_sesssion_old($std)
{
		$sql = DB::connection('oldporta')->table('students_profile')->where('std_id',$std)->first();
	 	return 	$sql;
}

//  =========================== get fail course units ==============================================
protected function get_fail_crunit($l,$id,$s,$season)
{
$sql =StudentResult::where([['level_id',$l],['session',$s],['user_id',$id],['grade','F'],['season',$season]])
->get(); 
$tcu=$sql->sum('cu');
return $tcu;
}

public function getprobationStudents1($id,$l,$s)
{
   // get student that did probation
 $s1 = $s-1;
 
 
//$prob_Student_reg = StudentReg::where([['user_id',$id],['session',$s],['level_id',$l]])->first();

$u = DB::connection('mysql2')->table('student_regs')
->where([['user_id',$id],['session',$s1],['level_id',$l]])->count();
if($u != 0){
return true;
}
return '';
}

public function get_school_status($MatricNumber,$Session)
 {
 /*$res = Http::get('https://myunical.edu.ng/verifyfee/GetFeeStatus.ashx',[
  'matricno' =>$MatricNumber,
  'session' => $Session,
]);
//return $res;
  $responseBody = $res->getBody();*/
  if($Session < 2016){
    return 'OK Proceed';
  }else{
  $next =$Session +1;
  $s=$Session.'/'.$next;
 
   $URL = 'https://myunical.edu.ng/verifyfee/GetFeeStatus.ashx?matricno='.$MatricNumber.'&session='.$s;
 $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);
        if ($contents) return $contents;
        else return FALSE;
  }
 // return $response;
     
}

public function getEnableResultUpload($d){
  $r =array();
  $r =EnableResultUpload::where('department_id',$d)->get();
  //if($r->count() > 0){
    return $r;
 
}

//========================specialization code=====================
public function getRegisteredCoursesSpecialization($p, $d, $f, $fos, $sFos, $l, $s, $sm, $sts)
{
   // $reg = DB::table('register_courses')
   $spec=array();
   $r = DB::table('register_courses')
   ->join('register_specializations', 'register_specializations.registercourse_id', '=', 'register_courses.id')
   ->where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['register_courses.fos_id', $fos], ['level_id', $l], ['session', $s], ['semester_id', $sm], ['reg_course_status', $sts],['register_specializations.specialization_id',$sFos]])
   ->orderBy('reg_course_code', 'ASC')->select('register_courses.id')->get();
   if (count($r) > 0) {
      foreach($r as $v)
      {
      $spec[]=$v->id;
      }
    }
    

    $reg =RegisterCourse::where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos], ['level_id', $l], ['session', $s], ['semester_id', $sm], ['reg_course_status', $sts]])
   ->whereIn('id',$spec)
    ->orderBy('reg_course_code', 'ASC')
    ->get();
if (count($reg) > 0) {
    return $reg;
}
return '';

}

public function getRegisteredStudentsSpecialization($p, $d, $f, $fos,$sFos, $l, $s,$perpage)
    {
        // get student that did probation
        $prob_user_id = array();
        $prob_user_id = $this->getprobationStudents($p, $d, $f, $l, $s);

        if($perpage == 0){
            $users = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
              ->where([['users.programme_id',$p],['users.department_id',$d],['users.faculty_id',$f],['specialization_id',$sFos]])
          ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['users.fos_id', $fos], ['student_regs.level_id', $l], ['student_regs.session', $s]])
            ->whereNotIn('users.id', $prob_user_id)
              ->orderBy('users.matric_number', 'ASC')
            ->distinct()
            ->select('users.*')
            ->get();

        }else{
        $users = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
              ->where([['users.programme_id',$p],['users.department_id',$d],['users.faculty_id',$f],['specialization_id',$sFos]])
           ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['users.fos_id', $fos], ['student_regs.level_id', $l], ['student_regs.session', $s]])
            ->whereNotIn('users.id', $prob_user_id)
             ->orderBy('users.matric_number', 'ASC')
            ->distinct()
            ->select('users.*')
            ->paginate($perpage)->withQueryString();
        }

        return $users;
    }


    //=================================== get mop up student====================
    public function getMopUpStudents($p,$d,$f,$l,$s)
    {
       // get student that did probation
     $s1 = $s-1;
     $prob_user_id = array(); $normal=array();
     
   $prob_Student_reg =DB::connection('mysql2')->table('student_regs')
   ->where([['semester',1],['programme_id',$p],['department_id',$d],['faculty_id',$f],['level_id',$l],['session',$s],['moppedUp',1]])->get();
   if(count($prob_Student_reg) > 0){
   foreach ($prob_Student_reg as $key => $value) {
   $normal []=$value->user_id;
   }
    }
    $u =DB::connection('mysql2')->table('student_regs')
    ->where([['session','<=',$s1],['level_id',$l],['semester',1]])
    ->whereIn('user_id',$normal)->get();
    if($u->count() > 0){
     foreach ($u as $key => $v) {   
    $prob_user_id [] = $v->user_id;
   }
   }
   return $prob_user_id;
    }

        // -------------- get register probation students for report ----------------------------------------
        public function getRegisteredMopUpStudentsForReport($p,$d,$f,$fos,$l,$s,$perpage)
        {
            $prob_user_id = $this->getMopUpStudents($p, $d, $f, $l, $s);
            if($perpage == 0){
            $users = DB::connection('mysql2')->table('users')
                ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
                ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['users.fos_id', $fos], ['student_regs.level_id', $l], ['student_regs.session', $s]])
                ->whereIn('users.id', $prob_user_id)
                ->orderBy('users.matric_number', 'ASC')
                ->distinct()
                ->select('users.*')
                ->get();
            }else{
                $users = DB::connection('mysql2')->table('users')
                ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
                ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['users.fos_id', $fos], ['student_regs.level_id', $l], ['student_regs.session', $s]])
                ->whereIn('users.id', $prob_user_id)
                ->orderBy('users.matric_number', 'ASC')
                ->distinct()
                ->select('users.*')
                ->paginate($perpage)->withQueryString();
            }
    
            return $users;
        }
}