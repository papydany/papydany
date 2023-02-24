@extends('layouts.display')
@section('title','Mopped Up Registration')
@section('content')
@inject('r','App\Models\R')

             
                  <div class="row" style="min-height: 520px;">
        <div class="col-sm-12">
      @if(isset($gsr))
       @if(count($gsr) > 0)
     <?php  $department = $r->get_departmetname($d);
     $faculty = $r->get_facultymetname($f);?>
                           
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:18px; font-weight:700;">UNIVERSITY OF CALABAR</p>
    
    <p class="text-center" style="font-size:14px; font-weight:700;">MOP UP REGISTERED STUDENTS</p>
    <div class="col-sm-9 www">
  
    <p>FACULTY: {{$faculty}}</p>
      <p>DEPARTMENT: {{$department}}</p>
 
 
      </div>


    </td></tr>
 
  
  
</table>

            
@foreach($gsr as $k => $item)  
<h3>Level {{$k}}00</h3>

                 <table class="table table-bordered table-striped">
                 <tr>
                     
                        <th>S/N</th>
                        <th>Mat Number</th>
                        <th>Surname</th>
                        <th>Names</th>
                      <th>Other names</th>
                      <th>Session</th>
                      <th>Courses</th>
                  
                          </tr>
                            <?php $c = 0; ?>
                      @foreach($item as $v)
                 
                      <?php $c = ++$c; 
                      
                      ?>
                      <tr>
                      
                      <td>{{$c}}</td>
                      <td>{{$v->matric_number}}</td>
                       <td>{{strtoupper($v->surname)}}</td>
                       <td>{{strtoupper($v->firstname)}}</td>
                        <td>{{strtoupper($v->othername)}}</td>
                         <td>{{$v->session}}</td>
                         <td>
                            <?php
                            if(isset($cA[$v->id])){
                             foreach($cA[$v->id]['value'] as $vv){
                                echo '&nbsp;'.$vv->course_code.',&nbsp;';
                            }
                          }
                            ?>
                         </td>
                       </tr>
                     
                      @endforeach
                  </table>
                  @endforeach


                       @else
                        <p class="alert alert-warning">No Register students  is available</p>
                        @endif
                        
  @endif
                  </div>
                    </div>
                    </div>
                    </div>
                    </div>
  @endsection 
             