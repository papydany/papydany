@extends('layouts.admin')
@section('title','students')
@section('content')
@inject('R','App\Models\R')
 <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Dashboard <small>First Class students</small>
                        </h1>
                        <ol class="breadcrumb">
                            <li class="active">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="panel panel-default">
                <div class="panel-heading"> Class Of Degree </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('firstClassStudent') }}"  data-parsley-validate>
                        {{ csrf_field() }}
                     

<div class="form-group">
 
                        

                       <div class="col-sm-3">
                                 <label for="faculty">Faculty</label>
                                <select class="form-control" name="faculty" required>
                                    <option value="">Select</option>
                                    @if(isset($f))
                                    @foreach($f as $v)
                                    <option value="{{$v->id}}">{{$v->faculty_name}}</option>

                                    @endforeach

                                    @endif
                                </select>
                                </div>
                            
                        
                    

                    
                          
                                 <div class="col-sm-3">
                              <label for="session" class=" control-label">Session</label>
                              <select class="form-control" name="session" required>
                              <option value=""> - - Select - -</option>
                               
                                  @for ($year = (date('Y')); $year >= 2018; $year--)
                                  {{!$yearnext =$year+1}}
                                  <option value="{{$year}}">{{$year.'/'.$yearnext}}</option>
                                  @endfor
                                
                              </select>
                             
                            </div>
                            <div class="col-sm-3">
                              <label for="session" class=" control-label">Class Of Degree</label>
                              <select class="form-control" name="classDegree" required>
                              <option value=""> - - Select - -</option>
                               
                             
                                  <option value="1">First Class</option>
                                  <option value="21">Second  Class Upper</option>
                                  <option value="22">Second  Class Lower</option>
                                  <option value="3">Third Class</option>
                                  <option value="p">Passed</option>
                                 
                                
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
                        </div>
                        
                <!-- /.row -->
                <div class="row">
                <div class="col-md-12"> 
                @isset($fos)

                @if(count($fos) > 0)
                @if($classDegree == 1)
                <h3>First Class Students (CGPA above 4.5)</h3>
                @elseif($classDegree==21)
                <h3>Second Class Upper Students (CGPA between 4.49 and 3.50)</h3>
                @elseif($classDegree==22)
                <h3>Second Class Lower Students (CGPA between 3.49 and 2.40)</h3>
                @elseif($classDegree==3)
                <h3>Third Class Students (CGPA between 2.39 and 1.50)</h3>
                @elseif($classDegree=='p')
                <h3>Passed  Students (CGPA between 1.49 and 1.00)</h3>
                @endif
                <table class="table table-bordered table-striped">
                <tr>
                <th>S/N</th>
                  <th>Matric Number</th>
                 <th>Name</th>
                 <th>Department</th>
                 <th>CGPA</th>
                </tr>
                
                {{!!$c = 0}}
                @foreach($fos as $k)
               <?php 
               $depart = $R->get_departmetname($k->department_id);
                $user= DB::connection('mysql2')->table('users')
                ->join('student_regs', 'student_regs.user_id', '=', 'users.id')
                ->select('users.*','level_id')
                ->where([['student_regs.session',$s], ['student_regs.faculty_id',$fac],['fos_id',$k->id],
                ['student_regs.department_id',$k->department_id],['level_id','>=',$k->duration],['semester',1],['season','NORMAL']])->orderBy('department_id','Asc')
                ->distinct()->get();
                foreach($user as $v){
        $cgpa =$R->get_cgpa($s,$v->id,'VACATION');

               ?>
          @if($cgpa >= '4.5' && $classDegree == 1)
            
                <tr>
                <td>{{++$c}}</td>
                <td>{{$v->matric_number}}</td>
                <td>{{$v->surname .' '.$v->firstname.' '.$v->othername}}</td>
                <td>{{$depart}}</td>
                <td>{{$cgpa}}</td>
             
                </tr>
                @elseif($cgpa <= 4.49 && $cgpa >= 3.50 && $classDegree == 21)
                <tr>
                <td>{{++$c}}</td>
                <td>{{$v->matric_number}}</td>
                <td>{{$v->surname .' '.$v->firstname.' '.$v->othername}}</td>
                <td>{{$depart}}</td>
                <td>{{$cgpa}}</td>
             
                </tr>


                @elseif($cgpa <= 3.49 && $cgpa >= 2.40 && $classDegree == 22)
                <tr>
                <td>{{++$c}}</td>
                <td>{{$v->matric_number}}</td>
                <td>{{$v->surname .' '.$v->firstname.' '.$v->othername}}</td>
                <td>{{$depart}}</td>
                <td>{{$cgpa}}</td>
             
                </tr>

                @elseif($cgpa <= 2.39 && $cgpa >= 1.50 && $classDegree == 3)
                <tr>
                <td>{{++$c}}</td>
                <td>{{$v->matric_number}}</td>
                <td>{{$v->surname .' '.$v->firstname.' '.$v->othername}}</td>
                <td>{{$depart}}</td>
                <td>{{$cgpa}}</td>
             
                </tr>

                @elseif($cgpa <= 1.49 && $cgpa >= 1.00 && $classDegree == 'p')
                <tr>
                <td>{{++$c}}</td>
                <td>{{$v->matric_number}}</td>
                <td>{{$v->surname .' '.$v->firstname.' '.$v->othername}}</td>
                <td>{{$depart}}</td>
                <td>{{$cgpa}}</td>
             
                </tr>

                 @endif
                <?php } ?>
                @endforeach
       
 <tr> 
     <td colspan="5">           @if($c == 0) 
<p class="alert alert-warning" role="alert">No students with first class available</p>
             @endif
            </td>
 </tr>
             


                </table>
 
                @endif
                @endif
                </div>
                </div>
                

@endsection               