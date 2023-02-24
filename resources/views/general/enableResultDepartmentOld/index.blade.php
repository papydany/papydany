@extends('layouts.admin')
@section('title','Enable Result Upload')
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
                <div class="panel-heading">Enable Result Upload (OLD PORTAL)</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('updateEnableResultDepartmentOld') }}" target="_blank" data-parsley-validate>
                        {{ csrf_field() }}
                        

                        @if($result->name =="admin" || $result->name =="support" || $result->name =="DVC")

<div class="form-group">
  <input type="hidden" name="admin" value="1">
  <input type="hidden" name="approval" value="2">
                        

                       <div class="col-sm-3">
                                 <label for="faculty">Faculty</label>
                                 <select class="form-control" name="faculty" id="faculty_id_old" required>
                                    <option value="">Select</option>
                                    @if(isset($fc))
                                    @foreach($fc as $v)
                                    <option value="{{$v->faculties_id}}">{{$v->faculties_name}}</option>

                                    @endforeach

                                    @endif
                                </select>
                                </div>
                            
                                <div class="col-sm-3">
                              <label for="password-confirm" >Department</label>
                              <select class="form-control" name="id" id="department_id_old" required>
                                
                                 
                                  
                                </select>
                               

                               
                            </div>
                       
                      

                       
                   



@endif


                     



                         
                            

                              
                      
                     
                          
                      
                            <div class="col-sm-2">
                                 <br/>
                               <input type="submit" name="Approve"  class="btn btn-primary" />
                               
                            </div>
                            </div>
                             
                           
                              </form>  
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

<script src="{{URL::to('js/main2.js')}}"></script>

@endsection

                      
 