<?php

namespace App\Exports;

use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\RegisterCourse;

class classAttendanceSoft implements FromView,ShouldAutoSize,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
  
 private $id,$s,$d,$fos,$semester,$courseReg;
 private $reg,$department;
            
            public function __construct($id,$s,$d,$fos,$semester)
            {
            
            $this->id = $id;
            $this->s =$s;
            $this->d =$d;
            $this->fos =$fos;
            $this->semester =$semester;
            $this->department = Department::find($d);
          
            $this->reg = RegisterCourse::where([['id', $this->id], ['session', $this->s]])->first();
            $this->courseReg = DB::connection('mysql2')->table('users')
            ->join('course_regs', 'users.id', '=', 'course_regs.user_id')
             ->where([['users.department_id', $this->d],['course_regs.session', $s], ['course_regs.semester_id', $semester],['course_regs.course_id', $this->reg->course_id]])
             ->select('course_regs.*', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number','users.image_url')
             ->orderBy('level_id','ASC')->orderBy('users.matric_number','ASC')
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
                
        return view('admin.regcourse.attendance', [
              
                    'courseReg' => $this->courseReg,'code'=>$this->reg->reg_course_code,'session'=>$this->s,
                    'semester'=>$this->semester,'department'=>$this->department
                ]);
               }
        }
    
    

