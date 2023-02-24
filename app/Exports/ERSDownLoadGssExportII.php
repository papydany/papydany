<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ERSDownloadGssExportII implements FromView,ShouldAutoSize,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
        private $user,$s, $title,$code,$season,$course_id,$semester,$result;
        private $userId=array();
        
        public function __construct(array $request,$course_id)
        {
        
        $this->s = $request['session'];
        $this->season=$request['period'];
        $s =$this->s;
        $ss =$this->season;
        $this->course_id =$course_id;
       $c =DB::table('courses')->find($course_id);
       $this->semester =$c->semester;
       $this->title =$c->course_title;
       $this->code =$c->course_code;
       $this->result=DB::connection('mysql2')->table('student_results')->where([['course_id',$this->course_id],['session',$s],['season',$ss]])->get();
       foreach($this->result as $v)
       {
        $this->userId[]=$v->user_id;
       }
       $this->user = DB::connection('mysql2')->table('course_regs')
       ->join('users', 'course_regs.user_id', '=', 'users.id')
       ->where([['course_regs.period', $ss],['course_regs.session', $s], ['course_regs.course_id',$this->course_id]])
      ->whereNotIn('users.id',$this->userId)
       ->orderBy('users.matric_number', 'ASC')
       ->select('users.id', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number')
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
               
            ];
        }
       
        public function view(): View
        {
            
    return view('desk.register_student.ers_excel_II', [
          
                'user' => $this->user,'title'=>$this->title,'code'=>$this->code,'session'=>$this->s,
                'semester'=>$this->semester
            ]);
           }
    }

