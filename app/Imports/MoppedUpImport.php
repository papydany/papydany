<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use App\Models\StudentResult;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\MyTrait;

class MoppedUpImport implements ToCollection, SkipsEmptyRows, WithHeadingRow, WithValidation
{
    use Importable;
    use MyTrait;
   
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    private $user;
  
    public function rules(): array
    {
        return [
            'matricno' => [
                'required',
            ],
        ];
    }
    public function headingRow(): int
    {
        return 6;
    }
    public function __construct(array $request)
    {
      
        $period = $request['period'];
        $course_id = $request['course_id'];
        $department_id = $request['department_id'];
        $semester =$request['semester'];

        $register_course =DB::table('register_courses')
        ->where([['course_id',$course_id],['department_id',$department_id]])
        ->get();

        $user_with_result =array();
        $register_course_id=array();
        $srArray=array();
        foreach($register_course as $v)
        {
        $register_course_id [] =$v->id;

        }

        $sr = DB::connection('mysql2')->table('student_regs')
        ->where([['department_id',$department_id],['moppedUp',1],['semester',$semester]]) 
        ->orderBy('session','ASC')->get();
        if($sr->count() != 0){
        foreach($sr as $v){
       $srArray[]=$v->id;
        }
    }

        $result =DB::connection('mysql2')->table('course_regs')
        ->join('student_results','student_results.coursereg_id','=','course_regs.id')
        ->where([['student_results.course_id',$course_id],['student_results.season',$period]])
        ->whereIn('registercourse_id',$register_course_id)
        ->whereIn('studentreg_id',$srArray)
        ->get();
        
   
        if(count($result) > 0){
    foreach($result as $v)
    {
      $user_with_result [] = $v->user_id;
    }
 
        }

        $this->user = DB::connection('mysql2')->table('users')
        ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
        ->where([['course_regs.period', $period],['course_id',$course_id]])
        ->whereIn('registercourse_id',$register_course_id)
        ->whereIn('studentreg_id',$srArray)
        ->orderBy('users.matric_number', 'ASC')
        ->select('course_regs.*','users.matric_number','users.jamb_reg', 'users.entry_year')
        ->get();
        
        /*  $this->user = DB::connection('mysql2')->table('users')
              ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
              ->where([['course_regs.period', $period],['course_id',$course_id]])
              ->whereIn('registercourse_id',$register_course_id)
              ->whereNotIn('users.id', $user_with_result)
              ->whereIn('studentreg_id',$srArray)
              ->orderBy('users.matric_number', 'ASC')
              ->select('course_regs.*','users.matric_number','users.jamb_reg', 'users.entry_year')
              ->get();*/
              
             
              return $this->user;
    }




    public function collection(Collection $data)
    {
      

        if(count($this->user) == 0)
        {
            return 0;
        }else{
            $flag ='MoppedUp';
            $date = date("Y-m-d H:i:s");
        
        $excelData=array();
        $insert_data=array();
        //if(!empty($data))
        //{
          
          foreach ($data as $row)
          {
           if(is_numeric($row['exam'])){
              $total = $row['exam'];
            $excelData[$row['matricno']] =['matric_number' => $row['matricno'],'scriptNo' => $row['scriptno'],
            'exam'=>$row['exam'],'total'=>$total];
           }
       
          }
        
          $keys = array_keys($excelData);
        
          foreach($this->user as $v)
          {
              
      if(in_array($v->matric_number,$keys))
      {
                    if($v->matric_number == $excelData[$v->matric_number]['matric_number']){
                
                     
                        $exam =$excelData[$v->matric_number]['exam'];
                        $total =$excelData[$v->matric_number]['total'];
                        
                        $scriptno =$excelData[$v->matric_number]['scriptNo'];
                        if($scriptno == '')
                        {
                            $scriptno =0;
                        }
                      
                            $grade_value = $this->get_grade($total);
                        
                        $grade = $grade_value['grade'];
                     
                     $cp = $this->mm($grade, $v->course_unit);
                     $check = StudentResult::where([['course_id', $v->course_id], ['coursereg_id', $v->id], ['user_id', $v->user_id],['approved','!=',2]])->first();

                     // update back table if records exist
                     if ($check != null) {
                       
                         $check->grade = $grade;
                         $check->cp =$cp['cp'];
                         $check->ca = 0;
                         $check->exam = $exam;
                         $check->total=$total;
                         $check->examofficer=Auth::user()->id;
                         $check->save();
                     }else{
                    
                    $insert_data[] =['user_id' => $v->user_id, 'matric_number' => $v->matric_number, 'scriptNo' => $scriptno, 'course_id' => $v->course_id, 'coursereg_id' => $v->id, 'ca' =>0, 'exam' => $exam, 'total' => $total, 'grade' => $grade,
                     'cu' => $v->course_unit, 'cp' => $cp['cp'], 'level_id' => $v->level_id,
                    'session' => $v->session, 'semester' => $v->semester_id, 'status' => 0, 'season' => $v->period, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date'=>$date,'approved' => 0];
                     }
                }
                }elseif(in_array($v->jamb_reg,$keys)){
                    if($v->jamb_reg == $excelData[$v->jamb_reg]['matric_number']){
                
                     
                        $exam =$excelData[$v->jamb_reg]['exam'];
                        $total =$excelData[$v->jamb_reg]['total'];
                        
                        $scriptno =$excelData[$v->jamb_reg]['scriptNo'];
                        if($scriptno == '')
                        {
                            $scriptno =0;
                        }
                      
                            $grade_value = $this->get_grade($total);
                        
                        $grade = $grade_value['grade'];
                     $cp = $this->mm($grade,$v->course_unit);
                     $check = StudentResult::where([['course_id', $v->course_id], ['coursereg_id', $v->id], ['user_id', $v->user_id],['approved','!=',2]])->first();

                 // update back table if records exist
                 if ($check != null) {
                   
                     $check->grade = $grade;
                     $check->cp =$cp['cp'];
                     $check->ca = 0;
                     $check->exam = $exam;
                     $check->total=$total;
                     $check->examofficer=Auth::user()->id;
                     $check->save();
                 }else{
                    
                    $insert_data[] =['user_id' => $v->user_id, 'matric_number' => $v->matric_number, 'scriptNo' => $scriptno, 'course_id' => $v->course_id, 'coursereg_id' => $v->id, 'ca' =>0, 'exam' => $exam, 'total' => $total, 'grade' => $grade,
                     'cu' => $v->course_unit, 'cp' => $cp['cp'], 'level_id' => $v->level_id,
                    'session' => $v->session, 'semester' => $v->semester_id, 'status' => 0, 'season' => $v->period, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date'=>$date,'approved' => 0];
                 }
                }
                }
            }

            if(!empty($insert_data))
            {
                DB::connection('mysql2')->table('student_results')->insert($insert_data);
              return 1;
             }
           } 
}
}
