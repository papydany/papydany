@extends('layouts.admin')
@section('title','Enter Result')
@section('content')
@inject('r','App\Models\R')
<?php 

$role =session('key'); ?>

            <div class="row">
                    <div class="col-lg-12">
                       
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </li>
                        </ol>
                    </div>
                </div>
                  <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">Enter Result</div>
                <div class="panel-body">
                <div class="col-sm-6 col-sm-offset-3">
                  {{!$next = $s + 1}}
                
                    <p> <strong>Semester : </strong>@if($sm == 1)First @else Second @endif</p>
                    <p><strong>Session : </strong>{{$s.' / '.$next}}</p>
                
                  

                    
                       
@if(count($c))
<?php 
                     $collection =$c->groupBy('department_name')->toArray();

                     
                    

                     ?>

                      <form class="form-horizontal" role="form" method="GET" action="{{ url('eo_result_c_gss') }}" data-parsley-validate>
                    
                  <div class="form-group">
                  <label for="level" class=" control-label">Course</label>
                     <select class="form-control" name="id" required>
                     <option value="">-- select --</option>
                  
                      @foreach($collection as  $k => $value)
                     
                       <optgroup label="{{$k}}">
                       @foreach($value as $v)
                      <option value="{{$v->course_id.'-'.$v->department_id}}"> {{$v->reg_course_code}} </option>
                      @endforeach
                         </optgroup>
                    @endforeach

                 
                     </select>
                    
                     <input type="hidden" name="semester" value="{{$sm}}">
                      <input type="hidden" name="session" value="{{$s}}">
                      <input type="hidden" name="programme_id" value="{{$p}}">
                      </div>
  <div class="form-group">
                  <label for="level" class=" control-label">Period</label>
                     <select class="form-control" name="period" required>
                     <option value="">-- select --</option>
                     <option value="NORMAL">NORMAL</option>
                     <option value="RESIT">RESIT (diploma)</option>
                     <option value="VACATION">VACATION</option>
                    </select>
                      </div>
                           
                <div class="form-group">
                  <label for="result_type" class=" control-label">Result Type </label>
                <select class="form-control" name="result_type" required>
                     <option value="">-- select --</option>
                     <option value="Sessional">Sessional</option>
                     @if($role->name == 'examsofficer' || $role->name == 'HOD')
                     <option value="Omitted">Omitted</option>
                     @endif
                     <!--<option value="Correctional">Correctional</option>-->
               </select>
                      </div>
                      

                         <div class="form-group ">
 
                       <!-- <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fa fa-btn fa-user"></i> Continue
                                </button>-->
                                &nbsp;&nbsp;
                               
                                <button type="submit" name="excel" value="excel" class="btn btn-primary btn-sm">
                                    <i class="fa fa-btn fa-user"></i> Use  Excel To Upload
                                </button>
                              
                                &nbsp;&nbsp;
                                <button type="submit" name="excel" value="download" class="btn btn-success btn-sm">
                                    <i class="fa fa-btn fa-user"></i> Download Excel Format
                                </button>
                                </div>
                                </form>

                       @else
                        <p class="alert alert-warning">No Course has been assign to  you in these semester</p>
                        @endif
                              

                   
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
  @endsection                  