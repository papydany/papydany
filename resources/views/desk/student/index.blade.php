@extends('layouts.admin')
@section('title','student')
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

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">New student</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('new_student') }}" data-parsley-validate>
                    {{ csrf_field() }}
                    <div class="form-group">



                        <div class="col-sm-3">
                            <label for="fos" class=" control-label">Field Of Study</label>
                            <select class="form-control" name="fos" required>
                                <option value=""> - - Select - -</option>

                                @foreach($f as $v)
                                    <option value="{{$v->id}}">{{$v->fos_name}}</option>
                                @endforeach

                            </select>

                        </div>

                        <div class="col-sm-3">
                            <label for="session" class=" control-label">Session</label>
                            <select class="form-control" name="session" required>
                                <option value=""> - - Select - -</option>

                                @for ($year = (date('Y')); $year >= 2009; $year--)
                                    {{!$yearnext =$year+1}}
                                    <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                @endfor

                            </select>

                        </div>

                        <div class="col-sm-3">
                            <label for="session" class=" control-label">Entry Month</label>
                            <select class="form-control" name="entry_month" required>
                                @if(Auth::user()->programme_id == 4)
                                <option value=""> - - Select - -</option>
                                    <option value="1">April</option>
                                    <option value="1">August</option>
                                @else
                                    <option value="0">January</option>
                                @endif


                            </select>

                        </div>


                        <div class="col-sm-3">
                            <br/>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-btn fa-user"></i> View Student
                            </button>
                        </div>
                    </div>
                 {{-- @for($i = 0; $i < 5; $i++) --}}
                    <div style="border-style: solid; margin-bottom:1em; padding:1em">
                        <div class="form-group">
                         <div class="col-sm-4">
                              <label for="Course_title" class=" control-label">Surname</label>
                                <input type="text" class="form-control" name="surname" value="{{ old('surname') }}">
                            </div>
                            <div class="col-sm-4">
                              <label for="Course_title" class=" control-label">FirstName</label>
                                <input type="text" class="form-control" name="firstname" value="{{ old('name') }}">
                            </div>
                            <div class="col-sm-4">
                              <label for="Course_title" class=" control-label">Othername</label>
                                <input type="text" class="form-control" name="othername" value="{{ old('othername') }}">
                            </div>
                        </div>

                        <div class="form-group">
                         <div class="col-sm-4">
                              <label for="Course_title" class=" control-label">Email</label>
                                <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                            </div>
                            <div class="col-sm-4">
                              <label for="Course_title" class=" control-label">Phone</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                            </div>
                            <div class="col-sm-4">
                            <label for="Course_title" class=" control-label">Gender</label>
                              <select class="form-control" name="gender"> 
                              <option value="">Select Gender</option>
                                  <option value="male">male</option>
                                  <option value="female">female</option>
                                  <option></option>
                              </select>
                            </div>
</div>
<div class="form-group">
<div class="col-sm-4">
                              <label for="Course_title" class=" control-label">Matric Number</label>
                                <input type="text" class="form-control" name="matric_number" value="">
                            </div>
                         <div class="col-sm-4">
                              <label for="Course_title" class=" control-label">Pin</label>
                                <input type="text" class="form-control" name="pin" value="">
                            </div>
                            <div class="col-sm-4">
                              <label for="Course_title" class=" control-label">Serial</label>
                                <input type="text" class="form-control" name="serial" value="">
                            </div>
                          
</div>                 
                    </div>                       
              

                         {{-- @endfor --}}
                

                </form>
                </div>
            </div>
        </div>
    
            </div>
        </div>
    </div>
</div>


@endsection

