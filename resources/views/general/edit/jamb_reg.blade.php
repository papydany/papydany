@extends('layouts.admin')
@section('title','Edit Jamb Reg Number')
@section('content')
@inject('r','App\Models\R')

    <!-- Page Heading -->
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header">
                Edit Jamb Reg Number 
            </h3>
            <ol class="breadcrumb">
                <li class="active">
                    <i class="fa fa-dashboard"></i> Edit Jamb Reg Number 
                </li>
            </ol>
        </div>
    </div>
    <?php
    $department = $r->get_departmetname($u->department_id);

    $fos_name = $r-> get_fos($u->fos_id);
    ?>

    <div class="row">
    <form class="form-horizontal" role="form" method="POST" action="{{ url('/edit_jamb_reg') }}" data-parsley-validate> 
        {{ csrf_field() }}  	
     <div class="col-xs-12">	
         <h5 class="alert alert-success">{{$u->surname }} {{$u->firstname }} {{$u->othername }}</h5>
         <br/>
    
        <h5><b>Department :</b> {{$department}}</h5>
        <h5><b>Field Of Study :</b> {{$fos_name}}</h5>
        <br/>
        
   

     <div class="col-sm-4">
     <label for="matric_number">Old Jamb Reg Number</label>
         <input id="jamb Reg" type="text" class="form-control" name="oldmatric_number" value="{{$u->jamb_reg}}" disabled>
         <input type="hidden" class="form-control" name="old_jamb_reg" value="{{$u->jamb_reg}}">
         <input type="hidden" class="form-control" name="id" value="{{$u->id}}">

        
     </div>

        <div class="col-sm-4">
     <label for="matric_number">Enter New Jamb Reg Number</label>
         <input id="jamb_reg" type="text" class="form-control" name="jamb_reg" value="" required>

         @if ($errors->has('jamb_reg'))
             <span class="help-block">
                 <strong>{{ $errors->first('jamb_reg') }}</strong>
             </span>
         @endif
     </div>
<br/>
         <input type="submit" name="" value="submit" class="btn btn-success">
     </div>
     </div>
 </form>
    </div>
@endsection 
@section('script')
<script src="{{URL::to('js/main.js')}}"></script>

@endsection
