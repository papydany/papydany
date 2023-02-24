@extends('layouts.admin')
@section('title','student management')
@section('content')
<div class="row">
    <div class="col-lg-12">
        
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard
            </li>
        </ol>
    </div>
</div>
<?php $result=session('key');?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading"> Student Management</div>
            <div class="panel-body">
            <ul>
                   <li style="padding: 5px">
                        <a href="{{url('studentManagementAddCourses')}}" target='blank'>Add Courses</a>
                    </li>
                    <li style="padding: 5px">
                        <a href="{{url('studentManagementAddRepeatCourses')}}" target='blank'>Add Repeat Courses</a>
                    </li>
                    <li style="padding: 5px">
                        <a href="{{url('studentManagementAddCarryOverCourses')}}" target='blank'>Add CarryOver Courses</a>
                    </li>
                    @if(Auth::user()->faculty_id ==18 || Auth::user()->department_id == 44 || Auth::user()->department_id == 19 || Auth::user()->department_id == 136)
                    <li style="padding: 5px">
                        <a href="{{url('studentManagementAddRepeatCourses2')}}" target='blank'>Add Repeat Courses for two Level</a>
                    </li>
                    <li style="padding: 5px">
                        <a href="{{url('studentManagementAddCarryOverCourses2')}}" target='blank'>Add CarryOver Courses for two Level</a>
                    </li>
                    @endif
                    @if($result->name =="Deskofficer")
                   <li style="padding: 5px">
                        <a href="{{url('create_course_unit_special')}}" target='blank'>Set Course unit</a>
                    </li>
                   
                    <li style="padding: 5px">
                        <a href="{{url('view_course_unit')}}" target='blank'>View Set Course unit</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection