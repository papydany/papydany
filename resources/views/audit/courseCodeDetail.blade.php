@extends('layouts.display')
@section('title','Class Attendance')
@section('content')
@inject('r','App\Models\R')
<?php use Illuminate\Support\Facades\DB; ?>
<style>
    .table-bordered {
    border: 2px solid #000;

}
tr {
    border: 1.5px solid #000;
}
</style>
 <div class="row" style="min-height: 520px; margin:20px 5px;">
<div class="col-sm-12">
    @if(isset($cd))
    @if(count($cd) > 0)
     <?php  $department = $r->get_departmetname($d);
     $faculty = $r->get_facultymetname($f);
    $fos =$r->get_fos($fos);
    $next = $s + 1;
    $semester =DB::table('semesters')->where('semester_id',$reg->semester_id)->first();
    ?>
                           
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:18px; font-weight:700;">UNIVERSITY OF CALABAR, CALABAR</p>
    
    <p class="text-center" style="font-size:14px; font-weight:700;">EXAMINATION ATTENDANCE SHEET</p>
    <div class='row' style="padding-top:20px">
    <div class="col-sm-4 floatLeft30">
  
    
      <p><strong>DEPARTMENT : </strong> {{$department}}</p>
      <p><strong>PROGRAMME : </strong>  {{$fos}}</p>
 
      </div>
      <div class="col-sm-4 floatLeft30">
  
    
  <p><strong>Course Title: </strong> {{$reg->reg_course_title}}</p>
  <p><strong>Course Code: </strong>  {{$reg->reg_course_code}}</p>
  <p><strong>Course Unit: </strong>  {{$reg->reg_course_unit}}</p>

  </div>
  <div class="col-sm-4 floatRight30">
      <p> <strong>Level : </strong> {{$reg->level_id}}00 </p>
      <p><strong>Session : </strong>{{$s.' / '.$next}}</p>
       <p><strong>Semester : </strong>{{$semester->semester_name}} </p>
        

    </div>
    </div>

    </td></tr>
 
  
  
</table>

<?php $c = 0; ?>           

@foreach($cd as $k => $value) 

                 <table class="table table-bordered table-striped">
                   <tr>
                     <th colspan="12">{{$k}}00 LEVEL &nbsp;&nbsp;&nbsp;  @if($k > $reg->level_id) ( CARRY OVER STUDENTS ) @endif</th>
                   </tr>

                 <tr>
                 
                  
                        <th>S/N</th>
                        <th>Matric Number</th>
                        <th>Surname</th>
                        <th>FirstName</th>
                        <th>Other Names</th>
                         <th>Ca</th>
                         <th>Exams</th>
                         <th>Total</th>
                         <th>Grade</th>
                         <th>Exam Officer</th>
                         <th>Date Posted</th>
                          </tr>
                           
                      
                      @foreach($value as $v)
                      <?php $c = ++$c;
                      $examOfficer =$r->getUser($v->examofficer) ?>
                      <tr>
                      
                      <td>{{$c}}</td>
                       <td><strong>{{$v->matric_number}}</strong></td>

                        <td><strong>{{strtoupper($v->surname)}} </strong></td>
                        <td> {{strtoupper($v->firstname)}} </td>
                        <td> {{strtoupper( $v->othername)}}</td>
                        <td>{{$v->ca}}</td>
                        <td>{{$v->exam}}</td>
                        <td>{{$v->total}}</td>
                        <td>{{$v->grade}}</td>
                   
                     <td>{{$examOfficer->name}}</td>
                     <td>{{ date('F d, Y',strtotime($v->post_date));}}
                         <b>{{ date('- h : i A', strtotime($v->post_date));}}</b></td>
                       
                      </tr>
                      @endforeach
                      
                  </table>
                  @endforeach


                       @else
                        <p class="alert alert-warning">No Register students  is avalable</p>
                        @endif
                        
  @endif
                  </div>
                    </div>
                    </div>
                    </div>
                    </div>
  @endsection 
             