@extends('layouts.admin')
@section('title', 'Register Course')
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
                <div class="panel-heading">Registered Courses</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register_course') }}"
                        data-parsley-validate>
                        {{ csrf_field() }}
                        <div class="form-group">
                
                        <div class="col-sm-3">
                            <label for="semester" class=" control-label">Semester</label>
                            <select class="form-control" name="semester">
                                <option value=""> - - Select - -</option>
                              
                                    <option value="1">First Semester</option>
                                        <option value="2">Second Semester</option>
                               
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
                              <select class="form-control" name="department" id="department_id">
                             </select>

                               
                            </div>
               

                        @if (Auth::user()->programme_id == 4)
                            <div class="col-sm-3">
                                <label for="semester" class=" control-label">Entry Month</label>
                                <select class="form-control" name="month">
                                    <option value="">-- Select --</option>
                                    <option value="1">April Contact</option>
                                    <option value="2">August Contact</option>

                                </select>

                            </div>

                        @endif

                        <div class="col-sm-3">
                            <br />
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-btn fa-user"></i> View Course
                            </button>
                        </div>

                </div>

                </form>
            </div>
        </div>
    </div>
    </div>
    @if (isset($course))
    @if (count($course) > 0)
    <div class="row" style="background-color: #cce;padding-top: 20px; border-radius: 4px;">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                  Register Courses
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                
                
                  
                        @if ($semester == 1)
                            First
                        @elseif($semester ==2)
                            Second
                        @endif Semester
                    
           
            </div>
            <?php $result=session('key');?>
            <div class="panel-body">
                
                   
                        @if (isset($f))
                            @if (count($f) > 0)
                                <form class="form-horizontal" role="form" method="POST" action="{{ url('/reg_course') }}"
                                    data-parsley-validate>
                                    {{ csrf_field() }}
                                   
                                    @if (isset($m))
                                        <input type="hidden" name="month" value="{{ $m }}">
                                    @endif
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label for="fos" class=" control-label">Field Of Study</label>
                                            <select class="form-control" name="fos"  required>
                                                <option value=""> - - Select - -</option>

                                                @foreach ($f as $v)
                                                    <option value="{{ $v->id }}">{{ $v->fos_name }}</option>
                                                @endforeach

                                            </select>

                                        </div>
                                  
                                        <div class="col-sm-3">
                                            <label for="session" class=" control-label">Session</label>
                                            <select class="form-control" name="session_id" required>
                                                <option value=""> - - Select - -</option>

                                                @for ($year = date('Y'); $year >= 2009; $year--)
                                                    {{ !($yearnext = $year + 1) }}
                                                    <option value="{{ $year }}">{{ $year . '/' . $yearnext }}</option>
                                                @endfor

                                            </select>

                                        </div>
                                        <div class="col-sm-3">
                                      

                                        <label for="level" class=" control-label">Level</label>
                                    <select class="form-control" name="level" required>
                                    @if($result->name =="Deskofficer")
                                        <option value=""> - - Select - -</option>
                                        @if (Auth::user()->faculty_id == $med || Auth::user()->faculty_id == $den)
                                
                                  
                                        {{ $i = 1 }}
                                        @foreach ($l as $k => $v)

                                            @if ($v->level_id < 3)

                                                <option value="{{ $v->level_id }}">{{ $v->level_name }}</option>

                                            @else
                                                <option value="{{ $v->level_id }}">PART {{ $i++ }}</option>

                                            @endif
                                            @if ($v->level_id == 6)
                                            @break;
                                        @endif

                            @endforeach
                            @else
                            @if (isset($l))
                                    @foreach ($l as $v)
                                        <option value="{{ $v->level_id }}">{{ $v->level_name }}</option>
                                    @endforeach
                                @endif
                            @endif
@else
<option value="1">100</option>
                                  <option value="2">200</option>
                                  <option value="3">300</option>
                                  <option value="4">400</option>
                                  <option value="5">500</option>
                                  <option value="6">600</option>
                                  <option value="7">700</option>
                                  
@endif
                            </select>

                        </div>



                                        <div class="col-sm-3">
                                            <br />
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fa fa-btn fa-user"></i> Register Course
                                            </button>
                                        </div>

                                    </div>
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>S/N</th>
                                            <th>Title</th>
                                            <th>Code</th>
                                            <th>Status</th>
                                            <th>Unit</th>
                                            <th>Semester</th>
                                            <th>Action</th>
                                        </tr>
                                        {{ !!($c = 0) }}
                                        @foreach ($course as $v)
                                            <tr>
                                                <td>{{ ++$c }}</td>
                                                <td>{{ $v->course_title }}</td>
                                                <td>{{ $v->course_code }}</td>
                                                <td>{{ $v->status }}</td>
                                                <td>{{ $v->course_unit }}</td>
                                                <td>
                                                    @if ($v->semester == 1)
                                                        First Semester
                                                    @else
                                                        Second Semester
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="id[]" value="{{ $v->id }}">

                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </form>
                            @endif
                        @else
                            <div class=" col-sm-10 col-sm-offset-1 alert alert-warning" role="alert">
                                Field Of study have not been assign to the account.. contact system Adminstrator
                            </div>
                        @endif
                    @else

                        <div class=" col-sm-10 col-sm-offset-1 alert alert-warning" role="alert">
                            No Records!!!
                        </div>
                  

            </div>
        </div>
    </div>
    </div>
    @endif
    @endif
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
    <script src="{{ URL::to('js/main.js') }}"></script>

@endsection
