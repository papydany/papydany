@extends('layouts.display')
@section('title','Display Result')
@section('content')
@inject('r','App\Models\R')

             
                  <div class="row" style="min-height: 520px;">
        <div class="col-sm-12">
      @if(isset($u))
       @if(count($u) > 0)
     <?php  $department = $r->get_departmetname($d);
     $faculty = $r->get_facultymetname($f);
           

 
                  ?>
                           
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:18px; font-weight:700;">UNIVERSITY OF CALABAR</p>
    
    <p class="text-center" style="font-size:14px; font-weight:700;">REGISTERED STUDENTS</p>
    <div class="col-sm-9 www">
  
    <p>FACULTY: {{$faculty}}</p>
      <p>DEPARTMENT: {{$department}}</p>
 
 
      </div>
  <div class="col-sm-3 ww">
      <p> <strong>Level : </strong>{{$l}}00 </p>

     
        

    </div>

    </td></tr>
 
  
  
</table>

            
@foreach($u as $k => $item)  
<?php $fos =$r->get_fos($k) ?>
 <p>Course Of Study  <strong> : {{$fos}}</strong></p>
                 <table class="table table-bordered table-striped">
                 <tr>
                     
                        <th>S/N</th>
                        <th>Surname</th>
                        <th>Names</th>
                      <th>Other names</th>
                      <th>Sex</th> 
                        <th>State of Origin</th>
                          </tr>
                            <?php $c = 0; ?>
                      @foreach($item as $v)
                 
                      <?php $c = ++$c;
                      $state  = $r->state($v->state_id); ?>
                      <tr>
                      
                      <td>{{$c}}</td>
                       <td>{{strtoupper($v->surname)}}</td>
                       <td>{{strtoupper($v->firstname)}}</td>
                        <td>{{strtoupper($v->othername)}}</td>
                         <td>{{$v->gender}}</td>
                       
                   
                     <td>{{$state->state_name}}</td>
                       
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
             