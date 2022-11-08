@extends('layouts.display')
@section('title','Result ')
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
  
     <?php  $department ='';// $r->get_departmetname($d);
     $faculty = '';//$r->get_facultymetname(Auth::user()->faculty_id);
    ?>
                           
<table  class="table table-bordered">
<tr><td>
<p class="text-center" style="font-size:18px; font-weight:700;">UNIVERSITY OF CALABAR, CALABAR</p>
    
    <p class="text-center" style="font-size:14px; font-weight:700;">Result Report</p>
    <p><strong>DEPARTMENT : </strong> {{$department}}</p>
    </td></tr>
 
  
  
</table>

         
@if(isset($u))
@if(count($u) != 0)
<?php   //$collectionLevel =$allcourse->groupBy('level_id')->toArray(); 
           
           ?> 
           
                 <table class="table table-bordered table-striped">
                 <tr>
                 <th>S/N</th>
                 <th>Matric Number</th>
                 <th>Code</th>
                 <th>session</th>
                 <th>Level</th>
                 <th>semester</th>
                 <th>ca</th>
                 <th>exam</th>
                 <th>total</th>
                 <th>Grade</th>
                 <th>posted Date</th>
                 <th>updated Date</th>
                   </tr>
                   <?php $c = 0;?>
           @foreach($u as $k => $v)  
             
                <tr>
                      <tr>
                      
                      <td>{{++$c}}</td>
                       <td>{{$v->matric_number}}</td>
                       <td>{{$v->course_code}}</td>
                        <td>{{$v->session}}</td>
                        <td> {{$v->level_id}}</td>
                        <td>@if($v->semester == 1)
                            First
                            @else
                            Second
                            @endif
                            
                        </td>
                        <td>{{$v->ca}}</td>
                        <td>{{$v->exam}}</td>
                        <td>{{$v->total}}</td>
                        <td>{{$v->grade}}</td>
                        <td>{{$v->post_date}}</td>
                        <td>{{isset($v->updated_at) ? $v->updated_at : ''}}</td>
                       
                      </tr>
                      @endforeach     
                    
                  </table>
                  {{ $u->links() }}
                
                 


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
             