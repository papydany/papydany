<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class UsersExport implements FromView, WithValidation
{
    private $reg;
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
        $this->s = $request['session'];
        $this->f = $request['faculty_id'];
        $this->d=$request['department'];
        $s =$this->s;
        $f =$this->f;
        $d =$this->d;
        $user = DB::connection('mysql2')->table('users')
        ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
        ->select('fos_id','student_regs.id')
        ->where([['student_regs.session', $s], ['users.faculty_id', $f],
        ['users.department_id', $d]])
        ->distinct('fos_id')->get();

        if(count($user) > 0)
        {
            foreach ($user as $v) {
                $fos_id[] = $v->fos_id;
                $studentRegId [] =$v->id;
               }
            
               $results = DB::connection('mysql2')->table('course_regs')
               ->join('student_results','course_regs.id','=','student_results.coursereg_id')
               ->where([['student_results.session', $s]])
               ->whereIn('studentreg_id',$studentRegId)
               ->select('course_regs.registercourse_id')
             ->distinct()->get();
               foreach ($results as $v) {
                   $course_id_with_result[] = $v->registercourse_id;
               }
           
               if(config('app.env') === 'production'){
               $this->reg = DB::table('register_courses')
                   ->join('faculties', 'register_courses.faculty_id', '=', 'faculties.id')
                   ->where([['session', $s], ['reg_course_status', '!=', 'E']])
                   ->whereNotIn('register_courses.id', $course_id_with_result)
                   ->whereIn('fos_id', $fos_id)
                  ->whereIn('register_courses.id', function($query) use ($s) {
                   $query->select('registercourse_id')->from('unical8_exams2.course_regs')
                   ->where('session',$s);
               })
               ->select('register_courses.id','course_id', 'fos_id','level_id', 'department_id', 'semester_id', 'faculty_id', 'reg_course_title', 'reg_course_code','reg_course_status', 'faculty_name')
               
                 ->orderBy('register_courses.level_id', 'ASC', 'semester_id', 'ASC')
                   ->distinct()->get()->groupBy('level_id');
           }else{
               $this->reg = DB::table('register_courses')
               ->join('faculties', 'register_courses.faculty_id', '=', 'faculties.id')
               ->where([['session', $s], ['reg_course_status', '!=', 'E']])
               ->whereNotIn('register_courses.id', $course_id_with_result)
               ->whereIn('fos_id', $fos_id)
             ->whereIn('register_courses.id', function($query) use ($s) {
               $query->select('registercourse_id')->from('unical_exams2.course_regs')
               ->where('session',$s);
           })
           
           ->select('register_courses.id','course_id', 'fos_id','register_courses.level_id','department_id', 'semester_id', 'faculty_id', 'reg_course_title', 'reg_course_code', 'reg_course_status','faculty_name')
            
             ->orderBy('register_courses.level_id', 'ASC', 'semester_id', 'ASC')
               ->distinct()->get()->groupBy('level_id');
           }

        }else{
            return 0;
        }
       
       
    }
    public function view(): View
    {
        
return view('admin.course_with_no_result.report1', [
            'reg' => $this->reg,'s'=>$this->s,'f'=>$this->f,'d'=>$this->d
        ]);
       }
}
