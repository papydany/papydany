@extends('layouts.display')
@section('title','report')
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
    @if(isset($cr))
    @if(count($cr) > 0)
     <?php  //
   
  //  $fos =$r->get_fos($fos);
    $next = $s + 1;
    $ll=$l+1;
    $semester =DB::table('semesters')->where('semester_id',$sm)->first();
    ?>
                           
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:18px; font-weight:700;">UNIVERSITY OF CALABAR, CALABAR</p>
    
    <p class="text-center" style="font-size:14px; font-weight:700;">Courses and Registered Students</p>
    <div class='row' style="padding-top:20px">
 

  <div class="col-sm-4 floatRight30">
      <p> <strong>Level : </strong>{{$l}}00 & {{$ll}}00</p>
      <p><strong>Session : </strong>{{$s.' / '.$next}}</p>
       <p><strong>Semester : </strong>{{$semester->semester_name}} </p>
        

    </div>
    </div>

    </td></tr>
 
  
  
</table>

<?php $c = 0; ?>           



                 <table class="table table-bordered table-striped">
                  
                 <tr>
                 
                  
                        <th>S/N</th>
                        <th>Course Code</th>
                        <th>Course Unit</th>
                        <th>Department</th>
                     
                        <th>Size</th>
                      <th>Department Offering The Courses</th>
                          </tr>
                           
                      
                      @foreach($cr as $v)
                      
                      
                      <?php $c = ++$c; 
                      //dd($v->course_id);
                      //$data[$v];
                     // $department = $r->get_departmetname($data[$v->course_id]['dept']);
                      ?>
                      <tr>
                      
                      <td>{{$c}}</td>
                       <td>{{$data[$v->id]['code']}}</td>
                       <td>{{$data[$v->id]['unit']}}</td>
                        <td>{{$data[$v->id]['dept']}}</td>
                       
                        <td>{{$data1[$v->id]['number']}}</td>
                        <td><?php 
                        foreach($data2[$v->id] as $vc){
                            foreach($vc as $vc1)
                            {
                                echo $vc1->department_name .' <br/> ';
                            }
                      
                        } ?></td>
                       
                   
                       
                      </tr>
                      @endforeach
                
                      
                  </table>
                 


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
             