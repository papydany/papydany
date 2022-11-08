@extends('layouts.admin')
@section('title','View Registered student')
@section('content')
@inject('r','App\Models\R')
        <!-- Page Heading -->
<?php 
 use Illuminate\Support\Facades\Auth;
$result= session('key');
$fosName =$r->get_fos($u->fos_id) ?>
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
            <div class="panel-heading">Enter Result &nbsp;&nbsp;&nbsp; {{$level}}00 LEVEL
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {{$session}} Session 
            </div>
            <div class="panel-body">
                <p><b>Names : </b>{{strtoupper($u->surname." ".$u->firstname." ".$u->othername)}} 
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{$u->matric_number}}</p>
                    <p><b>Field Of Study : </b>{{$fosName}}</p>
                   
                @if($s->count() == 0)
                <h4 class="text-danger text-center">All result have been entered for these candidate for the level and session</h4>

                @else
                
                <form class="form-horizontal" role="form" method="POST" action="{{ url('postResult') }}" data-parsley-validate>
                                        {{ csrf_field() }}
                                <div class="modal-content">
                                 
                                    <div class="modal-body">
                                        <div class="col-md-12" style="margin-bottom: 5px;">
                                        
                                        <div class="col-sm-2 text-center" ><b>Code</b></div>
                                        <div class="col-sm-5 text-center" ><b>Title</b></div>
                                        
                                        <div class="col-sm-2 text-center" ><b>Script No</b></div>
                                  
                                        <div class="col-sm-1 text-center" ><b>Grade</b></div>
                                        
                                        <input type="hidden" name="updateany" value="1"/>
                                         <input type="hidden" name="fos_id" value="{{$u->fos_id}}"/>
                                            <input type="hidden" name="user_id" value="{{$u->id}}"/>
                                            <input type="hidden" name="matric_number" value="{{$u->matric_number}}"/>
                                            <input type="hidden" name="session_id" value="{{$session}}"/>
                                            <input type="hidden" name="level_id" value="{{$level}}"/>
                                            <input type="hidden" name="season" value="{{$season}}"/>
                                              <input type="hidden" name="entry_year" value="{{$u->entry_year}}"/>
                                            
                                        @foreach($s as $k => $item)
                                        
                                       
                                        <div class="col-sm-12 " >
                                            
                                        
                                        @if($k == 1)
                                        <h4><strong>FIRST SEMESTER</strong></h4>
                                        <hr/>
                                        <input type="hidden" name="Firstsemeter" value="{{$k}}">
                                        @elseif($k== 2)
                                        <input type="hidden" name="Secondsemeter" value="{{$k}}">
                                        <hr/>
                                       <h4><strong>SECOND SEMESTER</strong></h4> 
                                       <hr/>
                                        @endif 
                                    </div>
                                        
                                        @foreach($item as  $v)
                                        <input type="hidden" name="semester_id[{{$v->id}}]" value="{{$v->semester_id}}">
                                            <div class="col-sm-12" style="margin-bottom: 9px;">
                                           
                                            <div class="col-sm-2 text-center text-success" ><b>{{$v->course_code}}</b> </div>
                                            <div class="col-sm-5 text-center text-info" ><b>{{$v->course_title}}</b></div>
                                           
                                          
                                           
                                              <div class="col-sm-2 text-center text-danger">
 <input type="text" class="form-control"  name="scriptNo[{{$v->id}}]"  value=" " />
</div>

  <div class="col-sm-1 text-center text-danger">
  <input type="text"  class="form-control"  name="total[{{$v->id.'~'.$v->course_id.'~'.$v->course_unit}}]" pattern="[FDdfpP]{1}"   value="" id="total{{$v->id}}" />

  </div>
  
                                           

                                               

                                                
                                                </div>
                                                <div class="clearfix"></div>

                                                @endforeach
                                            @endforeach



                                    </div>
                                    <div class="modal-footer">
                                            
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 
                                    
                                </div>
                                </div>
                                </form>
                                @endif
                            </div>
                            </div>
                        </div>
                      

    </div>
</div>
@endsection 




               
              