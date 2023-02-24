<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
/*use App\Models\PdsResult;
use App\Models\Department;
use App\Models\Programme;
use App\Models\Fos;
use App\Models\Faculty;
use App\Models\CourseReg;
use App\Models\StudentResult;
use App\Models\StudentResultBackup;
use App\Models\RegisterCourse;*/
use App\Models\Fos;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\MyTrait;
class R
{
use MyTrait;
const Agric =18;
//----------------------get result-----------------------------------
 public function getresult($id){
        $result =DB::connection('mysql2')->table('student_results')
                         ->where('coursereg_id',$id)
                         ->first();
                       return $result;
       
   }

   public function getresultWithResultType($id,$ResultType){
    $result =DB::connection('mysql2')->table('student_results')
                     ->where([['coursereg_id',$id],['flag',$ResultType]])
                     ->first();
                   return $result;
   
}
//--------------------------get role name--------------------------------------
   public function getrolename($id){
        $user = DB::table('roles')
            ->join('user_roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id',$id)
            ->first();
            return $user;
    }

    public function getroleId($id){
      $user = DB::table('roles')
          ->join('user_roles', 'roles.id', '=', 'user_roles.role_id')
          ->where('user_roles.user_id',$id)
          ->select('roles.id')
          ->first();
          return $user->id;
  }

  public function getResultActivation($id){
    $user = DB::table('result_activations')
        ->where([['role_id',$id],['status',1]])
        ->first();
        if($user == null)
        {
          return null;
        }else{
        return $user->session;
        }
}

public function getUser($id){
  $user = User::find($id);
  return $user;
}
//===================================PDS=================================================================
//---------------------get pds result------------------------------------//
   public function pds_getresult($id,$mat_no,$course,$semester,$session){
        $result =DB::connection('mysql2')->table('pds_results')
                         ->where([['pdg_user',$id],['matric_number',$mat_no],['course',$course],['semester',$semester],['session',$session]])
                         ->first();
                       return $result;
       
   }
//---------------------------------------------------------------------------------------------
 public   function select_result_display($s_id,$course_id,$semester,$session){

if( empty($course_id) ){
    return array();
  }
$result= array();
$subj_id=array();

      $query =PdsResult::wherein('course',$course_id)->where([['pdg_user',$s_id],['semester',$semester],['session',$session]])->get();
        if(count($query) != 0){
foreach ($query as $key => $value) {
   $subj_id[$value->course] = $value;
}
}

 
     if(!empty($subj_id)){
    if(count($subj_id) != 0){
      
    $keys = array_keys($subj_id);
  }
  }else{
    $keys =array('');
  }

  foreach($course_id as $k=>$v ) {
    

    if( in_array($v, $keys) ) {
     
        $result[] = array('course'=>$v,'grade'=>$subj_id[$v]['grade']);
      
      }else{
 $result[] = array('course'=>$v,'grade'=>'','point'=>'');
      
      }
    
  }
  

      return $result;
      
}
//-----------------------------------------------get course-------------------------------------------------
public function getcourse()
{
  $c =PdsCourse::get();
  foreach ($c as $key => $value) {
    $id[] =$value->id;
  }
return $id;
}
//------------------------------------------------------------------------------
 public   function get_result_point($s_id,$course_id,$semester,$session){
$query =PdsResult::wherein('course',$course_id)->where([['pdg_user',$s_id],['semester',$semester],['session',$session]])->get();
return $query->sum('point');
 }
//--------------------------------------------------------------------------------------------------------
 public function get_course_grade($id,$c,$s,$sm)
 {
     $result =PdsResult::where([['pdg_user',$id],['course',$c],['session',$s],['semester',$sm]])->get();
     return $result;
 }

 //-----------------------------------------------------------------------------------------------------
 public   function get_course_avg($s_id,$course_id,$session){
$query =PdsResult::where([['pdg_user',$s_id],['session',$session],['course',$course_id]])->get();
$sum = $query->sum('total');

$no = Count($query);
if($no == 0)
{
 $avg ='zero';
}else{
$avg = $sum/$no;
}

return $avg;

 }
//==================================================end of pds code======================================
//---------------------------------------------------------------------------------------------------------
 public function get_course_grade_point($total)
 {

  switch($total) {
      case $total =='zero':
               $return['grade']  = '';
               $return['point'] = '';
                 return $return;
                 break;
            case $total >= 70:
                $return['grade'] = 'A';
                $return['point'] = 5;
               return $return;
                break;
            case $total >= 60:
                $return['grade']  = 'B';
                 $return['point'] = 4;
                  return $return;
                break;
            case $total >= 50:
                 $return['grade']  = 'C';
                 $return['point'] = 3;
     return $return;
                break;
            case $total >= 45:
                 $return['grade']  = 'D';
                 $return['point'] = 2;
                  return $return;
                break;
            case $total >= 40:
                 $return['grade']  = 'E';
                 $return['point'] = 1;
                  return $return;
                break;
            case $total < 40:
                 $return['grade']  = 'F';
                $return['point'] = 0;
                 return $return;
                break;
            
        }
    
 }
 //---------------------------------------get department by name----------------------------------------------
public function get_departmetname($id)
{

$d =Department::find($id);
if($d == null)
{
  return "No Department";
}
return $d->department_name;
}
//----------------------------------------get faculty by name--------------------------------------------
public function get_facultymetname($id)
{
$d =Faculty::find($id);
if($d == null)
{
  return "No Faculty";
}
return $d->faculty_name;
}

//========================oldportal=================================

public function get_departmetnameOld($id)
{
  $d = DB::connection('oldporta')->table('departments')->where('departments_id',$id)->first();
  if($d != null){
  return $d->departments_name;
  }
  return null;
}
//----------------------------------------get faculty by name--------------------------------------------
public function get_facultymetnameOld($id)
{
  $d = DB::connection('oldporta')->table('faculties')->where('faculties_id',$id)->first();
  if($d != null){
  return $d->faculties_name;
  }
  return null;
}

//---------------------------------------get programme by name---------------------------------------------
public function get_dept_options($id)
{
$d =DB::connection('oldporta')->table('dept_options')->where('do_id',$id)->first();
if($d == null)
{
  return "No fos";
}
return $d->programme_option;
}
//---------------------------------------get programme by name---------------------------------------------
public function get_programmename($id)
{
$d =Programme::find($id);
if($d == null)
{
  return "No Programme";
}
return $d->programme_name;
}
//-------------------------------------get field of studies--------------------------------------
public function get_fos($id)
{
$d =Fos::find($id);
if($d == null)
{
  return "No Field Of study";
}
return $d->fos_name;
}

//=================  report  ============================================
public function courseRegList($id,$s,$season)
{
  $creglist = array();
  $creg =DB::connection('mysql2')->table('course_regs')->where([['user_id',$id],['session',$s],['period',$season]])->get();
  if(count($creg))
  {
  foreach ($creg as $key => $value) {
    $creglist[] =  $value->course_id;
  }
}
return $creglist;
}

public function courseRegListBySemeter($id,$s,$season,$semester)
{
  $creglist = array();
  $creg =DB::connection('mysql2')->table('course_regs')->where([['user_id',$id],['session',$s],['period',$season],['semester_id',$semester]])->get();
  if(count($creg))
  {
  foreach ($creg as $key => $value) {
    $creglist[] =  $value->course_id;
  }
}
return $creglist;
}

// result with course id 
public function resultWhereInCourseId($id,$course_id,$s,$season)
{
  $s_result =DB::connection('mysql2')->table('student_results')
  ->where([['user_id',$id],['session',$s],['season',$season]])
  ->whereIn('course_id',$course_id)->get();
  return $s_result;
}
 // get result grade
function getStudentResult($course_id,$courseRegList=null,$resultWhereInCourseId=null) {
  
  if( empty($course_id) )
    return array();
   $all = array();
 /* $s_result =DB::connection('mysql2')->table('student_results')
  ->where([['user_id',$id],['session',$s],['season',$season]])
  ->whereIn('course_id',$course_id)->get();*/
 
  //if(count($resultWhereInCourseId) > 0)
  if(count($resultWhereInCourseId) > 0)
  {
    foreach ($resultWhereInCourseId as $key => $value ) {
      $all[$value->course_id] =$value;
    }
    
  }


  $keys = array_keys($all);

  foreach($course_id as $k=>$v ) {

    if( in_array($v, $keys) ) {
      
      if( empty($all[$v]->total) || $all[$v]->total ==0 ) {
        if($all[$v]->approved == 2){
          $result[] = array( 'total'=>$all[$v]->total, 'grade'=>'<span class="B" style="color:red">'.$all[$v]->grade.'</span>');
        }else{
          $result[] = array( 'total'=>$all[$v]->total, 'grade'=>$all[$v]->grade);
        }
        
      } else {
        if($all[$v]->flag == 'Correctional')
        {
          $result[] = array( 'total'=>$all[$v]->total, 'grade'=>'<span class="B" style="color:red">'.$all[$v]->grade.'</span><span class="B"> *</span>');
        }else{
          if($all[$v]->approved == 2){
            $result[] = array( 'total'=>$all[$v]->total, 'grade'=>'<span class="B" style="color:red">'.$all[$v]->grade.'</span>');
          }else{
            $result[] = array( 'total'=>$all[$v]->total, 'grade'=>$all[$v]->grade);
          }
          
        }
        
      }
    } else {
      if( in_array($v, $courseRegList) )
        $result[] = array('total'=>'', 'grade'=>'&nbsp;&nbsp;');
     else
      $result[] = array('total'=>'', 'grade'=>'');
    }
  }

  return $result;
  
}
//------------------------------------get register elective course-----------------------------------------
public function getRegisteredCourseElective($s,$l,$sem,$fos)
{
    $reg_id =array();
   $sql =DB::table('register_courses')
   ->where([['fos_id',$fos],['level_id',$l],['session',$s],['reg_course_status','E'],['semester_id',$sem]])->get();
   if(count($sql) > 0)
   {
      foreach ($sql as $key => $value) {
    $reg_id [$value->id] =['id'=>$value->id,'code'=>$value->reg_course_code];
   }
   }

   return $reg_id;
}
// get elective result
//-----------------------------------get elective course---------------------------------------
public function register_course_elective($fos_id,$l,$s,$sem)
{
  $sql =DB::table('register_courses')
  ->where([['fos_id',$fos_id],['level_id',$l],['session',$s],['reg_course_status','E'],['semester_id',$sem]])
  ->get();
  return $sql;
}
function fetch_electives($id, $s,$l,$sem,$season,$sql) {
 $elec = '';
 $reg_id =array();$reg_course_id=array();
 
 if(count($sql) > 0)
 {
    foreach ($sql as $key => $value) {
  $reg_course_id [$value->course_id] =['course_id'=>$value->course_id,'code'=>$value->reg_course_code];
  $reg_id [] =$value->id;
 }
 

$coursereg =DB::connection('mysql2')->table('student_results')
->where([['user_id',$id],['session',$s],['season',$season],['level_id',$l],['semester',$sem]])
->whereIn('coursereg_id', function($query) use ($id,$l,$s,$sem,$season,$reg_id) {
  $query->select('id')->from('course_regs')->whereIn('registercourse_id',$reg_id)
  ->where([['user_id',$id],['level_id',$l],['semester_id',$sem],['session',$s],['course_status','E'],['period',$season]]);
})->get();

if(count($coursereg) > 0)
  {
  foreach ($coursereg as $key => $value1) {
 //$grade = $this->getSingleResult($id,$s,$l,$sem,$season,$value->course_id);
  if($value1->approved == 2)
  {
    $elec .= $value1->cu.' '.substr_replace($reg_course_id[$value1->course_id]['code'], '',3,0).' <span style="color:red">'.$value1->grade."</span><br/>";
  }else{
    $elec .= $value1->cu.' '.substr_replace($reg_course_id[$value1->course_id]['code'], '',3,0).' '.$value1->grade."<br/>";
  }
    
   
    }
  } 

  $nocoursereg =DB::connection('mysql2')->table('course_regs')->whereIn('registercourse_id',$reg_id)
->where([['user_id',$id],['session',$s],['period',$season],['level_id',$l],['semester_id',$sem],['course_status','E']])
->whereNotIn('id', function($query) use ($id,$l,$s,$sem,$season) {
  $query->select('coursereg_id')->from('student_results')->where([['user_id',$id],['level_id',$l],['semester',$sem],['session',$s],['season',$season]]);
})->get();
if(count($nocoursereg) > 0)
  {
  foreach ($nocoursereg as $key => $value2) {
 $elec .= $value2->course_unit.' '.substr_replace($reg_course_id[$value2->course_id]['code'], '',3,0)."<br/>";
  }
  }
   
}
  return $elec;
}

function fetch_electives2($id, $s,$l,$sem,$season,$sql,$status) {
  $elec = '';
  $reg_id =array();$reg_course_id=array();
  //dd($sql);
  if(count($sql) > 0)
  {
     foreach ($sql as $key => $value) {
   $reg_course_id [$value->course_id] =['course_id'=>$value->course_id,'code'=>$value->reg_course_code];
   $reg_id [] =$value->id;
  }
  
 
 $coursereg =DB::connection('mysql2')->table('student_results')
 ->where([['user_id',$id],['session',$s],['season',$season],['level_id',$l],['semester',$sem]])
 ->whereIn('coursereg_id', function($query) use ($id,$l,$s,$sem,$season,$reg_id,$status) {
   $query->select('id')->from('course_regs')->whereIn('registercourse_id',$reg_id)
   ->where([['user_id',$id],['level_id',$l],['semester_id',$sem],['session',$s],['course_status',$status],['period',$season]]);
 })->get();
 
 if(count($coursereg) > 0)
   {
   foreach ($coursereg as $key => $value1) {
  //$grade = $this->getSingleResult($id,$s,$l,$sem,$season,$value->course_id);
   if($value1->approved == 2)
   {
     $elec .= $value1->cu.' '.substr_replace($reg_course_id[$value1->course_id]['code'], '',3,0).' <span style="color:red">'.$value1->grade."</span><br/>";
   }else{
     $elec .= $value1->cu.' '.substr_replace($reg_course_id[$value1->course_id]['code'], '',3,0).' '.$value1->grade."<br/>";
   }
     
    
     }
   } 
 
   $nocoursereg =DB::connection('mysql2')->table('course_regs')->whereIn('registercourse_id',$reg_id)
 ->where([['user_id',$id],['session',$s],['period',$season],['level_id',$l],['semester_id',$sem],['course_status',$status]])
 ->whereNotIn('id', function($query) use ($id,$l,$s,$sem,$season) {
   $query->select('coursereg_id')->from('student_results')->where([['user_id',$id],['level_id',$l],['semester',$sem],['session',$s],['season',$season]]);
 })->get();
 
 if(count($nocoursereg) > 0)
   {
   foreach ($nocoursereg as $key => $value2) {
  $elec .= $value2->course_unit.' '.substr_replace($reg_course_id[$value2->course_id]['code'], '',3,0)."<br/>";
   }
   }
    
 }
   return $elec;
 }

 public function register_course_elective2($fos_id,$l,$s,$sem,$status)
 {
   $sql =DB::table('register_courses')
   ->where([['fos_id',$fos_id],['level_id',$l],['session',$s],['reg_course_status',$status],['semester_id',$sem]])
   ->get();
   return $sql;
 }

//------------------------------------gpa for a session ---------------------------------


function get_gpa($r){
  
  $tcu = 0; $tgp = 0;  

   
$s_result =$r;
 
  if(count($s_result) > 0)
  {
foreach ($s_result as $key => $value) {
  //$cu = $this->get_crunit($value->course_id, $s, $id,$season);
  $cu =$value->cu;
  $gp = $this->get_gradepoint ($value->grade, $cu );
   
    $tcu = $tcu + $cu;
    $tgp = $tgp + $gp;
   
}
  @$gpa = $tgp / $tcu ;
  $gpa = number_format ($gpa,2);
  return $gpa;
}
return 0;

}
// get course unit
//------------------------------------get course unit-----------------------------------
private function get_crunit ($courseid, $s, $id,$season ) {
  if($season == "VACATION")
  {
    $creg =CourseReg::where([['user_id',$id],['session',$s],['course_id',$courseid]])->whereIn('period',['NORMAL','VACATION'])->first();
  }elseif($season == "RESIT"){
   $creg =CourseReg::where([['user_id',$id],['session',$s],['course_id',$courseid]])->whereIn('period',['NORMAL','RESIT'])->first();
  }else{
    $creg =CourseReg::where([['user_id',$id],['session',$s],['period',$season],['course_id',$courseid]])->first();
  }

  $cu = $creg['course_unit'];
  return $cu;
}
// get grade point
//-------------------------------------get grade point-------------------------------------------
private function get_gradepoint ($grade, $cu){
  
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
// get result
//---------------------------------------------------------------------------------------------------------
private function getResult_grade($id,$s,$l,$season,$course_id_array)
{
    $s_result =DB::connection('mysql2')->table('student_results')
    ->where([['user_id',$id],['session',$s],['season',$season],['level_id',$l]])
  ->whereIn('course_id',$course_id_array)->get();
  return $s_result;
}
public function getCourseWithResult($id,$s,$l,$season)
{
  $courseId=array();
  if($season == 'VACATION')
  {
    $s_result =DB::connection('mysql2')->table('student_results')
    ->where([['user_id',$id],['session',$s],['level_id',$l]])
    ->whereIn('season',['VACATION','NORMAL'])
  ->get();
  }else{
    $s_result =DB::connection('mysql2')->table('student_results')
    ->where([['user_id',$id],['session',$s],['season',$season],['level_id',$l]])
  ->get();
  }
    
  if(count($s_result) > 0){
  foreach($s_result as $v)
  {
    $courseId[]=$v->course_id;
}
  }
  return $courseId;
}

public function getCourseWithResultAll($id)
{
  $courseId=array();

    $s_result =DB::connection('mysql2')->table('student_results')
    ->where('user_id',$id)->get();
  if(count($s_result) > 0){
  foreach($s_result as $v)
  {
    $courseId[]=$v->course_id;
}
  }
  return $courseId;
}
// get result single
//---------------------------------------------------------------------------------------------------------
private function getSingleResult($id,$s,$l,$sem,$season,$course_id)
{
    $s_result =DB::connection('mysql2')->table('student_results')
    ->where([['user_id',$id],['session',$s],['season',$season],['level_id',$l],['course_id',$course_id],['semester',$sem]])->first();
 return $s_result;
}

public function failedResult($id,$s,$l,$taketype)
{
$return='';
$chekvac='';

  if($taketype == 'VACATION')
  {
    $chekvac ='VACATION';
    $taketype =['VACATION','NORMAL'];
  }elseif($taketype =='NORMAL'){
    $taketype =['NORMAL'];
  }else{
    $taketype =['RESIT'];
  }
  $check =DB::connection('mysql2')->table('student_results')
  ->where([['user_id',$id],['session',$s],['level_id',$l],['grade',"F"]])
  ->whereIn('season',$taketype)
  ->get();
  
  if(count($check) != 0)
  {
  
return $return =['number_of_F'=>count($check),'sum_of_F'=>$check->sum('cu')];
   
  }
  
 return  $return =['number_of_F'=> 0,'sum_of_F'=> 0];
}
//------------------------ remarks -----------------------------------------

  function result_check_pass_sessional($l,$id,$s,$cgpa,$taketype='',$fos=null,$f='',$sum_of_F='',$resultObject='',$query='',$totalElective='',
  $passedVacationCourseId='')
{ 
  $fail=array();$carryf ='';$rept=''; $course_id_array =array();$pass_course_id=array();
  
  $new_prob=$this->new_Probtion($l,$id,$s,$cgpa,$fos,$taketype,$sum_of_F);
  if($new_prob==true){
    return $new_prob;
  }
  $chekvac='';
  //if($f == self::Agric && $fos->)

  if($taketype == 'VACATION')
  {
    $chekvac ='VACATION';
    $taketype =['VACATION','NORMAL'];
  }else{
    $taketype =['NORMAL'];
  }
  
  //if($numberOf_F != 0){
 

$sql =DB::connection('mysql2')->table('student_results')
->where([['user_id',$id],['session','<=',$s],['grade',"F"],['level_id','<=',$l]])
->whereIn('season',$taketype)
->whereNotIn('course_id',$passedVacationCourseId)
->select('course_id','cu')->get()->groupBy('course_id','id');


if (count($sql)!=0){ 
  foreach($sql as $key => $value)
  {
    $course_array [$key]=['course_id'=>$key,'number'=>$value->count()];
    $course_id_array []=$key;
  }

  $sql1 = DB::connection('mysql2')->table('student_results')
  ->where([['user_id',$id],['session','<=',$s],['grade','!=',"F"],['level_id','<=',$l]])
  ->whereIn('course_id',$course_id_array)
  ->whereIn('season',$taketype)
  ->get();
  if (count($sql1)!=0){
    foreach ($sql1 as $k => $v)
{
$pass_course_id[]= $v->course_id;
}
   }

$unpass_course_id=array_diff($course_id_array,$pass_course_id);

$exitingResult =$resultObject;
$coursereg_join = CourseReg::where([['user_id',$id],['level_id',$l],['session',$s]])
->whereIn('course_status',['R','D'])
->whereNotIn('course_id',$exitingResult)->select('course_id', 'course_code');
$coursereg = CourseReg::where('user_id',$id)->whereIn('course_id',$unpass_course_id)
->select('course_id', 'course_code')->union($coursereg_join)->distinct()->get();

if(count($coursereg) != 0 ){
foreach($coursereg as $k =>$v)
{
  $code = substr($v->course_code,0,3).''.substr($v->course_code,3,4);
  $type = substr($v->course_code,0,3); 
if(in_array($v->course_id,$course_id_array))
{
  $n = $course_array[$v->course_id]['number'];
}else{
  $n = 1;
}
  
  if ($n >= 3)
  {
    
      if (!in_array($type,['GSS','GST']))
      { 
     // if($this->ignore_carryF ($id, $v->course_id, $s ) == '' && $chekvac == '')
      if($this->ignore_carryF ($id, $v->course_id, $s ) == '')
          {
          $carryf .= ', '.$code;
          }
      } else {
               $rept .= ', '.$code;
              }
  } elseif($n < 3) 
  {
      $rept .= ', '.$code;
  }

}//dd($carryf);
}
}
//}else{
 /* if($l !=1){
   $result =$resultObject;
   
  $coursereg = CourseReg::where([['user_id',$id],['level_id',$l],['session',$s]])
  ->whereIn('course_status',['R','D'])
  ->whereNotIn('course_id',$result)->get();
  
  foreach($coursereg as $k =>$v)
  {
    $code = substr($v->course_code,0,3).' '.substr($v->course_code,3,4);
    $rept .= ', '.$code;
  }
}*/
//}

  $take =  $this->take_courses_sessional($id, $l, $s,$taketype,$resultObject,$query,$totalElective,$fos->id);
  

  if($s == '2018' && $chekvac == 'VACATION' && $f==8)
  {
    $take ='';
  }
  $carryf = $carryf != '' ? '<b>CARRY F :</b>'.substr($carryf,2)."<br>" : '';
  $rept = $rept != '' ? '<b>RPT : </b>'. substr($rept,2) : '';
  $rept = $take != '' ? '<b>TAKE : </b>'. $take ."<br>".$rept : $rept;
 // $dur = $this->G_duration($id);
  $dur = $fos->duration;
  
  if (($l >= $dur) && ($rept == '')) {
    $fail = "PASS <br>".$carryf;
  } else if (($carryf != '') && ($rept != '')) {
    $fail = $carryf . $rept;
  } else if (($carryf != '') && ($rept == '')) {
    $fail = "PASS <br>".$carryf;
  } else if (($carryf != '') || ($rept != '')) {
    $fail = $carryf . $rept;
  } else { $fail = 'PASS' ;}
  
  return $fail;

}




//=======================premedical remarks =======================================

function preMedicalRemarks($l,$id,$s,$cgpa,$take_ignore=false,$resultObject='',$query='',$fos=null)
{ $fail=array();$rept=''; $course_id_array =array();$pass_course_id=array();
  
  if($fos->duration == 5){}else{
 if($cgpa < '2.5')
 {
   return 'Change Of Programme';
 }
  }

  $check =DB::connection('mysql2')->table('student_results')
  ->where([['user_id',$id],['session',$s],['level_id',$l],['grade',"F"]])
  ->select('course_id','cu')->get();
  
if (count($check)!=0){ // found failed courses in the level
  foreach($check as  $value)
  {
   $course_id_array []=$value->course_id;
  }
$coursereg = CourseReg::where('user_id',$id)->whereIn('course_id',$course_id_array)
->select('course_id', 'course_code')->distinct()->get();

if(count($coursereg) != 0){
  $n =0;
foreach($coursereg as $k =>$v)
{
  $code = substr($v->course_code,0,3).' '.substr($v->course_code,3,4);
  $type = substr($v->course_code,0,3); // GSSS

      if (!in_array($type,['GSS','GST']))
      { 
     $rept .= ', '.$code;
     $n ++;
      } else {
      $rept .= ', '.$code;
    }
  } 

  if($n > 2)
  {
    return 'Change Of Programme';
  }

}

}

  $take = $take_ignore == true ? '' : $this->take_courses_sessional($id, $l, $s,'NORMAL',$resultObject,$query,$fos->id);
 
  $rept = $rept != '' ? '<b>RPT : </b>'. substr($rept,2) : '';
  $rept = $take != '' ? '<b>TAKE : </b>'. $take ."<br>".$rept : $rept;

  if ($rept == '') {
    $fail = "PASS <br>";
  } else if ($rept != '') {
    $fail =  $rept;
  }
  return $fail;

}
//======================= clinical remarks ========================================

function clinicalRemarks($l,$id,$s,$season)
{ $fail=array();$rept=''; $coursereg_id_array =array();
  $noResult =DB::connection('mysql2')->table('student_results')
  ->where([['user_id',$id],['session',$s],['level_id',$l],['season',$season]])
  ->get();
  if(count($noResult) == 0 )
  {
    return '<b>NO Result</b>';
  }else{
  // student who have passed  all courses
  $check =DB::connection('mysql2')->table('student_results')
  ->where([['user_id',$id],['session',$s],['level_id',$l],['season',$season]])
  ->whereIn('grade',['P','PD'])
  ->get();

 if (count($check) == 0){ // found failed courses in the level
  $check =DB::connection('mysql2')->table('student_results')
  ->where([['user_id',$id],['session',$s],['level_id',$l],['season',$season]])
  ->select('total')->get();
  $totalFailed =$check->sum('total');
 
  if($l == 3 && $totalFailed < 120)
  {
    return '<b>Change Of Programme</b>';
  }else{
    return 'Repeat the year';
  }
 }
 
 $check2 =DB::connection('mysql2')->table('student_results')
 ->where([['user_id',$id],['session',$s],['level_id',$l],['grade','F'],['season',$season]])
 ->select('coursereg_id')->get();

 if(count($check2) != 0)
 {
   if($season =='VACATION') {
    $rept ='REPEAT THE YEAR FOR THE LAST TIME';
   }else{
   foreach($check2 as $v)
  {
    $coursereg_id_array []=$v->coursereg_id;
  }

  $coursereg = CourseReg::where([['user_id',$id],['level_id',$l],['session',$s],['period',$season]])
  ->whereIn('id',$coursereg_id_array)->get();
  foreach($coursereg as $v)
  {
    $rept .= ' , '.strtoupper($v->course_title);
  }
$rept ='RESIT '.$rept;
   }
 }else{
   $rept ='PASS';
 }
}
return $rept;

}
// =========================  diploma remarks ======================================

 function result_check_pass_sessional_diploma($l, $id, $s,$taketype='',$resultObject='',$query='')
{ $fail=''; $pass='';$c=0;$rept='';
 
  $check =StudentResult::where([['user_id',$id],['session',$s],['level_id',$l]])->get()->COUNT();
  if($check != 0){
 /*$sql_num = StudentResult::where([['user_id',$id],['session','<=',$s],['grade',"F"],['level_id','<=',$l]])->groupBy('course_id','id')->select('course_id','cu')->COUNT('course_id');*/
$sql =StudentResult::where([['user_id',$id],['session',$s],['grade',"F"],['level_id',$l],['flag',"Sessional"],['season',$taketype]])->groupBy('course_id','id')->select('course_id')->distinct()->get();



$c=count($sql);

if($c !=0){
foreach($sql as $key => $value)
{

$rowc = CourseReg::where([['user_id',$id],['course_id',$value->course_id],['level_id',$l],['session',$s]])->first();
if($rowc != null )
{
$code = substr($rowc->course_code,0,3).' '.substr($rowc->course_code,3,4);
$rept .= ', '.$code;

}
}
}

  $take = $this->take_courses_sessional($id, $l, $s, $taketype,$resultObject,$query);

if($taketype == "RESIT")
{
 $rept = $rept != '' ? '<b>CARRY F</b> '. substr($rept,2) : '';
}else
{
   $rept = $rept != '' ? '<b>RESIT</b> '. substr($rept,2) : '';
}
 
  $rept = $take != '' ? '<b>TAKE</b> '. $take ."<br>".$rept : $rept;
 
  
  if($rept == ''){
    $fail = "PASS <br>";
  } else if ($rept != '') {
    $fail = $rept;
  }else { $fail = 'PASS' ;}
  
  return $fail;
}else{
   return $fail;
}
}


//------------------------ entry session----------------------------------------------------
function get_entry_sesssion($id)
{//dd($id);
  $users = DB::connection('mysql2')->table('users')
  ->find($id);
  return  $users->entry_year;
}
//---------------------------------- new probation-------------------------------------------
function new_Probtion($l,$id,$s,$cgpa,$fos,$taketype,$sumoffailedcourses=null){
 // $fail_cu=$this->get_fail_crunit($l,$id,$s,$taketype);
 if($taketype == 'VACATION'){
  $fail_cu=0;
 }else{
  $fail_cu =$sumoffailedcourses;
 }
//get fos duaration
//$duration =Fos::find($fos);
$duration =$fos;
 //$entry_year = $this->get_entry_sesssion($id);

$return ='';
if($l >= $duration->duration)
{

}else{


 if($fail_cu > 15 && $cgpa < 1.5 || $cgpa >=0.00 && $cgpa <=0.99 ){
      
    $return = 'WITHDRAW';
    }
    elseif($cgpa >=1.00 && $cgpa <=1.49 || $fail_cu ==15){

      $return = 'PROBATION';

    }//elseif( $cgpa > 1.49 && $cgpa <=1.5 && $fail_cu ==15 ){
      elseif( $cgpa >=1.5 && $fail_cu > 15 ){
    $return = 'CHANGE PROGRAMME';
    }
  }

    return $return;
}
/*=================================================== probation function ==================================*/

//------------------------ probation remarks -----------------------------------------

  function result_check_pass_probational($l,$id,$s,$cgpa,$fos,$result)
{ $fail=''; $pass='';$c=0;$carryf ='';$rept='';$take='';
  
$new_prob=$this->withdrawer_condition_for_probation($l,$id,$s,$cgpa);

  if($new_prob==true){
    return $new_prob;
  }
  $take =$this->take_courses_sessional($id,$l,$s,'PROBATION',$result); 
  //var_dump($take);
  $check =StudentResult::where([['user_id',$id],['session',$s],['level_id',$l]])->get()->COUNT();

  if($check != 0){

$sql =StudentResult::where([['user_id',$id],['session',$s],['grade',"F"],['level_id',$l]])->groupBy('course_id','id')->select('course_id')->distinct()->get();

$c=count($sql);
//dd($sql);

if($c !=0){
foreach($sql as $key => $value)
{
 
$sql1 = StudentResult::where([['user_id',$id],['session','<=',$s],['grade','!=',"F"],['level_id','<=',$l],['course_id',$value->course_id]])->first();

$sql2 = StudentResult::where([['user_id',$id],['session','<=',$s],['grade',"F"],['level_id','<=',$l],['course_id',$value->course_id]])->get();
    
if ($sql1 != null){ //found that failed course passed in the level

$pass .= ','.$sql1->course_id;


}else{
$rowc = CourseReg::where([['user_id',$id],['course_id',$value->course_id],['level_id','<=',$l],['session','<=',$s]])->first();
if($rowc != null )
{
$code = substr($rowc->course_code,0,3).' '.substr($rowc->course_code,3,4);
            
$type = substr($rowc->course_code,0,3); // GSSS

$n = count($sql2);

          
if ($n >= 3)
{
    if ($type != 'GSS')
    { 
        
        if($this->ignore_carryF ($id, $value->course_id, $s ) == '')
        {
            $carryf .= ', '.$code;
        }
    } else {
             $rept .= ', '.$code;
            }
} elseif($n < 3) 
{
    $rept .= ', '.$code;
}
}
}
}
}//dd('lhhh');
 // $take = $this->take_courses_sessional($id, $l, $s,$taketype,$resultObject,$query);

 // $take = take_courses_sessional($id, $l, $s, $taketype='');
  //$rept = $carryf == $rept? '': $rept;
  
  $carryf = $carryf != '' ? 'CARRY F '.substr($carryf,2)."<br>" : '';
  $rept = $rept != '' ? 'RPT '. substr($rept,2) : '';
  $rept = $rept;
  $dur = $fos->duration;
  $rept = $take != '' ? '<b>TAKE</b> '. $take ."<br>".$rept : $rept;
  if (($l >= $dur) && ($rept == '')) {
    $fail = "PASS <br>".$carryf;
  } else if (($carryf != '') && ($rept != '')) {
    $fail = $carryf . $rept;
  } else if (($carryf != '') && ($rept == '')) {
    $fail = "PASS <br>".$carryf;
  } else if (($carryf != '') || ($rept != '')) {
    $fail = $carryf . $rept;
  } else { $fail = 'PASS' ;}
  
  return $fail;
}else{
   return $fail;
}
}

// condition for withdrawer for probation condition
function withdrawer_condition_for_probation($l,$id,$s,$cgpa){
  $fail_cu=$this->get_fail_crunit($l,$id,$s,'NORMAL');

 //$entry_year = $this->get_entry_sesssion($id);

$return ='';


 if($fail_cu >= 15 || $cgpa < 1.5 ){
      
    $return = 'WITHDRAW OR CHANGE PROGRAMME';
    }
    return $return;
}

/*=================================================== End probation function ==================================*/
function get_fail_crunit($l,$id,$s,$taketype){

$sql =StudentResult::where([['level_id',$l],['user_id',$id],['session',$s],['grade','F'],['season',$taketype]])
->get(); 
$tcu=$sql->sum('cu');
return $tcu;
}

public function ignore_carryF ( $id, $course_id, $s ){
  $sql =CourseReg::where([['user_id',$id],['session',$s],['course_id',$course_id]])->get();
  if(count($sql) == 0)
  {

  return 'true';
  } else { // add this carryF course since it exist in same year
    return '';
  }
 
  
}

public function registerCompulsaryCoursesNotInResultQuery($fos_id,$l,$s)
{
 $sql = DB::table('register_courses')->where([['fos_id',$fos_id],['level_id',$l],['session',$s],['reg_course_status','C']])
  ->get();
  return $sql;
}
public function registerCompulsaryCoursesNotInResultQuerySpecialization($fos_id,$l,$s,$sFos)
{
  $r = DB::table('register_courses')
  ->join('register_specializations', 'register_specializations.registercourse_id', '=', 'register_courses.id')
  ->where([ ['register_courses.fos_id', $fos_id], ['level_id', $l], ['session', $s], ['reg_course_status','C'],['register_specializations.specialization_id',$sFos]])
  ->orderBy('reg_course_code', 'ASC')->select('register_courses.*')->get();
  return $r;
}
public function take_courses_sessional($id,$l,$s,$taketype='',$result=array(),$query='',$totalElective ='',$fos_id=null) 
{
  $take = '';
  $regcos_array = array();
  //$result=array();

/*if(count($result) > 0)
      {*/
      
      if ($taketype == 'RESIT')
      {
        
        //var_dump($result_array);
    $cos =CourseReg::where([['user_id',$id],['level_id',$l],['session',$s],['course_status','R']])
      ->whereNotIn('course_id',$result)->get();

      foreach ($cos as $key => $v) {
      $regcos_array [] =$v->registercourse_id;
      }
    
$sql =DB::table('register_courses')->whereIn('id',$regcos_array)->get();

     // dd($sql);
      }
      elseif($taketype == 'PROBATION')
      {

    $cos =CourseReg::where([['user_id',$id],['level_id',$l],['session',$s]])
      ->whereNotIn('course_id',$result)->get();

      foreach ($cos as $key => $v) {
      $regcos_array [] =$v->registercourse_id;
      }
    
$sql =DB::table('register_courses')->whereIn('id',$regcos_array)->get();

     // dd($sql);
      
      }else
      {
        $sql=$query;
     
      }
    

      if(count($sql) > 0)
      {
        foreach ($sql as $key => $value) {
        
         if(!in_array($value->course_id,$result)){
              $take.= ', '.substr($value->reg_course_code,0,3).' '.substr($value->reg_course_code,3,4);
        }
      }
    
      }

      if($totalElective > 0)
      {
        $cosE =CourseReg::where([['user_id',$id],['level_id',$l],['session',$s],['course_status','E']])
      ->whereNotIn('course_id',$result)
      ->get();
      if(count($cosE) > 0)
      {
        foreach ($cosE as $key => $value) {
          
       
               $take.= ', '.substr($value->course_code,0,3).' '.substr($value->course_code,3,4);
         
       }
      }
      }

      // ==================addition code 18/12/22==============
      // to check for any compulsary courses that the student have not take in previous session
    if($l != 1){
    $srArraySession=array(); $srArrayLevel=array();
    $allResult =$this->getCourseWithResultAll($id);
      $studentReg=StudentReg::where([['user_id',$id],['session','<',$s],['level_id','<',$l]])
      ->select('session','level_id')->distinct()->get();
      if(count($studentReg) > 0)
      {
        foreach($studentReg as $sv){
//$srArraySession[] =$sv->session;
 
      $r = DB::table('register_courses')
       ->where([ ['register_courses.fos_id', $fos_id],['reg_course_status','C'],['level_id',$sv->level_id],['session',$sv->session]])
      ->orderBy('reg_course_code', 'ASC')->select('reg_course_code','course_id')->distinct()->get();
    
      
if(count($r) >0){
      foreach ($r as $key => $value) {
        
        if(!in_array($value->course_id,$allResult)){
             $take.= ', '.substr($value->reg_course_code,0,3).' '.substr($value->reg_course_code,3,4);
       }
     }
}
        }
      }
    }

 
  return $take != '' ? substr($take,2) : '';
  }

  
//---------------------- get duration --------------------------------------------
 public  function G_duration($id){
  $users = DB::connection('mysql2')->table('users')->find($id);
  $fos =Fos::find($users->fos_id);
  return $fos->duration;  
}

//----------------------------------------- auto cgpa ----------------------------------
function auto_cgpa( $s, $id, $l,$season ) {
 $duration = $this->G_duration($id);
  $year_of_study = $l.'/'.$duration;
  $info = $this->get_count_session_used($id);
  if( $l < $duration ) {
   
      return $this->get_cgpa($s,$id,$season); 
  
    
  } elseif( $info == $duration ) {
    //final year std who has had no probation
    return $this->get_cgpa($s,$id,$season);
  } else {
    //helpiing with final year + spill over cgpa calc
    $yr = substr($year_of_study,0,1);
    $calc = $yr - $duration;
    $magic_s = ($calc == 0) ? $s : $s-$calc;
    
   
    return $this->get_cgpa($s,$id,$season);
  }
  
}
//------------------------ cgpa--------------------------------
function get_cgpa($s,$id,$season){

$tcu = 0; $tgp = 0;$coursereg_id =array();

$coursereg =DB::connection('mysql2')->table('course_regs')
->where([['user_id',$id],['session','<=',$s]])->get();
foreach ($coursereg as $key => $value) {
$coursereg_id [] =$value->id;
}

if($season == 'VACATION')
{
    $result = DB::connection('mysql2')->table('student_results')
    ->where([['user_id',$id],['level_id','!=',0],['session','<=',$s]])
      ->whereIn('season',['NORMAL','VACATION'])
      ->whereIn('coursereg_id',$coursereg_id)->get();
}
elseif($season =='RESIT')
{
  $result = DB::connection('mysql2')->table('student_results')
  ->where([['user_id',$id],['level_id','!=',0],['session','<=',$s]])
      ->whereIn('season',['NORMAL','RESIT'])
      ->whereIn('coursereg_id',$coursereg_id)->get();

}
else
{
$result = DB::connection('mysql2')->table('student_results')
->where([['user_id',$id],['level_id','!=',0],['session','<=',$s],['season',$season]])->whereIn('coursereg_id',$coursereg_id)->get();
}

 
if(count($result) > 0)
{
  foreach ($result as $key => $value) {
   
  //$cu = $this->get_crunit($value->course_id,$value->session,$id,$season);
  $cu =$value->cu;
  $gp = $this->get_gradepoint($value->grade,$cu);

    $tcu += $cu;
    $tgp += $gp;
  }

@$gpa = $tgp / $tcu ;
$gpa = number_format ($gpa,2); 
return $gpa;
}
return 0;
}
//------------------- get session used------------------------------------
function get_count_session_used( $id, $l = 6 ) {
  $stdReg =StudentReg::where([['user_id',$id],['level_id','<=',$l]])
  ->whereIn('semester',['1,2'])->distinct()->select('session')->get()->count();

  if( $stdReg > 0)
  {
    return $stdReg;
  }else{
  
    return '';  
  }

}
//-------------------get pin year------------------------------------------------------
public function get_pin_year($user_id,$mat,$year)
{
  $p =Pin::where([['student_id',$user_id],['matric_number',$mat],['session',$year]])->get();
return $p;
}
//---------------------------- get vacation course passed ----------------------
public function passedVacationCourseId($id,$fos,$level,$f,$last_level,$last_session)
{
  $vac =array();
  $passCourseInVacation =array();

  if($f == self::Agric)
  {
  if($fos->duration == 4 && $level >= 3)
  {
    // d/e agric
    $vac =DB::connection('mysql2')->table('student_results')
    ->where([['grade','!=','F'],['student_results.user_id',$id],['season','VACATION'],
    ['student_results.session','<=',$last_session],['student_results.level_id','<=',$last_level]])
    ->get();
    
  }else if($fos->duration == 5 && $level >= 4){
    // ume agric
    $vac =DB::connection('mysql2')->table('student_results')
    ->where([['grade','!=','F'],['student_results.user_id',$id],['season','VACATION'],
    ['student_results.session','<=',$last_session],['student_results.level_id','<=',$last_level]])
    ->get();
  }
  }elseif($fos->duration > $level){
    //non agric
    $vac =DB::connection('mysql2')->table('student_results')
    ->where([['grade','!=','F'],['student_results.user_id',$id],['season','VACATION'],
    ['student_results.session','<=',$last_session],['student_results.level_id','<=',$last_level]])
    ->get();

  }
  if(count($vac) >  0){
    foreach($vac as $vc)
    {
      $passCourseInVacation []=$vc->course_id;
    }
  }

  return $passCourseInVacation;
}
//---------------------------get repeat course-------------------------------------------
public function repeat_course($id,$session,$level,$season,$last_session=null,$passCourseInVacation=null,$f=null,$fos=null)
{
  $return = '';
  $inc = array();
  if($season =='VACATION')
  {
    $last_level =$level;
    $last_session =$session;
    $level =$level + 1;
    $period =['NORMAL'];
  }else{
  $last_level =$level -1;
  if($last_session == null){
  $last_session =$session -1;
  }
  $period =['NORMAL','VACATION'];
  }
  
  $gss_gst =['gss','gst'];
  $array_of_failed_course_id =array();
  $array_of_failed_course_id_with_count =array();
  if($fos->duration == 4 && $level == 4 && $f== self::Agric )
  {
    $last_session1 =$last_session-1;
    $last_level1 =$last_level-1;
    $failed_course  = DB::connection('mysql2')->table('student_results')
    ->join('course_regs', 'student_results.coursereg_id', '=', 'course_regs.id')
    ->whereBetween('student_results.session',[$last_session1,$last_session])
    ->whereBetween('student_results.level_id',[$last_level1,$last_level])
    ->where([['grade','F'],['student_results.user_id',$id]])
   ->whereNotIn('student_results.course_id',$passCourseInVacation)
    ->select('student_results.*', 'course_regs.course_code', 'course_regs.period')
    ->get();
    
  }elseif($fos->duration == 5 && $level == 5 && $f== self::Agric ){
    $last_session1 =$last_session-1;
    $last_level1 =$last_level-1;
    $failed_course  = DB::connection('mysql2')->table('student_results')
    ->join('course_regs', 'student_results.coursereg_id', '=', 'course_regs.id')
    ->whereBetween('student_results.session',[$last_session1,$last_session])
    ->whereBetween('student_results.level_id',[$last_level,$last_level])
    ->where([['grade','F'],['student_results.user_id',$id]])
   ->whereNotIn('student_results.course_id',$passCourseInVacation)
    ->select('student_results.*', 'course_regs.course_code', 'course_regs.period')
    ->get();
  }else{
  $failed_course  = DB::connection('mysql2')->table('student_results')
            ->join('course_regs', 'student_results.coursereg_id', '=', 'course_regs.id')
            ->where([['grade','F'],['student_results.user_id',$id],['student_results.session',$last_session],['student_results.level_id',$last_level]])
           ->whereNotIn('student_results.course_id',$passCourseInVacation)
            ->select('student_results.*', 'course_regs.course_code', 'course_regs.period')
            ->get(); 
  }
  
            if(count($failed_course) > 0)
            {
  foreach($failed_course as $key => $value)
  {
   $array_of_failed_course_id [] =$value->course_id;
  }

$course =DB::connection('mysql2')->table('student_results')->where([['grade','F'],['user_id',$id],['session','<=',$last_session],['level_id','<',$level]])
          ->whereIn('course_id',$array_of_failed_course_id)
          ->get()
          ->groupBy('course_id');
 //dd($course);
        foreach($course as  $key => $value)
        {
           $array_of_failed_course_id_with_count[$key]=array('size'=>count($value));
        }
  foreach ($failed_course as $key => $value) {
  
    $coursenumber =$array_of_failed_course_id_with_count[$value->course_id]['size'];
    
if ( in_array(strtolower(substr($value->course_code,0,3)),$gss_gst) ||   $coursenumber < 3 )
{  
  //  if( $value->session ==  $last_session && in_array($value->period,$period)) { 
    $inc[$value->coursereg_id] = array( 'sizem'=> $coursenumber, 'code'=>$value->course_code, 'std'=>$id,'pero'=>$value->period );
     continue;
   
 // }   
          
    
}
}

   
$return = '';
    
    foreach( $inc as $v ) {
    
      if(in_array(strtolower(substr($v['code'],0,3)),$gss_gst) ){
        $return .=  substr_replace($v['code'],' ',3,0)." F<br/>";
      }
      elseif( $v['sizem'] < 3 ) 
      {
        $return .= $v['sizem'] == 2 ? substr_replace($v['code'],' ',3,0)." F/F<br/>" : substr_replace($v['code'],' ',3,0)." F<br/>";
      }
    }
    $return = substr( $return, 0, -5);
  }

    return strtoupper($return);
}
//----------------------- failed drop courses--------------------------------
public function get_failed_drop_courses($id,$l,$s,$season,$course_status,$semester)
{
  $return =array();
  $course =CourseReg::where([['user_id',$id],['level_id',$l],['session',$s],['period',$season],['course_status',$course_status],['semester_id',$semester]])->get();

  if(count($course) > 0)
  {
  foreach ($course as $key => $value) {
    if($course_status == 'R') // repeat courses
    {
    if($season =='VACATION')
    {
      $result =StudentResult::where([['course_id',$value->course_id],['grade','F'],['session',$s]])->first();
   
    }else{
    $result =StudentResult::where([['course_id',$value->course_id],['grade','F'],['session','<',$s]])->first();
    }
    if($result != null)
    {
      $return [] =$value->course_id;
    }
  }else
  {
    $return [] =$value->course_id;
  }
  }
}
return $return;
}

//------------------failed drop course result---------------------------------------------
function get_failed_drop_course_result($id,$l,$s, $semester,$rpt_list,$carryov_list,$season) {
  //var_dump($rpt_list);
  $to_go = array();
  $return = '';
  if(empty($carryov_list))
  {
    $merger =$rpt_list;
  }
  elseif(empty($rpt_list)){
    $merger =$carryov_list;
  }
  elseif(!empty($carryov_list && $rpt_list)){
  $merger = array_merge($rpt_list, $carryov_list);
}
  
  if( !empty($merger) ) 
  {

  $result =StudentResult::whereIn('course_id',$merger)->where([['user_id',$id],['session',$s],['semester',$semester],['level_id',$l],['season',$season]])->get();
  
  // dd($result);
   if(count($result) > 0)
   {
   foreach ($result as $key => $value) {
    $reg =CourseReg::find($value->coursereg_id);
    
        $return .= "<br/>". $value->cu.' '.substr_replace($reg->course_code," ",3, 0).' '.$value->grade;
      }
      
   }
}
    $return = substr($return, 5);
    echo strtoupper($return);
   // dd($return);
  
   // echo "";
  
}

function getFailedDropCourseResult($id,$l,$s,$semester,$season) {
  //var_dump($rpt_list);
  $to_go = array();
  $return = '';
if($season == 'VACATION')
{
  if(config('app.env') === 'production'){
  $course =DB::connection('mysql2')->table('course_regs')
->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
->where([['course_regs.user_id',$id],['course_regs.level_id',$l],['course_regs.session',$s],['course_regs.period',$season],['course_regs.semester_id',$semester]])
->whereIn('course_regs.course_status',['R','D'])
->whereNotIn('course_regs.course_id', function($query) use ($l,$s,$id) {
  $query->select('course_id')->from('unical8_exams2.course_regs')
  ->where([['level_id',$l],['session',$s],['course_status','C'],['user_id',$id]]);
})
->select('cu','grade','course_code','approved')
->get();
  }else{
$course =DB::connection('mysql2')->table('course_regs')
->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
->where([['course_regs.user_id',$id],['course_regs.level_id',$l],['course_regs.session',$s],['course_regs.period',$season],['course_regs.semester_id',$semester]])
->whereIn('course_regs.course_status',['R','D'])
->whereNotIn('course_regs.course_id', function($query) use ($l,$s) {
  $query->select('course_id')->from('unical_exams2.course_regs')
  ->where([['level_id',$l],['session',$s],['course_status','C']]);
})
->select('cu','grade','course_code','approved')
->get();
  }

}else{
  $course =DB::connection('mysql2')->table('course_regs')
  ->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
  ->where([['course_regs.user_id',$id],['course_regs.level_id',$l],['course_regs.session',$s],['course_regs.period',$season],['course_regs.semester_id',$semester]])
  ->whereIn('course_regs.course_status',['R','D'])
  ->select('cu','grade','course_code','approved')
  ->get();
}


  if(count($course) != 0) 
  {

   foreach ($course as $key => $value) {
  if($value->approved == 2)
  {
    $return .= "<br/>". $value->cu.' '.substr_replace($value->course_code," ",3, 0).' <span style="color:red">'.$value->grade.'</span>';
  }else{
    $return .= "<br/>". $value->cu.' '.substr_replace($value->course_code," ",3, 0).' '.$value->grade;
  }
        
      }
      
   }

    $return = substr($return, 5);
    echo strtoupper($return);
   // dd($return);
  
   // echo "";
  
}


//==================== failedDropCourseResultCorrection ============================================

function getFailedDropCourseResultCorrection($id,$l,$s,$semester,$season) {
  //var_dump($rpt_list);
  $to_go = array();
  $return = '';
if($season == 'VACATION')
{
  if(config('app.env') === 'production'){
  $course =DB::connection('mysql2')->table('course_regs')
->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
->where([['course_regs.user_id',$id],['course_regs.level_id',$l],['course_regs.session',$s],['course_regs.period',$season],['course_regs.semester_id',$semester]])
->whereIn('course_regs.course_status',['R','D'])
->whereNotIn('course_regs.course_id', function($query) use ($l,$s) {
  $query->select('course_id')->from('unical8_exams2.course_regs')
  ->where([['level_id',$l],['session',$s],['course_status','C']]);
})
->select('cu','grade','course_code')
->get();
  }else{
$course =DB::connection('mysql2')->table('course_regs')
->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
->where([['course_regs.user_id',$id],['course_regs.level_id',$l],['course_regs.session',$s],['course_regs.period',$season],['course_regs.semester_id',$semester]])
->whereIn('course_regs.course_status',['R','D'])
->whereNotIn('course_regs.course_id', function($query) use ($l,$s) {
  $query->select('course_id')->from('unical_exams2.course_regs')
  ->where([['level_id',$l],['session',$s],['course_status','C']]);
})
->select('cu','grade','course_code')
->get();
  }

}else{
  $course =DB::connection('mysql2')->table('course_regs')
  ->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
  ->where([['course_regs.user_id',$id],['course_regs.level_id',$l],['course_regs.session',$s],['course_regs.period',$season],['course_regs.semester_id',$semester]])
  ->whereIn('course_regs.course_status',['R','D'])
  ->select('cu','grade','course_code','course_regs.course_id','student_results.user_id')
  ->get();
}


  if(count($course) != 0) 
  {

   foreach ($course as $key => $value) {
    $cor =StudentResultBackup::where([['user_id',$value->user_id],['session',$s],['season',$season],['course_id',$value->course_id]])->first();
    if($cor != null)
    {
        $return .= "<br/>". $value->cu.' '.substr_replace($value->course_code," ",3, 0).' '.$cor->grade;
    }
      }
      
   }

    $return = substr($return, 5);
    echo strtoupper($return);
   // dd($return);
  
   // echo "";
  
}



//======================================= getFailedDropCourseResult Probation========================

function getFailedDropCourseResultProbation($id,$l,$s,$semester,$season,) {
  //var_dump($rpt_list);
  $to_go = array();
  $return = '';
  if(config('app.env') === 'production'){
  $course =DB::connection('mysql2')->table('course_regs')
->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
->where([['course_regs.user_id',$id],['course_regs.level_id',$l],['course_regs.session',$s],['course_regs.period',$season],['course_regs.semester_id',$semester]])
->whereIn('course_regs.course_status',['R','D'])
->whereNotIn('course_regs.course_id', function($query) use ($l,$s,$id) {
  $query->select('course_id')->from('unical8_exams2.course_regs')
  ->where([['level_id',$l],['session','<',$s],['course_status','C'],['user_id',$id]]);
})
->select('cu','grade','course_code')
->get();

  }else{
$course =DB::connection('mysql2')->table('course_regs')
->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
->where([['course_regs.user_id',$id],['course_regs.level_id',$l],['course_regs.session',$s],['course_regs.period',$season],['course_regs.semester_id',$semester]])
->whereIn('course_regs.course_status',['R','D'])
->whereNotIn('course_regs.course_id', function($query) use ($l,$s,$id) {
  $query->select('course_id')->from('unical_exams2.course_regs')
  ->where([['level_id',$l],['session','<',$s],['course_status','C'],['user_id',$id]]);
})
->select('cu','grade','course_code')
->get();
  }

  if(count($course) != 0) 
  {

   foreach ($course as $key => $value) {
  
        $return .= "<br/>". $value->cu.' '.substr_replace($value->course_code," ",3, 0).' '.$value->grade;
      }
      
   }

    $return = substr($return, 5);
    echo strtoupper($return);
   // dd($return);
  
   // echo "";
  
}

//====================================drop courses=====================================
public function get_drop_course($id,$l,$s,$fos,$season=null)
{
  if($season == 'VACATION')
  {
    $last_session =$s;
    $last_level =$l;
  }else{
  $last_session =$s-1;
  $last_level =$l-1;
  }
  $return ='';
  $coursereg_id =array();
  $probationstudent = $this->probationStudent($id,$last_level,'NORMAL');

  if($probationstudent == true)
  {

  }else{


  $course_reg=DB::connection('mysql2')->table('course_regs')->where([['user_id',$id],['level_id',$last_level],['session',$last_session],['course_status','C']])->get();
  if(count($course_reg) > 0)
{
  foreach ($course_reg as $key => $value) {
$coursereg_id [] =$value->registercourse_id;
  }


$reg =DB::table('register_courses')->where([['fos_id',$fos],['level_id',$last_level],['session',$last_session],['reg_course_status','C']])
->whereNotIn('id',$coursereg_id)
->orderBy('reg_course_status','ASC')
->get();

if(count($reg) > 0)
{
  foreach ($reg as $key => $value) {

$return .=substr_replace($value->reg_course_code," ",3, 0).'<br/>';
  }
}
}
  }
echo strtoupper($return);
}

  
public function getDropCourse($id,$l,$last_session,$fos,$season=null,$f=null)
{
  
  if($season == 'VACATION')
  {

    $last_level =$l;
  }else{
  //$last_session =$s-1;
  $last_level =$l-1;
  }
  $return ='';
  $probationstudent = $this->probationStudent($id,$last_level,'NORMAL');
 if($probationstudent == true)
  {}else{
    if(config('app.env') === 'production'){
    if($fos->duration == 5 && $l==5 && $f==self::Agric)
{
  $last_level =$l-2;
  $last_session =$last_session-1;
  $reg =DB::table('register_courses')->where([['fos_id',$fos->id],['level_id',$last_level],['session',$last_session],['reg_course_status','C']])
  ->whereNotIn('id', function($query) use ($id,$last_level,$last_session) {
     $query->select('registercourse_id')->from('unical8_exams2.course_regs')
     ->where([['user_id',$id],['level_id',$last_level],['session',$last_session],['course_status','C']]);
 })->get();
}elseif($fos->duration == 4 && $l==4 && $f==self::Agric){
  $last_level =$l-2;
  $last_session =$last_session-1;
  $reg =DB::table('register_courses')->where([['fos_id',$fos->id],['level_id',$last_level],['session',$last_session],['reg_course_status','C']])
      ->whereNotIn('id', function($query) use ($id,$last_level,$last_session) {
         $query->select('registercourse_id')->from('unical8_exams2.course_regs')
         ->where([['user_id',$id],['level_id',$last_level],['session',$last_session],['course_status','C']]);
     })->get();

}else{

 $reg =DB::table('register_courses')->where([['fos_id',$fos->id],['level_id',$last_level],['session',$last_session],['reg_course_status','C']])
      ->whereNotIn('id', function($query) use ($id,$last_level,$last_session) {
         $query->select('registercourse_id')->from('unical8_exams2.course_regs')
         ->where([['user_id',$id],['level_id',$last_level],['session',$last_session],['course_status','C']]);
     })->get();
    }
    }
    else{ 
      // for agric final drop courses local host
    //  $last_level =$l-2;
  //$last_session =$last_session-1;
    $reg =DB::table('register_courses')->where([['fos_id',$fos->id],['level_id',$last_level],['session',$last_session],['reg_course_status','C']])
 ->whereNotIn('id', function($query) use ($id,$last_level,$last_session) {
		$query->select('registercourse_id')->from('unical_exams2.course_regs')
    ->where([['user_id',$id],['level_id',$last_level],['session',$last_session],['course_status','C']]);
})->get();
    }
if(count($reg) > 0)
{
  foreach ($reg as $key => $value) {

$return .=substr_replace($value->reg_course_code," ",3, 0).'<br/>';
  }
}
}
  
echo strtoupper($return);
}

//===============================probation drop course =========================

public function get_drop_course_probation($id,$l,$s,$query)
{
 
  $last_session =$s-1;
  $last_level =$l;
  
  $return ='';
  $coursereg_id =array();
 
  $course_reg=DB::connection('mysql2')->table('course_regs')->where([['user_id',$id],['level_id',$last_level],['session',$last_session],['course_status','C']])->get();
  if(count($course_reg) > 0)
{
  foreach ($course_reg as $key => $value) {
$coursereg_id [] =$value->registercourse_id;
  }
}

$reg =$query;

if(count($reg) > 0)
{
  foreach ($reg as $key => $value) {
if(!in_array($value->id,$coursereg_id))
$return .=substr_replace($value->reg_course_code," ",3, 0).'<br/>';
  }
}
  
echo strtoupper($return);
}

//=================================summmer vation function===========================

public function repeat_summer_course($id,$session,$level)
{
  $return = '';
  $inc = array();
  $last_level =$level;
    $last_session =$session;
    $level =$level + 1;
    $period =['NORMAL'];
 
  $gss_gst =['gss','gst'];
  $array_of_failed_course_id =array();
  $array_of_failed_course_id_with_count =array();
  $failed_course  = DB::connection('mysql2')->table('student_results')
            ->join('course_regs', 'student_results.coursereg_id', '=', 'course_regs.id')
            ->where([['grade','F'],['student_results.user_id',$id],['student_results.session',$last_session],['student_results.level_id',$last_level]])
            ->whereIn('student_results.season',$period)
            ->select('student_results.*', 'course_regs.course_code', 'course_regs.period')
            ->get();
           
            if(count($failed_course) > 0)
            {
  foreach($failed_course as $key => $value)
  {
   $array_of_failed_course_id [] =$value->course_id;
  }

$course =DB::connection('mysql2')->table('student_results')->where([['grade','F'],['user_id',$id],['session','<=',$last_session],['level_id','<',$level]])
          ->whereIn('course_id',$array_of_failed_course_id)
          ->whereIn('student_results.season',$period)
          ->get()
          ->groupBy('course_id');
  
        foreach($course as  $key => $value)
        {
           $array_of_failed_course_id_with_count[$key]=array('size'=>count($value));
        }
  foreach ($failed_course as $key => $value) {
  
    $coursenumber =$array_of_failed_course_id_with_count[$value->course_id]['size'];
    
if ( in_array(strtolower(substr($value->course_code,0,3)),$gss_gst) ||   $coursenumber < 3 )
{  
    if( $value->session ==  $last_session && in_array($value->period,$period)) { 
    $inc[$value->coursereg_id] = array( 'sizem'=> $coursenumber, 'code'=>$value->course_code, 'std'=>$id,'pero'=>$value->period );
     continue;
   
  }   
          
    
}
}

   
$return = '';
    
    foreach( $inc as $v ) {
    
      if(in_array(strtolower(substr($v['code'],0,3)),$gss_gst) ){
        $return .=  substr_replace($v['code'],' ',3,0)." F<br/>";
      }
      elseif( $v['sizem'] < 3 ) 
      {
        $return .= $v['sizem'] == 2 ? substr_replace($v['code'],' ',3,0)." F/F<br/>" : substr_replace($v['code'],' ',3,0)." F<br/>";
      }
    }
    $return = substr( $return, 0, -5);
  }

    return strtoupper($return);
}

/*==================== correctional result function========================================*/
 // get result grade
 function getStudentResultCorrection($course_id,$courseRegList=null,$resultWhereInCourseId=null) {
  
  if( empty($course_id) )
    return array();
   $all = array();
  $s_result =$resultWhereInCourseId;//StudentResult::where([['user_id',$id],['session',$s],['season',$season]])
  //->whereIn('course_id',$course_id)->get();
 
  if(count($s_result) > 0)
  {
    
    foreach ($s_result as $key => $value ) {
$cor =StudentResultBackup::where([['user_id',$value->user_id],['session',$value->session],['season',$value->season],['course_id',$value->course_id]])->first();
 if($cor != null)
 {
  $all[$value->course_id] =$cor;
 }else
 {
  $all[$value->course_id] =$value;
 }

    }
    
  }

  /*coursereg*/
  /*  $creglist = array();
      $creg =CourseReg::where([['user_id',$id],['session',$s],['period',$season]])->get();
      if(count($creg))
      {
      foreach ($creg as $key => $value) {
        $creglist[] =  $value->course_id;
      }
    }*/
/*coursereg*/
  $keys = array_keys($all);

  foreach($course_id as $k=>$v ) {

    if( in_array($v, $keys) ) {
      if( empty($all[$v]->total) || $all[$v]->total==0 ) {
        $result[] = array( 'total'=>$all[$v]->total, 'grade'=>$all[$v]->grade );
      } else {
        $result[] = array( 'total'=>$all[$v]->total, 'grade'=>$all[$v]->grade);
      }
    } else {
      if( in_array($v, $courseRegList) )
        $result[] = array('total'=>'', 'grade'=>'&nbsp;&nbsp;');
      else
        $result[] = array('total'=>'', 'grade'=>'');
    }
  }

  return $result;
  
}

//---------------------- gpa correctional ----------------------------------------------------------------
function get_gpa_correctional($resultWhereInCourseId){
  
  $tcu = 0; $tgp = 0;  $course_id = array();
  //$entry_year = $this->get_entry_sesssion($id);
  
  //, level_id, std_mark_custom_2, period
 /*  $creg =CourseReg::where([['user_id',$id],['session',$s],['period',$season]])->get();
   foreach ($creg as $key => $value) {
     $course_id[] =$value->course_id;
   }*/
$s_result = $this->getResult_grade_correction($resultWhereInCourseId);
 
  if(count($s_result) > 0)
  {
foreach ($s_result as $key => $value) {
  $cu = $value->cu;//$this->get_crunit($value->course_id, $s, $id,$season);
  $gp = $this->get_gradepoint ($value->grade, $cu);
   
    $tcu = $tcu + $cu;
    $tgp = $tgp + $gp;
   
}
  @$gpa = $tgp / $tcu ;
  $gpa = number_format ($gpa,2);
  return $gpa;
}
return 0;

}

// ------------------get result grade correction-------------------------------
private function getResult_grade_correction($s_result)
{$result =array();
  /*  $s_result =StudentResult::where([['user_id',$id],['session',$s],['season',$season],['level_id',$l]])
  ->whereIn('course_id',$course_id_array)->get();*/
  foreach($s_result as $v)
  {
    $srb =StudentResultBackup::where([['user_id',$v->user_id],['session',$v->session],['season',$v->season],
    ['level_id',$v->level_id],['course_id',$v->course_id]])
  ->first();
  if($srb != null)
  {
    $result[]=$srb;
  }else{
    $result[]=$v;
  }
  }
  
  return $result;
}

//------------------------ remarks  correction-----------------------------------------


public function remarks_correctional($l,$id,$s,$season, $cgpa, $taketype = false,$resultObject=null,$query=null,$totalElective=null){

$prob = $this->new_Probtion_correctional($l,$id,$s,$season,$cgpa);

if($prob==true){
  
return $prob;
}
$return = '';
$carryf = '';
$take ='';

$take = $this->take_courses_sessional($id, $l, $s, $taketype,$resultObject,$query,$totalElective);

//$repeat = get_repeat_courses_reloaded_corr($l, $s, $s_id, $d, $fos);
$repeat1 = $this->repeatCourseCorrection($id,$s,$l,$season);

if( !empty( $repeat1 ) ) 
{
  
  foreach($repeat1 as $rep1)
  {
    
    if( $rep1['number'] == 3 )
    {
      
      $carryf .= substr_replace($rep1['code1'],' ',3, 0).',';
    }
    elseif($rep1['number'] < 3)
    {
      $return .= substr_replace($rep1['code1'],' ',3 ,0).',';
    }
  }
  
  $carryf = empty($carryf) ? '' : '<b> CARRY F : </b>'.$carryf." <br/>";
  $return = empty($return) ? '' : '<b> RPT : </b>'.$return;
  //var_dump($return);
  
}

  if(!empty($return) || !empty($carryf))
  {
    $return = $carryf.$return;
  
  }

  if(empty($take) && !empty($carryf) &&  empty($return)) 
  {
    //echo "here1";		
    return $cgpa > 0.99 ? "PASS </br> ".$carryf : '';
  }
  else if(empty($take) && $return=='') 
  {
    //echo "here2";
    return $cgpa > 0.99 ? "PASS": '';
  }
$return .= $take != '' ? '<br>TAKE '. $take :'';

  //$return .= $take != '' ? 'TAKE '. $take ."<br>".$return :'';
return strtoupper($return);


}

public function repeatCourseCorrection($id,$s,$l,$season)
{
$courseRegCourseId =array();
$courseRegCourseIdAndCode =array();
$course_array =array();
$courseReg =CourseReg::where([['user_id',$id],['session',$s],['level_id',$l]])
->whereIn('period',[$season])->select('course_id','course_code')->get();
foreach($courseReg as $v)
{
  $courseRegCourseIdAndCode[$v->course_id] =['course_id'=>$v->course_id,'code'=>$v->course_code];
  $courseRegCourseId []=$v->course_id;
}


$check =StudentResult::where([['user_id',$id],['session',$s],['level_id',$l],['grade','!=',"F"]])
->whereIn('season',[$season])->select('course_id')->get();
 
$sql =StudentResult::where([['user_id',$id],['session','<=',$s],['grade',"F"],['level_id','<=',$l]])
->whereNotIn('course_id',$check)->whereIn('course_id',$courseRegCourseId)->whereIn('season',[$season])
->select('course_id','cu');

$sql2 =StudentResultBackup::where([['user_id',$id],['session',$s],['grade',"F"],['level_id',$l]])
->whereIn('season',[$season])->union($sql)->select('course_id','cu')->get()->groupBy('course_id','id');


if (count($sql2)!=0){ // found failed courses in the level
  foreach($sql2 as $key => $value)
  {
    $course_array []=['course_id'=>$key,'number'=>$value->count(),'code1'=>$courseRegCourseIdAndCode[$key]['code']];
   
   //var_dump($course_array);
  }

}

return $course_array;
}
//-------------------------------- probation for correctional result -------------
function new_Probtion_correctional($l,$id,$s,$season,$cgpa){

  $fail_cu=$this->fail_course_unit_correction($l,$id,$s,$season);
  $return ='';
 
		if($fail_cu > 15 && $cgpa < 1.5 || $cgpa >=0.00 && $cgpa <=0.99 ){
      $return = 'WITHDRAW';
      }
      elseif($cgpa >=1.00 && $cgpa <=1.49 || $fail_cu ==15){
     $return = 'PROBATION';
     }elseif( $cgpa >=1.5 && $fail_cu > 15 ){
      $return = 'CHANGE PROGRAMME';
      }
	
		return $return;
}

private function fail_course_unit_correction($l,$id,$s,$season){
   $tcu =''; $tcu1 ='';
   if($season == 'NORMAL')
   {
    $season =['NORMAL'];
   }else{
     $season =['NORMAL','VACATION'];
   }
$sql =StudentResult::where([['user_id',$id],['session',$s],['level_id',$l],['grade','F']])
->whereIn('season',$season)->get();
$tcu=$sql->sum('cu');
$sql =StudentResultBackup::where([['user_id',$id],['session',$s],['level_id',$l],['grade','F']])
->whereIn('season',$season)->get();
$tcu1=$sql->sum('cu');
$c =$tcu + $tcu1;
 return $c;
 }
 public function getCorrectionName($id){
  $u = DB::connection('mysql2')->table('correction_names')
  ->where('user_id',$id)->first();
return $u;
}
 public function G_degree( $cpga, $ignore = false ) 
 {
   
   if( $ignore )
     return '';
     
   switch( $cpga ){
     case $cpga <= 1.49 && $cpga >= 1.00 :
       return 'PASS';
     break;
     case $cpga <= 2.39 && $cpga >= 1.50 :
       return 'THIRD CLASS';
     break;
     case $cpga <= 3.49 && $cpga >= 2.40 :
       return 'SECOND CLASS LOWER';
     break;
     case $cpga <= 4.49 && $cpga >= 3.50 :
       return 'SECOND CLASS UPPER';
     break;
     case $cpga <= 5.00 && $cpga >= 4.50:
       return 'FIRST CLASS';
     break;
     default:
       return '---';
     break;
   }
   
 }

 public function D_degree( $cpga, $ignore = false ) 
 {
   
   if( $ignore )
     return '';
     
   switch( $cpga ){
     case $cpga <= 2.39 && $cpga >= 1.00 :
       return 'PASS';
     break;
     case $cpga <= 3.49 && $cpga >= 2.40 :
       return 'MERIT';
     break;
     case $cpga <= 4.49 && $cpga >= 3.50 :
       return 'CREDIT';
     break;
     case $cpga <= 5.00 && $cpga >= 4.50 :
       return 'DISTINCTION';
    
     break;
     default:
       return '---';
     break;
   }
   
 }

 public function state($id){
  $s =State::find($id);
  return $s;
 }
 public function isMopUpStudent($id)
 {
  $sr=StudentReg::where([['moppedUp',1],['user_id',$id]])->first();
  return $sr;
 }
     
}	

