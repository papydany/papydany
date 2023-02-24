@extends('layouts.admin')
@section('title','Mopped Up Exams')
@section('content')
@inject('r','App\Models\R')
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
                <div class="panel-heading">Mop Up Download And Upload of Result</div>
                <div class="panel-body">

                        @if(isset($c))
                        @if(count($c) != 0)
                  
                        <table class="table">

                  <tr><th>#</th>
              <th>Code</th>
              <th>department</th><th colspan="3">Action</th></tr>
                  <?php $i =0; ?>
                        @foreach($c as $v)
                        <?php  $depart=$r->get_departmetname($v->department_id);
                            ?>
                     
                       <tr><td>{{$i ++}}</td>
                        <td>{{$v->course_code}}</td>
                        <td>{{$depart}}</td>
                        <td>
                          <a href="{{url('downloadMopUpERS',[$v->course_id,$v->department_id])}}" class="btn btn-success">Download ERS</a>
                        </td>
                        <td>
                          
                          <a href="{{url('uploadMopUpResult',[$v->course_id,$v->department_id])}}" class="btn btn-primary" target="_blank">Upload Result</a>
                        </td>
                        <td>
                          
                          <a href="{{url('viewMopUpResult',[$v->course_id,$v->department_id])}}" class="btn btn-warning" target="_blank">View Result</a>
                        </td>
                      </tr>
            
                        @endforeach
                        </table>
                      
                      
                      
@else
<p>No Records</p>
                        @endif

                        @endif
                        </div>
                        </div>
                      </div>

                      

                        </div>
                      

@endsection  


                    