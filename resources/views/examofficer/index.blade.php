@extends('layouts.admin')
@section('title','Lecturer')
@section('content')
@inject('r','App\Models\R')

<?php 
use Illuminate\Support\Facades\Auth;
$role =$r->getroleId(Auth::user()->id); 
$acct =$r->getResultActivation($role); 
$eru =$r->getEnableResultUpload(Auth::user()->department_id);?>
                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Dashboard <small>Statistics Overview</small>
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- /.row -->
  
                 <div class="row" style="min-height: 420px;">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">Enter Result</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/eo_assign_courses') }}" data-parsley-validate>
                        {{ csrf_field() }}
                        <div class="form-group">
                   
                     <div class="col-sm-2">
                              <label for="programme" class=" control-label">Programme</label>
                              <select class="form-control" name="programme" id="programme_id" required>
                                  <option value=""> - - Select - -</option>
                                  @if(isset($p))
                                  @foreach($p as $v)
                                  <option value="{{$v->id}}">{{$v->programme_name}}</option>
                                  @endforeach
                                  @endif
                              </select>
                             
                            </div>

                         
                         

                               <div class="col-sm-2">
                              <label for="session" class=" control-label">Session</label>
                              <select class="form-control" name="session"  required>
                              <option value=""> - - Select - -</option>
                               
                                  @for ($year = (date('Y')); $year >= 2009; $year--)
                                  {{!$yearNext =$year+1}}
                                   @if($acct != null &&  $acct >= $year)
                                   @if($acct >= $year && Auth::user()->faculty_id == $med || $acct >= $year && Auth::user()->faculty_id == $den)
                                  
                                   <option value="{{$year}}">{{$year.'/'.$yearNext}}</option>
                                   @elseif(count($eru) != 0)

                                   
@foreach($eru as $eruValue)
@if($year == $eruValue->session)

<option value="{{$year}}">{{$year.'/'.$yearNext}}</option>
@endif
@endforeach
                                   @else
                                   <option value="">Session Deactivated</option>
                                   @endif

                                   @else
                                   <option value="{{$year}}">{{$year.'/'.$yearNext}}</option>
                                   @endif
                                  
                                  @endfor
                                
                              </select>
                             
                            </div>
                              <div class="col-sm-3">
                              <label for="semester" class=" control-label">Level</label>
                              @if(Auth::user()->faculty_id == $med || Auth::user()->faculty_id == $den)
                      
                              <select class="form-control" name="level">
                               <option value=""> - - Select - -</option>
                               <option value="1">100</option>
                            <option value="2">200</option>
                            <option value="3">Part I</option>
                            <option value="4">Part II</option>
                            <option value="5">Part III</option>
                            <option value="6">Part IV</option>
                          </select>
                             
                          @else
                              <select class="form-control" name="level" id="level_id"  required>
                                  <option value=""> - - Select - -</option>
                                 </select>
                                 @endif
                             
                            </div>
                      
                          <div class="col-sm-3">
                              <label for="semester" class=" control-label">Semester</label>
                              <select class="form-control" name="semester" id="semester_id"  required>
                                  <option value=""> - - Select - -</option>
                                
                              </select>
                             
                            </div>
                          
                            
                              <div class="col-sm-2">
                                 <br/>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-btn fa-user"></i> Continue
                                </button>
                            </div>
                            </div>
                           
                              </form>  </div>
                        </div>
                        </div>
                        </div>
                <!-- /.row -->
<div class="modal fade" id="myModal" role="dialog" style="margin-top: 100px;">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
       
        <div class="modal-body text-danger text-center">
          <p>... processing ...</p>
        </div>
       
      </div>
      
    </div>
  </div> 

@endsection
@section('script')
<script src="{{URL::to('js/main.js')}}"></script>

@endsection

             