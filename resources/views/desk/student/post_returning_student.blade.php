@extends('layouts.admin')
@section('title','Returning Student')
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
                <?php $fu1 =0; $du1=0; $ru1=0; $fu2 =0; $du2=0; $ru2=0;  ?>

    <div class="row" style="min-height: 520px;">
       
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">Returning Student</div>
                <div class="panel-body">
                
                    <p>{{$u->surname}} {{$u->firstname}} {{$u->othername}} {{$u->matric_number}}</p>
               
                <form class="form-horizontal" role="form" method="POST" action="{{ url('registerReturningStudent') }}" data-parsley-validate>
                        {{ csrf_field() }}
        <div class="col-sm-8">              
          <input type="hidden" name="session" value="{{$s}}">
          <input type="hidden" name="level" value="{{$l}}">
          <input type="hidden" name="fos" value="{{$fos}}">
          <input type="hidden" name="season" value="{{$season}}">
          <input type="hidden" name="id" value="{{$id}}">
          <input type="hidden" name="isProbation" value="{{$isProbation}}">
          @if(count($rs) == 0)
         
                       {{!!$c = 0}}
                       <table class="table table-bordered table-striped">
                        <tr>
                         
                        <th class="text-center">S/N </th>
                        <th class="text-center">TITLE</th>
                        <th class="text-center">CODE</th>
                        <th class="text-center">STATUS</th>
                      <th class="text-center">UNIT</th>
                      </tr> 
                      <tr><td colspan="6">First Semester
  <input type="hidden" name="semesterFirst" value="1"> </td> </tr>               
                       @if(!empty($frc1) > 0)
                  

    @foreach($frc1 as $vf)
     {{!++$c}}
   
     <tr>
     
     <td>{{$c}}</td>
     <td>
      <input type="checkbox"  value="{{$vf->id}}" checked disabled>
      <input type="hidden" name="idf[]" value="{{$vf->id}}">
     </td> 
     <td>{{strtoupper($vf->reg_course_title)}}</td>
     <td>{{strtoupper($vf->reg_course_code)}}</td>
     <td class="text-center">R</td>
     <td class="text-center">{{$vf->reg_course_unit}}</td>
    
     </tr>
    @endforeach 
  @endif

  @if(!empty($drc1) > 0)
  <?php $du1 =$drc1->sum('reg_course_unit'); ?>
   @foreach($drc1 as $vd)
    {{!++$c}}
   
    <tr>
   
    <td>{{$c}}</td>                                            
    <td><input type="checkbox"   value="{{$vd->id}}" checked disabled>
   <input type="hidden" name="idd[]" value="{{$vd->id}}">
    </td> 
    <td>{{strtoupper($vd->reg_course_title)}}</td>
    <td>{{strtoupper($vd->reg_course_code)}}</td>
    <td class="text-center">D</td>
    <td class="text-center">{{$vd->reg_course_unit}}</td>
    
    </tr>
  @endforeach
@endif
<tr><td colspan="6">Second Semester
     <input type="hidden" name="semesterSecond" value="2"> </td></tr>
                       @if(!empty($frc2) > 0)
                    
   
    @foreach($frc2 as $vf)
     {{!++$c}}
   
     <tr>
     
     <td>{{$c}}</td>
     <td>
      <input type="checkbox"  value="{{$vf->id}}" checked disabled>
      <input type="hidden" name="idf[]" value="{{$vf->id}}">
     </td> 
     <td>{{strtoupper($vf->reg_course_title)}}</td>
     <td>{{strtoupper($vf->reg_course_code)}}</td>
     <td class="text-center">R</td>
     <td class="text-center">{{$vf->reg_course_unit}}</td>
     
     </tr>
    @endforeach 
  @endif

  @if(!empty($drc2) > 0)
  <?php $du2 =$drc2->sum('reg_course_unit'); ?>
   @foreach($drc2 as $vd)
    {{!++$c}}
   
    <tr>
   
    <td>{{$c}}</td>                                            
    <td><input type="checkbox"   value="{{$vd->id}}" checked disabled>
   <input type="hidden" name="idd[]" value="{{$vd->id}}">
    </td> 
    <td>{{strtoupper($vd->reg_course_title)}}</td>
    <td>{{strtoupper($vd->reg_course_code)}}</td>
    <td class="text-center">D</td>
    <td class="text-center">{{$vd->reg_course_unit}}</td>
   
    </tr>
  @endforeach
@endif
                       </table>

          @else
                    @foreach($rs as $k => $item)
                    
                    <h4>@if($k == 1) First
                    <input type="hidden" name="semesterFirst" value="{{$k}}">   
                    @else Second 
                    <input type="hidden" name="semesterSecond" value="{{$k}}">
                    @endif Semester</h4>
                        <table class="table table-bordered table-striped">
                        <tr>
                          <th>
                              @if($k == 1)
                              <input type="checkbox" id="all_ids" >
                              @endif
                            </th>
                        <th class="text-center">S/N </th>
                        <th class="text-center">TITLE</th>
                        <th class="text-center">CODE</th>
                        <th class="text-center">STATUS</th>
                      <th class="text-center">UNIT</th>
                      
                        
                      
                       </tr>
                       {{!!$c = 0}}
                       @if($k == 1)
                       @if(!empty($frc1) > 0)
                       <?php $fu1 =$frc1->sum('reg_course_unit'); ?>
    @foreach($frc1 as $vf)
     {{!++$c}}
   
     <tr>
     
     <td>{{$c}}</td>
     <td>
      <input type="checkbox"  value="{{$vf->id}}" checked disabled>
      <input type="hidden" name="idf[]" value="{{$vf->id}}">
     </td> 
     <td>{{strtoupper($vf->reg_course_title)}}</td>
     <td>{{strtoupper($vf->reg_course_code)}}</td>
     <td class="text-center">R</td>
     <td class="text-center">{{$vf->reg_course_unit}}</td>
    
     </tr>
    @endforeach 
  @endif

  @if(!empty($drc1) > 0)
  <?php $du1 =$drc1->sum('reg_course_unit'); ?>
   @foreach($drc1 as $vd)
    {{!++$c}}
   
    <tr>
   
    <td>{{$c}}</td>                                            
    <td><input type="checkbox"   value="{{$vd->id}}" checked disabled>
   <input type="hidden" name="idd[]" value="{{$vd->id}}">
    </td> 
    <td>{{strtoupper($vd->reg_course_title)}}</td>
    <td>{{strtoupper($vd->reg_course_code)}}</td>
    <td class="text-center">D</td>
    <td class="text-center">{{$vd->reg_course_unit}}</td>
    
    </tr>
  @endforeach
@endif
@endif

@if($k == 2)
                       @if(!empty($frc2) > 0)
                       <?php $fu2 =$frc2->sum('reg_course_unit'); ?>
    @foreach($frc2 as $vf)
     {{!++$c}}
   
     <tr>
     
     <td>{{$c}}</td>
     <td>
      <input type="checkbox"  value="{{$vf->id}}" checked disabled>
      <input type="hidden" name="idf[]" value="{{$vf->id}}">
     </td> 
     <td>{{strtoupper($vf->reg_course_title)}}</td>
     <td>{{strtoupper($vf->reg_course_code)}}</td>
     <td class="text-center">R</td>
     <td class="text-center">{{$vf->reg_course_unit}}</td>
     
     </tr>
    @endforeach 
  @endif

  @if(!empty($drc2) > 0)
  <?php $du2 =$drc2->sum('reg_course_unit'); ?>
   @foreach($drc2 as $vd)
    {{!++$c}}
   
    <tr>
   
    <td>{{$c}}</td>                                            
    <td><input type="checkbox"   value="{{$vd->id}}" checked disabled>
   <input type="hidden" name="idd[]" value="{{$vd->id}}">
    </td> 
    <td>{{strtoupper($vd->reg_course_title)}}</td>
    <td>{{strtoupper($vd->reg_course_code)}}</td>
    <td class="text-center">D</td>
    <td class="text-center">{{$vd->reg_course_unit}}</td>
   
    </tr>
  @endforeach
@endif
@endif
@if($k == 1)
<?php $ru1 =$item->sum('reg_course_unit');
$total1 =$ru1 +$fu1 +$du1;
?>
@else
<?php $ru2 =$item->sum('reg_course_unit');

$total2 =$ru2 +$fu2 +$du2;
?>
@endif


                       @foreach($item as $vs)
                       <tr>
                       <td>{{++$c}}</td>
                         <td>
                           
                           <input type="checkbox" name="idc[]" class="ids" value="{{$vs->id}}">
                           <input type="hidden" name="code[{{$vs->id}}]" value="{{$vs->reg_course_code}}">
                           <input type="hidden" name="status[{{$vs->id}}]" value="{{$vs->reg_course_status}}">
                           
                           <input type="hidden" name="unit[{{$vs->id}}]" value="{{$vs->reg_course_unit}}">
                           <input type="hidden" name="course_id[{{$vs->id}}]" value="{{$vs->course_id}}">
                           <input type="hidden" name="title[{{$vs->id}}]" value="{{$vs->reg_course_title}}">
                         </td>
                       
                       <td>{{$vs->reg_course_title}}</td>
                       <td class="text-center">{{$vs->reg_course_code}}</td>
                       <td class="text-center">{{$vs->reg_course_status}}</td>
                       <td class="text-center">{{$vs->reg_course_unit}}</td>
                      
                                             
                       </tr>
                       
                       @endforeach
                       @if($k == 1)
                       <tr><th colspan="3">Total Unit</th><th>{{$total1}}</th></tr>
                       @else
                       <tr><th colspan="3">Total Unit</th><th>{{$total2}}</th></tr>
                       @endif
                    
                        </table>
                        @endforeach
                        @endif
        </div>
        <div class="col-sm-3"> 
         <div class="form-group">
             <label>Pin</label> 
        <input type="text" class='form-control' name="pin" value="" placeholder="PIN" required>
         </div>
         <div class="form-group">
             <label>Serial Number</label>
        <input type="text" class='form-control' name="serial" value="" placeholder="Serial Number" required>
         </div>
         <input type="submit" value="Register Course" class="btn btn-primary">
        </div>
                        
                </form>   
                

                </div>
            </div>
        </div>
    </div>


@endsection                      