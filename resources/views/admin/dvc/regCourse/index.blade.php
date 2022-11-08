@extends('layouts.admin')
@section('title','Registered Courses')
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
                <div class="panel-heading">Registered Courses</div>
                <div class="panel-body">
             <form class="form-horizontal" role="form" method="POST" action="{{ url('getRegCourse') }}" target="_blank" data-parsley-validate>
                                  {{ csrf_field() }}
                   <div class="form-group{{ $errors->has('start_serial_number') ? ' has-error' : '' }}">
                    
                                <div class="col-md-2">
                                <label for="session" class="control-label">Select session</label>
                                 <select class="form-control" name="session" required>
                               <option value="">Select</option>
                               @for($i=2016;$i <= Date('Y'); $i++)
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
                            <div class="col-md-2">
                                <label for="session" class="control-label">Select semester</label>
                                 <select class="form-control" name="semester" required>
                               <option value="">Select</option>
                              

                        <option value="1">First</option>
                        <option value="2">Second</option>
                              
                             </select>

                                @if ($errors->has('semester'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('semester') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-sm-2">
                              <label for="session" class="control-label">Faculty</label>
                              <select class="form-control" name="faculty" id="faculty_id" required>
                              <option value=""> - - Select - -</option>
                               
                                  @foreach ($f as $v)
                                 
                                  <option value="{{$v->id}}">{{$v->faculty_name}}</option>
                                  @endforeach
                                
                              </select>
                             
                            </div>
                              <div class="col-sm-2">
                              <label for="session" class="control-label">Department</label>
                              <select class="form-control" name="department" id="department_id" required>
                              
                               
                                
                              </select>
                             
                            </div>
                           
                            <div class="col-sm-2">
                              <label for="fos" class="control-label">Field Of Study</label>
                              <select class="form-control" name="fos" id="fos_id" required>
                            
                                
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
<script src="{{URL::to('js/main.js')}}"></script>

@endsection
