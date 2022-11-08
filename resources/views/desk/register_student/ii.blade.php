@extends('layouts.admin')
@section('title','View Registered student')
@section('content')
@inject('r','App\Models\R')
        <!-- Page Heading -->
<?php 
 use Illuminate\Support\Facades\Auth;
//$result= session('key');
$role =$r->getroleId(Auth::user()->id); 

$acct =$r->getResultActivation($role); ?>
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
            <div class="panel-heading">View Registered Student</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="GET" action="{{ url('post_register_student_ii') }}" data-parsley-validate >
                    {{ csrf_field() }}
                    <div class="form-group">



                        <div class="col-sm-3 col-md-2">
                            <label for="fos" class=" control-label">Field Of Study</label>
                            <select class="form-control" name="fos_id" required>
                                <option value=""> - - Select - -</option>

                                @foreach($f as $v)
                                    <option value="{{$v->id}}">{{$v->fos_name}}</option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-sm-3 col-md-2">
                            <label for="session" class=" control-label">Session</label>
                            <select class="form-control" name="session_id" required>
                                <option value=""> - - Select - -</option>
                                @for ($year = (date('Y')); $year >= 2009; $year--)
                                {{!$yearnext =$year+1}}
                                 @if($acct != null && $acct >= $year)
                                 @if($acct >= $year && Auth::user()->faculty_id == $med || $acct >= $year && Auth::user()->faculty_id == $den)
                                  
                                  <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                  @else
                                  <option value="">Session Deactivated</option>
                                  @endif

                                 @else
                                 <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                 @endif
                                
                                @endfor
                            </select>

                        </div>
                        {{--    @if($result->name =="examsofficer")--}}
        
                            <div class="col-sm-3 col-md-2">
                                <label for="level" class=" control-label">Level</label>
                                <select class="form-control" name="level">
                                    <option value=""> - - Select - -</option>
                            <option value="1">100</option>
                            <option value="2">200</option>
                            <option value="3">300</option>
                            <option value="4">400</option>
                            <option value="5">500</option>
                            <option value="6">600</option>
                            <option value="7">700</option>
                           
                                       
                                </select>
    
                            </div>
                            {{--    @else

                 
                        <div class="col-sm-3 col-md-2">
                            <label for="level" class=" control-label">Level</label>
                            <select class="form-control" name="level">
                                <option value=""> - - Select - -</option>
                                @if(isset($l))
                                    @foreach($l as $v)
                                        <option value="{{$v->level_id}}">{{$v->level_name}}</option>
                                    @endforeach
                                @endif
                            </select>

                        </div>
                        @endif --}}
                        <div class="col-sm-3 col-md-2">
                            <label for="level" class=" control-label">Season</label>
                            <select class="form-control" name="season">
                                <option value=""> - - Select - -</option>
                                <option value="NORMAL">NORMAL</option>
                                 @if(Auth::user()->programme_id == 2)
                                <option value="RESIT">RESIT</option>
                                @else
                                <option value="VACATION">VACATION</option>

                                @endif

                            </select>

                        </div>

                        <div class="col-sm-3 col-md-2">
                            <br/>
                            <button type="submit" class="btn btn-success btn-xs">
                                <i class="fa fa-btn fa-user"></i> View Student
                            </button>
                        </div>
                        <div class="col-sm-3 col-md-2">
                            <br/>
                            <button type="submit" class="btn btn-primary btn-xs" name="excel" value="excel">
                                <i class="fa fa-btn fa-user"></i> Generate Excel Formart
                            </button>
                        </div>
                        </div>
                    </div>



                </form>
            </div>
        </div>
    </div>
    @if(isset($u))
        @if(count($u) > 0)
            {{!$next = $ss+1}}
            <div class="col-sm-12">
                <p>
                    <span><strong>Session : </strong>{{$ss." / ".$next}}</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span><strong>Level : </strong>{{$l_id}}00</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span><strong>Season : </strong>{{$season}}</span>&nbsp;&nbsp;&nbsp;&nbsp;  
                </p>

                <table class="table table-bordered table-striped">
                    <tr>
                        <th class="text-center">S/N</th>
                       <!-- <th>Select</th>-->
                        <th class="text-center">MATRIC NUMBERS</th>
                        <th class="text-center">NAMES</th>
                      <th class="text-center">ACTION</th>

                    </tr>
                    {{!!$c = 0}}
                    @foreach($u as $v)
                        <tr>
                            <td>{{++$c}}</td>
                           
                            <td>{{$v->matric_number}}</td>
                            <td>{{$v->surname." ".$v->firstname." ".$v->othername}}</td>
                            
                         <td>
                                <a href="{{url('registered_student_detail',[$v->id,$l_id,$ss,$season])}}" type="button" class="btn btn-primary btn-xs" target="_blank">Enter Result</a>
                        
                               | <a href="{{url('registered_student_detail_update',[$v->id,$l_id,$ss,$season])}}" type="button" class="btn btn-warning btn-xs" target="_blank">Update Result</a>
                                
                               | <a href="{{url('registered_student_detail_delete',[$v->id,$l_id,$ss,$season])}}" type="button" class="btn btn-danger btn-xs" target="_blank">Delete Result</a>

                               | <a href="{{url('registered_student_detail_update_any',[$v->id,$l_id,$ss,$season])}}" type="button" class="btn btn-success btn-xs" target="_blank">Update Any Result</a>
                        
                        
                            </td>
                        </tr>
                        @endforeach
                </table>

  @else
                    <div class=" col-sm-10 col-sm-offset-1 alert alert-warning" role="alert" >
                        No Student  is avalable!!!
                    </div>

                @endif
                @endif
            </div>
</div>
</div>
</div>


@endsection

