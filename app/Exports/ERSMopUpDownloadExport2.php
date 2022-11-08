<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ERSMopUpDownloadExport2 implements FromView,ShouldAutoSize,WithStyles

{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $user;
        
    public function __construct($id,$code,$title,$semester)
    {
        $this->id = $id;
        $this->code =$code;
        $this->title =$title;
        $this->s ='2020 / 2021';
        $this->semester =$semester;

        $this->user = DB::connection('mysql2')->table('users')
        ->join('student_regs', 'users.id', '=', 'student_regs.user_id')
        ->join('course_regs', 'student_regs.id', '=', 'course_regs.studentreg_id')
        ->where([['course_regs.semester_id', $this->semester],['student_regs.semester', $this->semester],
        ['course_regs.course_id',$this->id],['student_regs.moppedUp',1]])
         ->select('course_regs.*', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number')
         ->orderBy('users.matric_number','ASC')
         ->get(); 
       
       
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
          
        ];
    }
   
    public function view(): View
    {
        
return view('moppedUp.ers_excel2', [
    'user' => $this->user,'title'=>$this->title,'code'=>$this->code,'session'=>$this->s
        ]);
       }
}
