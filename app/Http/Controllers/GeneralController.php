<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Pin;
use App\Models\StudentResult;
use App\Models\CorrectionName;
use App\Models\StudentResultBackup;
use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Hash;
use App\Http\Traits\MyTrait;
use App\Models\EnableResultUpload;
use App\Models\ResultActivation;

class GeneralController extends Controller
{
    use MyTrait;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit_matric_number($id)
    {
    $user =DB::connection('mysql2')->table('users')->find($id);
   

    if($user == null)
    {
        Session::flash('warning',"Students does not exist.");
    
    }
    return view('general.edit.matric_number')->with('u',$user);
    }

    public function post_edit_matric_number(Request $request)
    {
        $old_matric_number = $request->input('old_matric_number');
        $new_matric_number = $request->input('matric_number');
        $id = $request->input('id');
        
       
        if($new_matric_number != $old_matric_number)
        {
        $this->validate($request,array('matric_number' => 'bail|required|unique:mysql2.users',));
        }
        $user = User::on('mysql2')->find($id);
        $old_matric_number  = $user->matric_number;
        $user->matric_number = $new_matric_number;
        $user->password = bcrypt($new_matric_number);
        $user->save();
        
        // ==== updated result table
        StudentResult::where('user_id',$id)
        ->update(['matric_number' => $new_matric_number]);

        // ==== updated backup result table
        StudentResultBackup::where('user_id',$id)
        ->update(['matric_number' => $new_matric_number]);
        
        // ==== pin
        Pin::where([['student_id',$id],['matric_number',$old_matric_number]])
        ->update(['matric_number' => $new_matric_number]);
        
        
        
           Session::flash('success',"SUCCESSFUL");
           return back();
    }

    public function updateMatricNo(Request $request)
    {
$id =$request->oldmat;
$variable =$request->mat;
$clean_list =array();

foreach ($id as $value) {
    if (!empty($variable[$value])) {
    $user = User::on('mysql2')->where('matric_number',$variable[$value])->first();
    if($user == null){
     $clean_list[] = array('new'  => $variable[$value], 'old'=>$value);
    }
    }
}

if(count($clean_list) > 0)
{
foreach($clean_list as $v)
{
    $old = $v['old'];
    $new= $v['new'];
 User::on('mysql2')->where('matric_number',$old)->update(['matric_number' => $new,'password'=>bcrypt($new)]);
    // ==== updated result table
    StudentResult::where('matric_number',$old)->update(['matric_number' => $new]);

    // ==== updated backup result table
    StudentResultBackup::where('matric_number',$old)->update(['matric_number' => $new]);
    
    // ==== pin
   Pin::where('matric_number',$old)->update(['matric_number' => $new]);
  // echo $new.'<br/>';
      
}
//dd();
Session::flash('success',"SUCCESSFUL");
           return back();
}
Session::flash('warning',"No records is updated, either you did not enter matric number to be updated  
or matric number exist already.contact system ADMIN");
           return back();
    }

    public function edit_jamb_reg($id)
    {
    $user =DB::connection('mysql2')->table('users')->find($id);
   

    if($user == null)
    {
        Session::flash('warning',"Students does not exist.");
    
    }
    return view('general.edit.jamb_reg')->with('u',$user);
    }

    public function post_edit_jamb_reg(Request $request)
    {
        $old_jamb_reg = $request->input('old_jamb_reg');
        $new_jamb_reg = $request->input('jamb_reg');
        $id = $request->input('id');
        
       
        if($new_jamb_reg != $old_jamb_reg)
        {
        $this->validate($request,array('jamb_reg' => 'bail|required|unique:mysql2.users',));
        }
        $user = User::on('mysql2')->find($id);
        $old_jamb_reg  = $user->jamb_reg;
        $user->jamb_reg = $new_jamb_reg;
        $user->password = bcrypt($user->matric_number);
        $user->save();
        Session::flash('success',"SUCCESSFUL");
        return back();
    }




    public function edit_profile($id)
    {
    $user = User::on('mysql2')->find($id);
    return view('general.edit.profile')->with('u',$user);
    }

    public function post_edit_profile(Request $request)
    {
        $id = $request->input('id');
        $this->validate($request,array('email' => 'required|unique:mysql2.users,email,'.$id,));

        
        $surname = $request->input('surname');
        $firstname = $request->input('firstname');
        $othername = $request->input('othername');
        $phone= $request->input('phone');
        $email = $request->input('email');

        $user = User::on('mysql2')->find($id);
      
        $user->surname = strtoupper($surname);
        $user->firstname = strtoupper($firstname);
        $user->othername = strtoupper($othername);
        $user->phone = $phone;
        $user->email = strtolower($email);
        $user->gender= $request->gender;
        $user->save();
        Session::flash('success',"SUCCESSFUL");
        return back();
    }

    public function updatedepartment(Request $request)
    {
      
      $u =DB::connection('mysql2')->table('users')
            ->where('id', $request->user_id)
            ->update(['faculty_id' =>$request->faculty_id,'department_id' => $request->department_id,'fos_id' => $request->fos_id]);
            Session::flash('success',"SUCCESSFUL");
            return back();
    }

    public function resetStudentPassword($id)
    {
    $user = User::on('mysql2')->find($id);
    $user->password =Hash::make($user->matric_number);
    $user->save();
    Session::flash('success',"SUCCESSFUL");
    return back();
   // return view('general.edit.profile')->with('u',$user);
    }

public function uploadRightLog()
{
    $url = DB::table('result_upload_right_log')->orderBy('id','desc')->paginate(100);
    //dd($url);
    return view('general.logs.uploadrightlog')->with('u',$url);
}
 
public function regLog(){
    $url = DB::connection('mysql2')->table('student_regs')->where('semester',2)->whereNotNull('deskofficer')->orderBy('id','desc')->get(); 
$g =$url->groupBy('deskofficer');
return view('general.logs.reglog')->with('u',$g);
}


public function correctionName($id){
    $user = User::on('mysql2')->find($id);
    return view('general.edit.correctionName')->with('u',$user);
}
public function updateCorrectionName(Request $request){
    $id = $request->input('id');
    $surname = $request->input('surname');
    $firstname = $request->input('firstname');
    $othername = $request->input('othername');
    $user = User::on('mysql2')->find($id);
    $cn =New CorrectionName;
    $cn->surname = $user->surname;
    $cn->firstname =$user->firstname;
    $cn->othername = $user->othername;
    $cn->user_id = $user->id;
    $cn->matric_number = $user->matric_number;
    $cn->save();

    $user->surname = strtoupper($surname);
    $user->firstname = strtoupper($firstname);
    $user->othername = strtoupper($othername);
    $user->save();
    Session::flash('success',"SUCCESSFUL");
    return back();
}

public function studentsInformation()
{
   $p = $this->getp();
    $fc = Faculty::orderBy('faculty_name', 'ASC')->get();
    return view('general.studentsInformation.index')->with('fc',$fc)->with('p',$p);
}
public function postStudentsInformation(Request $request)
{
    $dd = Department::orderBy('department_name', 'ASC')->get();
    $s = $request->session;
    $d = $request->department;
    $pg =$request->programme;
    if($d== null){
        $d=Auth::user()->department_id;
    }
    $l = $request->level;
    $r =$request->registerCourse;
    $p =$request->print;
    $ers=$request->ers;
    $program = $this->getp();
    $department=Department::find($d);
    $faculty=Faculty::find($department->faculty_id);
    
    
 /*   if($ers == 'ers')
{
return Excel::download(new ErsMultiSheetExport($request->all(),$department->department_name,$faculty->faculty_name),'ers.xlsx');

}*/
    if($r == 1){
    $vId =array();
    $regDetail =array();
$users = DB::connection('mysql2')->table('users')
->join('student_regs', 'users.id', '=', 'student_regs.user_id')
->where([['users.department_id',$d],['users.programme_id',$pg],['users.entry_year',$s]])
->where([['student_regs.semester',1],['level_id',$l]])
->get();
$gusers =$users->groupBy('fos_id');

return view('general.studentsInformation.view')->with('u',$gusers)->with('d',$d)->with('l',$l)->with('f',$department->faculty_id);
}/*elseif($p =='PDF'){
    
$vId =array();
$regDetail =array();
$regCourse =DB::table('register_courses')->where([['level_id',$l],['session',$s]])->get();
if(count($regCourse) == 0)
{
Session::flash('warning', "No registered Course for these department in the session you selected");
return back();
}

foreach($regCourse as $v)
{
$vId[] =$v->id;
$regDetail[$v->id] =
['title'=>$v->reg_course_title,'code'=>$v->reg_course_code,'unit'=>$v->reg_course_unit,'status'=>$v->reg_course_status];

}
$courseReg = DB::connection('mysql2')->table('users')
->join('course_regs', 'users.id', '=', 'course_regs.user_id')
->where([['users.fos_id', $fos], ['users.department_id', $d], ['course_regs.session', $s], ['course_regs.semester_id', $semester], ['course_regs.level_id', $l]])
->whereIn('registercourse_id',$vId)
->select('course_regs.*', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number','users.image_url')
->orderBy('users.surname','ASC')
->get();
$gcourseReg =$courseReg->groupBy('registercourse_id');

$data = ['u'=>$gcourseReg,'d'=>$d,'l'=>$l,'s'=>$s,'sm'=>$semester,'fos'=>$fos,'rd'=>$regDetail];
//dd($data);
$pdf = PDF::loadview('admin.admin_courseRegStudents3',$data);
return $pdf->setPaper('a4', 'landscape')->stream('admin.admin_courseRegStudents3.pdf');
  
    }else{

    $users = DB::connection('mysql2')->table('users')
        ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
        ->where([['users.fos_id', $fos], ['student_regs.department_id', $d], ['student_regs.session', $s], ['student_regs.semester', $semester], ['student_regs.level_id', $l]])
        ->select('student_regs.*', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number')
        ->orderBy('users.surname','ASC')
        ->get();
        if($p == 2)
        {
           
        return view('admin.admin_courseRegStudents2')->with('item',$users)->with('d',$d)->with('l',$l)->with('s',$s)->with('sm',$semester)->with('fos',$fos);
        }
      
    return view('admin.admin_courseRegStudents')->with('u',$users)->with('d',$dd)->with('l',$l)->with('s',$s)->with('sm',$semester)->with('p',$program);
    }*/
}
public function enableResultDepartment()
{
    $fc = Faculty::orderBy('faculty_name', 'ASC')->get();
return view('general.enableResultDepartment.index')->with('fc',$fc);
}
public function postEnableResultDepartment(Request $request)
{
    $fc = Faculty::orderBy('faculty_name', 'ASC')->get();
    $ra =ResultActivation::where('status',1)->first();
    $dd = Department::where('faculty_id',$request->faculty)->orderBy('department_name', 'ASC')->get();
    return view('general.enableResultDepartment.index')->with('fc',$fc)->with('ra',$ra)->with('d',$dd);
}

public function updateEnableResultDepartment(Request $request)
{
    $session=$request->session;
    $id =$request->id;
    $data =array();

    if($id==null){
        Session::flash('warning',"please select Department");
        return back(); 
    }
    if($session==null){
        Session::flash('warning',"please select session");
        return back(); 
    }

    foreach($session as $s){
        foreach($id as $v){
        $check=EnableResultUpload::where([['department_id',$v],['session',$s]])->first();
        if($check == null){
        $data[]=['department_id'=>$v,'session'=>$s];
        }
        }
    }

if(count($data) >0){
    DB::table('enable_result_uploads')->insert($data);
    Session::flash('success',"Successful");
        return back(); 
}else{
    Session::flash('warning',"Department is already active");
        return back(); 
}
   
  
}

public function viewEnableResultDepartment()
{
$g=EnableResultUpload::get()->groupBy('department_id');
$d=EnableResultUpload::select('department_id')->distinct()->get();
$department=Department::whereIn('id',$d)->get();
$data =array();

foreach($department as $v)
{
$data[$v->id] = ['name'=>$v->department_name];
}
return view('general.enableResultDepartment.view')->with('g',$g)->with('d',$data);
}
public function reverseEnableResultDepartment(Request $request)
{
$del =EnableResultUpload::whereIn('department_id',$request->id)->delete();
Session::flash('success',"Successful");
return back(); 
}
//==================================enable old result upload
public function enableResultDepartmentOld()
{
    $f = DB::connection('oldporta')->table('faculties')->orderBy('faculties_name', 'ASC')->get();
    return view('general.enableResultDepartmentOld.index')->with('fc',$f);
  
}

public function updateEnableResultDepartmentOld(Request $request)
{
  
    $id =$request->id;
   

    if($id==null){
        Session::flash('warning',"please select Department");
        return back(); 
    }
 
$data=array();

        $check=DB::connection('oldporta')->table('enable_result_uploads')->where('department_id',$id)->first();

        if($check == null){
        $data[]=['department_id'=>$id];
        }
 
if(count($data) >0){
    DB::connection('oldporta')->table('enable_result_uploads')->insert($data);
    Session::flash('success',"Successful");
        return back(); 
}else{
    Session::flash('warning',"Department is already active");
        return back(); 
}
   
  
}

public function viewEnableResultDepartmentOld()
{
$check=DB::connection('oldporta')->table('enable_result_uploads')->get();


$data =array();

foreach($check as $v)
{
$data[] = $v->department_id;
}
$department=DB::connection('oldporta')->table('departments')->whereIn('departments_id',$data)->get();
return view('general.enableResultDepartmentOld.view')->with('d',$department);
}
public function reverseEnableResultDepartmentOld(Request $request)
{
$del =DB::connection('oldporta')->table('enable_result_uploads')->whereIn('department_id',$request->id)->delete();
Session::flash('success',"Successful");
return back(); 
}

public function changeJambRegToPassword($entry_year)
{
    $user =DB::connection('mysql2')->table('users')
    ->where([['entry_year',$entry_year],['entry_month','!=',5]])->get();

    foreach($user->chunk(500) as $items){
    foreach($items as $v)
    {DB::connection('mysql2')->table('users')
    ->where('id', $v->id)
    ->update(['password' =>Hash::make(strtoupper($v->matric_number)),'entry_month'=>5]);
   }
}
  die('success');
    return back();
}
}
