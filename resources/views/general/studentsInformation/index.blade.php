@extends('layouts.admin')
@section('title','Students Information')
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
                <div class="panel-heading">Students Information</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" target="_blank" action="{{ url('studentsInformation') }}" data-parsley-validate>
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
                           

                     
      
                          
                                 <div class="col-sm-2">
                              <label for="session" class=" control-label">Entry Year</label>
                              <select class="form-control" name="session" required>
                              <option value=""> - - Select - -</option>
                               
                                  @for ($year = (date('Y')); $year >= 2016; $year--)
                                  {{!$yearnext =$year+1}}
                                  <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                  @endfor
                                
                              </select>
                             
                            </div>
            @elseif($result->name =="HOD" || $result->name =="examsofficer")
                           
                      
            <div class="col-sm-4">
                              <label for="session" class=" control-label">Session</label>
                              <select class="form-control" name="session"  required>
                              <option value=""> - - Select - -</option>
                               
                                  @for ($year = (date('Y')); $year >= 2016; $year--)
                                  {{!$yearnext =$year+1}}
                                  <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                  @endfor
                                
                              </select>
                             
                            </div>
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
 <div class="col-sm-4">
                              <label for="fos" class=" control-label">Field Of Study</label>
                              <select class="form-control" name="fos" id="fos_id" required>
                               <option value=""> - - Select - -</option>
                                 
                                 
                                  <option value=""></option>
                               
                                  
                              </select>
                             
                            </div>


@else

                        
                
                        

                               <div class="col-sm-3">
                              <label for="session" class=" control-label">Session</label>
                              <select class="form-control" name="session" required>
                              <option value=""> - - Select - -</option>
                               
                                  @for ($year = (date('Y')); $year >= 2016; $year--)
                                  {{!$yearnext =$year+1}}
                                  <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                  @endfor
                                
                              </select>
                             
                            </div>
                       
                            
                             @endif
                        

                       
                        
                         
                       
                         

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
                      
                          
                     
                            

                         
                            <div class="col-md-1">
                              <br/>
                                <button type="submit" class="btn btn-primary" name="registerCourse" value="1">
                                    <i class="fa fa-btn fa-user"></i> Generate 
                                </button>
                            </div>
                         <!--   <div class="col-md-2">
                              <br/>
                                <button type="submit" class="btn btn-success" name="print" value="PDF">
                                    <i class="fa fa-btn fa-user"></i> Generate  PDF Copy
                                </button>
                            </div>

                            <div class="col-md-2">
                              <br/>
                                <button type="submit" class="btn btn-primary" name="print" value="2">
                                    <i class="fa fa-btn fa-user"></i> Print Registered student
                                </button>
                            </div>
                            <div class="col-md-2">
                              <br/>
                                <button type="submit" class="btn btn-danger" name="ers" value="ers">
                                    <i class="fa fa-btn fa-user"></i> Excel ERS
                                </button>
                            </div>-->

                        </div>

                        </form>
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

                    