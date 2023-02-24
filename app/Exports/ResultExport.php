<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResultExport implements FromView, ShouldAutoSize,WithStyles
{
    private $user;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(array $request,int $regId, String $title,String $code,int $course_id, string $department,string $faculty)
    {
    $this->fos = $request['fos'];
    $this->l = $request['level'];
    $this->s = $request['session'];
    $this->season=$request['season'];
    $this->semester = $request['semester'];
    $this->id = $regId;
    $this->code =$code;
    $this->title =$title;
    $this->course_id=$course_id;
    $this->faculty =$faculty;
    $this->department=$department;
 
    $this->user= DB::connection('mysql2')->table('users')
    ->join('course_regs', 'course_regs.user_id', '=', 'users.id')
    ->leftjoin('student_results', 'course_regs.id', '=', 'student_results.coursereg_id')
    ->where([['course_regs.registercourse_id',$this->id], ['course_regs.level_id',$this->l],['course_regs.semester_id',$this->semester],['course_regs.session',$this->s],['course_regs.course_id',$course_id],['course_regs.period',$this->season]])
     ->orderBy('users.matric_number', 'ASC')
    ->select('course_regs.*','student_results.ca','student_results.exam','student_results.total','student_results.grade', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number')
    ->get();
        }
        
        public function styles(Worksheet $sheet)
        {
            return [
                // Style the first row as bold text.
                1    => ['font' => ['bold' => true]],
                2    => ['font' => ['bold' => true]],
                3    => ['font' => ['bold' => true]],
                4    => ['font' => ['bold' => true]],
                6   => ['font' => ['bold' => true]],
            ];
        }


    
        public function view(): View
        {
            
    return view('desk.register_student.ers_result_excel', [
                'user' => $this->user,'title'=>$this->title,'code'=>$this->code,'session'=>$this->s,
                'level'=>$this->l,'faculty'=>$this->faculty,'department'=>$this->department,'semester'=>$this->semester
            ]);
           }
}
