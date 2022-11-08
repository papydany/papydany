@extends('layouts.admin')
@section('title','Generate Result')
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
                <div class="panel-heading">Generate Result</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="GET" action="{{ url('getreport1') }}" target="_blank" data-parsley-validate>
                        {{ csrf_field() }}
                          <input type="hidden" name="duration" id="duration" value="">
                          <input type="hidden" name="dvc" value="1">
  <input type="hidden" name="page_number" value="1">    
  <input type="hidden" name="selected" value="6"> 
                      

<div class="form-group">
                    

                 
                        

                        </div>
                          <div class="form-group">
                              <div class="col-sm-3">
                              <label for="level" class=" control-label">Matric Number</label>
                              <input type="text" class="form-control" name="matricNumber" value="">
                             
                            </div>
                          
                                 <div class="col-sm-2">
                              <label for="session" class=" control-label">Session</label>
                              <select class="form-control" name="session" required>
                              <option value=""> - - Select - -</option>
                               
                                  @for ($year = (date('Y')); $year >= 2016; $year--)
                                  {{!$yearnext =$year+1}}
                                  <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                  @endfor
                                
                              </select>
                             
                            </div>

       



                     <div class="col-sm-2">
                              <label for="level" class=" control-label">Level</label>
                              <select class="form-control" name="level" id="level_id" required>
                                  <option value=""> - - Select - -</option>
                                  <option value="1">100</option>
                                  <option value="2">200</option>
                                  <option value="3">300</option>
                                  <option value="4"> 400 </option>
                                  <option value="5"> 500</option>
                                  <option value="6">600</option>
                                  <option value="7"> 700</option>
                                 
                              </select>
                             
                            </div>
                            <div class="col-md-2 form-group">
                              <label  class=" control-label">Result Type</label>
                              
                              
        <select class="form-control" name="result_type" required>
                                  <option value=""> - - Select - -</option>
    <option value="1">Sessional Result</option>
     <option value="2">Omited Result </option>
    <!-- <option value="3">Probational Result </option>-->
     <option value="4">Correctional Result </option>
     <option value="5">Long Vacation Result </option>
    <!--<option value="7">Mid-Year Summer Result </option>-->
                                 
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


 
                        
@endsection  
@section('script')
<script src="{{URL::to('js/main.js')}}"></script>

@endsection

                      
 