<?php
namespace App\Exports;
namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class ErsSheets implements FromQuery, WithTitle ,FromView
{
    private $courseTitle;
    private $regId;
    private $courseReg;

    public function __construct(array $request,int $regId, String $courseTitle )
    {
    $this->fos = $request['fos'];
    $this->l = $request['level'];
    $this->s = $request['session'];
    $this->season=$request['season'];
    $this->semester = $request['semester'];
    $this->id = $regId;
    $this->title =$courseTitle;
    }

    /**
     * @return Builder
     */
    public function query()
    {
       
            $this->courseReg = DB::connection('mysql2')->table('users')
           ->join('course_regs', 'users.id', '=', 'course_regs.user_id')
            ->where([['users.fos_id', $this->fos], ['users.department_id', $this->d], ['course_regs.session', $this->s], ['course_regs.semester_id', $this->semester], ['course_regs.level_id', $this->l]])
            ->whereIn('registercourse_id',$this->id)
            ->select('course_regs.*', 'users.surname', 'users.firstname', 'users.othername', 'users.matric_number','users.image_url')
            ->orderBy('users.surname','ASC')
            ->groupBy('registercourse_id')
            ->get();

            return $this->courseReg;
           
         
         
        }
    

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    public function view(): View
    {
        
return view('desk.register_student.ers_excel', [
            'user' => $this->courseReg
        ]);
       }
}