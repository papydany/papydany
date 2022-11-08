@extends('layouts.admin')
@section('title','SetUp Graduation List')
@section('content')
@inject('r','App\Models\R')
<?php 
use Illuminate\Support\Facades\Auth;
$result= session('key'); ?>
 <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                       
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </li>
                        </ol>
                    </div>
                </div>

    <div class="row" style="min-height: 520px;">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">View Graduation List</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('viewGraduate') }}" target="_blank" data-parsley-validate>
                        {{ csrf_field() }}
                          <input type="hidden" name="duration" id="duration" value="">

                        @if($result->name =="admin" || $result->name =="support" || $result->name =="DVC")

<div class="form-group">
  <input type="hidden" name="admin" value="1">
  <input type="hidden" name="approval" value="2">
                        

                       <div class="col-sm-3">
                                 <label for="faculty">Faculty</label>
                                <select class="form-control" name="faculty_id" id="faculty_idg">
                                    <option value="">Select</option>
                                    @if(isset($fc))
                                    @foreach($fc as $v)
                                    <option value="{{$v->id}}">{{$v->faculty_name}}</option>

                                    @endforeach

                                    @endif
                                </select>
                                </div>
                            
                                <div class="col-sm-3">
                              <label for="password-confirm" >Department</label>
                              <select class="form-control" name="department" id="department_idg" required>
                             </select>

                               
                            </div>
                            <div class="col-sm-3">
                                 <label for="faculty">Start Date</label>
                                 <input type="date" name="start" class="form-control"/>
                             
                                </div>
                                 <div class="col-sm-3">
                              <label for="semester" class=" control-label">End Date</label>
                              <input type="date" name="end" class="form-control"/>
                            
                             
                            </div>

                        </div>
                   



@endif


                        <div class="form-group">




                         
                            

                              
                      
                     
                          
                           <!--   <div class="col-sm-2">
                                 <br/>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-btn fa-user"></i> View 
                                </button>
                            </div>-->
                            <div class="col-sm-2">
                                 <br/>
                               <input type="submit" name="excel" class="btn btn-primary" value="Excel Report"/>
                               
                            </div>
                            </div>
                             
                           
                              </form>  </div>
                        </div>
                        </div>
                        </div>


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

                      
 