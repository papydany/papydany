<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Pin;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\MyTrait;
use App\Models\StudentResult;
use App\Exports\PinsExport;
use App\Models\RegisterCourse;
use App\Exports\RegisterCourseExport;
use App\Models\Course;
use App\Models\CourseReg;
use App\Models\AssignCourse;
use App\Models\StudentResultBackup;
use GuzzleHttp\Client;
use App\Imports\StudentsImport;
use App\Exports\Graduate;
use App\Models\UpdateResult;
use App\Models\Fos;
use App\Models\Faculty;

class SupportController extends Controller
{
   use MyTrait;
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
    {
     return view('support.index');
    }

    //======================================== Get create Pin =====================================
    public function get_create_pin()
    {
      //101176
    	return view('support.get_create_pin');
    }
//======================================== post create Pin =====================================
    function post_create_pin(Request $request)
    {
    ini_set('max_execution_time', 980);    
    $this->validate($request,array(
    'number'=>'required',
     'pin_lenght'=>'required',
    'session' => 'required',
       ));
$pin =new Pin;

$pin->session = $request->session;
$pin->status= 0;

     
for ($i = 0; $i <=$request->number; $i++) {
  $rand = $this->generateRandomString($request->pin_lenght);
  $pin->pin = $rand;
 $check =Pin::where([['pin',$rand],['session',$request->session]])->first();
  if($check == null)
  {
 	DB::table('pins')->insert(['pin' => $rand, 'status' => 0,'session'=>$request->session]);


  }
 else{

  	$i--;
  }

 
    }
       Session::flash('success',"SUCCESSFULL.");
    	return view('support.get_create_pin');

    }
    //============== get_student_with_entry_year===========
    public function get_student_with_entry_year(Request $request)
    {
        $entry_year =$request->entry_year;
   $user = DB::connection('mysql2')->table('users')->where('entry_year',$entry_year)->orderBy('department_id','ASC')->paginate(500);

return view('support.entry_year')->with('u',$user);
    }

//========================================view un_used_ Pin =====================================
 public function view_unused_pin()
 {
 	
 	$unused_pin = Pin::where('status',0)->orderBy('id','ASC')->paginate(500);
 	
 	return view('support.view_unused_pin')->with('unused_pin',$unused_pin);
 }
//========================================view used_ Pin =====================================

 public function view_used_pin()
 {
 	//$used_pin = Pin::where('status',1)->paginate(500);
 	return view('support.view_used_pin');
 }
 public function post_used_pin(Request $request)
 {
      $s =$request->input('session');

    $used_pin = Pin::where([['status',1],['session',$s]])->orderBy('updated_at','DSC')->get();  
  
        $url ="get_used_pin?session=".$s;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        //Create a new Laravel collection from the array data
        $collection = new Collection($used_pin);

        //Define how many items we want to be visible in each page
        $perPage = 500;

        //Slice the collection to get the items to display in current page
        $currentPageSearchResults = $collection->slice(($currentPage- 1) * $perPage, $perPage)->all();

        //Create our paginator and pass it to the view
        $paginatedSearchResults= new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);

       // return view('search', ['results' => $paginatedSearchResults]);
   
    return view('support.view_used_pin')->with('u',$paginatedSearchResults)->with('url',$url)->with('used_pin',$used_pin);
 }
// =======================  seria number  ==================
  public function post_serial_number(Request $request)
 {
      $id =$request->input('serial_number');

    $pin = Pin::find($id);
   if($pin == null)
   {
    $request->session()->flash('warning', ' No Records is not available');
 return redirect()->action([SupportController::class,'view_used_pin']); 
   }
   /*if($pin->status == 0)
   {
     $role =$this->g_rolename(auth::user()->id);
    if($role !='support'){
    $request->session()->flash('warning', ' Pin have not been used.');
    return redirect()->action('SupportController@view_used_pin');
    }
   }*/
   $user = DB::connection('mysql2')->table('users')->where('matric_number',$pin->matric_number)->first();

    return view('support.view_used_pin')->with('pin',$pin)->with('user',$user);
 }
 // =================== convert pin =================================

 public function convert_pin()
 {
return view('support.pin.convert');
 }

 //========post convert pin ================
 public function post_convert_pin(Request $request)
 {
    $s =$request->start_serial_number;
  
    $e =$request->end_serial_number;
    for ($i=$s; $i <= $e; $i++) {

     
        $pin =Pin::find($i);
        if($pin != null)
        {
       /* if($pin->status == 0)
        {*/
          $pin->status = 0;
          $pin->student_type = null;
          $pin->student_id = null;
          $pin->matric_number = null;
          $pin->log1 = 0;
          $pin->log2 = 0;
      $pin->session =$request->session;
     
        $pin->save();
   // }
}
    }
   //dd();
    $request->session()->flash('success', ' SUCCESSFULL');
 return redirect()->action([SupportController::class,'convert_pin']);
 }
 //================== export pin =========================================
 public function export_pin()
 {
  return Excel::download(new PinsExport, 'invoices.xlsx');
   
 }
 /*--------------------------------------function  --------------------------------------------------*/

// =================== get student pin ===========================
     public function student_pin()
     { $d = Department::orderBy('department_name','ASC')->get(); 
     $fos = $this->get_fos();
     $u =User::find(Auth::user()->id);
    
          return view('support.student_pin')->with('d',$d)->with('f',$fos)->with('ud',$u);
     }

      public function get_student_pin(Request $request)
     { 
     $session =$request->session;
     $department =$request->department;
     $fos =$request->fos;
     $l =$request->level;
     $matric_number =array();
    
 // $f =$request->fos;
  $d = Department::orderBy('department_name','ASC')->get();
  $foss = $this->get_fos();
  $ud =User::find(Auth::user()->id); 
  if($l == 1)
  {
    $user = DB::connection('mysql2')->table('users')
    ->join('unical8_exams1.pins', 'pins.student_id', '=', 'users.id')
    ->where([['entry_year',$session],['fos_id',$fos]])->orderBy('matric_number','ASC')
    ->select('users.*','pins.id as pd','pins.pin')
    ->get();
  }else
  {
    $user = DB::connection('mysql2')->table('users')
    ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
    ->join('unical8_exams1.pins', 'pins.student_id', '=', 'users.id')
   ->where('users.fos_id',$fos)
   ->where([['student_regs.session',$session],['student_regs.level_id',$l],['student_regs.semester',1]])
   ->orderBy('matric_number','ASC')
    ->select('users.*','pins.id as pd','pins.pin')
    ->get();
  }
 //dd($user);

   return view('support.student_pin')->with('d',$d)->with('u',$user)->with('di',$department)->with('fos',$fos)
  ->with('g_s',$session)->with('f',$foss)->with('ud',$ud)->with('level',$l);
     } 

     /*--------------------------- reset --------------------------------- */
     public function reset_pin(Request $request)
     {
      if($request->isMethod('post'))
      {
        $pin =Pin::find($request->id);
        if($pin == null){
      $request->session()->flash('warning', 'serial number does not exist');

        }else{
          $role =$this->g_rolename(Auth::user()->id);
          if($role == 'Deskofficer')
          {
            $student = DB::connection('mysql2')->table('users')->find($pin->student_id);

            if(Auth::user()->department_id != $student->department_id)
            {
              $request->session()->flash('warning', 'you can only reset pin of students in these department');
              return back();
             }
             $pinnumber =Pin::where('student_id',Auth::user()->id)->count();

             if(Auth::user()->entry_year == '2016' &&  $pinnumber <= 4 )
             {return back();

             }elseif(Auth::user()->entry_year == '2017' &&  $pinnumber <= 3){
            }
            elseif(Auth::user()->entry_year == '2018' &&  $pinnumber <= 3){

            
          }elseif(Auth::user()->entry_year == '2019' &&  $pinnumber == 1){

          }
          }
        
        $pin->status = 0;
        $pin->student_type = null;
        $pin->student_id = null;
        $pin->matric_number = null;
        $pin->log1 = 0;
        $pin->log2 = 0;
        $pin->session= $request->session;
        $pin->save();
    $request->session()->flash('success', ' SUCCESSFULL');
        }
 return back();
      }
      return view('support.pin.reset_pin');
     }

     public function updateR(Request $request)
     { 
       $crId=array();
    $cr = DB::connection('mysql2')->table('course_regs')
    ->where('course_code','R')->distinct()->get();
    foreach($cr as $v)
    {
$crId[]=$v->registercourse_id;
    }
  $reg=DB::table('register_courses')->whereIn('id',$crId)->get();

   foreach($reg as $vr) 
   {
    if($vr->reg_course_status =='G')
    {
$s='R';
    }else{
      $s=$vr->reg_course_status;
    }
     $c = DB::connection('mysql2')->table('course_regs')
    ->where('registercourse_id', $vr->id)
    ->update(['course_code' => $vr->reg_course_code,'course_title'=>$vr->reg_course_title,'course_status'=>$s]);
   }
  }

//  dvc dash board
public function regCourse()
{
    
    $f = $this->get_faculty();

    return view('admin.dvc.regCourse.index')->with('f',$f);
}

public function getRegCourse(request $request)
{
  $session = $request->session;
  $sm =$request->semester;
  $d = $request->department;
  $f=$request->faculty_id;
  $fos=$request->fos;
  $excel=$request->excel;
  
    if($excel =='excel')
    {
      $department =$this->get_department($d);
      $depart =str_replace(' ', '', $department->department_name);
     
      return Excel::download(new RegisterCourseExport($request->all()), $depart.'.xlsx');
    }

    $r=DB::table('register_courses')->where([['department_id',$d],['semester_id',$sm],['session',$session],['fos_id',$fos]])
    ->whereIn('reg_course_status',['C','E'])
    ->orderBy("level_id",'ASC')
    ->get()->groupBy('level_id');
    
    return view('admin.dvc.regCourse.view')->with('f',$f)->with('s',$session)->with('sm',$sm)->with('reg',$r)->with('d',$d);
    
}
public function getDuplicateUser()
{
    $t=DB::connection('mysql2')->table('users')->select('matric_number', (DB::raw('COUNT(matric_number)')))
    ->groupBy('matric_number')
    ->havingRaw('COUNT(matric_number) > 1')
    ->get();
  
    dd($t);

    return view('admin.support')->with('r',$t);
}
public function getDuplicateCourse()
{
    $t=DB::table('courses')->select('course_code', (DB::raw('COUNT(course_code)')))
    ->groupBy('course_code')
    ->havingRaw('COUNT(course_code) > 1')
    ->get();

    return view('admin.support')->with('r',$t);
}
   
public function getDuplicate()
{
    $t=DB::connection('mysql2')->table('student_results')->select('coursereg_id', (DB::raw('COUNT(coursereg_id)')))
    ->groupBy('coursereg_id')
    ->havingRaw('COUNT(coursereg_id) > 1')
    ->get();
    $c=array();
    if(count($t) > 0){
    foreach($t as $v)
    {
      $value[] =$v->coursereg_id;
    }
    $r=DB::connection('mysql2')->table('student_results')
    ->whereIn('coursereg_id',$value)->get();
    foreach($r as $vv)
    {
      if(in_array($vv->coursereg_id,$c))
      {
        StudentResult::destroy($vv->id);
      }else{
$c[] =$vv->coursereg_id;
      }
    }
  }
dd($c); //var_dump($t);
dd();

    return view('admin.support')->with('r',$r);
}

public function postDuplicate(Request $request)
{
  StudentResult::destroy($request->id);

    return back();
}
public function reports1()
{
  return view('support.reports1.index');
}

public function approveResult()
{
  $f = $this->get_faculty();
  return view('support.dvc.approveResult.index')->with('fc',$f);
}

public function postApproveResult(Request $request)
{
  $date =date('Y-m-d');
  if($request->approve == null){
    $request->session()->flash('warning', ' no student selected');
  }else{
   if($request->approval){
     $updateValue =2;
   }
   else{
    $updateValue =0;
   }
    foreach($request->approve as $v)
    {
    DB::connection('mysql2')
    ->table('student_results')
    ->where([['user_id',$v],['session',$request->session],['level_id',$request->level],['season',$request->season]])
    ->update(['approved' => $updateValue,'approved_date'=>$date]);
    }
    $request->session()->flash('success', ' SUCCESSFULL');
  }
  return view('support.dvc.approveResult.index');
}

public function setupGraduate()
{
  $f = $this->get_faculty();
  return view('support.dvc.setupGraduate.index')->with('fc',$f);
}

public function postSetupGraduate(Request $request)
{
  $gd=$request->gd;
  $y =substr($gd,0,4);
  if($request->graduate == null){
    $request->session()->flash('warning', ' no student selected');
  }else{
    foreach($request->graduate as $v)
    {
    DB::connection('mysql2')->table('users')->where('id',$v)
    ->update(['date_of_graduation' => $gd,'year_of_graduation'=>$y,'graduation_status'=>1]);
    }
    $request->session()->flash('success', ' SUCCESSFULL');
  }
  return view('support.dvc.setupGraduate.index');
}

public function viewGraduate()
{
  $f = $this->get_faculty();
  return view('support.dvc.viewGraduate.index')->with('fc',$f);
}

public function postViewGraduate(Request $request)
{
  if($request->excel !=null)
  {
  return Excel::download(new Graduate($request->all()),'ers.xlsx');
  }
  $d =$request->department; 
  $s=$request->start;
  $e=$request->end;
  $u =DB::connection('mysql2')->table('users')->where([['department_id',$d],['graduation_status',1]])
  ->whereBetween('date_of_graduation', [$s, $e])->get();
  return view('support.dvc.viewGraduate.view')->with('u',$u)->with('d',$d);
}

public function oldportalviewGraduate()
{
  $f = DB::connection('oldporta')->table('faculties')->orderBy('faculties_name', 'ASC')->get();
  return view('support.dvc.oldportalviewGraduate.index')->with('fc',$f);
}

public function postoldportalViewGraduate(Request $request)
{
  if($request->excel !=null)
  {
  return Excel::download(new Graduate($request->all()),'ers.xlsx');
  }
  $d =$request->department; 
  $s=$request->start;
  $e=$request->end;
  $u =DB::connection('mysql2')->table('users')->where([['department_id',$d],['graduation_status',1]])
  ->whereBetween('date_of_graduation', [$s, $e])->get();
  return view('support.dvc.viewGraduate.view')->with('u',$u)->with('d',$d);
}
//==================== individual result ===========================================
public function individualResult(Request $request)
    {
            $u = DB::connection('mysql2')->table('users')->where('matric_number',$request->matric_number)->first();

            $studentDetails = DB::connection('mysql2')->table('student_regs')
                    ->join('course_regs', 'course_regs.studentreg_id', '=', 'student_regs.id')
                    ->leftjoin('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
                    ->where([['student_regs.user_id', $u->id]])
                    ->where([['course_regs.user_id', $u->id]])
                    ->orderBy('student_regs.session','ASC')
                    ->orderBy('course_regs.semester_id', 'ASC')
                    ->orderBy('course_regs.course_code', 'ASC')
                   ->select('course_regs.*','student_results.ca','student_results.exam','student_results.total')
                    ->get()
                    ->groupBy('session');
                   return view('support.individualResult.index')->with('s',$studentDetails)->with('u',$u);
       
    }

    //==============================class attendace==========================
    public function admin_classAttendance()
    {
      $fos = $this->get_fos();
      $p = $this->getp();
      $f = $this->get_faculty();
     
      return view('support.admin_classAttendance.index')->with('f',$fos)->with('fc',$f)->with('p',$p);
    }

    public function changeTFCtoTMS(){
      $c =DB::table('courses')->where('course_code');
    }

    //========================== solve business management isses===

    /*function updateRegisterCourseTable()
    {
      $fos =7;
      $dfos=720;
    
      $r=RegisterCourse::where([['fos_id',$fos],['reg_course_status','G']])
      ->whereIn('session',['2018','2019'])
      ->whereIn('level_id',[2,3])->get();
      foreach($r as $v){
        $check=RegisterCourse::where([['course_id',$v->course_id],['fos_id',$dfos],['reg_course_status','G'],['session',$v->session],['level_id',$v->level_id]])
      ->first();
      if($check == null){
        $data[] =['course_id'=>$v->course_id,'programme_id'=>$v->programme_id,'department_id'=>$v->department_id,'faculty_id'=>$v->faculty_id,'fos_id'=>$dfos,'specialization_id'=>0,'level_id'=>$v->level_id,'semester_id'=>$v->semester_id,'reg_course_title'=>$v->reg_course_title,'reg_course_code'=>$v->reg_course_code,'reg_course_unit'=>$v->reg_course_unit,'reg_course_status'=>"G",'session'=>$v->session];
  
      }
      }
      if(!empty($data)) 
      {
      DB::table('register_courses')->insert($data);
      echo'success';
    }else{
      echo'no insert';
    }
    }*/

    public function autoAssignCourse()
    {
      $data =array();
      $reg=RegisterCourse::where([['reg_course_code','like', '%GSS%'],['session','2019']])->get();
      foreach($reg as $v){
        $assign_course = AssignCourse::where([['registercourse_id', $v->id], ['user_id',2459]])->first();
        if($assign_course == null){

      $data[] = ['registercourse_id' => $v->id, 'user_id' => 2459, 'department_id' => $v->department_id, 'faculty_id' => $v->faculty_id, 'fos_id' => $v->fos_id, 'level_id' => $v->level_id, 'session' => $v->session, 'semester_id' => $v->semester_id];

      }
    }

      DB::table('assign_courses')->insert($data);
dd('success');
      
    }
  
    public function updateCourse()
    {
  
      //RegisterCourse::find(95765);
     // $u=DB::connection('mysql2')->table('users')->where('fos_id',674)->get();
     /* $u = DB::connection('mysql2')->table('users')
      ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
      ->select('users.*','level_id')
      ->where([['student_regs.session','2019'],['level_id',1],['semester',1],['fos_id',674]])
      ->distinct()->get();
     
      foreach($u as $v)
      {
        $id[]=$v->id;
      }

      CourseReg::where([['course_code','BIO112'],['course_status','C'],['session','2019']])
      ->whereIn('user_id',$id)->update(['registercourse_id'=>95765]);
      dd('success');*/
      /*$cc=DB::table('courses')->select('course_code', (DB::raw('COUNT(course_code)')))
      ->limit(5)
      ->groupBy('course_code')
      ->havingRaw('COUNT(course_code) > 1')
      ->get();
     //dd($cc);
     $dp ='';
     $l ='';
     $cc =RegisterCourse::where([['department_id',$dp],['level_id',$l]])->get();
      foreach($cc as $c){
      $t =Course::where('course_code',$c->reg_course_code)->orderBy('id','ASC')->first();
      $id =$t->id;
      //$reg =RegisterCourse::where('reg_course_code',$c->course_code)->select('course_id')->distinct('course_id')->get();
     
      RegisterCourse::where('reg_course_code',$c->reg_course_code)->update(['course_id'=>$id]);
      CourseReg::where('course_code',$c->reg_course_code)->update(['course_id'=>$id]);
      $cr =CourseReg::where('course_code',$c->reg_course_code)->get();
      foreach($cr as $v){
      StudentResult::where('coursereg_id',$v->id)->update(['course_id'=>$id]);
      StudentResultBackup::where('coursereg_id',$v->id)->update(['course_id'=>$id]);
      }
      DB::table('courses')->where([['course_code',$c->course_code],['id','!=',$id]])->delete();
    }

      dd('success');*/

    }

    public function updateCourse2()
    {
    
      $ccc=DB::table('courses')->select('course_code', (DB::raw('COUNT(course_code)')))
      ->groupBy('course_code')
      ->havingRaw('COUNT(course_code)  >1')
      ->get();
      foreach($ccc as $d)
      {
        $dd[]=$d->course_code;
      }

      $cc =DB::table('courses')->whereNotIn('course_code',$dd)->get();
     
      foreach($cc as $c){
      $cr =CourseReg::where('course_code',$c->course_code)->get();
      foreach($cr as $v){
      StudentResult::where('coursereg_id',$v->id)->update(['course_id'=>$c->id]);
      StudentResultBackup::where('coursereg_id',$v->id)->update(['course_id'=>$c->id]);
      }
      }
      dd('success');

    }


    public function updateCourse3()
    {
      $cc=['ECO111'];
     
      foreach($cc as $c){
       /* $job = (new \App\Jobs\UpdateCourse())
        ->delay(now()->addSeconds(60)); 
         dispatch($job);
         dd('succcess');*/
     
     /* $cc=DB::table('courses')->select('course_code', (DB::raw('COUNT(course_code)')))
      ->groupBy('course_code')
      ->havingRaw('COUNT(course_code) >1')
      ->get();*/
      
     // $c=$cc->course_code;
      $t =Course::where('course_code',$c)->orderBy('id','ASC')->first();
      $id =$t->id;
      RegisterCourse::where('reg_course_code',$c)->update(['course_id'=>$id]);
      CourseReg::where('course_code',$c)->update(['course_id'=>$id]);
      $cr =CourseReg::where('course_code',$c)->get();
      foreach($cr as $v){
      StudentResult::where('coursereg_id',$v->id)->update(['course_id'=>$id]);
      StudentResultBackup::where('coursereg_id',$v->id)->update(['course_id'=>$id]);
      }
     
      DB::table('courses')->where([['course_code',$c],['id','!=',$id]])->delete();
    }

      dd('success'.' '.$c);

    }


    public function apiWithoutKey()
    {//dd(';');
        $client = new Client(); //GuzzleHttp\Client
        $url = "https://portalapi.unical.sch.ng/prod/student/v1/api/ExternalQuery/Authenticate";


        $response = $client->request('POST', $url, [
          
            'json' => [
              'username' => 'myexbackend@portal.com',
              'password'=> 'dWq6R2#:Cp/X',
             
          ]
           
        ]);

        $responseBody = json_decode($response->getBody());
        
        

        $params = [
          "reg_no"=>'',
          "dept_id" => 1,
          "fac_id" =>1,
        ];
        
        $request =utf8_encode(json_encode($params));
     $key =utf8_encode($responseBody->cipherKey);
        $hmac =$this->GetHMacHeader( $request, $key);
        $headers = [
          'X-TIM-Signature'  => $hmac,
            'token' => $responseBody->token,
            
        ];

        $client1 = new Client();
        $url1 = "https://portalapi.unical.sch.ng/prod/student/v1/api/ExternalQuery/GetBioData";
        $response = $client1->request('POST', $url1, [
             'json' => $params,
            'headers' => $headers,
            
        ]);

        $responseBody = json_decode($response->getBody());

        
        
        
    }

    public function apiWithKey()
    {
        $client = new Client();
        $url = "https://dev.to/api/articles/me/published";

        $params = [
            //If you have any Params Pass here
        ];

        $headers = [
            'api-key' => 'k3Hy5qr73QhXrmHLXhpEh6CQ'
        ];

        $response = $client->request('GET', $url, [
            // 'json' => $params,
            'headers' => $headers,
            'verify'  => false,
        ]);

        $responseBody = json_decode($response->getBody());
dd($responseBody);
       
    }

    public function GetHMacHeader( $request, $key)
{
  
  $sig = hash_hmac('sha256', $request, $key);
 return $sig;
}

public function asc ()
{
  $s ='HELLO';
$a =pack('C*',$s);
echo $a;
}

public function studentImport(Request $request)
{
  $errors=array();

  if($request->file('student'))
  {
      
      try {
  $path = $request->file('student');

Excel::import(new StudentsImport($request->all()),$path);
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

Session::flash('success', "SUCCESSFULL.");
  return back();
   }
}

//=================================reverse grade=====================

public function reverseGrade($examofficer)
{

  $r=StudentResult::where('examofficer',$examofficer)->get();
 
  if(count($r) > 0){
  foreach($r as $v){
    $u=UpdateResult::where('student_results_id',$v->id)->first();
    if($u != null){
    $grade_value = $this->get_grade($u->total);
    $grade = $grade_value['grade'];
    $cp = $this->mm($grade, $u->cu);
    
    $data=['reverse'=>1,'ca'=>$u->ca,'exam'=>$u->exam,'total'=>$u->total,'examofficer'=>$u->former_deskofficer_id,'post_date'=>$u->posted,'grade'=>$grade,'cp'=>$cp['cp']];
    $sr=DB::connection('mysql2')->table('student_results')->where('id',$v->id)->update($data);
  $u->delete();
    }

  }
  dd('success');
  }
  dd('emptyresult');
}

public function displayResultTable()
{
  $r=StudentResult::where('updated_at','!=',null)->paginate(10000);
  dd($r);
  return view('support.displayResultTable.index')->with('r',$r);
}
public function firstClassStudent()
{
  $f=Faculty::get();
  return view('support.firstClassStudent.index')->with('f',$f);
}
public function postfirstClassStudent(Request $request)
{
  $fac=$request->faculty;$dId=array();$s=$request->session;$classDegree=$request->classDegree;
  $ff=Faculty::get();
  $faculty=Department::where('faculty_id',$fac)->get();
  foreach($faculty as $d){
    $dId[] =$d->id;
  }

 // $fos=Fos::whereIn('department_id',$dId)->select('department_id','duration')->distinct()->get();
  $fos=Fos::whereIn('department_id',$dId)->get();

/*foreach($fos as $v)
  {
 /*   $studentReg =StudentReg::where([['department_id',$v->department_id],['level_id','>=',$v->duration],['semester',1],['season','NORMAL'],['session',$s]])
    ->get()->groupBy('department_id');*/
 /*$user= DB::connection('mysql2')->table('users')
    ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
    ->select('users.*','level_id')
    ->where([['student_regs.session',$s], ['student_regs.faculty_id',$f],['fos_id',$v->id],
    ['student_regs.department_id',$v->department_id],['level_id','>=',$v->duration],['semester',1],['season','NORMAL']])->orderBy('department_id','Asc')
    ->distinct()->get();
  }*/
  //dd($fos);
  return view('support.firstClassStudent.index')->with('fos',$fos)->with('s',$s)->with('f',$ff)->with('fac',$fac)->with('classDegree',$classDegree);

}

//https://serversforhackers.com/laravel-perf


}
