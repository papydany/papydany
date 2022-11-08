@extends('layouts.admin')
@section('title','Create Course Unit')
@section('content')
@inject('R','App\Models\R')
<?php use Illuminate\Support\Facades\DB; ?>
<form class="form-horizontal" role="form" method="POST" action="{{ url('getDuplicate') }}" data-parsley-validate>
                        {{ csrf_field() }}
<table class="table">
    @if(count($r))
    <tr>
        <td>
           id 
        </td>
        <td>
          courseReg ID  
        </td>
        <td>
            grade
        </td>
    </tr>
    <?php $c =0; ?>
    @foreach($r as $k => $v)
    <?php $t =DB::table('courses')->where('course_code',$v->course_code)->count(); ?>
    <tr>
        <td>{{++$c}}</td>
        <td>
        {{$t}}
        </td>
        <td>
          {{$v->course_code}}  
        </td>
       
    </tr>

    @endforeach

    @endif
</table>
<button type="submit" class="btn btn-danger">
                                    <i class="fa fa-btn fa-user"></i> Delete
                                </button>
</form>
@endsection 