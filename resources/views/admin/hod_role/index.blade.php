@extends('layouts.admin')
@section('title','Assign HOD And Examsofficer Role')
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

    <div class="row">
        <div class="col-sm-12" style="min-height: 520px;">
            <div class="panel panel-default">
                <div class="panel-heading">Assign HOD & Examsofficer Role <a href="{{url('view_assign_hod')}}" class="btn btn-danger pull-right">View HOD</a></div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="GET" action="{{ url('get_lecturer_4_hod') }}" data-parsley-validate>
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('department_name') ? ' has-error' : '' }}">
                      
                        @if($result->name == 'Deskofficer')
                            <input type="hidden" name="faculty_id" value="{{Auth::user()->faculty_id}}"/>

                            <input type="hidden" name="department_id" value="{{Auth::user()->department_id}}"/>
                            @else

                            <div class="col-md-4">
                                <label for="faculty_id" class="control-label">Select Faculty</label>
                                 <select class="form-control" name="faculty_id" id="faculty_id" required>
                               <option value="">Select</option>
                               @if(count($f) > 0)
                               @foreach($f as $v)
                        <option value="{{$v->id}}">{{$v->faculty_name}}</option>
                                @endforeach
                                @endif
                             </select>

                                @if ($errors->has('faculty_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('faculty_id') }}</strong>
                                    </span>
                                @endif
                            </div>

                             <div class="col-md-4">
                                <label for="department_id" class="control-label">Select Department</label>
                                 <select class="form-control" name="department_id" id="department_id" required>
                               <option value="">Select</option>
                               </select>
                            </div>

                        @endif

                     
                            <div class="col-md-3">
                            <br/>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-btn fa-user"></i> Continue
                                </button>
                            </div>

                        </div>

                        </form>
                        </div>
                        </div>
                        <div class="col-sm-12"> 
                           @if(isset($u))
                        @if(count($u) > 0)
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('assign_hod') }}" data-parsley-validate>
                        {{ csrf_field() }}
                        <table class="table table-bordered table-striped">
                        <tr >
                          <td>Select</td>
                        <th>S/N</th>
                          <th>Title</th>
                        <th>Name</th>
                         <th>Username</th>
                    
                  
                        <th>Edit Right Status</th>
                       <th>Edit Right</th>
                       </tr>
                       {{!!$c = 0}}
                       @foreach($u as $v)
                      @if($v->status == 0) 
                      <tr>
                      @else
                      <tr style="background-color:goldenrod">
                      @endif
                      
                        <td><label><input type="radio" name="optradio" value="{{$v->id.'~'.$v->department_id}}"></label></td>
                       <td>{{++$c}}</td>
                         <td>{{$v->title}}</td>
                       <td>{{$v->name}}</td>
                       <td>{{$v->username}}</td>
                      <!-- <td>{{$v->password}}</td>-->
                       
<td>{{$v->edit_right}}</td>
<td><div class="btn-group">
  <button type="button" class="btn btn-success dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Action  <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
      @if($v->status ==1)
      <li><a href="{{url('activate',[$v->id,0])}}">Activate</a></li>
         @else
    <li><a href="{{url('/edit_right',[$v->id,0])}}">0</a></li>
    <li><a href="{{url('/edit_right',[$v->id,6])}}">6</a></li>
    <li><a href="{{url('/edit_right',[$v->id,8])}}">8</a></li>
    <li><a href="{{url('/edit_right',[$v->id,10])}}">10</a></li>
    <li><a href="{{url('/edit_right',[$v->id,12])}}">12</a></li>
    
   
        <li class="divider"></li>
        <li><a href="{{url('activate',[$v->id,1])}}">Deactivate</a></li>
        <li class="divider"></li>
        <li><a href="{{url('update_email',$v->id)}}">Update Email</a></li>
        @endif
  </ul>
</div></td>
       

                       </tr>
                       @endforeach
                     <tr>
                     <td colspan="3"><input type="submit" class="btn btn-success btn-block" name="hod" value="Assign HOD"></td>
                     <td></td>
                     <td colspan="2"><input type="submit" class="btn btn-primary btn-block" name="eo" value="Assign Exams Officer"></td>
                     </tr>
                        </table>
                      </form>
  {{ $u->links() }}

                        @endif
                        @endif

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
                   