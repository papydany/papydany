<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\AssignCourse;
use App\Models\Contact;
use App\Models\Course;
use App\Models\CourseReg;
use App\Models\CourseUnit;
use App\Models\Department;
use App\Models\DeskofficeFos;
use App\Models\Faculty;
use App\Models\Fos;
use App\Models\EmailBackUp;
use App\Http\Traits\MyTrait;
use App\Models\Programme;
use App\Models\PublishResult;
use App\Models\RegisterCourse;
use App\Models\Role;
use App\Models\Specialization;
use App\Models\StudentReg;
use App\Models\StudentResult;
use App\Models\User;
use App\Models\UpdateUser;
use App\Models\RegisterSpecialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Exports\UsersExport;
use App\Exports\WithdralExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use App\Exports\ErsMultiSheetExport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Mail\Reply;

# Instantiate the client.

class HomeController extends Controller
{
    use MyTrait;
    const DESKOFFICER = 3;
    const PDS = 1;
    const ModernLanguage = 7;
    const LECTURER = 5;
    const HOD = 7;
    const DVC = 'DVC';
    const EXAMSOFFICER = 4;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {/* $u =DB::connection('mysql2')->table('users')->where([['fos_id',7],['entry_year','2018']])->get();
        foreach($u as $v)
        {
            echo $v->id.',';
        }
        dd();*/
        $no='';
      
        $result=session('key');
        if($result->name =="admin" || $result->name =="support" ){
            $data =array();
        $r =StudentReg::select('user_id')->distinct()->where([['level_id',1]])->get();
     
       foreach($r as $v)
       {
           $data[] =$v->user_id;
       }
        
        $u =DB::connection('mysql2')->table('users')->whereIntegerNotInRaw('id',$data)->get();
        $no=count($u);
    }
        return view('admin.index')->with('no',$no);
    }
    //==================studentsWithOnlyProfile====================================
    public function studentsWithOnlyProfile()
    {
        $data1=array();$data=array();
        $uu =DB::connection('mysql2')->table('users')->get();
        
        $r =StudentReg::select('user_id')->distinct()->get();
       // dd($r);
          foreach($r as $v)
       {
        
$data[]=$v->user_id;
       }
     //  dd($data);
       
        
        foreach($uu as $v)
        {
            $c =in_array($v->id,$data);
            if(!$c){
                $data1[]=$v->id;
            }
        }
      
         $u =DB::connection('mysql2')->table('users')->whereIn('id',$data1)->orderBy('entry_year','desc')->get()->groupBy('faculty_id'); 
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('admin.studentsWithOnlyProfile.index')->with('u',$u)->with('f',$f);

    }
    //==============================contact mail ===================================================
    public function contactMail()
    {

        $sql = Contact::where('status', 0)->orderBy('id', 'desc')->get();
        return view('admin.contactMail')->with('c',$sql);
    }
// ======================reeply email ===================
    public function replyemail(Request $request)
    {
        $email = strtolower($request->email);
        $email = preg_replace('/\s+/', '', $email);
        $body = $request->reply;
        $emailbody = $request->reply;
        $id = $request->id;
        $ph = $request->phone;
        $phone = $request->phone;

        if ($ph[0] != 0) {
            $phone = '0' . $phone;
        }

        $sendsms = $request->sendsms;

        if ($sendsms != null) {
            $send = "UnicalDb";
            $sender = urlencode($send);
            $body = urlencode($body);
            // var_dump($sender);
            // old api
            /* $response = file_get_contents('https://bulksmspro.ng/index.php?option=com_spc&comm=spc_api&username=papydany&password=papydany7@&sender='.$sender.'&recipient='.$phone.'&message='.$body);*/

            //$response = file_get_contents('https://bulksmsa.com/index.php?option=com_spc&comm=spc_api&username=papydany&password=papydany7@&message='.$body.'&sender='.$sender.'&mobiles='.$phone);
            $response = file_get_contents('http://app.nigeriansms.com/api/?username=papydany&password=papydany7@&message='.$body.'&sender='.$sender.'&mobiles='.$phone);
           
        }

       // $data = array('email' => $email, 'body' => $emailbody);
        Mail::to($email)->send(new Reply($emailbody));

      /* Mail::send(array('html' => 'emails.reply'), $data, function ($message) use ($data) {

            $message->to($data['email'], $data['body']);
            $message->subject("Reply From Result database");
$ema
        });*/

        if(Mail::failures() != 0) {
            $c = Contact::find($id);
            $c->status = 1;
            $c->save();
            $request->session()->flash('success', 'Successful!');
            
        }

        else {
            $request->session()->flash('warning', 'failed!');
        }
        return redirect()->action([HomeController::class,'contactMail']);
       
    }
//3DVj5=wcTr.ZjEg
    //=============================admin student details ===============================
    public function admin_studentdetails(Request $request)
    {
        $matric_number = $request->matric_number;
        $users = DB::connection('mysql2')->table('users')
            ->where('matric_number', $matric_number)
            ->first();
        if ($users != null) {
            $stdReg = DB::connection('mysql2')->table('student_regs')->where('user_id', $users->id)->get();
            $pin = DB::table('pins')->where('student_id', $users->id)->get();
            $f = Faculty::get();
            return view('admin.admin_studentdetails')->with('u',$users)->with('sr',$stdReg)->with('f',$f)->with('p',$pin);
        }
        $request->session()->flash('warning', 'Students matric number does not exist');
        return redirect()->action([HomeController::class,'index']);
    }

//============================================================================================
    public function updatedepartment(Request $request)
    {

        $u = DB::connection('mysql2')->table('users')
            ->where('id', $request->user_id)
            ->update(['faculty_id' => $request->faculty_id, 'department_id' => $request->department_id, 'fos_id' => $request->fos_id]);

    }

    //====================  edit images==============================================
    /* public function edit_image($id)
    {
    $users = DB::connection('mysql2')->table('users')
    ->find($id);

    return view('admin.edit_image')->withU($users);
    }

    public function post_edit_image(Request $request)
    {
    $users = DB::connection('mysql2')->table('users')
    ->find($request->id);

    if(count($users) > 0)
    {
    if($request->hasFile('image_url')) {
    $image = $request->file('image_url');
    $filename = time() . '.' . $image->getClientOriginalExtension();

    $destinationPath = 'https://unicalexams.edu.ng/img/student';
    $img = Image::make($image->getRealPath());
    $img->resize(150, 100, function ($constraint) {
    $constraint->aspectRatio();
    })->save($destinationPath . '/' . $filename);
    $users->image_url = $filename;
    $users->save();
    Session::flash('success',"successfull.");

    }
    }

    return view('admin.edit_image')->withU($users);
    }*/
    //===============================autocomplte department =========================================
    public function autocomplete_department(Request $request)
    {
        $data = Department::select("search_name as name")->where("search_name", "LIKE", "%{$request->input('query')}%")->distinct()->get();
        return response()->json($data);
    }
    //========================= number of registered students =========================================
    public function admin_getRegStudents()
    {
        return view('admin.admin_getRegStudents');
    }
    public function post_getRegStudents(Request $request)
    {
        $s = $request->session;
        // $s_type = $request->student_type;
        $r = DB::connection('mysql2')->table('student_regs')
            ->where([['semester', 1], ['session', $s], ['season', 'NORMAL']])->get();
       $role =$this->g_rolename(Auth::user()->id);
       $st = $r->count();
$g=$r->groupBy('department_id');

       if($role == self::DVC)
       {
           if($s < '2018')
           {
               $st =$st -2000;

           }
       }
        // $st = Pin::where([['status',1],['session',$s],['student_type',$s_type]])->get()->count();

        return view('admin.admin_getRegStudents')->with('n',$st)->with('g',$g);
    }

    //========================= course registered students =========================================
    public function admin_courseRegStudents()
    {
        $fos = $this->get_fos();
        $p = $this->getp();
        $fc = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('admin.admin_courseRegStudents')->with('fc',$fc)->with('f',$fos)->with('p',$p);
    }
    public function post_courseRegStudents(Request $request)
    {
        $dd = Department::orderBy('department_name', 'ASC')->get();
        $s = $request->session;
        $fos = $request->fos;
        $d = $request->department;
        if($d== null){
            $d=Auth::user()->department_id;
        }
        $l = $request->level;
        $semester = $request->semester;
        $r =$request->registerCourse;
        $p =$request->print;
        $ers=$request->ers;
        $program = $this->getp();
        $department=Department::find($d);
        $faculty=Faculty::find($department->faculty_id);
        
        if($ers == 'ers')
{
    return Excel::download(new ErsMultiSheetExport($request->all(),$department->department_name,$faculty->faculty_name),'ers.xlsx');
    
}
        if($r == 1){
            $vId =array();
            $regDetail =array();
$regCourse =DB::table('register_courses')->where([['fos_id',$fos],['level_id',$l],['semester_id',$semester],['session',$s]])->get();
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

    return view('admin.admin_courseRegStudents1')->with('u',$gcourseReg)->with('d',$d)->with('l',$l)->with('s',$s)
    ->with('sm',$semester)->with('fos',$fos)->with('rd',$regDetail);
}elseif($p =='PDF'){
    $vId =array();
    $regDetail =array();
$regCourse =DB::table('register_courses')->where([['fos_id',$fos],['level_id',$l],['semester_id',$semester],['session',$s]])->get();
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
        }
    }

    //==================== delete course registered students ==============================
    public function delete_courseRegStudents($id)
    {
        $coureg_id = array();
        $course_reg = CourseReg::where('studentreg_id', $id)->get();
        foreach ($course_reg as $key => $value) {
            $coureg_id[] = $value->id;
            $result = StudentResult::where('coursereg_id', $value->id)->first();

            if ($result != null) {
                // delete result one after the other
                $result_delete = StudentResult::destroy($result->id);
            }
        }
        // delete course reg
        $check_delete = CourseReg::destroy($coureg_id);
// delete the student reg
        $studentreg_delete = StudentReg::destroy($id);

        Session::flash('success', "successfull.");
        return back();
    }

    public function delete_multiple_courseRegStudents(Request $request)
    {$coureg_id = array();
        $variable = $request->input('id');
        if ($variable == null) {
            return back();
        }

        $course_reg = CourseReg::whereIn('studentreg_id', $variable)->get();
        foreach ($course_reg as $key => $value) {
            $coureg_id[] = $value->id;
            $result = StudentResult::where('coursereg_id', $value->id)->first();

            if ($result != null) {
                // delete result one after the other
                $result_delete = StudentResult::destroy($result->id);
            }
        }
        // delete course reg
        $check_delete = CourseReg::destroy($coureg_id);
// delete the student reg
        $studentreg_delete = StudentReg::destroy($variable);

        Session::flash('success', "successfull.");
        return back();
    }

// ==================   view students =======================================================
    public function admin_viewStudents()
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('admin.viewstudents.admin_viewStudents')->with('f',$f);
    }
    public function post_viewStudents(Request $request)
    {
        $dd = Department::orderBy('department_name', 'ASC')->get();
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $s = $request->session;
        $fos = $request->fos;
        $d = $request->department;
        $update =$request->updateMatricno;
        $a =$request->attendance;
        $vtd=$request->vtd;
        if($a != null){
            $r =StudentReg::select('user_id')->distinct()->get();
            $u =DB::connection('mysql2')->table('users')->whereNotIn('id',$r)
            ->where([['entry_year', $s], ['department_id', $d]])->orderBy('matric_number', 'ASC')->get();
            return view('admin.viewstudents.attendance')->with('f',$f)->with('item',$u)
            ->with('d',$d)->with('s',$s);
        }
        $st = DB::connection('mysql2')->table('users')
            ->where([['entry_year', $s], ['fos_id', $fos], ['department_id', $d]])->orderBy('matric_number', 'ASC')->get();

        return view('admin.viewstudents.admin_viewStudents')->with('f',$f)->with('u',$st)->with('d',$dd)
        ->with('did',$d)->with('fosid',$fos)->with('s',$s)->with('update',$update)->with('vtd',$vtd);
    }

    public function deleteStudents(Request $request)
    {
        $variable = $request->id;
        if ($variable == null) {
            Session::flash('warning', "please select students.");
            return back();
        }
$u_id=array();
        foreach($variable as $v)
        {
           $sr =StudentResult::where('user_id',$v)->get()->count();
           if($sr == 0){
            $u_id[] =$v;
           }
        }
        if(!empty($u_id))
        {
            $cr =CourseReg::whereIn('user_id',$u_id)->delete();
            $sreg=StudentReg::whereIn('user_id',$u_id)->delete();
            $u=DB::connection('mysql2')->table('users')->whereIn('id',$u_id)->delete();

            Session::flash('success', "Success");
        }else{

            Session::flash('warning', "No students records is avalaible to delete");
        }
        return back();
    }
    //======================================== faculty =====================================
    public function new_faculty()
    {
        return view('admin.new_faculty');
    }

    //======================================== post faculty =====================================
    public function post_new_faculty(Request $request)
    {
        $this->validate($request, array('faculty_name' => 'required'));

        $check = Faculty::where('faculty_name', strtoupper($request->faculty_name))->first();
        if ($check != null) {
            Session::flash('warning', "faculty exist already.");
            return view('admin.new_faculty');
            exit();
        }
        $faculty = new Faculty;

        $faculty->faculty_name = strtoupper($request->faculty_name);

        $faculty->save();

        Session::flash('success', "SUCCESSFULL.");
        return view('admin.new_faculty');
    }

//=================================view faculty =====================================
    public function view_faculty()
    {
        //$f = Faculty::all();
        $f=DB::table('faculties')->get();
        return view('admin.view_faculty')->with('f',$f);
    }
//==============================edit faculty=================================
    public function edit_faculty($id)
    {
        $f = Faculty::find($id);
        return view('admin.edit_faculty')->with('f',$f);
    }

//==============================updatefaculty=================================
    public function post_edit_faculty(Request $request)
    {
        $id = $request->id;
        $f = Faculty::find($id);
        $f->faculty_name = strtoupper($request->faculty_name);
        $f->save();
        Session::flash('success', "SUCCESSFULL.");
        return redirect()->action([HomeController::class,'view_faculty']);
    }

//======================================== Department =====================================
    public function new_department()
    {
        $f = Faculty::all();
        return view('admin.new_department')->with('f',$f);
    }

    //======================================== post department =====================================
    public function post_new_department(Request $request)
    {
        $this->validate($request, array('faculty_id' => 'required', 'department_name' => 'required'));

        $faculty_id = $request->faculty_id;
        $department = strtoupper($request->department_name);

        $check = Department::where([['faculty_id', $faculty_id], ['department_name', $department]])->first();
        if ($check != null) {
            Session::flash('warning', "Department exist already.please");
            return $this->new_department();
            exit();
        }
        $d = new Department;

        $d->faculty_id = $faculty_id;
        $d->department_name = $department;
        $d->save();

        Session::flash('success', "SUCCESSFULL.");
        return $this->new_department();
    }
//========================================== view department=======================================

    public function view_department()
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('admin.view_department')->with('f',$f);
    }

    public function post_view_department(Request $request)
    {

        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $this->validate($request, array('faculty_id' => 'required'));
        $id = $request->faculty_id;
        $d = Department::where('faculty_id', $id)->get();
        return view('admin.view_department')->with('f',$f)->with('d',$d);
    }
//==============================edit department=================================
    public function edit_department($id)
    {
        $d = Department::find($id);
        return view('admin.edit_department')->with('d',$d);
    }

//==============================updatedepartment=================================
    public function post_edit_department(Request $request)
    {
        $id = $request->id;
        $d = Department::find($id);
        $d->department_name = strtoupper($request->department_name);
        $d->save();
        Session::flash('success', "SUCCESSFULL.");
        return redirect()->action([HomeController::class,'view_department']);
    }

//======================================== Programme =====================================
    public function new_programme()
    {
        return view('admin.new_programme');
    }

    //======================================== post programme =====================================
    public function post_new_programme(Request $request)
    {
        $this->validate($request, array('programme_name' => 'required'));

        $p = strtoupper($request->programme_name);

        $check = Programme::where('programme_name', $p)->first();
        if ($check != null) {
            Session::flash('warning', "Programme exist already.please");
            return $this->new_programme();
            exit();
        }
        $pg = new Programme;
        $pg->programme_name = $p;
        $pg->save();
        Session::flash('success', "SUCCESSFULL.");
        return $this->new_programme();
    }
//========================================== view programme=======================================

    public function view_programme()
    {
        $p = Programme::all();
        return view('admin.view_programme')->with('p',$p);
    }

//======================================== Fos =====================================
    public function new_fos()
    {

        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $p = Programme::all();
        return view('admin.new_fos')->with('f',$f)->with('p',$p);
    }

    //======================================== post fos =====================================
    public function post_new_fos(Request $request)
    {
        $this->validate($request, array(
            'fos_name' => 'required',
            'department_id' => 'required',
            'programme_id' => 'required',
            'duration' => 'required'));

        $fos = strtoupper($request->fos_name);

        $check = Fos::where('fos_name', $fos)->first();
        if ($check != null) {
            Session::flash('warning', "fos exist already.please");
            return $this->new_fos();
            exit();
        }
        $f = new Fos;
        $f->fos_name = $fos;
        $f->department_id = $request->department_id;
        $f->programme_id = $request->programme_id;
        $f->degree = $request->degree;
        $f->duration = $request->duration;
        $f->status = 0; // not assign
        $f->save();
        Session::flash('success', "SUCCESSFULL.");
        return $this->new_fos();
    }
//========================================== view fos=======================================
    public function view_fos()
    {
        $d = Department::orderBy('department_name', 'ASC')->get();
        return view('admin.view_fos')->with('d',$d);
    }
//==============================post view ============================================================
    public function post_view_fos(Request $request)
    {
        $d = Department::orderBy('department_name', 'ASC')->get();
        $this->validate($request, array('department_id' => 'required'));
        $id = $request->department_id;

        $fos = Fos::where('department_id', $id)->get();

        return view('admin.view_fos')->with('fos',$fos)->with('d',$d);
    }

//==============================edit fos=================================
    public function edit_fos($id)
    {
        $f = Fos::find($id);
        return view('admin.edit_fos')->with('f',$f);
    }

//==============================update fos=================================
    public function post_edit_fos(Request $request)
    {
        $id = $request->id;
        $f = Fos::find($id);
        $f->fos_name = strtoupper($request->fos_name);
        $f->degree = $request->degree;
        $f->duration = $request->duration;
        $f->save();
        Session::flash('success', "SUCCESSFULL.");
        return redirect()->action([HomeController::class,'view_fos']);
    }

//==============================delete fos=================================
    public function delete_fos($id, $yes = null)
    {

        $user = DB::connection('mysql2')->table('users')->where('fos_id', $id)->count();
        if ($user > 0) {
            Session::flash('warning', "these field of study have students.");
            //  return redirect(session()->get('url'));
            return back();
        }
        if ($yes != 1) {
            session()->put('url', url()->previous());
            return view('admin.regcourse.confirmation');
        }

        $assign_fos = DeskofficeFos::where('fos_id', $id)->delete();
        $course_unit = CourseUnit::where('fos', $id)->delete();
        $assign_course = AssignCourse::where('fos_id', $id)->delete();
        $rg = RegisterCourse::where('fos_id', $id)->delete();
        $fos = Fos::destroy($id);
        Session::flash('success', "SUCCESSFULL.");
        return redirect(session()->get('url'));

    }

//================================ assign fos ========================================================
    public function assign_fos()
    {
        $d = Department::orderBy('department_name', 'ASC')->get();
        return view('admin.assign_fos')->with('d',$d);
    }
//====================================post assign fos =====================================================
    public function post_assign_fos(Request $request)
    {
        $d = Department::orderBy('department_name', 'ASC')->get();
        $this->validate($request, array('department_id' => 'required'));
        $id = $request->department_id;

        $fos = Fos::where([['department_id', $id], ['status', 0]])->get();
        // $user = User::where('department_id',$id)->get();

        $user = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where('users.department_id', $id)
            ->where('user_roles.role_id', self::DESKOFFICER)
            ->select('users.*')
            ->get();

        return view('admin.assign_fos')->with('fos',$fos)->with('d',$d)->with('u',$user);
    }
//=================================================================================================================
    public function assign_fosdesk(Request $request)
    {
        $variable = $request->input('fos');
        $this->validate($request, array('deskofficer' => 'required'));
        if ($variable == null) {
            return back();
        }
        $u_id = $request->deskofficer;
// status 1 mean fos is assign and 0 mean not assigned
        foreach ($variable as $key => $value) {
            $v[] = ['fos_id' => $value, 'user_id' => $u_id, 'status' => 1];

        }

        DB::table('deskoffice_fos')->insert($v);
        foreach ($variable as $key => $value) {
            DB::table('fos')->where('id', $value)->update(['status' => 1]);
        }
        Session::flash('success', 'successfull');
        return redirect()->action([HomeController::class,'assign_fos']);
    }

//==========================specialization=======================================
    public function newSpecialization()
    {
        $fos = $this->get_fos();
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $p = Programme::all();
        return view('admin.specialization.index')->with('f',$f)->with('p',$p)->with('fos',$fos);
    }

//-------------------------- post specialization  -----------------------
    public function postSpecialization(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required',
            'programme_id' => 'required',
            'fos_id' => 'required',
            'level' => 'required'));

        $name = strtoupper($request->name);
        $check = Specialization::where([['name', $name], ['fos_id', $request->fos_id]])->first();
        if ($check != null) {
            Session::flash('warning', "Specialization exist already.please");
            return $this->newSpecialization();
            exit();
        }
        if($request->department_id)
        {
        $department =$request->department_id;
        }else{
            $department =Auth::user()->department_id;
        }
        
      
        $s = new Specialization;
        $s->name = $request->name;
        $s->department_id = $department;
        $s->programme_id = $request->programme_id;
        $s->level = $request->level;
        $s->fos_id = $request->fos_id; // not assign
        $s->save();
        Session::flash('success', "SUCCESSFULL.");
        return $this->newSpecialization();
    }
//------------------------- view specialization --------------
    public function viewSpecialization()
    {
        $fos = $this->get_fos();
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('admin.specialization.view')->with('f',$f)->with('fos',$fos);
    }
//==============================post view ============================================================
    public function postViewSpecialization(Request $request)
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $this->validate($request, array('fos_id' => 'required'));
        $fos = $this->get_fos();
        $id = $request->fos_id;
        if($request->department_id){
        $d = $request->department_id;
        }else{
            $d=Auth::user()->departemnt_id;
        }
       if($request->faculty_id){
        $fac = $request->faculty_id;
       }else{
           $fac=Auth::user()->faculty_id;
       }
        $s = Specialization::where('fos_id', $id)->get();

        return view('admin.specialization.view')->with('s',$s)->with('f',$f)->with('d',$d)->with('id',$id)
            ->with('ff',$fac)->with('fos',$fos);
    }

//---------------------------------------------------------------------
    public function editSpecialization($id)
    {
        $f = Specialization::find($id);
        return view('admin.specialization.edit')->with('f',$f);
    }

//----------------------- update specialization -------------------
    public function updateSpecialization(Request $request)
    {
        $id = $request->id;
        $f = Specialization::find($id);
        $f->name = strtoupper($request->name);
        $f->level = $request->level;
        $f->save();
        Session::flash('success', "SUCCESSFULL.");
        return redirect()->action([HomeController::class,'viewSpecialization']);
    }
// -------------------- assign specialization ----------------------
    public function assignSpecialization()
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $fos = $this->get_fos();
        return view('admin.specialization.assign')->with('f',$f)->with('fos',$fos);
    }

    public function postAssignSpecialization(Request $request)
    {
        $fos = $this->get_fos();
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $s = $request->session;
        $fos_id = $request->fos_id;
        if($request->department_id)
        {
            $d = $request->department_id;
        }else{
            $d = Auth::user()->department_id;
        }
        $sp =Specialization::where('fos_id',$fos_id)->get();
        $st = DB::connection('mysql2')->table('users')
            ->where([['entry_year', $s], ['fos_id', $fos_id], ['department_id', $d],['specialization_id',null]])
            ->orwhere([['entry_year', $s], ['fos_id', $fos_id], ['department_id', $d],['specialization_id',0]])
           
            ->orderBy('matric_number', 'ASC')->get();

        return view('admin.specialization.assign')->with('f',$f)->with('u',$st)->with('did',$d)->with('fosid',$fos_id)
        ->with('s',$s)->with('fos',$fos)->with('sp',$sp);
    }

    public function updateAssignSpecialization(Request $request)
    {
        $s = $request->specialization_id;
        
        $studentId = array();
        $variable = $request->id;
        if ($variable == null) {
            Session::flash('warning', "please select students.");
            return back();
        }

        foreach ($variable as $k) {
            $studentId[] = $k;
        }
        if (count($studentId) == 0) {
            Session::flash('warning', "students array is enpty.");
            return back();
        }
        DB::connection('mysql2')->table('users')
            ->whereIn('id', $studentId)->update(['specialization_id' => $s]);
        Session::flash('success', "students records updated.");
        return back();

    }

    public function viewAssignSpecialization()
    {
        $fos = $this->get_fos();
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('admin.specialization.viewassign')->with('f',$f)->with('fos',$fos);
    }

    public function postViewAssignSpecialization(Request $request)
    {
        $fos = $this->get_fos();
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $sfos=$request->sfos;
        $s=$request->session;
        $fos_id = $request->fos_id;
        if($request->department_id)
        {
            $d = $request->department_id;
        }else{
            $d = Auth::user()->department_id;
        }
        $sp =Specialization::where('id',$sfos)->first();
       $user = DB::connection('mysql2')->table('users')
       ->where([['specialization_id',$sfos],['entry_year',$s]])
       ->get();

        return view('admin.specialization.viewassign')->with('f',$f)->with('fos',$fos)->with('u',$user)
        ->with('did',$d)->with('fosid',$fos_id)->with('s',$s)->with('sp',$sp);
        
    }
//========================================== view desk officer=======================================
    public function new_desk_officer()
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $p = Programme::all();
        return view('admin.new_desk_officer')->with('f',$f)->with('p',$p);
    }
//====================================post desk officer =====================================================
    public function post_desk_officer(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required|string|max:255',
            'username' => 'required|unique:users',
            'password' => 'required|string|min:6',
            'faculty_id' => 'required',
            'email' => 'required',
            'department_id' => 'required',
            'programme_id' => 'required'));

        $user = new User;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->email = $request->email;
        $user->plain_password = $request->password;
        $user->faculty_id = $request->faculty_id;
        $user->department_id = $request->department_id;
        $user->programme_id = $request->programme_id;
        $user->fos_id = 0;
        $user->edit_right = 0;

        $user->save();
        $role = Role::find(3);
        $user_role = DB::table('user_roles')->insert(['user_id' => $user->id, 'role_id' => $role->id]);
        Session::flash('success', "SUCCESSFULL.");
        return redirect()->action([HomeController::class,'new_desk_officer']);

    }

    public function view_desk_officer()
    {
        $users = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', self::DESKOFFICER)
            ->where('users.department_id', '!=', 0)
            ->orderBy('username', 'ASC')
            ->select('users.*')
            ->paginate(20);
        return view('admin.view_desk_officer')->with('u',$users);
    }
// ================ view suspend deskofficer =============================================
    public function suspend_desk_officer()
    {
        $users = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', self::DESKOFFICER)
            ->where('users.department_id', 0)
            ->orderBy('username', 'ASC')
            ->select('users.*')
            ->paginate(20);
        return view('admin.deskofficer.suspend_desk_officer')->with('u',$users);
    }

//====================== assign suspended desk officer ==============================
    public function assign_deskofficer($id)
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $p = Programme::all();
        $u = User::find($id);
        return view('admin.deskofficer.assign_deskofficer')->with('f',$f)->with('p',$p)->with('u',$u);
    }

    public function post_assign_deskofficer(Request $request)
    {
        $this->validate($request, array(
            'faculty_id' => 'required',
            'department_id' => 'required',
            'programme_id' => 'required'));

        $user = User::find($request->id);

        $user->faculty_id = $request->faculty_id;
        $user->department_id = $request->department_id;
        $user->programme_id = $request->programme_id;
        $user->fos_id = 0;
        $user->edit_right = 0;
        $user->save();

        Session::flash('success', "SUCCESSFULL.");
        return redirect()->action([HomeController::class,'suspend_desk_officer']);

    }

//========================== activate desk officer =================================================

    public function activate($id, $e)
    {
        if (isset($e)) {
            $user = User::find($id);
            if($user->status > 1)
            {
                Session::flash('warning', "account permanently suspend.");  
                return back();
            }
            $user->status = $e;
            $user->save();
// $e == 1 is for deactivation. so check and delete any assign course to the lectures
         /*   if ($e == 1) {
                AssignCourse::where('user_id', $id)->delete();
            }*/
            Session::flash('success', "SUCCESSFULL.");
            return back();
        }
    }
//================= suspend desk officer account ============================================

    public function suspend($id, $e = null)
    {

        if ($e != 1) {
            session()->put('url', url()->previous());
            return view('admin.deskofficer.suspend_confirmation');
        }
        $user = User::find($id);

        $user->faculty_id = 0;
        $user->department_id = 0;
        $user->programme_id = 0;
        $user->save();

// delete field of study
        $df = DeskofficeFos::where('user_id', $id)->get();

        foreach ($df as $key => $value) {
            $fos_update = Fos::find($value->fos_id);
            $fos_update->status = 0;
            $fos_update->save();
        }
        DeskofficeFos::where('user_id', $id)->delete();
        Session::flash('success', "successfull.");
        return redirect()->action([HomeController::class,'view_desk_officer']);
    }

    public function resultReport($id,$d)
    {
        $user = DB::connection('mysql2')->table('student_results')
        ->join('course_regs', 'student_results.coursereg_id', '=', 'course_regs.id')
        ->join('users', 'users.id', '=', 'student_results.user_id')
        ->where([['examofficer', $id],['users.faculty_id',$d]])
        ->orderBy('student_results.session', 'ASC')
        ->select('student_results.*','course_regs.course_code')
        ->paginate(1000);
        return view('admin.resultReport.index')->with('u',$user);
    }

    public function resultReport1($id)
    {
        $user = DB::connection('mysql2')->table('student_results')
        ->join('course_regs', 'student_results.coursereg_id', '=', 'course_regs.id')
        ->join('users', 'users.id', '=', 'student_results.user_id')
        ->where('examofficer', $id)
        ->orderBy('student_results.session', 'ASC')
        ->select('student_results.*','course_regs.course_code')
        ->paginate(1000);
        return view('admin.resultReport.index')->with('u',$user);
    }
//========================================== Pds new desk officer=======================================
/*    public function pds_new_desk_officer()
    {

        return view('admin.pds_new_desk_officer');
    }
//====================================post desk officer =====================================================
    public function pds_post_desk_officer(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required|string|max:255',
            'username' => 'required|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required'));

        $user = new User;
        $user->title = $request->title;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->plain_password = $request->password;
        $user->faculty_id = 0;
        $user->department_id = 0;
        $user->programme_id = 1;
        $user->fos_id = 0;
        $user->edit_right = 0;
        $user->save();

        $user_role = DB::table('user_roles')->insert(['user_id' => $user->id, 'role_id' => $request->role]);
        Session::flash('success', "SUCCESSFULL.");
        return redirect()->action('HomeController@pds_new_desk_officer');

    }
//=====================================================================================
    public function pds_view_desk_officer()
    {
        $users = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->whereIn('user_roles.role_id', [self::PDS, self::ModernLanguage])
            ->orderBy('department_id', 'ASC')
            ->select('users.*', 'user_roles.role_id')
            ->paginate(20);
        return view('admin.pds_view_desk_officer')->with('u',$users);
    }

//-------------------------------------predegree new courses ----------------------------------------------
    public function pds_create_course()
    {

        return view('admin.pds_create_course');
    }
//-------------------------------------new courses ----------------------------------------------
    public function pds_post_create_course(Request $request)
    {
        $variable = $request->input('f_course_code');
        $title = $request->input('course_title');
        $s_course_code = $request->input('s_course_code');
        if ($variable == null) {
            Session::flash('warning', "course Code is empty");
            return back();
        }
        foreach ($variable as $key => $value) {
            if (!empty($value)) {
                $cc = strtoupper(str_ireplace(" ", "", $value));
                $bb = strtoupper(str_ireplace(" ", "", $s_course_code[$key]));
                $clean_list[$cc] = array('course_title' => $title[$key], 'f_course_code' => $cc, 's_course_code' => $bb);
            }
        }

        foreach ($clean_list as $kk => $vv) {
            $course_code[] = $vv['f_course_code'];
        }

        $check = PdsCourse::whereIn('f_course_code', $course_code)->get();
        if (count($check) > 0) {
            foreach ($check as $key => $value) {
                unset($clean_list[$value->course_code]);
            }

        }
        if (count($clean_list) != 0) {

            foreach ($clean_list as $k => $v) {

                $data[] = ['course_title' => $clean_list[$k]['course_title'], 'f_course_code' => $clean_list[$k]['f_course_code'], 's_course_code' => $clean_list[$k]['s_course_code']];

            }
            DB::table('pds_courses')->insert($data);
            Session::flash('success', "SUCCESSFULL.");
            return redirect()->action('HomeController@pds_create_course');
        }
    }
//-------------------------------------pds view courses ----------------------------------------------

    public function pds_view_course()
    {
        $course = PdsCourse::orderBy('course_title', 'ASC')->get();
        return view('admin.pds_view_course')->withC($course);
    }

    public function modern_view_course()
    {
        $course = PdsModernCourse::orderBy('semester', 'ASC')->orderBy('code', 'ASC')->get();
        return view('admin.modern_view_course')->withC($course);
    }*/
//--------------------------------------- Edit right---------------------------------------------------
    public function edit_right($id, $e)
    {
        if (isset($id)) {
            $user = User::find($id);
            $user->edit_right = $e;
            $user->save();
            Session::flash('success', "SUCCESSFULL.");
            return back();
        }
    }

//========================== assign hod role =========================================
    public function assign_hod_role()
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('admin.hod_role.index')->with('f',$f);
    }
//------------------------------ get lecturer 4 hod --------------------------------------
    public function get_lecturer_4_hod(Request $request)
    {
        $f = Faculty::get();
        $f_id = $request->faculty_id;
        $d = $request->department_id;
        $user = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', self::LECTURER)
            ->where([['users.faculty_id', $f_id], ['users.department_id', $d]])
            ->orderBy('users.name', 'ASC')
            ->select('users.*')
            ->paginate(50);
        return view('admin.hod_role.index')->with('u',$user)->with('f',$f);
    }
//----------------------------------- assign hod----------------------------------------------------
    public function assign_hod(Request $request)
    {
        if ($request->hod) {
            $role = self::HOD;
        } elseif ($request->eo) {
            $role = self::EXAMSOFFICER;
            $user = 0;
        }

        $id = $request->optradio;
        $id = explode('~', $id);
        if ($role == self::HOD) {
            $user = DB::table('users')
                ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
                ->where('user_roles.role_id', $role)
                ->where('users.department_id', $id[1])
                ->get()
                ->count();
        }
        if ($user == 0) {

            $user_role = DB::table('user_roles')->where('user_id', $id[0])->update(['role_id' => $role]);
            Session::flash('success', "successful.");

        } else {
            if ($role == self::HOD) {
                Session::flash('warning', "HOD exist in these department.You have to remove existing person before you can assign another person.");

            } else {
                Session::flash('warning', "Exams Officer exist already.");

            }
        }

        return back();

    }

//-----------------------view hod -------------------------------------------------------------------
    public function view_assign_hod()
    {
        $result= session('key');
       
        if($result->name =="Deskofficer")
        {
            $user = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where('users.department_id',Auth::user()->department_id)
            ->where('user_roles.role_id', self::HOD)
            ->orderBy('users.department_id', 'ASC')
            ->select('users.*')
            ->get();
 
        }else{
        $user = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', self::HOD)
            ->orderBy('users.department_id', 'ASC')
            ->select('users.*')
            ->get();
        }
       
        return view('admin.hod_role.view')->with('u',$user);
    }

//==============================remove hod=============================================================
    public function remove_hod($id)
    {
        if (isset($id)) {
            $user_role = DB::table('user_roles')->where('user_id', $id)->update(['role_id' => self::LECTURER]);
        }
        Session::flash('success', "SUCCESSFULL.");
        return back();
    }
//======================== transfer of lecturer==================================
public function transferLecturer()
{
    $f = Faculty::orderBy('faculty_name', 'ASC')->get();
    return view('admin.transferLecturer.index')->with('f',$f);
}

public function get_transferLecturer(Request $request)
{
    $f = Faculty::get();
    $f_id = $request->faculty_id;
    $d = $request->department_id;
    $user = DB::table('users')
        ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
        ->where('user_roles.role_id', self::LECTURER)
        ->where([['users.faculty_id', $f_id], ['users.department_id', $d]])
        ->orderBy('users.name', 'ASC')
        ->select('users.*')
        ->paginate(50);
    return view('admin.transferLecturer.index')->with('u',$user)->with('f',$f);
}

public function post_transferLecturer(Request $request)
{
    $c =$request->check;
    if(empty($c)){
        Session::flash('warning', "please select lecturer");
        return back();
    }
    
    $f = $request->faculty;
    $d = $request->department;
    $date =date("Y/m/d");
    $v = DB::table('users')->whereIn('id',$c)->get();
    foreach($v as $u){
    $up=new UpdateUser;
    $up->user_id =$u->id;
    $up->user_department_id=$u->department_id;
    $up->user_faculty_id=$u->faculty_id;
    $up->posted=$date;
    $up->admin=Auth::user()->id;
    $up->save();
}

    $user = DB::table('users')->whereIn('id',$c)->update(['faculty_id'=>$f,'department_id'=>$d]);
    
        Session::flash('success', "SUCCESSFULL."); 
   
   
    return back();
}
//===========================exams officer =========================================
    public function assign_exams_officer(Request $request)
    {
        $f = Faculty::get();
        if ($request->isMethod('post')) {
            $variable = $request->input('fos');
            if ($variable == null) {

                return back();
            }
            $id = $request->optradio;
            $id = explode('~', $id);
            $v=array();
           // 
            foreach ($variable as $key => $value) {
                $check = DeskofficeFos::where([['fos_id', $value], ['user_id', $id[0]]])->first();
                if ($check == null) {
                    $v[] = ['fos_id' => $value, 'user_id' => $id[0], 'status' => 0];
                }

            }
            if (count($v) != 0) {
                DB::table('deskoffice_fos')->insert($v);
                DB::table('user_roles')->where('user_id', $id[0])->update(['role_id' => self::EXAMSOFFICER]);
                Session::flash('success', "Successfull.");
            } else {
                Session::flash('warning', "No records added, because FOS exist for these exams officer already.");
            }
        }
        return view('admin.assign_exams_officer.index')->with('f',$f);
    }
//------------------------------ get lecturer 4 exams officer --------------------------------------
    public function get_lecturer_4_exams_officer(Request $request)
    {
        $f = Faculty::get();
        $f_id = $request->faculty_id;
        $d = $request->department_id;
        $depart_name = Department::find($d);
        $fos = Fos::where('department_id', $d)->get();
        $user = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', self::LECTURER)
            ->where([['users.faculty_id', $f_id], ['users.department_id', $d]])
            ->orderBy('users.name', 'ASC')
            ->select('users.*')
            ->get();
        return view('admin.assign_exams_officer.index')->with('u',$user)->with('f',$f)->with('fos',$fos)->with('dname',$depart_name);
    }
//------------------------- view exams officer ---------------------------------------------
    public function view_exams_officer()
    {
        $department_id=Auth::user()->department_id;
        if($department_id != 0)
        {
            $users = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->where('user_roles.role_id', self::EXAMSOFFICER)
            ->where('users.department_id',$department_id)
            ->orderBy('departments.department_name', 'ASC')
            ->select('users.*')
            ->paginate(20);
        }else{
        $users = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->where('user_roles.role_id', self::EXAMSOFFICER)
            ->where('users.department_id', '!=', 0)
            ->orderBy('departments.department_name', 'ASC')
            ->select('users.*')
            ->paginate(20);
        }
        return view('admin.assign_exams_officer.view')->with('u',$users);
    }
//------------------------- remove exams officer ------------------------
    public function remove_exams_officer($id)
    {
        if (isset($id)) {
            $user_role = DB::table('user_roles')->where('user_id', $id)->update(['role_id' => self::LECTURER]);
            $desk = DeskofficeFos::where('user_id', $id)->delete();
            Session::flash('success', "SUCCESSFULL.");
        }

        return back();
    }

//------------------------- detail exams officer ---------------------------------------
    public function detail_exams_officer($id)
    {
        if (isset($id)) {
            $user = User::find($id);
            return view('admin.assign_exams_officer.detail')->with('u',$user);
        }

    }
//------------------------------------ remove fos --------------------------------------
    public function remove_fos($id)
    {
        if (isset($id)) {
            $desk = DeskofficeFos::where('id', $id)->delete();
            Session::flash('success', "SUCCESSFULL.");
        }
        return back();
    }
//-----------------------------------create course unit -----------------------------------------------
    public function create_course_unit()
    {
        return view('admin.create_course_unit');
    }
//-----------------------------------post create course unit -----------------------------------------------
    public function post_create_course_unit(Request $request)
    {
        $course_unit = new CourseUnit;
        $course_unit->session = $request->session;
        $course_unit->level = 0;
        $course_unit->fos = 0;
        $course_unit->min = $request->min;
        $course_unit->max = $request->max;
        $course_unit->save();
        Session::flash('success', "SUCCESSFULL.");
        return back();

    }
//-----------------------------------create course unit -----------------------------------------------
    public function create_course_unit_special()
    {
        $d = Department::orderBy('department_name', 'ASC')->get();
        $fos = $this->get_fos();
        $u = User::find(Auth::user()->id);
        return view('admin.create_course_unit_special')->with('d',$d)->with('f',$fos)->with('ud',$u);
    }
//-----------------------------------post create course unit -----------------------------------------------
    public function post_create_course_unit_special(Request $request)
    {
        $c = CourseUnit::where([['session', $request->session], ['fos', $request->fos], ['level', $request->level]])->get();

        if (count($c) == 0) {
            $course_unit = new CourseUnit;
            $course_unit->session = $request->session;
            $course_unit->level = $request->level;
            $course_unit->fos = $request->fos;
            $course_unit->min = $request->min;
            $course_unit->max = $request->max;
            $course_unit->save();
            Session::flash('success', "SUCCESSFULL.");
        } else {
            Session::flash('warning', "course unit for these field of study have been set already.");
        }
        return back();

    }

//================  edit_course_unit ===================================
    public function edit_course_unit($id)
    {
        $course_unit = CourseUnit::find($id);
        return view('admin.course_unit.edit')->with('c',$course_unit);
    }

//================  updated_course_unit ===================================
    public function update_course_unit(Request $request)
    {
        $c = CourseUnit::find($request->id);
        $c->min = $request->min;
        $c->max = $request->max;
        $c->save();
        Session::flash('success', "SUCCESSFULL.");
        return redirect()->action([HomeController::class,'view_course_unit']);
        //return view('admin.course_unit.edit')->withC($course_unit);
    }
//=====================view registered courses ===========================
    public function adminreg_course()
    {
      //  $d = DB::table('departments')->orderBy('department_name', 'ASC')->get();
      $f = $this->get_faculty();
        return view('admin.reg_course')->with('f',$f);
    }

    public function post_adminreg_course(request $request)
    {
        $f = $this->get_faculty();
        $this->validate($request, array('fos' => 'required', 'session' => 'required', 'level' => 'required'));
        $session = $request->session;
        $fos = $request->fos;
        $l = $request->level;
        $dd = $request->department;

        $register_course = RegisterCourse::where([['department_id', $dd], ['fos_id', $fos], ['level_id', $l], ['session', $session]])->orderBy('semester_id', 'ASC')->orderBy('reg_course_status', 'ASC')->get();

        return view('admin.reg_course')->with('r',$register_course)->with('g_s',$session)->with('g_l',$l)->with('fos',$fos)
        ->with('dd',$dd)->with('f',$f);
    }
// edit courses reg
    public function edit_adminreg_course($id, $s)
    {
        $getreg = RegisterCourse::where([['id', $id], ['session', $s]])->first();

        return view('admin.regcourse.edit')->with('r',$getreg);
    }

    public function edit_adminreg_course_semester($id, $s)
    {
        $getreg = RegisterCourse::where([['id', $id], ['session', $s]])->first();

        return view('admin.regcourse.edit_semester')->with('r',$getreg);
    }

    public function update_adminreg_course_semester(Request $request)
    {
        $id = $request->id;
        $s = $request->session;
       $semester = $request->semester;
        //dd($semester);
        $getreg = RegisterCourse::where([['id', $id], ['session', $s]])->first();

// normal courses first
$courseRegID =array();
$studentResultID =array();
        $course = Course::find($getreg->course_id);
        if ($course != null) {
/*$course->course_title =$title;
$course->course_code =$code;
$course->status =$status;
$course->course_unit =$unit;
$course->save();*/
// update register courses

        
            $getreg->semester_id = $semester;
            $getreg->save();

            $getcourse = CourseReg::where([['registercourse_id', $getreg->id], ['course_id', $getreg->course_id]])->get();

            if (count($getcourse) > 0) {
// update register courses students

$data = ['semester_id' => $semester];
$result_data = ['semester' => $semester];
CourseReg::where('registercourse_id', $getreg->id)->where('course_id', $getreg->course_id)->update($data);

// update result
                foreach ($getcourse as $key => $value) {
                    $courseRegID [] =$value->id;
                }
                    $result = StudentResult::whereIn('coursereg_id', $courseRegID)->get();
                    if(count($result) != 0)
                    {
foreach ($result as $key => $v) {
    $studentResultID []=$v->id;
}
  StudentResult::whereIn('id', $studentResultID)->update($result_data);
                    }       
                
            }
            Session::flash('success', "SUCCESSFULL.");
        } else {
            Session::flash('warning', "Please check not on course table.");
        }

        return redirect($request->pre_url);

    }

    public function update_adminreg_course(Request $request)
    {
        $id = $request->id;
        $s = $request->session;
        $code = $request->code;
        $title = $request->title;
        $status = $request->status;
        $unit = $request->unit;
        $semester = $request->semester;
        //dd($semester);
        $getreg = RegisterCourse::where([['id', $id], ['session', $s]])->first();

// normal courses first
$courseRegID =array();
$studentResultID =array();
        $course = Course::find($getreg->course_id);
        if ($course != null) {
/*$course->course_title =$title;
$course->course_code =$code;
$course->status =$status;
$course->course_unit =$unit;
$course->save();*/
// update register courses

            $getreg->reg_course_title = $title;
            $getreg->reg_course_code = $code;
            if($status == 'G')
            {

            }else{
            $getreg->reg_course_status = $status;
            }
            $getreg->reg_course_unit = $unit;
            $getreg->semester_id = $semester;
            $getreg->save();

            $getcourse = CourseReg::where([['registercourse_id', $getreg->id], ['course_id', $getreg->course_id]])->get();

            if (count($getcourse) > 0) {
// update register courses students
if($status == 'G')
{
    $data = ['course_title' => $title, 'course_code' => $code, 'course_unit' => $unit, 'semester_id' => $semester];
    
}else{
                $data = ['course_title' => $title, 'course_code' => $code, 'course_unit' => $unit, 'course_status' => $status, 'semester_id' => $semester];
              
}
$result_data = ['cu' => $unit, 'semester' => $semester];
             CourseReg::where('registercourse_id', $getreg->id)->where('course_id', $getreg->course_id)->update($data);

// update result
                foreach ($getcourse as $key => $value) {
                    $courseRegID [] =$value->id;
                }
                    $result = StudentResult::whereIn('coursereg_id', $courseRegID)->get();
                    if(count($result) != 0)
                    {
foreach ($result as $key => $v) {
    $studentResultID []=$v->id;
}
  StudentResult::whereIn('id', $studentResultID)->update($result_data);
                    }
                  
                   
                
            }
            Session::flash('success', "SUCCESSFULL.");
        } else {
            Session::flash('warning', "Please check not on course table.");
        }

        return redirect($request->pre_url);

    }

    public function delete_adminreg_course($id, $s, $yes = null)
    {
        if ($yes != 1) {
            session()->put('url', url()->previous());
            return view('admin.regcourse.confirmation');
        }

        $course = CourseReg::where([['registercourse_id', $id], ['session', $s]])->get();
        if (count($course) > 0) {
            foreach ($course as $key => $value) {
                $data[] = $value->id;
            }
// result
            $result = StudentResult::whereIn('coursereg_id', $data)->get();
            if (count($result) > 0) {
                foreach ($result as $kr => $vr) {
                    $dat_r[] = $vr->id;
                }
                $del_result = StudentResult::destroy($dat_r);
            }

            $del_course = CourseReg::destroy($data);

        }
        $regSpec =RegisterSpecialization::where('registercourse_id',$id)->delete();
        $reg = RegisterCourse::destroy($id);
        $assign_course = AssignCourse::where('registercourse_id', $id)->first();
        if ($assign_course != null) {
            $assign_course->delete();
        }
        Session::flash('success', "successfull.");
        return redirect(session()->get('url'));
    }

    public function delete_adminreg_multiple_course(Request $request)
    {
        $variable = $request->input('id');
        $session = $request->input('session');
        if ($variable == null) {
            return back();
        }
        $course = CourseReg::whereIn('registercourse_id', $variable)->where('session', $session)->get();
        if (count($course) > 0) {
            foreach ($course as $k => $v) {
                $dat[] = $v->id;
            }
            $del_course = CourseReg::destroy($dat);
// result
            $result = StudentResult::whereIn('coursereg_id', $dat)->get();
            if (count($result) > 0) {
                foreach ($result as $kr => $vr) {
                    $dat_r[] = $vr->id;
                }
                $del_result = StudentResult::destroy($dat_r);
            }

        }
        $assign_course = AssignCourse::whereIn('registercourse_id', $variable)->get();
        if (count($assign_course) > 0) {
            foreach ($assign_course as $key => $value) {
                $data[] = $value->id;
            }

            AssignCourse::destroy($data);
        }
        $regSpec =RegisterSpecialization::where('registercourse_id',$variable)->delete();
        $reg = RegisterCourse::destroy($variable);

        
        Session::flash('success', "successfull.");
        return back();
    }

//---------------------------- add course to students ---------------------------------------------
    public function add_adminreg_course($id, $s, $yes = null)
    {

        if ($yes != 1) {
            session()->put('url', url()->previous());
            return view('admin.regcourse.add_confirmation');
        }
        $data = array();
        $reg = DB::table('register_courses')->find($id);

/// get resiter students
        $user = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
            ->where([['student_regs.level_id', $reg->level_id], ['student_regs.semester', $reg->semester_id], ['users.fos_id', $reg->fos_id], ['student_regs.season', 'NORMAL'], ['student_regs.session', $s]])
            ->select('student_regs.*')
            ->get();
        //dd($reg->reg_course_unit);

        // get course unit set for the programme
        $course_unit = CourseUnit::where([['fos', $reg->fos_id], ['session', $s], ['level', $reg->level_id]])->first();

        if ($course_unit == null) {
            $course_unit = CourseUnit::where([['fos', 0], ['session', $s], ['level', 0]])->first();

        }

        // check for students that have not register for the courses
        foreach ($user as $key => $v) {
            $course = CourseReg::where([['registercourse_id', $id], ['session', $s], ['studentreg_id', $v->id], ['user_id', $v->user_id], ['semester_id', $v->semester], ['course_id', $reg->course_id], ['level_id', $v->level_id]])->first();
            if ($course == null) {
                // check for the total unit
                $coursereg = CourseReg::where([['session', $s], ['studentreg_id', $v->id], ['user_id', $v->user_id], ['semester_id', $v->semester], ['level_id', $v->level_id]])->sum('course_unit');
                $newcourseregtotal = $coursereg + $reg->reg_course_unit;

                if ($newcourseregtotal <= $course_unit->max) {
                    $data[] = ['studentreg_id' => $v->id, 'registercourse_id' => $id, 'user_id' => $v->user_id, 'level_id' => $v->level_id, 'semester_id' => $v->semester, 'course_id' => $reg->course_id, 'course_title' => $reg->reg_course_title, 'course_code' => $reg->reg_course_code, 'course_unit' => $reg->reg_course_unit, 'course_status' => $reg->reg_course_status, 'session' => $reg->session, 'period' => 'NORMAL'];
                }
            }

        }
//dd($data);
        if (!empty($data)) {
            DB::connection('mysql2')->table('course_regs')->insert($data);
            Session::flash('success', "successfull.");
        } else {
            Session::flash('warning', "all students have register these course.");
        }
        return redirect(session()->get('url'));
    }

    //========================== classAttendance=================
    public function classAttendance($id,$s,$d,$fos,$l,$semester)
    {
        $reg = RegisterCourse::where([['id', $id], ['session', $s]])->first();
        $courseReg = DB::connection('mysql2')->table('users')
        ->join('course_regs', 'users.id', '=', 'course_regs.user_id')
         ->where([['users.fos_id', $fos], ['users.department_id', $d], ['course_regs.session', $s], ['course_regs.semester_id', $semester],['course_regs.course_id', $reg->course_id]])
         ->select('course_regs.*', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number','users.image_url')
         ->orderBy('level_id','ASC')->orderBy('users.matric_number','ASC')
         ->get()->groupBy('level_id');
         return view('admin.regcourse.classAttendance')->with('reg',$reg)->with('item',$courseReg)->with('d',$d)->with('l',$l)->with('s',$s)->with('sm',$semester)->with('fos',$fos);

    }
// ================ change password ===========================
    public function changepassword()
    {

        return view('admin.changepassword');
    }
// ================ post change password ===========================
    public function post_changepassword(Request $request)
    {
        $this->validate($request, array('password' => 'required'));
        $password = $request->password;
        $user = User::find(Auth::user()->id);
        $user->password = bcrypt($password);
        $user->plain_password = $password;
        $user->save();
        Session::flash('success', "successfull.");

        return back();
    }

// ================ change email ===========================
    public function changeemail()
    {

        return view('admin.changeemail.index');
    }
// ================ post change email ===========================
    public function post_changeemail(Request $request)
    {
        $this->validate($request, array('email' => 'required', 'unique'));
        $email = $request->email;
        $user = User::find(Auth::user()->id);
        $user->email = $email;
        $user->save();
        Session::flash('success', "successfull.");

        return back();
    }

// ============================== delete students registration =============
    public function deleteRegistration($id)
    {
        $reg=DB::connection('mysql2')->table('student_regs')->where('id', $id)->first();
       
        $s = DB::connection('mysql2')->table('student_results')
        ->where([['session',$reg->session],['semester',$reg->semester],['level_id',$reg->level_id],['user_id',$reg->user_id],['season',$reg->season]])->get(); 
      
        if(count($s) > 0){
            Session::flash('warning', "registration with result can not be drop again. contact system admin");
           return back(); 
        }
        $courseReg = DB::connection('mysql2')->table('course_regs')->where('studentreg_id', $id)->get();
        if (count($courseReg) > 0) {
            foreach ($courseReg as $key => $value) {
                // check students result is present
                $student_results = DB::connection('mysql2')->table('student_results')->where('coursereg_id', $value->id)->first();
                
                if ($student_results != null) { // delete students result
                    DB::connection('mysql2')->table('student_results')->where('id', $student_results->id)->delete();

                } // delete course reg
                DB::connection('mysql2')->table('course_regs')->where('id', $value->id)->delete();
            }

        } // delete student reg
        DB::connection('mysql2')->table('student_regs')->where('id', $id)->delete();
        Session::flash('success', "successfull.");
        return back();
    }

//=============================== transfer officer =====================================
    public function transfer_officer()
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $p = Programme::get();
        return view('admin.transfer_officer.index')->with('f',$f)->with('p',$p);
    }

    public function post_transfer_officer(Request $request)
    {
        $f = Faculty::get();
        $p = Programme::get();
        $id = $request->officer_id;
        $m_fac = $request->m_fac_id;
        $m_dept = $request->m_dept_id;
        $programme = $request->programme_id;

        $user = user::find($id);
        $user->department_id = $m_dept;
        $user->faculty_id = $m_fac;
        $user->programme_id = $programme;
        $user->save();
        // delete field of study
        $df = DeskofficeFos::where('user_id', $id)->get();
        foreach ($df as $key => $value) {
            $fos_update = Fos::find($value->fos_id);
            $fos_update->status = 0;
            $fos_update->save();
        }
        DeskofficeFos::where('user_id', $id)->delete();
        Session::flash('success', "successfull.");
        return back();

        //return view('admin.transfer_officer.index')->withF($f)->withP($p);
    }
//=================== courses and result ration =====================
public function coursesAndResultRatio()
{
    $fos = $this->get_fos();
$p = $this->getp();
       
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('admin.coursesAndResultRatio.index')->with('f',$fos)->with('fc',$f)->with('p',$p);
 
}

public function postCoursesAndResultRatio(Request $request)
{
    $ff = Faculty::orderBy('faculty_name', 'ASC')->get();
   
    $s = $request->session;
    $f = $request->faculty_id;
    $d=$request->department;
    $p=$request->programme;
    if(!isset($f))
    {
        $f=Auth::user()->faculty_id;
    }
    if(!isset($d))
    {
        $d=Auth::user()->department_id;
    }
    if(!isset($p))
    {
        $p=Auth::user()->programme_id;
    }
    //$sm=$request->semester;
    $fos=$request->fos;
    $coursereg_id=array();    
$course_id=array();
$courseDetail=array();
$resultDetail=array();
    $reg = DB::table('register_courses')
        ->select('id','course_id','reg_course_title','reg_course_code','reg_course_status','semester_id','level_id')
        ->where([['session',$s],['programme_id',$p],['department_id',$d],['faculty_id',$f],
        ['fos_id',$fos]])->orderBy('level_id','Asc')->orderBy('reg_course_status','Asc')->orderBy('reg_course_code','Asc')
        ->distinct('course_id')->get();
    $reg2 =$reg->groupBy('level_id');
        if(count($reg) == 0)
        {
            Session::flash('warning', "No Registered courses in the session");
            return back();
        }
   /* foreach ($reg as $v) {
        $course_id[] = $v->course_id;
        $allcourse[] = $v;
        $reg_id[] = $v->id;
    }*/
  
    foreach ($reg2 as $k => $t) {
        $reg_id=array();
        foreach($t as $v){
        $course_id[] = $v->course_id;
        $allcourse[] = $v;
        $reg_id[] = $v->id;
        }
    
    $course = DB::connection('mysql2')->table('course_regs')
    ->where([['session', $s],['level_id',$k]])->whereIn('registercourse_id',$reg_id)
    ->distinct()->get()->groupBy('course_id');

    foreach($course as $key => $col)
    {
     $courseDetail[$key][$k] =['noOfCourse'=>count($col),'course_id'=>$key];
     foreach($col as $value){
     $coursereg_id[]=$value->id;
     }
    //}

    $results = DB::connection('mysql2')->table('student_results')
    ->where([['session', $s],['course_id',$key],['level_id',$k]])
    ->whereIn('coursereg_id',$coursereg_id)
    ->get();

    $approvedresults = DB::connection('mysql2')->table('student_results')
    ->where([['session', $s],['course_id',$key],['level_id',$k],['approved',2]])
    ->whereIn('coursereg_id',$coursereg_id)
    ->get();
    //->distinct()->get()->groupBy('course_id');

    $resultDetail[$key][$k] =['noOfResult'=>count($results) ,'noOfApprovedResult'=>count($approvedresults)];

    }
}
  
  /* foreach($results as $key => $res)
   {
    $resultDetail[$key] =['noOfResult'=>count($res)];
   }*/
  
  
   return view('admin.coursesAndResultRatio.view')->with('allcourse',$reg2)->with('d',$d)->with('fos',$fos)->with('s',$s)->with('resultD',$resultDetail)->with('courseD',$courseDetail);
}


//=================== courses with no results =====================
    public function course_with_no_result()
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        return view('admin.course_with_no_result.index')->with('f',$f);
    }

    public function post_course_with_no_result(Request $request)
    {
        $ff = Faculty::orderBy('faculty_name', 'ASC')->get();
        $this->validate($request, array('session' => 'required', 'level' => 'required', 'faculty_id' => 'required'));
        $s = $request->session;
        $l = $request->level;
        $f = $request->faculty_id;
        $d=$request->department;

        $results = DB::connection('mysql2')->table('student_results')
            ->select('course_id')
            ->where([['level_id', $l], ['session', $s]])
            ->distinct()->get();
        foreach ($results as $v) {
            $course_id_with_result[] = $v->course_id;
        }

        $user = DB::connection('mysql2')->table('users')
            ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
            ->select('fos_id')
            ->where([['student_regs.level_id', $l], ['student_regs.session', $s], ['users.faculty_id', $f],
            ['users.department_id', $d]])
            ->distinct('fos_id')->get();
        if(count($user) == 0){
            Session::flash('success', "No Registered students in the session");
            return back();
        }
        foreach ($user as $v) {
            $fos_id[] = $v->fos_id;
        }
        //dd($fos_id);
        if(config('app.env') === 'production'){
        $reg = DB::table('register_courses')
            ->join('faculties', 'register_courses.faculty_id', '=', 'faculties.id')
            ->where([['level_id', $l], ['session', $s], ['reg_course_status', '!=', 'E']])
            ->whereNotIn('course_id', $course_id_with_result)
            ->whereIn('fos_id', $fos_id)
           ->whereIn('register_courses.id', function($query) use ($l,$s) {
            $query->select('registercourse_id')->from('unical8_exams2.course_regs')
            ->where([['level_id',$l],['session',$s]]);
        })
        ->select('register_courses.id','course_id', 'fos_id', 'department_id', 'semester_id', 'faculty_id', 'reg_course_title', 'reg_course_code','reg_course_status', 'faculty_name')
        
          ->orderBy('faculty_name', 'ASC', 'semester_id', 'ASC')
            ->distinct()->get()->groupBy('faculty_id');
    }else{
        $reg = DB::table('register_courses')
        ->join('faculties', 'register_courses.faculty_id', '=', 'faculties.id')
        ->where([['level_id', $l], ['session', $s], ['reg_course_status', '!=', 'E']])
        ->whereNotIn('course_id', $course_id_with_result)
        ->whereIn('fos_id', $fos_id)
      ->whereIn('register_courses.id', function($query) use ($l,$s) {
        $query->select('registercourse_id')->from('unical_exams2.course_regs')
        ->where([['level_id',$l],['session',$s]]);
    })

 ->select('register_courses.id','course_id', 'fos_id', 'department_id', 'semester_id', 'faculty_id', 'reg_course_title', 'reg_course_code', 'reg_course_status','faculty_name')
     
      ->orderBy('faculty_name', 'ASC', 'semester_id', 'ASC')
        ->distinct()->get()->groupBy('faculty_id');
    }
    

        return view('admin.course_with_no_result.report')->with('reg',$reg)->with('l',$l)->with('s',$s);
    }

//=================== courses with no results ii=====================
public function courseWithNoResult()
{
    $f = Faculty::orderBy('faculty_name', 'ASC')->get();
    return view('admin.course_with_no_result.index1')->with('f',$f);
}

public function postCourseWithNoResult(Request $request)
{
    
        
    $ff = Faculty::orderBy('faculty_name', 'ASC')->get();
    $this->validate($request, array('session' => 'required','faculty_id' => 'required'));
    $s = $request->session;
    $f = $request->faculty_id;
    $d=$request->department;
    $course_id_with_result =array();
    $old=0;
    if($request->excel =='excel'){
        return Excel::download(new UsersExport($request->all()), 'invoices.xlsx');
    }elseif($request->excel =='ws'){
$withdral=array(); $wch=array();$probation=array();
        $user = DB::connection('mysql2')->table('users')
        ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
        ->select('users.*','level_id')
        ->where([['student_regs.session', $s], ['users.faculty_id', $f],
        ['users.department_id', $d],['semester',1]])->orderBy('level_id','Asc')->distinct()->get();
if(count($user) == 0){
    Session::flash('warning', "no registered Students");
    return back();
}
foreach($user as $v){
        $cgpa =$this->get_cgpa($s,$v->id,'NORMAL');
        $r = $this->Probtion($v->level_id,$v->id,$s,$cgpa,'NORMAL',$v->fos_id);
        if($r== "WITHDRAW")
        {
$withdral[]=['mat'=>$v->matric_number,'surname'=>$v->surname,'firstname'=>$v->firstname,'othername'=>$v->othername,'level'=>$v->level_id];
        }
        elseif($r== "WITHDRAW OR CHANGE PROGRAMME" || $r== "CHANGE PROGRAMME")
        {
         $wch[]=['mat'=>$v->matric_number,'surname'=>$v->surname,'firstname'=>$v->firstname,'othername'=>$v->othername,'level'=>$v->level_id];
        }elseif($r=="PROBATION")
        {
         $probation[] =['mat'=>$v->matric_number,'surname'=>$v->surname,'firstname'=>$v->firstname,'othername'=>$v->othername,'level'=>$v->level_id]; 
        }
    }
    return Excel::download(new WithdralExport($withdral,$wch,$probation,$d,$s,$f,$old), 'withdraw.xlsx');

    }else{
    $user = DB::connection('mysql2')->table('users')
        ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
        ->select('fos_id','student_regs.id')
        ->where([['student_regs.session', $s], ['users.faculty_id', $f],
        ['users.department_id', $d]])
        ->distinct('fos_id')->get();
    if(count($user) == 0){
        Session::flash('success', "No Registered students in the session");
        return back();
    }
    foreach ($user as $v) {
        $fos_id[] = $v->fos_id;
        $studentRegId [] =$v->id;
    }

    $results = DB::connection('mysql2')->table('course_regs')
    ->join('student_results','course_regs.id','=','student_results.coursereg_id')
    ->where([['student_results.session', $s]])
    ->whereIn('studentreg_id',$studentRegId)
    ->select('course_regs.registercourse_id')
  ->distinct()->get();
    foreach ($results as $v) {
        $course_id_with_result[] = $v->registercourse_id;
    }

    if(config('app.env') === 'production'){
    $reg = DB::table('register_courses')
        ->join('faculties', 'register_courses.faculty_id', '=', 'faculties.id')
        ->where([['session', $s], ['reg_course_status', '!=', 'E']])
        ->whereNotIn('register_courses.id', $course_id_with_result)
        ->whereIn('fos_id', $fos_id)
       ->whereIn('register_courses.id', function($query) use ($s) {
        $query->select('registercourse_id')->from('unical8_exams2.course_regs')
        ->where('session',$s);
    })
    ->select('register_courses.id','course_id', 'fos_id','level_id', 'department_id', 'semester_id', 'faculty_id', 'reg_course_title', 'reg_course_code','reg_course_status', 'faculty_name')
    
      ->orderBy('register_courses.level_id', 'ASC', 'semester_id', 'ASC')
        ->distinct()->get()->groupBy('level_id');

        
}else{
    $reg = DB::table('register_courses')
    ->join('faculties', 'register_courses.faculty_id', '=', 'faculties.id')
    ->where([['session', $s], ['reg_course_status', '!=', 'E']])
    ->whereNotIn('register_courses.id', $course_id_with_result)
    ->whereIn('fos_id', $fos_id)
  ->whereIn('register_courses.id', function($query) use ($s) {
    $query->select('registercourse_id')->from('unical_exams2.course_regs')
    ->where('session',$s);
})

->select('register_courses.id','course_id', 'fos_id','register_courses.level_id','department_id', 'semester_id', 'faculty_id', 'reg_course_title', 'reg_course_code', 'reg_course_status','faculty_name')
 
  ->orderBy('register_courses.level_id', 'ASC', 'semester_id', 'ASC')
    ->distinct()->get()->groupBy('level_id');
}


    return view('admin.course_with_no_result.report2')->with('reg',$reg)->with('s',$s)->with('f',$f)->with('d',$d);
}
}

//=================== courses with no results ii=====================
public function withdrawalOldportal()
{
    $f = DB::connection('oldporta')->table('faculties')->orderBy('faculties_name', 'ASC')->get();
    return view('admin.withdrawOldportal.index')->with('f',$f);
}

public function postWithdrawalOldportal(Request $request)
{   
    $ff = DB::connection('oldporta')->table('faculties')->orderBy('faculties_name', 'ASC')->get();
    $this->validate($request, array('session' => 'required','faculty' => 'required'));
    $s = $request->session;
    $f = $request->faculty;
    $d=$request->department;
    $dname=$this->getOldDepartment1($d);
    $old=1;
$withdral=array(); $wch=array();$probation=array();
        $user = DB::connection('oldporta')->table('students_profile')
        ->join('students_reg', 'students_reg.std_id', '=', 'students_profile.std_id')
        ->select('students_profile.*','level_id')
        ->where([['students_reg.yearsession',$s], ['students_profile.stdfaculty_id',$f],
        ['students_profile.stddepartment_id',$d]])->orderBy('level_id','Asc')->distinct()->get();
   
if(count($user) == 0){
    Session::flash('warning', "no registered Students");
    return back();
}
foreach($user as $v){
        $cgpa =$this->get_cgpa_old($s,$v->std_id);
        $r = $this->new_Probtion_old($v->level_id,$v->std_id,$s,$cgpa);
        if($r== "WITHDRAW")
        {
$withdral[]=['mat'=>$v->matric_no,'surname'=>$v->surname,'firstname'=>$v->firstname,'othername'=>$v->othernames,'level'=>$v->level_id];
        }
        elseif($r== "WITHDRAW OR CHANGE PROGRAMME" || $r== "CHANGE PROGRAMME")
        {
         $wch[]=['mat'=>$v->matric_no,'surname'=>$v->surname,'firstname'=>$v->firstname,'othername'=>$v->othernames,'level'=>$v->level_id];
        }elseif($r=="PROBATION")
        {
         $probation[] =['mat'=>$v->matric_no,'surname'=>$v->surname,'firstname'=>$v->firstname,'othername'=>$v->othernames,'level'=>$v->level_id]; 
        }
}


    return Excel::download(new WithdralExport($withdral,$wch,$probation,$d,$s,$f,$old), $dname.'withdrawOld.xlsx');

return view('admin.withdrawOldportal.view')->with('wch',$wch)->with('probation',$probation)->with('withral',$withdral)->with('s',$s)->with('f',$ff)->with('d',$d);

}







// ============================= publish result ================================
    public function publish_result()
    {
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $p = Programme::get();
        return view('admin.publish_result.index')->With('f',$f)->with('p',$p);
    }

    public function post_publish_result(Request $request)
    {
        $department_id = $request->department_id;
        $programme = $request->programme_id;
        $level = $request->level;
        $session = $request->session;
        $nsession = $session + 1;
        $fv = array();
$result =session('key');
if($result->name == 'Deskofficer')
{
$des = DeskofficeFos::where('user_id',Auth::user()->id)->get();
foreach ($des as $v) {
    $fv[] = $v->fos_id;
}
$fos = Fos::whereIn('id', $fv)->get();
}else{
        $fos = Fos::where([['department_id', $department_id], ['programme_id', $programme]])->get();

        foreach ($fos as $v) {
            $fv[] = $v->id;
        }
}
        $pr = PublishResult::whereIn('fos_id', $fv)->where([['level_id', $level], ['session', $session]])->get();
        $f = Faculty::orderBy('faculty_name', 'ASC')->get();
        $p = Programme::get();

        return view('admin.publish_result.index')->with('f',$f)->with('p',$p)->with('fos',$fos)->with('pr',$pr)
        ->with('l',$level)->with('s',$session)
            ->with('ns',$nsession);
    }

    public function publish(Request $request)
    {
        $variable = $request->fos_id;
        if (!isset($variable)) {
            Session::flash('warning', "Please select field of study to publish");
            // return redirect(session()->get('url'));
            return back();
        }
        $l = $request->level_id;
        $s = $request->session;
        $data = array();
        $data2 = array();
        $data3 = array();
        $data4 = array();
        $data5 = array();
        $exist_data = array();
        $date = date('Y-m-d');
        $rcid=array();
        $ddd=array();
        $ccc=array();
        foreach ($variable as $v) {
            $data2[] = ['fos_id' => $v, 'level_id' => $l, 'session' => $s];
            $data5[] = $v;
        }
        $register_course = RegisterCourse::where([['level_id', $l], ['session', $s]])->whereIn('fos_id', $data5)->get();

        if (count($register_course) == 0) {
            Session::flash('warning', "No registerd Courses for the level and session on the selected Field Of Study");
            // return redirect(session()->get('url'));
            return back();
        }
        $data4 = ['approved' => 1, 'approved_date' => $date];
        foreach ($register_course as $value) {
          //  $ddd[] = $value->course_id;
            $data3[] = $value->fos_id;
            $rcid[] =$value->id;
        }

     $cosreg =CourseReg::wherein('registercourse_id',$rcid)->get();
   
     foreach($cosreg as $vc)
     {
      /* ;*/
        //if($sr == null){
            $ccc[] = $vc->id;
        //}
     }
//filter the one approve by sbc
     $sr = DB::connection('mysql2')->table('student_results')
     ->whereIn('coursereg_id',$ccc)
        ->where('approved','!=', 2)
        ->get();
        if(count($sr) == 0)
        {
            Session::flash('warning', "No result to publish. All result has been Approved by  SBC");
            return back(); 
        }
foreach($sr as $srv)
{
    $ddd[] =$srv->coursereg_id;
}
    
        $data3 = array_unique($data3);
       

        DB::connection('mysql2')->table('student_results')
            ->whereIn('coursereg_id', $ddd)
            ->where([['level_id', $l], ['session', $s], ['approved', '!=', 1]])
            ->update($data4);

        $pr = PublishResult::where([['level_id', $l], ['session', $s]])->whereIn('fos_id', $data3)->get();

        if (count($pr)) {
            foreach ($pr as $v) {
                $update = PublishResult::where([['fos_id', $v->fos_id], ['level_id', $l], ['session', $s]])
                    ->update(['publish_date' => $date]);
                $exist_data[] = $v->fos_id;
            }
        }

        $remain_fos_id = array_diff($data3, $exist_data);
        if (count($remain_fos_id) > 0) {
            foreach ($remain_fos_id as $v) {
                $data[] = ['fos_id' => $v, 'level_id' => $l, 'session' => $s, 'publish_date' => $date];
            }
            $p = PublishResult::insert($data);
        }

        Session::flash('success', "Successful");

        return back();
    }

//-------------------------------------------success---------------------------------------------
    public function success()
    {
        return view('admin.view_course_unit');
    }
//-----------------------------------view  course unit -----------------------------------------------
    public function view_course_unit()
    {
        return view('admin.view_course_unit');
    }
//-----------------------------------view  course unit -----------------------------------------------
    public function post_view_course_unit(Request $request)
    {
        $r = $this->g_rolename(Auth::user()->id);
        if ($r == 'Deskofficer') {
            $fos = $this->get_fos();
            if ($fos) {
                foreach ($fos as $v) {
                    $fosId[] = $v->id;
                }
            }
            $c = CourseUnit::whereIn('fos', $fosId)
                ->where('session', $request->session)
                ->get();
        } else {
            $c = CourseUnit::where('session', $request->session)->get();
        }

        return view('admin.view_course_unit')->with('c',$c);
    }
//========================================================================================
    // function to get department
    public function getDepartment($id)
    {

        $d = Department::where('faculty_id', $id)->orderBy('department_name','ASC')->get();
        return response()->json($d);
    }
    public function getOldDepartment($id)
    {
 $d = DB::connection('oldporta')->table('departments')->where('fac_id',$id)->orderBy('departments_name','ASC')->get();
 return response()->json($d);
    }
    public function getOldDepartment1($id)
    {
 $d = DB::connection('oldporta')->table('departments')->where('departments_id',$id)->first();
 return $d->departments_name;
    }
// function to get fos
    public function getFos($id)
    {
        $d = Fos::where('department_id', $id)->orderBy('fos_name', 'ASC')->get();
        return response()->json($d);
    }
    // function to get fos old
    public function getOldFos($id)
    {
        $d = DB::connection('oldporta')->table('dept_options')
        ->where('dept_id', $id)->orderBy('programme_option', 'ASC')->get();
        return response()->json($d);
    }

    // function to get sfos
    public function Sfos($id)
    {
        $d = Specialization::where('fos_id', $id)->orderBy('name', 'ASC')->get();
        return response()->json($d);
    }
// function to get fos
    public function username($id)
    {
        $d = User::where([['department_id', $id], ['programme_id', '!=', 0]])->get();
        return response()->json($d);
    }
//========================== update email ==========================
    public function update_email($id)
    {
        $d = User::find($id);
        return view('admin.update_email.index')->with('d',$d);
    }

    // ================ post change email ===========================
    public function post_update_email(Request $request)
    {
        $this->validate($request, array('email' => 'required|unique:users'));
        $email = $request->email;
        $id = $request->id;
        $user = User::find($id);
        $oldemail =$user->email;
        $user->email = $email;
        $user->save();

        $ue = new EmailBackUp;
        $ue->oldEmail=$oldemail;
        $ue->newEmail=$request->email;
        $ue->user_id=$id;
        $ue->admin=Auth::user()->id;
        $ue->posted=Date('Y-m-d');
        $ue->save();
        Session::flash('success', "successfull.");

        return back();
    }//62377284

//================= transfer students=====================
    public function transferStudents(Request $request)
    {
        $s = $request->session;
        $f = $request->faculty;
        $d = $request->department;
        $fos = $request->fos;
        $variable = $request->id;
        if ($variable == null) {
            Session::flash('warning', "please select students.");
            return back();
        }
        // check if the department and unit have registered courses
        $l=1;
        $reg = RegisterCourse::where([['fos_id', $fos], ['session', $s], ['level_id', $l]])->get();
        $check = $reg->count();
        if ($check == 0) {
            Session::flash('warning', "you have no registerd courses.");
            return back();
        }
        if($request->TWR != null)
        {
            foreach ($variable as $v) {
                $u = DB::connection('mysql2')->table('users')
                ->where('id', $v)
                ->update(['faculty_id' => $f, 'department_id' => $d, 'fos_id' => $fos, 'entry_year' => $s]);
                $id[]=$v;
            }
            foreach($reg as $r)
            {
            $c =DB::connection('mysql2')->table('course_regs')
            ->where([['level_id',$l],['course_id',$r->course_id],['course_status',$r->reg_course_status]]) 
            ->whereIn('user_id',$id)
            ->update(['registercourse_id' => $r->id,'course_unit'=>$r->reg_course_unit,'session'=>$s]);

            $sr =DB::connection('mysql2')->table('student_regs')
            ->where('level_id',$l)->whereIn('user_id',$id)
            ->update(['session'=>$s,'faculty_id'=>$f,'department_id'=>$d]);

            $rs =DB::connection('mysql2')->table('student_results')
            ->where('level_id',$l)->whereIn('user_id',$id)
            ->update(['session'=>$s]);

            $br =DB::connection('mysql2')->table('student_result_backups')
            ->where('level_id',$l)->whereIn('user_id',$id)
            ->update(['session'=>$s]);
            
           }
        
        }else{
        $programme = Fos::find($fos);

        foreach ($variable as $v) {
           
           $l = 1;
            $courseReg2 = '';
            $firstSemeter = 1;
            $secondSemester = 2;
            $us = DB::connection('mysql2')->table('users')->find($v);
            $new =substr_replace($us->matric_number,'',-2);
            
               $len =strlen(strtoUpper($us->matric_number));
                $startlenght  =$len - 2;
            $TR = substr(strtoUpper($us->matric_number),$startlenght,2);
           // dd($TR);
            if($TR =='TR')
            {
                $u = DB::connection('mysql2')->table('users')
                ->where('id', $v)
                ->update(['faculty_id' => $f, 'department_id' => $d, 'fos_id' => $fos, 'entry_year' => $s]);
                $id[]=$v;
         //  }
             //update for business managemnet
         /*  foreach($reg as $r)
             {
                $c =DB::connection('mysql2')->table('course_regs')
                ->where([['level_id',$l],['session',$s],['course_id',$r->course_id],['course_status',$r->reg_course_status]]) 
             ->whereIn('user_id',$id)
             ->update(['registercourse_id' => $r->id,'course_unit'=>$r->reg_course_unit]);
             
            }*/
             

            
            }else{

                $new_matric_number = $us->matric_number;//.'TR';
                $old_matric_number = $us->matric_number;
                $data =['matric_number'=>$new_matric_number];
               // dd($data);
              $error = Validator::make($data, [
                    'matric_number' => [
                        'required',
                        Rule::unique('mysql2.users')->ignore($v),
                    ],
                ]);
                $error =$error->errors();
                
                if($error->first('matric_number') != '')
                {
                    continue;
                }
                $u = DB::connection('mysql2')->table('users')
                ->where('id', $v)
                ->update(['matric_number'=>$new_matric_number,'password'=>bcrypt($new_matric_number),'faculty_id' => $f, 'department_id' => $d, 'fos_id' => $fos, 'entry_year' => $s]);
               
                DB::connection()->table('pins')
                ->where([['student_id',$v],['matric_number',$old_matric_number]])
                ->update(['matric_number' => $new_matric_number]);
            }
            $deleteResult = StudentResult::where('user_id', $v)->delete();
            $deleteCourseReg = CourseReg::where('user_id', $v)->delete();
            $deleteStudentReg = StudentReg::where('user_id', $v)->delete();    
            
            //check if student has registered for first semester
            $check1 = $this->registrationStatus($v, $firstSemeter, $s);
            if ($check1 == null) {
                $studentRegId = $this->studentReg($v, $f, $d, $programme->programme_id, $l, $s, $firstSemeter, 'NORMAL');
                $registeredCourses = $this->getRegisteredCourses1($l, $s, $firstSemeter, $fos);
                $courseReg = $this->studentCourseReg($v, $studentRegId, $registeredCourses, $l, $s, $firstSemeter, 'NORMAL');
            }

            //check if student has registered for second semester
            $check2 = $this->registrationStatus($v, $secondSemester, $s);
            if ($check2 == null) {
                $studentRegId = $this->studentReg($v, $f, $d, $programme->programme_id, $l, $s, $secondSemester, 'NORMAL');
                $registeredCourses = $this->getRegisteredCourses1($l, $s, $secondSemester, $fos);
                $courseReg2 = $this->studentCourseReg($v, $studentRegId, $registeredCourses, $l, $s, $secondSemester, 'NORMAL');
            }

        }
        if ($courseReg2 == 1) {
            Session::flash('success', "successfull.");
        } else {
            Session::flash('warning', "was not success.");
        }
    }
        Session::flash('success', "successfull.");
        return back();
    }


    public function cleanRegisterCourseTable()
    {
        if(config('app.env') === 'production'){
            $reg = DB::table('register_courses')
                ->where('reg_course_status', 'G')
               ->whereNotIn('id', function($query){
                $query->select('registercourse_id')->from('unical8_exams2.course_regs');
            })
            ->select('id')->distinct()->get();
        }else{
            $reg = DB::table('register_courses')
            ->where('reg_course_status', 'G')
           ->whereNotIn('id', function($query){
            $query->select('registercourse_id')->from('unical_exams2.course_regs');
        })
        ->select('id')->distinct()->get();
        } 
        
        if(count($reg) != 0){
        foreach($reg as $v){
$regA[]=$v->id;
        }
        RegisterCourse::destroy($regA);
    }
    }


    
}
