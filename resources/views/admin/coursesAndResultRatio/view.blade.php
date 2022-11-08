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
  
     <?php  $department = $r->get_departmetname($d);
     $faculty = '';//$r->get_facultymetname(Auth::user()->faculty_id);
    $fos =$r->get_fos($fos);
    $next = $s + 1;
   
    ?>
                           
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:18px; font-weight:700;">UNIVERSITY OF CALABAR, CALABAR</p>
    
    <p class="text-center" style="font-size:14px; font-weight:700;">Course And Result Ratio</p>
    <div class='row' style="padding-top:20px">
    <div class="col-sm-4 floatLeft30">
  
    
      <p><strong>DEPARTMENT : </strong> {{$department}}</p>
      <p><strong>PROGRAMME : </strong>  {{$fos}}</p>
 
      </div>

  <div class="col-sm-4 floatRight30">
  <p><strong>Session : </strong>{{$s.' / '.$next}}</p>
       <p><strong>Semester : </strong>First & Second </p>
        

    </div>
    </div>

    </td></tr>
 
  
  
</table>

         
@if(isset($allcourse))
@if(count($allcourse) != 0)
<?php   //$collectionLevel =$allcourse->groupBy('level_id')->toArray(); 
           
           ?> 
            @foreach($allcourse as $k1 => $itemLevel) 
            <p class='text-align:center'><b><u>{{$k1}}00 LEVEL</u></b></p>
                 <table class="table table-bordered table-striped">
                 <tr>
                 <th>S/N</th>
                 <th>Code</th>
                 <th>Titile</th>
                 <th>Status</th>
                 <th>Number of Registered Students</th>
                 <th>Number of Result Uploaded</th>
                 <th>Outstanding Result</th>
                 <th>Number of Approved</th>
                   </tr>



           <?php   $collection =$itemLevel->groupBy('semester_id'); 
              
           ?> 
           @foreach($collection as $k => $items)  
           <?php $c = 0;?>  
                <tr>
                <th colspan="5">@if($k == 1) First Smemester @else Second Semester @endif<th></tr>
                 
                  

              
                           
                   @foreach($items as  $value)                  
                     
                      <?php $c = ++$c;
                      $res =isset($resultD[$value->course_id][$k1]['noOfResult'])  ? $resultD[$value->course_id][$k1]['noOfResult'] : 0;
                      $cos =isset($courseD[$value->course_id][$k1]['noOfCourse']) ? $courseD[$value->course_id][$k1]['noOfCourse'] : 0;
                      $approved =isset($resultD[$value->course_id][$k1]['noOfApprovedResult']) ? $resultD[$value->course_id][$k1]['noOfApprovedResult'] : 0;
                     
                      $balance = $cos  - $res;
                      ?>
                      <tr>
                      
                      <td>{{$c}}</td>
                       <td>{{$value->reg_course_code}}</td>
                       <td>{{$value->reg_course_title}}</td>
                        <td><?php if($value->reg_course_status == 'G') echo 'CARRAY OVER'; ?></td>
                        <td> {{$cos}} </td>
                        <td>{{$res}}  </td>
                        <td>{{$balance}}  </td>
                        <td>{{$approved}}  </td>
                      </tr>
                      @endforeach     
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
             