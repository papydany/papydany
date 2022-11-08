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
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use Hash;
use App\Http\Traits\MyTrait;

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
        
        
        
           Session::flash('success',"SUCCESSFULL");
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
 User::on('mysql2')->where('matric_number',$old)->update(['matric_number' => $new]);
    // ==== updated result table
    StudentResult::where('matric_number',$old)->update(['matric_number' => $new]);

    // ==== updated backup result table
    StudentResultBackup::where('matric_number',$old)->update(['matric_number' => $new]);
    
    // ==== pin
   Pin::where('matric_number',$old)->update(['matric_number' => $new]);
  // echo $new.'<br/>';
      
}
//dd();
Session::flash('success',"SUCCESSFULL");
           return back();
}
Session::flash('warning',"No records is updated, either you did not enter matric number to be updated  
or matric number exist already.contact system ADMIN");
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
        Session::flash('success',"SUCCESSFULL");
        return back();
    }

    public function updatedepartment(Request $request)
    {
      
      $u =DB::connection('mysql2')->table('users')
            ->where('id', $request->user_id)
            ->update(['faculty_id' =>$request->faculty_id,'department_id' => $request->department_id,'fos_id' => $request->fos_id]);
            Session::flash('success',"SUCCESSFULL");
            return back();
    }

    public function resetStudentPassword($id)
    {
    $user = User::on('mysql2')->find($id);
    $user->password =Hash::make($user->matric_number);
    $user->save();
    Session::flash('success',"SUCCESSFULL. the new password is the mattric number");
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
    Session::flash('success',"SUCCESSFULL");
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
}
