@extends('layouts.admin')
@section('title','Exams Officer')
@section('content')
 @inject('r','App\Models\R')
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
  
                 <div class="row">
        <div class="col-sm-12" style="min-height: 300px;">
            <div class="panel panel-default">
                <div class="panel-heading">View Result</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('post_v_result_gss') }}" data-parsley-validate>
                        {{ csrf_field() }}
                        <div class="form-group">
                     <div class="col-sm-2">
                              <label for="programme" class=" control-label">Programme</label>
                              <select class="form-control" name="programme" id="programme_id" required>
                                  <option value=""> - - Select - -</option>
                                  @if(isset($p))
                                  @foreach($p as $v)
                                  <option value="{{$v->id}}">{{$v->programme_name}}</option>
                                  @endforeach
                                  @endif
                              </select>
                             
                            </div>

                         
                         

                               <div class="col-sm-2">
                              <label for="session" class=" control-label">Session</label>
                              <select class="form-control" name="session"  required>
                              <option value=""> - - Select - -</option>
                               
                                  @for ($year = (date('Y')); $year >= 2009; $year--)
                                  {{!$yearnext =$year+1}}
                                  <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                  @endfor
                                
                              </select>
                             
                            </div>
                      
                          <div class="col-sm-3">
                              <label for="semester" class=" control-label">Semester</label>
                              <select class="form-control" name="semester" id="semester_id"  required>
                                  <option value=""> - - Select - -</option>
                                
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
                              <div class="col-sm-4 col-sm-offset-4" style="margin-top: 20px;">

                              
@if(isset($c))
                        @if(count($c) > 0)
                        <?php $collection =$c->groupBy('department_name')->toArray();?>
                        {{!$next = $s + 1}}
                  {{! $semester =DB::table('semesters')
                  ->where('semester_id',$sm)->first()}}
                        <p> <strong>Semester : </strong>{{$semester->semester_name}}</p>
                       
                        <p><strong>Session : </strong>{{$s.' / '.$next}}</p>

                      <form class="form-horizontal" role="form" method="POST" action="{{ url('d_result_gss') }}" target="_blank" data-parsley-validate>
                    {{ csrf_field() }}
                  <div class="form-group">
                  <label for="level" class=" control-label">Course</label>
                  <select class="form-control" name="id" required>
                     <option value="">-- select --</option>
                  
                      @foreach($collection as  $k => $value)
                     
                       <optgroup label="{{$k}}">
                       @foreach($value as $v)
                      <option value="{{$v->course_id.'-'.$v->department_id}}"> {{$v->reg_course_code}} </option>
                      @endforeach
                         </optgroup>
                    @endforeach

                 
                     </select>

                    
                     <input type="hidden" name="semester" value="{{$sm}}">
                      <input type="hidden" name="session" value="{{$s}}">
                      </div>
  <div class="form-group">
                  <label for="level" class=" control-label">Session</label>
                     <select class="form-control" name="period" required>
                     <option value="">-- select --</option>
                     <option value="NORMAL">NORMAL</option>
                     <option value="RESIT">RESIT (diploma)</option>
                      <option value="VACATION">VACATION</option>
                
                     </select>
                      </div>

                         <div class="form-group ">
 
                        <button type="submit" class="btn btn-success btn-block ">
                                    <i class="fa fa-btn fa-user"></i>View Result
                                </button>

                                </div>
                                <div class="form-group ">
 
 <input type="submit" class="btn btn-primary btn-block" name="excel" value="View By Excel"/>

         
         </div>
                                </form>

                       @else
                        <p class="alert alert-warning">No Course is avalable</p>
                        @endif
                        @endif           


</div>
                        </div>
                        </div>
                        </div>
                <!-- /.row -->

<div class="row" style="min-height: 220px;">
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

            