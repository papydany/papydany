<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;


class ErsExport implements FromView, WithValidation
{
    private $user;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function rules(): array
    {
        return [
            'session' => [
                'required',
                'string',
            ],
        ];
    }

    public function __construct(array $request)
    {
        $this->fos_id = $request['fos_id'];
        $this->l = $request['level'];
        $this->s = $request['session_id'];
       $this->season=$request['season'];
        
       $s =$this->s;
        $l =$this->l;
        $ss =$this->season;
        $fos_id =$this->fos_id;
        $d =Auth::user()->department_id;
        $f=Auth::user()->faculty_id;
        $this->user = DB::connection('mysql2')->table('student_regs')
        ->distinct('student_regs.matric_number')
        ->join('users', 'student_regs.user_id', '=', 'users.id')
        ->where('users.fos_id', $fos_id)
        ->where([ ['student_regs.department_id', $d], ['student_regs.faculty_id', $f], ['student_regs.season', $ss],
            ['student_regs.session', $s], ['student_regs.level_id', $l]])
        ->orderBy('users.matric_number', 'ASC')
        ->select('users.id', 'users.firstname', 'users.surname', 'users.othername', 'users.matric_number', 'users.fos_id', 'users.entry_year')
->get();  
       
    }
    public function view(): View
    {
        
return view('desk.register_student.ers_excel', [
            'user' => $this->user
        ]);
       }
}
