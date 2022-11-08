@extends('layouts.admin')
@section('title','Edit Register Course')
@section('content')
@inject('R','App\Models\R')

             
                  <div class="row" style="min-height: 520px;">
        <div class="col-sm-12">
@if(isset($r))
                        @if($r != null)
       <?php  $department = $R->get_departmetname($r->department_id);
 
        $fos =$R->get_fos($r->fos_id);    


     ?>
     <table  class="table table-bordered">
<tr><td>

      <p class="text-center" style="font-size:14px; font-weight:700;">EDIT REGISTERED COURSES</p>
    <div class="col-sm-9 www">
  
  
      <p>DEPARTMENT: {{$department}}</p>
          <p>PROGRAMME:  {{$fos}}</p>
 
      </div>
  <div class="col-sm-3 ww">
   {{!$next = $r->session + 1}}
      <p> <strong>Level : </strong>{{$r->level_id}}00 </p>
      <p><strong>Session : </strong>{{$r->session.' / '.$next}}</p>
   
     

    </div>

    </td></tr>
 
  
  
</table>
<div class="col-sm-8">
     <form class="form-horizontal" role="form" method="POST" action="{{ url('/edit_adminreg_course_semester') }}" data-parsley-validate>
                        {{ csrf_field() }}
             <input type="hidden" name="pre_url" value="{{url()->previous()}}">            
            <input type="hidden" name="session" value="{{$r->session}}"> 
            <input type="hidden" name="id" value="{{$r->id}}">
                   
                        <table class="table table-bordered table-striped">
                        <tr>
                        <th>Title</th>
                        <th>{{$r->reg_course_title}}</th>
                      </tr>
                      <tr>
                        <th>Code</th>
                        <th>{{$r->reg_course_code}}</th>
                      </tr>
                    
                      <tr>
                      <th>Status</th>
                      <th>{{$r->reg_course_status}}</th>
                      </tr>
                     
                   
                      <tr>
                      <th>Unit</th>
                       <td>{{$r->reg_course_unit}}</td>
                     </tr>
                     <th>Semester</th>
                       <td>
                    <select name="semester" class="form-control" required>
                          <option value="">select</option>
                          <option value="1">First Semeter</option>
                          <option value="2">Second Semester</option>
                        </select>
                       
                    </td>
                     <tr>
                     </tr>
                 
<tr><td></td><td colspan="8"><input type="submit" value="Update" class="btn btn-primary"></td></tr>                       
                        </table>
                      </form>
                    </div>

                        @else
 <div class=" col-sm-10 col-sm-offset-1 alert alert-warning" role="alert" >
    No Records!!!
    </div>

                        @endif
                        @endif
                        </div>
                        </div>

  @endsection 