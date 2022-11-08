@extends('layouts.admin')
@section('title','Correction Name')
@section('content')
@inject('r','App\Models\R')

    <!-- Page Heading -->
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header">
            Correction Name
            </h3>
            <ol class="breadcrumb">
                <li class="active">
                    <i class="fa fa-dashboard"></i> Correction Name
                </li>
            </ol>
        </div>
    </div>
    <?php
    $department = $r->get_departmetname($u->department_id);

    $fos_name = $r-> get_fos($u->fos_id);
    ?>

    <div class="row">
    <div class="col-sm-12">	
            <div class="col-sm-12">	
    <form class="form-horizontal" role="form" method="POST" action="{{ url('correctionName') }}" data-parsley-validate> 
        {{ csrf_field() }}  
        
        <div class="form-group">
         <h5 class="alert alert-success">{{$u->surname }} {{$u->firstname }} {{$u->othername }}</h5>
         <br/>
    
        <h5><b>Department :</b> {{$department}}</h5>
        <h5><b>Field Of Study :</b> {{$fos_name}}</h5>
        <br/>
        
        <input type="hidden" class="form-control" name="id" value="{{$u->id}}">

     <div class="col-sm-4">
     <label for="surname">Surname</label>
         <input  type="text" class="form-control" name="surname" value="" required>
        </div>

        <div class="col-sm-4">
     <label for="matric_number">First Name</label>
         <input id="matric_number" type="text" class="form-control" name="firstname" value="" required>

         @if ($errors->has('firstname'))
             <span class="help-block">
                 <strong>{{ $errors->first('firstname') }}</strong>
             </span>
         @endif
     </div>
     <div class="col-sm-4">
        <label for="matric_number">Other Name</label>
            <input id="matric_number" type="text" class="form-control" name="othername" value="">
   
            @if ($errors->has('othername'))
                <span class="help-block">
                    <strong>{{ $errors->first('othername') }}</strong>
                </span>
            @endif
        </div>
     </div>
    
   
         <input type="submit" name="" value="submit" class="btn btn-success">
     </div>
     </div>
 </form>
    </div>
    </div>
</div>
@endsection 
@section('script')
<script src="{{URL::to('js/main.js')}}"></script>

@endsection
