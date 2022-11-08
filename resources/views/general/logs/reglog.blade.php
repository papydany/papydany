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
    
    <p class="text-center" style="font-size:14px; font-weight:700;">Registration</p>
  
    </td></tr>
 
  
  
</table>

         
@if(isset($u))
@if(count($u) != 0)
<?php   //$collectionLevel =$allcourse->groupBy('level_id')->toArray(); 
           
           ?> 
           
                 <table class="table table-bordered table-striped">
                 <tr>
                 <th>S/N</th>
                 <th>Name</th>
                 <th>Department</th>
                 <th>number</th>
                   </tr>
                   <?php $c = 0;?>
           @foreach($u as $k => $v)  
             <?php 
             $d='';
             $user =$r->getUser($k);
            
             $d =$r->get_departmetname($user->department_id);
            
             ?>
                <tr>
                      <tr>
                      
                      <td>{{++$c}}</td>
                       <td>{{$user ? $user->name :''}}</td>
                       <td>{{$d ? $d : ''}}</td>
                        <td>{{count($v)}}</td>
                        
                       
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
             