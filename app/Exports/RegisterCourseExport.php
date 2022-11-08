<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;



class RegisterCourseExport implements FromView, ShouldAutoSize,WithStyles
{
    private $reg;
   

    public function __construct(array $request)
    {
   
            $this->session = $request['session'];
            $this->sm =$request['semester'];
            $this->d = $request['department'];
            $this->f=$request['faculty'];
            $this->fos=$request['fos'];

        
            $this->reg=DB::table('register_courses')->where([['fos_id',$this->fos],['department_id',$this->d],['semester_id',$this->sm],['session',$this->session]])
            ->whereIn('reg_course_status',['C','E'])
            ->orderBy("level_id",'ASC')
            ->get()->groupBy('level_id');
            
        
            
        }
    

    /**
     * @return string
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function view(): View
    {
        
return view('admin.dvc.regCourse.excel', [
            'f' => $this->f,'s'=>$this->session,'sm'=>$this->sm,'d'=>$this->d,'reg'=>$this->reg
        ]);
       }
   
}
