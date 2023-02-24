@extends('layouts.admin')
@section('title','Assign Course')
@section('content')
@inject('r','App\Models\R')
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
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">Assign Courses To Lecturere In Other Department</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/assign_course_other') }}" data-parsley-validate>
                        {{ csrf_field() }}

                        @if($result->name =="admin" || $result->name =="support")

<div class="form-group">
  <input type="hidden" name="admin" value="1">
                          <div class="col-sm-3">
                                 <label for="programme">Programm</label>
                                <select class="form-control" name="programme_id" id='programme_id'>
                                    <option value="">Select</option>
                                    @if(isset($p))
                                    @foreach($p as $v)
                                    <option value="{{$v->id}}">{{$v->programme_name}}</option>

                                    @endforeach

                                    @endif
                                </select>
                                </div>

                       <div class="col-sm-3">
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

                                <div class="col-sm-3">
                              <label for="password-confirm" >Department</label>
                              <select class="form-control" name="department_id" id="department_id" required>
                             </select>

                               
                            </div>
                            <div class="col-sm-3">
                                 <label for="faculty">Field Of Study</label>
                                  <select class="form-control" name="fos" id="fos_id" required>
                             </select>
                                </div>

                        </div>
                          <div class="form-group">
                              <div class="col-sm-3">
                              <label for="level" class=" control-label">Level</label>
                              <select class="form-control" name="level" id="level_id">
                                  <option value=""> - - Select - -</option>
                                 
                              </select>
                             
                            </div>
                          
                                 <div class="col-sm-3">
                              <label for="session" class=" control-label">Session</label>
                              <select class="form-control" name="session_id" required>
                              <option value=""> - - Select - -</option>
                               
                                  @for ($year = (date('Y')); $year >= 2016; $year--)
                                  {{!$yearnext =$year+1}}
                                  <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                  @endfor
                                
                              </select>
                             
                            </div>

                             <div class="col-sm-3">
                              <label for="semester" class=" control-label">Semester</label>
                              <select class="form-control" name="semester" id="semester_id">
                                  <option value=""> - - Select - -</option>
                                 
                              </select>
                             
                            </div>
                          </div>



@else
                        <div class="form-group">
                     <div class="col-sm-2">
                              <label for="level" class=" control-label">Level</label>
                              <select class="form-control" name="level">
                                  <option value=""> - - Select - -</option>
                                  @if(isset($l))
                                  @if(count($l) != 0)
                                  @foreach($l as $v)
                                  <option value="{{$v->level_id}}">{{$v->level_name}}</option>
                                  @endforeach
                                  @else
                                  <option value="1">{{100}}</option>
                                  <option value="2">{{200}}</option>
                                  <option value="3">{{300}}</option>
                                  <option value="4">{{400}}</option>
                                  <option value="5">{{500}}</option>
                                 
                                  @endif
                                  @endif
                              </select>
                             
                            </div>

                            @if($result->name =="HOD")
                           
                      
                           <input type="hidden" name="HOD" value="1">
                                           <div class="col-sm-2">
                                            <label for="fos" class=" control-label">Programme</label>
                                            <select class="form-control" name="programme_id" id="p_id" required>
                                             <option value=""> - - Select - -</option>
                                             @if(isset($p))
                                                @foreach($p as $v)
                                                <option value="{{$v->id}}">{{$v->programme_name}}</option>
                                                @endforeach
                                                @endif
                                                
                                            </select>
                                           
                                          </div>
               <div class="col-sm-3">
                                            <label for="fos" class=" control-label">Field Of Study</label>
                                            <select class="form-control" name="fos" id="fos_id" required>
                                             <option value=""> - - Select - -</option>
                                               
                                               
                                                <option value=""></option>
                                             
                                                
                                            </select>
                                           
                                          </div>
              
              
              @else

                         
                             <div class="col-sm-3">
                              <label for="fos" class=" control-label">Field Of Study</label>
                              <select class="form-control" name="fos" required>
                               <option value=""> - - Select - -</option>
                                 
                                  @foreach($f as $v)
                                  <option value="{{$v->id}}">{{$v->fos_name}}</option>
                                  @endforeach
                                  
                              </select>
                             
                            </div>
                            @endif

                               <div class="col-sm-2">
                              <label for="session" class=" control-label">Session</label>
                              <select class="form-control" name="session_id" required>
                              <option value=""> - - Select - -</option>
                               
                                  @for ($year = (date('Y')); $year >= 2016; $year--)
                                  {{!$yearnext =$year+1}}
                                  <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                  @endfor
                                
                              </select>
                             
                            </div>
                            @if($result->name =="HOD")
                            <div class="col-sm-2">
                              <label for="semester" class=" control-label">Semester</label>
                              <select class="form-control" name="semester">
                                  <option value=""> - - Select - -</option>
                                  <option value="1">FIRST</option>
                                  <option value="2">SECOND</option>
                                
                              </select>
                             
                            </div>

                            @else
                          <div class="col-sm-3">
                              <label for="semester" class=" control-label">Semester</label>
                              <select class="form-control" name="semester">
                                  <option value=""> - - Select - -</option>
                                  @if(isset($s))
                                  @foreach($s as $v)
                                  <option value="{{$v->semester_id}}">{{$v->semester_name}}</option>
                                  @endforeach
                                  @endif
                              </select>
                             
                            </div>
                            @endif
                            </div>
                            @endif
                            <div class="form-group">
   
                             <div class="col-sm-3">
                                      <br/>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-btn fa-user"></i> Continue
                                </button>
                            </div>
                            </div>
                              </form>
                        

                      

                      



                        @if(isset($rs))
                        @if(count($rs) > 0)
                     
                        <hr/>
                  {{!$next = $g_s + 1}}
                  <?php  $department=$r->get_departmetname($d_id); ?>
                        <p><span><strong>Department :: </strong>{{$department}}</span>&nbsp;&nbsp;
                          <span><strong>Level : </strong>{{$g_l}}00</span>&nbsp;&nbsp;
                        <span><strong>Session : </strong>{{$g_s.' / '.$next}}</span></p>
                          <form class="form-horizontal" role="form" method="POST" action="{{url('/assign_course_o')}}" data-parsley-validate>
                        {{ csrf_field() }}
                        <div class="col-sm-7">
                        <table class="table table-bordered table-striped">
                        <tr>
                       <th>Select</th>
                       <th>Code</th>
                       <th>Status</th>
                       <th>Unit</th>
                       
                       </tr>
                        @if($result =="admin" || $result =="support")
                      <input type="hidden" name="department_id" value="{{$d_id}}">
                        <input type="hidden" name="faculty_id" value="{{$f_id}}">
                        <input type="hidden" name="admin" value="1">

                        @endif
                    
                       @foreach($rs as $v)
                      <tr>
                       <td>
                        <input type="hidden" name="fos_id[]" value="{{$v->fos_id}}">
                        <input type="hidden" name="level[]" value="{{$v->level_id}}">
                        <input type="hidden" name="semester_id[]" value="{{$v->semester_id}}">
                        <input type="hidden" name="session[]" value="{{$v->session}}">
                       <input type="checkbox" name="id[]" value="{{$v->id}}"></td>
                       <td> {{$v->reg_course_code}} </td>
                      <td> {{$v->reg_course_unit}} </td>
                        <td> {{$v->reg_course_status}} </td>
                        </tr>
                       @endforeach
                       </table>
                       </div>
                       <div class="col-sm-5">
            <table class="table table-bordered table-striped col-sm-5">
                        <tr>
                       <th>Select Department</th>
                       
                       </tr>
                       <tr>
                        <td>
                     @if(isset($depart))
                        @if(count($depart) > 0)
                        <select class="form-control" name="department" id="department">
                          <option value="">--- Select ---</option>
                          @foreach($depart as $d)
                       <option value="{{$d->id}}">{{$d->department_name}}</option>

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
                              <select class="form-control" name="Lecturer" id="Lecturer" required>
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
                                </form>
                       @else
                        <p class="alert alert-warning">No Department  is available</p>
                        @endif
                        @endif
                    
                       
                       @else
                        <p class="alert alert-warning">No register courses available is available</p>
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

  @endsection                      @section('script')
<script src="{{URL::to('js/main.js')}}"></script>

@endsection