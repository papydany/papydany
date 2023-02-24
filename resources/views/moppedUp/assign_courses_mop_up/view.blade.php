@extends('layouts.admin')
@section('title','View Assign Course')
@section('content')
@inject('r','App\Models\R')
<?php $result= session('key'); ?>
 
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
                <div class="panel-heading">View Assigned Courses Mop Up</div>
                <div class="panel-body">
      

                                @if(isset($ac))
                        @if(count($ac) > 0)
                        <hr/>
                 
                           <?php  $department=$r->get_departmetname($d_id);
                            ?>
                        <p><span><strong>Department :: </strong>{{$department}}</span>&nbsp;&nbsp;
                     
                        </p>
                       

                        <table class="table table-bordered table-striped">
                        <tr>
                      
                       <th>Code</th>
                      
                       <th>Assign To</th>
                       <th>Department Of</th>
                       <td>Action</td>
                       
                       </tr>
                      
                    
                       @foreach($ac as $v)
                       <?php  $depart=$r->get_departmetname($v->department_id);
                            ?>
                      <tr>
               
                        
                       <td> {{isset($v->course_code) ? $v->course_code : ''}} </td>
                  
                         <td> {{isset($v->name) ? $v->name : ''}} </td>
                         <td>{{$depart}}</td>
                         <td>
                           @if($result->name =="DVC")
                           @else
                           <div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Action <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <li><a href="{{url('remove_mop_assign_course',$v->assign_courses_mop_up_id)}}">Remove</a></li>
    
  </ul>
</div>
@endif
</td>

                        </tr>
                       @endforeach
                      
                       </table>
                   
                      
                  
                       @else
                        <p class="alert alert-warning">No Assign course is available is avalable</p>
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

   @endsection   

    @section('script')
<script src="{{URL::to('js/main.js')}}"></script>

@endsection                          
                             
