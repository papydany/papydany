<?php

namespace App\Imports;
use App\Http\Traits\MyTrait;
use Illuminate\Support\Facades\DB;
use App\Models\StudentResult;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Auth;

class UserGssUpdateImport implements ToCollection, SkipsEmptyRows, WithHeadingRow, WithValidation
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
                'string',
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
        $session = $request['session'];
        $department_id = $request['department_id'];

        $register_course =DB::table('register_courses')
        ->where([['course_id',$course_id],['department_id',$department_id],['session',$session]])
        ->get();

        $register_course_id=array();
        foreach($register_course as $v)
        {
        $register_course_id [] =$v->id;
        }

        
          $this->user = DB::connection('mysql2')->table('users')
              ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
              ->where([['course_regs.period', $period],['course_id',$course_id],['session',$session]])
              ->whereIn('registercourse_id',$register_course_id)
             
              ->orderBy('users.matric_number', 'ASC')
              ->select('course_regs.*','users.matric_number', 'users.entry_year')
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
                      $grade_value = $this->get_grade($total);
                    $grade = $grade_value['grade'];
                     $cp = $this->mm($grade, $this->unit);
                     // check for update
            $check = StudentResult::where([['course_id', $v->course_id], ['coursereg_id', $v->coursereg_id], ['user_id', $v->user_id]])->first();

                 // update back table if records exist
                 if ($check != null) {
                   
                     $check->grade = $grade;
                     $check->cp = $cp;
                     $check->ca = $ca;
                     $check->exam = $exam;
                      $check->total=$total;
                     $check->save();
                 }else{
                    
                    $insert_data[] =['user_id' => $v->user_id, 'matric_number' => $v->matric_number, 'scriptNo' => $scriptno, 'course_id' => $v->course_id, 'coursereg_id' => $v->id, 'ca' => $ca, 'exam' => $exam, 'total' => $total, 'grade' => $grade,
                     'cu' => $this->unit, 'cp' => $cp['cp'], 'level_id' => $this->l,
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
