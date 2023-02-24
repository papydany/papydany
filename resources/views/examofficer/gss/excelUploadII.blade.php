@extends('layouts.admin')
@section('title','Enter Result')
@section('content')
@inject('r','App\Models\R')
 <!-- Page Heading -->
 <style type="text/css">
 .fc {padding:0px;text-align: center;font-weight: bolder;font-size: 14px;}
 .table>tbody>tr>td{padding:4px;}
 .cc {width:6%;}
        </style>
                <div class="row">
                
               
                    <div class="col-lg-12">
                       
                        <ol class="breadcrumb">
                            <li class="active" style="font-weight: bolder;">
                            
                            Course  : <strong>{{$c->course_title}}</strong>
                            &nbsp;&nbsp; &nbsp;&nbsp; <b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;
                               Course Code : {{$c->course_code}}
                               &nbsp;&nbsp; &nbsp;&nbsp; <b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;
                               Course Unit : {{$c->course_unit}}
                               &nbsp;&nbsp; &nbsp;&nbsp; <b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;
                            
                               <?php $next = $s + 1;?>
                             Session: {{$s.' / '.$next}}
                            
                                &nbsp;&nbsp; &nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;&nbsp;&nbsp;
                              
                            </li>
                        </ol>
                    </div>
                </div>
                  <div class="row" style="min-height: 520px;">
       
            <div class="panel panel-default">
                <div class="panel-heading">Upload  Result 
                  &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                  &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                  &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                  <span class="text-center text-success"><strong>Result Type :</strong>&nbsp;{{$rt}}</span></div>
                <div class="panel-body">
             
                <h4 class="text-danger">NB : The excel sheet must carry the header title as indicated below </h4>
                   
                      <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{ url('excel_insert_result_gssII') }}" data-parsley-validate>
                   
                        {{ csrf_field() }}
                      
                      
                        
                        <input type="hidden" name="course_id" value="{{$c->id}}">
                        <input type="hidden" name="period" value="{{$period}}">
                        <input type="hidden" name="session" value="{{$s}}">

                 <table class="table table-bordered table-striped">
                 <tr>
                       
                        
                        <th class="text-center">MatricNo</th>
                        <th class="text-center">NAMES</th>
                        <th width="15%"  class="text-center">ScriptNo</th> 
                        <th class="cc text-center">CA</th> 
                        <th class="cc text-center">EXAM</th>
                        <th class="cc text-center">TOTAL</th>
                      
                          </tr>
                          
                  
<tr></tr>
<td>
  <label>select excell file</label>
<input type="file" name="excel_import_result" value="" class="form-control" required>
</td>

<td style="padding-top: 18px;padding-bottom:10px;">
<input type="submit" class="btn btn-primary" name="insert" value="Insert Result"/>
</td>

<td style="padding-top: 18px;padding-bottom:10px;">
<input type="submit" class="btn btn-warning " name="update" value="Update Result"/>

</td>
</td>
</form>
<form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{ url('excel_insert_result_gssII') }}" data-parsley-validate>
                   
                   {{ csrf_field() }}
                 
                 
                   
                   <input type="hidden" name="course_id" value="{{$c->id}}">
                   <input type="hidden" name="period" value="{{$period}}">
                   <input type="hidden" name="session" value="{{$s}}">
<td style="padding-top: 18px;padding-bottom:10px;">
<input type="submit" class="btn btn-success" name="view" value="View Result"/>

</td>
</tr>
</table>                            
</form>

 </div>
</div>
</div>
</div>
  @endsection 
