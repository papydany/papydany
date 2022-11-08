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
    
   
    $next = $s + 1;
   
    ?>
                           
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:18px; font-weight:700;">UNIVERSITY OF CALABAR, CALABAR</p>
    
    <p class="text-center" style="font-size:14px; font-weight:700;">EXAMINATION ATTENDANCE SHEET</p>
    <div class='row' style="padding-top:20px">
    <div class="col-sm-4 floatLeft30">
  
    
      <p><strong>DEPARTMENT : </strong> {{$department}}</p>
      
 
      </div>
      <div class="col-sm-4 floatLeft30">
  
    
  <p><strong>Course Title: </strong> Unregister Students That have Paid Fees</p>
 

  </div>
  <div class="col-sm-4 floatRight30">
      <p> <strong>Level : </strong>100  And 200</p>
      <p><strong>Session : </strong>{{$s.' / '.$next}}</p>
     
        

    </div>
    </div>

    </td></tr>
 
  
  
</table>

<?php $c = 0; ?>           



                 <table class="table table-bordered table-striped">
                  

                 <tr>
                 
                  
                        <th>S/N</th>
                        <th>Matric Number</th>
                        <th>Surname</th>
                        <th>FirstName</th>
                        <th>Other Names</th>
                      <th>Script Number</th>
                        
                        <th>Signature</th>
                          </tr>
                           
                      
                         @foreach($item as  $v) 
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
             