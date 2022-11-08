<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class CourseExport implements FromView, ShouldAutoSize,WithStyles
{
    
    private $user;
        private $courseTitle;
        private $regId;
        private $courseReg;
    
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
        //$this->status =$status;

        
     
           
            /*$this->user  = DB::connection('mysql2')->table('users')
               ->join('course_regs', 'users.id', '=', 'course_regs.user_id')
                ->where([['users.fos_id', $this->fos], ['course_regs.session', $this->s], ['course_regs.semester_id', $this->semester], ['course_regs.level_id', $this->l]])
                ->where('registercourse_id',$this->id)
                ->select('course_regs.*', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number','users.image_url')
                ->orderBy('users.surname','ASC')
                ->get();*///->groupBy('registercourse_id');
                
                $this->user  = DB::connection('mysql2')->table('users')
                ->join('course_regs', 'users.id', '=', 'course_regs.user_id')
                 ->where([['users.fos_id', $this->fos], ['course_regs.session', $this->s], ['course_regs.semester_id', $this->semester]])
                 ->where('course_id',$this->course_id)
                 ->select('course_regs.*', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number','users.image_url')
                 ->orderBy('users.matric_number','ASC')
                 ->get();
            }
        
    
        /**
         * @return string
         */
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

  /*      public function map($invoice): array
    {
        return [
           'G'=>'E' + 'F',
        ];
    }*/
    
        public function view(): View
        {
            
    return view('desk.register_student.ers_excel', [
                'user' => $this->user,'title'=>$this->title,'code'=>$this->code,'session'=>$this->s,
                'level'=>$this->l,'faculty'=>$this->faculty,'department'=>$this->department,'semester'=>$this->semester
            ]);
           }
    }

