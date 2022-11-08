@extends('layouts.admin')
@section('title','Course With No Result')
@section('content')
<?php $result= session('key') ?>
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
                <!-- /.row -->
                <div class="row" style="min-height: 520px;">
                <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">Withdrawal Students (Oldportal) </div>
                <div class="panel-body">
             <form class="form-horizontal" role="form" method="POST" action="{{ url('withdrawalOldportal') }}" target="_blank" data-parsley-validate>
                                  {{ csrf_field() }}
                   <div class="form-group{{ $errors->has('start_serial_number') ? ' has-error' : '' }}">
                    
                                <div class="col-md-2">
                                <label for="session" class="control-label">Select session</label>
                                 <select class="form-control" name="session" required>
                               <option value="">Select</option>
                               @for($i=2014;$i <= 2018; $i++)
                               {{$next =$i+1}}

                        <option value="{{$i}}">{{$i.' / '.$next}}</option>
                                @endfor
                             </select>

                                @if ($errors->has('session'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('session') }}</strong>
                                    </span>
                                @endif
                            </div>
                           
                          


                            <div class="col-md-3">
                                <label for="session" class="control-label">Select Faculty</label>
                                 <select class="form-control" name="faculty" id="faculty_id_old" required>
                               <option value="">Select</option>
                               @foreach($f as $v)
                           

                        <option value="{{$v->faculties_id}}">{{$v->faculties_name}}</option>
                                @endforeach
                             </select>

                                @if ($errors->has('faculty_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('faculty_id') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-sm-3">
                                <label for="session" class="control-label">Department</label>
                                <select class="form-control" name="department" id="department_id_old" required>
                                
                                 
                                  
                                </select>
                               
                              </div>

                            
                      
                              <div class="col-md-2">
                            <br/>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i> generate report
                                </button>
                              </div>
                              <div class="col-md-2">
                                  <br/>
                                <button type="submit" class="btn btn-success" value="excel" name="excel">
                                    <i class="fa fa-btn fa-user"></i> Export Excel
                                </button>
                            </div>
                          
                            

                        </div>

                        </form>
                    </div>
                        </div>
                        </div>
                        </div>
               
                

@endsection   
@section('script')
<script src="{{URL::to('js/main2.js')}}"></script>

@endsection
