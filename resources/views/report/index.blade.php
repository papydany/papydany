@extends('layouts.admin')
@section('title','Register Course To Student')
@section('content')

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
                <div class="panel-heading">Register Course To Student</div>
                <div class="panel-body">
             <form class="form-horizontal" role="form" method="POST" action="{{ url('registeredCourseToStudent') }}" target="_blank" data-parsley-validate>
                                  {{ csrf_field() }}
                   <div class="form-group{{ $errors->has('start_serial_number') ? ' has-error' : '' }}">
                    
                                <div class="col-sm-2">
                                <label for="session" class="control-label">Select session</label>
                                 <select class="form-control" name="session" required>
                               <option value="">Select</option>
                               @for($i=2020;$i <= Date('Y'); $i++)
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
                           
                            <div class="col-sm-2">
                              <label for="level" class=" control-label">Level</label>
                              <select class="form-control" name="level" required>
                              <option value=""> - - Select - -</option>
                               
                                 
                                  <option value="1">100</option>
                                  <option value="2">200</option>
                                  <option value="3">300</option>
                                  <option value="4">400</option>
                                  <option value="5">500</option>
                                  <option value="6">600</option>
                                  <option value="7">700</option>
                                  <option value="8">800</option>
                                
                              </select>
                             
                            </div>
                            <div class="col-sm-2">
                                <label for="session" class="control-label">Select Programme</label>
                                 <select class="form-control" name="programme" required>
                                 <option value="">Select</option>
                               <option value="3">Udergraduate</option>
                               <option value="2">Diploma</option>
                                 </select>
                      
                            </div>

                            <div class="col-sm-2">
                                <label for="session" class="control-label">Semester</label>
                                <select class="form-control" name="semester" required>
                                <option value="">Select</option>
                                <option value="1">First Semester</option>
                                <option value="2">Second Semester</option>
                                 
                                  
                                </select>
                               
                              </div>
                      
                              <div class="col-md-2">
                            <br/>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i> generate report
                                </button>
                            </div>

                        </div>

                        </form>
                    </div>
                        </div>
                        </div>
                        </div>
               
                

@endsection   

