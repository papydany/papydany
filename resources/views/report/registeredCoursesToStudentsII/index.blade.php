@extends('layouts.admin')
@section('title','Register Courses To Students II')
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
                <div class="panel-heading">Register Courses To Students II</div>
                <div class="panel-body">
             <form class="form-horizontal" role="form" method="POST" action="{{ url('registeredCoursesToStudentsII') }}" target="_blank" data-parsley-validate>
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
                                 <label for="faculty">Faculty</label>
                                <select class="form-control" name="faculty_id" id="faculty_id">
                                    <option value="">Select</option>
                                    @if(isset($fc))
                                    @foreach($fc as $v)
                                    <option value="{{$v->id}}">{{$v->faculty_name}}</option>

                                    @endforeach

                                    @endif
                                </select>
                                </div>

                                <div class="col-sm-2">
                              <label for="password-confirm" >Department</label>
                              <select class="form-control" name="department" id="department_id" required>
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

                        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">Search Registered Course with Students</div>
                <div class="panel-body">
             <form class="form-horizontal" role="form" method="POST" action="{{ url('registeredCoursesToStudentsIII') }}" target="_blank" data-parsley-validate>
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
                                <label for="session" class="control-label">Semester</label>
                                <select class="form-control" name="semester" required>
                                <option value="">Select</option>
                                <option value="1">First Semester</option>
                                <option value="2">Second Semester</option>
                                 
                                  
                                </select>
                               
                              </div>
                      
                            <div class="col-sm-2">
                                 <label for="faculty">Course Code</label>
                                 <input type="text" class="form-control" name="code" required>
                               
                                </div>

                       
                        

                         
                      
                              <div class="col-md-2">
                            <br/>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i> Search
                                </button>
                              </div>
                              <div class="col-md-2">
                            <br/>
                                <button type="submit" class="btn btn-primary" name='p' value="1">
                                    <i class="fa fa-btn fa-user"></i> Excel Download
                                </button>
                            </div>

                        </div>

                        </form>
                    </div>
                        </div>
                        <p class="text-danger">The first 3 letters of the course code can be used to Search.
                            <br/>Instead of entering full course code eg Gss101  which will give you the departments that offers the course ,<br/>
                             you can enter GSS  and all the GSS courses will be displayed. eg Gss101, Gss202 etc</p>
                        </div>
                        </div>
               
                

@endsection   
@section('script')
<script src="{{URL::to('js/main.js')}}"></script>

@endsection
