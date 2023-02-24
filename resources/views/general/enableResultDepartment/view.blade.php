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
                <div class="panel-heading">View Enable Department Result Uploads</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST"  action="{{ url('viewEnableResultDepartment') }}" data-parsley-validate>
                        {{ csrf_field() }}
                        <div class="form-group">
                     
<table class="table">
    <tr>
        <th>S/N</th>
        <th>Select</th>
        <th>Department</th>
        <th>Session</th>
    </tr>
    @if(isset($g))
    <?php
    
    $c=0; ?>
    @foreach($g as $k => $item)
 
    <tr>
    <td>{{++$c}}</td>
    <td><input type="checkbox" name="id[]" value="{{$k}}"/> </td>
    <td>{{$d[$k]['name']}}</td>
    <td>
        @foreach($item as $v)
        {{$v->session}} 
        <br/>
    @endforeach
</td>
    </tr>

    @endforeach
  @endif
</table>

     
               

                       
                            <div class="col-md-1">
                              <br/>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-btn fa-user"></i> Delete
                                </button>
                            </div>
                       
                        </div>

                        </form>
                        </div>
                        </div>
                      </div>

                  

                      

                        </div>
                      

@endsection  


                    