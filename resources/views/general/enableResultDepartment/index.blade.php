@extends('layouts.admin')
@section('title','Enable Department Result Uploads')
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
                <div class="panel-heading">Enable Department Result Uploads</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST"  action="{{ url('enableResultDepartment') }}" data-parsley-validate>
                        {{ csrf_field() }}
                        <div class="form-group">
                     


                       <div class="col-sm-2">
                                 <label for="faculty">Faculty</label>
                                <select class="form-control" name="faculty" required>
                                    <option value="">Select</option>
                                    @if(isset($fc))
                                    @foreach($fc as $v)
                                    <option value="{{$v->id}}">{{$v->faculty_name}}</option>

                                    @endforeach

                                    @endif
                                </select>
</div>
               

                       
                            <div class="col-md-1">
                              <br/>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i> Continue 
                                </button>
                            </div>
                       
                        </div>

                        </form>
                        </div>
                        </div>
                      </div>

                      @if(isset($d))

                      <div class="col-sm-12" >
            <div class="panel panel-default">
             
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST"  action="{{ url('updateEnableResultDepartment') }}" data-parsley-validate>
                        {{ csrf_field() }}
                        <div class="form-group">
                     


                       <div class="col-sm-4" style="border:1px black solid;">
                        <label for="faculty">Department</label>
                          @if($d)
                          @foreach($d as $v)
                          <p>
                          
                          <input type="checkbox" name='id[]' value="{{$v->id}}" />
                          {{$v->department_name}}
</p>
                          @endforeach

                          @endif
</div>
<div class="col-sm-2" style="border:1px black solid;">
                        <label for="faculty">Session</label>
                    
                               
                                  @for ($year = $ra->session; $year >=2016 ; $year--)
                                  {{!$yearNext =$year+1}}
                                  <p>
                                 
                          <input type="checkbox" name='session[]' value="{{$year}}"  />
                          {{$year.'/'.$yearNext}}
                        </p>
                                  
                                  @endfor
                                
                             
</div>

                       
                            <div class="col-md-1">
                              <br/>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-btn fa-user"></i> Save
                                </button>
                            </div>
                       
                        </div>

                        </form>
                        </div>
                        </div>
                      </div>
                      @endif

                      

                        </div>
                      

@endsection  


                    