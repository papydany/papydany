@extends('layouts.admin')
@section('title','View Registered Probation student')
@section('content')
@inject('r','App\Models\R')
        <!-- Page Heading -->
<?php $result= session('key');
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
            <div class="panel-heading">View Registered Probation Student</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="GET" action="{{ url('enter_probation_result1') }}" data-parsley-validate >
                    {{ csrf_field() }}
                    <div class="form-group">

                            <div class="col-sm-3 col-md-2">
                                    <label for="session" class=" control-label">Session</label>
                                    <select class="form-control" name="session_id" required>
                                        <option value=""> - - Select - -</option>
        
                                    
                                        @for ($year = (date('Y')); $year >= 2016; $year--)
                                  {{!$yearnext =$year+1}}
                                   @if($acct != null)
                                   @if($acct >= $year )
                                   <option value="">Session Deactivated</option>
                                   
                                   @else
                                   <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                   @endif

                                   @else
                                   <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                   @endif
                                  
                                  @endfor
                                    </select>
        
                                </div>
    @if($result->name =="examsofficer")

                            <div class="col-sm-3">
                                    <label for="fos" class=" control-label">Programme</label>
                                    <select class="form-control" name="p" id="p_id" required>
                                     <option value=""> - - Select - -</option>
                                       
                                        @foreach($p as $v)
                                        <option value="{{$v->id}}">{{$v->programme_name}}</option>
                                        @endforeach
                                        
                                    </select>
                                   
                                  </div>
       <div class="col-sm-3">
                                    <label for="fos" class=" control-label">Field Of Study</label>
                                    <select class="form-control" name="fos" id="fos_id" required>
                                     <option value=""> - - Select - -</option>
                                       
                                       
                                        <option value=""></option>
                                     
                                        
                                    </select>
                                   
                                  </div>
      
                           <div class="col-sm-3">
                                    <label for="level" class=" control-label">Level</label>
                                    <select class="form-control" name="level" id="level_id" required>
                                        <option value=""> - - Select - -</option>
                                       
                                    </select>
                                   
                                  </div>
                                  @else



                        <div class="col-sm-3">
                            <label for="fos" class=" control-label">Field Of Study</label>
                            <select class="form-control" name="fos" required>
                                <option value=""> - - Select - -</option>

                                @foreach($f as $v)
                                    <option value="{{$v->id}}">{{$v->fos_name}}</option>
                                @endforeach

                            </select>

                        </div>

                      

                      
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
                        @endif
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
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-btn fa-user"></i> View Student
                            </button>
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
                    <span><strong>Entry Year : </strong>{{$ss." / ".$next}}</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span><strong>Level : </strong>{{$l_id}}00</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span><strong>Semester : </strong>
                    @if(Auth::user()->programme_id == 4)

                    @else
                    First & Second
                       
                  </span>
                     @endif
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
                            <!--<td><input type="checkbox" value="{{$v->id}}" name="id[]"> </td>-->
                            <td>{{$v->matric_number}}</td>
                            <td>{{$v->surname." ".$v->firstname." ".$v->othername}}</td>
                            
                         <td>
                         <a href="{{url('registered_student_detail',[$v->user_id,$l_id,$ss,$season])}}" type="button" class="btn btn-primary btn-xs" target="_blank">Enter Result</a>
                        
                        | <a href="{{url('registered_student_detail_update',[$v->user_id,$l_id,$ss,$season])}}" type="button" class="btn btn-warning btn-xs" target="_blank">Update Result</a>
                         
                        | <a href="{{url('registered_student_detail_delete',[$v->user_id,$l_id,$ss,$season])}}" type="button" class="btn btn-danger btn-xs" target="_blank">Delete Result</a>

                        | <a href="{{url('registered_student_detail_update_any',[$v->user_id,$l_id,$ss,$season])}}" type="button" class="btn btn-success btn-xs" target="_blank">Update Any Result</a>
                 
                            
                            </td>
                        </tr>
<!-- ======== =============== for student course reg ========================================-->
                        
                     
</form>
                    @endforeach
                </table>
                 <!--<button type="submit" class="btn btn-danger">Submit</button>-->
                </form>

                @else
                    <div class=" col-sm-10 col-sm-offset-1 alert alert-warning" role="alert" >
                        No Student is avalable!!!
                    </div>

                @endif
                @endif
            </div>
</div>
</div>
</div>


@endsection

@section('script')




<script src="{{URL::to('js/main.js')}}"></script>



@endsection              