@extends('layouts.admin')
@section('title','Specialization')
@section('content')
@inject('r','App\Models\R')
<?php $result= session('key'); ?>
 <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Dashboard <small>Statistics Overview</small>
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </li>
                        </ol>
                    </div>
                </div>

    <div class="row">
        <div class="col-sm-12" style="min-height: 420px;">
            <div class="panel panel-default">
                <div class="panel-heading">Create Specialization Field</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/newSpecialization') }}" data-parsley-validate>
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('department_name') ? ' has-error' : '' }}">
                         <div class="col-md-4">
                              <label for="fos_name" class=" control-label">New Specialization</label>
                                <input  type="text" class="form-control" name="name" value="" required>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                      
                            @if($result->name =="admin" || $result->name =="support")
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

                            <div class="col-md-4">
                                <label for="department_id" class="control-label">Select FOS</label>
                                 <select class="form-control" name="fos_id" id="fos_id" required>
                               <option value="">Select</option>
                               </select>
                            </div>
                            @else

                            <div class="col-sm-4">
                                <label for="fos" class=" control-label">Field Of Study</label>
                                <select class="form-control" name="fos_id" required>
                                 <option value=""> - - Select - -</option>
                                   
                                    @foreach($fos as $v)
                                    <option value="{{$v->id}}">{{$v->fos_name}}</option>
                                    @endforeach
                                    
                                </select>
                               
                              </div>
                              @endif

                             <div class="col-md-4">
                                <label for="programme_id" class="control-label">Select Programme</label>
                                 <select class="form-control" name="programme_id"  required>
                               <option value="">Select</option>
                               @if(count($p) > 0)
                               @foreach($p as $v)
                        <option value="{{$v->id}}">{{$v->programme_name}}</option>
                                @endforeach
                                @endif
                             </select>

                                @if ($errors->has('programme_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('programme_id') }}</strong>
                                    </span>
                                @endif
                            </div>

                             <div class="col-md-4">
                              <label for="duration" class=" control-label">Level</label>
                                <input  type="number" class="form-control" name="level" value="" placeholder="Enter the level specialization is starting" required>

                                @if ($errors->has('level'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('level') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-3">
                            <br/>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-btn fa-user"></i> Create
                                </button>
                            </div>

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
                   