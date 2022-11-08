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
                        @if($result->name =="admin" || $result->name =="support" || $result->name =="DVC")


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
                           

                     
      
                          
                        
            @elseif($result->name =="HOD" || $result->name =="examsofficer")
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
                                <button type="submit" class="btn btn-primary" name="registerCourse" value="1">
                                    <i class="fa fa-btn fa-user"></i> Generate 
                                </button>
                                <button type="submit" class="btn btn-success" name="ers" value="1">
                                    <i class="fa fa-btn fa-user"></i> Generate ERS
                                </button>
                            </div>
                       

                        </div>

                        </form>
                        @if(isset($c))
                        @if(count($c) != 0)
                        <table class="table">

                  <tr><th>#</th><th>Code</th><th>Action</th></tr>
                  <?php $i =0; ?>
                        @foreach($c as $v)
                        <tr><td>{{$i ++}}</td>
                        <td>{{$v->course_code}}</td>
                        <td>
                          <a href="{{url('downloadMopUpERS',[$v->id,$d])}}" class="btn btn-success">Download ERS</a>
                        </td>
                      </tr>
                        @endforeach
                        </table>
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

                    