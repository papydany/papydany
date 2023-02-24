<?php

namespace App\Imports;
use App\Http\Traits\MyTrait;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;

class UsersImport implements ToCollection, SkipsEmptyRows, WithHeadingRow, WithValidation
{
    use Importable;
    use MyTrait;
    const MEDICINE = 14;
    const DENTISTRY = 10;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    private $user;
    private $unit;
    private $faculty_id;
    private $l;
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

        $id = $request['registeredCourseId'];
        $period = $request['period'];
        $course_id = $request['course_id'];
        $department_id = $request['department_id'];
        $fos_id = $request['fos_id'];
      //  $this->l = $request['level'];
        $session = $request['session'];
        $semester = $request['semester'];
        $this->unit =$request['unit'];
        $this->faculty_id = $request['faculty_id'];

        $register_course =DB::table('register_courses')
        ->where([['course_id',$course_id],['department_id',$department_id],['session',$session],['fos_id',$fos_id]])
        ->get();
        
        $user_with_result =array();
        $register_course_id=array();
        foreach($register_course as $v)
        {
        $register_course_id [] =$v->id;
        }
        $result =DB::connection('mysql2')->table('course_regs')
        ->join('student_results','student_results.coursereg_id','=','course_regs.id')
        ->where([['semester_id',$semester],['student_results.course_id',$course_id],['student_results.session',$session],['student_results.season',$period]])
        ->whereIn('registercourse_id',$register_course_id)
        ->get();
       /* $result =DB::connection('mysql2')->table('student_results')
        //->where([['course_id',$course_id],['level_id',$this->l],['session',$session],['semester',$semester],['season',$period]])
        ->where([['course_id',$course_id],['session',$session],['semester',$semester],['season',$period]])
       ->get();*/
        
        
        if(count($result) > 0){
    foreach($result as $v)
    {
      $user_with_result [] = $v->user_id;
    }
        }
        
          /*$this->user = DB::connection('mysql2')->table('users')
              ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
              ->where('course_regs.registercourse_id', $id)
              ->where('course_regs.period', $period)
              ->whereNotIn('users.id', $user_with_result)
              ->orderBy('users.matric_number', 'ASC')
              ->select('course_regs.*','users.matric_number', 'users.entry_year')
              ->get();*/
              $this->user = DB::connection('mysql2')->table('users')
              ->join('course_regs','course_regs.user_id','=','users.id')
              ->where([['course_regs.period', $period],['course_id',$course_id],['session',$session]])
              ->whereIn('registercourse_id',$register_course_id)
              ->whereNotIn('users.id', $user_with_result)
              ->orderBy('users.matric_number', 'ASC')
              ->select('course_regs.*','users.matric_number','users.entry_year')
              ->get();
       
              return $this->user;
    }



    public function collection(Collection $data)
    {
       

        if(count($this->user) == 0)
        {
            return 0;
        }else{
            $flag ='sessional';
            $date = date("Y-m-d H:i:s");
        
        $excelData=array();
        $insert_data=array();
        //if(!empty($data))
        //{
          
          foreach ($data as $row)
          {
           if(is_numeric($row['ca']) && is_numeric($row['exam'])){
              $total = $row['ca'] + $row['exam'];
            $excelData[$row['matricno']] =['matric_number' => $row['matricno'],'scriptNo' => $row['scriptno'], 'ca' => $row['ca'],
            'exam'=>$row['exam'],'total'=>$total];
           }
       
          }
         
          $keys = array_keys($excelData);
          
          foreach($this->user as $v)
          {
              
      if(in_array($v->matric_number,$keys))
      {
                    if($v->matric_number == $excelData[$v->matric_number]['matric_number']){
                
                        $ca = $excelData[$v->matric_number]['ca'];
                        $exam =$excelData[$v->matric_number]['exam'];
                        $total =$excelData[$v->matric_number]['total'];
                        
                        $scriptno =$excelData[$v->matric_number]['scriptNo'];
                        if($scriptno == '')
                        {
                            $scriptno =0;
                        }
                        if ($this->faculty_id == Self::MEDICINE || $this->faculty_id == Self::DENTISTRY) {
                            $grade_value = $this->get_grade_medicine($total, $v->period,$v->level_id);
                        } else {
                            $grade_value = $this->get_grade($total);
                        }
                        $grade = $grade_value['grade'];
                     $cp = $this->mm($grade, $v->course_unit);
                    
                    $insert_data[] =['user_id' => $v->user_id, 'matric_number' => $v->matric_number, 'scriptNo' => $scriptno, 'course_id' => $v->course_id, 'coursereg_id' => $v->id, 'ca' => $ca, 'exam' => $exam, 'total' => $total, 'grade' => $grade,
                     'cu' => $v->course_unit, 'cp' => $cp['cp'], 'level_id' =>$v->level_id,
                    'session' => $v->session, 'semester' => $v->semester_id, 'status' => 0, 'season' => $v->period, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date'=>$date,'approved' => 0];
                  
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
