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

class UserGssImportII implements ToCollection, SkipsEmptyRows, WithHeadingRow, WithValidation
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
        return 4;
    }
    public function __construct(array $request)
    {
        $period = $request['period'];
        $course_id = $request['course_id'];
        $session = $request['session'];

        $register_course =DB::table('register_courses')
        ->where([['course_id',$course_id],['session',$session]])
        ->get();

        $user_with_result =array();
        $register_course_id=array();
        foreach($register_course as $v)
        {
        $register_course_id [] =$v->id;
        }
      
        $result =DB::connection('mysql2')->table('course_regs')
        ->join('student_results','student_results.coursereg_id','=','course_regs.id')
        ->where([['student_results.course_id',$course_id],['student_results.session',$session],['student_results.season',$period]])
        ->whereIn('registercourse_id',$register_course_id)
        ->get();
        
   
        if(count($result) > 0){
    foreach($result as $v)
    {
      $user_with_result [] = $v->user_id;
    }
 
        }
        
          $this->user = DB::connection('mysql2')->table('users')
              ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
              ->where([['course_regs.period', $period],['course_id',$course_id],['session',$session]])
              ->whereIn('registercourse_id',$register_course_id)
              ->whereNotIn('users.id', $user_with_result)
              ->orderBy('users.matric_number', 'ASC')
              ->select('course_regs.*','users.matric_number','users.jamb_reg', 'users.entry_year')
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
        // dd($this->user);
          foreach($this->user->chunk(100) as $users){
            $insert_data=array();
          foreach($users as $v)
          {
        /*    $result_exist =DB::connection('mysql2')
            ->table('student_results')
            ->where([['coursereg_id',$v->id],['user_id',$v->user_id]])
            ->first();  
          if($result_exist == Null ) 
          {  */   
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
                     $cp = $this->mm($grade, $v->course_unit);
                      $insert_data[] =['user_id' => $v->user_id, 'matric_number' => $v->matric_number, 'scriptNo' => $scriptno, 'course_id' => $v->course_id, 'coursereg_id' => $v->id, 'ca' => $ca, 'exam' => $exam, 'total' => $total, 'grade' => $grade,
                     'cu' => $v->course_unit, 'cp' => $cp['cp'], 'level_id' =>$v->level_id,
                    'session' => $v->session, 'semester' => $v->semester_id, 'status' => 0, 'season' => $v->period, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date'=>$date,'approved' => 0];
                  
                }
                }elseif(in_array($v->jamb_reg,$keys)){
                    if($v->jamb_reg == $excelData[$v->jamb_reg]['matric_number']){
                
                        $ca = $excelData[$v->jamb_reg]['ca'];
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
                    
                    $insert_data[] =['user_id' => $v->user_id, 'matric_number' => $v->matric_number, 'scriptNo' => $scriptno, 'course_id' => $v->course_id, 'coursereg_id' => $v->id, 'ca' => $ca, 'exam' => $exam, 'total' => $total, 'grade' => $grade,
                     'cu' =>$v->course_unit, 'cp' => $cp['cp'], 'level_id' =>$v->level_id,
                    'session' => $v->session, 'semester' => $v->semester_id, 'status' => 0, 'season' => $v->period, 'flag' => $flag, 'examofficer' => Auth::user()->id, 'post_date'=>$date,'approved' => 0];
                  
                }
                }
           // }
            }
       // dd($insert_data);

            if(!empty($insert_data))
            {
            DB::connection('mysql2')->table('student_results')->insert($insert_data);
            }
        }
           
        
            return 1;
           } 
}
   
}
