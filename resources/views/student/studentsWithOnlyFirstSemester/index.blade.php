@extends('layouts.admin')
@section('title','Registered Students')
@section('content')
@inject('R','App\Models\R')
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
       

                      
                        @if(isset($u))
                   
                        <div class="col-sm-12">
                     
                         
                          @if(count($u))
                 
                          <form class="form-horizontal" role="form" method="POST" action="{{ url('studentsWithOnlyFirstSemester') }}" data-parsley-validate>
                        {{ csrf_field() }}

                          <table class="table table-bordered table-striped">
                            <tr>
                            
                              <th>S/N</th>
                              <th>Select</th>
                              <th>Matric</th>
                              <th>Name</th>
                               <th>session</th>
                           
                              <th>fos</th>
                              
                            </tr>
                             {{!!$c = 0}}
                       @foreach($u as $v)
                      
                      
                     
                       <?php  
                     $fos = $R->get_fos($v->fos_id) ?>
                       <tr>
                      
                       <td>{{++$c}}</td>
                       <td><input type="checkbox" name="id[]" value="{{$v->id}}"/></td>
                       <td>{{$v->matric_number}}</td>
                       <td>{{$v->surname.' '.$v->firstname. ' '.$v->othername}}</td>
                        <td>{{$v->entry_year}}</td>
                  
                        <td>{{$fos}}</td>
                       
                       </tr>
                       @endforeach
                 
                       
                          </table>


 <div class="col-sm-12" >
            <div class="panel panel-default">
                <div class="panel-heading">Register for Second semester</div>
                <div class="panel-body">
                   
                      
                         
                         

                          
                      
                            

                            <div class="col-md-2">
                            <br/>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i>Register Second Semester Students
                                </button>
                            </div>

                        </div>

                        </form>
                        </div>
                        </div>
                      </div>

                          @else
<div class=" col-sm-10 col-sm-offset-1 alert alert-warning" role="alert" >
    No Records!!!
    </div>

                          @endif
                        </div>
                        @endif
                        </div>
                      
  
@endsection  


                    