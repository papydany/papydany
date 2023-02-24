@extends('layouts.admin')
@section('title','Enter Result')
@section('content')

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
                        
                    
                               Course Code : {{$c->course_code}}
                               &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                               Course Unit : {{$c->course_unit}}
                               &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                            
                            
                         
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
                  <span class="text-center text-success"><strong>Result Type :</strong>&nbsp;Mop Up </span></div>
                <div class="panel-body">
             
                <h4 class="text-danger">NB : The excel sheet must carry the header title as indicated below </h4>
                   
                      <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{ url('uploadMopUpResult') }}" data-parsley-validate>
                   
                        {{ csrf_field() }}
                      
                      
                        <input type="hidden" name="course_id" value="{{$id}}">
                        <input type="hidden" name="department_id" value="{{$d}}">
                     
                        <input type="hidden" name="period" value="NORMAL">
                      
                        <input type="hidden" name="semester" value="{{$c->semester}}">
                       
                 <table class="table table-bordered table-striped">
                 <tr>
                       
                        
                        <th class="text-center">MatricNo</th>
                        <th class="text-center">NAMES</th>
                        <th width="15%"  class="text-center">ScriptNo</th> 
                       
                        <th class="cc text-center">EXAM</th>
                        <th class="cc text-center">TOTAL</th>
                      
                          </tr>
                          
                  
<tr></tr>
<td>
  <label>select excell file</label>
<input type="file" name="excel_import_result" value="" class="form-control" required>
</td>
<td colspan="3"></td>
<td colspan="2" style="padding-top: 18px;padding-bottom:10px;">

<button type="submit" class="btn btn-primary btn-block ">
<i class="fa fa-btn fa-user"></i> Submit Result</button>
</td>
</tr>
</table>                            
</form>

 </div>
</div>
</div>
</div>
  @endsection 
