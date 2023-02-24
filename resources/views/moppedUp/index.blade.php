@extends('layouts.admin')
@section('title','Mopped Up Exams')
@section('content')
<?php $result= session('key'); ?>
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
        <div class="col-sm-12" >
            <div class="panel panel-default">
                <div class="panel-heading">Mop Up Registered Students</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" target="_blank" action="{{ url('moppedUpRegisteredStudents') }}" data-parsley-validate>
                        {{ csrf_field() }}
                        <div class="form-group">
                        @if($result->name =="admin" || $result->name =="support" || $result->name =="DVC" || Auth::user()->faculty_id == 29)


  <input type="hidden" name="admin" value="1">
                          <div class="col-sm-2">
                                 <label for="programme">Programm</label>
                                <select class="form-control" name="programme">
                                    <option value="">Select</option>
                                    @if(isset($p))
                                    @foreach($p as $v)
                                    <option value="{{$v->id}}">{{$v->programme_name}}</option>

                                    @endforeach

                                    @endif
                                </select>
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
                           @elseif($result->name =="HOD")
                           <div class="col-sm-4">
                              <label for="fos" class=" control-label">Programme</label>
                              <select class="form-control" name="programme" id="p_id" required>
                               <option value=""> - - Select - -</option>
                               @if(isset($p))
                                  @foreach($p as $v)
                                  <option value="{{$v->id}}">{{$v->programme_name}}</option>
                                  @endforeach
                                  @endif
                                  
                              </select>
                             
                            </div>
@else

    
                             @endif
            
                            <div class="col-md-12">
                            <br/>
                              @if(Auth::user()->faculty_id == 29)
                              <button type="submit" class="btn btn-success" name="ers" value="1">
                                    <i class="fa fa-btn fa-user"></i> Generate  ERS & Upload Result
                                </button>
                              @elseif($result->name =="HOD")
                              
                                <button type="submit" class="btn btn-primary" name="registerCourse" value="1">
                                    <i class="fa fa-btn fa-user"></i> Generate 
                                </button>
                                <button type="submit" class="btn btn-success" name="ers" value="1">
                                    <i class="fa fa-btn fa-user"></i> Assign Courses mop Up
                                </button>
                                <button type="submit" class="btn btn-danger" name="viewAssignCourse" value="1">
                                    <i class="fa fa-btn fa-user"></i> View Assign Courses (mop up)
                                </button>
                                <button type="submit" class="btn btn-success" name="deResult" value="1">
                                    <i class="fa fa-btn fa-user"></i> Download  ERS & Upload Result
                                </button>
                                @else
                                <button type="submit" class="btn btn-primary" name="registerCourse" value="1">
                                    <i class="fa fa-btn fa-user"></i> Generate 
                                </button>
                                <button type="submit" class="btn btn-success" name="deResult" value="1">
                                    <i class="fa fa-btn fa-user"></i> Download  ERS & Upload Result
                                </button>
                                @endif
                            </div>
                       

                        </div>

                        </form>
                        @if(isset($c))
                        @if(count($c) != 0)
                        @if($result->name =="HOD")
                        <form class="form-horizontal" role="form" method="POST" action="{{url('assignCoursesMopUp')}}" data-parsley-validate>
                        {{ csrf_field() }}
                        <input type="hidden" name="d" value="{{$d}}">
                        <input type="hidden" name="f" value="{{$f}}">
                        @endif
                        <table class="table">

                  <tr><th>#</th>
                  @if($result->name =="HOD")
                  <th>Select To Assign Course</th>
@endif
                  <th>Code</th><th colspan="2">Action</th></tr>
                  <?php $i =0; ?>
                        @foreach($c as $v)
                       <?php $code =substr($v->course_code,0,3); 
                       
                        ?>
                       @if(Auth::user()->faculty_id ==29)
                       <tr><td>{{$i ++}}</td>
                        <td>{{$v->course_code}}</td>
                        <td>
                          <a href="{{url('downloadMopUpERS',[$v->id,$d])}}" class="btn btn-success">Download ERS</a>
                        </td>

                        <td>
                          
                          <a href="{{url('uploadMopUpResult',[$v->id,$d])}}" class="btn btn-primary" target="_blank">Upload Result</a>
                        </td>
                       <td> <a href="{{url('viewMopUpResult',[$v->id,$d])}}" class="btn btn-warning" target="_blank">View Result</a>
</td>
                      </tr>
                      @elseif($code =='GSS' && $f !=29 || $code =='GST' && $f !=29)
                       @else
                       <tr><td>{{$i ++}}</td>
                       @if($result->name =="HOD")
                        <td>
                        <input type="checkbox" name="id[]" value="{{$v->id}}">
                        
                            </td>
                        @endif
                        <td>{{$v->course_code}}</td>
                        


                      
                      </tr>
                     
                      @endif
                        @endforeach
                        </table>
                        @if($result->name =="HOD")  
                        <div class="col-sm-6">
                        <table class="table table-bordered table-striped col-sm-5">
                        <tr>
                       <th>Select Department</th>
                       
                       </tr>
                       <tr>
                        <td>
                     @if(isset($dept))
                        @if(count($dept) > 0)
                        <select class="form-control" name="department" id="department">
                          <option value="">--- Select ---</option>
                          @foreach($dept as $dp)
                       <option value="{{$dp->id}}">{{$dp->department_name}}</option>

                          @endforeach
                        </select>
                      </td>
                          </tr>
                          <tr>
                            <th>
                            Select Lecturer
                          </th>
                          </tr>
                          <tr>
                            <td>
                              <select class="form-control" name="lecturer" id="Lecturer" required>
                                <option value=""></option>
                                
                              </select>
                            </td>
                          </tr>
                      </table>
                      
                      </div>
                      <div class="clear"></div>
                      <div class="form-group col-sm-12 col-sm-offset-3">
 
                        <button type="submit" class="btn btn-danger btn-lg ">
                                    <i class="fa fa-btn fa-user"></i> Assign Courses
                                </button>
                                </div>
                               
                                @endif
                                @endif
                                @endif
                                </form>
@else
<p>No Records</p>
                        @endif

                        @endif
                        </div>
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

                    