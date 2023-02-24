<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\AssignCourse;
use App\Models\Course;
use App\Models\CourseReg;
use App\Models\StudentReg;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Fos;
use App\Http\Traits\MyTrait;
use App\Models\Level;
use App\Models\Pin;
use App\Models\CourseUnit;
use App\Models\RegisterCourse;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\StudentResult;
use App\Models\StudentResultBackup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Imports\UsersImport;
use App\Imports\UserGssImport;
use App\Imports\UserGssUpdateImport;
use App\Imports\UserGssImportII;
use App\Imports\UserGssUpdateImportII;
use App\Exports\ResultDownloadGssExportII;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ErsExport;
use App\Models\RegisterSpecialization;
use App\Models\UpdateResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\Custom;


class DeskController extends Controller
{
    use MyTrait;
    const EXAMSOFFICER = 4;
    const LECTURER = 5;
    const HOD = 7;
    const FIRST = 1;
    const SECOND = 2;
    const MEDICINE = 14;
    const DENTISTRY = 10;
    const AGRIC = 18;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        return view('desk.index');
    }

    //======================================== Lecturer =====================================

    public function new_lecturer()
    {

        return view('desk.new_lecturer');
    }

    //======================================== post lecturer =====================================
    public function post_new_lecturer(Request $request)
    {
        $variable = $request->input('username');
        foreach ($variable as $key => $value) {
            if (!empty($value)) {
                $clean_list[$value] = array('title' => $request->title[$key], 'name' => $request->name[$key], 'email' => $request->email[$key], 'password' => $request->password[$key], 'username' => $value);
            }
        }

        foreach ($clean_list as $kk => $vv) {
            $username[] = $vv['username'];
        }

        $check = User::whereIn('username', $username)->get();
        if (count($check) > 0) {
            foreach ($check as $key => $value) {
                unset($clean_list[$value->username]);
            }

        }

        if (count($clean_list) != 0) {

            foreach ($clean_list as $k => $v) {

                $user = DB::table('users')->insertGetId(['title' => $clean_list[$k]['title'], 'name' => $clean_list[$k]['name'], 'username' => $clean_list[$k]['username'], 'password' => bcrypt($clean_list[$k]['password']), 'email' => $clean_list[$k]['email'], 'plain_password' => $clean_list[$k]['password'], 'faculty_id' => Auth::user()->faculty_id, 'department_id' => Auth::user()->department_id, 'programme_id' => 0, 'fos_id' => 0, 'edit_right' => 0]);

                $role = Role::find(5);
                $user_role = DB::table('user_roles')->insert(['user_id' => $user, 'role_id' => $role->id]);

            }
            Session::flash('success', "SUCCESSFUL.");
            return redirect()->action([DeskController::class,'new_lecturer']);
        }

    }
    public function resetPassword($id)
    {
        $p ='123456789';
        $u =DB::table('users')->where('id',$id)->update(['password' => bcrypt($p),'plain_password'=>$p]);
        
        Session::flash('success', "password reset successful. new password is ".$p);
     return back();
                
    }

    public function view_lecturer()
    {
        $user = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->whereIn('user_roles.role_id', [self::LECTURER, self::HOD, self::EXAMSOFFICER])
            ->where([['users.faculty_id', Auth::user()->faculty_id], ['users.department_id', Auth::user()->department_id]])
            ->orderBy('users.name', 'ASC')
            ->select('users.*')
            ->paginate(50);

        return view('desk.view_lecturer')->with('l',$user);
    }
//=============================== print lecturer ===================================
    public function print_lecturer()
    {
        $user = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->whereIn('user_roles.role_id', [self::LECTURER, self::HOD, self::EXAMSOFFICER])
            ->where([['users.faculty_id', Auth::user()->faculty_id], ['users.department_id', Auth::user()->department_id]])
            ->orderBy('users.name', 'ASC')
            ->select('users.*')
            ->get();

        return view('desk.print_lecturer')->with('l',$user);
    }
//---------------------------Edit Lecturer ---------------------------------------------------

    public function edit_lecturer($id)
    {
        $lecturer = User::find($id);

        return view('desk.edit_lecturer')->with('l',$lecturer);
    }

    public function post_edit_lecturer(Request $request, $id)
    {

        $lecturer = User::find($id);
        $lecturer->title = $request->title;
        $lecturer->name = $request->name;
        $lecturer->email = $request->email;
/*$lecturer->password =bcrypt($request->password);
$lecturer->plain_password =$request->password;*/
        $lecturer->save();
        return redirect()->action([DeskController::class,'view_lecturer']);
    }
//-------------------------------------new courses ----------------------------------------------
    public function new_course()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
        return view('desk.new_course')->with('l',$level)->with('s',$semester)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }
//-------------------------------------new courses ----------------------------------------------
    public function post_new_course(Request $request)
    {
        $this->validate($request, array('level' => 'required', 'semester' => 'required'));
        $semester = $request->input('semester');
        $level = $request->input('level');
        $month = $request->input('month');
        $variable = $request->input('course_code');
        $title = $request->input('course_title');
        $unit = $request->input('course_unit');
        $status = $request->input('status');
        if ($variable == null) {
            Session::flash('warning', "course Code is empty");
            return back();
        }
        foreach ($variable as $key => $value) {
            if (!empty($value)) {
                $cc = strtoupper(str_ireplace(" ", "", $value));

                $clean_list[$cc] = array('course_title' => $title[$key], 'course_unit' => $unit[$key], 'course_code' => $cc, 'status' => $status[$key]);
            }
        }

        foreach ($clean_list as $kk => $vv) {
            $course_code[] = $vv['course_code'];

        }

        $check = Course::whereIn('course_code', $course_code)->get();
        if (count($check) > 0) {
            foreach ($check as $key => $value) {
                unset($clean_list[$value->course_code]);
            }

        }

        if (count($clean_list) != 0) {

            foreach ($clean_list as $k => $v) {

                $data[] = ['course_title' => $clean_list[$k]['course_title'], 'course_code' => $clean_list[$k]['course_code'], 'course_unit' => $clean_list[$k]['course_unit'], 'status' => $clean_list[$k]['status'], 'level' => $level, 'department_id' => Auth::user()->department_id, 'semester' => $semester];

            }
            DB::table('courses')->insert($data);
            Session::flash('success', "SUCCESSFUL.");
            return redirect()->action([DeskController::class,'new_course']);
        }

    }
//-------------------------------------view courses ----------------------------------------------

    public function view_course()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
        return view('desk.view_course')->with('l',$level)->with('s',$semester);
    }

    public function get_view_course(request $request)
    {
        $l = Level::where('programme_id', Auth::user()->programme_id)->get();
        $this->validate($request, array('level' => 'required'));

        $level = $request->level;
        $month = $request->month;
        if ($month != null) {
            $course = Course::where([['department_id', Auth::user()->department_id], ['level', $level], ['month', $month]])
                ->orderBy('semester', 'ASC')->orderBy('status', 'ASC')->orderBy('course_code', 'ASC')->get();
        } else {
            $course = Course::where([['department_id', Auth::user()->department_id], ['level', $level]])
                ->orderBy('semester', 'ASC')->orderBy('status', 'ASC')->orderBy('course_code', 'ASC')->get();
        }

        return view('desk.view_course')->with('course',$course)->with('l',$l);
    }
//---------------------------Edit Lecturer ---------------------------------------------------
//08036138346
    public function edit_course($id)
    {
        /*  if(Auth::user()->edit_right == 0)
        {
        Session::flash('danger',"You need edit right to edit course. contact the system admin.");
        return redirect()->action('DeskController@view_course');
        }*/
        $course = Course::find($id);

        return view('desk.edit_course')->with('c',$course);
    }

    public function post_edit_course(Request $request, $id)
    {

        $c = Course::find($id);
        $c->course_title = strtoupper($request->course_title);
       // $c->course_code = strtoupper($request->course_code);
        $c->course_unit = $request->course_unit;
        $c->status = $request->status;
        $c->semester = $request->semester;
        $c->save();
        Session::flash('success', "SUCCESSFUL.");
        return redirect()->action([DeskController::class,'view_course']);
    }

//----------------------- delete course ------------------------------------------------------
    public function delete_course($id)
    {
        /*  if(Auth::user()->edit_right == 0)
        {
        Session::flash('danger',"You need edit right to delete course. contact the system admin.");
        return redirect()->action('DeskController@view_course');
        }*/
        $course = Course::destroy($id);
        Session::flash('success', "SUCCESSFUL.");
        return redirect()->action([DeskController::class,'view_course']);
    }

    public function delete_multiple_course(Request $request)
    {
        /*  if(Auth::user()->edit_right == 0)
        {
        Session::flash('danger',"You need edit right to delete course. contact the system admin.");
        return redirect()->action('DeskController@view_course');
        }*/
        $variable = $request->input('id');
        if ($variable == null) {
            return back();
        }

        $course = Course::destroy($variable);
        Session::flash('success', "SUCCESSFUL.");
        return redirect()->action([DeskController::class,'view_course']);
    }
//------------------------------------------ register course --------------------------------------------

    public function register_course()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
     // $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
        
        $f = $this->get_faculty();

       return view('desk.register_course')->with('fc',$f)->with('l',$level)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }

//------------------------------------------------------------------------------
    public function get_register_course(request $request)
    {
        $f = $this->get_faculty();
        $l = Level::where('programme_id', Auth::user()->programme_id)->get();
        $s = Semester::where('programme_id', Auth::user()->programme_id)->get();
        $this->validate($request, array('semester' => 'required'));
        $semester = $request->semester;
        $month = $request->month;
        $d=$request->department;
        if($d == null)
        {
        $d =Auth::user()->department_id;
        }
        // get fos
       
        $fos = $this->get_fos();
        if ($fos == null) {
            Session::flash('waring', "Contact the system Admin. your account is not assign to field od study.");
            return back();
        }
        if ($month != null) {
            $course = Course::where([['department_id',$d], ['semester', $semester], ['month', $month]])
                ->orderBy('course_code', 'ASC')->get();
            return view('desk.register_course')->with('course',$course)->with('fc',$f)->with('l',$l)->with('s',$s)->with('semester',$semester)->with('f',$fos)->with('M',$month)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
        } else {
            $course = Course::where([['department_id', $d], ['semester', $semester]])
                ->orderBy('course_code', 'ASC')->get();
            return view('desk.register_course')->with('course',$course)->with('fc',$f)->with('l',$l)->with('s',$s)->with('semester',$semester)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
        }

    }
//-------------------------------------------------------------------------------------------------
    public function post_register_course(Request $request)
    {
        $this->validate($request, array('fos' => 'required', 'session_id' => 'required'));
        $session = $request->input('session_id');
        $month = $request->input('month');
        $fos = $request->input('fos');
        $level = $request->input('level');
        $p = Auth::user()->programme_id;
        $d = Auth::user()->department_id;
        $f = Auth::user()->faculty_id;
   
        $fos_name = Fos::find($fos);
        if($p == 0){
            $p =$fos_name->programme_id;
        }
        $variable = $request->input('id');
        if ($variable == null) {
            Session::flash('warning', "select course to register.");
            return back();
        }
       
        $course = Course::whereIn('id', $variable)->get();
        foreach ($course as $key => $value) {
  $data[$value->id] = ['course_id' => $value->id, 'programme_id' => $p, 'department_id' => $d, 
  'faculty_id' => $f, 'fos_id' => $fos, 'specialization_id'=>0, 'level_id' => $level, 
  'semester_id' => $value->semester, 'reg_course_title' => $value->course_title,
  'reg_course_code' => $value->course_code, 'reg_course_unit' => $value->course_unit, 
  'reg_course_status' => $value->status, 'session' => $session, 'month' => $month];

    $check_data[] = $value->id;
    $check_level[] = $level;
        }
        
// check if course exist already on the register course table
        $check = RegisterCourse::whereIn('course_id', $check_data)
            ->whereIn('level_id', $check_level)
            ->where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos], ['session', $session], ['reg_course_status', '!=', 'G']])
            ->get();
              
        if (count($check) > 0) {
            foreach ($check as $key => $value) {
               unset($data[$value->course_id]);
            }
        }
       
if(count($data) > 0){
        DB::table('register_courses')->insert($data);
    
 Session::flash('success', "SUCCESSFUL.");
}else{
    Session::flash('warning ', "Check register courses exist already.");
}
 return redirect()->action([DeskController::class,'register_course']);
    }
//-------------------------------view register --------------------------------------
    public function view_register_course()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        // $semester =Semester::where('programme_id',Auth::user()->programme_id)->get();
        // get fos
        $fos = $this->get_fos();
        return view('desk.view_register_course')->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }
//----------------------------------------------------------------------------------------------------
    public function post_view_register_course(request $request)
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
        // get fos
        $fos_id = $this->get_fos();

        $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required'));
        $session = $request->session_id;
        $fos = $request->fos;

        $l = $request->level;
        $p = Auth::user()->programme_id;
        $d = Auth::user()->department_id;
        $f = Auth::user()->faculty_id;

        $register_course = RegisterCourse::where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos], ['level_id', $l], ['session', $session]])
        ->whereIn('reg_course_status',['C','E'])
        ->orderBy('semester_id', 'ASC')->orderBy('reg_course_status', 'ASC')->get();
        return view('desk.display_register_course')
        ->with('l',$level)->with('s',$semester)->with('f',$fos_id)->with('r',$register_course)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos);
    }
//--------------------------------- registered course ----------------------------------------------
    public function registeredcourse()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        // $semester =Semester::where('programme_id',Auth::user()->programme_id)->get();
        // get fos
        $p = $this->getp();
        $fos = $this->get_fos();
        return view('desk.registeredcourse')->with('p',$p)->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }

//----------------------------------------------------------------------------------------------------
    public function post_registeredcourse(request $request)
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
        // get fos
        $fos_id = $this->get_fos();
        $pp = $this->getp();
        $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required'));
        $session = $request->session_id;
        $fos = $request->fos;
        
        $l = $request->level;
        $p = Auth::user()->programme_id;
        $d = Auth::user()->department_id;
        $f = Auth::user()->faculty_id;
        $fos_name = Fos::find($fos);
        if($p == 0)
        {
            $p =$request->p;
        }
        /*if($sfos == 0)
        {
          $specialization ='No Specialization Field Of Study';
        }else{
        $specialization = Specialization::find($sfos);
        $specialization =$specialization->name;
        }*/

        $register_course = RegisterCourse::where([['programme_id',$p],['department_id',$d],['faculty_id',$f],['fos_id',$fos],['level_id',$l],['session',$session]])
        ->orderBy('semester_id','ASC')->orderBy('reg_course_status','ASC')->get();
       
        return view('desk.registeredcourse')->with('p',$pp)->with('l',$level)->with('s',$semester)->with('f',$fos_id)->with('r',$register_course)
        ->with('g_s',$session)->with('g_l',$l)->with('fos',$fos)->with('fn',$fos_name)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }
    //---------------------------specialization ------------------------------------------------------

    public function specialized_course()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        $fos = $this->get_fos();
        if ($fos == null) {
            Session::flash('waring', "Contact the system Admin. your account is not assign to field od study.");
            return back();
        }
        return view('desk.specialization.registerCourse')->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }

//------------------------------------------------------------------------------
    public function get_specialized_course(request $request)
    {
        $l = Level::where('programme_id', Auth::user()->programme_id)->get();
      $this->validate($request, array('level'=>'required','session'=>'required','fos' =>'required'));
       
        $level = $request->level;
        $month = $request->month;
        $s = $request->session;
        $fos_id = $request->fos;
        $spec = Specialization::where('fos_id',$fos_id)->get();
        // get fos
        $fos = $this->get_fos();
        if ($fos == null) {
            Session::flash('waring', "Contact the system Admin. your account is not assign to field od study.");
            return back();
        }
        if ($month != null) {
            $course = RegisterCourse::where([ ['level_id', $level], ['session', $s],['fos_id',$fos_id], ['month', $month]])
            ->whereIn('reg_course_status',['C','E'])
            ->orderBy('semester_id', 'ASC') ->orderBy('reg_course_code', 'ASC')->get();
            return view('desk.specialization.registerCourse')->with('spec',$spec)->with('course',$course)->with('l',$l)->with('level',$level)->with('s',$s)->with('fos_id',$fos_id)->with('f',$fos)->with('M',$month)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
        } else {
            $course = RegisterCourse::where([['level_id', $level], ['session', $s],['fos_id',$fos_id]])
            ->whereIn('reg_course_status',['C','E'])
            ->orderBy('semester_id', 'ASC') ->orderBy('reg_course_code', 'ASC')->get();
            return view('desk.specialization.registerCourse')->with('spec',$spec)->with('course',$course)->with('l',$l)->with('level',$level)->with('s',$s)->with('fos_id',$fos_id)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
        }

    }
//-------------------------------------------------------------------------------------------------
    public function post_specialized_course(Request $request)
    {
        $this->validate($request, array('spec' => 'required'));
        $fos_id = $request->input('fos_id');
        $spec = $request->input('spec');
        $variable = $request->input('id');
        if ($variable == null) {
            Session::flash('warning', "select course to register.");
            return back();
        }
        $scheck =array();$sData=array();

        $course = RegisterCourse::whereIn('id', $variable)->get();
        foreach ($course as $key => $value) {
  $data[$value->id] = ['registercourse_id' => $value->id,'fos_id' =>$fos_id, 'specialization_id'=>$spec,'status' =>0];
$check_data[] = $value->id;
}
        
// check if course exist already on the register course table
        $check = RegisterSpecialization::whereIn('registercourse_id', $check_data)
            ->where([['fos_id', $fos_id], ['specialization_id',$spec]])
            ->get();
              
        if (count($check) > 0) {
            foreach ($check as $key => $value) {
               unset($data[$value->registercourse_id]);
            }
        }
       
if(count($data) > 0){
        DB::table('register_specializations')->insert($data);
 
        Session::flash('success', "SUCCESSFUL.");
}else{
    Session::flash('warning ', "Check specialization courses exist already.");
}
 return redirect()->action([DeskController::class,'specialized_course']);
    }
//------------------- veiw specialization-----------------------
    public function viewSpecializedCourse()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        // $semester =Semester::where('programme_id',Auth::user()->programme_id)->get();
        // get fos
        $fos = $this->get_fos();
        return view('desk.specialization.viewCourses')->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    } 

    public function postSpecializedCourse(request $request)
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
        // get fos
        $fos_id = $this->get_fos();

        $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required'));
        $session = $request->session_id;
        $fos = $request->fos;
        $sfos = $request->sfos;
        $l = $request->level;
        $button =$request->print;
        $p = Auth::user()->programme_id;
        $d = Auth::user()->department_id;
        $f = Auth::user()->faculty_id;
        $fos_name = Fos::find($fos);
        if($sfos == 0)
        {
          $specialization ='No Specialization Field Of Study';
        }else{
        $specialization = Specialization::find($sfos);
        $specialization =$specialization->name;
        }
if($button == 1){
    $register_course = DB::table('register_courses')
        ->join('register_specializations','register_courses.id','=','register_specializations.registercourse_id')
        ->whereIn('reg_course_status',['C','E'])
        ->where('register_specializations.specialization_id',$sfos)
        ->where([['programme_id',$p],['department_id',$d],['faculty_id',$f],['register_courses.fos_id',$fos],['level_id',$l],['session',$session]])
        ->orderBy('semester_id','ASC')->orderBy('reg_course_status','ASC')->get();
        return view('desk.specialization.print') ->with('l',$level)->with('s',$semester)->with('f',$fos_id)->with('r',$register_course)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos);

}else{
        $register_course = DB::table('register_courses')
        ->join('register_specializations','register_courses.id','=','register_specializations.registercourse_id')
        ->where([['programme_id',$p],['department_id',$d],['faculty_id',$f],['register_courses.fos_id',$fos],['level_id',$l],['session',$session]])
        ->where('register_specializations.specialization_id',$sfos)
        ->orderBy('semester_id','ASC')->orderBy('reg_course_status','ASC')->get();
       
        return view('desk.specialization.viewCourses')->with('l',$level)->with('f',$fos_id)->with('r',$register_course)
        ->with('g_s',$session)->with('g_l',$l)->with('fos',$fos)->with('fn',$fos_name)->with('med',self::MEDICINE)->with('den',self::DENTISTRY)
        ->with('spec',$specialization);
    }
}

//-------------------------------view register --------------------------------------
public function view_specialized_course()
{
    $level = Level::where('programme_id', Auth::user()->programme_id)->get();
    // $semester =Semester::where('programme_id',Auth::user()->programme_id)->get();
    // get fos
    $fos = $this->get_fos();
    return view('desk.specialization.view_specialized_course')->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
}
//----------------------------------------------------------------------------------------------------
public function post_view_specialized_course(request $request)
{
    $level = Level::where('programme_id', Auth::user()->programme_id)->get();
    $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
    // get fos
    $fos_id = $this->get_fos();

    $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required'));
    $session = $request->session_id;
    $fos = $request->fos;

    $l = $request->level;
    $p = Auth::user()->programme_id;
    $d = Auth::user()->department_id;
    $f = Auth::user()->faculty_id;

    $register_course = RegisterCourse::where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos], ['level_id', $l], ['session', $session]])
    ->whereIn('reg_course_status',['C','E'])
    ->orderBy('semester_id', 'ASC')->orderBy('reg_course_status', 'ASC')->get();
    return view('desk.display_register_course')
    ->with('l',$level)->with('s',$semester)->with('f',$fos_id)->with('r',$register_course)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos);
}
//----------------------------------------assign courses ---------------------------------------------------
    public function assign_course()
    {
        $level = $this->get_level();
       
        $semester = $this->get_semester();
        // get fos
        $fos = $this->get_fos();
        $p = $this->getp();
        $f = $this->get_faculty();
        return view('desk.assigncourse.index')->with('l',$level)->with('s',$semester)->with('f',$fos)->with('p',$p)->with('fc',$f);

    }
    public function delete_desk_course($id, $s)
    {

        $check = CourseReg::where([['registercourse_id', $id], ['session', $s]])->first();
        if ($check != null) {
            Session::flash('warning', "The courses selected has been registered by students.so u can not delete it. contact admin");

            return back();
        }
        $reg = RegisterCourse::destroy($id);
        $assign_course = AssignCourse::where('registercourse_id', $id)->first();
        if ($assign_course != null) {
            $assign_course->delete();
        }
        Session::flash('success', "successful.");
        return back();
    }

    public function delete_desk_multiple_course(Request $request)
    {
        $variable = $request->input('id');
        $session = $request->input('session');
        if ($variable == null) {
            return back();
        }
        $check = CourseReg::whereIn('registercourse_id', $variable)->where('session', $session)->get();
        if (count($check) > 0) {
            Session::flash('warning', "Some of the courses selected has been registered by students.so u can not do mass deleting .delete one after the other.");

            return back();
        }
        $reg = RegisterCourse::destroy($variable);

        $assign_course = AssignCourse::whereIn('registercourse_id', $variable)->get();

        if (count($assign_course) > 0) {
            foreach ($assign_course as $key => $value) {
                $data[] = $value->id;
            }

            AssignCourse::destroy($data);
        }
        Session::flash('success', "SUCCESSFUL.");
        return back();
    }
//=========================================================================================================
    //--------------------------------------------------------------------------------------------------------
    public function get_assign_course(request $request)
    {
       // Session::flash('warning', "Assign Course has been disbled for now");
       // return back();
        $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required', 'semester' => 'required'));
        $semester_id = $request->semester;
        $session = $request->session_id;
        $fos_id = $request->fos;
        $l = $request->level;
        //dd($request->admin);
        if (isset($request->admin)) {
            $p = $request->programme_id;
            $f = $request->faculty_id;
            $d = $request->department_id;
        }
        elseif(isset($request->HOD)) {
            $p = $request->programme_id;
            $f = $this->f();
            $d = $this->d();
        }
        else {
            $p = $this->p();
            $f = $this->f();
            $d = $this->d();
        }

        $level = $this->get_level();
        $semester = $this->get_semester();
        $fos = $this->get_fos();
        $pp = $this->getp();
        $fc = $this->get_faculty();

        $lecturer = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->whereIn('user_roles.role_id', [self::LECTURER, self::EXAMSOFFICER, self::HOD])
            ->where([['users.faculty_id', $f], ['users.department_id', $d]])
            ->orderBy('users.name', 'ASC')
            ->select('users.*')
            ->get();

        $assign_course = AssignCourse::where([['department_id', $d], ['faculty_id', $f], ['fos_id', $fos_id], ['level_id', $l], ['session', $session], ['semester_id', $semester_id]])->orderBy('semester_id', 'ASC')->select('registercourse_id')->get();

        if (count($assign_course) > 0) {
            foreach ($assign_course as $key => $value) {
                $register_course_id[] = $value->registercourse_id;
            }

            $register_course = RegisterCourse::whereNotIn('id', $register_course_id)
                ->where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos_id], ['level_id', $l], ['session', $session], ['semester_id', $semester_id]])
                ->orderBy('semester_id', 'ASC')->distinct('course_id')->get();

        } else {
            $register_course = RegisterCourse::where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos_id], ['level_id', $l], ['session', $session], ['semester_id', $semester_id]])
            ->orderBy('semester_id', 'ASC')->distinct('course_id')->get();
        }

        return view('desk.assigncourse.index')->with('l',$level)->with('s',$semester)->with('f',$fos)->with('rs',$register_course)->with('lec',$lecturer)->with('g_s',$session)
        ->with('g_l',$l)->with('p',$pp)->with('fc',$fc)->with('f_id',$f)->with('d_id',$d);
    }

//--------------------------assign courses other-----------------------------------
    public function assign_course_other()
    {
       // Session::flash('warning', "Assign Course has been disbled for now");
       // return back();
        $level = $this->get_level();
        $semester = $this->get_semester();
        // get fos
        $fos = $this->get_fos();
        $p = $this->getp();
        $f = $this->get_faculty();
        return view('desk.assigncourse.assign_courses_other')->with('l',$level)->with('s',$semester)->with('f',$fos)->with('p',$p)->with('fc',$f);
    }

    public function post_assign_course_other(Request $request)
    {
        $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required', 'semester' => 'required'));
        $semester_id = $request->semester;
        $session = $request->session_id;
        $fos_id = $request->fos;
        $l = $request->level;

        $level = $this->get_level();
        $semester = $this->get_semester();
        $fos = $this->get_fos();
        if (isset($request->admin)) {
            $p = $request->programme_id;
            $f = $request->faculty_id;
            $d = $request->department_id;
        } elseif(isset($request->HOD)) {
            $p = $request->programme_id;
            $f = $this->f();
            $d = $this->d();
        }
        else {
            $p = $this->p();
            $f = $this->f();
            $d = $this->d();
        }
        $pp = $this->getp();
        $fc = $this->get_faculty();

        $department = Department::orderBy('department_name', 'ASC')->get();

        $assign_course = AssignCourse::where([['department_id', $d], ['faculty_id', $f], ['fos_id', $fos_id], ['level_id', $l], ['session', $session], ['semester_id', $semester_id]])->orderBy('semester_id', 'ASC')->select('registercourse_id')->get();
        if (count($assign_course) > 0) {
            foreach ($assign_course as $key => $value) {
                $register_course_id[] = $value->registercourse_id;
            }

            $register_course = RegisterCourse::whereNotIn('id', $register_course_id)
                ->where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos_id], ['level_id', $l], ['session', $session], ['semester_id', $semester_id]])
                ->orderBy('semester_id', 'ASC')->distinct('course_id')->get();

        } else {
            $register_course = RegisterCourse::where([['programme_id', $p],['department_id', $d],['faculty_id', $f], ['fos_id', $fos_id], ['level_id', $l], ['session', $session], ['semester_id', $semester_id]])
            ->orderBy('semester_id', 'ASC')->distinct('course_id')->get();
        }

        return view('desk.assigncourse.assign_courses_other')->with('l',$level)->with('s',$semester)->with('f',$fos)->with('rs',$register_course)->with('depart',$department)->with('g_s',$session)->with('g_l',$l)->with('p',$pp)->with('fc',$fc)->with('f_id',$f)->with('d_id',$d);
    }

//-------------------------------------------------------------------------------------
    public function post_assign_course_o(Request $request)
    {
        $id = $request->input('id');
        $this->validate($request, array('Lecturer' => 'required'));
        if ($id == null) {
            return back();
        }
        $lecturer = $request->input('Lecturer');
        if (isset($request->admin)) {

            $f = $request->faculty_id;
            $d = $request->department_id;
        } else {

            $f = $this->f();
            $d = $this->d();
        }
$date=date("Y-m-d h:i:s");
// status 1 mean fos is assign and 0 mean not assigned
        foreach ($id as $key => $value) {
            $v[] = ['registercourse_id' => $value,'created_at'=>$date,'admin'=>auth::user()->id, 'user_id' => $lecturer, 'department_id' => $d, 'faculty_id' => $f, 'fos_id' => $request->input('fos_id')[$key], 'level_id' => $request->input('level')[$key], 'session' => $request->input('session')[$key], 'semester_id' => $request->input('semester_id')[$key]];

        }

        DB::table('assign_courses')->insert($v);
        Session::flash('success', "SUCCESSFUL.");
        return redirect()->action([DeskController::class,'assign_course_other']);

    }

//-------------------------------------------------------------------------------------
    public function post_assign_course(Request $request)
    {
        $id = $request->input('id');
        $this->validate($request, array('lecturer' => 'required'));
        if ($id == null) {
            return back();
        }
        $lecturer = $request->input('lecturer');
        $f = $request->f_id;
        $d = $request->d_id;
// status 1 mean fos is assign and 0 mean not assigned
$date=date("Y-m-d h:i:s");
        foreach ($id as $key => $value) {
            $v[] = ['registercourse_id' => $value,'created_at'=>$date,'admin'=>auth::user()->id, 'user_id' => $lecturer, 'department_id' => $d, 'faculty_id' => $f, 'fos_id' => $request->input('fos_id')[$key], 'level_id' => $request->input('level')[$key], 'session' => $request->input('session')[$key], 'semester_id' => $request->input('semester_id')[$key]];

        }

        DB::table('assign_courses')->insert($v);
        Session::flash('success', "SUCCESSFUL.");
        return redirect()->action([DeskController::class,'assign_course']);

    }
//------------------------------------------------------------------------------------------------------
    public function view_assign_course()
    {
        //Session::flash('warning', "View Assign Course has been disbled for now");
        //return back();
        $level = $this->get_level();
        $semester = $this->get_semester();
        $fos = $this->get_fos();
        $p = $this->getp();
        $f = $this->get_faculty();
       
        return view('desk.assigncourse.view')->with('l',$level)->with('s',$semester)->with('f',$fos)->with('fc',$f)->with('p',$p);
    }

    public function get_view_assign_course(request $request)
    {
        $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required', 'semester' => 'required'));

        $semester_id = $request->semester;
        $session = $request->session_id;
        $fos_id = $request->fos;
        $l = $request->level;

        $level = $this->get_level();
        $semester = $this->get_semester();
        $fos = $this->get_fos();
        $pp = $this->getp();
        $fc = $this->get_faculty();
        if (isset($request->admin)) {
            $p = $request->programme_id;
            $f = $request->faculty_id;
            $d = $request->department_id;
        } else {
            $p = $this->p();
            $f = $this->f();
            $d = $this->d();
        }

        $assign_course =DB::table('assign_courses')
        ->join('register_courses','register_courses.id', '=','assign_courses.registercourse_id')
        ->join('users','users.id', '=','assign_courses.user_id')
        ->where([['assign_courses.department_id', $d], ['assign_courses.faculty_id', $f], ['assign_courses.fos_id', $fos_id],
         ['assign_courses.level_id', $l], ['assign_courses.session', $session], ['assign_courses.semester_id', $semester_id]])
         ->orderBy('assign_courses.semester_id', 'ASC')
         ->select('assign_courses.id as asid','users.*','register_courses.*')
         ->get();
        
        return view('desk.assigncourse.view')->with('l',$level)->with('s',$semester)->with('f',$fos)->with('fc',$fc)
        ->with('p',$pp)->with('f_id',$f)->with('d_id',$d)->with('ac',$assign_course)->with('g_s',$session)->with('g_l',$l)
        ->with('s_id',$semester_id);
        
    }
    public function remove_assign_course($id)
    {
        $r = AssignCourse::find($id);
        $regCourse =RegisterCourse::find($r->registercourse_id);
        $result=DB::connection('mysql2')->table('student_results')->where([['course_id',$regCourse->course_id],['examofficer',$r->user_id],['session',$r->session]])->get()->count();
    
       if($result != 0)
        {
            Session::flash('warning', "The assign user have enter result already for these course. so you cant drop it");  
        }else{
        $r->delete();
        Session::flash('success', "SUCCESSFUL.");
        }
        return redirect()->action([DeskController::class,'view_assign_course']);
    }
    public function remove_multiple_assign_course(Request $request)
    {
        $id = $request->id;
        $del=array();
        $aCourse =AssignCourse::where('id',$id)->get();
        foreach($aCourse as $v)
        {
         $regCourse =RegisterCourse::find($v->registercourse_id);
        $result=DB::connection('mysql2')->table('student_results')->where([['course_id',$regCourse->course_id],['examofficer',$v->user_id]])->get()->count();
        if($result == 0)
        {
       $del[]=$v->id;
        }

    }

if(!empty($del)){
        $r = AssignCourse::destroy($id);
        Session::flash('success', "SUCCESSFUL.");
}else{
    Session::flash('warning', "The assign user have enter result already for these course. so you cant drop it");
}
        return redirect()->action([DeskController::class,'view_assign_course']);
    }
//----------------------------------print assign course------------------------------------------------
    public function print_assign_course()
    {
        $level = $this->get_level();
        $semester = $this->get_semester();
        $fos = $this->get_fos();
        return view('desk.print_assign_courses')->with('l',$level)->with('s',$semester)->with('f',$fos);
    }
//=================================print post assign =====================================
    public function get_print_assign_course(request $request)
    {
        $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required', 'semester' => 'required'));

        $semester_id = $request->semester;
        $session = $request->session_id;
        $fos_id = $request->fos;
        $l = $request->level;
        $p = $this->p();
        $f = $this->f();
        $d = $this->d();

        $assign_course = AssignCourse::where([['department_id', $d], ['faculty_id', $f], ['fos_id', $fos_id], ['level_id', $l], ['session', $session], ['semester_id', $semester_id]])->orderBy('semester_id', 'ASC')->get();

        return view('desk.display_assign_course')->with('ac',$assign_course)->with('g_s',$session)->with('g_l',$l)
        ->with('s_id',$semester_id)->with('Fos',$fos_id);
    }

    //======================================== student =====================================
    public function returning_student()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        $fos = $this->get_fos();
       return view('desk.student.returning_student')->with('f',$fos)->with('l',$level);
    }

    public function getReturningStudent(Request $request)
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
     
        $fos_id = $this->get_fos();

        $this->validate($request, array('fos' =>'required', 'session_id'=>'required', 'level' =>'required'));
        $session = $request->session_id;
        $fos = $request->fos;
        $l = $request->level;
        $season = $request->season;
        $p = Auth::user()->programme_id;
        $d = Auth::user()->department_id;
        $f = Auth::user()->faculty_id;
        $fos_name = Fos::find($fos);
        $nextSession =$session +1;
        $nextL=$l+1;


        $prob_user_id = array();//$this->getprobationStudents($p, $d, $f, $l, $session);
        $sr= DB::connection('mysql2')->table('student_regs')
        ->join('users', 'student_regs.user_id', '=', 'users.id')
        ->whereBetween('level_id', [$l, $nextL])
       ->where([['users.fos_id',$fos],['student_regs.department_id',$d],['session',$nextSession],['season',$season],['semester',1]])
        ->distinct('student_regs.user_id')->get();
    
        if(count($sr) > 0)
        {
foreach($sr as $vr)
{
    $prob_user_id [] =$vr->user_id;
}
        }
    
        $registeStudent = $this->registerdStudents($fos, $p, $d, $f, $season, $session, $l, $prob_user_id);
       // dd($registeStudent);
       // $register_course = RegisterCourse::where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos], ['level_id', $l], ['session', $session]])->orderBy('semester_id', 'ASC')->orderBy('reg_course_status', 'ASC')->get();
        return view('desk.student.returning_student')
        ->with('l',$level)->with('f',$fos_id)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos)->with('fn',$fos_name)
        ->with('rs',$registeStudent)->with('season',$season);
        
    }

    public function postReturningStudent(Request $request)
    {
$id=$request->id;

if($id == null)
{
    Session::flash('danger', "You did not select any student.");
    return back();
}
$studentProfile = DB::connection('mysql2')->table('users')->where('id',$id)->first();
      

$schFessStatus =$this->get_school_status($studentProfile->matric_number,$request->session);
/*if(Auth::user()->department_id == 25 || Auth::user()->faculty_id==self::MEDICINE)
{
$schFessStatus ='OK Proceed';
}*/
if($schFessStatus !='OK Proceed')
{
    Session::flash('warning', "these students have not paid school fees");
    return back(); 
}

$s=$request->session;
$fos=$request->fos_id;
 $l=$request->level_id;
 $nextLevel =$l+1;
 $season=$request->season; 
 $previous_session =$s -1;

 $specialization_id='';
 $isProbation ='';
 
 $failed_register_course2 = array();
  $drop_register_course2 =array();
  $failed_register_course1 = array();
  $drop_register_course1 =array();

  // check if all register courses has result
  $course_id_with_no_result =array(); 
  // modified to allow hnd 400 students to register first semester only cousrs of outstanding result in year 3 11/1/2022
  // adding check for only first semester
 
 $courseReg=CourseReg::where([['session',$previous_session],['level_id',$l],['user_id',$id]])->get();
   
  foreach ($courseReg as $key => $value) {
   $studentresult=StudentResult::where([['session',$previous_session],['level_id',$l],['user_id',$id],['coursereg_id',$value->id]])->first();
   if($studentresult == null)
   {
     // introducce to check for GSS with no result for 2019 session
  $ch =CourseReg::where([['id',$value->id],['session','2019']])->where('course_code','Like','GSS%')->first();
  if($ch == null)
  $course_id_with_no_result [] =$value->id;
   }
  }
  //dd($course_id_with_no_result);
  
  if(count($course_id_with_no_result) != 0)
  {
    Session::flash('danger', "Selected Students  have outstanding grade");
    return back();
  }

    $cgpa =$this->get_cgpa($s,$id,'NORMAL');
// diploma  student 
if(Auth::user()->programme_id == 2)
{
$r = '';
$u=DB::connection('mysql2')->table('users')->find($id);
$register_course = RegisterCourse::where([ ['fos_id', $fos], ['level_id', $nextLevel], ['session', $s]])
->whereIn('reg_course_status',['C','E'])
->orderBy('semester_id', 'ASC')
->orderBy('reg_course_status', 'ASC')->get()->groupBy('semester_id');

}else{
// for normal students... diploma own is
$r = $this->Probtion($l,$id,$previous_session,$cgpa,'NORMAL',$fos);

if($r== "WITHDRAW" || $r== "WITHDRAW OR CHANGE PROGRAMME" || $r== "CHANGE PROGRAMME")
  {
    Session::flash('danger', "Selected Students  academic standing is WITHDRAW OR CHANGE PROGRAMME");
    return back();
}

$u=DB::connection('mysql2')->table('users')->find($id);

$specialization_id=$u->specialization_id;

 // get failed courses
 
 $firstSemester =1;
 $secondSemester =2;
if($season == 'NORMAL')
{
    $failed_courses1 =$this->getPreviouFailedCourses($id,$firstSemester,$previous_session,'NORMAL');
    $failed_courses2 =$this->getPreviouFailedCourses($id, $secondSemester,$previous_session,'NORMAL');


    /* =========================== failed course===============================*/
    //------------------------------ first semester
if(count($failed_courses1) > 0)
{
  foreach ($failed_courses1 as $key => $value) {
  
  $check_failed_courses =$this->NumberPreviousFailedCoursePerCourseId($id,$firstSemester,$previous_session,$value->course_id);
  $course_code =$this->getCourseCodeType($value->coursereg_id);
  $type = substr($course_code->course_code,0,3);
  // modified 08/07/2021
  if(in_array($type ,[ 'GSS','GST']) || $check_failed_courses < 3)
   {
$failed_courses_course_id1 [] =$value->course_id;
}
    
   }
  
  }

// -------------------  second semester-------------------------
  if(count($failed_courses2) > 0)
{
  foreach ($failed_courses2 as $key => $value) {
  
  $check_failed_courses =$this->NumberPreviousFailedCoursePerCourseId($id,$secondSemester,$previous_session,$value->course_id);
  $course_code =$this->getCourseCodeType($value->coursereg_id);
  $type = substr($course_code->course_code,0,3);
  // modified 08/07/2021
  if(in_array($type ,[ 'GSS','GST']) || $check_failed_courses < 3)
   {
$failed_courses_course_id2 [] =$value->course_id;
}
    
   }
  
  }

  /*====================================== drop courses===================================*/
 
  //    ======================= first semeter
$combine_course_id1 =array();
$unregister_course_id1 =array();  
  $d_register_course1 =$this->GetRegisteredCompulsaryCourses($firstSemester,$previous_session,$l,$fos,$specialization_id);
  if(count($d_register_course1) > 0)
  {
    foreach ($d_register_course1 as $key => $value) {
      $courseReg=$this->GetCourseRegSingle($previous_session,$l,$id,$value->id,$value->course_id,$firstSemester,'NORMAL'); 
    if($courseReg== null)
     {
      $unregister_course_id1 [] =$value->course_id;
     }
    }
  }

  // combine the failed and unregister courses_id;
  if(!empty($failed_courses_course_id1) && !empty($unregister_course_id1))
  {
$combine_course_id1 =array_merge($failed_courses_course_id1,$unregister_course_id1);

  }elseif(!empty($failed_courses_course_id1))
  {
$combine_course_id1 =$failed_courses_course_id1;
  }
  elseif(!empty($unregister_course_id1))
  {
$combine_course_id1 =$unregister_course_id1;
  }

  if(!empty($combine_course_id1))
  {
    $insert_data =array();
    foreach ($combine_course_id1 as $key => $value) {
      // get courses first
   $r_c =RegisterCourse::where([['course_id',$value],['semester_id',$firstSemester],['fos_id',$fos],['session',$previous_session]])->first();
   if($r_c != null){

     // check if failed or unregister courses id exist in registercourses
     
      $check_register_course =RegisterCourse::where([['course_id',$value],['semester_id',$firstSemester],['fos_id',$fos],['session',$s],['level_id',$nextLevel],['reg_course_status',"G"]])->first();
      // failed or unregister courses id does not exist in registercourses
      
      if($check_register_course == null)
      {
    // $specializationId =$this->getSpecializationIdWithLevel(Auth::user()->specialization_id,$previous_level);
   $insert_data[] =['course_id'=>$value,'programme_id'=>Auth::user()->programme_id,'department_id'=>Auth::user()->department_id,'faculty_id'=>Auth::user()->faculty_id,'fos_id'=>$fos,'specialization_id'=>0,
   'level_id'=>$nextLevel,'semester_id'=>$firstSemester,'reg_course_title'=>$r_c->reg_course_title,'reg_course_code'=>$r_c->reg_course_code,'reg_course_unit'=>$r_c->reg_course_unit,'reg_course_status'=>"G",'session'=>$s];
      }
    }
    }

    if(!empty($insert_data)) 
    {
    DB::table('register_courses')->insert($insert_data);
  }

  // select course from register course
  // for failed course
  
  if(!empty($failed_courses_course_id1))
  {
  $failed_register_course1 =$this->GetRegisteredCoursesWithArrayCourseId($firstSemester,$s,$nextLevel,"G",$failed_courses_course_id1,$fos);
}


// for drop courses
  if(!empty($unregister_course_id1))
  {
 $drop_register_course1 =$this->GetRegisteredCoursesWithArrayCourseId($firstSemester,$s,$nextLevel,"G",$unregister_course_id1,$fos);
  
}

}
//====================second semeter
$unregister_course_id2 =array(); $combine_course_id2 =array();
  $d_register_course2 =$this->GetRegisteredCompulsaryCourses($secondSemester,$previous_session,$l,$fos,$specialization_id);
  if(count($d_register_course2) > 0)
{
  foreach ($d_register_course2 as $key => $value) {
    $courseReg=$this->GetCourseRegSingle($previous_session,$l,$id,$value->id,$value->course_id,$secondSemester,'NORMAL'); 
   
   if($courseReg== null)
   {
    $unregister_course_id2 [] =$value->course_id;
   }
  }
}

// combine the failed and unregister courses_id;
  if(!empty($failed_courses_course_id2) && !empty($unregister_course_id2))
  {
$combine_course_id2 =array_merge($failed_courses_course_id2,$unregister_course_id2);

  }elseif(!empty($failed_courses_course_id2))
  {
$combine_course_id2 =$failed_courses_course_id2;
  }
  elseif(!empty($unregister_course_id2))
  {
$combine_course_id2 =$unregister_course_id2;
  }

  if(!empty($combine_course_id2))
  {
    $insert_data =array();
    foreach ($combine_course_id2 as $key => $value) {
      // get courses first
   $r_c =RegisterCourse::where([['course_id',$value],['semester_id',$secondSemester],['fos_id',$fos],['session',$previous_session]])->first();
   if($r_c != null){
// check if failed or unregister courses id exist in registercourses
     $check_register_course =RegisterCourse::where([['course_id',$value],['semester_id',$secondSemester],['fos_id',$fos],['session',$s],['level_id',$nextLevel],['reg_course_status',"G"]])->first();
      // failed or unregister courses id does not exist in registercourses
      
      if($check_register_course == null)
      {
    // $specializationId =$this->getSpecializationIdWithLevel(Auth::user()->specialization_id,$previous_level);
   $insert_data[] =['course_id'=>$value,'programme_id'=>Auth::user()->programme_id,'department_id'=>Auth::user()->department_id,'faculty_id'=>Auth::user()->faculty_id,'fos_id'=>$fos,'specialization_id'=>0,
   'level_id'=>$nextLevel,'semester_id'=>$secondSemester,'reg_course_title'=>$r_c->reg_course_title,'reg_course_code'=>$r_c->reg_course_code,'reg_course_unit'=>$r_c->reg_course_unit,'reg_course_status'=>"G",'session'=>$s];
      }
    }
    }

    if(!empty($insert_data)) 
    {
    DB::table('register_courses')->insert($insert_data);
  }

  // select course from register course
  // for failed course


  if(!empty($failed_courses_course_id2))
  {
  $failed_register_course2 =$this->GetRegisteredCoursesWithArrayCourseId($secondSemester,$s,$nextLevel,"G",$failed_courses_course_id2,$fos);
 }
// for drop courses
  if(!empty($unregister_course_id2))
  {
  $drop_register_course2 =$this->GetRegisteredCoursesWithArrayCourseId($secondSemester,$s,$nextLevel,"G",$unregister_course_id2,$fos);
  }

 }
$isProbation ='';
if($r=="PROBATION")
{
    $nextLevel =$l;
    $register_course =array();
    $isProbation =1;
}else{
  $presentSpecializationId =$this->getSpecializationIdWithLevel($specialization_id,$nextLevel);
  if($presentSpecializationId == 0){
    $register_course = RegisterCourse::where([ ['fos_id', $fos], ['level_id', $nextLevel], ['session', $s]])
    ->whereIn('reg_course_status',['C','E'])
    ->orderBy('semester_id', 'ASC')
    ->orderBy('reg_course_status', 'ASC')->get()->groupBy('semester_id');
  }else{
    $register_course=DB::table('register_courses')
    ->join('register_specializations','register_courses.id','=','register_specializations.registercourse_id')
    ->where([['programme_id',Auth::user()->programme_id],['faculty_id',Auth::user()->faculty_id],['department_id',Auth::user()->department_id],['register_courses.fos_id',$fos],
    ['session',$s],['level_id',$nextLevel]])
  ->where('register_specializations.specialization_id',$specialization_id)
    ->whereIn('reg_course_status',["C","E"])
    ->select('register_courses.*')
  ->orderBy('reg_course_status','ASC')->get()->groupBy('semester_id');
  }
}



//dd($register_course);
      
}else{
    die('vacation is coming');
}
}

return view('desk.student.post_returning_student')->with('rs',$register_course)->with('drc1',$drop_register_course1)->with('frc1',$failed_register_course1)->with('drc2',$drop_register_course2)->with('frc2',$failed_register_course2)->with('s',$s)
->with('l',$nextLevel)->with('fos',$fos)->with('season',$season)->with('id',$id)->with('u',$u)->with('isProbation',$isProbation);

    }

    public function registerReturningStudent(Request $request)
    {
        $id=$request->id;
        $s=$request->session;
        $fos=$request->fos;
        $l=$request->level;
        $regCourseId =$request->idc;
        $failedRegCourseId =$request->idf;
        $dropRegCourseId =$request->idd;
        $d=Auth::user()->department_id;
        $f=Auth::user()->faculty_id;
        $p=Auth::user()->programme_id;
        $firstSemester =1;
        $secondSemester =2;
        $pinPin =$request->pin;
        $pinId =$request->serial;
        $isprobation=$request->isProbation;
      if($dropRegCourseId == null && $failedRegCourseId == null && $regCourseId== null ){
        Session::flash('danger', "select course");
    return back();
}  
      
    
        //=============================pin authetication ========================
        $pin=Pin::where([['id',$pinId],['pin',$pinPin]])->first();
        if($pin == null){
            Session::flash('danger', "wrong pin or serial number");
            return back();
        }
        if($pin->status == 1 && $pin->student_id !=$id)
        {Session::flash('danger', "Pin use by another matric number");
            return back();

        }
        if($pin->log1 == 1 && $pin->log2 == 1){
            Session::flash('danger', "Pin already used for registration");
            return back();
        }
        $u=DB::connection('mysql2')->table('users')->find($id);
             if($failedRegCourseId == null)
             {
             $failedUnit1 =0;
             $failedUnit2 =0;
             $failedRegisteredCourses1=array();
             $failedRegisteredCourses2=array();
             }else{
             $failedRegisteredCourses1 =RegisterCourse::whereIn('id',$failedRegCourseId)->where('semester_id',$firstSemester)->get();
             $failedUnit1 =$failedRegisteredCourses1->sum('reg_course_unit');

             $failedRegisteredCourses2 =RegisterCourse::whereIn('id',$failedRegCourseId)->where('semester_id',$secondSemester)->get();
            $failedUnit2 =$failedRegisteredCourses2->sum('reg_course_unit');
             }

            if($dropRegCourseId == null)
            {
          $dropUnit1 =0;
          $dropUnit2 =0;
          $dropRegisteredCourses1 =array();
          $dropRegisteredCourses2=array();

            }else{
                $dropRegisteredCourses1 =RegisterCourse::whereIn('id',$dropRegCourseId)->where('semester_id',$firstSemester)->get();
                $dropUnit1 =$dropRegisteredCourses1->sum('reg_course_unit');
                $dropRegisteredCourses2 =RegisterCourse::whereIn('id',$dropRegCourseId)->where('semester_id',$secondSemester)->get();
                $dropUnit2 =$dropRegisteredCourses2->sum('reg_course_unit');
            }

            if($regCourseId == null)
            {
            $rcUnit1 =0;
            $rcUnit2 =0;
            $registeredCourses1 =array();
            $registeredCourses2 =array();
            }else{
             $registeredCourses1 =RegisterCourse::whereIn('id',$regCourseId)->where('semester_id',$firstSemester)->get();
             $rcUnit1 =$registeredCourses1->sum('reg_course_unit');
             $registeredCourses2 =RegisterCourse::whereIn('id',$regCourseId)->where('semester_id',$secondSemester)->get();
             $rcUnit2 =$registeredCourses2->sum('reg_course_unit');
            }

          $total1 =$failedUnit1 + $dropUnit1 + $rcUnit1;
          $total2 =$failedUnit2 + $dropUnit2 + $rcUnit2;
          if($isprobation == 1){}else{
          $course_unit =CourseUnit::where([['session',$s],['level',$l],['fos',$fos]])->first();
          if($course_unit == null)
          {
           $course_unit =CourseUnit::where([['session',$s],['level',0],['fos',0]])->first();
          }
   
          if($total1 > $course_unit->max){
            Session::flash('danger', "First semester unit is greater than ".$course_unit->max);
            return back();
        }
        if($total2 > $course_unit->max){
            Session::flash('danger', "Second semester unit is greater than ".$course_unit->max);
            return back();
        }
    }
        
         //check if student has registered for first semester
         $check1 = $this->registrationStatus($id,$firstSemester,$s);
         if($check1 == null){
          $studentRegId1 = $this->studentReg($id, $f, $d, $p, $l, $s, $firstSemester,'NORMAL',$fos);
          if(count($registeredCourses1) > 0){
          $courseReg1 = $this->studentCourseReg($id,$studentRegId1,$registeredCourses1,$l,$s,$firstSemester,'NORMAL',$fos);
          }
          if(count($dropRegisteredCourses1) > 0){
          $dropCourseReg1 = $this->studentCourseRegWithStatus($id, $studentRegId1, $dropRegisteredCourses1, $l, $s, $firstSemester, 'NORMAL','D',$fos);
          }
          if(count($failedRegisteredCourses1) > 0){
          $failedCourseReg1 = $this->studentCourseRegWithStatus($id, $studentRegId1, $failedRegisteredCourses1, $l, $s, $firstSemester, 'NORMAL','R',$fos);
          }
          DB::table('pins')->where('id', $pinId)->update(['log1' => 1,'log_session'=>$s,'student_id'=>$id,'status'=>1,'matric_number'=>$u->matric_number]);
        }
          $check2 = $this->registrationStatus($id,$secondSemester,$s);
         if ($check2 == null) { 
             
          $studentRegId2 = $this->studentReg($id, $f, $d, $p, $l, $s, $secondSemester,'NORMAL',$fos);
          if(count($dropRegisteredCourses2) > 0){ 
          $dropCourseReg2 = $this->studentCourseRegWithStatus($id, $studentRegId2, $dropRegisteredCourses2, $l, $s, $secondSemester,'NORMAL','D',$fos);
          }
          if(count($failedRegisteredCourses2) > 0){ 
          $failedCourseReg2 = $this->studentCourseRegWithStatus($id, $studentRegId2, $failedRegisteredCourses2, $l, $s, $secondSemester,'NORMAL','R',$fos);
          }
          if(count( $registeredCourses2) > 0){ 
          $courseReg2 = $this->studentCourseReg($id, $studentRegId2, $registeredCourses2, $l, $s, $secondSemester,'NORMAL',$fos);
          }
          DB::table('pins')->where('id', $pinId)->update(['log2' => 1,'log_session'=>$s]);
          }
         
          Session::flash('success', "Success");
          return back();
          
    }

    public function new_student()
    {
        $fos = $this->get_fos();
        return view('desk.student.index')->with('f',$fos);
    }

    public function post_new_student(Request $request)
    {
      
        $schFessStatus =$this->get_school_status($request->matric_number,$request->session);
      /*  if(Auth::user()->department_id == 25 || Auth::user()->faculty_id==self::MEDICINE)
        {
          $schFessStatus ='OK Proceed';  
        }*/
        
        if($schFessStatus !='OK Proceed')
        {
            $schFessStatus =$this->get_school_status($request->jamb_reg,$request->session);
            if($schFessStatus !='OK Proceed')
            {
            Session::flash('warning', "these students have not pay school fess");
            return back(); 
            }
        }
        $pinId =$request->serial;
       // $pinPin =$request->pin;
        $pin=Pin::where([['id',$request->serial],['pin',$request->pin]])->first();
        if($pin == null){
            Session::flash('warning', "check the serial or pin of the card");
          return back();
        }
        if($pin->status != 0)
        {
            Session::flash('warning', "pin already used");
            return back();
        }
        $surname=$request->surname;
        $firstname=$request->firstname;
        $othername=$request->othername;
        $fos=$request->fos;
        $email =$request->email;
        $phone =$request->phone;
        $gender =$request->gender;
        $s=$request->session;
        $jamb_reg=$request->jamb_reg;
        $l =1;
        $firstSemester =1;
        $secondSemester =2;
        $f=auth::user()->faculty_id;
        $d=auth::user()->department_id;
        $p=auth::user()->programme_id;
        $m=$request->matric_number;
        $password =Hash::make($m);
        $data =array();
      
        $reg = RegisterCourse::where([['fos_id',$fos], ['session',$s], ['level_id',$l]])->get();
        $check = $reg->count();
        
        if ($check == 0) {
            Session::flash('warning', "you have no registerd courses.");
            return back();
        }
        $u = DB::connection('mysql2')->table('users')->where('matric_number',$m)->first();
        
        if($u != null){
            Session::flash('warning', "Students exist already");
            return back();  
        }
        $j = DB::connection('mysql2')->table('users')->where('jamb_reg',$jamb_reg)->first();
        if($j != null){
            Session::flash('warning', "Students exist already");
            return back();  
        }
        $data =['matric_number'=>$m,'jamb_reg'=>$m,'surname'=>$surname,'firstname'=>$firstname,
        'othername'=>$othername,'programme_id'=>$p,'faculty_id'=>$f,'department_id'=>$d,'fos_id'=>$fos,
        'specialization_id'=>0,'email'=>$email,'entry_year'=>$s,'password'=>$password,'phone'=>$phone,'gender'=>$gender];

        $v=DB::connection('mysql2')->table('users')->insertGetId($data);

        $check1 = $this->registrationStatus($v, $firstSemester, $s);
        if ($check1 == null) {
            $studentRegId = $this->studentReg($v, $f, $d, $p, $l, $s, $firstSemester,'NORMAL',$fos);
            $registeredCourses = $this->getRegisteredCourses1($l, $s, $firstSemester, $fos);
            $courseReg = $this->studentCourseReg($v, $studentRegId, $registeredCourses, $l, $s, $firstSemester,'NORMAL',$fos);
            DB::table('pins')->where('id', $pinId)->update(['log1' => 1,'log_session'=>$s,'student_id'=>$v,'status'=>1,'matric_number'=>$m]);
      
        }

        //check if student has registered for second semester
        $check2 = $this->registrationStatus($v, $secondSemester, $s);
        if ($check2 == null) {
            $registeredCourses = $this->getRegisteredCourses1($l, $s, $secondSemester, $fos);
            if($registeredCourses != null){
            $studentRegId = $this->studentReg($v, $f, $d, $p, $l, $s, $secondSemester,'NORMAL',$fos);
            $courseReg2 = $this->studentCourseReg($v, $studentRegId, $registeredCourses, $l, $s, $secondSemester,'NORMAL',$fos);
            DB::table('pins')->where('id', $pinId)->update(['log2' => 1,'log_session'=>$s]);
            }
        }
        Session::flash('success', "Success");
        return back();
        
    }

    public function view_student()
    {
        $pp = $this->getp();
        $fos = $this->get_fos();
        return view('desk.view_student')->with('f',$fos)->with('pp',$pp);
    }
    // ===========================post view student ==========================================
    public function post_view_student(Request $request)
    {$pp = $this->getp();
        $faculty = Faculty::orderBy('faculty_name', 'ASC')->get();
        $fos = $this->get_fos();
        $fos_id = $request->input('fos_id');
        $session = $request->input('session_id');
        $entry_month = $request->input('entry_month');
        $p = $this->p();
        if($p == 0){
            $p =$request->pp;
        }
        $f = $this->f();
        $d = $this->d();
        $update =$request->updateMatricno;
    
        $user = DB::connection('mysql2')->table('users')->where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos_id], ['entry_year', $session], ['entry_month', $entry_month]])->orderBy('matric_number', 'ASC')->get();

        return view('desk.view_student')->with('faculty',$faculty)->with('f',$fos)->with('u',$user)->with('s',$session)->with('update',$update)->with('pp',$pp);
    }
// view student details
    public function view_student_detail(Request $request, $id)
    {
        $fos = $this->get_fos();
        $users = DB::connection('mysql2')->table('users')
            ->where([['id', $id], ['department_id', $this->d()]])
            ->first();
        if ($users != null) {
            $stdReg = DB::connection('mysql2')->table('student_regs')->where('user_id', $users->id)->get();
            $f = Faculty::get();
            return view('desk.view_student_detail')->with('u',$users)->with('sr',$stdReg)->with('f',$fos)->with('fc',$f);
        }

        $request->session()->flash('warning', 'Students  does not exist');
        return back();
    }

    // update students entry year

    public function update_entry_year(Request $request)
    {
        $entry_year = $request->input('session');
        $id = $request->input('user_id');
        $present_entry_year = $request->input('present_entry_year');
        $matric_number = $request->input('matric_number');

        $stdReg = DB::connection('mysql2')->table('student_regs')->where('user_id', $id)->count();
        if ($stdReg == 0) {
            $users = DB::connection('mysql2')->table('users')
                ->where([['id', $id], ['department_id', $this->d()]])
                ->update(['entry_year' => $entry_year]);
            $pin = Pin::where([['student_id', $id], ['matric_number', $matric_number], ['session', $present_entry_year]])
                ->update(['session' => $entry_year]);

            $request->session()->flash('success', 'SUCCESSFUL.');
            return back();

        } else {
            $request->session()->flash('warning', 'students registration of courses must be delete
         before you can update entry year.');
            return back();
        }

    }

    //======================================== register student =====================================

    public function register_student()
    {
        $fos = $this->get_fos();
        $l = $this->get_level();
        $semester = $this->get_semester();
        return view('desk.register_student')->with('f',$fos)->with('l',$l)->with('s',$semester)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }
    // ===========================post view student ==========================================
    public function post_register_student(Request $request, $fos_id = null, $level = null, $semester_id = null, $session = null, $season = null)
    {

        $fos = $this->get_fos();
        $l = $this->get_level();
        $semester = $this->get_semester();
        if (isset($fos_id) && isset($level) && isset($semester_id) && isset($session) && isset($season)) {
            $season = $season;
            $semester_id = $semester_id;
            $fos_id = $fos_id;
            $l_id = $level;
            $session = $session;
        } else {
            $season = $request->input('season');
            $semester_id = $request->input('semester_id');
            $fos_id = $request->input('fos_id');
            $l_id = $request->input('level');
            $session = $request->input('session_id');
        }
        //dd($level);
        $p = $this->p();
        if ($p == 0) {
            $foses = Fos::find($fos_id);
            $p = $foses->programme_id;
        }
        $f = $this->f();
        $d = $this->d();
        //$prob_user_id = $this->getprobationStudents($p, $d, $f, $l_id, $session);
        $all_user = array();
        $studentreg_id = array();
//dd($semester_id);
        $user = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
            ->where('users.fos_id', $fos_id)
            ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['student_regs.season', $season],
                ['student_regs.session', $session], ['student_regs.semester', $semester_id], ['student_regs.level_id', $l_id]])
           // ->whereNotIn('users.id', $prob_user_id)
            ->orderBy('users.matric_number', 'ASC')
            ->select('student_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.fos_id', 'users.entry_year')
            ->get();

        //Get current page form url e.g. &page=6
        $url = url()->full();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        //Create a new Laravel collection from the array data
        $collection = new Collection($user);

        //Define how many items we want to be visible in each page
        $perPage = 20;
        //Slice the collection to get the items to display in current page
        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        //Create our paginator and pass it to the view
        $paginatedSearchResults = new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
        return view('desk.register_student')->with('u',$paginatedSearchResults)->with('ss',$session)->with('f',$fos)->with('l',$l)->with('s',$semester)
            ->with('l_id',$l_id)->with('s_id',$semester_id)->with('url',$url)->with('med',self::MEDICINE)->with('den',self::DENTISTRY)->with('Ff',$f);
    }

    //================================= registered students II============================
    public function register_student_ii()
    {
        $fos = $this->get_fos();
        $l = $this->get_level();
        return view('desk.register_student.ii')->with('l',$l)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }
    // ===========================post view student ==========================================
    public function post_register_student_ii(Request $request, $fos_id = null, $level = null, $session = null, $season = null)
    {
        $fos = $this->get_fos();
        $l = $this->get_level();

        if (isset($fos_id) && isset($level) && isset($session) && isset($season)) {
            $season = $season;
            $fos_id = $fos_id;
            $l_id = $level;
            $session = $session;
        } else {
            $season = $request->input('season');
            $fos_id = $request->input('fos_id');
            $l_id = $request->input('level');
            $session = $request->input('session_id');
        }
        if($request->excel == 'excel')
        {
            return Excel::download(new ErsExport($request->all()), 'ers.xlsx');
        }else{
            $role = session('key');
           // $faculty_id
           // if($f == Self::MEDICINE || $role->name =='examsofficer' && $f ==Self::DENTISTRY || $role->name =='examsofficer' && $session < 2020 || $role->name =='HOD' && $session < 2020)
   
        //dd($level);
        $p = $this->p();
        if ($p == 0) {
            $foses = Fos::find($fos_id);
            $p = $foses->programme_id;
        }
        $f = $this->f();
        $d = $this->d();
        //$prob_user_id = $this->getprobationStudents($p, $d, $f, $l_id, $session);
        //dd($prob_user_id);
        $user = DB::connection('mysql2')->table('student_regs')
            ->distinct('student_regs.matric_number')
            ->join('users', 'student_regs.user_id', '=', 'users.id')
            ->where('users.fos_id', $fos_id)
            ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['student_regs.season', $season],
                ['student_regs.session', $session], ['student_regs.level_id', $l_id]])
          //->whereNotIn('users.id', $prob_user_id)
            ->orderBy('users.matric_number', 'ASC')
            ->select('users.id', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.fos_id', 'users.entry_year')
 ->get();
    }
        return view('desk.register_student.ii')->with('ss',$session)->with('l',$l)->with('f',$fos)->with('l_id',$l_id)
        ->with('u',$user)->with('season',$season)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }

    //============================= registered_student_detail========================
    public function registered_student_detail(Request $request, $user_id = null, $level = null, $session = null, $season = null)
    {

        if (isset($user_id) && isset($level) && isset($session) && isset($season)) {
            $season = $season;
            $user_id = $user_id;
            $level = $level;
            $session = $session;
            $studentDetails = array();
            $resultCourseregId =array();
            $u = DB::connection('mysql2')->table('users')->find($user_id);

            $result =DB::connection('mysql2')->table('student_results')
            ->where([['user_id',$user_id],['session',$session],['level_id',$level],['season',$season]])
            ->get();
           // dd($result);
            if($result->count() != null)
            {
                foreach($result as $v){
                    $resultCourseregId [] =$v->coursereg_id;

                }
            }

            $studentDetails = DB::connection('mysql2')->table('student_regs')
                ->join('course_regs', 'course_regs.studentreg_id', '=', 'student_regs.id')
                ->where([['student_regs.level_id', $level], ['student_regs.session', $session], ['student_regs.season', $season],
                    ['student_regs.user_id', $user_id]])
                ->where([['course_regs.user_id', $user_id], ['course_regs.level_id', $level], ['course_regs.session', $session], ['course_regs.period', $season]])
                ->whereNotIn('course_regs.id',$resultCourseregId)
                ->orderBy('course_regs.semester_id', 'ASC')
                ->orderBy('course_regs.course_code', 'ASC')
               ->orderBy('course_regs.session', 'ASC')
                ->select('course_regs.*')
                ->get()
                ->groupBy('semester_id');
//dd($user);
           // foreach ($user as $v) {
              /*  $r = $this->getResult($v->id);
                $studentDetails[] = ['id' => $v->id, 'course_id' => $v->course_id, 'code' => $v->course_code, 'unit' => $v->course_unit, 'r' => $r->id ? $r->id : '', 'ca' => $r->ca ? $r->ca : '',
                    'exam' => $r->exam ? $r->exam : '', 'total' => $r->total ? $r->total : ''];*/

   /* $studentDetails[] = ['id' => $v->id, 'course_id' => $v->course_id, 'code' => $v->course_code, 'unit' => $v->course_unit];
           
            }*/
           /* if(Auth::user()->faculty_id == Self::DENTISTRY && $level > 2 || Auth::user()->faculty_id == Self::MEDICINE && $level > 2 ){
                return view('desk.register_student.student_details_medicine')->with('s',$studentDetails)->with('session',$session)
                ->with('level',$level)->with('u',$u)->with('season',$season);
            }else{*/
            return view('desk.register_student.student_details')->with('s',$studentDetails)->with('session',$session)
                ->with('level',$level)->with('u',$u)->with('season',$season);
          //  }
        } else {
            dd('something went wrong. contact system admin.');
        }
    }

        //============================= registered_student_detail_update========================
        public function registered_student_detail_update(Request $request, $user_id = null, $level = null, $session = null, $season = null)
        {
    
            if (isset($user_id) && isset($level) && isset($session) && isset($season)) {
                $season = $season;
                $user_id = $user_id;
                $level = $level;
                $session = $session;
                $studentDetails = array();
               // $resultCourseregId =array();
                $u = DB::connection('mysql2')->table('users')->find($user_id);

                $studentDetails = DB::connection('mysql2')->table('student_regs')
                    ->join('course_regs', 'course_regs.studentreg_id', '=', 'student_regs.id')
                    ->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
                    ->where([['student_regs.level_id', $level], ['student_regs.session', $session], ['student_regs.season', $season],
                        ['student_regs.user_id', $user_id]])
                    ->where([['course_regs.user_id', $user_id], ['course_regs.level_id', $level], ['course_regs.session', $session], ['course_regs.period', $season]])
                    ->where([['student_results.level_id', $level], ['student_results.session', $session], ['student_results.season', $season],
                        ['student_results.user_id', $user_id]])
                    ->orderBy('course_regs.semester_id', 'ASC')
                    ->orderBy('course_regs.course_code', 'ASC')
                    ->orderBy('course_regs.session', 'ASC')
                    ->select('course_regs.*','student_results.ca','student_results.exam','student_results.total','student_results.grade','student_results.scriptNo','student_results.approved','student_results.id as r')
                    ->get()
                    ->groupBy('semester_id');
                   /* if(Auth::user()->faculty_id == Self::DENTISTRY && $level !=1 || Auth::user()->faculty_id == Self::MEDICINE && $level !=1 ){
                        return view('desk.register_student.student_details_update_medicine')->with('s',$studentDetails)->with('session',$session)
                        ->with('level',$level)->with('u',$u)->with('season',$season);
                    }else{*/
    
                return view('desk.register_student.student_details_update')->with('s',$studentDetails)->with('session',$session)
                ->with('level',$level)->with('u',$u)->with('season',$season);
                  //  }
            } else {
                dd('something went wrong. contact system admin.');
            }
        }

        //============================= registered_student_detail_update any========================
        public function registered_student_detail_update_any(Request $request, $user_id = null, $level = null, $session = null, $season = null)
        {
    
            if (isset($user_id) && isset($level) && isset($session) && isset($season)) {
                $season = $season;
                $user_id = $user_id;
                $level = $level;
                $session = $session;
                $studentDetails = array();
                $resultCourseregId =array();
                $u = DB::connection('mysql2')->table('users')->find($user_id);

                $studentDetails = DB::connection('mysql2')->table('student_regs')
                    ->join('course_regs', 'course_regs.studentreg_id', '=', 'student_regs.id')
                    ->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
                    ->where([['student_regs.level_id', $level], ['student_regs.session', $session], ['student_regs.season', $season],
                        ['student_regs.user_id', $user_id]])
                    ->where([['course_regs.user_id', $user_id], ['course_regs.level_id', $level], ['course_regs.session', $session], ['course_regs.period', $season]])
                    ->where([['student_results.level_id', $level], ['student_results.session', $session], ['student_results.season', $season],
                        ['student_results.user_id', $user_id]])
                    ->orderBy('course_regs.semester_id', 'ASC')
                    ->orderBy('course_regs.course_code', 'ASC')
                    ->orderBy('course_regs.session', 'ASC')
                    ->select('course_regs.*','student_results.ca','student_results.exam','student_results.total','student_results.scriptNo','student_results.approved','student_results.id as r')
                    ->get()
                    ->groupBy('semester_id');
                   
    
                return view('desk.register_student.student_details_update_any')->with('s',$studentDetails)->with('session',$session)
                        ->with('level',$level)->with('u',$u)->with('season',$season);
            } else {
                dd('something went wrong. contact system admin.');
            }
        }
            //============================= registered_student_detail delete========================
    public function registered_student_detail_delete(Request $request, $user_id = null, $level = null, $session = null, $season = null)
    {

        if (isset($user_id) && isset($level) && isset($session) && isset($season)) {
            $season = $season;
            $user_id = $user_id;
            $level = $level;
            $session = $session;
            $studentDetails = array();
           
            $u = DB::connection('mysql2')->table('users')->find($user_id);

            $studentDetails = DB::connection('mysql2')->table('student_regs')
                    ->join('course_regs', 'course_regs.studentreg_id', '=', 'student_regs.id')
                    ->leftjoin('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
                
                    ->where([ ['student_regs.season', $season],
                        ['student_regs.user_id', $user_id]])
                    ->where([['course_regs.user_id', $user_id], ['course_regs.period', $season]])
                    ->orderBy('student_regs.session','ASC')
                    ->orderBy('course_regs.semester_id', 'ASC')
                    ->orderBy('course_regs.course_code', 'ASC')
                   ->select('course_regs.*','student_results.ca','student_results.exam','student_results.approved','student_results.total')
                    ->get()
                    ->groupBy('session');
//dd($studentDetails);
          
            return view('desk.register_student.student_details_delete')->with('s',$studentDetails)->with('session',$session)
                        ->with('level',$level)->with('u',$u)->with('season',$season);
        } else {
            dd('something went wrong. contact system admin.');
        }
    }
    //================================= Entering Result =====================================================
    public function enter_result(Request $request)
    {
        $url = url()->previous();
        if ($request->input('delete') == 'delete') {
            $ch = $request->input('chk');
           
            if ($ch == null) {
                Session::flash('warning', "you did not select any course to delete.");
                return back();
            } else {
                foreach ($ch as $v) {
                    
                    $sr = StudentResult::where([['coursereg_id', $v],['approved','!=',2]])->delete();
                    if($sr == 0){
                    $srb = StudentResultBackup::where('coursereg_id', $v)->delete();
                    $cr = CourseReg::destroy($v);
                    }
                }
                Session::flash('success', "SUCCESSFUL.");
            }

            return redirect($url);
        }
        if(Auth::user()->resultRight != 1){
            Session::flash('warning', "You need result upload right to upload or update any result. Click Result Upload Right button.");
            return back();
        }
      //  $fos = $this->get_fos();
        $l = $this->get_level();
      //  $semester = $this->get_semester();
        $faculty_id = $request->input('faculty_id');
      //  $fos_id = $request->input('fos_id');
        $semester_id = $request->input('semester_id');
        $season = $request->input('season');
      //  $entry_year = $request->input('entry_year');
        $l_id = $request->input('level_id');
        $session = $request->input('session_id');
        $user_id = $request->input('user_id');
        $mat_no = $request->input('matric_number');

        $variable = $request->input('total');

        $flag = "Sessional";
        $date = date("Y/m/d H:i:s");

        foreach ($variable as $k => $v) {

            $xc = explode('~', $k);
            $v = strtoupper($v);
            if (!empty($v)) {
                if ($faculty_id == Self::MEDICINE || $faculty_id == Self::DENTISTRY) {
                    $grade_value = $this->get_grade_medicine($v, $season, $l);
                } else {
                    $grade_value = $this->get_grade($v);
                }
                $grade = $grade_value['grade'];

                $size = count($xc);
                if (4 == $size) {
                    //UPDATE EXISTING RESULT
                    $result_id = $xc[0];
                    $coursereg_id = $xc[1];
                    $course_id = $xc[2];
                    $cu = $xc[3];
                    $x = $this->mm($grade, $cu);

                    $ca = $request->input('ca')[$result_id];

                    $exam = $request->input('exam')[$result_id];

                    $update = StudentResult::find($result_id);
                    if ($update->total != $v && $update->approved != 2) // only updates when the total is different
                    {
// for update_result table
                        $ur = new UpdateResult;
                        $ur->ca =$update->ca;
                        $ur->exam =$update->exam;
                        $ur->total =$update->total;
                        $ur->user_id = $update->user_id;
                        $ur->student_results_id= $update->id;
                        $ur->former_deskofficer_id =$update->examofficer;
                       $ur->posted = $update->post_date;
                        $ur->save();

                        $update->ca = $ca;
                        $update->exam = $exam;
                        $update->total = $v;
                        $update->grade = $grade;
                        $update->cp = $x['cp'];
                        $update->examofficer = Auth::user()->id;
                        $update->post_date = $date;
                        $update->save();

                        

                    }

                } else {
                    //INSERT FRESH RESULT
                    $coursereg_id = $xc[0];
                    $course_id = $xc[1];
                    $cu = $xc[2];
                    $x = $this->mm($grade, $cu);

                    $ca = $request->input('ca')[$coursereg_id];
                    $exam = $request->input('exam')[$coursereg_id];
                    $cp = $x['cp'];

                    $check_result = StudentResult::where([['user_id', $user_id], ['level_id', $l_id], ['session', $session], ['course_id', $course_id], ['coursereg_id', $coursereg_id]])->first();
                    if ($check_result == null) {
                        $insert_data[] = ['user_id' => $user_id, 'matric_number' => $mat_no, 'course_id' => $course_id, 'coursereg_id' => $coursereg_id, 'ca' => $ca, 'exam' => $exam, 'total' => $v, 'grade' => $grade, 'cu' => $cu, 'cp' => $x['cp'], 'level_id' => $l_id, 'session' => $session, 'semester' => $semester_id, 'status' => 0, 'season' => $season, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date' => $date, 'approved' => 0];
                    }

                }

            }
        }
        if (isset($insert_data)) {
            if (count($insert_data) > 0) {
                DB::connection('mysql2')->table('student_results')->insert($insert_data);
            }
        }

        Session::flash('success', "SUCCESSFUL.");
        return redirect($url);
        // return redirect()->action('DeskController@post_register_student',['fos_id'=>$fos_id,'level'=>$l_id,'semester_id'=>$semester_id,'session'=>$session,'season'=>$season]);
    }



       //================================= Post Result updated=====================================================
       public function postResult(Request $request)
       {
           $url = url()->previous();

           if ($request->input('delete') == 'delete') {
            $ch = $request->input('chk');
            $vID=array();
         
            if ($ch == null) {
                Session::flash('warning', "you did not select any course to delete.");
                return back();
            } else {
                foreach ($ch as $v) {
                    // checking if its approved
                $sr = StudentResult::where([['coursereg_id', $v],['approved',2]])->first();
                if($sr==Null)
                    $vID []=$v;
                }
                if(!empty($vID)){
              
                DB::connection('mysql2')->table('course_regs')->whereIn('id',$vID)->delete();
                DB::connection('mysql2')->table('student_results')->whereIn('coursereg_id',$vID)->delete();
                DB::connection('mysql2')->table('student_result_backups')->whereIn('coursereg_id',$vID)->delete();
                   // $cr = CourseReg::destroy($v);
                    //$sr = StudentResult::where('coursereg_id', $v)->delete();
                    //$srb = StudentResultBackup::where('coursereg_id', $v)->delete();
                    Session::flash('success', "SUCCESSFUL.");
                }else{
                Session::flash('warning', "Deleting of Courses and result grade not successful.");
                }
                }
                return redirect($url);
        }

        if ($request->input('delete') == 'deleteResult') {
            $ch = $request->input('chk');
            $vID=array();
         
            if ($ch == null) {
                Session::flash('warning', "you did not select any course to delete result.");
                return back();
            } else {
                foreach ($ch as $v) {
                    // checking if its approved
                $sr = StudentResult::where([['coursereg_id', $v],['approved',2]])->first();
                if($sr==Null)
                    $vID []=$v;
                }
                if(!empty($vID)){
                DB::connection('mysql2')->table('student_results')->whereIn('coursereg_id',$vID)->delete();
                DB::connection('mysql2')->table('student_result_backups')->whereIn('coursereg_id',$vID)->delete();
                  
                    Session::flash('success', "SUCCESSFUL.");
                }else{
                Session::flash('warning', "Deleting of result grade not successful.");
                }
                }
                return redirect($url);
        }
          
if(Auth::user()->resultRight != 1){
    Session::flash('warning', "You need result upload right to upload or update any result. Click Result Upload Right button.");
    return back();
}
          // $fos = $this->get_fos();
          // $l = $this->get_level();
           
          // $semester = $this->get_semester();
           //$faculty_id = $request->input('faculty_id');
          // $fos_id = $request->input('fos_id');
           $season = $request->input('season');
         //  $entry_year = $request->input('entry_year');
           $l_id = $request->input('level_id');
           $session = $request->input('session_id');
           $user_id = $request->input('user_id');
           $mat_no = $request->input('matric_number');
          $variable = $request->input('total');
   
           $flag = "Sessional";
           $date = date("Y/m/d H:i:s");
           $insert_data =array();
           $updateany = $request->input('updateany');
           if($updateany == null){
            // check if the student have register higher session
     $stdreg =StudentReg::where([['user_id',$user_id],['session','>',$session]])->count();
 
     if($stdreg > 0){
         Session::flash('warning', "You can not enter grade in a lower session again.");
         return back(); 
     }
 }
   
           foreach ($variable as $k => $v) {
   
               $xc = explode('~', $k);
               $v = strtoupper($v);
               $size = count($xc);
               if (!empty($v)) { 
                   
                /*   if (Auth::user()->faculty_id == Self::MEDICINE && $l_id > 2 || Auth::user()->faculty_id == Self::DENTISTRY && $l_id > 2) {
                    $grade =$v;

                    if (4 == $size) {
                        //UPDATE EXISTING RESULT
                        $result_id = $xc[0];
                        $coursereg_id = $xc[1];
                        $course_id = $xc[2];
                        $scriptNo = $request->input('scriptNo')[$result_id];
                        $update = StudentResult::find($result_id);

                        if ($update->total != $v && $update->approved != 2) // only updates when the total is different
                        {
                          // for update_result table
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
                            $update->grade = $grade;
                            $update->examofficer = Auth::user()->id;
                            $update->post_date = $date;
                            $update->save();
    
                            //delete corrected courses
    
                            if($updateany == 1)
                            {
                                $cr = CourseReg::where([['course_id', $course_id],['level_id','>',$l_id],['user_id',$user_id]])->delete();
                                $sr = StudentResult::where([['course_id', $course_id],['level_id','>',$l_id],['user_id',$user_id]])->delete();
                                $srb = StudentResultBackup::where([['course_id', $course_id],['level_id','>',$l_id],['user_id',$user_id]])->delete();
                            }
                        }
    
                    } else {
                   
                           //INSERT FRESH RESULT
                           $coursereg_id = $xc[0];
                           $course_id = $xc[1];
                           $semester_id = $request->input('semester_id')[$coursereg_id];
                           $scriptNo = $request->input('scriptNo')[$coursereg_id];
                           $check_result = StudentResult::where([['user_id', $user_id], ['level_id', $l_id], ['session', $session], ['course_id', $course_id], ['coursereg_id', $coursereg_id]])->first();
                           if ($check_result == null) {
                               $insert_data[] = ['user_id' => $user_id, 'matric_number' => $mat_no, 'scriptNo'=>$scriptNo, 'course_id' => $course_id, 'coursereg_id' => $coursereg_id, 'ca' => 2, 'exam' => 2, 'total' => 2, 'grade' => $grade, 'cu' => 2, 'cp' => 2, 'level_id' => $l_id, 'session' => $session, 'semester' => $semester_id, 'status' => 0, 'season' => $season, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date' => $date, 'approved' => 0];
                           }
       
                       }


                   }else{*/
            
                   if (Auth::user()->faculty_id == Self::MEDICINE || Auth::user()->faculty_id == Self::DENTISTRY) {
                       
                       $grade_value = $this->get_grade_medicine($v,$season,$l_id);
                     
                   } else {
                       $grade_value = $this->get_grade($v);
                   }
                   $grade = $grade_value['grade'];
                   
                  

                   if (4 == $size) {
                    //UPDATE EXISTING RESULT
                    $result_id = $xc[0];
                    $coursereg_id = $xc[1];
                    $course_id = $xc[2];
                    $cu = $xc[3];
                    $x = $this->mm($grade, $cu);
                    $scriptNo = $request->input('scriptNo')[$result_id];
                    $ca = $request->input('ca')[$result_id];
                    $exam = $request->input('exam')[$result_id];
                    $update = StudentResult::find($result_id);
                   
                    if ($update->total != $v && $update->approved != 2) // only updates when the total is different
                    {
                    
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
                        $update->total = $v;
                        $update->grade = $grade;
                        $update->cp = $x['cp'];
                        $update->examofficer = Auth::user()->id;
                        $update->post_date = $date;
                        $update->save();

                        //delete corrected courses

                        if($updateany == 1)
                        {
                            $cr = CourseReg::where([['course_id', $course_id],['level_id','>',$l_id],['user_id',$user_id]])->delete();
                            $sr = StudentResult::where([['course_id', $course_id],['level_id','>',$l_id],['user_id',$user_id]])->delete();
                            $srb = StudentResultBackup::where([['course_id', $course_id],['level_id','>',$l_id],['user_id',$user_id]])->delete();
                        }
                    }

                } else {
               
                       //INSERT FRESH RESULT
                       $coursereg_id = $xc[0];
                       $course_id = $xc[1];
                       $cu = $xc[2];
                       $x = $this->mm($grade, $cu);
                       $semester_id = $request->input('semester_id')[$coursereg_id];
                       $scriptNo = $request->input('scriptNo')[$coursereg_id];
                       $ca = $request->input('ca')[$coursereg_id];
                       $exam = $request->input('exam')[$coursereg_id];
                       $cp = $x['cp'];
   
                       $check_result = StudentResult::where([['user_id', $user_id], ['level_id', $l_id], ['session', $session], ['course_id', $course_id], ['coursereg_id', $coursereg_id]])->first();
                       if ($check_result == null) {
                           $insert_data[] = ['user_id' => $user_id, 'matric_number' => $mat_no, 'scriptNo'=>$scriptNo, 'course_id' => $course_id, 'coursereg_id' => $coursereg_id, 'ca' => $ca, 'exam' => $exam, 'total' => $v, 'grade' => $grade, 'cu' => $cu, 'cp' => $x['cp'], 'level_id' => $l_id, 'session' => $session, 'semester' => $semester_id, 'status' => 0, 'season' => $season, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date' => $date, 'approved' => 0];
                       }
   
                   }
                //}
            }
           }
          
          if($size == 4){
            Session::flash('success', "Update of result  SUCCESSFUL."); 
          }else{
           
           if (isset($insert_data)) {
               if (count($insert_data) > 0) {
                   DB::connection('mysql2')->table('student_results')->insert($insert_data);
                   Session::flash('success', "SUCCESSFUL.");
               }else{
                Session::flash('warning', "Result insert array is empty.");
               }
           }
        }
           
           return redirect($url);
           // return redirect()->action('DeskController@post_register_student',['fos_id'=>$fos_id,'level'=>$l_id,'semester_id'=>$semester_id,'session'=>$session,'season'=>$season]);
       }









    //================================= get Register Student=====================================================
    public function get_register_student($fos_id = null, $l_id = null, $semester_id = null, $session = null, $season = null)
    {
        if (isset($fos_id) && isset($l_id) && isset($semester_id) && isset($session) && isset($season)) {
            $fos = $this->get_fos();
            $l = $this->get_level();
            $semester = $this->get_semester();
            $p = $this->p();
            $p = $this->p();
            if ($p == 0) {
                $foses = Fos::find($fos_id);
                $p = $foses->programme_id;
            }
            $f = $this->f();
            $d = $this->d();
            $prob_user_id = $this->getprobationStudents($p, $d, $f, $l_id, $session);
            $user = DB::connection('mysql2')->table('users')
                ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
                ->where('users.fos_id', $fos_id)
                ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['student_regs.season', $season],
                    ['student_regs.session', $session], ['student_regs.semester', $semester_id], ['student_regs.level_id', $l_id]])
                ->whereNotIn('users.id', $prob_user_id)
                ->select('student_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.fos_id', 'users.entry_year')
                ->orderBy('users.matric_number', 'ASC')
                ->get();

            return view('desk.register_student')->with('u',$user)->with('ss',$session)->with('f',$fos)->with('l',$l)->with('s',$semester)
                ->with('l_id',$l_id)->with('s_id',$semester_id);

        }
    }
// ======================================more enter result =============================================
    public function more_result(Request $request)
    {

        $variable = $request->input('id');
        $user = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
            ->whereIn('student_regs.id', $variable)
            ->select('student_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.fos_id')
            ->get();
        return view('desk.more_result')->with('u',$user);
    }

    // ======================================more enter result =============================================
    public function post_more_result(Request $request)
    {
        $id = $request->input('id');
        $fos_id = $request->input('fos_id');
        $variable = $request->input('grade');
        $flag = "Sessional";
        $date = date("Y/m/d H:i:s");

        foreach ($variable as $k => $v) {
            $xc = explode('~', $k);
            $v = strtoupper($v);

            if (!in_array($v, array('A', 'B', 'C', 'D', 'E', 'F'))) {
                continue;
            }

            if (!empty($v)) {

                $size = count($xc);
                if (11 == $size) {
                    //UPDATE EXISTING RESULT
                    $result_id = $xc[0];
                    $coursereg_id = $xc[1];
                    $user_id = $xc[2];
                    $l_id = $xc[3];
                    $semester_id = $xc[4];
                    $session = $xc[5];
                    $season = $xc[6];
                    $course_id = $xc[7];
                    $cu = $xc[8];
                    $mat_no = $xc[9];
                    $studentreg_id[] = $xc[10];
                    $x = $this->mm($v, $cu);

                    $update = StudentResult::find($result_id);
                    $update->grade = $v;
                    $update->cp = $x['cp'];
                    $update->save();

                } else {
                    //INSERT FRESH RESULT
                    $coursereg_id = $xc[0];
                    $user_id = $xc[1];
                    $l_id = $xc[2];
                    $semester_id = $xc[3];
                    $session = $xc[4];
                    $season = $xc[5];
                    $course_id = $xc[6];
                    $cu = $xc[7];
                    $mat_no = $xc[8];
                    $studentreg_id[] = $xc[9];
                    $x = $this->mm($v, $cu);

                    $check_result = StudentResult::where([['user_id', $user_id], ['level_id', $l_id], ['session', $session], ['course_id', $course_id], ['coursereg_id', $coursereg_id]])->first();
                    if (count($check_result) == 0) {
                        $insert_data[] = ['user_id' => $user_id, 'matric_number' => $mat_no, 'course_id' => $course_id, 'coursereg_id' => $coursereg_id, 'grade' => $v, 'cu' => $cu, 'cp' => $x['cp'], 'level_id' => $l_id,
                            'session' => $session, 'semester' => $semester_id, 'status' => 0, 'season' => $season, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date' => $date, 'approved' => 0];
                    }

                }

            }
        }

        if (isset($insert_data)) {
            if (count($insert_data) > 0) {
                DB::connection('mysql2')->table('student_results')->insert($insert_data);
            }
        }
        Session::flash('success', "SUCCESSFUL.");

        $user = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
            ->whereIn('student_regs.id', $studentreg_id)
            ->select('student_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.fos_id')
            ->get();
        return view('desk.more_result')->with('u',$user);
    }
//=========================== enter result by course ===============================================
    public function e_result()
    {$fos = $this->get_fos();
        $l = $this->get_level();
        $semester = $this->get_semester();
        return view('desk.e_result')->with('f',$fos)->with('l',$l)->with('s',$semester)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }

    //================================== post result by course =============================================
    public function e_result_next(Request $request)
    {$fos = $this->get_fos();
        $l = $this->get_level();
        $semester = $this->get_semester();
        $fos_id = $request->input('fos');
        $l_id = $request->input('level');
        $s_id = $request->input('session');
        $semester_id = $request->input('semester');
        $rc = RegisterCourse::where([['semester_id', $semester_id], ['level_id', $l_id], ['fos_id', $fos_id], ['session', $s_id]])->get();
        return view('desk.e_result_next')->with('rc',$rc)->with('l_id',$l_id)->with('s_id',$s_id)->with('sm_id',$semester_id)->with('f',$fos)->with('l',$l)->with('s',$semester)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }

    //================================== get student  by course =============================================
    public function e_result_c(Request $request)
    {
        
        $id = $request->input('id');
        $period = $request->input('period');
        $result_type = $request->input('result_type');
        $registercourse = RegisterCourse::find($id);
        $p = $registercourse->programme_id;
        $d = $registercourse->department_id;
        $f = $registercourse->faculty_id;
        $l = $registercourse->level_id;
        $s = $registercourse->session;
        $sm = $registercourse->semester_id;
       // $prob_user_id = $this->getprobationStudents($p, $d, $f, $l, $s);
        if($request->excel =='excel')
        {
        return view('desk.excelUpload.index')->with('f',$f)->with('c',$registercourse)->with('rt',$result_type)->with('med',self::MEDICINE)->with('den',self::DENTISTRY)->with('period',$period);

        }
      $update ='';
        if($request->update =='update')
        {
            $user_with_no_result = $this->student_with_no_result($id, $period);
           $user = DB::connection('mysql2')->table('users')
                ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
                ->where('course_regs.registercourse_id', $id)
                ->where('course_regs.period', $period)
               // ->whereNotIn('users.id', $prob_user_id)
                ->whereNotIn('users.id', $user_with_no_result)
                ->orderBy('users.matric_number', 'ASC')
                ->select('course_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.entry_year')
                ->get();
           // return view('desk.excelUpload.index')->withF($f)->withC($registercourse)->withRt($result_type)->withMed(self::MEDICINE)->withPeriod($period);
$update = $request->update;
        }else{
      

        if ($result_type == "Omitted") {
            //$user_with_no_result = $this->student_with_no_result($id, $period);
            $studentWithResult = $this->student_with_result($registercourse->course_id,$registercourse->fos_id,$s,$sm,$period);
            $user = DB::connection('mysql2')->table('users')
                ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
                ->where('course_regs.registercourse_id', $id)
                ->where('course_regs.period', $period)
                ->whereNotIn('users.id', $studentWithResult)
              //  ->whereNotIn('users.id', $prob_user_id)
                ->orderBy('users.matric_number', 'ASC')
                ->select('course_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.entry_year')
                ->get();
        } elseif ($result_type == "Correctional") {
            $studentWithResult = $this->student_with_result($registercourse->course_id,$registercourse->fos_id,$s,$sm,$period);
            /*  $result =DB::connection('mysql2')->table('student_results')
            ->where([['coursereg_id',$v->id],['approved',1]])->first();*/

            $user = DB::connection('mysql2')->table('users')
                ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
                ->join('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
                ->where('course_regs.registercourse_id', $id)
                ->where('course_regs.period', $period)
                ->where('student_results.approved', 1)
                ->whereIn('users.id', $studentWithResult)
              //  ->whereNotIn('users.id', $prob_user_id)
                ->orderBy('users.matric_number', 'ASC')
                ->select('course_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.entry_year')
                ->get();

        } else {
            $studentWithResult = $this->student_with_result($registercourse->course_id,$registercourse->fos_id,$s,$sm,$period);
            $user = DB::connection('mysql2')->table('users')
                ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
                ->where('course_regs.registercourse_id', $id)
                ->where('course_regs.period', $period)
               // ->whereNotIn('users.id', $prob_user_id)
                ->whereNotIn('users.id', $studentWithResult)
                ->orderBy('users.matric_number', 'ASC')
                ->select('course_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.entry_year')
                ->get();
        }
    }
        //dd($user);
        //Get current page form url e.g. &page=6
        $url = "e_result_c?id=" . $id . '&period=' . $period . '&result_type=' . $result_type;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        //Create a new Laravel collection from the array data
        $collection = new Collection($user);

        //Define how many items we want to be visible in each page
        $perPage = 50;

        //Slice the collection to get the items to display in current page
        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        //Create our paginator and pass it to the view
        $paginatedSearchResults = new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);

        // return view('search', ['results' => $paginatedSearchResults]);

        return view('desk.e_result_c')->with('u',$paginatedSearchResults)->with('url',$url)->with('c',$registercourse)->with('rt',$result_type)->with('Up',$update)->with('f',$f)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);

    }

//========================================== result insert for student percourse ==========================

    public function insert_result(Request $request)
    {
        if(Auth::user()->resultRight != 1){
            Session::flash('warning', "You need result upload right to upload or update any result. Click Result Upload Right button");
            return back();
        }
        $this->validate($request, array('id' => 'required'));
        $url = $request->input('url');
        $id = $request->input('id');
        
        if ($request->input('delete') == 'delete') {

            if ($id == null) {
                Session::flash('warning', "you did not select any course to delete.");
                return back();
            } else {
              
                foreach ($id as $key => $value) {
                    $v = $request->input('coursereg_id')[$value];
                    // $user_id =$request->input('user_id')[$value];
// appeoved with 2 is sbc approval
                    $sr = StudentResult::where([['coursereg_id', $v],['approved','!=',2]])->delete();
                
                    if($sr != 0){
                    $srb = StudentResultBackup::where('coursereg_id', $v)->delete();
                    $cr = CourseReg::destroy($v);
                    }
                }

                Session::flash('success', "Delete of courses successful.");
            }

            return redirect($url);
        }
      
        $flag = $request->input('flag');
        $faculty_id = $request->input('faculty_id');
        $date = date("Y/m/d H:i:s");
        $result_id = "";

        foreach ($id as $key => $value) {
            $coursereg_id = $request->input('coursereg_id')[$value];
            $user_id = $request->input('user_id')[$value];
            $mat_no = $request->input('matric_number')[$value];
            $course_id = $request->input('course_id')[$value];
            $cu = $request->input('cu')[$value];
            $session = $request->input('session')[$value];
            $semester = $request->input('semester')[$value];
            $l_id = $request->input('level_id')[$value];
            $season = $request->input('season')[$value];
            $script = $request->input('scriptNo')[$value];
            $ca = $request->input('ca')[$value];
            $exam = $request->input('exams')[$value];
            //$total=$request->input('total')[$value];
            $total = $ca + $exam;
            $entry_year = $request->input('entry_year')[$value];
            if ($faculty_id == Self::MEDICINE || $faculty_id == Self::DENTISTRY) {
                $grade_value = $this->get_grade_medicine($total, $season, $l_id);
            } else {
                $grade_value = $this->get_grade($total);
            }

            $grade = $grade_value['grade'];
            $cp = $this->mm($grade, $cu);
            //  $result_id =$request->input('result_id')[$value];
            //if($request->has('result_id'.[$value])) {

            $result_id = $request->input('result_id')[$value];

            //  }
            //check ca, exams, total
            if ($ca == '') {$ca = 0;}
            if ($exam == '') {$exam = 0;}
            if ($total == '') {$total = 0;}

            if (!empty($result_id)) {

                $update = StudentResult::find($result_id);

//================ correctional result ==================
                if ($flag == 'Correctional') {
                    $reason = $request->input('reason');
                    // check these back up result, if scores exist already
                    $check = StudentResultBackup::where([['level_id', $l_id], ['session', $session], ['course_id', $course_id], ['coursereg_id', $coursereg_id], ['user_id', $update->user_id]])
                        ->first();

                    // update back table if records exist
                    if ($check != null) {
                        $check->grade = $update->grade;
                        $check->cp = $update->cp;
                        $check->reason = $update->reason;
                        $check->save();
                    } else {
                        // insert if records doest not exist
                        $srb = new StudentResultBackup;
                        $srb->user_id = $update->user_id;
                        $srb->matric_number = $update->matric_number;
                        $srb->coursereg_id = $update->coursereg_id;
                        $srb->course_id = $update->course_id;
                        $srb->grade = $update->grade;
                        $srb->cu = $update->cu;
                        $srb->cp = $update->cp;
                        $srb->session = $update->session;
                        $srb->semester = $update->semester;
                        $srb->level_id = $update->level_id;
                        $srb->status = 0;
                        $srb->season = $update->season;
                        $srb->reason = $update->reason;
                        $srb->save();

                    }

                } // end of correctional result
                if($update->approved != 2){
                    $ur = new UpdateResult;
                    $ur->ca =$update->ca;
                    $ur->exam =$update->exam;
                    $ur->total =$update->total;
                    $ur->user_id = $update->user_id;
                    $ur->student_results_id= $update->id;
                    $ur->former_deskofficer_id =$update->examofficer;
                   $ur->posted = $update->post_date;
                    $ur->save();

                $update->scriptNo = $script;
                $update->ca = $ca;
                $update->exam = $exam;
                $update->total = $total;
                $update->grade = $grade;
                $update->flag = $flag;
                $update->cp = $cp['cp'];
                $update->save();
                }
            } else {

                $check_result = StudentResult::where([['user_id', $user_id], ['matric_number', $mat_no], ['level_id', $l_id], ['session', $session], ['course_id', $course_id], ['coursereg_id', $coursereg_id]])->first();
                if ($check_result == null) {
                    $insert_data[] = ['user_id' => $user_id, 'matric_number' => $mat_no, 'scriptNo' => $script, 'course_id' => $course_id, 'coursereg_id' => $coursereg_id, 'ca' => $ca, 'exam' => $exam, 'total' => $total, 'grade' => $grade, 'cu' => $cu, 'cp' => $cp['cp'], 'level_id' => $l_id,
                        'session' => $session, 'semester' => $semester, 'status' => 0, 'season' => $season, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date' => $date, 'approved' => 0];
                }

            }

        }

        if (isset($insert_data)) {
            if (count($insert_data) > 0) {
                DB::connection('mysql2')->table('student_results')->insert($insert_data);
            }
        }
        Session::flash('success', "SUCCESSFUL.");
        return back();
        //return redirect($url);
    }

    //========================  excel insert result ==========================

    
    public function excel_insert_result(Request $request)
    {
        if(Auth::user()->resultRight != 1){
            Session::flash('warning', "You need result upload right to upload or update any result. Click Result Upload Right button");
            return back();
        }
        $errors =array();

        
        if($request->file('excel_import_result'))
        {
            
            try {
        $path = $request->file('excel_import_result');
    
 Excel::import(new UsersImport($request->all()),$path);
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


      //========================  excel insert result gss ==========================

    
      public function excel_insert_result_gss(Request $request)
      {
          /*if(Auth::user()->resultRight != 1){
              Session::flash('warning', "You need result upload right to upload or update any result. Click Result Upload Right button");
              return back();
          }*/
          $errors =array();
  
          
          if($request->file('excel_import_result'))
          {
            $path = $request->file('excel_import_result');
            if($request->insert != null){        
              try {
   
   Excel::import(new UserGssImport($request->all()),$path);
   Session::flash('success', "SUCCESSFUL.");
      } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
          $failures = $e->failures();
          
          foreach ($failures as $failure) {
              $failure->row(); // row that went wrong
              $failure->attribute(); // either heading key (if using heading row concern) or column index
              $errors[] = $failure->errors(); // Actual error messages from Laravel validator
              $failure->values(); // The values of the row that has failed.
          }
          
      }
    }elseif($request->update != null){
        try {
   
            Excel::import(new UserGssUpdateImport($request->all()),$path);
            Session::flash('success', "SUCCESSFUL.");
               } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                   $failures = $e->failures();
                   
                   foreach ($failures as $failure) {
                       $failure->row(); // row that went wrong
                       $failure->attribute(); // either heading key (if using heading row concern) or column index
                       $errors[] = $failure->errors(); // Actual error messages from Laravel validator
                       $failure->values(); // The values of the row that has failed.
                   }
                   
               }
    }
  
      
          return back();
           }
      }

      public function excel_insert_result_gssII(Request $request)
      {
          /*if(Auth::user()->resultRight != 1){
              Session::flash('warning', "You need result upload right to upload or update any result. Click Result Upload Right button");
              return back();
          }*/
          $errors =array();
  
          
          if($request->file('excel_import_result'))
          {
            $path = $request->file('excel_import_result');
            if($request->insert != null){        
              try {
   
   Excel::import(new UserGssImportII($request->all()),$path);
   Session::flash('success', "SUCCESSFUL.");
      } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
          $failures = $e->failures();
          
          foreach ($failures as $failure) {
              $failure->row(); // row that went wrong
              $failure->attribute(); // either heading key (if using heading row concern) or column index
              $errors[] = $failure->errors(); // Actual error messages from Laravel validator
              $failure->values(); // The values of the row that has failed.
          }
          
      }
    }elseif($request->update != null){
        try {
   
            Excel::import(new UserGssUpdateImportII($request->all()),$path);
            Session::flash('success', "SUCCESSFUL.");
               } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                   $failures = $e->failures();
                   
                   foreach ($failures as $failure) {
                       $failure->row(); // row that went wrong
                       $failure->attribute(); // either heading key (if using heading row concern) or column index
                       $errors[] = $failure->errors(); // Actual error messages from Laravel validator
                       $failure->values(); // The values of the row that has failed.
                   }
                   
               }
    }
}elseif($request->view !=null)
    {
        return Excel::download(new ResultDownloadGssExportII($request->all()), 'ers.xlsx');
     
    }
  
      
          return back();
           
      }

//--------------------------------------------view result --------------------------------------------------
    public function view_result()
    {
        $fos = $this->get_fos();
        $l = $this->get_level();
        $semester = $this->get_semester();
        return view('desk.view_result')->with('f',$fos)->with('l',$l)->with('s',$semester);
    }

//----------------------------------------------------------------------------------

    public function post_view_result(Request $request)
    {

        $fos = $this->get_fos();
        $l_id = $this->get_level();
        $semester_id = $this->get_semester();

        $this->validate($request, array('fos' => 'required', 'session' => 'required', 'level' => 'required', 'semester' => 'required'));
        $f_id = $request->input('fos');
        $l = $request->input('level');
        $semester = $request->input('semester');
        $session = $request->input('session');
        $d = Auth::user()->department_id;
        $f = Auth::user()->faculty_id;
        $p = Auth::user()->programme_id;

        $course = DB::table('register_courses')
        ->where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $f_id], ['level_id', $l], ['semester_id', $semester], ['session', $session]])
        ->orderBy('reg_course_status', 'ASC')
        ->orderBy('reg_course_code', 'ASC')
        ->get();

        return view('desk.view_result')->with('c',$course)->with('sm',$semester)->with('si',$session)->with('li',$l)->with('f',$fos)->with('l',$l_id)->with('s',$semester_id)->with('f_id',$f_id);

    }

//-----------------------------------------display result ----------------------------------------------------

    public function view_result_detail(Request $request)
    {
        $id = $request->input('id');
        $xc = explode('~', $id);
        $reg_id = $xc[0];
        $course_id = $xc[1];
        $course_code = $xc[2];
        $fos_id = $request->input('fos_id');
        $l = $request->input('level');
        $sm = $request->input('semester');
        $s = $request->input('session');
        $period = $request->input('period');

        $user = DB::connection('mysql2')->table('users')
            ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
            ->leftjoin('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
            ->where([['course_regs.registercourse_id', $reg_id], ['course_regs.level_id', $l], ['course_regs.semester_id', $sm], ['course_regs.session', $s], ['course_regs.course_id', $course_id], ['course_regs.period', $period]])
          //  ->where([ ['student_results.level_id', $l], ['student_results.semester', $sm], ['student_results.session', $s],['student_results.season', $period]])
           
            ->orderBy('users.matric_number', 'ASC')
            ->select('course_regs.*','student_results.ca','student_results.exam','student_results.total','student_results.grade', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number')
            ->get();

        return view('desk.view_result_detail')->with('u',$user)->with('sm',$sm)->with('s',$s)->with('l',$l)->with('fos_id',$fos_id)->with('course_code',$course_code);
    }

    //--------------------------------------------delete result --------------------------------------------------
    public function delete_result()
    {
        $fos = $this->get_fos();
        $l = $this->get_level();
        $semester = $this->get_semester();
        return view('desk.delete_result')->with('f',$fos)->with('l',$l)->with('s',$semester);
    }

//----------------------------------------------------------------------------------

    public function post_delete_result(Request $request)
    {

        $fos = $this->get_fos();
        $l_id = $this->get_level();
        $semester_id = $this->get_semester();

        $this->validate($request, array('fos' => 'required', 'session' => 'required', 'level' => 'required', 'semester' => 'required'));
        $f_id = $request->input('fos');
        $l = $request->input('level');
        $semester = $request->input('semester');
        $session = $request->input('session');
        $d = Auth::user()->department_id;
        $f = Auth::user()->faculty_id;
        $p = Auth::user()->programme_id;

        $course = DB::table('register_courses')
        ->where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $f_id], ['level_id', $l], ['semester_id', $semester], ['session', $session]])
        ->orderBy('reg_course_status', 'ASC')
        ->orderBy('reg_course_code', 'ASC')
        ->get();

        return view('desk.delete_result')->with('c',$course)->with('sm',$semester)->with('si',$session)->with('li',$l)->with('f',$fos)->with('l',$l_id)->with('s',$semester_id)->with('f_id',$f_id);

    }

    public function delete_desk_result($id)
    {

        $reg = StudentResult::where([['approved','!=',2],['id',$id]])->delete();
        if($reg == 0)
        {
            Session::flash('warning', "You can not delete result Approved by SBC.");
        }else{
            Session::flash('success', "SUCCESSFUL.");
        }

        
        return redirect()->action([DeskController::class,'delete_result']);
    }

    public function delete_desk_multiple_result(Request $request)
    { $r=array();
        $variable = $request->input('id');
        if ($variable == null) {Session::flash('warning', "you have not select any result.");
            return redirect()->action([DeskController::class,'delete_result']);
        }
foreach($variable as $v)
{
    $s =StudentResult::where([['approved','!=',2],['id',$v]])->first();

    if($s != null)
    {
      $r[] =$v; 
    }
}

if(!empty($r)){
        $reg = StudentResult::destroy($r);

        Session::flash('success', "SUCCESSFUL.");
}else{
    Session::flash('warning', "No recourds to delete. may all result selected has been approved by SBC."); 
}
        return redirect()->action([DeskController::class,'delete_result']);
    }
//-----------------------------------------display result ----------------------------------------------------

    public function delete_result_detail(Request $request)
    {
        $id = $request->input('id');
        $xc = explode('~', $id);
        $reg_id = $xc[0];
        $course_id = $xc[1];
        $course_code = $xc[2];
        $fos_id = $request->input('fos_id');
        $l = $request->input('level');
        $sm = $request->input('semester');
        $s = $request->input('session');
        $period = $request->input('period');
        $ResultType = $request->input('result_type');
        // $result =DB::connection('mysql2')->table('student_results');
        $user = DB::connection('mysql2')->table('users')
            ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
            ->join('student_results', 'student_results.user_id', '=', 'users.id')
            ->where([['student_results.level_id', $l], ['student_results.semester', $sm], ['student_results.session', $s], ['student_results.course_id', $course_id], ['student_results.season', $period], ['student_results.flag', $ResultType]])
            ->where([['course_regs.registercourse_id', $reg_id], ['course_regs.level_id', $l], ['course_regs.semester_id', $sm], ['course_regs.session', $s], ['course_regs.course_id', $course_id], ['course_regs.period', $period]])
            ->orderBy('users.matric_number', 'ASC')
            
            ->select('course_regs.id as cosId', 'student_results.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number')

            ->get();
        // dd($user);
        return view('desk.delete_result_detail')->with('u',$user)->with('sm',$sm)->with('s',$s)->with('l',$l)->with('fos_id',$fos_id)->with('course_code',$course_code)->with('result_type',$ResultType);
    }

    //================= resultUploadRight============================
    public function resultUploadRight()
    {
       $token = $this->generateRandomString(6);
       $newDateTime = Carbon::now()->addMinutes(60);
       $email =Auth::user()->email;
       if($email == null){
    
        Session::flash('warning', 'your account has no email. contact system admistrator!');
       }else{
       $user_id =Auth::user()->id;
       $now = Carbon::now();
       
       $check = DB::table('result_upload_rights')->where('user_id',$user_id)->whereDate('expired_time',$now)
       ->whereTime('expired_time','>',$now)->first();
       
       if($check != null)
       {
           Session::flash('warning', 'check you email.the code sent has not expired');
       }else{


      DB::table('result_upload_rights')->insert([
        'user_id' => $user_id, 
        'token' => $token, 
        'expired_time' => $newDateTime
      ]);
      
/*$data = array('email' => $email, 'body' => $token);
    Mail::send('emails.custom', $data, function($message) use($data){
        $message->to($data['email']);
        $message->subject('Result upload Right');
    
    });*/
    Mail::to($email)->send(new Custom($token));

    if(Mail::failures() != 0) {
        Session::flash('success', 'We have e-mailed your upload right code!');
    }

    else {
        Session::flash('warning', 'Email failed to send');
    }

    
}


}
    
return view('desk.resultUploadRight.index'); 

    }
    public function uploadRight(Request $request)
    {
$token =$request->token;
$user_id =Auth::user()->id;
$now = Carbon::now();

$check = DB::table('result_upload_rights')->where([['user_id',$user_id],['token',$token]])->whereDate('expired_time',$now)
       ->whereTime('expired_time','>',$now)->first();
  
if($check != null){
    DB::table('result_upload_rights')->where('user_id',$user_id)->delete();
    $affected = DB::table('users')
              ->where('id', $user_id)
              ->update(['resultRight' => 1]);
              $role =$this->getrolename($user_id);
DB::table('result_upload_right_log')->insert([
                'user_id' => $user_id, 
                'role' => $role,
                'department_id'=>Auth::user()->department_id,
                'date' => $now
              ]);
              
              Session::flash('success', 'You account has been activated, you can Enter Result now.');
}else{
    Session::flash('warning', 'Please check your code again or its has expired.'); 
}
return view('desk.resultUploadRight.confirm');
    }

    //=============.=============== REPORT ==============================================================
    //------------------------------ Report methods ------------------------------------------------------
    public function departmentreport()
    {
        $p = $this->getp();
        return view('desk.hod.index')->with('p',$p)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }
    public function report()
    {
        $fos = $this->get_fos();
        $p = $this->getp();
        $f = $this->get_faculty();
        return view('desk.report.index')->with('f',$fos)->with('fc',$f)->with('p',$p);
    }

    public function post_report(Request $request)
    {
        if ($request->input('result_type') == 0) {
            Session::flash('warning', "you did not select result type.");
            return back();
        }
        $sfos = $request->sfos;
        $dvc = 0;
        $approval = 0;
        $correctionName =$request->correctionName;
        if(isset($request->approval))
        {
         $approval =$request->approval;
        }
        
        if (isset($request->admin)) {
//$fos_id =$request->input('fos');
            $d = $request->department_id;
            $f = $request->faculty_id;
            $foss = Fos::find($request->input('fos'));
            $p = $foss->programme_id;
            

        }elseif(isset($request->dvc)){
            $dvc =$request->dvc;
            $user_dvc =DB::connection('mysql2')->table('users')->where('matric_number',$request->matricNumber)->first();
           
            
            if($user_dvc == null)
            {
                Session::flash('warning', "Student Records does not exist. contact system admin to find out if the student is in old portal");
            return back(); 
            }
            $d = $user_dvc->department_id;
            $f = $user_dvc->faculty_id;
            $p = $user_dvc->programme_id;
            $foss = Fos::find($user_dvc->fos_id);
            $fos =$user_dvc->fos_id;
        }
        else {
            $d = Auth::user()->department_id;
            $f = Auth::user()->faculty_id;
            $p = Auth::user()->programme_id;
            $foss = Fos::find($request->input('fos'));
        }

        if ($p == 0) {
         //   $foss = Fos::find($request->input('fos'));
            $p = $foss->programme_id;
           // $p = $request->p;
        }
       
//$user_id = Auth::user()->id;
        $flag = "Sessional";
        $perPage = $request->input('page_number');
if(isset($request->dvc)){
    $this->validate($request, array('session' => 'required', 'level' => 'required', 'result_type' => 'required'));
      
}else{
    $this->validate($request, array('fos' => 'required', 'session' => 'required', 'level' => 'required', 'result_type' => 'required'));
      
}

         $regc1 = '';
        $reg2c = '';
        if($dvc == 0){
        $fos = $request->input('fos');
        }
        

        $s = $request->input('session');
        $l = $request->input('level');
        $final = '';
        if ($l != 1) {
            $sl = explode('~', $l);
            $l = $sl[0];
            if (isset($sl[1])) {
                $final = $sl[1];
            }
        }

        $result_type = $request->input('result_type');
        $duration = $request->input('duration');
        if ($request->selected == 6) {
            $final = $request->input('final');
        }

        if ($request->selected == 8) {
            $final = $request->input('final');
        }
      
// select student type report

        if ($result_type == 6) {
           
            $users = $this->selectedRegisteredStudents($p, $d, $f, $fos, $l, $s,$sfos);
            return view('desk.report.selectStudent')
            ->with('fos',$fos)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('users',$users)->with('flag',$flag)
            ->with('f',$f)->with('d',$d)->with('final',$final);
        }
        if ($result_type == 8) {
            
            $users = $this->selectedCorrectionalNameStudents($p, $d, $f, $fos);
            return view('desk.report.selectCorrection')
            ->with('fos',$fos)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('users',$users)->with('flag',$flag)
            ->with('f',$f)->with('d',$d)->with('final',$final);
        }

        if($final == 's'){}
        else{
            if($sfos == 0){
        $regcourse1C = $this->getRegisteredCourses($p, $d, $f, $fos, $l, $s, 1, 'C');
            }else{
            $regcourse1C = $this->getRegisteredCoursesSpecialization($p, $d, $f, $fos,$sfos, $l, $s, 1, 'C');  
            }
            
// jumb register courses for second semester for medicine
        if ($f == self::MEDICINE && $l > 2 || $f == self::DENTISTRY && $l > 2) {} else {

            if($sfos == 0){
                $regcourse2C = $this->getRegisteredCourses($p, $d, $f, $fos, $l, $s, 2, 'C');
                    }else{
                    $regcourse2C = $this->getRegisteredCoursesSpecialization($p, $d, $f, $fos,$sfos, $l, $s, 2, 'C');  
                    }
        }
    }

    
// seasional result

        if ($result_type == 12) {
            // resit students
            $season ='RESIT';
            if ($request->selected == 6) {
                
                if($dvc == 1){
                    $selectedID[] =$user_dvc->id;  
                }else{
                $selectedID = $request->ids;
                }

                $users = $this->SitRegisteredStudentselectedRes($selectedID, $l, $s, $season);
            } else {
            $users = $this->getResitRegisteredStudents($p, $d, $f, $fos, $l, $s,$season ,$perPage);
            }
        } elseif ($result_type == 7) {

            $title = "SUMMER VACATION";
            $season = "VACATION";
            $flag = "Sessional";
            if ($request->selected == 6) {
                if($dvc == 1){
                    $selectedID[] =$user_dvc->id;  
                }else{
                $selectedID = $request->ids;
                }

                $users = $this->SelectedResitRegisteredStudents($selectedID, $l, $s, $season);
            } else {

                $users = $this->getResitRegisteredStudents($p, $d, $f, $fos, $l, $s, $season,$perPage);
            }
        } elseif ($result_type == 5) {

            $title = "LONG VACATION";
            $season = "VACATION";
            $flag = "Sessional";
            if ($request->selected == 6) {
                if($dvc == 1){
                    $selectedID[] =$user_dvc->id;  
                }else{
                $selectedID = $request->ids;
                }

                $users = $this->SelectedResitRegisteredStudents($selectedID,$l,$s,$season);
            } else {

                $users = $this->getResitRegisteredStudents($p,$d,$f,$fos,$l,$s,$season,$perPage);
                
                
            }
        } elseif ($result_type == 4) {
            $title = "CORRECTIONAL";
            $season = "NORMAL";
            $flag = "Correctional";
            if ($request->selected == 6) {
                if($dvc == 1){
                    $selectedID =$user_dvc->id;  
                }else{
                $selectedID = $request->ids;
                }
                $users = $this->SelectedStudentsWithFlag($selectedID, $l, $s, $flag);
            } else {
                $users = $this->getRegisteredStudentsWithFlag($p, $d, $f, $fos, $l, $s, $flag,$perPage);
                //dd($users);
            }
        } elseif ($result_type == 2) {
            $flag = "Omitted";
            $title = "OMITTED";
            $season = "NORMAL";
            if ($request->selected == 6) {
                if($dvc == 1){
                    $selectedID[] =$user_dvc->id;  
                }else{
                $selectedID = $request->ids;
                }
                
                $users = $this->SelectedStudentsWithFlag($selectedID, $l, $s, $flag);
            } else {
                $users = $this->getRegisteredStudentsWithFlag($p, $d, $f, $fos, $l, $s, $flag,$perPage);
            }
        } 
        elseif ($result_type == 14) {

           if($f== self::AGRIC && $foss->duration == 5 && $l ==3 || $f== self::AGRIC && $foss->duration == 4 && $l ==2)
           {
            $title = "DELAYED";
            $season = "NORMAL";
            if ($request->selected == 6) {
                if($dvc == 1){
                    $selectedID[] =$user_dvc->id;  
                }else{
                $selectedID = $request->ids;
                }
                
                $users = $this->SelectedStudentsWithFlag($selectedID, $l, $s, $flag);
            } else {
                $users = $this->getRegisteredProbationStudentsForReport($p, $d, $f, $fos, $l, $s,$perPage);

                
            }
        }else{dd('check the parameters you selected');}
        }
        
        elseif ($result_type == 1) {
          
           if($f== self::AGRIC)
           {
            if($foss->duration == 5 && $l ==4)
            {
            $title = "INDUSTRIAL ATTACHEMENT";
            }elseif($foss->duration == 4 && $l ==3){
            $title = "INDUSTRIAL ATTACHEMENT";
            }else{
                $title = "SESSIONAL";
            }

           }else{
            $title = "SESSIONAL";
           }
            if ($final != '') {
                $title = "DEGREE";
            }
            
            $season = "NORMAL";
            if ($request->selected == 6) {
                if($dvc == 1){
                    $selectedID[] =$user_dvc->id;  
                }else{
                $selectedID = $request->ids;
                }
                 
                $users = $this->SelectedStudentsWithFlag($selectedID, $l, $s, $flag);

            } else {
                if($sfos == 0){
                    $users = $this->getRegisteredStudents($p,$d,$f,$fos,$l,$s,$perPage);
                   }else{
                    $users = $this->getRegisteredStudentsSpecialization($p, $d, $f, $fos,$sfos, $l, $s,$perPage);
                }
            
            }
        } elseif ($result_type == 9) {
            $title = "MOP UP (2020)";
            $season = "NORMAL";
            $ps = $s - 1;
            $regcourse1C = $this->getRegisteredCourses($p, $d, $f, $fos, $l, $ps, 1, 'C');
            $regcourse2C = $this->getRegisteredCourses($p, $d, $f, $fos, $l, $ps, 2, 'C');
            $users = $this->getRegisteredMopUpStudentsForReport($p, $d, $f, $fos, $l, $s,$perPage);

        
        } elseif ($result_type == 3) {
            $title = "PROBATIONAL";
            $season = "NORMAL";
            $ps = $s - 1;
            $regcourse1C = $this->getRegisteredCourses($p, $d, $f, $fos, $l, $ps, 1, 'C');
            $regcourse2C = $this->getRegisteredCourses($p, $d, $f, $fos, $l, $ps, 2, 'C');
            $users = $this->getRegisteredProbationStudentsForReport($p, $d, $f, $fos, $l, $s,$perPage);

        }elseif ($result_type == 11) {
                 $title = "SESSIONAL";
                if ($final != '') {
                    $title = "DEGREE";
                 }
                 $season = "NORMAL";
                 if ($request->selected == 6) {
                     if($dvc == 1){
                         $selectedID[] =$user_dvc->id;  
                     }else{
                     $selectedID = $request->ids;
                     }
                    $users = $this->SelectedStudentsWithFlag($selectedID, $l, $s, $flag);
     
                 } else {
                    if($sfos == 0){
                     $users = $this->getRegisteredStudents($p,$d,$f,$fos,$l,$s,$perPage);
                    }else{
                     $users = $this->getRegisteredStudentsSpecialization($p, $d, $f, $fos,$sfos, $l, $s,$perPage);
                 }
                }
                
        } else {
            if($sfos == 0){
                $users = $this->getRegisteredStudents($p,$d,$f,$fos,$l,$s,$perPage);
               }else{
                $users = $this->getRegisteredStudentsSpecialization($p, $d, $f, $fos,$sfos, $l, $s,$perPage);
            }
        }

        $course_id1 = array();
        $course_id2 = array();
        $regc1 = array();
        $reg2c = array();
        $no1C =0;
        $no2C=0;
        
        if( $final == 's'){}
        else{
// medicine and level 3 above
        if ($f == self::MEDICINE && $l > 2 || $f == self::DENTISTRY && $l > 2) {
            if (empty($regcourse1C)) {
                echo '<h3>You have not registered  the courses for these session</h3>';
                dd();
            }
        } else {
            if (empty($regcourse1C && $regcourse2C)) {
                echo '<h3>You have not registered  the courses for these session </h3>';
                dd();
            }
           
        }
// medicine and level 3 above
        if ($f == self::MEDICINE && $l > 2 || $f == self::DENTISTRY && $l > 2) {
            foreach ($regcourse1C as $key => $value) {
                $regc1[] = $value;
                $course_id1[] = $value->course_id;
            }
            $no1C = count($regcourse1C);
            $no2C = 0;
        } else {
            foreach ($regcourse1C as $key => $value) {
                $regc1[] = $value;
                $course_id1[] = $value->course_id;
            }

            foreach ($regcourse2C as $key => $value) {
                $reg2c[] = $value;
                $course_id2[] = $value->course_id;
            }

            $no1C = count($regcourse1C);
            $no2C = count($regcourse2C);
        }
    }
//Get current page form url e.g. &page=6
        $url = url()->full();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        //Create a new Laravel collection from the array data
        //$collection = new Collection($users);

        //Slice the collection to get the items to display in current page
       // $currentPageSearchResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        //Create our paginator and pass it to the view
        $paginatedSearchResults = $users;//new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
        

        if ($result_type == 11) {
            // ------- sessional
            $title = "SESSIONAL";
            $season = "NORMAL";

            return view('desk.report.sessional_diploma')
            ->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
            ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
            ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
        } elseif ($result_type == 12) {
            // ------- sessional
            $title = "RESIT";
            $season = "RESIT";
            if ($l != 1) {
                $sl = explode('~', $l);
                $l = $sl[0];
            }
            return view('desk.report.resit_diploma')
            ->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
            ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
            ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
        }elseif ($result_type == 9) {
            return view('desk.report.mopup_report')
            ->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
            ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
            ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
        }elseif ($result_type == 3) {
            return view('desk.report.probation_report')
            ->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
            ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
            ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
        } elseif ($result_type == 14) {
            return view('desk.report.delayed_report')
            ->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
            ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
            ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
        }
        
        elseif ($result_type == 5) {
            if ($f == Self::MEDICINE && $l > 2 || $f == self::DENTISTRY && $l > 2) {
                return view('desk.report.resit_clinical_report')
                ->with('fos',$fos)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
                ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
                ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
            }
            return view('desk.report.long_vacation_report')
            ->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
            ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
            ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
        } elseif ($result_type == 7) {
            return view('desk.report.summer_vacation_report')
            ->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
            ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
            ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
        } else {

            if ($f == Self::MEDICINE && $l < 3 || $f == self::DENTISTRY && $l < 3) {
                return view('desk.report.pre_medical_report')->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
                ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
                ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
            
            } elseif ($f == Self::MEDICINE && $l > 2 || $f == self::DENTISTRY && $l > 2) {
                return view('desk.report.clinical_report')
                ->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
                ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
                ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
          
            }
            if($correctionName == 1){
               
                return view('desk.report.correctionName.display_report')->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
                ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
                ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);
       
            }
            return view('desk.report.display_report')->with('sfos',$sfos)->with('fos',$foss)->with('l',$l)->with('s',$s)->with('duration',$duration)->with('t',$title)->with('n1c',$no1C)->with('n2c',$no2C)->with('regc1',$regc1)
            ->with('regc2',$reg2c)->with('users',$users)->with('flag',$flag)->with('season',$season)->with('course_id1',$course_id1)->with('course_id2',$course_id2)->with('u',$paginatedSearchResults)
            ->with('url',$url)->with('page',$perPage)->with('pn',$perPage)->with('f',$f)->with('d',$d)->with('final',$final)->with('cpage',$currentPage)->with('approval',$approval);

        }

    }

    // ======================== probation result fuction ==================================

    public function enter_probation_result()
    {
        $fos = $this->get_fos();
        $p = $this->getp();
        $l = $this->get_level();

        return view('desk.result.probation.index')->with('f',$fos)->with('l',$l)->with('p',$p);
    }
    // ===========================enter_probation_result_next ==========================================
    public function enter_probation_result_next(Request $request)
    {
        $fos = $this->get_fos();
        $l = $this->get_level();
        $pp = $this->getp();
       // $semester = $this->get_semester();
        $season = $request->input('season');
        $fos_id = $request->input('fos');
        $l_id = $request->input('level');
        $session = $request->input('session_id');
        $p = $request->input('p');
        if ($p == null) {
            $p = $this->p();
        }

        $f = $this->f();
        $d = $this->d();
        $user = $this->getRegisteredProbationStudents($p, $d, $f, $fos_id, $l_id, $session, $season);

        return view('desk.result.probation.index')->with('u',$user)->with('ss',$session)->with('f',$fos)->with('l',$l)
            ->with('l_id',$l_id)->with('p',$pp)->with('season',$season);
    }

    //================================= probation Entering Result =====================================================
    public function probation_enter_result(Request $request)
    {
        $fos = $this->get_fos();
        $l = $this->get_level();
        $fos_id = $request->input('fos_id');
        $season = $request->input('season');
        $entry_year = $request->input('entry_year');
        $l_id = $request->input('level_id');
        $session = $request->input('session_id');
        $user_id = $request->input('user_id');
        $mat_no = $request->input('matric_number');
        $programme_id = $request->input('programme_id');

        $variable = $request->input('total');

        $flag = "Sessional";
        $date = date("Y/m/d H:i:s");

        foreach ($variable as $k => $v) {

            $xc = explode('~', $k);
            $v = strtoupper($v);
            if (!empty($v)) {
                $grade_value = $this->get_grade($v);
                $grade = $grade_value['grade'];
                $size = count($xc);
                if (4 == $size) {
                    //UPDATE EXISTING RESULT
                    $result_id = $xc[0];
                    $coursereg_id = $xc[1];
                    $course_id = $xc[2];
                    $cu = $xc[3];
                    $x = $this->mm($grade, $cu);

                    $ca = $request->input('ca')[$result_id];
                    $semester_id = $request->input('semester_id')[$result_id];

                    $exam = $request->input('exam')[$result_id];

                    $update = StudentResult::find($result_id);
                    if ($update->total != $v) // only updates when the total is different
                    {
                        // $update->exam =$semester;
                        $update->ca = $ca;
                        $update->exam = $exam;
                        $update->total = $v;
                        $update->grade = $grade;
                        $update->cp = $x['cp'];
                        $update->examofficer = Auth::user()->id;
                        $update->post_date = $date;
                        $update->save();
                    }

                } else {
                    //INSERT FRESH RESULT
                    $coursereg_id = $xc[0];
                    $course_id = $xc[1];
                    $cu = $xc[2];
                    $x = $this->mm($grade, $cu);
                    $semester_id = $request->input('semester_id')[$coursereg_id];
                    $ca = $request->input('ca')[$coursereg_id];
                    $exam = $request->input('exam')[$coursereg_id];
                    $cp = $x['cp'];

                    $check_result = StudentResult::where([['user_id', $user_id], ['level_id', $l_id], ['session', $session], ['course_id', $course_id], ['coursereg_id', $coursereg_id]])->first();
                    if ($check_result == null) {
                        $insert_data[] = ['user_id' => $user_id, 'matric_number' => $mat_no, 'course_id' => $course_id, 'coursereg_id' => $coursereg_id, 'ca' => $ca, 'exam' => $exam, 'total' => $v, 'grade' => $grade, 'cu' => $cu, 'cp' => $x['cp'], 'level_id' => $l_id, 'session' => $session, 'semester' => $semester_id, 'status' => 0, 'season' => $season, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date' => $date, 'approved' => 0];
                    }

                }

            }
        }
        if (isset($insert_data)) {
            if (count($insert_data) > 0) {
                DB::connection('mysql2')->table('student_results')->insert($insert_data);
            }
        }
        Session::flash('success', "SUCCESSFUL.");
        return redirect()->action('DeskController@get_register_probation_student', ['programme_id' => $programme_id, 'fos_id' => $fos_id, 'l_id' => $l_id, 'session' => $session, 'season' => $season]);
    }
    //================================= get Register Student=====================================================
    public function get_register_probation_student($programme_id = null, $fos_id = null, $l_id = null, $session = null, $season = null)
    {
        if (isset($fos_id) && isset($l_id) && isset($session) && isset($season)) {
            $fos = $this->get_fos();
            $l = $this->get_level();
            //$p =$this->p();
            $p = $this->getp();
            $f = $this->f();
            $d = $this->d();

            $user = $this->getRegisteredProbationStudents($programme_id, $d, $f, $fos_id, $l_id, $session, $season);

            return view('desk.result.probation.index')
                ->with('u',$user)->with('ss',$session)->with('f',$fos)->with('l',$l)
            ->with('l_id',$l_id)->with('p',$p);

        }
    }

// ------------------------------      custom methods---------------------------------------------------
    public function getRegisteredCourses($p, $d, $f, $fos, $l, $s, $sm, $sts)
    {
       // $reg = DB::table('register_courses')
        $reg =RegisterCourse::where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos], ['level_id', $l], ['session', $s], ['semester_id', $sm], ['reg_course_status', $sts]])
            ->orderBy('reg_course_code', 'ASC')
            ->get();
        if (count($reg) > 0) {
            return $reg;
        }
        return '';

    }
    //---------------------------------------------------------------------------------------
    // get registered students
    public function getRegisteredStudents($p, $d, $f, $fos, $l, $s,$perpage)
    {
        // get student that did probation
        $prob_user_id = array();
        $omitted_array = array();
        $corrected_array = array();
        if(self::MEDICINE || self::DENTISTRY){}else{
        $prob_user_id = $this->getprobationStudents($p, $d, $f, $l, $s);
        }
//$omitted =$this->getOmittedResultStudents($p,$d,$f,$fos,$l,$s);
      /*  $omitted = $this->getStudentsWithFlag($p, $d, $f, $fos, $l, $s, "Omitted");
        $correctional = $this->getStudentsWithFlag($p, $d, $f, $fos, $l, $s, "Correctional");

        if ($omitted != null) {
            foreach ($omitted as $key => $value) {
                $omitted_array[] = $value->id;
            }
        }

        if ($correctional != null) {
            foreach ($correctional as $key => $value) {
                $corrected_array[] = $value->id;
            }
        }*/
        if($perpage == 0){
            $users = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
           // ->join('student_results', 'users.id', '=', 'student_results.user_id')
          //  ->where([['users.programme_id',$p],['users.department_id',$d],['users.faculty_id',$f],['student_results.level_id',$l],['student_results.session',$s]])
            ->where([['users.programme_id',$p],['users.department_id',$d],['users.faculty_id',$f]])
          ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['users.fos_id', $fos], ['student_regs.level_id', $l], ['student_regs.session', $s],['student_regs.moppedUp',null]])
            ->whereNotIn('users.id', $prob_user_id)
           // ->whereNotIn('student_results.flag',['Correctional','Omitted'])
           // ->whereNotIn('users.id', $omitted_array)
           // ->whereNotIn('users.id', $corrected_array)
            ->orderBy('users.matric_number', 'ASC')
            ->distinct()
            ->select('users.*')
            ->get();

        }else{
        $users = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
           // ->join('student_results', 'users.id', '=', 'student_results.user_id')
          // ->where([['users.programme_id',$p],['users.department_id',$d],['users.faculty_id',$f],['student_results.level_id',$l],['student_results.session',$s]])
            ->where([['users.programme_id',$p],['users.department_id',$d],['users.faculty_id',$f]])
           ->where([['student_regs.programme_id',$p],['student_regs.department_id',$d],['student_regs.faculty_id', $f],['users.fos_id',$fos],['student_regs.level_id',$l],['student_regs.session',$s],['student_regs.moppedUp',null]])
            ->whereNotIn('users.id', $prob_user_id)
           // ->whereNotIn('student_results.flag',['Correctional','Omitted'])
           // ->whereNotIn('users.id', $omitted_array)
           // ->whereNotIn('users.id', $corrected_array)
            ->orderBy('users.matric_number', 'ASC')
            ->distinct()
            ->select('users.*')
            ->paginate($perpage)->withQueryString();
        }

        return $users;
    }


       // get registered students
       public function selectedRegisteredStudents($p, $d, $f, $fos, $l, $s,$sfos)
       {
           // get student that did probation
           $prob_user_id = array();
          if(self::MEDICINE || self::DENTISTRY){}else{
           $prob_user_id = $this->getprobationStudents($p, $d, $f, $l, $s);
          }
   //$omitted =$this->getOmittedResultStudents($p,$d,$f,$fos,$l,$s);
        
   
         
           $users = DB::connection('mysql2')->table('users')
               ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
               ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['users.fos_id', $fos], ['student_regs.level_id', $l], ['student_regs.session', $s]])
               ->whereNotIn('users.id', $prob_user_id)
              ->orderBy('users.matric_number', 'ASC')
               ->distinct()
               ->select('users.*')
               ->get();
   
           return $users;
       }
     // get registered students
     public function selectedCorrectionalNameStudents($p, $d, $f, $fos)
     {
     $users = DB::connection('mysql2')->table('users')
             ->join('correction_names', 'users.id', '=', 'correction_names.user_id')
             ->where([['users.programme_id', $p], ['users.department_id', $d], ['users.faculty_id', $f], ['users.fos_id', $fos]])
            ->orderBy('users.matric_number', 'ASC')
             ->distinct()
             ->select('users.*')
             ->get();
 
         return $users;
     }
    //---------------------------------------------------------------------------------------
    // get registered omiited students
    public function getRegisteredOmittedStudents($p, $d, $f, $fos, $l, $s)
    {
        // get student that did probation

        $prob_user_id = array();
        $omitted_array = array();

        $prob_user_id = $this->getprobationStudents($p, $d, $f, $l, $s);

        $omitted = $this->getOmittedResultStudents($p, $d, $f, $fos, $l, $s);

        if ($omitted != null) {
            foreach ($omitted as $key => $value) {
                $omitted_array[] = $value->id;
            }
        }
        $users = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
            ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['users.fos_id', $fos], ['student_regs.level_id', $l], ['student_regs.session', $s]])
            ->whereNotIn('users.id', $prob_user_id)
            ->whereIn('users.id', $omitted_array)
            ->orderBy('users.matric_number', 'ASC')
            ->distinct()
            ->select('users.*')
            ->get();

        return $users;
    }

    // -------------- get register probation students ----------------------------------------
    public function getRegisteredProbationStudents($p, $d, $f, $fos_id, $l, $s, $season)
    {
        $prob_user_id = $this->getprobationStudents($p, $d, $f, $l, $s);
        $user = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
            ->where('users.fos_id', $fos_id)
            ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['student_regs.season', $season],
                ['student_regs.session', $s], ['student_regs.level_id', $l], ['student_regs.semester', 1]])
            ->whereIn('users.id', $prob_user_id)
            ->orderBy('users.matric_number', 'ASC')
            ->select('student_regs.*', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.fos_id', 'users.entry_year')
            ->get();

        return $user;
    }

    // -------------- get register probation students for report ----------------------------------------
    public function getRegisteredProbationStudentsForReport($p,$d,$f,$fos,$l,$s,$perpage)
    {
        $prob_user_id = $this->getprobationStudents($p, $d, $f, $l, $s);
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
    //------------------------get resit registered students-----------------------------------------------

    public function getResitRegisteredStudents($p, $d, $f, $fos, $l, $s, $season,$perpage)
    {
        if($perpage == 0){
        $users = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
            ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['users.fos_id', $fos], ['student_regs.level_id', $l], ['student_regs.session', $s],['student_regs.season', $season],['student_regs.moppedUp',null]])
            ->orderBy('users.matric_number', 'ASC')
            ->distinct()
            ->select('users.*')
            ->get();
        }else{
            $users = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
            ->where([['student_regs.programme_id', $p], ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['users.fos_id', $fos], ['student_regs.level_id', $l], ['student_regs.session', $s], ['student_regs.season', $season],['student_regs.moppedUp',null]])
            ->orderBy('users.matric_number', 'ASC')
            ->distinct()
            ->select('users.*')
            ->paginate($perpage)->withQueryString();
        }

        return $users;
    }

    public function SelectedResitRegisteredStudents($arrayId, $l, $s, $season)
    {
        $users = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
            ->where([['student_regs.level_id', $l], ['student_regs.session', $s], ['student_regs.season', $season],['student_regs.moppedUp',null]])
            ->whereIn('users.id', $arrayId)
            ->orderBy('users.matric_number', 'ASC')
            ->distinct()
            ->select('users.*')
            ->get();

        return $users;
    }

    //------------------------------ get students who are on omitted result type ---------------------------
    public function getOmittedResultStudents($p, $d, $f, $fos, $l, $s)
    {
        $users = DB::connection('mysql2')->table('users')
            ->join('student_results', 'users.id', '=', 'student_results.user_id')
            ->where([['users.programme_id', $p], ['users.department_id', $d], ['users.faculty_id', $f], ['users.fos_id', $fos], ['student_results.level_id', $l], ['student_results.session', $s],['student_results.flag', 'Omitted'],['student_regs.moppedUp',null]])
            ->orderBy('users.matric_number', 'ASC')
            ->distinct()
            ->select('users.*')
            ->get();
        return $users;
    }
    //============================================END OF REPORT=============================================

//================================== custom function =========================================================

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
    protected function p()
    {$p = Auth::user()->programme_id;
        return $p;
    }
    protected function d()
    {
        $d = Auth::user()->department_id;
        return $d;
    }

    protected function f()
    {
        $f = Auth::user()->faculty_id;
        return $f;
    }

    public function getcourse($id, $l, $f, $s)
    {
        $d = DB::table('register_courses')
            ->where([['semester_id', $id], ['level_id', $l], ['fos_id', $f], ['session', $s]])->get();
        return response()->json($d);
    }

    public function getFosPara($id)
    {
        $d = DB::table('fos')->find($id);
        return response()->json($d);
    }

    public function getLecturer($id)
    {
        $l = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->wherein('user_roles.role_id', [self::LECTURER,self::HOD,self::EXAMSOFFICER])
           ->where([['users.department_id', $id],['users.status',null]])
            ->orderBy('users.name', 'ASC')
            ->select('users.*')
            ->get();

        return response()->json($l);
    }
//========================== get result ======================
    private function getResult($id)
    {
        $result = DB::connection('mysql2')->table('student_results')
            ->where('coursereg_id', $id)
            ->first();
        return $result;

    }

    //========================= student management====================

    public function studentManagement()
    {
        return view('desk.studentManagement.index');
    }

    public function studentManagementAddCourses()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        $fos = $this->get_fos();
        return view('desk.studentManagement.addCourse')->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }

    public function getStudentManagementAddCourse(Request $request)
    {
        //$level = Level::where('programme_id', Auth::user()->programme_id)->get();
        //$semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
        $fos_id = $this->get_fos();

        $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required'));
        $session = $request->session_id;
        $fos = $request->fos;
        $l = $request->level;
        $season = $request->season;
        $p = Auth::user()->programme_id;
        $d = Auth::user()->department_id;
        $f = Auth::user()->faculty_id;
        $fos_name = Fos::find($fos);
        if($p == 0){
            $p =$fos_name->programme_id;
        }
        $level = Level::where('programme_id', $p)->get();
        $semester = Semester::where('programme_id',$p)->get();

        $prob_user_id = array();//$this->getprobationStudents($p, $d, $f, $l, $session);
        $registeStudent = $this->registerdStudents($fos, $p, $d, $f, $season, $session, $l, $prob_user_id);
        $register_course = RegisterCourse::where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos], ['level_id', $l], ['session', $session]])->orderBy('semester_id', 'ASC')->orderBy('reg_course_status', 'ASC')->get();
        return view('desk.studentManagement.addCourse')
        ->with('l',$level)->with('s',$semester)->with('f',$fos_id)->with('r',$register_course)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos)->with('fn',$fos_name)
        ->with('rs',$registeStudent)->with('season',$season)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
        
    }

    public function postStudentManagementAddCourse(request $request)
    {
        $studentsId = $request->ids;
        $regCourseId = $request->idc;
        $level = $request->level_id;
        $season = $request->season;
        $session = $request->session;
        $fos = $request->fos_id;
        $code = $request->input('code');
        $status = $request->input('status');
        $semester = $request->input('semester');
        $title = $request->input('title');
        $courseId = $request->input('course_id');
        $unit = $request->input('unit');

        if ($regCourseId == null) {
            Session::flash('warning', "courses was not selected");
            return back();
        }
        if ($studentsId == null) {
            Session::flash('warning', "students was not selected");
            return back();
        }
        $data = array();
        foreach ($studentsId as $v) {

            $course_unit = $this->getTotalCourseunit($fos, $session, $level);

            $newCourseRegTotal = 0;
            $courseRegTotalFirstSemester = $this->getTotalCourseUnitPerSemster($v, $session, 1, $level, $season);
            $courseRegTotalSecondSemester = $this->getTotalCourseUnitPerSemster($v, $session, 2, $level, $season);

            foreach ($regCourseId as $vc) {
                $checkCourse = DB::connection('mysql2')->table('course_regs')
                    ->where([['user_id', $v], ['level_id', $level], ['session', $session],
                        ['period', $season], ['registercourse_id', $vc], ['course_id', $courseId[$vc]]])
                    ->first();
                if ($checkCourse == null) {
                    $status_code = $status[$vc];
                    if ($status[$vc] == 'G') {
                        $courseReg = DB::connection('mysql2')->table('course_regs')
                            ->where([['user_id', $v], ['course_id', $courseId[$vc]], ['level_id', '<', $level]])
                            ->first();
                        if ($courseReg == null) {
                            $status_code = 'D';
                        } else {
                            $status_code = 'R';
                        }
                    }
                    // check for the total unit

                    //get student reg
                    $studentReg = DB::connection('mysql2')->table('student_regs')
                        ->where([['user_id', $v], ['level_id', $level], ['session', $session],
                            ['season', $season], ['semester', $semester[$vc]]])
                        ->first();
                    if ($semester[$vc] == 1) {
                        // first semster
                        $courseRegTotalFirstSemester += $unit[$vc];
                        //check if its drop or repeat

                        //echo $courseRegTotalFirstSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                        if ($courseRegTotalFirstSemester <= $course_unit->max) {
                            $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' => $vc, 'user_id' => $v,'fos_id'=>$fos,
                                'level_id' => $level, 'semester_id' => $semester[$vc], 'session' => $session, 'period' => $season,
                                'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                                'course_unit' => $unit[$vc], 'course_status' => $status_code];
                        }

                    } elseif ($semester[$vc] == 2) { // second semester
                        $courseRegTotalSecondSemester += $unit[$vc];

                        // echo $courseRegTotalSecondSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                        if ($courseRegTotalSecondSemester <= $course_unit->max) {
                            $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' => $vc, 'user_id' => $v,'fos_id'=>$fos,
                                'level_id' => $level, 'semester_id' => $semester[$vc], 'session' => $session, 'period' => $season,
                                'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                                'course_unit' => $unit[$vc], 'course_status' => $status_code];
                        }
                    }

                }
            }
        }

        if (count($data) != 0) {
            DB::connection('mysql2')->table('course_regs')->insert($data);
            Session::flash('success', "successful");
            return back();

        }
        Session::flash('warning', "courses is not added.Check the students total credit, you can not register more than its required unit");
        return back();

    }


    //================================= Add Repeat Course ==================
    public function studentManagementAddRepeatCourses()
    {
        $level = Level::where('programme_id', Auth::user()->programme_id)->get();
        $fos = $this->get_fos();
        return view('desk.studentManagement.addRepeatCourse')->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }
    public function getStudentManagementAddRepeatCourse(Request $request)
    {
       // $level = Level::where('programme_id', Auth::user()->programme_id)->get();
       // $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
        $fos_id = $this->get_fos();

        $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required'));
        $session = $request->session_id;
        $fos = $request->fos;
        $l = $request->level;
        $season = $request->season;
        $p = Auth::user()->programme_id;
        $d = Auth::user()->department_id;
        $f = Auth::user()->faculty_id;
        $fos_name = Fos::find($fos);
        if($p == 0){
            $p =$fos_name->programme_id;
        }
        $level = Level::where('programme_id', $p)->get();
        $semester = Semester::where('programme_id',$p)->get();

        $prob_user_id = array(); //$this->getprobationStudents($p, $d, $f, $l, $session);
        $registeStudent = $this->registerdStudents($fos, $p, $d, $f, $season, $session, $l, $prob_user_id);
        $register_course = RegisterCourse::where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos], ['level_id', $l], ['session', $session]])->orderBy('semester_id', 'ASC')->orderBy('reg_course_status', 'ASC')->get();
        return view('desk.studentManagement.addRepeatCourse')->with('l',$level)->with('s',$semester)->with('f',$fos_id)->with('r',$register_course)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos)->with('fn',$fos_name)
        ->with('rs',$registeStudent)->with('season',$season)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
    }

    public function postStudentManagementAddRepeatCourse(request $request)
    {
        $studentsId = $request->ids;
        $regCourseId = $request->idc;
        $level = $request->level_id;
        $season = $request->season;
        $session = $request->session;
        $fos = $request->fos_id;
        $code = $request->input('code');
        $status = $request->input('status');
        $semester = $request->input('semester');
        $title = $request->input('title');
        $courseId = $request->input('course_id');
        $unit = $request->input('unit');
        $next_level =$level + 1;
        $next_session =$session + 1;
        $p =Auth::user()->programme_id;
        $fos_name = Fos::find($fos);
        if($p == 0){
            $p =$fos_name->programme_id;
        }


        if ($regCourseId == null) {
            Session::flash('warning', "courses was not selected");
            return back();
        }
        if ($studentsId == null) {
            Session::flash('warning', "students was not selected");
            return back();
        }
        $data = array();
        foreach ($studentsId as $v) {

            $course_unit = $this->getTotalCourseunit($fos,$next_session,$next_level);

            $newCourseRegTotal = 0;
            $courseRegTotalFirstSemester = $this->getTotalCourseUnitPerSemster($v, $next_session, 1, $next_level, $season);
            $courseRegTotalSecondSemester = $this->getTotalCourseUnitPerSemster($v, $next_session, 2, $next_level, $season);
            $status_code ='R';
            foreach ($regCourseId as $vc) {
                $checkCourse = DB::connection('mysql2')->table('course_regs')
                    ->where([['user_id', $v], ['level_id', $next_level], ['session', $next_session],
                        ['period', $season],['course_id', $courseId[$vc]]])
                    ->first();
                if ($checkCourse == null) {
    // check result if he failed  it in last session
    $result =DB::connection('mysql2')->table('student_results')
    ->where([['user_id', $v], ['level_id', $level], ['session', $session],
    ['season', $season],['course_id', $courseId[$vc]],['grade','F']])->first();
    if($result != null ){
// if he failed the course last session,  continue
    $check_register_course =DB::connection('mysql')->table('register_courses')
    ->where([['course_id',$courseId[$vc]],['fos_id',$fos],['specialization_id',0],['session',$next_session],['level_id',$next_level],['reg_course_status',"G"]])->first();
    if($check_register_course == null)
    {
 $insert_data =['course_id'=>$courseId[$vc],'programme_id'=>$p,'department_id'=>Auth::user()->department_id,
 'faculty_id'=>Auth::user()->faculty_id,'fos_id'=>$fos,'specialization_id'=>0,'level_id'=>$next_level,'semester_id'=>$semester[$vc],
 'reg_course_title'=>$title[$vc],'reg_course_code'=>$code[$vc],'reg_course_unit'=>$unit[$vc],'reg_course_status'=>"G",'session'=>$next_session];
 
 $register_id = DB::connection('mysql')->table('register_courses')->insertGetId($insert_data);


}else{
    $register_id  =$check_register_course->id;
}                
                        

                    //get student reg
                    $studentReg = DB::connection('mysql2')->table('student_regs')
                        ->where([['user_id', $v], ['level_id', $next_level], ['session', $next_session],
                            ['season', $season], ['semester', $semester[$vc]]])
                        ->first();
                if($studentReg != null){
                    if ($semester[$vc] == 1) {
                        // first semster
                        $courseRegTotalFirstSemester += $unit[$vc];
                      
                        //echo $courseRegTotalFirstSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                        if ($courseRegTotalFirstSemester <= $course_unit->max) {
                            $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' => $register_id, 'user_id' => $v,'fos_id'=>$fos,
                                'level_id' => $next_level, 'semester_id' => $semester[$vc], 'session' => $next_session, 'period' => $season,
                                'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                                'course_unit' => $unit[$vc], 'course_status' => $status_code];
                        }

                    } elseif ($semester[$vc] == 2) { // second semester
                        $courseRegTotalSecondSemester += $unit[$vc];

                        // echo $courseRegTotalSecondSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                        if ($courseRegTotalSecondSemester <= $course_unit->max) {
                            $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' =>$register_id, 'user_id' => $v,'fos_id'=>$fos,
                                'level_id' => $next_level, 'semester_id' => $semester[$vc], 'session' => $next_session, 'period' => $season,
                                'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                                'course_unit' => $unit[$vc], 'course_status' => $status_code];
                        }
                    }

                }
            }
                }
            }
        }

        if (count($data) != 0) {
            DB::connection('mysql2')->table('course_regs')->insert($data);
            Session::flash('success', "successful");
            return back();

        }
        Session::flash('warning', "courses is not added.Check the students total credit, you can not register more than its required unit");
        return back();

    }

 //================================= Add CarryOver Course ==================
 public function studentManagementAddCarryOverCourses()
 {
     $level = Level::where('programme_id', Auth::user()->programme_id)->get();
     $fos = $this->get_fos();
     return view('desk.studentManagement.addCarryOverCourse')->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);;
 }
 public function getStudentManagementAddCarryOverCourse(Request $request)
 {
    $fos_id = $this->get_fos();
    $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required'));
     $session = $request->session_id;
     $fos = $request->fos;
     $l = $request->level;
     $season = $request->season;
     $p = Auth::user()->programme_id;
     $d = Auth::user()->department_id;
     $f = Auth::user()->faculty_id;
     $fos_name = Fos::find($fos);
     if($p == 0){
         $p =$fos_name->programme_id;
     }
     $level = Level::where('programme_id', $p)->get();
     $semester = Semester::where('programme_id',$p)->get();

     $prob_user_id =array();// $this->getprobationStudents($p, $d, $f, $l, $session);
     $registeStudent = $this->registerdStudents($fos, $p, $d, $f, $season, $session, $l, $prob_user_id);
     $register_course = DB::connection('mysql')->table('register_courses')->where([['programme_id', $p],['department_id', $d],['faculty_id', $f],['fos_id', $fos],['level_id', $l],['session', $session],['session', $session]])
     ->whereIn('reg_course_status',['C','E'])->orderBy('semester_id', 'ASC')->orderBy('reg_course_status', 'ASC')->get();
     return view('desk.studentManagement.addCarryOverCourse')
     ->with('l',$level)->with('s',$semester)->with('f',$fos_id)->with('r',$register_course)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos)->with('fn',$fos_name)
        ->with('rs',$registeStudent)->with('season',$season)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);

 }

 public function postStudentManagementAddCarryOverCourse(request $request)
 {
     $studentsId = $request->ids;
     $regCourseId = $request->idc;
     $level = $request->level_id;
     $season = $request->season;
     $session = $request->session;
     $fos = $request->fos_id;
     $code = $request->input('code');
     $status = $request->input('status');
     $semester = $request->input('semester');
     $title = $request->input('title');
     $courseId = $request->input('course_id');
     $unit = $request->input('unit');
     $next_level =$level + 1;
     $next_session =$session + 1;
     $p =Auth::user()->programme_id;
     $fos_name = Fos::find($fos);
     if($p == 0){
         $p =$fos_name->programme_id;
     }

     if ($regCourseId == null) {
         Session::flash('warning', "courses was not selected");
         return back();
     }
     if ($studentsId == null) {
         Session::flash('warning', "students was not selected");
         return back();
     }
     $data = array();
     foreach ($studentsId as $v) {

         $course_unit = $this->getTotalCourseunit($fos,$next_session,$next_level);

         $newCourseRegTotal = 0;
         $courseRegTotalFirstSemester = $this->getTotalCourseUnitPerSemster($v, $next_session, 1, $next_level, $season);
         $courseRegTotalSecondSemester = $this->getTotalCourseUnitPerSemster($v, $next_session, 2, $next_level, $season);
         $status_code ='D';
         foreach ($regCourseId as $vc) {
            $lastSessionCourseReg = DB::connection('mysql2')->table('course_regs')
            ->where([['user_id', $v], ['level_id', $level], ['session', $session],
                ['period', $season], ['registercourse_id', $vc], ['course_id', $courseId[$vc]]])
            ->first();
            if($lastSessionCourseReg == null)
            {
             $checkCourse = DB::connection('mysql2')->table('course_regs')
                 ->where([['user_id', $v], ['level_id', $next_level], ['session', $next_session],
                     ['period', $season],['course_id', $courseId[$vc]]])
                 ->first();
             if ($checkCourse == null) {

 $check_register_course =DB::connection('mysql')->table('register_courses')
 ->where([['course_id',$courseId[$vc]],['fos_id',$fos],['specialization_id',0],['session',$next_session],['level_id',$next_level],['reg_course_status',"G"]])->first();
 if($check_register_course == null)
 {
$insert_data =['course_id'=>$courseId[$vc],'programme_id'=>$p,'department_id'=>Auth::user()->department_id,
'faculty_id'=>Auth::user()->faculty_id,'fos_id'=>$fos,'specialization_id'=>0,'level_id'=>$next_level,'semester_id'=>$semester[$vc],
'reg_course_title'=>$title[$vc],'reg_course_code'=>$code[$vc],'reg_course_unit'=>$unit[$vc],'reg_course_status'=>"G",'session'=>$next_session];

$register_id = DB::connection('mysql')->table('register_courses')->insertGetId($insert_data);


}else{
 $register_id  =$check_register_course->id;
}                
                     

                 //get student reg
                 $studentReg = DB::connection('mysql2')->table('student_regs')
                     ->where([['user_id', $v], ['level_id', $next_level], ['session', $next_session],
                         ['season', $season], ['semester', $semester[$vc]]])
                     ->first();
             if($studentReg != null){
                 if ($semester[$vc] == 1) {
                     // first semster
                     $courseRegTotalFirstSemester += $unit[$vc];
                   
                     //echo $courseRegTotalFirstSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                     if ($courseRegTotalFirstSemester <= $course_unit->max) {
                         $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' => $register_id, 'user_id' => $v,'fos_id'=>$fos,
                             'level_id' => $next_level, 'semester_id' => $semester[$vc], 'session' => $next_session, 'period' => $season,
                             'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                             'course_unit' => $unit[$vc], 'course_status' => $status_code];
                     }

                 } elseif ($semester[$vc] == 2) { // second semester
                     $courseRegTotalSecondSemester += $unit[$vc];

                     // echo $courseRegTotalSecondSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                     if ($courseRegTotalSecondSemester <= $course_unit->max) {
                         $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' =>$register_id, 'user_id' => $v,'fos_id'=>$fos,
                             'level_id' => $next_level, 'semester_id' => $semester[$vc], 'session' => $next_session, 'period' => $season,
                             'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                             'course_unit' => $unit[$vc], 'course_status' => $status_code];
                     }
                 }

             }
         }
        }  
         }
     }

     if (count($data) != 0) {
         DB::connection('mysql2')->table('course_regs')->insert($data);
         Session::flash('success', "successful");
         return back();

     }
     Session::flash('warning', "courses is not added.Check the students total credit, you can not register more than its required unit");
     return back();

 }   


   //================================= Add Repeat Course 2 ==================
   public function studentManagementAddRepeatCourses2()
   {
       $level = Level::where('programme_id', Auth::user()->programme_id)->get();
       $fos = $this->get_fos();
       return view('desk.studentManagement.addRepeatCourse2')->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
   }
   public function getStudentManagementAddRepeatCourse2(Request $request)
   {
      // $level = Level::where('programme_id', Auth::user()->programme_id)->get();
      // $semester = Semester::where('programme_id', Auth::user()->programme_id)->get();
       $fos_id = $this->get_fos();

       $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required'));
       $session = $request->session_id;
       $fos = $request->fos;
       $l = $request->level;
       $season = $request->season;
       $p = Auth::user()->programme_id;
       $d = Auth::user()->department_id;
       $f = Auth::user()->faculty_id;
       $fos_name = Fos::find($fos);
       if($p == 0){
           $p =$fos_name->programme_id;
       }
       $level = Level::where('programme_id', $p)->get();
       $semester = Semester::where('programme_id',$p)->get();

       $prob_user_id = array(); //$this->getprobationStudents($p, $d, $f, $l, $session);
       $registeStudent = $this->registerdStudents($fos, $p, $d, $f, $season, $session, $l, $prob_user_id);
       $register_course = RegisterCourse::where([['programme_id', $p], ['department_id', $d], ['faculty_id', $f], ['fos_id', $fos], ['level_id', $l], ['session', $session]])->orderBy('semester_id', 'ASC')->orderBy('reg_course_status', 'ASC')->get();
       return view('desk.studentManagement.addRepeatCourse2')->with('l',$level)->with('s',$semester)->with('f',$fos_id)->with('r',$register_course)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos)->with('fn',$fos_name)
       ->with('rs',$registeStudent)->with('season',$season)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);
   }

   public function postStudentManagementAddRepeatCourse2(request $request)
   {
       $studentsId = $request->ids;
       $regCourseId = $request->idc;
       $level = $request->level_id;
       $season = $request->season;
       $session = $request->session;
       $fos = $request->fos_id;
       $code = $request->input('code');
       $status = $request->input('status');
       $semester = $request->input('semester');
       $title = $request->input('title');
       $courseId = $request->input('course_id');
       $unit = $request->input('unit');
       $next_level =$level + 2;
       $next_session =$session + 2;
       $p =Auth::user()->programme_id;
       $fos_name = Fos::find($fos);
       if($p == 0){
           $p =$fos_name->programme_id;
       }


       if ($regCourseId == null) {
           Session::flash('warning', "courses was not selected");
           return back();
       }
       if ($studentsId == null) {
           Session::flash('warning', "students was not selected");
           return back();
       }
       $data = array();
       foreach ($studentsId as $v) {

           $course_unit = $this->getTotalCourseunit($fos,$next_session,$next_level);

           $newCourseRegTotal = 0;
           $courseRegTotalFirstSemester = $this->getTotalCourseUnitPerSemster($v, $next_session, 1, $next_level, $season);
           $courseRegTotalSecondSemester = $this->getTotalCourseUnitPerSemster($v, $next_session, 2, $next_level, $season);
           $status_code ='R';
           foreach ($regCourseId as $vc) {
               $checkCourse = DB::connection('mysql2')->table('course_regs')
                   ->where([['user_id', $v], ['level_id', $next_level], ['session', $next_session],
                       ['period', $season],['course_id', $courseId[$vc]]])
                   ->first();
               if ($checkCourse == null) {
   // check result if he failed  it in last session
   $result =DB::connection('mysql2')->table('student_results')
   ->where([['user_id', $v], ['level_id', $level], ['session', $session],
   ['season', $season],['course_id', $courseId[$vc]],['grade','F']])->first();
   if($result != null ){
// if he failed the course last session,  continue
   $check_register_course =DB::connection('mysql')->table('register_courses')
   ->where([['course_id',$courseId[$vc]],['fos_id',$fos],['specialization_id',0],['session',$next_session],['level_id',$next_level],['reg_course_status',"G"]])->first();
   if($check_register_course == null)
   {
$insert_data =['course_id'=>$courseId[$vc],'programme_id'=>$p,'department_id'=>Auth::user()->department_id,
'faculty_id'=>Auth::user()->faculty_id,'fos_id'=>$fos,'specialization_id'=>0,'level_id'=>$next_level,'semester_id'=>$semester[$vc],
'reg_course_title'=>$title[$vc],'reg_course_code'=>$code[$vc],'reg_course_unit'=>$unit[$vc],'reg_course_status'=>"G",'session'=>$next_session];

$register_id = DB::connection('mysql')->table('register_courses')->insertGetId($insert_data);


}else{
   $register_id  =$check_register_course->id;
}                
                       

                   //get student reg
                   $studentReg = DB::connection('mysql2')->table('student_regs')
                       ->where([['user_id', $v], ['level_id', $next_level], ['session', $next_session],
                           ['season', $season], ['semester', $semester[$vc]]])
                       ->first();
               if($studentReg != null){
                   if ($semester[$vc] == 1) {
                       // first semster
                       $courseRegTotalFirstSemester += $unit[$vc];
                     
                       //echo $courseRegTotalFirstSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                       if ($courseRegTotalFirstSemester <= $course_unit->max) {
                           $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' => $register_id, 'user_id' => $v,'fos_id'=>$fos,
                               'level_id' => $next_level, 'semester_id' => $semester[$vc], 'session' => $next_session, 'period' => $season,
                               'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                               'course_unit' => $unit[$vc], 'course_status' => $status_code];
                       }

                   } elseif ($semester[$vc] == 2) { // second semester
                       $courseRegTotalSecondSemester += $unit[$vc];

                       // echo $courseRegTotalSecondSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                       if ($courseRegTotalSecondSemester <= $course_unit->max) {
                           $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' =>$register_id, 'user_id' => $v,'fos_id'=>$fos,
                               'level_id' => $next_level, 'semester_id' => $semester[$vc], 'session' => $next_session, 'period' => $season,
                               'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                               'course_unit' => $unit[$vc], 'course_status' => $status_code];
                       }
                   }

               }
           }
               }
           }
       }

       if (count($data) != 0) {
           DB::connection('mysql2')->table('course_regs')->insert($data);
           Session::flash('success', "successful");
           return back();

       }
       Session::flash('warning', "courses is not added.Check the students total credit, you can not register more than its required unit");
       return back();

   }

//================================= Add CarryOver Course ==================
public function studentManagementAddCarryOverCourses2()
{
    $level = Level::where('programme_id', Auth::user()->programme_id)->get();
    $fos = $this->get_fos();
    return view('desk.studentManagement.addCarryOverCourse2')->with('l',$level)->with('f',$fos)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);;
}
public function getStudentManagementAddCarryOverCourse2(Request $request)
{
   $fos_id = $this->get_fos();
   $this->validate($request, array('fos' => 'required', 'session_id' => 'required', 'level' => 'required'));
    $session = $request->session_id;
    $fos = $request->fos;
    $l = $request->level;
    $season = $request->season;
    $p = Auth::user()->programme_id;
    $d = Auth::user()->department_id;
    $f = Auth::user()->faculty_id;
    $fos_name = Fos::find($fos);
    if($p == 0){
        $p =$fos_name->programme_id;
    }
    $level = Level::where('programme_id', $p)->get();
    $semester = Semester::where('programme_id',$p)->get();

    $prob_user_id =array();// $this->getprobationStudents($p, $d, $f, $l, $session);
    $registeStudent = $this->registerdStudents($fos, $p, $d, $f, $season, $session, $l, $prob_user_id);
    $register_course = DB::connection('mysql')->table('register_courses')->where([['programme_id', $p],['department_id', $d],['faculty_id', $f],['fos_id', $fos],['level_id', $l],['session', $session],['session', $session]])
    ->whereIn('reg_course_status',['C','E'])->orderBy('semester_id', 'ASC')->orderBy('reg_course_status', 'ASC')->get();
    return view('desk.studentManagement.addCarryOverCourse2')
    ->with('l',$level)->with('s',$semester)->with('f',$fos_id)->with('r',$register_course)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos)->with('fn',$fos_name)
       ->with('rs',$registeStudent)->with('season',$season)->with('med',self::MEDICINE)->with('den',self::DENTISTRY);

}

public function postStudentManagementAddCarryOverCourse2(request $request)
{
    $studentsId = $request->ids;
    $regCourseId = $request->idc;
    $level = $request->level_id;
    $season = $request->season;
    $session = $request->session;
    $fos = $request->fos_id;
    $code = $request->input('code');
    $status = $request->input('status');
    $semester = $request->input('semester');
    $title = $request->input('title');
    $courseId = $request->input('course_id');
    $unit = $request->input('unit');
    $next_level =$level + 2;
    $next_session =$session + 2;
    $p =Auth::user()->programme_id;
    $fos_name = Fos::find($fos);
    if($p == 0){
        $p =$fos_name->programme_id;
    }

    if ($regCourseId == null) {
        Session::flash('warning', "courses was not selected");
        return back();
    }
    if ($studentsId == null) {
        Session::flash('warning', "students was not selected");
        return back();
    }
    $data = array();
    foreach ($studentsId as $v) {

        $course_unit = $this->getTotalCourseunit($fos,$next_session,$next_level);

        $newCourseRegTotal = 0;
        $courseRegTotalFirstSemester = $this->getTotalCourseUnitPerSemster($v, $next_session, 1, $next_level, $season);
        $courseRegTotalSecondSemester = $this->getTotalCourseUnitPerSemster($v, $next_session, 2, $next_level, $season);
        $status_code ='D';
        foreach ($regCourseId as $vc) {
           $lastSessionCourseReg = DB::connection('mysql2')->table('course_regs')
           ->where([['user_id', $v], ['level_id', $level], ['session', $session],
               ['period', $season], ['registercourse_id', $vc], ['course_id', $courseId[$vc]]])
           ->first();
           if($lastSessionCourseReg == null)
           {
            $checkCourse = DB::connection('mysql2')->table('course_regs')
                ->where([['user_id', $v], ['level_id', $next_level], ['session', $next_session],
                    ['period', $season],['course_id', $courseId[$vc]]])
                ->first();
            if ($checkCourse == null) {

$check_register_course =DB::connection('mysql')->table('register_courses')
->where([['course_id',$courseId[$vc]],['fos_id',$fos],['specialization_id',0],['session',$next_session],['level_id',$next_level],['reg_course_status',"G"]])->first();
if($check_register_course == null)
{
$insert_data =['course_id'=>$courseId[$vc],'programme_id'=>$p,'department_id'=>Auth::user()->department_id,
'faculty_id'=>Auth::user()->faculty_id,'fos_id'=>$fos,'specialization_id'=>0,'level_id'=>$next_level,'semester_id'=>$semester[$vc],
'reg_course_title'=>$title[$vc],'reg_course_code'=>$code[$vc],'reg_course_unit'=>$unit[$vc],'reg_course_status'=>"G",'session'=>$next_session];

$register_id = DB::connection('mysql')->table('register_courses')->insertGetId($insert_data);


}else{
$register_id  =$check_register_course->id;
}                
                    

                //get student reg
                $studentReg = DB::connection('mysql2')->table('student_regs')
                    ->where([['user_id', $v], ['level_id', $next_level], ['session', $next_session],
                        ['season', $season], ['semester', $semester[$vc]]])
                    ->first();
            if($studentReg != null){
                if ($semester[$vc] == 1) {
                    // first semster
                    $courseRegTotalFirstSemester += $unit[$vc];
                  
                    //echo $courseRegTotalFirstSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                    if ($courseRegTotalFirstSemester <= $course_unit->max) {
                        $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' => $register_id, 'user_id' => $v,'fos_id'=>$fos,
                            'level_id' => $next_level, 'semester_id' => $semester[$vc], 'session' => $next_session, 'period' => $season,
                            'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                            'course_unit' => $unit[$vc], 'course_status' => $status_code];
                    }

                } elseif ($semester[$vc] == 2) { // second semester
                    $courseRegTotalSecondSemester += $unit[$vc];

                    // echo $courseRegTotalSecondSemester.'-'.$v.'='.$vc.'='.$semester[$vc].'<br/>';
                    if ($courseRegTotalSecondSemester <= $course_unit->max) {
                        $data[] = ['studentreg_id' => $studentReg->id, 'registercourse_id' =>$register_id, 'user_id' => $v,'fos_id'=>$fos,
                            'level_id' => $next_level, 'semester_id' => $semester[$vc], 'session' => $next_session, 'period' => $season,
                            'course_id' => $courseId[$vc], 'course_title' => $title[$vc], 'course_code' => $code[$vc],
                            'course_unit' => $unit[$vc], 'course_status' => $status_code];
                    }
                }

            }
        }
       }  
        }
    }

    if (count($data) != 0) {
        DB::connection('mysql2')->table('course_regs')->insert($data);
        Session::flash('success', "successful");
        return back();

    }
    Session::flash('warning', "courses is not added.Check the students total credit, you can not register more than its required unit");
    return back();

}   

}
