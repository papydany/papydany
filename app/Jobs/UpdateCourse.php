<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\StudentResult;
use App\Models\RegisterCourse;
use App\Models\Course;
use App\Models\CourseReg;
use App\Models\StudentResultBackup;
use Illuminate\Support\Facades\DB;

class UpdateCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $cc=DB::table('courses')->select('course_code', (DB::raw('COUNT(course_code)')))
      ->groupBy('course_code')
      ->havingRaw('COUNT(course_code) >1')
      ->get();
      foreach($cc as $v){
      $c=$v->course_code;
      $t =Course::where('course_code',$c)->orderBy('id','ASC')->first();
      $id =$t->id;
      RegisterCourse::where('reg_course_code',$c)->update(['course_id'=>$id]);
      CourseReg::where('course_code',$c)->update(['course_id'=>$id]);
      $cr =CourseReg::where('course_code',$c)->get();
      foreach($cr as $v){
      StudentResult::where('coursereg_id',$v->id)->update(['course_id'=>$id]);
      StudentResultBackup::where('coursereg_id',$v->id)->update(['course_id'=>$id]);
      }
     
      DB::table('courses')->where([['course_code',$c],['id','!=',$id]])->delete();
      }
    }
}
