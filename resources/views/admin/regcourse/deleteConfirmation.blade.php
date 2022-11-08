@extends('layouts.admin')
@section('title','Edit Register Course')
@section('content')
    <div class="row" style="min-height: 560px;">
        <div class="col-sm-12">
        	<br/>
        	
        	<p class="text-center" style="font-size:20px; font-weight:700;color:red;">Do you want to continue with delete the Course and Result?</p>
        	<br/>
        	<p class="text-center"><a href="{{url()->current().'/1'}}" class="btn btn-xs btn-danger">Yes</a>
        		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        		<a href="{{url()->previous()}}" class="btn btn-xs btn-primary">No</a></p>
</div>
</div>
 @endsection 