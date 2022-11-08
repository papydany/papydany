<?php use Illuminate\Support\Facades\Auth; ?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
       
        @inject('r','App\Models\R')
        @if(Auth::user()->programme_id == 0 && Auth::user()->department_id == 0)
        <a class="navbar-brand" href="{{url('/')}}"><img id="logo" src="{{asset('logo.png')}}" alt="Logo"></a>
        @elseif(Auth::user()->programme_id == 1)
<a class="navbar-brand" style="color:#fff;" href="{{url('/')}}"><strong>PDS</strong></a>
        @else

        <?php $dept= $r->get_departmetname(Auth::user()->department_id) ?>
          <a class="navbar-brand" style="color:#fff;" href="{{url('/')}}"><strong>{{ $dept}}</strong></a>
          @endif
    </div>
    <!-- Top Menu Items -->
    <ul class="nav navbar-right top-nav">
        @if (!Auth::guest())

        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i>  {{ Auth::user()->name }} <b class="caret"></b></a>
            <ul class="dropdown-menu">

                <li class="divider"></li>
                 <li>
                 <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>
                                    </li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    
                                            
                                        </form>
                                
             
            </ul>
        </li>
        @endif
    </ul>
    
       

  <?php $result= session('key'); //session('key') ?>
  

    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
             <li class="active">
                <a href="{{url('/')}}"><i class="fa fa-fw fa-dashboard"></i>
                 Dashboard</a>
            </li>
          
            @if($result->name =="DVC")
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo1"><i class="fa fa-fw fa-table"></i>Report<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo1" class="collapse">
                <li>
                <a href="{{url('reports1')}}"><i class="fa fa-fw fa-edit"></i>Report</a>
               
            </li>
            <li>
                <a href="{{url('registeredCourseToStudent')}}"><i class="fa fa-fw fa-edit"></i>Register Courses To  Student</a>
               
            </li>
                   
                   

                   
                </ul>
            </li>
   
           
            <li>
                <a href="{{url('approveResult')}}"><i class="fa fa-fw fa-edit"></i>Approve Result </a>
               
            </li>
            <li>
                <a href="https://oldportal.unicalexams.edu.ng/dvc.php" target="_blank"><i class="fa fa-fw fa-edit"></i>Approve Result(old portal)</a>
               
            </li>
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo2"><i class="fa fa-fw fa-table"></i>Graduate<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo2" class="collapse">
                    <li>
                        <a href="{{url('setupGraduate')}}">Setup</a>
                    </li>
                    <li>
                        <a href="{{url('viewGraduate')}}">View</a>
                    </li>
                   
                    <li>
                        <a href="{{url('oldportalviewGraduate')}}">View(old portal)</a>
                    </li>

                    <li>
                        <a href="{{url('firstClassStudent')}}">Class Of Degree Graduate</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo2a"><i class="fa fa-fw fa-table"></i>Audit<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo2a" class="collapse">
                    <li>
                        <a href="{{url('auditCourseCode')}}">Course code</a>
                    </li>
                    <li>
                        <a href="{{url('auditCourseCodeOld')}}">Old Course code</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="{{url('withdrawalOldportal')}}"><i class="fa fa-fw fa-edit"></i>Withdrawal (Oldportal)</a>
               
            </li>

@elseif($result->name =="admin" || $result->name =="support")  
@if($result->name =="support")
<li>
                <a href="javascript:;" data-toggle="collapse" data-target="#logs"><i class="fa fa-fw fa-bar-chart-o"></i> Log<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="logs" class="collapse">
                  <li>
                        <a href="{{url('uploadRightLog')}}" target="_blank">upload right</a>
                    </li>
                    <li>
                        <a href="{{url('regLog')}}">registration log</a>
                    </li>
                   
                </ul>
            </li>
            @endif

           
             <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo1"><i class="fa fa-fw fa-bar-chart-o"></i> FACULTY<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo1" class="collapse">
                  <li>
                        <a href="{{url('new_faculty')}}">New Faculty</a>
                    </li>
                    <li>
                        <a href="{{url('view_faculty')}}">View Faculty</a>
                    </li>
                   
                </ul>
            </li>
                <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo2"><i class="fa fa-fw fa-table"></i>Department<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo2" class="collapse">
                    <li>
                        <a href="{{url('new_department')}}">New Department</a>
                    </li>
                    <li>
                        <a href="{{url('view_department')}}">View Department</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo3"><i class="fa fa-fw fa-edit"></i>Programme<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo3" class="collapse">
                    <li>
                        <a href="{{url('new_programme')}}">New Programme </a>
                    </li>
                    <li>
                        <a href="{{url('view_programme')}}">View Programme</a>
                    </li>
                </ul>
            </li>

             <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo4"><i class="fa fa-fw fa-edit"></i>Field Of Study<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo4" class="collapse">
                    <li>
                        <a href="{{url('new_fos')}}">New FOS </a>
                    </li>
                    <li>
                        <a href="{{url('view_fos')}}">View FOS</a>
                    </li>

                    <li>
                        <a href="{{url('assign_fos')}}">Assign FOS</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo4s"><i class="fa fa-fw fa-edit"></i>Specialization<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo4s" class="collapse">
                    <li>
                        <a href="{{url('newSpecialization')}}">Create </a>
                    </li>
                    <li>
                        <a href="{{url('viewSpecialization')}}">View</a>
                    </li>

                    <li>
                        <a href="{{url('assignSpecialization')}}">Assign </a>
                    </li>
                    <li>
                        <a href="{{url('viewAssignSpecialization')}}">View Assign </a>
                    </li>
                </ul>
            </li>
             

  <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo5"><i class="fa fa-fw fa-edit"></i>Desk Officer<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo5" class="collapse">
                    <li>
                        <a href="{{url('new_desk_officer')}}">New Desk Officer</a>
                    </li>
                    <li>
                        <a href="{{url('view_desk_officer')}}">View Desk Officer</a>
                    </li>
                     <li>
                        <a href="{{url('suspend_desk_officer')}}">Suspend Desk Officer</a>
                    </li>
                </ul>
            </li>
            
        <!-- <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo6"><i class="fa fa-fw fa-edit"></i>PDS<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo6" class="collapse">
                    <li>
                        <a href="{{url('pds_new_desk_officer')}}">New Desk Officer</a>
                    </li>
                    <li>
                        <a href="{{url('pds_view_desk_officer')}}">View Desk Officer</a>
                    </li>
                      <li>
                        <a href="{{url('pds_create_course')}}">New course Science</a>
                    </li>-->
                   <!--  <li>
                        <a href="{{url('pds_create_course')}}">New course Science</a>
                    
                    <li>
                        <a href="{{url('pds_view_course')}}">View course Science</a>
                    </li>
                    
                        <a href="{{url('pds_create_course')}}">New course Modern language</a>
                    </li>
                    <li>
                        <a href="{{url('modern_view_course')}}">View course Modern language</a>
                    </li>
                </ul>
            </li>-->
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo7"><i class="fa fa-fw fa-edit"></i>Set Course Unit<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo7" class="collapse">
                    <li>
                        <a href="{{url('create_course_unit')}}">Create</a>
                    </li>
                    <li>
                        <a href="{{url('view_course_unit')}}">View </a>
                    </li>
                     
                    
                </ul>
            </li>
<li>
                        <a href="{{url('adminreg_course')}}">View Registered Courses</a>
                    </li>
   


             <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo12"><i class="fa fa-fw fa-bar-chart-o"></i> PIN<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo12" class="collapse">
                  @if($result->name =="support")  
                  <li>
                        <a href="{{url('create_pin')}}">Create Pin</a>
                    </li>
                     <li>
                        <a href="{{url('export_pin')}}">Export Pin</a>
                    </li>
                     <li>
                        <a href="{{url('view_unused_pin')}}">View Unused Pin</a>
                    </li>
                    @endif 
                     <li>
                        <a href="{{url('convert_pin')}}">Convert Pin</a>
                    </li>
                    <li>
                        <a href="{{url('student_pin')}}">Student Pin</a>
                    </li>
                    
                   
                    <li>
                        <a href="{{url('view_used_pin')}}">View Used Pin</a>
                    </li>
                    <li>
                        <a href="{{url('reset_pin')}}">Reset Pin</a>
                    </li>
                    
                </ul>
            </li>
               <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo9"><i class="fa fa-fw fa-edit"></i>Assign Courses<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo9" class="collapse">
                
                    <li>
                        <a href="{{url('assign_course_other')}}">Assign Courses</a>
                    </li>
                    <li>
                        <a href="{{url('view_assign_course')}}">View Assigned Courses</a>
                    </li>
                <!-- <li>
                       <li>
                        <a href="{{url('assign_course')}}">Assign Courses</a>
                    </li>
                        <a href="{{url('print_assign_course')}}">Print Assigned Courses</a>
                    </li>-->

                    
                </ul>
            </li>

            <li>
                <a href="{{url('reports')}}"><i class="fa fa-fw fa-edit"></i>Report</a>
               
            </li>
             
                       
@elseif($result->name =="Deskofficer")
<li>
    <a href="{{url('resultUploadRight')}}"><i class="fa fa-fw fa-edit"></i>Result Upload Right</a>
               
</li>
 
             <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo1"><i class="fa fa-fw fa-bar-chart-o"></i> Lecturer<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo1" class="collapse">
                  <li>
                        <a href="{{url('new_lecturer')}}">New Lecturer</a>
                    </li>
                    <li>
                        <a href="{{url('view_lecturer')}}">View Lecturer</a>
                    </li>
                   <li>
                        <a href="{{url('print_lecturer')}}" target="_blank">Print Lecturer</a>
                    </li>
                </ul>
            </li>
                <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo2"><i class="fa fa-fw fa-table"></i>Courses<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo2" class="collapse">
                    <li>
                        <a href="{{url('new_course')}}">New Courses</a>
                    </li>
                    <li>
                        <a href="{{url('view_course')}}">View Courses</a>
                    </li>
                   
                    <li>
                        <a href="{{url('register_course')}}">Register Courses</a>
                    </li>
                    <li>
                        <a href="{{url('view_register_course')}}">Print Registered Courses</a>
                    </li>
                    <li>
                        <a href="{{url('registeredcourse')}}">View Registered Courses</a>
                    </li>
                    <li>
                        <a href="{{url('specialized_course')}}">Register Specialized Courses</a>
                    </li>
                    <li>
                        <a href="{{url('view_specialized_course')}}">Print Specialized Courses</a>
                    </li>
                    <li>
                        <a href="{{url('viewSpecializedCourse')}}">View Specialized Courses</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo3"><i class="fa fa-fw fa-edit"></i>Assign Courses<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo3" class="collapse">
                   <li>
                        <a href="{{url('assign_course')}}">Assign Courses</a>
                    </li>
                    <li>
                        <a href="{{url('view_assign_course')}}">View Assigned Courses</a>
                    </li>
                     <li>
                        <a href="{{url('print_assign_course')}}">Print Assigned Courses</a>
                    </li>

                    <li>
                        <a href="{{url('assign_course_other')}}">Assign Courses(Other Lecturer)</a>
                    </li>
                </ul>
            </li>

            
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo4s"><i class="fa fa-fw fa-edit"></i>Specialization<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo4s" class="collapse">
                    <li>
                        <a href="{{url('newSpecialization')}}">Create </a>
                    </li>
                    <li>
                        <a href="{{url('viewSpecialization')}}">View</a>
                    </li>

                    <li>
                        <a href="{{url('assignSpecialization')}}">Assign </a>
                    </li>
                    <li>
                        <a href="{{url('viewAssignSpecialization')}}">View Assign </a>
                    </li>
                </ul>
            </li>

             <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo4"><i class="fa fa-fw fa-edit"></i>Student<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo4" class="collapse">
                <li>
                    <a href="{{url('new_student')}}">New Student</a>
                    </li>
                    <li>
                        <a href="{{url('returning_student')}}">Returning Student</a>
                    </li>
                    <li>
                        <a href="{{url('view_student')}}">Veiw Student </a>
                    </li>
                   

                    <li>
                        <a href="{{url('register_student_ii')}}">Registered Student II</a>
                    </li>
                    <li>
                    <a href="{{url('studentsWithOnlyProfile')}}">Outstanding Student</a>
                    </li>
                </ul>
            </li>
             

  <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo5"><i class="fa fa-fw fa-edit"></i>Result<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo5" class="collapse">
                    <li>
                        <a href="{{url('e_result')}}">Enter result</a>
                    </li>
                    <li>
                        <a href="{{url('update_result')}}">Update result</a>
                    </li>
                    <li>
                        <a href="{{url('view_result')}}">View result</a>
                    </li>
                     <li>
                        <a href="{{url('delete_result')}}">Delete result</a>
                    </li>
                    <li>
                        <a href="{{url('enter_probation_result')}}">Enter Probation Result</a>
                    </li>
                </ul>
            </li>
            

  <li>
                <a href="{{url('reports')}}"><i class="fa fa-fw fa-edit"></i>Report</a>
               
            </li>
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demopin"><i class="fa fa-fw fa-edit"></i>PIN<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demopin" class="collapse">
                    <li>
                        <a href="{{url('student_pin')}}">Student Pin</a>
                    </li>
                    <li>
                        <a href="{{url('view_used_pin')}}">View Used Pin</a>
                    </li>
                    <li>
                        <a href="{{url('reset_pin')}}">Reset Pin</a>
                    </li>
                </ul>
            </li>


 @elseif($result->name =="HOD")
 <li>
    <a href="{{url('resultUploadRight')}}"><i class="fa fa-fw fa-edit"></i>Result Upload Right</a>
               
</li>

          <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo5"><i class="fa fa-fw fa-edit"></i>Result<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo5" class="collapse">
                   <li>
                        <a href="{{url('lecturer')}}">Enter result</a>
                    </li>
                    <li>
                        <a href="{{url('v_result')}}">View  result</a>
                    </li>
                    <li>
                        <a href="{{url('eo_delete_result')}}">Delete result</a>
                    </li>
                </ul>

            </li>
            
    <li>
                <a href="{{url('departmentreport')}}"><i class="fa fa-fw fa-edit"></i> Generate Report</a>
               
            </li>     
            
            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo3"><i class="fa fa-fw fa-edit"></i>Assign Courses<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo3" class="collapse">
                   <li>
                        <a href="{{url('assign_course')}}">Assign Courses</a>
                    </li>
                    <li>
                        <a href="{{url('view_assign_course')}}">View Assigned Courses</a>
                    </li>
                   

                    <li>
                        <a href="{{url('assign_course_other')}}">Assign Courses(Other Lecturer)</a>
                    </li>
                </ul>
            </li>
@elseif($result->name =="lecturer" || $result->name =="examsofficer")
<li>
    <a href="{{url('resultUploadRight')}}"><i class="fa fa-fw fa-edit"></i>Result Upload Right</a>
               
</li>
<!-- gss faculty-->
@if(Auth::user()->faculty_id == 29 )
<li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo5"><i class="fa fa-fw fa-edit"></i>Result<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo5" class="collapse">
                     <li>
                        <a href="{{url('lecturer_gss')}}">Enter result</a>
                    </li>
                    <li>
                        <a href="{{url('v_result_gss')}}">View result</a>
                    </li>
                    <li>
                        <a href="{{url('eo_delete_result_gss')}}">Delete result</a>
                    </li>
                 
                </ul>
            </li>

@else
  <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo5"><i class="fa fa-fw fa-edit"></i>Result<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo5" class="collapse">
                     <li>
                        <a href="{{url('lecturer')}}">Enter result</a>
                    </li>
                    <li>
                        <a href="{{url('v_result')}}">View result</a>
                    </li>
                    <li>
                        <a href="{{url('eo_delete_result')}}">Delete result</a>
                    </li>
                    @if($result->name =="examsofficer")
                    <li>
                        <a href="{{url('enter_probation_result')}}">Enter Probation Result</a>
                    </li>
                    @endif
                </ul>
            </li>
            @if($result->name =="lecturer")
             <li>
                        <a href="{{url('r_student')}}">Registered Student</a>
                    </li>
                    @endif
                    @if($result->name =="examsofficer")
                   {{-- <li>
                            <a href="{{url('register_student')}}">Registered Student</a>
                        </li>--}}

                        <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo2"><i class="fa fa-fw fa-table"></i>Courses<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo2" class="collapse">
                  
                    <li>
                        <a href="{{url('register_course')}}">Register Courses</a>
                    </li>
                   
                    <li>
                        <a href="{{url('registeredcourse')}}">View Registered Courses</a>
                    </li>
                
                </ul>
            </li>
                        <li>
                        <a href="{{url('view_student')}}">Veiw Student </a>
                    </li>
                        <li>
                            <a href="{{url('register_student_ii')}}">Registered Student </a>
                        </li>
                    <li>
                        <a href="{{url('departmentreport')}}"><i class="fa fa-fw fa-edit"></i> Generate Report</a>
                       
                    </li> 
                    @endif
@endif


            @endif
          <!--  <li>
                <a href="{{url('changeemail')}}"><i class="fa fa-fw fa-edit"></i>Update Email<i class="fa fa-fw fa-caret-down"></i></a>
                </li>-->
             <li>
                <a href="{{url('changepassword')}}"><i class="fa fa-fw fa-edit"></i>Change Password<i class="fa fa-fw fa-caret-down"></i></a>
                </li>
 <li>
                                              <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>
                                        </li>
               
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                           
                                        </form>
                                    





    <!--===============================================-->
          
        </ul>
    </div>
    <!-- /.navbar-collapse -->
</nav>