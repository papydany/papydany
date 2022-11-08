@extends('layouts.display')
@section('title','Display Result')
@section('content')
@inject('r','App\Models\R')
<style>
  p{font-size:12px;
  font-weight: bold;}
  td{font-size:10px;
  }
  .table>tbody>tr>td{padding: 1px;font-weight: bold;}
  .table>tbody>tr>th{padding: 3px;}
</style>
<div class="row" style="min-height: 420px;">
<div class="col-sm-12">
    @if(isset($reg))
    @if(count($reg) > 0)
    {{!$next = $s + 1}}
    
                           
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:20px; font-weight:500;">UNIVERSITY OF CALABAR</p>
<p class="text-center" style="font-size:14px; font-weight:500;">REGISTERED COURSES </p>
<?php $faculty =$r->get_facultymetname($f); 
$department = $r->get_departmetname($d);?>

<div class="col-sm-4">
<p class="text-center"> &nbsp;&nbsp;<span class="text-justify"><b> FACULTY:  {{$faculty}}</b></span></p>
<p class="text-center"> &nbsp;&nbsp;<span class="text-center"><b> DEPARTMENT :  {{$department}}</b></span></p>  
</div>
      <div class="col-sm-4">
      <p><strong>Session : </strong>{{$s.' / '.$next}}</p>
      </div>
      <div class="col-sm-4">
       <p><strong>Semester : </strong>@if($sm == 1)
       First
    @else
    Second
@endif</p>
       </div>
      

    </div>

    </td></tr>
 
  
  
</table>
{{!!$e = 0}}
<div class="row">
@foreach($reg as $k => $value)



<div class="col-sm-3">

<div class="col-sm-12"><p >{{++$e}} &nbsp;&nbsp;<span class="text-center"> Level : <b> {{$k}}00</b></span></p></div>
{{!!$ee = 0}}
        
        <?php 
         $collectionFosId =$value->groupBy('fos_id');
     
        ?>  
                 
        {{!!$eee = 0}}
        @foreach($collectionFosId as $keyfosid =>  $itemfosid)  
        <?php
        
       
        $fos =$r->get_fos($keyfosid);?>
        <p >{{++$eee}} &nbsp;&nbsp;<span class="text-center"> Unit : <b> {{$fos}}</b></span></p>
        <table class="table table-bordered table-striped">
                 <tr>
                     
                        <th>S/N</th>
                        <th>Code</th>
                        <th>Status</th>
                     
                      
                          </tr>
                            {{!!$c = 0}}
                      @foreach($itemfosid as $v)
                   
                      {{!$c = ++$c}}
                      <tr>
                      
                      <td>{{$c}}</td>
                      <td>{{$v->reg_course_code}}</td>
                     <td>{{$v->reg_course_status}}</td>
                      </tr>
                     
                      @endforeach
                  </table>
                
                  @endforeach
                  </div>
                  
                
                  @endforeach
</div>
                


                       @else
                        <p class="alert alert-warning">No Result is available  is avalable</p>
                        @endif
                        
  @endif
                  </div>
                    </div>
                    </div>
                    </div>
                    </div>
  @endsection 
             
             