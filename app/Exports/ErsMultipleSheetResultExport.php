<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;

class ErsMultipleSheetResultExport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(array $request,string $department,string $faculty)
    {
      $this->request =$request; 
      $this->fos = $request['fos'];
        $this->l = $request['level'];
        $this->s = $request['session'];
        $this->season=$request['season'];
        $this->semester = $request['semester'];
        $this->department =$department;
        $this->faculty =$faculty;
        

        
    }
    public function sheets(): array
    {
        $sheets = [];

        $regCourse =DB::table('register_courses')->where([['fos_id',$this->fos],['level_id',$this->l],['semester_id',$this->semester],['session',$this->s]])
        ->whereIn('reg_course_status',['C','E'])->get();
        if(count($regCourse) != 0)
        {
        
            foreach($regCourse as $v)
            {
                $vId[] =$v->id;
                $regDetail[$v->id] =
                ['title'=>$v->reg_course_title,'code'=>$v->reg_course_code,'unit'=>$v->reg_course_unit,'status'=>$v->reg_course_status];
                $sheets[] = new ResultExport($this->request,$v->id,$v->reg_course_title,$v->reg_course_code,$v->course_id,$this->department,$this->faculty);
            }

           
        }

        return $sheets;
    }
}
