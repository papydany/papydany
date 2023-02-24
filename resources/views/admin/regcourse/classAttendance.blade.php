@extends('layouts.display')
@section('title','Class Attendance')
@section('content')
@inject('r','App\Models\R')
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
    @if(isset($item))
    @if(count($item) > 0)
     <?php  $department = $r->get_departmetname($d);
     $faculty = '';//$r->get_facultymetname(Auth::user()->faculty_id);
    $fos =$r->get_fos($fos);
    $next = $s + 1;
    $semester =DB::table('semesters')->where('semester_id',$sm)->first();
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
      <p><strong>Session : </strong>{{$s.' / '.$next}}</p>
       <p><strong>Semester : </strong>{{$semester->semester_name}} </p>
        

    </div>
    </div>

    </td></tr>
 
  
  
</table>

<?php $c = 0; $b = 0;?>           

@foreach($item as $k => $value) 
<?php $b = ++$b; ?>
                 <table class="table table-bordered table-striped">
                   <tr>
                     <th colspan="7">{{$k}}00 LEVEL &nbsp;&nbsp;&nbsp;  @if($b > 1) ( CARRY OVER STUDENTS ) @endif</th>
                   </tr>

                 <tr>
                 
                  
                        <th>S/N</th>
                        <th>Matric Number</th>
                        <th>Surname</th>
                        <th>FirstName</th>
                        <th>Other Names</th>
                      <th>Script Number</th>
                        
                        <th>Signature</th>
                          </tr>
                           
                      
                      @foreach($value as $v)
                      <?php $c = ++$c; ?>
                      <tr>
                      
                      <td>{{$c}}</td>
                       <td><strong>{{$v->matric_number}}</strong></td>

                        <td><strong>{{strtoupper($v->surname)}} </strong></td>
                        <td> {{strtoupper($v->firstname)}} </td>
                        <td> {{strtoupper( $v->othername)}}</td>
                        <td></td>
                   
                     <td></td>
                       
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
             